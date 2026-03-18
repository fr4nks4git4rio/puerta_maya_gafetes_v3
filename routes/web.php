<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return redirect()->route('home');
//});

//Route::get('/prueba', function (){
//    var_dump(array_flip(auth()->user()->roles_array));
//});

use App\Actions\ActivarTarjetaV3;
use App\Actions\CrearTarjetaV3;
use App\Actions\DesactivarTarjetaV2;
use App\Controladora;
use App\Empleado;
use App\Factura;
use App\Http\Controllers\ControladoraController;
use App\Http\Controllers\PuertaController;
use App\Http\Controllers\SettingsController;
use App\Imports\DatosImport;
use App\Local;
use App\Puerta;
use App\SolicitudGafete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/arreglas_facturas', function () {
    set_time_limit(6000);
    $facturas = Factura::where('fact_id', '>', 1458)->get();

    $facturas->map(function (Factura $factura) {
        if ($factura->fact_qr_code_path) {
            $factura->fact_qr_code_path = Str::replaceFirst('pm_credenciales', 'puerta_maya_gafetes_v3', $factura->fact_qr_code_path);
        }
        if ($factura->fact_xml_path) {
            $factura->fact_xml_path = Str::replaceFirst('pm_credenciales', 'puerta_maya_gafetes_v3', $factura->fact_xml_path);
        }
        if ($factura->fact_qr_code_path || $factura->fact_xml_path)
            $factura->save();
    });

    echo "ECHO!!!!!!";
});

