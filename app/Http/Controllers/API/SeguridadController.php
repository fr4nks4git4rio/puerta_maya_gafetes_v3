<?php

namespace App\Http\Controllers\API;

use App\Controladora;
use App\Empleado;
use App\EmpleadoUbicacion;
use App\GafeteEstacionamiento;
use App\PermisoMantenimiento;
use App\PermisoTemporal;
use App\Services\ControladoraAccesoService;
use App\SolicitudGafete;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LogAcceso;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SeguridadController extends Controller
{
    public function loadPuertasVirtualesEntrada()
    {
        $controladora = Controladora::where('ctrl_usuario', '!=', '')->where('ctrl_contrasenna', '!=', '')->first();
        $puertas[] = ['door_id' => 0, 'door_nombre' => 'Seleccione...'];
        $controladora->Puertas()
            ->where('door_tipo', 'PEATONAL')
            ->where('door_direccion', 'ENTRADA')
            ->where('door_modo', 'VIRTUAL')
            ->get()
            ->map(function ($element) use (&$puertas) {
                $puertas[] = [
                    'door_id' => $element->door_id,
                    'door_nombre' => $element->door_nombre
                ];
            });
        return response()->json(['success' => true, 'data' => ['puertas' => $puertas]]);
    }
    public function loadPuertasVirtualesSalida()
    {
        $controladora = Controladora::where('ctrl_usuario', '!=', '')->where('ctrl_contrasenna', '!=', '')->first();
        $puertas[] = ['door_id' => 0, 'door_nombre' => 'Seleccione...'];
        $controladora->Puertas()
            ->where('door_tipo', 'PEATONAL')
            ->where('door_direccion', 'SALIDA')
            ->where('door_modo', 'VIRTUAL')
            ->get()
            ->map(function ($element) use (&$puertas) {
                $puertas[] = [
                    'door_id' => $element->door_id,
                    'door_nombre' => $element->door_nombre
                ];
            });
        return response()->json(['success' => true, 'data' => ['puertas' => $puertas]]);
    }

    public function loadVariablesGlobales()
    {
        $autos = cantidad_autos_dentro();
        $motos = cantidad_motos_dentro();
        return response()->json(['success' => true, 'data' => ['autos' => $autos, 'motos' => $motos]]);
    }
    public function checkGafete($gafete)
    {
        $data = $this->checkFormatGafete($gafete);
        if (!$data)
            return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Formato incorrecto de Gafete!']], 200);

        switch ($data['tipo']) {
            case 'GA':
                if ($data['separador'] === '-') {
                    $empleado = Empleado::where('empl_id', $data['identificador'])->withTrashed()->get();
                    if ($empleado->count() > 0 && $empleado->first()->GafeteAcceso()) {
                        $empleado = $empleado->first();
                        $data['info'] = [
                            'nombre_empleado' => $empleado->empl_nombre,
                            'estado' => $empleado->GafeteAcceso()->sgft_estado,
                            'vacunado' => $empleado->empl_vacuna_validada == 1,
                            'local' => $empleado->Local->lcal_nombre_comercial,
                            'vigencia' => settings()->get('anio_impresion', today()->year),
                            'comentarios' => $empleado->GafeteAcceso()->sgft_comentario_admin ? $empleado->GafeteAcceso()->sgft_comentario_admin : '',
                            'activo' => $empleado->empl_deleted_at === null
                        ];
                    } else {
                        return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Empleado no encontrado!']], 200);
                    }
                } elseif ($data['separador'] === '_') {
                    $solicitud = SolicitudGafete::where('sgft_id', $data['identificador'])->withTrashed()->get();
                    if ($solicitud->count() > 0) {
                        $solicitud = $solicitud->first();
                        $data['info'] = [
                            'nombre_empleado' => $solicitud->Empleado->empl_nombre,
                            'estado' => $solicitud->sgft_estado,
                            'vacunado' => $solicitud->Empleado->empl_vacuna_validada == 1,
                            'local' => $solicitud->Empleado->Local->lcal_nombre_comercial,
                            'vigencia' => settings()->get('anio_impresion', today()->year),
                            'comentarios' => $solicitud->sgft_comentario_admin ? $solicitud->sgft_comentario_admin : '',
                            'activo' => $solicitud->sgft_activated_at !== null && $solicitud->sgft_disabled_at === null && $solicitud->sgft_deleted_at === null
                        ];
                    } else {
                        return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Solicitud de Gafete no encontrada!']], 200);
                    }
                }
                break;
            case 'GE':
                $gafete = GafeteEstacionamiento::where('gest_id', $data['identificador'])->withTrashed()->get();
                if ($gafete->count() > 0) {
                    $gafete = $gafete->first();
                    $data['info'] = [
                        'numero' => $gafete->gest_numero . ' de ' . (strtolower($gafete->gest_tipo) == 'auto' ? $gafete->Local->lcal_espacios_autos : $gafete->Local->lcal_espacios_motos),
                        'tipo' => $gafete->gest_tipo,
                        'local' => $gafete->Local->lcal_nombre_comercial,
                        'comentarios' => $gafete->gest_comentario_admin ? $gafete->gest_comentario_admin : '',
                        'estado' => $gafete->gest_estado,
                        'activo' => $gafete->gest_disabled_at === null
                    ];
                } else {
                    return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Gafete no encontrado!']], 200);
                }
                break;
            case 'PT':
                $permiso_temp = PermisoTemporal::where('ptmp_id', $data['identificador'])->withTrashed()->get();
                if ($permiso_temp->count() > 0) {
                    $permiso_temp = $permiso_temp->first();
                    $data['info'] = [
                        'nombre_persona' => $permiso_temp->ptmp_nombre,
                        'estado' => $permiso_temp->ptmp_estado,
                        'local' => $permiso_temp->Local->lcal_nombre_comercial,
                        'vacunado' => $permiso_temp->ptmp_vacunado == 1,
                        'fecha_inicio' => Carbon::createFromFormat('Y-m-d', $permiso_temp->ptmp_vigencia_inicial)->format('d/m/Y'),
                        'fecha_fin' => Carbon::createFromFormat('Y-m-d', $permiso_temp->ptmp_vigencia_final)->format('d/m/Y'),
                    ];
                } else {
                    return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Permiso Temporal no encontrado!']], 200);
                }
                break;
            case 'PM':
                $permiso_mant = PermisoMantenimiento::where('pmtt_id', $data['identificador'])->withTrashed()->get();
                if ($permiso_mant->count() > 0) {
                    $permiso_mant = $permiso_mant->first();
                    $data['info'] = [
                        'nombre_empresa' => $permiso_mant->pmtt_empresa,
                        'representante' => $permiso_mant->pmtt_representante,
                        'estado' => $permiso_mant->pmtt_estado,
                        'listado_personas' => $permiso_mant->pmtt_listado_trabajadores,
                        'fecha_inicio' => Carbon::createFromFormat('Y-m-d', $permiso_mant->pmtt_vigencia_inicial)->format('d/m/Y'),
                        'fecha_fin' => Carbon::createFromFormat('Y-m-d', $permiso_mant->pmtt_vigencia_final)->format('d/m/Y'),
                        'trabajo_realizar' => $permiso_mant->pmtt_trabajo
                    ];
                } else {
                    return response()->json(['data' => ['tipo' => '', 'acceso' => false, 'msg' => 'Permiso de Mantenimiento no encontrado!'], 'error' => '!'], 200);
                }
                break;
        }

        $data = $this->checkAcceso($data);

        return response()->json(['data' => $data]);
    }

    public function darEntradaGafete($puerta, $gafete)
    {
        $data = $this->checkFormatGafete($gafete);
        if (!$data)
            return response()->json(['data' => ['success' => false, 'msg' => 'Formato incorrecto de Gafete!']], 200);

        switch ($data['tipo']) {
            case 'GA':
                if ($data['separador'] === '-') {
                    $empleado = Empleado::where('empl_id', $data['identificador'])->withTrashed()->first();
                    if ($empleado && $empleado->GafeteAcceso()) {
                        if ($empleado->Ubicacion && $empleado->Ubicacion->emplub_ubicacion == 1)
                            return response()->json(['data' => ['success' => false, 'msg' => 'El empleado se encuentra dentro del recinto!']], 200);
                    } else {
                        return response()->json(['data' => ['success' => false, 'msg' => 'Empleado no encontrado!']], 200);
                    }
                } elseif ($data['separador'] === '_') {
                    $solicitud = SolicitudGafete::where('sgft_id', $data['identificador'])->withTrashed()->first();
                    if ($solicitud->count() > 0) {
                        if ($solicitud->Empleado->Ubicacion && $solicitud->Empleado->Ubicacion->emplub_ubicacion == 1)
                            return response()->json(['data' => ['success' => false, 'msg' => 'El empleado se encuentra dentro del recinto!']], 200);
                        $empleado = $solicitud->Empleado;
                    } else {
                        return response()->json(['data' => ['success' => false, 'msg' => 'Solicitud de Gafete no encontrada!']], 200);
                    }
                }
                break;
            case 'GE':
            case 'PT':
            case 'PM':
                return response()->json(['data' => ['success' => false, 'msg' => 'No se puede dar salida al gafete.']], 200);
        }

        DB::beginTransaction();

        try {
            $fecha = now();
            LogAcceso::create([
                'lgac_card_number' => $empleado->GafeteAcceso()->sgft_numero,
                'lgac_empl_id' => $empleado->empl_id,
                'lgac_tipo' => 'SALIDA',
                'lgac_in_out_state' => 0,
                'lgac_ctrl_id' => $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id,
                'lgac_source' => 'mobile_app',
                'lgac_time' => $fecha->format('Y-m-d') . 'T' . $fecha->format('H:i:s') . 'Z'
            ]);
            $controllerService = new ControladoraAccesoService(Controladora::find($empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id));
            $data = [
                'empleado' => $empleado,
                'puertas_numeros' => implode(',', $empleado->GafeteAcceso()->Puertas()->where('door_direccion', 'ENTRADA')->where('door_modo', 'FISICA')->pluck('door_numero')->toArray())
            ];
            $ubicacion = DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->first();
            if ($ubicacion) {
                DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->update([
                    'emplub_door_in_id' => $puerta,
                    'emplub_door_out_id' => null,
                    'emplub_ubicacion' => 1,
                    'emplub_fecha' => now()
                ]);
            } else {
                DB::table('empleados_ubicacion')->insert([
                    'emplub_empl_id' => $empleado->empl_id,
                    'emplub_door_in_id' => $puerta,
                    'emplub_door_out_id' => null,
                    'emplub_ubicacion' => 1,
                    'emplub_fecha' => now()
                ]);
            }
            $controllerService->updatePerson($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Ha ocurrido un error intentando dar entrada al empleado. Error: {$e->getMessage()}");
            return response()->json(['data' => ['success' => false, 'msg' => "Ha ocurrido un error intentando dar entrada al empleado."]]);
        }

        return response()->json(['data' => ['success' => true]]);
    }
    public function darSalidaGafete($puerta, $gafete)
    {
        $data = $this->checkFormatGafete($gafete);
        if (!$data)
            return response()->json(['data' => ['tipo' => '', 'success' => false, 'msg' => 'Formato incorrecto de Gafete!']], 200);

        switch ($data['tipo']) {
            case 'GA':
                if ($data['separador'] === '-') {
                    $empleado = Empleado::where('empl_id', $data['identificador'])->withTrashed()->get();
                    if ($empleado->count() > 0 && $empleado->first()->GafeteAcceso()) {
                        $empleado = $empleado->first();
                        if ($empleado->Ubicacion && $empleado->Ubicacion->emplub_ubicacion == 0)
                            return response()->json(['data' => ['success' => false, 'msg' => 'El empleado no se encuentra dentro del recinto!']], 200);
                    } else {
                        return response()->json(['data' => ['success' => false, 'msg' => 'Empleado no encontrado!']], 200);
                    }
                } elseif ($data['separador'] === '_') {
                    $solicitud = SolicitudGafete::where('sgft_id', $data['identificador'])->withTrashed()->get();
                    if ($solicitud->count() > 0) {
                        $solicitud = $solicitud->first();
                        if ($solicitud->Empleado->Ubicacion && $solicitud->Empleado->Ubicacion->emplub_ubicacion == 0)
                            return response()->json(['data' => ['success' => false, 'msg' => 'El empleado no se encuentra dentro del recinto!']], 200);
                        $empleado = $solicitud->Empleado;
                    } else {
                        return response()->json(['data' => ['success' => false, 'msg' => 'Solicitud de Gafete no encontrada!']], 200);
                    }
                }
                break;
            case 'GE':
            case 'PT':
            case 'PM':
                return response()->json(['data' => ['success' => false, 'msg' => 'No se puede dar salida al gafete.']], 200);
        }

        DB::beginTransaction();

        try {
            $fecha = now();
            LogAcceso::create([
                'lgac_card_number' => $empleado->GafeteAcceso()->sgft_numero,
                'lgac_empl_id' => $empleado->empl_id,
                'lgac_tipo' => 'SALIDA',
                'lgac_in_out_state' => 0,
                'lgac_ctrl_id' => $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id,
                'lgac_source' => 'mobile_app',
                'lgac_time' => $fecha->format('Y-m-d') . 'T' . $fecha->format('H:i:s') . 'Z'
            ]);
            $controllerService = new ControladoraAccesoService(Controladora::find($empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id));
            // $ubicacion = $empleado->Ubicacion;
            // if ($ubicacion->exists() && in_array($ubicacion->PuertaEntrada->door_tipo, ['AUTO', 'MOTO']))
            //     $numeros = implode(',', $empleado->GafeteAcceso()->Puertas()->where('door_direccion', 'ENTRADA')->where('door_tipo', 'PEATONAL')->where('door_modo', 'FISICA')->pluck('door_numero')->toArray());
            // else
            $numeros = implode(',', $empleado->GafeteAcceso()->Puertas()->where('door_direccion', 'SALIDA')->where('door_modo', 'FISICA')->pluck('door_numero')->toArray());
            $data = [
                'empleado' => $empleado,
                'puertas_numeros' => $numeros
            ];
            $ubicacion = DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->first();
            if ($ubicacion) {
                DB::table('empleados_ubicacion')->where('emplub_empl_id', $ubicacion->emplub_empl_id)->update([
                    'emplub_door_out_id' => $puerta,
                    'emplub_ubicacion' => 0,
                    'emplub_fecha' => now()
                ]);
            } else {
                DB::table('empleados_ubicacion')->insert([
                    'emplub_empl_id' => $empleado->empl_id,
                    'emplub_door_out_id' => $puerta,
                    'emplub_ubicacion' => 0,
                    'emplub_fecha' => now()
                ]);
            }
            $controllerService->updatePerson($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Ha ocurrido un error intentando dar salida al empleado. Error: {$e->getMessage()}");
            return response()->json(['data' => ['success' => false, 'msg' => "Ha ocurrido un error intentando dar salida al empleado."]]);
        }

        return response()->json(['data' => ['success' => true]]);
    }

    public function recibirEventoStream(Request $request)
    {
        $data = $request->input();
        if (isset($data['AccessControllerEvent']) && $data['AccessControllerEvent']['majorEventType'] == 5 && $data['AccessControllerEvent']['subEventType'] == 1) {
            DB::beginTransaction();
            try {
                $event = $data['AccessControllerEvent'];
                $empleado = Empleado::find($event['employeeNoString']);
                if ($empleado) {
                    $controladora = Controladora::where('ctrl_id', $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id)->first();
                    $door = DB::table('puertas')
                        ->where('door_controladora_id', $controladora->ctrl_id)
                        ->where('door_numero', $event['doorNo'])
                        ->first();
                    $logAcceso = LogAcceso::firstOrCreate([
                        'lgac_serial_no' => $event['serialNo']
                    ], [
                        'lgac_mac_address' => $data['macAddress'],
                        'lgac_ip' => $data['ipAddress'],
                        'lgac_major' => $event['majorEventType'],
                        'lgac_minor' => $event['subEventType'],
                        'lgac_card_number' => $event['cardNo'],
                        'lgac_lector' => $event['cardReaderNo'],
                        'lgac_door_number' => $event['doorNo'],
                        'lgac_door_id' => $door->door_id,
                        'lgac_ctrl_id' => $door->door_controladora_id,
                        'lgac_empl_id' => $event['employeeNoString'],
                        'lgac_tipo' => $door->door_direccion,
                        'lgac_in_out_state' => $door->door_direccion === 'ENTRADA',
                        'lgac_source' => 'event_notification_stream',
                        'lgac_time' => $data['dateTime'],
                        'lgac_payload' => json_encode($data),
                        'lgac_created_at' => str_replace(['T', 'Z'], [' ', ''], $data['dateTime'])
                    ]);
                    $controllerService = new ControladoraAccesoService($controladora);

                    if ($door->door_direccion == 'SALIDA') {
                        $numeros = implode(
                            ',',
                            $empleado->GafeteAcceso()
                                ->Puertas()
                                ->where('door_direccion', 'ENTRADA')
                                ->where('door_modo', 'FISICA')
                                ->pluck('door_numero')->toArray()
                        );
                    } else {
                        $numeros = implode(
                            ',',
                            $empleado->GafeteAcceso()
                                ->Puertas()
                                ->where('door_direccion', 'SALIDA')
                                ->where('door_modo', 'FISICA')
                                ->pluck('door_numero')->toArray()
                        );
                    }
                    $data = [
                        'empleado' => $empleado,
                        'puertas_numeros' => $numeros
                    ];
                    $res = $controllerService->updatePerson($data);

                    if (!$res['success']) {
                        Log::error("Ha ocurrido un error recibiendo el evento desde la conrtroladora. Error: " . $res['message']);
                        DB::rollBack();
                        return ['success' => false];
                    }

                    $ubicacion = DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->first();
                    $autos = $ubicacion ? max($ubicacion->emplub_autos, 0) : 0;
                    $motos = $ubicacion ? max($ubicacion->emplub_motos, 0) : 0;

                    $autos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'AUTO' ? ($autos + 1) : $autos) : ($door->door_tipo == 'AUTO' ? ($autos - 1) : $autos);
                    $motos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'MOTO' ? ($motos + 1) : $motos) : ($door->door_tipo == 'MOTO' ? ($motos - 1) : $motos);

                    if (!$ubicacion) {
                        if ($door->door_direccion == 'ENTRADA') {
                            DB::table('empleados_ubicacion')->insert([
                                'emplub_empl_id' => $empleado->empl_id,
                                'emplub_lcal_id' => $empleado->empl_lcal_id,
                                'emplub_door_in_id' => $door->door_id,
                                'emplub_door_out_id' => null,
                                'emplub_ubicacion' => 1,
                                'emplub_fecha' => $logAcceso->lgac_created_at,
                                'emplub_autos' => $autos,
                                'emplub_motos' => $motos
                            ]);
                        } else {
                            DB::table('empleados_ubicacion')->insert([
                                'emplub_empl_id' => $empleado->empl_id,
                                'emplub_lcal_id' => $empleado->empl_lcal_id,
                                'emplub_door_in_id' => null,
                                'emplub_door_out_id' => $door->door_id,
                                'emplub_ubicacion' => 0,
                                'emplub_fecha' => $logAcceso->lgac_created_at,
                                'emplub_autos' => $autos,
                                'emplub_motos' => $motos
                            ]);
                        }
                    } else {
                        if ($door->door_direccion == 'ENTRADA') {
                            DB::table('empleados_ubicacion')
                                ->where('emplub_empl_id', $empleado->empl_id)
                                ->update([
                                    'emplub_door_in_id' => $door->door_id,
                                    'emplub_door_out_id' => null,
                                    'emplub_ubicacion' => 1,
                                    'emplub_fecha' => $logAcceso->lgac_created_at,
                                    'emplub_autos' => $autos,
                                    'emplub_motos' => $motos
                                ]);
                        } else {
                            DB::table('empleados_ubicacion')
                                ->where('emplub_empl_id', $empleado->empl_id)
                                ->update([
                                    'emplub_door_out_id' => $door->door_id,
                                    'emplub_ubicacion' => 0,
                                    'emplub_fecha' => $logAcceso->lgac_created_at,
                                    'emplub_autos' => $autos,
                                    'emplub_motos' => $motos
                                ]);
                        }
                    }

                    DB::commit();
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Ha ocurrido un error intentando procesar el evento recibido desde el streamer. Error: {$e->getMessage()}");
                return ['success' => false];
            }
        }
        return ['success' => true];
    }

    private function checkFormatGafete($gafete)
    {
        $gafete_tmp = explode("-", $gafete);

        if (count($gafete_tmp) === 2 && is_int((int)$gafete_tmp[1]) && array_key_exists($gafete_tmp[0], array_flip(['GA', 'GE', 'PT', 'PM']))) {
            return [
                'tipo' => $gafete_tmp[0],
                'identificador' => $gafete_tmp[1],
                'separador' => "-"
            ];
        }

        $gafete_tmp = explode("_", $gafete);

        if (count($gafete_tmp) === 2 && is_int((int)$gafete_tmp[1]) && array_key_exists($gafete_tmp[0], array_flip(['GA', 'GE', 'PT', 'PM']))) {
            return [
                'tipo' => $gafete_tmp[0],
                'identificador' => $gafete_tmp[1],
                'separador' => "_"
            ];
        }

        return false;
    }

    private function checkAcceso($data)
    {
        $access = true;
        $message = '';
        switch ($data['tipo']) {
            case 'GA':
                if (!$data['info']['vacunado']) {
                    $access = false;
                    $message = 'Empleado no Vacunado!';
                    break;
                }
                if (!$data['info']['activo']) {
                    $access = false;
                    $message = 'El Empleado causó baja!';
                    break;
                }
                break;
            case 'GE':
                if ($data['info']['estado'] === 'CANCELADA') {
                    $access = false;
                    $message = 'El Gafete ha sido Cancelado!';
                    break;
                }
                if (!$data['info']['activo']) {
                    $access = false;
                    $message = 'El Gafete ha sido desactivado!';
                    break;
                }
                break;
            case 'PT':
                if (Carbon::createFromFormat('d/m/Y', $data['info']['fecha_inicio'])->format('Y-m-d') > today()->format('Y-m-d')) {
                    $access = false;
                    $message = 'El Permiso Temporal aun no está vigente!';
                    break;
                }
                if (Carbon::createFromFormat('d/m/Y', $data['info']['fecha_fin'])->format('Y-m-d') < today()->format('Y-m-d')) {
                    $access = false;
                    $message = 'Permiso Temporal concluido!';
                    break;
                }
                if (!$data['info']['vacunado']) {
                    $access = false;
                    $message = 'Trabajador no Vacunado!';
                    break;
                }
                if (!array_key_exists($data['info']['estado'], array_flip(['APROBADO', 'ENTREGADO']))) {
                    $access = false;
                    $message = 'Estado de Permiso: ' . $data['info']['estado'] . '!';
                }
                break;
            case 'PM':
                if (Carbon::createFromFormat('d/m/Y', $data['info']['fecha_inicio'])->format('Y-m-d') > today()->format('Y-m-d')) {
                    $access = false;
                    $message = 'El Permiso de Mantenimiento aun no está vigente!';
                    break;
                }
                if (Carbon::createFromFormat('d/m/Y', $data['info']['fecha_fin'])->format('Y-m-d') < today()->format('Y-m-d')) {
                    $access = false;
                    $message = 'Permiso de Mantenimiento concluido!';
                    break;
                }
                break;
        }

        $data['acceso'] = $access;
        $data['msg'] = '';
        if (!$access) {
            $data['msg'] = $message;
        }

        return $data;
    }
}
