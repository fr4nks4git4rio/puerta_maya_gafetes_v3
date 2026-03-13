<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjeta;
use App\Actions\ActivarTarjetaV2;
use App\Actions\ActivarTarjetaV3;
use App\Actions\CrearTarjetaV2;
use App\Actions\CrearTarjetaV3;
use App\Actions\DesactivarTarjeta;
use App\Actions\DesactivarTarjetaV2;
use App\Actions\EliminarTarjetaV3;
use App\Clases\DoorCommandGenerator;
use App\ComprobantePago;
use App\Controladora;
use App\Factura;

use App\Notifications\ComprobanteRechazado;
use App\Notifications\SolicitudGafeteImpreso;
use App\Notifications\SolicitudGafeteRechazado;
use App\Puerta;
use App\Reports\ComprobanteSolicitudReport;
use App\Reports\ContraparteGafeteAccesoReport;
use App\Services\ControladoraAccesoService;
use App\User;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Empleado;
use App\Local;
use App\SolicitudGafete;

use App\Reports\GafeteBaseReport;
use App\VGafetesRfidV3;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SolicitudGafeteController extends Controller
{

    protected $rules = [
        'insert' => [
            'sgft_lcal_id' => 'required:exists:locales,lcal_id',
            'sgft_empl_id' => 'required:exists:empleados,empl_id',
            'sgft_permisos' => ['required', 'array'],
            'sgft_tipo' => 'required',
            'sgft_anio' => 'nullable',
            //            'sgft_cpag_id'     => 'required_without:sgft_gratuito',
            'sgft_cpag_id' => 'nullable',
            'sgft_comentario' => 'nullable',
            'sgft_gratuito' => '',
            'sgft_gafete_reposicion' => 'nullable',
            //            'sgft_file_comprobante' => 'required|file'
        ],

        'do-rechazar' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
            'sgft_comentario_admin' => 'required',
        ],

        'do-validar' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
            //            'sgft_comentario_admin'     => 'required',
        ],

        'do-rechazar-comprobante' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
            'sgft_comentario_admin' => 'required',
        ],


        'do-aceptar-comprobante' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
        ],


        'do-imprimir' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
            'sgft_numero' => 'required|digits_between:5,10',
            // 'sgft_comentario_admin'     => 'required',
        ],

        'do-entregar' => [
            'sgft_id' => 'required:exists:solicitudes_gafetes,sgft_id',
            // 'sgft_comentario_admin'     => 'required',
        ],

        'reapply' => [
            'sgft_id' => 'required|exists:solicitudes_gafetes,sgft_id',
            'sgft_empl_id' => 'required|exists:empleados,empl_id',
            'sgft_tipo' => 'required',
            'sgft_permisos' => ['required', 'array'],
            'sgft_cpag_id' => 'nullable',
            'sgft_comentario' => 'nullable',
            'sgft_gratuito' => '',
        ],

    ];

    protected $etiquetas = [
        'sgft_empl_id' => 'Empleado',
        'sgft_lcal_id' => 'Local',
        'sgft_tipo' => 'Tipo',
        'sgft_permisos' => 'Permisos',
        'sgft_anio' => 'Año',
        'sgft_cpag_id' => 'Comprobante',
        'sgft_comentario_admin' => 'Comentario',
        'sgft_gratuito' => 'Gafete gratuito',
        'sgft_numero' => 'Número Tarjeta',
        'sgft_gafete_reposicion' => 'Gafete a reponer',

    ];

    protected $system_folder_empleados = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }

    /**
     * para el LOCATARIO
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \Exception
     */
    public function index(Request $request, Builder $htmlBuilder)
    {

        //figure out the local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;
        // dd($usuario,$local);

        if ($request->ajax()) {
            return Datatables::of(
                SolicitudGafete::select([
                    'sgft_id',
                    'sgft_empl_id',
                    'sgft_thumb',
                    'sgft_nombre',
                    'sgft_cargo',
                    'sgft_tipo',
                    'sgft_permisos',
                    'sgft_fecha',
                    'sgft_estado',
                    'sgft_comentario',
                    'sgft_comentario_admin',
                    'sgft_gratuito'
                ])
                    ->whereSgftLcalId($local->lcal_id)
                    //                                ->whereIn('sgft_estado',['PENDIENTE','IMPRESA'])
                    ->whereRaw("( sgft_estado in ('PENDIENTE','IMPRESA','PENDIENTE DE COBRO') or (sgft_estado = 'CANCELADA' AND date(sgft_updated_at) >= curdate() - INTERVAL 3 DAY ) ) ")
            )
                ->editColumn('sgft_thumb', function (SolicitudGafete $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->sgft_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('sgft_comentario', function (SolicitudGafete $model) {
                    $html = '<small>';

                    $html .= $model->sgft_comentario;

                    if ($model->sgft_comentario_admin != "")
                        $html .= '<br/><b class="text-info">' . $model->sgft_comentario_admin . '</b>';

                    $html .= '</small>';

                    return $html;
                })
                ->editColumn('sgft_tipo', function (SolicitudGafete $model) {

                    $color = 'badge-info';
                    if ($model->sgft_tipo == 'REPOSICIÓN') $color = 'badge-primary';
                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgft_tipo . '</small>';

                    if ($model->sgft_gratuito == 1):
                        $html .= '<br/><small class="badge badge-success"> <i class="zmdi zmdi-money-off"></i> GRATUITO</small>';
                    endif;
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('sgft_estado', function (SolicitudGafete $model) {

                    $color = 'badge-primary';
                    if ($model->sgft_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->sgft_estado == 'PENDIENTE DE COBRO') $color = 'badge-pink';
                    if ($model->sgft_estado == 'IMPRESA') $color = 'badge-success';
                    if ($model->sgft_estado == 'ENTREGADA') $color = 'badge-inverse';
                    if ($model->sgft_estado == 'CANCELADA') $color = 'badge-danger';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgft_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->filterColumn('sgft_comentario', function ($query, $keyword) {
                    $query->whereRaw(" CONCAT(sgft_comentario, ' ', sgft_comentario_admin, IF(sgft_gratuito = 1, ' GRATUITO','')) like ?", ["%{$keyword}%"]);
                })
                ->addColumn('actions', function (SolicitudGafete $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    if ($model->sgft_estado === 'PENDIENTE DE COBRO')
                        $html .= '<span class="btn btn-primary btn-sm btn-capturar-comprobante" title="Capturar Comprobante" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-center-focus-weak"></i></span>';
                    else
                        $html .= '<span class="btn btn-primary btn-sm btn-comprobante-pdf" title="Comprobante" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-collection-pdf"></i></span>';

                    //                            $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato Gafete" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-accounts-list-alt"></i></span>';
                    //                            $html .= '<span class="btn btn-primary btn-sm btn-imprimir" title="Marcar como impreso" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    //                            $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    //                            $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['sgft_thumb', 'sgft_comentario', 'sgft_tipo', 'sgft_estado', 'actions'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'sgft_id', 'name' => 'sgft_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'sgft_thumb', 'name' => 'sgft_thumb', 'title' => 'Foto', 'search' => false, 'width' => '5%'])
            ->addColumn(['data' => 'sgft_nombre', 'name' => 'sgft_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'sgft_cargo', 'name' => 'sgft_cargo', 'title' => 'Cargo'])
            ->addColumn(['data' => 'sgft_fecha', 'name' => 'sgft_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'sgft_tipo', 'name' => 'sgft_tipo', 'title' => 'Tipo'])
            ->addColumn(['data' => 'sgft_permisos', 'name' => 'sgft_permisos', 'title' => 'Permisos'])
            ->addColumn(['data' => 'sgft_comentario', 'name' => 'sgft_comentario', 'title' => 'Comentarios'])
            //            ->addColumn(['data' => 'sgft_gratuito', 'name' => 'sgft_gratuito', 'title' => 'Gratuito'])
            ->addColumn(['data' => 'sgft_estado', 'name' => 'sgft_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);;


        return view('web.solicitud-gafete.index', compact('dataTable', 'local'));
    }

    public function form(SolicitudGafete $solicitud = null, Request $request)
    {

        $url = ($solicitud == null) ? url('solicitud-gafete/insert') : url('solicitud-gafete/edit', $solicitud->getKey());

        //determinamos el local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;

        $saldos = $local->getSaldos();

        $empleados = Empleado::selectRaw('empl_id , empl_nombre')
            ->whereEmplLcalId($local->lcal_id)
            ->get()
            ->pluck('empl_nombre', 'empl_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $gafetesGratis = $local->gafetesGratuitosDisponibles();

        $comprobantes = ['' => 'Recuperando comprobantes capturados...'];

        $gafetesAuto = $local->gafetesEstacionamientoAutoDisponibles();
        $gafetesMoto = $local->gafetesEstacionamientoMotoDisponibles();

        $gafetes_activos = ['' => 'Recuperando gafetes activos...'];
        $gafetes_activos = SolicitudGafete::getGafetesEstacionaminetoActivos($local);
        $gafetes_activos_motos = $gafetes_activos->where('gest_tipo', '=', 'MOTO');
        $gafetes_activos_autos = $gafetes_activos->where('gest_tipo', '=', 'AUTO');

        if (count($gafetes_activos) > 0) {
            $gafetes_activos = $gafetes_activos->pluck('local_description', 'gest_id');
        }

        if (count($gafetes_activos_motos) > 0) {
            $gafetes_activos_motos = $gafetes_activos_motos->pluck('local_description', 'gest_id');
        }

        if (count($gafetes_activos_autos) > 0) {
            $gafetes_activos_autos = $gafetes_activos_autos->pluck('local_description', 'gest_id');
        }

        return view('web.solicitud-gafete.form', compact(
            'solicitud',
            'url',
            'empleados',
            'gafetesGratis',
            'comprobantes',
            'saldos',
            'local',
            'gafetesAuto',
            'gafetesMoto',
            'gafetes_activos',
            'gafetes_activos_autos',
            'gafetes_activos_motos'
        ));
    }

    public function formReapply(SolicitudGafete $solicitud, Request $request)
    {
        if ($solicitud->Empleado()->withTrashed()->first()->trashed()) {
            return '<p class="alert alert-danger">El empleado ya ha sido dado de baja</p>';
        }
        $solicitud->sgft_permisos = explode(",", $solicitud->sgft_permisos);

        $url = url('solicitud-gafete/reapply', $solicitud->getKey());

        $usuario = \Auth::getUser();
        $local = $usuario->Local;
        $saldos = $local->getSaldos();

        $empleados = Empleado::selectRaw('empl_id , empl_nombre')
            ->whereEmplLcalId($local->lcal_id)
            ->get()
            ->pluck('empl_nombre', 'empl_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $gafetesGratis = $local->gafetesGratuitosDisponibles();

        $comprobantes = ['' => 'Recuperando comprobantes capturados...'];

        $gafetesGratisAuto = $local->gafetesGratuitosAutoDisponibles();
        $gafetesGratisMoto = $local->gafetesGratuitosMotoDisponibles();

        $gafetesPagoAuto = $local->gafetesEstacionamientoAutoDisponibles();
        $gafetesPagoMoto = $local->gafetesEstacionamientoMotoDisponibles();

        $gafetes_activos = ['' => 'Recuperando gafetes activos...'];
        $gafetes_activos = SolicitudGafete::getGafetesEstacionaminetoActivos($local);
        $gafetes_activos_motos = $gafetes_activos->where('gest_tipo', '=', 'MOTO');
        $gafetes_activos_autos = $gafetes_activos->where('gest_tipo', '=', 'AUTO');

        if (count($gafetes_activos) > 0) {
            $gafetes_activos = $gafetes_activos->pluck('local_description', 'gest_id');
        }

        if (count($gafetes_activos_motos) > 0) {
            $gafetes_activos_motos = $gafetes_activos_motos->pluck('local_description', 'gest_id');
        }

        if (count($gafetes_activos_autos) > 0) {
            $gafetes_activos_autos = $gafetes_activos_autos->pluck('local_description', 'gest_id');
        }

        return view('web.solicitud-gafete.form', compact(
            'solicitud',
            'url',
            'empleados',
            'gafetesGratis',
            'comprobantes',
            'saldos',
            'local',
            'gafetesGratisAuto',
            'gafetesGratisMoto',
            'gafetesPagoAuto',
            'gafetesPagoMoto',
            'gafetes_activos',
            'gafetes_activos_autos',
            'gafetes_activos_motos'
        ));
    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $permisos = implode(',', $this->data['sgft_permisos']);
            if (Str::contains($permisos, ['AUTO', 'MOTO']) && $this->data['sgft_tipo'] === 'REPOSICIÓN') {
                $validator = Validator::make($this->data, [
                    'sgft_gafete_reposicion' => 'required'
                ], array(), $this->etiquetas);
                if ($validator->fails()) {
                    $this->errors = $validator->messages();
                    return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
                }
            }
            DB::beginTransaction();
            try {

                //Se aplica e costo del gafete y dependiente del estado suma al saldo real o saldo
                //en transito
                //                $this->data['sgft_costo'] = ($this->data['sgft_tipo'] == 'PRIMERA VEZ')? settings()->get('gft_tarifa_1', '0' ) : settings()->get('gft_tarifa_2', '0' );
                //                $this->data['sgft_costo'] = 0; //ahora el costo del gafete aplica hasta que se valide el comprobante

                if (!isset($this->data['sgft_gratuito'])) {
                    $this->data['sgft_gratuito'] = 0;
                }

                $empleado = Empleado::find($this->data['sgft_empl_id']);

                if ($empleado->empl_foto == "") {
                    return response()->json($this->ajaxResponse(false, "No se puede solicitar gafete si no se ha subido foto al empleado"));
                }


                if ($this->data['sgft_gratuito'] == 1) {
                    if ($permisos === 'PEATONAL' && $empleado->Local->gafetesGratuitosDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas disponibles."));
                    }
                    if (Str::contains($permisos, 'AUTO') && $empleado->Local->gafetesGratuitosAutoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas de AUTO disponibles."));
                    }
                    if (Str::contains($permisos, 'MOTO') && $empleado->Local->gafetesGratuitosMotoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas de MOTO disponibles."));
                    }

                    if ($this->data['sgft_tipo'] == 'REPOSICIÓN') {
                        return response()->json($this->ajaxResponse(false, "Los gafetes gratuitos no están disponibles para REPOSICIÓN"));
                    }
                } else if ($this->data['sgft_tipo'] != 'REPOSICIÓN') {
                    if (Str::contains($permisos, 'AUTO') && $empleado->Local->gafetesEstacionamientoAutoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de AUTO disponibles."));
                    }
                    if (Str::contains($permisos, 'MOTO') && $empleado->Local->gafetesEstacionamientoMotoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de MOTO disponibles."));
                    }
                }

                $anio_impresion = settings()->get('anio_impresion', date('Y'));
                $fecha_creacion = date('Y') < $anio_impresion ? $anio_impresion . '-01-01' : date('Y-m-d');
                $anio = substr($fecha_creacion, 0, 4);

                //validación EMPLEADO - AÑO - PRIMERA VEZ
                if ($empleado->Local->lcal_cacs_id != 1) {

                    if ($this->data['sgft_tipo'] == 'PRIMERA VEZ') {

                        $record = SolicitudGafete::where('sgft_empl_id', $this->data['sgft_empl_id'])
                            ->whereRaw("YEAR(sgft_fecha) = $anio ")
                            ->where('sgft_tipo', 'PRIMERA VEZ')
                            ->where('sgft_estado', '<>', 'CANCELADA')
                            ->first();

                        if ($record != null) {
                            return response()->json($this->ajaxResponse(false, "Ya se ha capturado una solicitud de PRIMERA VEZ para el empleado seleccionado."));
                        }
                    }
                } else {

                    $record = SolicitudGafete::where('sgft_empl_id', $this->data['sgft_empl_id'])
                        ->whereRaw("YEAR(sgft_fecha) = $anio ")
                        ->whereRaw(" sgft_estado NOT IN ('CANCELADA','IMPRESA','ENTREGADA') ")
                        ->first();

                    if ($record != null) {
                        return response()->json($this->ajaxResponse(false, "Ya se ha capturado una solicitud para el empleado seleccionado."));
                    }
                }


                //FIN validación EMPLEADO - AÑO - PRIMERA VEZ

                $solicitud = new SolicitudGafete();

                $solicitud->sgft_empl_id = $this->data['sgft_empl_id'];
                $solicitud->sgft_lcal_id = $empleado->empl_lcal_id;
                $solicitud->sgft_nombre = $empleado->empl_nombre;
                $solicitud->sgft_cargo = $empleado->Cargo->crgo_descripcion;
                $solicitud->sgft_tipo = $this->data['sgft_tipo'];
                $solicitud->sgft_permisos = $permisos;
                $solicitud->sgft_anio = $this->data['sgft_anio'];
                $solicitud->sgft_gafete_reposicion = $this->data['sgft_gafete_reposicion'];
                $solicitud->sgft_foto = $empleado->empl_foto;
                $solicitud->sgft_thumb = $empleado->empl_thumb;
                $solicitud->sgft_comentario = $this->data['sgft_comentario'];
                $solicitud->sgft_fecha = $fecha_creacion;
                $solicitud->sgft_estado = 'PENDIENTE';
                $solicitud->sgft_gratuito = $this->data['sgft_gratuito'];
                //                $solicitud->sgft_costo = $this->data['sgft_costo'];
                $solicitud->sgft_created_by = auth()->getUser()->id;

                $solicitud->sgft_cpag_id = $this->data['sgft_cpag_id'];

                $solicitud->sgft_costo = $solicitud->sgft_costo_tarifa;
                $saldos = $solicitud->Local->getSaldos();

                if (!$solicitud->sgft_gratuito) {
                    if ($solicitud->sgft_cpag_id) {
                        if ($solicitud->sgft_costo > $saldos['saldo_virtual']) {
                            return response()->json($this->ajaxResponse(false, "Saldo insuficiente, capture mas comprobantes."));
                        }
                    } else {
                        if ($solicitud->sgft_costo > $saldos['saldo_vigente']) {
                            return response()->json($this->ajaxResponse(false, "Saldo insuficiente, capture mas comprobantes."));
                        }
                    }
                }


                //verificamos que se adjunten comprobantes pendientes si existen
                //                if ($solicitud->sgft_gratuito == 0 && $solicitud->sgft_cpag_id < 1 && ComprobantePago::checkComprobantesPendientesPrevalidar($empleado->Local)) {
                //                    return response()->json($this->ajaxResponse(false, "Existe algún comprobante pendiente de validar, favor de adjuntarlo a la solicitud."));
                //                }

                $solicitud->save();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Solicitud <b>CREADA</b> correctamente.'));
        }
    }

    public function reapply(SolicitudGafete $solicitud, Request $request)
    {

        if (!$this->validateAction('reapply')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $permisos = implode(',', $this->data['sgft_permisos']);
            if (Str::contains($permisos, ['AUTO', 'MOTO']) && $this->data['sgft_tipo'] === 'REPOSICIÓN') {
                $validator = Validator::make($this->data, [
                    'sgft_gafete_reposicion' => 'required'
                ], array(), $this->etiquetas);
                if ($validator->fails()) {
                    $this->errors = $validator->messages();
                    return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
                }
            }
            DB::beginTransaction();

            try {

                $empleado = Empleado::find($this->data['sgft_empl_id']);

                if ($empleado->empl_foto == "") {
                    return response()->json($this->ajaxResponse(false, "No se puede solicitar gafete si no se ha subido foto al empleado"));
                }


                if ($this->data['sgft_gratuito'] == 1) {
                    if ($permisos === 'PEATONAL' && $empleado->Local->gafetesGratuitosDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas disponibles."));
                    }
                    if (Str::contains($permisos, 'AUTO') && $empleado->Local->gafetesGratuitosAutoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas de AUTO disponibles."));
                    }
                    if (Str::contains($permisos, 'MOTO') && $empleado->Local->gafetesGratuitosMotoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas de MOTO disponibles."));
                    }

                    if ($this->data['sgft_tipo'] == 'REPOSICIÓN') {
                        return response()->json($this->ajaxResponse(false, "Los gafetes gratuitos no están disponibles para REPOSICIÓN"));
                    }
                } else if ($this->data['sgft_tipo'] != 'REPOSICIÓN') {
                    if (Str::contains($permisos, 'AUTO') && $empleado->Local->gafetesEstacionamientoAutoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de AUTO disponibles."));
                    }
                    if (Str::contains($permisos, 'MOTO') && $empleado->Local->gafetesEstacionamientoMotoDisponibles() < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de MOTO disponibles."));
                    }
                }

                $anio_impresion = settings()->get('anio_impresion', date('Y'));
                $fecha_creacion = date('Y') < $anio_impresion ? $anio_impresion . '-01-01' : date('Y-m-d');
                //$anio = substr($fecha_creacion,0,4);


                $solicitud->sgft_empl_id = $this->data['sgft_empl_id'];
                $solicitud->sgft_lcal_id = $empleado->empl_lcal_id;
                $solicitud->sgft_nombre = $empleado->empl_nombre;
                $solicitud->sgft_cargo = $empleado->Cargo->crgo_descripcion;
                $solicitud->sgft_tipo = $this->data['sgft_tipo'];
                $solicitud->sgft_permisos = $permisos;
                $solicitud->sgft_foto = $empleado->empl_foto;
                $solicitud->sgft_thumb = $empleado->empl_thumb;
                $solicitud->sgft_comentario = $this->data['sgft_comentario'];
                $solicitud->sgft_gratuito = $this->data['sgft_gratuito'];
                $solicitud->sgft_fecha = $fecha_creacion;
                $solicitud->sgft_estado = 'PENDIENTE';
                $solicitud->sgft_created_by = auth()->getUser()->id;
                $solicitud->sgft_comentario_admin = null;

                $solicitud->sgft_cpag_id = $this->data['sgft_cpag_id'];


                $solicitud->sgft_costo = $solicitud->sgft_costo_tarifa;
                $saldos = $solicitud->Local->getSaldos();

                if (!$solicitud->sgft_gratuito) {
                    if ($solicitud->sgft_costo > $saldos['saldo_vigente'] && $solicitud->sgft_gratuito != 1) {
                        return response()->json($this->ajaxResponse(false, "Saldo insuficiente, capture mas comprobantes."));
                    }

                    //verificamos que se adjunten comprobantes pendientes si existen
                    if ($solicitud->sgft_cpag_id < 1 && ComprobantePago::checkComprobantesPendientesPrevalidar($empleado->Local)) {
                        return response()->json($this->ajaxResponse(false, "Existe algún comprobante pendiente de validar, favor de adjuntarlo a la solicitud."));
                    }
                }


                $solicitud->save();


                DB::commit();
                return response()->json($this->ajaxResponse(true, "Solictud <b>ACTUALIZADA</b> correctamente."));
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }
        }
    }

    public function insertExpres(Empleado $empleado, Request $request)
    {

        \DB::beginTransaction();
        try {

            if ($empleado->Local->lcal_cacs_id != 1) {
                return response()->json($this->ajaxResponse(false, "Opción valida únicamente para empleados de Puerta Maya"));
            }

            if ($empleado->empl_foto == "") {
                return response()->json($this->ajaxResponse(false, "No se puede solicitar gafete si no se ha subido foto al empleado"));
            }

            $anio_impresion = settings()->get('anio_impresion', date('Y'));
            $fecha_creacion = date('Y') < $anio_impresion ? $anio_impresion . '-01-01' : date('Y-m-d');

            $solicitud = new SolicitudGafete();

            $solicitud->sgft_empl_id = $empleado->empl_id;
            $solicitud->sgft_lcal_id = $empleado->empl_lcal_id;
            $solicitud->sgft_nombre = $empleado->empl_nombre;
            $solicitud->sgft_cargo = $empleado->Cargo->crgo_descripcion;
            $solicitud->sgft_tipo = 'PRIMERA VEZ';
            $solicitud->sgft_foto = $empleado->empl_foto;
            $solicitud->sgft_thumb = $empleado->empl_thumb;
            $solicitud->sgft_comentario = 'SOLICITUD EXPRÉS GENERADA POR RECEPCIÓN';

            $solicitud->sgft_fecha = $fecha_creacion;
            $solicitud->sgft_estado = 'PENDIENTE';
            $solicitud->sgft_created_by = auth()->getUser()->id;

            $solicitud->sgft_costo = 0;
            $solicitud->sgft_gratuito = 1;

            // $path =  $path = $request->file('sgft_comprobante')->store('comprobantes');
            // $solicitud->sgft_comprobante =  $path;

            //Validación EMPLEADO - AÑO - PRIMERA VEZ
            $anio = substr($fecha_creacion, 0, 4);
            $record = SolicitudGafete::where('sgft_empl_id', $empleado->empl_id)
                ->whereRaw("YEAR(sgft_fecha) = $anio ")
                //                ->where('sgft_tipo','PRIMERA VEZ')
                ->whereRaw(" sgft_estado NOT IN ('CANCELADA','IMPRESA','ENTREGADA') ")
                ->first();

            if ($record != null) {
                return response()->json($this->ajaxResponse(false, "Ya se ha capturado una solicitud de PRIMERA VEZ para el empleado seleccionado."));
            }
            // END VALIDACION EMPLEADO - AÑO - PRIMERA VEZ


            $solicitud->save();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, 'Solicitud <b>CREADA</b> correctamente.'));
    }

    //////////////////////////////////////////////////////////////////////////////////

    public function indexRecepcion(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {

            $records = SolicitudGafete::select([
                'sgft_id',
                'sgft_empl_id',
                'sgft_thumb',
                'sgft_lcal_id',
                'lcal_nombre_comercial',
                'sgft_cpag_id',
                'sgft_nombre',
                'sgft_cargo',
                'sgft_tipo',
                'sgft_fecha',
                'sgft_estado',
                'sgft_comentario',
                'sgft_gratuito'
            ])
                ->join('locales', 'sgft_lcal_id', 'lcal_id')
                ->whereIn('sgft_estado', ['PENDIENTE', 'VALIDADA', 'IMPRESA', 'PENDIENTE DE COBRO']);

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->whereSgftLcalId($filtro);
            }

            return Datatables::of($records)
                ->editColumn('sgft_thumb', function (SolicitudGafete $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->sgft_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('sgft_tipo', function (SolicitudGafete $model) {

                    $color = 'badge-info';
                    if ($model->sgft_tipo == 'REPOSICIÓN') $color = 'badge-primary';
                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgft_tipo . '</small>';

                    if ($model->sgft_gratuito == 1):
                        $html .= '<br/><small class="badge badge-success"> <i class="zmdi zmdi-money-off"></i> GRATUITO</small>';
                    endif;
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('sgft_estado', function (SolicitudGafete $model) {

                    $color = 'badge-primary';
                    if ($model->sgft_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->sgft_estado == 'IMPRESA') $color = 'badge-success';
                    if ($model->sgft_estado == 'ENTREGADA') $color = 'badge-inverse';
                    if ($model->sgft_estado == 'PENDIENTE DE COBRO') $color = 'badge-purple';
                    if ($model->sgft_estado == 'CANCELADA') $color = 'badge-danger';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgft_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('actions', function (SolicitudGafete $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-comprobante-pdf" title="Comprobante" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-collection-pdf"></i></span>';

                    if ($model->sgft_cpag_id > 0 && $model->ComprobantePago->cpag_estado == 'CAPTURADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-prevalidar-comprobante" title="Prevalidar Comprobante" data-id=' . $model->sgft_id . '><i class="fa fa-check-square-o"></i></span>';
                    }

                    if (in_array($model->sgft_estado, ['VALIDADA'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato Gafete" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-accounts-list-alt"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-imprimir" title="Marcar como impreso" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    }

                    if (
                        in_array($model->sgft_estado, ['PENDIENTE'])
                        && (($model->Local->getSaldos()['saldo_vigente'] >= $model->sgft_costo) || in_array(optional($model->ComprobantePago)->cpag_estado, ['PREVALIDADO', 'VALIDADO']))
                    ) {
                        $html .= '<span class="btn btn-primary btn-sm btn-validar" title="Validar Solicitud" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar Solicitud" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-close"></i></span>';
                    }

                    if (in_array($model->sgft_estado, ['IMPRESA'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-pendiente-cobro" title="Marcar como Pendiente de Cobro" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-money-off"></i></span>';
                    }

                    if (in_array($model->sgft_estado, ['PENDIENTE DE COBRO'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-cobrado" title="Marcar como Cobrado" data-id=' . $model->sgft_id . '><i class="zmdi zmdi-money"></i></span>';
                    }
                    $html .= '</div>';

                    return $html;
                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('sgft_comentario', function ($query, $keyword) {
                    $query->whereRaw(" CONCAT(sgft_comentario, IF(sgft_gratuito = 1, ' GRATUITO','')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['sgft_thumb', 'actions', 'sgft_tipo', 'sgft_estado'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => false,
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('solicitud-gafete/recepcion'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'sgft_id', 'name' => 'sgft_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local',])
            ->addColumn(['data' => 'sgft_thumb', 'name' => 'sgft_thumb', 'title' => 'Foto', 'search' => false, 'width' => '5%'])
            ->addColumn(['data' => 'sgft_nombre', 'name' => 'sgft_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'sgft_cargo', 'name' => 'sgft_cargo', 'title' => 'Cargo'])
            ->addColumn(['data' => 'sgft_fecha', 'name' => 'sgft_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'sgft_tipo', 'name' => 'sgft_tipo', 'title' => 'Tipo'])
            ->addColumn(['data' => 'sgft_comentario', 'name' => 'sgft_comentario', 'title' => 'Comentario'])
            ->addColumn(['data' => 'sgft_estado', 'name' => 'sgft_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.solicitud-gafete.index-recepcion', compact('dataTable', 'locales'));
    }

    public function detallesView(SolicitudGafete $solicitud)
    {
        return view('web.solicitud-gafete.detalles', compact('solicitud'));
    }

    /**
     * Vista para cambiar el estado de solicitud a IMPRESA
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function imprimirView(SolicitudGafete $solicitud)
    {
        $url = url('solicitud-gafete/do-imprimir-v3');

        if ($solicitud->Local->Acceso->cacs_id === 1)
            $puertas = Puerta::with('Controladora')
                ->whereHas('Controladora', function ($query) {
                    $query->where('ctrl_usuario', '!=', '')
                        ->where('ctrl_contrasenna', '!=', '');
                })
                ->where('door_modo', 'FISICA')->get();
        else
            $puertas = Puerta::with('Controladora')
                ->whereHas('Controladora', function ($query) {
                    $query->where('ctrl_usuario', '!=', '')
                        ->where('ctrl_contrasenna', '!=', '');
                })
                ->where('door_tipo', 'PEATONAL')
                ->where('door_modo', 'FISICA')->get();

        return view('web.solicitud-gafete.imprimir', compact('solicitud', 'url', 'puertas'));
    }

    /**
     * Genera la vista para prevalidar el COMPROBANTE DE PAGO,
     * al rechazarlo se debe rechazar tambien la solicitud que lo acompaña
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function prevalidarComprobanteView(SolicitudGafete $solicitud)
    {

        $url_aceptar = url('solicitud-gafete/do-aceptar-comprobante');
        $url_rechazar = url('solicitud-gafete/do-rechazar-comprobante');

        $costo_tarifa = $solicitud->sgft_costo_tarifa;

        return view('web.solicitud-gafete.validar-comprobante', compact('solicitud', 'url_aceptar', 'url_rechazar', 'costo_tarifa'));
    }

    /**
     * Genera el PDF del gafete
     * @param SolicitudGafete $solicitud
     */
    public function layoutPDF(SolicitudGafete $solicitud)
    {
        $report = new GafeteBaseReport(null, true, false);
        $report->setSolicitud($solicitud);

        return $report->exec();
    }

    public function contraparteAccesoPdf(SolicitudGafete $gafete)
    {

        $report = new ContraparteGafeteAccesoReport($gafete, true, false);
        $report->setSolicitud($gafete);

        return $report->exec();
    }

    /**
     * Genera un pdf que sirve de comprobante de la solicitud para
     * procesos internos del negocio
     * @param SolicitudGafete $solicitud
     */
    public function comprobantePDF(SolicitudGafete $solicitud)
    {

        $report = new ComprobanteSolicitudReport(null, true, false);
        $report->setSolicitud($solicitud);

        return $report->exec();
    }

    /**
     * Vista para rechazar únicamente la solicitud del gafete
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rechazarView(SolicitudGafete $solicitud)
    {

        $url = url('solicitud-gafete/do-rechazar');

        return view('web.solicitud-gafete.rechazar', compact('solicitud', 'url'));
    }

    /**
     * Vista para rechazar únicamente la solicitud del gafete
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function validarView(SolicitudGafete $solicitud)
    {

        $url = url('solicitud-gafete/do-validar');

        $saldos = $solicitud->Local->getSaldos();

        return view('web.solicitud-gafete.validar', compact('solicitud', 'url', 'saldos'));
    }

    /**
     * Vista para cambiar el estado de solicitud a ENTREGADA
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function entregarView(SolicitudGafete $solicitud)
    {

        $url = url('solicitud-gafete/do-entregar');

        return view('web.solicitud-gafete.entregar', compact('solicitud', 'url'));
    }

    /**
     * Ejecuta la acción de rechazar la solicitud en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doRechazar(Request $request)
    {
        if (!$this->validateAction('do-rechazar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);

                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $solicitud->sgft_estado = 'CANCELADA';
                $solicitud->save();

                $response_message = "Solicitud <b>RECHAZADA</b> correctamente.";
                $response_data = [];

                try {

                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->sgft_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new SolicitudGafeteRechazado($solicitud, $this->data['sgft_comentario_admin']));
                    //--

                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }

                \DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message, $response_data));
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }
        }
    }

    /**
     * Ejecuta la acción de rechazar la solicitud en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doValidar(Request $request)
    {
        if (!$this->validateAction('do-validar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {

                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);

                $saldos = $solicitud->Local->getSaldos();

                if ($solicitud->sgft_costo > $saldos['saldo_vigente']) {
                    return response()->json(
                        $this->ajaxResponse(
                            false,
                            'El saldo vigente del local: $' .
                                number_format($saldos['saldo_vigente'], 2) . ' no es suficiente para cubrir el costo del ' .
                                'gafete: $' . number_format($solicitud->sgft_costo, 2)
                        )
                    );
                }

                //                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $solicitud->sgft_estado = 'VALIDADA';
                $solicitud->save();

                $response_message = 'Evento <b>CREADO</b> correctamente.';
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->cpag_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new ComprobanteRechazado($solicitud, $this->data['sgft_comentario_admin']));
                    //-------------------------------------------------------------------------------------

                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Solicitud <b>VALIDADA</b> correctamente."));
        }
    }

    /**
     * Ejecuta la acción de rechazar el comprobante de pago
     * y la cancelar la solicitud que lo acompaña en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doRechazarComprobante(Request $request)
    {
        if (!$this->validateAction('do-rechazar-comprobante')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);
                $comprobante = $solicitud->ComprobantePago;

                if ($comprobante->cpag_estado != 'CAPTURADO') {
                    return response()->json($this->ajaxResponse(false, 'El comprobante ya se ha validado/rechazado anteriormente.'));
                }


                $comprobante->cpag_estado = 'RECHAZADO';
                $comprobante->save();

                //CANCELAMOS todas las solicitudes relacionadas
                $data_update = [
                    'sgft_comentario_admin' => $this->data['sgft_comentario_admin'],
                    'sgft_estado' => 'CANCELADA',
                    'sgft_cpag_id' => null,
                ];

                SolicitudGafete::whereSgftCpagId($comprobante->cpag_id)
                    ->whereSgftEstado('PENDIENTE')
                    ->update($data_update);

                //                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                //                $solicitud->sgft_estado = 'CANCELADA';
                //                $solicitud->sgft_cpag_id = null;
                //                $solicitud->save();

                $response_message = 'Evento <b>CREADO</b> correctamente.';
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($comprobante->cpag_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new ComprobanteRechazado($comprobante, $this->data['sgft_comentario_admin']));
                    //-------------------------------------------------------------------------------------

                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }

                \DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message, $response_data));
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }
        }
    }

    /**
     * Ejecuta la acción de cambiar el estado de la solicitud
     * a VALIDADA lo cual ya afecta el saldo del local
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doAceptarComprobante(Request $request)
    {
        if (!$this->validateAction('do-aceptar-comprobante')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);
                $comprobante = ComprobantePago::findOrFail($solicitud->sgft_cpag_id);

                if ($comprobante == null) {
                    return response()->json($this->ajaxResponse(false, "No se encontró comprobante vinculado."));
                }


                $comprobante->cpag_estado = 'PREVALIDADO';
                $comprobante->save();

                //si el comprobante requiere factura cambiar el estado de la factura pendiente asociada
                if ($comprobante->cpag_requiere_factura == 1) {

                    $factura = Factura::whereFactCpagId($comprobante->cpag_id)
                        ->whereFactEstado('PENDIENTE')
                        ->first();

                    if ($factura != null) {
                        $factura->fact_estado = 'PRECAPTURADA';
                        $factura->save();
                    } else {
                        return response()->json($this->ajaxResponse(false, "No se encontró registro de factura vinculado."));
                    }
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Comprobante <b>prevalidado</b> correctamente."));
        }
    }

    /**
     * Ejecuta la acción de cambiar estado de la solicitud a IMPRESA
     * en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doImprimir(Request $request)
    {
        $input = $request->input();
        if (!$this->validateAction('do-imprimir')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $con_tarjeta = SolicitudGafete::where('sgft_numero', $this->data['sgft_numero'])->get();
            if ($con_tarjeta->count() > 0) {
                return response()->json($this->ajaxResponse(false, 'Ya existe una Solicitud de Gafete con ese Número de Tarjeta!'));
            }

            DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);

                if ($solicitud->sgft_estado != 'VALIDADA') {
                    return response()->json($this->ajaxResponse(false, 'PARA MARCAR UNA SOLICITUD COMO <b>IMPRESA</b>, SU ESTADO DEBE SER <b>VALIDADA</b>'));
                }

                //                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $solicitud->sgft_estado = 'IMPRESA';
                $solicitud->sgft_numero = $this->data['sgft_numero'];
                $solicitud->save();

                //TODO desactivamos tarjetas anteriores
                if ($solicitud->Empleado->GafeteAcceso()) {
                    $gafete = VGafetesRfidV2::whereRefId($solicitud->Empleado->GafeteAcceso()->id)->whereTipo('PEATONAL')->first();
                    if ($gafete) {
                        foreach ($gafete->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                            foreach ($doors as $door) {
                                $activar = new DesactivarTarjetaV2($gafete, $door->Controladora, $door->pin_value);
                                $res = $activar->execute();

                                sleep(1);

                                if ($res == false) {
                                    DB::rollBack();
                                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al desactivar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
                                }
                            }
                        }
                    }
                }
                //TODO Fin de desactivacion

                $puertas = [];
                foreach ($input as $key => $data) {
                    if (stripos($key, 'puerta_') === 0)
                        $puertas[] = explode('_', $key)[1];
                }

                $solicitud->Puertas()->sync($puertas);
                $gafeteRfid = $solicitud->getVGafeteRfidV2();
                foreach ($solicitud->Puertas->groupBy('door_controladora_id') as $key => $doors) {

                    $controller = Controladora::findOrFail($key);
                    // creamos tarjeta v2
                    $activar = new CrearTarjetaV2($gafeteRfid, $controller);
                    $res = $activar->execute();

                    sleep(1);

                    if ($res == false) {
                        DB::rollBack();
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                    }

                    foreach ($doors as $door) {
                        $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $door->pin_value);
                        $res = $activar->execute();

                        if ($res == false) {
                            DB::rollBack();
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                        }
                    }
                }

                $response_message = "Solicitud <b>MARCADA COMO IMPRESA</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->sgft_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new SolicitudGafeteImpreso($solicitud));
                    //-------------------------------------------------------------------------------------
                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }

                \DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message, $response_data));
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage() . $e->getFile() . $e->getLine()));
            }
        }
    }

    /**
     * Ejecuta la acción de cambiar estado de la solicitud a IMPRESA
     * en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doImprimirV3(Request $request)
    {
        $input = $request->input();
        if (!$this->validateAction('do-imprimir')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $con_tarjeta = SolicitudGafete::where('sgft_numero', $this->data['sgft_numero'])->where('sgft_id', '!=', $this->data['sgft_id'])->get();
            if ($con_tarjeta->count() > 0) {
                return response()->json($this->ajaxResponse(false, 'Ya existe una Solicitud de Gafete con ese Número de Tarjeta!'));
            }

            $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);

            if ($solicitud->sgft_estado != 'VALIDADA') {
                return response()->json($this->ajaxResponse(false, 'PARA MARCAR UNA SOLICITUD COMO <b>IMPRESA</b>, SU ESTADO DEBE SER <b>VALIDADA</b>'));
            }
            $solicitud->sgft_numero = $this->data['sgft_numero'];
            $solicitud->save();
            $puertas = [];
            foreach ($input as $key => $data) {
                if (stripos($key, 'puerta_') === 0)
                    $puertas[] = explode('_', $key)[1];
            }
            $solicitud->Puertas()->sync($puertas);

            DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);
                $solicitud->sgft_estado = 'IMPRESA';
                $solicitud->save();

                //TODO eliminar tarjetas anteriores
                if ($solicitud->Empleado->GafeteAcceso()) {
                    $gafete = VGafetesRfidV3::whereRefId($solicitud->Empleado->GafeteAcceso()->id)->first();
                    if ($gafete && $gafete->controladora_id) {
                        $controladora = Controladora::find($gafete->controladora_id);
                        $eliminar = new EliminarTarjetaV3($gafete);
                        $res = $eliminar->execute();
                        if ($res == false) {
                            DB::rollBack();
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al eliminar la tarjeta en la controladora " . $controladora->ctrl_nombre, $res));
                        }
                    }
                }
                //TODO Fin de eliminacion

                $gafeteRfid = $solicitud->getVGafeteRfidV3();
                $controladora = Controladora::find($gafeteRfid->controladora_id);

                $crear = new CrearTarjetaV3($gafeteRfid);
                $res = $crear->execute();
                if ($res == false) {
                    DB::rollBack();
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controladora->ctrl_nombre.", $res));
                }

                $activar = new ActivarTarjetaV3($gafeteRfid);
                $res = $activar->execute();
                if ($res == false) {
                    DB::rollBack();
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controladora->ctrl_nombre.", $res));
                }

                $response_message = "Solicitud <b>MARCADA COMO IMPRESA</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->sgft_lcal_id)
                        ->get();

                    Notification::send($locatarios, new SolicitudGafeteImpreso($solicitud));
                    //-------------------------------------------------------------------------------------
                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }

                DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message, $response_data));
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage() . $e->getFile() . $e->getLine()));
            }
        }
    }

    /**
     * Ejecuta la acción de cambiar el estado de la solicitud
     * a ENTREGADA en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doEntregar(Request $request)
    {
        if (!$this->validateAction('do-entregar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $solicitud = SolicitudGafete::findOrFail($this->data['sgft_id']);

                if ($solicitud->sgft_estado != 'IMPRESA') {
                    return response()->json($this->ajaxResponse(false, 'PARA MARCAR UNA SOLICITUD COMO <b>ENTREGADA</b>, SU ESTADO DEBE SER <b>IMPRESA</b>'));
                }

                // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $solicitud->sgft_estado = 'ENTREGADA';

                $solicitud->save();
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Solicitud <b>FINALIZADA</b> correctamente."));
        }
    }

    public function doMarcarPendienteCobro(SolicitudGafete $solicitud)
    {
        \DB::beginTransaction();

        try {
            // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
            $solicitud->sgft_estado = 'PENDIENTE DE COBRO';

            $solicitud->save();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Solicitud marcada como <b>PENDIENTE DE COBRO</b> correctamente."));
    }

    public function doMarcarCobrada(SolicitudGafete $solicitud)
    {
        \DB::beginTransaction();

        try {
            // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
            $solicitud->sgft_estado = 'COBRADA';

            $solicitud->save();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Solicitud marcada como <b>COBRADA</b> correctamente."));
    }

    //////////////////////////////////////////////////////////////////////////

    public function getJSON(Empleado $empleado)
    {
        return response()->json($empleado);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = Empleado::select(\DB::raw('empl_id as id, empl_nombre as text'))
            ->where('empl_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }

    ///////////////////////////////////////////////////////////////////////////

}