Route::get('/convertir_numeros_gafetes_wiegand', function () {
    $data = Excel::toArray(new DatosImport, public_path("/Listado empleados por Local.xlsx"));
    $data = $data[0];
    array_shift($data);
    $empleados_autorizados = [];
    foreach ($data as $d) {
        if ($d[2])
            $empleados_autorizados[] = [
                'empleado_id' => $d[2],
                'auto' => $d[4] != null,
                'moto' => $d[5] != null
            ];
    }

    DB::beginTransaction();
    foreach ($empleados_autorizados as $empl) {
        try {
            $empleado = Empleado::find($empl['empleado_id']);
            if ($empleado && $empleado->GafeteAcceso()) {
                Log::error("Numero serial: " . $empleado->GafeteAcceso()->sgft_numero);
                $wiegand = convert_serial_to_wiegand($empleado->GafeteAcceso()->sgft_numero);
                Log::error("Numero wiegand: $wiegand");
                SolicitudGafete::where('sgft_id', $empleado->GafeteAcceso()->sgft_id)->update(['sgft_numero' => $wiegand]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}");
        }
    }
    DB::commit();
    echo "ECHO!!!!!";
});

Route::get('/asignar_permisos_estacionamiento', function () {
    $data = Excel::toArray(new DatosImport, public_path("/Listado empleados por Local_faltantes.xlsx"));
    $data = $data[0];
    array_shift($data);
    $empleados_autorizados = [];
    foreach ($data as $d) {
        if ($d[2])
            $empleados_autorizados[] = [
                'empleado_id' => $d[2],
                'auto' => $d[4] != null,
                'moto' => $d[5] != null
            ];
    }

    set_time_limit(600);

    foreach ($empleados_autorizados as $empl) {
        DB::beginTransaction();
        try {
            $empleado = Empleado::find($empl['empleado_id']);
            if ($empleado && $empleado->GafeteAcceso()) {

                $permisos = 'PEATONAL';

                if ($empl['auto'] || $empl['moto']) {
                    if ($empl['auto']) $permisos .= ",AUTO";
                    if ($empl['moto']) $permisos .= ",MOTO";
                    $fecha = now()->format('Y-m-d H:i:s');
                    DB::insert("INSERT INTO solicitudes_gafetes_reasignar
                    (sgftre_permisos, sgftre_anio, sgftre_estado, sgftre_fecha_solicitado, sgftre_fecha_asignado, sgftre_fecha_autorizado, sgftre_empl_id, sgftre_sgft_id, sgftre_lcal_id, sgftre_created_at)
                    VALUES ('$permisos', 2026, 'AUTORIZADO', '$fecha','$fecha','$fecha', $empleado->empl_id, {$empleado->GafeteAcceso()->sgft_id}, {$empleado->Local->lcal_id}, '$fecha')");
                }

                echo "Autorizando a: $empleado->empl_nombre. Gafete: {$empleado->GafeteAcceso()->sgft_numero}. Permisos: $permisos \n\t\n";

                // if ($empleado->Local->Acceso->cacs_id === 1)
                //     $puertas = Puerta::with('Controladora')
                //         ->whereHas('Controladora', function ($query) {
                //             $query->where('ctrl_usuario', '!=', '')
                //                 ->where('ctrl_contrasenna', '!=', '');
                //         })
                //         ->where('door_modo', 'FISICA')->pluck('door_id');
                // else
                $puertas = Puerta::with('Controladora')
                    ->whereHas('Controladora', function ($query) {
                        $query->where('ctrl_usuario', '!=', '')
                            ->where('ctrl_contrasenna', '!=', '');
                    })
                    ->where('door_modo', 'FISICA')
                    ->whereIn('door_tipo', explode(',', $permisos))->pluck('door_id');

                $empleado->GafeteAcceso()->Puertas()->sync($puertas);

                $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV3();
                $controladora = Controladora::find($gafeteRfid->controladora_id);

                $crear = new CrearTarjetaV3($gafeteRfid);
                $res = $crear->execute();
                if ($res == false) {
                    DB::rollBack();
                    Log::error("Ocurrió un error al crear la tarjeta en la controladora $controladora->ctrl_nombre.");
                }

                $activar = new ActivarTarjetaV3($gafeteRfid);
                $res = $activar->execute();
                if ($res == false) {
                    DB::rollBack();
                    Log::error("Ocurrió un error al activar la tarjeta en la controladora $controladora->ctrl_nombre.");
                }
            }
            DB::commit();
            sleep(1);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}");
        }
    }
    echo "ECHO!!!!!";
});

Route::get('/asignar_permisos_restantes', function () {
    $data = Excel::toArray(new DatosImport, public_path("/Empleados faltantes.xlsx"));
    $data = $data[0];
    array_shift($data);
    $empleados_autorizados = [];
    foreach ($data as $d) {
        $empleados_autorizados[] = [
            'empleado_id' => $d[0],
            'auto' => $d[2] != null,
            'moto' => $d[3] != null,
            'en_controladora' => $d[4] != null
        ];
    }

    foreach ($empleados_autorizados as $empl) {
        set_time_limit(60);
        DB::beginTransaction();
        try {
            $empleado = Empleado::find($empl['empleado_id']);
            if ($empleado && $empleado->GafeteAcceso()) {

                $permisos = 'PEATONAL';

                if ($empl['auto'] || $empl['moto']) {
                    if ($empl['auto']) $permisos .= ",AUTO";
                    if ($empl['moto']) $permisos .= ",MOTO";
                    $fecha = now()->format('Y-m-d H:i:s');
                    if (DB::table('solicitudes_gafetes_reasignar')->where('sgftre_empl_id', $empleado->empl_id)->count() > 0) {
                        Log::info("Permisos actualizados a '$empleado->empl_nombre': $permisos");
                        DB::table('solicitudes_gafetes_reasignar')
                            ->where('sgftre_empl_id', $empleado->empl_id)
                            ->update([
                                'sgftre_permisos' => $permisos
                            ]);
                    } else {
                        Log::info("Permisos asignados a '$empleado->empl_nombre': $permisos");
                        DB::insert("INSERT INTO solicitudes_gafetes_reasignar
                    (sgftre_permisos, sgftre_anio, sgftre_estado, sgftre_fecha_solicitado, sgftre_fecha_asignado, sgftre_fecha_autorizado, sgftre_empl_id, sgftre_sgft_id, sgftre_lcal_id, sgftre_created_at)
                    VALUES ('$permisos', 2026, 'AUTORIZADO', '$fecha','$fecha','$fecha', $empleado->empl_id, {$empleado->GafeteAcceso()->sgft_id}, {$empleado->Local->lcal_id}, '$fecha')");
                    }
                }

                $puertas = Puerta::with('Controladora')
                    ->whereHas('Controladora', function ($query) {
                        $query->where('ctrl_usuario', '!=', '')
                            ->where('ctrl_contrasenna', '!=', '');
                    })
                    ->where('door_modo', 'FISICA')
                    ->whereIn('door_tipo', explode(',', $permisos))->pluck('door_id');

                $empleado->GafeteAcceso()->Puertas()->sync($puertas);

                $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV3();
                $controladora = Controladora::find($gafeteRfid->controladora_id);

                if (!$empl['en_controladora']) {
                    Log::info('Numero serial: ' . $empleado->GafeteAcceso()->sgft_numero);
                    $wiegand = convert_serial_to_wiegand($empleado->GafeteAcceso()->sgft_numero);
                    Log::info('Numero wiegand: ' . $wiegand);
                    SolicitudGafete::where('sgft_id', $empleado->GafeteAcceso()->sgft_id)->update(['sgft_numero' => $wiegand]);
                    $crear = new CrearTarjetaV3($gafeteRfid);
                    $res = $crear->execute();
                    if ($res == false) {
                        DB::rollBack();
                        Log::error("Ocurrió un error al crear la tarjeta en la controladora $controladora->ctrl_nombre.");
                    }
                }

                $activar = new ActivarTarjetaV3($gafeteRfid);
                $res = $activar->execute();
                if ($res == false) {
                    DB::rollBack();
                    Log::error("Ocurrió un error al activar la tarjeta en la controladora $controladora->ctrl_nombre.");
                }
            }
            DB::commit();
            sleep(1);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error: {$e->getMessage()}");
        }
    }
    echo "ECHO!!!!!";
});


Auth::routes();

Route::get('login-google', 'Auth\LoginController@redirectToProvider');

Route::get('oauth-response', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index');

Route::get('/door-rc/{token}', 'DoorRcController@index');
Route::post('/door-rc/open-door/{token}/{door}', 'DoorRcController@openDoor');


Route::get('/informacion-general', 'HomeController@infoGeneral')->name('info-locatario')->middleware(['role:LOCATARIO']);
Route::get('/video-ayuda', 'HomeController@videoAyuda')->name('video-ayuda')->middleware(['role:LOCATARIO']);
Route::get('/modal-video-ayuda/{seconds}', 'HomeController@modalVideoAyuda')->name('modal-video-ayuda')->middleware(['role:LOCATARIO']);

Route::get('/generar_qr_image', function () {
    //    phpinfo();
    set_time_limit(3000);
    if (!extension_loaded('imagick')) {
        echo 'imagick not installed';
        return;
    }
    //    $res = \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PartnersImport,"C:\Users\Frank\Desktop\LISTA DE SOCIOS CONCESIONARIOS ACTUAL JUNIO-2022.xlsx");
    $cont = 0;
    $res = (new \App\Imports\PartnersImport)->toCollection('C:\Users\Frank\Desktop\ListaTodosSocios.csv');
    foreach ($res->first() as $key => $row) {
        if ($key != 0) {
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(60)->generate($row[3], 'C:\Users\Frank\Desktop\QRs\\' . $row[1] . '.png');
            $cont++;
        }
    }
    echo "Generadas $cont imágenes!";
});

Route::group([
    'prefix' => '/usuario',
    'middleware' => ['role:ADMINISTRADOR']
], function () {

    Route::get('/', 'UsuarioController@index');

    Route::post('form-roles/{usuario}', 'UsuarioController@formRoles');
    Route::post('set-roles/{usuario}', 'UsuarioController@setRoles');

    Route::post('form-token/{usuario}', 'UsuarioController@formToken');
    Route::get('refresh-token/{usuario}', 'UsuarioController@refreshToken');
    Route::get('unset-token/{usuario}', 'UsuarioController@unsetToken');

    Route::post('form-condominios/{usuario}', 'UsuarioController@formCondominios');
    Route::post('set-condominios/{usuario}', 'UsuarioController@setCondominios');

    Route::post('form/{usuario?}', 'UsuarioController@form');
    Route::post('insert', 'UsuarioController@insert');
    Route::post('edit/{usuario}', 'UsuarioController@edit');
    Route::post('delete/{usuario}', 'UsuarioController@delete');

    Route::post('/get-select-options', 'UsuarioController@getSelectOptions');
});

Route::group(['prefix' => 'controladoras'], function () {
    Route::get('/', [ControladoraController::class, 'index']);
    Route::post('form/{controladora?}', [ControladoraController::class, 'form']);
    Route::post('insert', [ControladoraController::class, 'insert']);
    Route::post('edit/{controladora}', [ControladoraController::class, 'edit']);
    Route::post('delete/{controladora}', [ControladoraController::class, 'delete']);

    Route::get('probar_conexion/{controladora}', [ControladoraController::class, 'probarConexionControladoraV3']);
    Route::get('syncronizar_registros/{controladora}', [ControladoraController::class, 'syncronizarRegistrosControladora']);
});

Route::group(['prefix' => 'puertas'], function () {
    Route::get('/', [PuertaController::class, 'index']);
    Route::post('form/{puerta?}', [PuertaController::class, 'form']);
    Route::post('insert', [PuertaController::class, 'insert']);
    Route::post('edit/{puerta}', [PuertaController::class, 'edit']);
    Route::post('delete/{puerta}', [PuertaController::class, 'delete']);
});


Route::group(['prefix' => '/settings'], function () {
    Route::get('/', 'SettingsController@index');
    Route::post('set-setting', 'SettingsController@setSetting');
    Route::post('guardar-cabecera-factura', 'SettingsController@guardarCabeceraFactura');

    Route::post('roles-view', 'SettingsController@rolesView');
    Route::post('new-role', 'SettingsController@roleForm');
    Route::post('role-form/{role?}', 'SettingsController@roleForm');
    Route::post('insert-role', 'SettingsController@insertRole');
    Route::post('edit-role', 'SettingsController@editRole');
    Route::post('role-permissions-view/{role}', 'SettingsController@rolePermissionsView');
    Route::post('role-navigation-view/{role}', 'SettingsController@roleNavigationView');

    Route::post('permissions-view', 'SettingsController@permissionsView');
    Route::post('new-permission', 'SettingsController@roleForm');
    Route::post('permission-form/{permission?}', 'SettingsController@permissionForm');
    Route::post('insert-permission', 'SettingsController@insertPermission');
    Route::post('edit-permission', 'SettingsController@editPermission');
    Route::post('delete-permission', 'SettingsController@deletePermission');
    Route::post('role-set-permissions/{role}', 'SettingsController@roleSetPermissions');
    Route::post('role-set-navigation/{role}', 'SettingsController@roleSetNavigation');
});

Route::group(['prefix' => '/gate-controller', 'middleware' => ['role:ADMINISTRADOR']], function () {
    Route::get('/', 'GateController@index');
    Route::post('/get-access-log', 'GateController@getAccessLog');
    Route::post('/open-door', 'GateController@openDoor');
    Route::post('/open-door-v3', 'GateController@openDoorV3');
    Route::post('/create-card', 'GateController@createCard');
    Route::post('/create-card-lote', 'GateController@createCardLote');
    Route::post('/authorize-card', 'GateController@authorizeCard');
    Route::post('/deauthorize-card', 'GateController@deauthorizeCard');
});

Route::group(['prefix' => '/role', 'middleware' => ['role:ADMINISTRADOR']], function () {

    Route::get('/', 'RoleController@index');
});

Route::get('/local/locales-by-razon-social', 'LocalController@getLocalsByRazonSocial')->middleware(['role:ADMINISTRADOR|CONTABILIDAD|RECEPCIÓN']);

Route::group(['prefix' => '/local', 'middleware' => ['role:ADMINISTRADOR|CONTABILIDAD']], function () {

    Route::get('/', 'LocalController@index');
    Route::get('/carretas', 'LocalController@indexCarretas');
    Route::get('/tours', 'LocalController@indexTours');
    Route::get('/muelle', 'LocalController@indexMuelle');
    Route::get('/agencias', 'LocalController@indexAgencia');

    Route::post('/form/{tipo}/{local?}', 'LocalController@form');
    Route::post('/insert', 'LocalController@insert');
    Route::post('/edit/{local}', 'LocalController@edit');
    Route::post('/delete/{local}', 'LocalController@delete');

    //Rutas de contabilidad
    Route::get('/conta', 'LocalController@indexConta');
    Route::get('/conta/carretas', 'LocalController@indexCarretasConta');
    Route::get('/conta/tours', 'LocalController@indexToursConta');
    Route::get('/conta/muelle', 'LocalController@indexMuelleConta');
    Route::get('/conta/agencias', 'LocalController@indexAgenciaConta');

    Route::post('/conta/form/{tipo}/{local}', 'LocalController@formContabilidad');
    Route::post('/edit-contabilidad/{local}', 'LocalController@editContabilidad');

    Route::get('/conta/panel_pac', [SettingsController::class, 'panelPac']);
    Route::get('/conta/cabecera_factura', [SettingsController::class, 'cabeceraFactura']);
    Route::get('/conta/obtener_timbres_disponibles', 'SoapController@obtenerTimbresDisponibles');

    Route::get('/conta/bancos/', 'BancoController@index');
    Route::post('/conta/bancos/form/{banco?}', 'BancoController@form');
    Route::post('/conta/bancos/insert', 'BancoController@insert');
    Route::post('/conta/bancos/edit/{banco}', 'BancoController@edit');
    Route::post('/conta/bancos/delete/{banco}', 'BancoController@delete');

    Route::get('/conta/cfdis/', 'CfdiController@index');
    Route::post('/conta/cfdis/form/{cfdi?}', 'CfdiController@form');
    Route::post('/conta/cfdis/insert', 'CfdiController@insert');
    Route::post('/conta/cfdis/edit/{cfdi}', 'CfdiController@edit');
    Route::post('/conta/cfdis/delete/{cfdi}', 'CfdiController@delete');

    Route::get('/conta/formas_pago/', 'FormaPagoController@index');
    Route::post('/conta/formas_pago/form/{forma_pago?}', 'FormaPagoController@form');
    Route::post('/conta/formas_pago/insert', 'FormaPagoController@insert');
    Route::post('/conta/formas_pago/edit/{forma_pago}', 'FormaPagoController@edit');
    Route::post('/conta/formas_pago/delete/{forma_pago}', 'FormaPagoController@delete');

    Route::get('/conta/metodos_pago/', 'MetodoPagoController@index');
    Route::post('/conta/metodos_pago/form/{metodo_pago?}', 'MetodoPagoController@form');
    Route::post('/conta/metodos_pago/insert', 'MetodoPagoController@insert');
    Route::post('/conta/metodos_pago/edit/{metodo_pago}', 'MetodoPagoController@edit');
    Route::post('/conta/metodos_pago/delete/{metodo_pago}', 'MetodoPagoController@delete');

    Route::get('/conta/tipos_comprobante/', 'TipoComprobanteController@index');
    Route::post('/conta/tipos_comprobante/form/{tipo_comprobante?}', 'TipoComprobanteController@form');
    Route::post('/conta/tipos_comprobante/insert', 'TipoComprobanteController@insert');
    Route::post('/conta/tipos_comprobante/edit/{tipo_comprobante}', 'TipoComprobanteController@edit');
    Route::post('/conta/tipos_comprobante/delete/{tipo_comprobante}', 'TipoComprobanteController@delete');

    Route::get('/conta/claves_unidades/', 'ClaveUnidadController@index');
    Route::post('/conta/claves_unidades/form/{clave_unidad?}', 'ClaveUnidadController@form');
    Route::post('/conta/claves_unidades/insert', 'ClaveUnidadController@insert');
    Route::post('/conta/claves_unidades/edit/{clave_unidad}', 'ClaveUnidadController@edit');
    Route::post('/conta/claves_unidades/delete/{clave_unidad}', 'ClaveUnidadController@delete');

    Route::get('/conta/claves_prod_servs/', 'ClaveProdServController@index');
    Route::post('/conta/claves_prod_servs/form/{clave_prod_serv?}', 'ClaveProdServController@form');
    Route::post('/conta/claves_prod_servs/insert', 'ClaveProdServController@insert');
    Route::post('/conta/claves_prod_servs/edit/{clave_prod_serv}', 'ClaveProdServController@edit');
    Route::post('/conta/claves_prod_servs/delete/{clave_prod_serv}', 'ClaveProdServController@delete');

    Route::get('/conta/regimenes_fiscales/', 'RegimenFiscalController@index');
    Route::post('/conta/regimenes_fiscales/form/{regimen_fiscal?}', 'RegimenFiscalController@form');
    Route::post('/conta/regimenes_fiscales/insert', 'RegimenFiscalController@insert');
    Route::post('/conta/regimenes_fiscales/edit/{regimen_fiscal}', 'RegimenFiscalController@edit');
    Route::post('/conta/regimenes_fiscales/delete/{regimen_fiscal}', 'RegimenFiscalController@delete');

    Route::get('/conta/series/', 'SerieController@index');
    Route::post('/conta/series/form/{serie?}', 'SerieController@form');
    Route::post('/conta/series/insert', 'SerieController@insert');
    Route::post('/conta/series/edit/{serie}', 'SerieController@edit');
    Route::post('/conta/series/delete/{serie}', 'SerieController@delete');
});

Route::group(['prefix' => '/cat-cargo', 'middleware' => ['role:ADMINISTRADOR']], function () {

    Route::get('/', 'CatCargoController@index');

    Route::post('/form/{cargo?}', 'CatCargoController@form');
    Route::post('/insert', 'CatCargoController@insert');
    Route::post('/edit/{cargo}', 'CatCargoController@edit');
    Route::post('/delete/{cargo}', 'CatCargoController@delete');
    Route::post('/form-accesos/{cargo}', 'CatCargoController@formAccesos');
    Route::post('/set-accesos/{cargo}', 'CatCargoController@setAccesos');
});

Route::group(['prefix' => '/cat-acceso', 'middleware' => ['role:ADMINISTRADOR']], function () {

    Route::get('/', 'CatAccesoController@index');

    Route::post('/form/{acceso?}', 'CatAccesoController@form');
    Route::post('/insert', 'CatAccesoController@insert');
    Route::post('/edit/{acceso}', 'CatAccesoController@edit');
    Route::post('/delete/{acceso}', 'CatAccesoController@delete');
});

Route::group(['prefix' => '/empleado'], function () {

    Route::get('/', 'EmpleadoController@index')->middleware(['role:LOCATARIO']);
    Route::post('/form/{empleado?}', 'EmpleadoController@form')->middleware(['role:LOCATARIO']);
    Route::post('/insert-admin', 'EmpleadoController@insertAdmin')->middleware(['role:RECEPCIÓN']);
    Route::post('/edit-admin/{empleado}', 'EmpleadoController@editAdmin')->middleware(['role:RECEPCIÓN']);
    Route::post('/insert', 'EmpleadoController@insert')->middleware(['role:LOCATARIO|RECEPCIÓN']);
    Route::post('/edit/{empleado}', 'EmpleadoController@edit')->middleware(['role:LOCATARIO|RECEPCIÓN']);
    Route::post('/delete/{empleado}', 'EmpleadoController@delete')->middleware(['role:LOCATARIO|RECEPCIÓN']);
    Route::get('/get-json/{empleado}', 'EmpleadoController@getJSON');

    Route::get('/admin', 'EmpleadoController@indexAdmin')->middleware(['role:RECEPCIÓN']);
    Route::post('/form-admin/{empleado?}', 'EmpleadoController@formAdmin')->middleware(['role:RECEPCIÓN']);

    //    Route::get('/get-json/{empleado}','EmpleadoController@getJSON');
    Route::get('/get-solicitudes-proceso/{empleado}', 'EmpleadoController@getSolicitudesEnProceso');
});


Route::group(['prefix' => '/solicitud-gafete'], function () {

    Route::get('/', 'SolicitudGafeteController@index')->middleware(['role:LOCATARIO']);
    Route::post('/form/{solicitud?}', 'SolicitudGafeteController@form')->middleware(['role:LOCATARIO']);
    Route::post('/form-reapply/{solicitud}', 'SolicitudGafeteController@formReapply')->middleware(['role:LOCATARIO']);
    Route::post('/insert', 'SolicitudGafeteController@insert')->middleware(['role:LOCATARIO']);
    Route::post('/expres/{empleado}', 'SolicitudGafeteController@insertExpres')->middleware(['role:RECEPCIÓN']);
    // Route::post('/edit/{solicitud}',  'SolicitudGafeteController@edit');
    // Route::post('/delete/{solicitud}','SolicitudGafeteController@delete');

    Route::get('/recepcion', 'SolicitudGafeteController@indexRecepcion')->middleware(['role:RECEPCIÓN']);

    Route::post('/detalles/{solicitud}', 'SolicitudGafeteController@detallesView')->middleware(['role:RECEPCIÓN|LOCATARIO']);
    Route::get('/pdf/{solicitud}', 'SolicitudGafeteController@layoutPDF')->middleware(['role:RECEPCIÓN']);
    Route::get('/contraparte-gafete-acceso/{gafete}', 'SolicitudGafeteController@contraparteAccesoPdf')->middleware(['role:SEGURIDAD']);
    Route::get('/comprobante-pdf/{solicitud}', 'SolicitudGafeteController@comprobantePDF')->middleware(['role:RECEPCIÓN|LOCATARIO']);
    Route::post('/marcar-impreso/{solicitud}', 'SolicitudGafeteController@imprimirView')->middleware(['role:RECEPCIÓN']);
    Route::post('/prevalidar-comprobante/{solicitud}', 'SolicitudGafeteController@prevalidarComprobanteView')->middleware(['role:RECEPCIÓN']);
    Route::post('/rechazar/{solicitud}', 'SolicitudGafeteController@rechazarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/validar/{solicitud}', 'SolicitudGafeteController@validarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/entregar/{solicitud}', 'SolicitudGafeteController@entregarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/reapply/{solicitud}', 'SolicitudGafeteController@reapply')->middleware(['role:LOCATARIO']);
    Route::post('/do-marcar-pendiente-cobro/{solicitud}', 'SolicitudGafeteController@doMarcarPendienteCobro')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-marcar-cobrada/{solicitud}', 'SolicitudGafeteController@doMarcarCobrada')->middleware(['role:RECEPCIÓN']);

    // Route::get('/do-pdf/{solicitud}',  'SolicitudGafeteController@doPDF')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-rechazar', 'SolicitudGafeteController@doRechazar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-validar', 'SolicitudGafeteController@doValidar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-rechazar-comprobante', 'SolicitudGafeteController@doRechazarComprobante')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-imprimir', 'SolicitudGafeteController@doImprimir')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-imprimir-v3', 'SolicitudGafeteController@doImprimirV3')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-entregar', 'SolicitudGafeteController@doEntregar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-aceptar-comprobante', 'SolicitudGafeteController@doAceptarComprobante')->middleware(['role:RECEPCIÓN']);
});

Route::group(['prefix' => '/solicitud-gafete-reasignar'], function () {

    Route::get('/', 'SolicitudGafeteReasignarController@index')->middleware(['role:RECEPCIÓN']);
    Route::post('/form/{empleado}', 'SolicitudGafeteReasignarController@form')->middleware(['role:LOCATARIO']);
    Route::post('/insert', 'SolicitudGafeteReasignarController@insert')->middleware(['role:LOCATARIO']);
    Route::post('/update/{solicitud}', 'SolicitudGafeteReasignarController@update')->middleware(['role:LOCATARIO']);
    Route::delete('/delete/{solicitud}', 'SolicitudGafeteReasignarController@delete')->middleware(['role:LOCATARIO']);
    Route::post('/form-validar/{solicitud}', 'SolicitudGafeteReasignarController@formValidar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-validar/{solicitud}', 'SolicitudGafeteReasignarController@doValidar')->middleware(['role:RECEPCIÓN']);
    Route::post('/form-rechazar/{solicitud}', 'SolicitudGafeteReasignarController@formRechazar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-rechazar', 'SolicitudGafeteReasignarController@doRechazar')->middleware(['role:RECEPCIÓN']);
});

Route::group(['prefix' => '/comprobante-pago'], function () {

    Route::get('/', 'ComprobantePagoController@indexContabilidad')->middleware(['role:CONTABILIDAD']);

    Route::post('/form', 'ComprobantePagoController@form')->middleware(['role:LOCATARIO']);
    Route::post('/form-validar/{comprobante}', 'ComprobantePagoController@formValidar')->middleware(['role:CONTABILIDAD']);
    Route::post('/insert', 'ComprobantePagoController@insert')->middleware(['role:LOCATARIO']);

    Route::post('/do-factura-manual/{id}', 'ComprobantePagoController@doFacturaManual')->name('comprobantes.do_factura_manual')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-validar/{comprobante}', 'ComprobantePagoController@doValidar')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-rechazar/{comprobante}', 'ComprobantePagoController@doRechazar')->middleware(['role:CONTABILIDAD']);


    Route::post('/get-select-options-gafete/{local}', 'ComprobantePagoController@getSelectOptionsGafete')->middleware(['role:LOCATARIO']);
    Route::post('/get-select-options-factura/{local?}', 'ComprobantePagoController@getSelectOptionsFactura')->middleware(['role:LOCATARIO|CONTABILIDAD']);
    Route::post('/get-select-options-permiso-temporal/{local?}', 'ComprobantePagoController@getSelectOptionsPermisoTemporal')->middleware(['role:LOCATARIO|RECEPCIÓN']);
});

Route::group(['prefix' => '/permiso-temporal'], function () {

    Route::get('/', 'PermisoTemporalController@index')->middleware(['role:LOCATARIO']);
    Route::post('/form/{permiso?}', 'PermisoTemporalController@form')->middleware(['role:LOCATARIO']);
    Route::post('/form-reapply/{permiso}', 'PermisoTemporalController@formReapply')->middleware(['role:LOCATARIO']);
    Route::post('/insert', 'PermisoTemporalController@insert')->middleware(['role:LOCATARIO']);
    Route::post('/reapply/{permiso}', 'PermisoTemporalController@reapply')->middleware(['role:LOCATARIO']);;

    //------------------

    Route::get('/recepcion', 'PermisoTemporalController@indexRecepcion')->middleware(['role:RECEPCIÓN']);
    Route::post('/detalles/{permiso}', 'PermisoTemporalController@detallesView')->middleware(['role:RECEPCIÓN|SEGURIDAD']);

    Route::post('/asignar/{permiso}', 'PermisoTemporalController@asignarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/entregar/{permiso}', 'PermisoTemporalController@entregarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/recibir/{permiso}', 'PermisoTemporalController@recibirView')->middleware(['role:RECEPCIÓN']);

    Route::get('/formato-oficial-pdf/{permiso}', 'PermisoTemporalController@formatoOficialPdf')->middleware(['role:SEGURIDAD|LOCATARIO']);


    Route::post('/do-asignar', 'PermisoTemporalController@doAsignar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-entregar', 'PermisoTemporalController@doEntregar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-recibir', 'PermisoTemporalController@doRecibir')->middleware(['role:RECEPCIÓN']);

    //------------------

    Route::get('/seguridad', 'PermisoTemporalController@indexSeguridad')->middleware(['role:SEGURIDAD']);

    Route::post('/rechazar/{permiso}', 'PermisoTemporalController@rechazarView')->middleware(['role:SEGURIDAD']);
    Route::post('/aprobar/{permiso}', 'PermisoTemporalController@aprobarView')->middleware(['role:SEGURIDAD']);

    Route::post('/do-rechazar', 'PermisoTemporalController@doRechazar')->middleware(['role:SEGURIDAD']);
    Route::post('/do-aprobar', 'PermisoTemporalController@doAprobar')->middleware(['role:SEGURIDAD']);


    //------------------
    Route::get('/vencidos', 'PermisoTemporalController@indexVencidos')->middleware(['role:RECEPCIÓN']);

    Route::post('/recibir-extemporaneo/{permiso}', 'PermisoTemporalController@recibirExtemporaneoView')->middleware(['role:RECEPCIÓN']);
    Route::post('/pagar-extemporaneo/{permiso}', 'PermisoTemporalController@pagarExtemporaneoView')->middleware(['role:RECEPCIÓN']);

    Route::post('/do-recibir-extemporaneo', 'PermisoTemporalController@doRecibirExtemporaneo')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-pagar-extemporaneo', 'PermisoTemporalController@doPagarExtemporaneo')->middleware(['role:RECEPCIÓN']);
});


Route::group(['prefix' => '/gafete-preimpreso', 'middleware' => ['role:RECEPCIÓN']], function () {

    Route::get('/', 'GafetePreimpresoController@index');
    Route::get('/permisos-temporales', 'GafetePreimpresoController@indexPermisosTemporales');
    Route::get('/otro', 'GafetePreimpresoController@indexOtro');

    Route::post('/form-temporal/{gafete?}', 'GafetePreimpresoController@formTemporal');

    Route::post('/insert', 'GafetePreimpresoController@insert');
    Route::post('/edit/{gafete}', 'GafetePreimpresoController@edit');
    Route::post('/delete/{gafete}', 'GafetePreimpresoController@delete');
    Route::get('/pdf-permiso-temporal/{gafete}', 'GafetePreimpresoController@pdfPermisoTemporal');
});


Route::group(['prefix' => '/gafete-estacionamiento'], function () {

    Route::get('/', 'GafeteEstacionamientoController@index');

    Route::get('/locatario', 'GafeteEstacionamientoController@indexLocatario')->middleware(['role:LOCATARIO']);
    Route::get('/recepcion', 'SolicitudGafeteReasignarController@index')->middleware(['role:RECEPCIÓN']);

    Route::post('/form/{gafete?}', 'GafeteEstacionamientoController@form')->middleware(['role:LOCATARIO']);
    Route::post('/form-reapply/{gafete}', 'GafeteEstacionamientoController@formReapply')->middleware(['role:LOCATARIO']);
    Route::post('/detalles/{gafete}', 'GafeteEstacionamientoController@detallesView')->middleware(['role:RECEPCIÓN|LOCATARIO']);
    Route::get('/pdf/{gafete}', 'GafeteEstacionamientoController@layoutPDF')->middleware(['role:RECEPCIÓN']);
    Route::get('/comprobante-pdf/{gafete}', 'GafeteEstacionamientoController@comprobantePDF')->middleware(['role:RECEPCIÓN|LOCATARIO']);

    Route::post('/insert', 'GafeteEstacionamientoController@insert');
    Route::post('/edit/{gafete}', 'GafeteEstacionamientoController@edit');
    Route::post('/delete/{gafete}', 'GafeteEstacionamientoController@delete');
    Route::get('/pdf/{gafete}', 'GafeteEstacionamientoController@doPdf');
    Route::get('/contraparte-gafete-estacionamiento/{gafete}', 'GafeteEstacionamientoController@contraparteEstacionamientoPdf')->middleware(['role:SEGURIDAD']);

    Route::post('/prevalidar-comprobante/{gafete}', 'GafeteEstacionamientoController@prevalidarComprobanteView')->middleware(['role:RECEPCIÓN']);
    Route::post('/rechazar/{gafete}', 'GafeteEstacionamientoController@rechazarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/validar/{gafete}', 'GafeteEstacionamientoController@validarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/entregar/{gafete}', 'GafeteEstacionamientoController@entregarView')->middleware(['role:RECEPCIÓN']);
    Route::post('/marcar-impreso/{gafete}', 'GafeteEstacionamientoController@imprimirView')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-marcar-pendiente-cobro/{gafete}', 'GafeteEstacionamientoController@doMarcarPendienteCobro')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-marcar-cobrada/{gafete}', 'GafeteEstacionamientoController@doMarcarCobrada')->middleware(['role:RECEPCIÓN']);

    Route::post('/do-rechazar', 'GafeteEstacionamientoController@doRechazar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-validar', 'GafeteEstacionamientoController@doValidar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-rechazar-comprobante', 'GafeteEstacionamientoController@doRechazarComprobante')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-imprimir', 'GafeteEstacionamientoController@doImprimir')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-entregar', 'GafeteEstacionamientoController@doEntregar')->middleware(['role:RECEPCIÓN']);
    Route::post('/do-aceptar-comprobante', 'GafeteEstacionamientoController@doAceptarComprobante')->middleware(['role:RECEPCIÓN']);
});


Route::group(['prefix' => '/permiso-mantenimiento'], function () {

    Route::get('/', 'PermisoMantenimientoController@index')->middleware(['role:LOCATARIO']);
    Route::get('/recepcion', 'PermisoMantenimientoController@indexRecepcion')->middleware(['role:RECEPCIÓN']);
    Route::get('/mantenimiento', 'PermisoMantenimientoController@indexMantenimiento')->middleware(['role:MANTENIMIENTO']);

    Route::post('/detalles/{permiso}', 'PermisoMantenimientoController@detallesView')->middleware(['role:RECEPCIÓN|MANTENIMIENTO']);
    Route::post('/form/{permiso?}', 'PermisoMantenimientoController@form')->middleware(['role:LOCATARIO']);
    Route::post('/insert', 'PermisoMantenimientoController@insert')->middleware(['role:LOCATARIO']);

    Route::post('/form-reapply/{permiso}', 'PermisoMantenimientoController@formReapply')->middleware(['role:LOCATARIO']);
    Route::post('/reapply/{permiso}', 'PermisoMantenimientoController@reapply')->middleware(['role:LOCATARIO']);;

    Route::post('/form-aprobar/{permiso}', 'PermisoMantenimientoController@aprobarView')->middleware(['role:MANTENIMIENTO']);
    Route::post('/form-rechazar/{permiso}', 'PermisoMantenimientoController@rechazarView')->middleware(['role:MANTENIMIENTO']);

    Route::post('/do-aprobar/{permiso}', 'PermisoMantenimientoController@aprobar')->middleware(['role:MANTENIMIENTO']);
    Route::post('/do-rechazar/{permiso}', 'PermisoMantenimientoController@rechazar')->middleware(['role:MANTENIMIENTO']);

    Route::get('/formato-pdf-firmante/{permiso}', 'PermisoMantenimientoController@formatoPdfFirmante')->middleware(['role:MANTENIMIENTO|SEGURIDAD|LOCATARIO']);
    Route::get('/formato-pdf-simple/{permiso}', 'PermisoMantenimientoController@formatoPdfSimple')->middleware(['role:LOCATARIO']);
});


Route::group(['prefix' => '/factura'], function () {

    Route::get('/locatario', 'FacturaController@indexLocatario')->middleware(['role:LOCATARIO']);
    Route::post('/form/{factura?}', 'FacturaController@form')->middleware(['role:LOCATARIO|CONTABILIDAD']);
    Route::post('/validar-concepto', 'FacturaController@validarConcepto')->middleware(['role:LOCATARIO|CONTABILIDAD']);
    Route::post('/form-agregar-concepto', 'FacturaController@formAgregarConcepto')->middleware(['role:LOCATARIO|CONTABILIDAD']);
    Route::post('/form-cancelar-factura/{factura}', 'FacturaController@formCancelar')->middleware(['role:LOCATARIO|CONTABILIDAD']);

    Route::post('/send-mail-form/{factura}', 'FacturaController@formSendMail')->middleware(['role:CONTABILIDAD']);
    Route::post('/insert', 'FacturaController@insert')->middleware(['role:LOCATARIO|CONTABILIDAD']);
    Route::post('/do-eliminar/{factura}', 'FacturaController@doEliminar')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-capturar/{factura}', 'FacturaController@doCapturar')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-timbrar/{factura}', 'FacturaController@doTimbrar')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-cancelar', 'FacturaController@doCancelar')->middleware(['role:CONTABILIDAD']);
    Route::get('/do-download-xml/{factura}', 'FacturaController@doDownloadXml')->middleware(['role:CONTABILIDAD']);
    Route::get('/do-download-pdf/{factura}', 'FacturaController@doFormatoPdf')->middleware(['role:CONTABILIDAD']);
    Route::get('/do-download-listado-comprobantes/{factura}', 'FacturaController@doListadoComprobantesPdf')->middleware(['role:CONTABILIDAD']);
    Route::get('/do-export-excel', 'FacturaController@doExportExcel')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-send-mail', 'FacturaController@doSendMail')->middleware(['role:CONTABILIDAD']);
    Route::get('/form-asociar-comprobantes-factura-manual', 'FacturaController@formAsociarComprobantesFacturaManual')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-asociar-comprobantes-factura-manual', 'FacturaController@doAsociarComprobantesFacturaManual')->middleware(['role:CONTABILIDAD']);

    Route::post('/form-editar-sm/{factura}', 'FacturaController@formEditarSm')->middleware(['role:CONTABILIDAD']);
    Route::post('/do-editar-sm', 'FacturaController@doEditarSm')->middleware(['role:CONTABILIDAD']);

    //------------------

    Route::get('/contabilidad', 'FacturaController@indexContabilidad')->middleware(['role:CONTABILIDAD']);
    Route::get('/almacen-facturas', 'FacturaController@indexAlmacenFacturas')->middleware(['role:CONTABILIDAD']);
    Route::post('/form-contabilidad/{factura?}', 'FacturaController@formContabilidad')->middleware(['role:CONTABILIDAD']);
    Route::post('/form-contabilidad-global/{factura?}', 'FacturaController@formContabilidadGlobal')->middleware(['role:CONTABILIDAD']);
    Route::post('/form-factura-manual/{factura?}', 'FacturaController@formFacturaManual')->middleware(['role:CONTABILIDAD']);
    Route::post('/form-agregar-concepto-contabilidad', 'FacturaController@formAgregarConceptoContabilidad')->middleware(['role:CONTABILIDAD']);

    Route::post('/get-local-data/{local}', 'FacturaController@getLocalData')->middleware(['role:CONTABILIDAD']);
});


Route::group(['prefix' => '/barco'], function () {

    Route::get('/calendario', 'CalendarioBarcosController@index')->middleware(['role:RECEPCIÓN']);
    Route::get('/obtener-eventos', 'CalendarioBarcosController@obtenerEventos'); //->middleware(['role:RECEPCIÓN|LOCATARIO']);
    Route::post('/nuevo-evento-form/{dateStr}', 'CalendarioBarcosController@nuevoEventoForm')->middleware(['role:RECEPCIÓN']);
    Route::post('/editar-evento-form/{evento}', 'CalendarioBarcosController@editarEventoForm')->middleware(['role:RECEPCIÓN']);

    Route::post('/insert-event', 'CalendarioBarcosController@insertEvent')->middleware(['role:RECEPCIÓN']);
    Route::post('/update-event', 'CalendarioBarcosController@updateEvent')->middleware(['role:RECEPCIÓN']);
});

Route::group(['prefix' => '/inventario'], function () {

    Route::get('/', 'InventarioController@index')->middleware(['role:RECEPCIÓN']);
    Route::post('/form/{record?}', 'InventarioController@form')->middleware(['role:RECEPCIÓN']);
    Route::post('/form-baja/', 'InventarioController@formBaja')->middleware(['role:RECEPCIÓN']);
    Route::post('/insert', 'InventarioController@insert')->middleware(['role:RECEPCIÓN']);
    Route::post('/insert-baja', 'InventarioController@insertBaja')->middleware(['role:RECEPCIÓN']);
    //    Route::post('/edit/{record}',  'InventarioController@edit')->middleware(['role:RECEPCIÓN']);
    //    Route::post('/delete/{record}','InventarioController@delete')->middleware(['role:RECEPCIÓN']);
    Route::get('/get-json/{record}', 'InventarioController@getJSON');

    Route::get('/get-stock-data', 'InventarioController@getStockData');
});

Route::group(['prefix' => '/reportes'], function () {

    Route::get('/mantenimiento', 'ReporteController@indexMantenimiento')->middleware(['role:MANTENIMIENTO']);
    Route::get('/seguridad', 'ReporteController@indexSeguridad')->middleware(['role:SEGURIDAD']);
    Route::get('/recepcion', 'ReporteController@indexRecepcion')->middleware(['role:RECEPCIÓN']);
    Route::get('/locatario', 'ReporteController@indexLocatario')->middleware(['role:LOCATARIO']);
    Route::get('/contabilidad', 'ReporteController@indexContabilidad')->middleware(['role:LOCATARIO|CONTABILIDAD']);


    Route::get('/solicitudes-mantenimiento-vigentes', 'ReporteController@solicitudesMantenimientoVigentes')->middleware(['role:MANTENIMIENTO|RECEPCIÓN']);
    Route::get('/permisos-temporales', 'ReporteController@permisosTemporales')->middleware(['role:SEGURIDAD|RECEPCIÓN']);
    Route::get('/gafetes-impresos-acceso', 'ReporteController@gafetesImpresosAcceso')->middleware(['role:RECEPCIÓN|LOCATARIO']);
    Route::get('/gafetes-impresos-estacionamiento', 'ReporteController@gafetesImpresosEstacionamiento')->middleware(['role:RECEPCIÓN']);

    Route::get('/accesos-vehicular', 'ReporteController@accesosVehicular')->middleware(['role:SEGURIDAD']);
    Route::get('/accesos-vehicular-6pm', 'ReporteController@accesosVehicular6pm')->middleware(['role:SEGURIDAD']);

    Route::get('/accesos-gafete', 'ReporteController@accesosGafete')->middleware(['role:SEGURIDAD']);
    Route::get('/accesos-gafete-6pm', 'ReporteController@accesosGafete6pm')->middleware(['role:SEGURIDAD']);

    Route::get('/gafetes-desactivados', 'ReporteController@gafetesDesactivados')->middleware(['role:RECEPCIÓN']);

    Route::get('/comprobantes-pago', 'ReporteController@comprobantesPago')->middleware(['role:RECEPCIÓN|CONTABILIDAD']);

    Route::get('/saldo-locales', 'ReporteController@saldoLocales')->middleware(['role:RECEPCIÓN|CONTABILIDAD']);
});

Route::group(['prefix' => '/disenno_gafetes'], function () {
    Route::get('/', 'DisennoGafeteController@index')->middleware(['role:RECEPCIÓN']);
    Route::post('/form/{disenno_gafete?}', 'DisennoGafeteController@form');
    Route::post('/insert', 'DisennoGafeteController@insert');
    Route::post('/edit/{paquete_disenno_gafete}', 'DisennoGafeteController@edit');
    Route::post('/delete/{paquete_disenno_gafete}', 'DisennoGafeteController@delete');
    Route::post('/form_seleccionar/{paquete_disenno_gafete}', 'DisennoGafeteController@formSeleccionar');
    Route::post('/seleccionar_paquete', 'DisennoGafeteController@seleccionarPaquete');
    Route::get('/texto_back_locales_form', 'DisennoGafeteController@formTextoBackLocales');
    Route::get('/texto_back_admin_form', 'DisennoGafeteController@formTextoBackAdmin');
    Route::post('/guardar_texto_back_locales', 'DisennoGafeteController@guardarTextoBackLocales');
    Route::post('/guardar_texto_back_admin', 'DisennoGafeteController@guardarTextoBackAdmin');
});

Route::group(['prefix' => '/transferencias-saldo'], function () {
    Route::get('/', 'TransferenciaSaldoController@index')->middleware(['role:RECEPCIÓN']);
    Route::post('/form/{transferencia?}', 'TransferenciaSaldoController@form');
    Route::post('/insert', 'TransferenciaSaldoController@insert');
    Route::post('/edit/{transferencia}', 'TransferenciaSaldoController@edit');
    Route::post('/delete/{transferencia}', 'TransferenciaSaldoController@delete');
});

Route::group(['prefix' => '/notification'], function () {
    Route::post('/mark-as-read/{noti_id}', 'NotificationController@markAsRead');
});


Route::group(['prefix' => '/activity', 'middleware' => ['role:ADMINISTRADOR|SEGURIDAD']], function () {
    Route::get('/', 'ActivityController@index');
    Route::get('/seguridad', 'ActivityController@indexSeguridad');
    Route::post('detail/{activity}', 'ActivityController@detailsView');
});

Route::group(['prefix' => '/profile'], function () {
    Route::get('/', 'PerfilController@index')->name('perfil');
    Route::post('change-password', 'PerfilController@doChangePassword');
    Route::post('change-user-info', 'PerfilController@doChangeUserInfo');
    Route::post('change-local-info', 'PerfilController@doChangeLocalInfo');
});

Route::get('/notificaciones', 'NotificationController@index')->name('notifications.index');
Route::post('/check_all_notificaciones', 'NotificationController@markAsReadByUser')->name('notifications.check_all');
Route::post('/check_some_notificaciones', 'NotificationController@markSomeAsReadByUser')->name('notifications.check_some');
