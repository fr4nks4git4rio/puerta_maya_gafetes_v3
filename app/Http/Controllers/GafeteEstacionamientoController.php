<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjeta;
use App\Actions\ActivarTarjetaV2;
use App\Actions\CrearTarjetaV2;
use App\Actions\DesactivarTarjeta;
use App\Actions\DesactivarTarjetaV2;
use App\ComprobantePago;
use App\Controladora;
use App\Factura;
use App\Local;
use App\Notifications\ComprobanteRechazado;
use App\Notifications\GafeteEstacionamientoImpreso;
use App\Notifications\GafeteEstacionamientoRechazado;
use App\Puerta;
use App\Reports\ComprobanteSolicitudEstacionamientoReport;
use App\Reports\ComprobanteSolicitudReport;
use App\Reports\ContraparteGafeteEstacionamientoReport;
use App\Reports\GafeteEstacionamientoReport;
use App\Reports\GafetePermisoTemporalReport;
use App\SolicitudGafete;
use App\User;
use App\VGafetesRfidV2;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\GafeteEstacionamiento;
use Illuminate\Support\Facades\DB;

class GafeteEstacionamientoController extends Controller
{

    protected $rules = [
        'insert' => [
            'gest_lcal_id' => 'required',
            'gest_tipo' => 'required',
            'gest_tipo_solicitud' => 'required',
            'gest_anio' => 'required',
            'gest_comentario' => 'nullable',
            'gest_cpag_id' => 'nullable',
            'gest_gratuito' => '',
            'gest_gafete_reposicion' => 'required_if:gest_tipo_solicitud,REPOSICIÓN',
        ],

        'edit' => [
            'gest_id' => 'required|exists:gafetes_estacionamiento,gest_id',
            'gest_lcal_id' => 'required',
            'gest_numero_nfc' => 'required|digits_between:5,10',
            'gest_tipo' => 'required',
            'gest_anio' => 'required',
            'gest_comentario' => 'nullable',
            'gest_gafete_reposicion' => 'required_if:gest_tipo_solicitud,REPOSICIÓN',
        ],

        'do-rechazar' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
            'gest_comentario_admin' => 'required',
        ],

        'do-validar' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
        ],

        'do-rechazar-comprobante' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
            'gest_comentario_admin' => 'required',
        ],


        'do-aceptar-comprobante' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
        ],

        'do-imprimir' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
            'gest_numero_nfc' => 'required|digits_between:5,10',
            // 'sgft_comentario_admin'     => 'required',
        ],

        'do-entregar' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
            // 'sgft_comentario_admin'     => 'required',
        ],

        'reapply' => [
            'gest_id' => 'required:exists:gafetes_estacionamiento,gest_id',
            'gest_tipo' => 'required',
            'gest_tipo_solicitud' => 'required',
            'gest_anio' => 'required',
            'gest_comentario' => 'nullable',
            'gest_cpag_id' => 'nullable',
            'gest_gratuito' => '',
        ],


    ];

    protected $etiquetas = [
        'gest_id' => 'Gafete',
        'gest_lcal_id' => 'Local',
        'gest_numero' => 'Número Tarjeta',
        'gest_tipo' => 'Clase',
        'gest_gratuito' => 'Gefete Gratuito',
        'gest_tipo_solicitud' => 'Tipo',
        'gest_anio' => 'Año',
        'gest_comentario' => 'Comentario',
        'gest_gafete_reposicion' => 'Gafete a reponer',
    ];


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

    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {

            $records = GafeteEstacionamiento::select(['gest_id', 'gest_anio', 'gest_lcal_id', 'gest_numero', 'gest_numero_nfc',
                'lcal_nombre_comercial',
                'gest_comentario', 'gest_tipo'])
                ->join('locales', 'gest_lcal_id', 'lcal_id');


            return Datatables::of($records)
                ->addColumn('actions', function (GafeteEstacionamiento $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-pdf"
                                    title="Imprimir" data-id=' . $model->gest_id . '><i class="zmdi zmdi-print"></i></span>';

                    $html .= '</div>';

                    return $html;

                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" locales.lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        //Definición del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

//        $htmlBuilder->ajax( [
//            'url'=> url('gafete-preimpreso/permisos-temporales') ,
////            'data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
//        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'gest_id', 'name' => 'gest_id', 'title' => 'Id', 'visible' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local', 'search' => true])
            ->addColumn(['data' => 'gest_anio', 'name' => 'gest_anio', 'title' => 'Año', 'search' => true])
            ->addColumn(['data' => 'gest_tipo', 'name' => 'gest_tipo', 'title' => 'Tipo', 'search' => true])
            ->addColumn(['data' => 'gest_numero', 'name' => 'gest_numero', 'title' => 'Consecutivo', 'search' => true])
            ->addColumn(['data' => 'gest_numero_nfc', 'name' => 'gest_numero_nfc', 'title' => 'Número Tarjeta', 'search' => true])
            ->addColumn(['data' => 'gest_comentario', 'name' => 'gest_comentario', 'title' => 'Comentario'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);


        return view('web.gafete-estacionamiento.index', compact('dataTable'));

    }


    /**
     * para el LOCATARIO
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \Exception
     */
    public function indexLocatario(Request $request, Builder $htmlBuilder)
    {

        //figure out the local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;

        if ($request->ajax()) {

            $records = GafeteEstacionamiento::select(['gest_id', 'gest_anio', 'gest_lcal_id',
                'gest_tipo_solicitud', 'gest_estado', 'gest_gratuito',
                'gest_numero', 'gest_numero_nfc',
                'lcal_nombre_comercial',
                'gest_comentario', 'gest_tipo'])
                ->join('locales', 'gest_lcal_id', 'lcal_id')
                ->whereGestLcalId($local->lcal_id)
                ->whereRaw("( gest_estado in ('PENDIENTE', 'IMPRESA') or (gest_estado = 'CANCELADA' AND date(gest_updated_at) >= curdate() - INTERVAL 7 DAY ) ) ");


            return Datatables::of($records)
                ->editColumn('get_comentario', function (GafeteEstacionamiento $model) {
                    $html = '<small>';

                    $html .= $model->gest_comentario;

                    if ($model->gest_comentario_admin != "")
                        $html .= '<br/><b class="text-info">' . $model->gest_comentario_admin . '</b>';

                    $html .= '</small>';

                    return $html;
                })
                ->editColumn('gest_tipo_solicitud', function (GafeteEstacionamiento $model) {

                    $color = 'badge-info';
                    if ($model->gest_tipo_solicitud == 'REPOSICIÓN') $color = 'badge-primary';
                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->gest_tipo_solicitud . '</small>';

                    if ($model->gest_gratuito == 1):
                        $html .= '<br/><small class="badge badge-success"> <i class="zmdi zmdi-money-off"></i> GRATUITO</small>';
                    endif;
                    $html .= '</div>';
                    return $html;

                })
                ->editColumn('gest_estado', function (GafeteEstacionamiento $model) {

                    $color = 'badge-primary';
                    if ($model->gest_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->gest_estado == 'IMPRESA') $color = 'badge-success';
                    if ($model->gest_estado == 'ENTREGADA') $color = 'badge-inverse';
                    if ($model->gest_estado == 'CANCELADA') $color = 'badge-danger';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->gest_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
                ->addColumn('actions', function (GafeteEstacionamiento $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->gest_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-comprobante-pdf" title="Comprobante" data-id=' . $model->gest_id . '><i class="zmdi zmdi-collection-pdf"></i></span>';

                    $html .= '</div>';

                    return $html;

                })
//                ->filterColumn('lcal_nombre_comercial', function($query, $keyword) {
//                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
//                })
                ->rawColumns(['actions', 'gest_comentario', 'gest_tipo_solicitud', 'gest_estado'])
                ->make(true);
        }

        //Definición del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('gafete-estacionamiento/locatario'),
//            'data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

//        $htmlBuilder->ajax( [
//            'url'=> url('gafete-preimpreso/permisos-temporales') ,
////            'data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
//        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'gest_id', 'name' => 'gest_id', 'title' => 'Id', 'visible' => false])
//            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local', 'search'=>true])
            ->addColumn(['data' => 'gest_anio', 'name' => 'gest_anio', 'title' => 'Año', 'search' => true])
            ->addColumn(['data' => 'gest_tipo', 'name' => 'gest_tipo', 'title' => 'Clase', 'search' => true])
            ->addColumn(['data' => 'gest_tipo_solicitud', 'name' => 'gest_tipo_solicitud', 'title' => 'Tipo', 'search' => true])
//            ->addColumn(['data' => 'gest_numero', 'name' => 'gest_numero', 'title' => 'Consecutivo', 'search'=>true])
//            ->addColumn(['data' => 'gest_numero_nfc', 'name' => 'gest_numero_nfc', 'title' => 'Número Tarjeta', 'search'=>true])
            ->addColumn(['data' => 'gest_comentario', 'name' => 'gest_comentario', 'title' => 'Comentario'])
            ->addColumn(['data' => 'gest_estado', 'name' => 'gest_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);


        return view('web.gafete-estacionamiento.index-locatario', compact('dataTable', 'local'));

    }

    public function form(GafeteEstacionamiento $gafete = null, Request $request)
    {

        $url = ($gafete == null) ? url('gafete-estacionamiento/insert') : url('gafete-estacionamiento/edit', $gafete->getKey());

        //determinamos el local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;

        $saldos = $local->getSaldos();

        $gafetesGratisAuto = $local->gafetesGratuitosAutoDisponibles();
        $gafetesGratisMoto = $local->gafetesGratuitosMotoDisponibles();

        $comprobantes = ['' => 'Recuperando comprobantes capturados...'];
        $gafetes_activos = ['' => 'Recuperando gafetes activos...'];
        $gafetes_activos = GafeteEstacionamiento::getGafetesActivos($local);
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

        return view('web.gafete-estacionamiento.form', compact('gafete', 'url', 'local',
            'gafetesGratisAuto', 'gafetesGratisMoto', 'saldos', 'comprobantes', 'gafetes_activos', 'gafetes_activos_autos',
            'gafetes_activos_motos'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {

                $local = Local::findOrFail($this->data['gest_lcal_id']);
                $tipo = $this->data['gest_tipo'];
                $tipo_solicitud = $this->data['gest_tipo_solicitud'];

                if (!isset($this->data['gest_gratuito'])) {
                    $this->data['gest_gratuito'] = 0;
                } else {
                    $this->data['gest_gratuito'] = 1;
                }

                //validamos que queden espacios fisicos dispoibles
                if ($this->data['gest_gratuito'] == 1) {
                    $gratuitosDisponibles = $this->data['gest_tipo'] == 'AUTO' ?
                        $local->gafetesGratuitosAutoDisponibles() :
                        $local->gafetesGratuitosMotoDisponibles();

                    if ($gratuitosDisponibles < 1) {
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes gratuitas disponibles."));
                    }

                    if ($this->data['gest_tipo_solicitud'] == 'REPOSICIÓN') {
                        return response()->json($this->ajaxResponse(false, "Los gafetes gratuitos no están disponibles para REPOSICIÓN"));
                    }
                } else {
                    $disponibles = $this->data['gest_tipo'] == 'AUTO' ?
                        $local->gafetesEstacionamientoAutoDisponibles() :
                        $local->gafetesEstacionamientoMotoDisponibles();

                    if ($tipo_solicitud == 'REPOSICIÓN') $disponibles = 1;

                    if ($disponibles < 1) {
                        return response()->json($this->ajaxResponse(false, "No quedan espacios de estacionamiento disponibles."));
                    }
                }

                $this->data['gest_estado'] = 'PENDIENTE';

                $gafete = new GafeteEstacionamiento();
                $gafete->fill($this->data);

                $gafete->gest_costo = $gafete->gest_costo_tarifa;
                $saldos = $local->getSaldos();

                if($this->data['gest_gratuito'] != 1){
                    if ($gafete->gest_cpag_id) {
                        if ($gafete->gest_costo > $saldos['saldo_virtual']) {
                            return response()->json($this->ajaxResponse(false, "Saldo insuficiente, capture mas comprobantes."));
                        }
                    } else {
                        if ($gafete->gest_costo > $saldos['saldo_vigente']) {
                            return response()->json($this->ajaxResponse(false, "Saldo insuficiente, capture mas comprobantes."));
                        }
                    }
                }

                //si es reexpedición debemos deshabilitar el gafete anterior
                if ($tipo_solicitud == 'REPOSICIÓN') {


                    $gafete->gest_gafete_reposicion = $this->data['gest_gafete_reposicion'];
                    $GafeteAnterior = GafeteEstacionamiento::findOrFail($this->data['gest_gafete_reposicion']);

                    if ($gafete->gest_tipo != $GafeteAnterior->gest_tipo) {
                        return response()->json($this->ajaxResponse(false, 'El tipo de gafete no coincide con el gafete a reponer.'));
                    }

                    $gafete->gest_numero = $GafeteAnterior->gest_numero;

                    $GafeteAnterior->gest_estado = 'CANCELADA';
                    $GafeteAnterior->gest_comentario_admin = 'Reposición de Gafete';
                    $GafeteAnterior->save();

//                    $gafeteRfid = $GafeteAnterior->getVGafeteRfidV2();
//
//                    foreach ($gafeteRfid->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
//                        $controller = Controladora::findOrFail($key);
//                        foreach ($doors as $door) {
//                            $desactivar = new DesactivarTarjetaV2($gafeteRfid, $controller, $door->pin_value);
//                            $res = $desactivar->execute();
//
//                            if ($res == false) {
//                                \DB::rollBack();
//                                return response()->json($this->ajaxResponse(false, 'No se pudo inhabilitar la tarjeta con Pin: ' . $gafeteRfid->referencia, $res));
//                            }
//                        }
//                    }
//                    $gafeteRfid->getOriginalRecord()->gest_disabled_at = now();
//                    $gafeteRfid->getOriginalRecord()->save();
                }

                $gafete->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Gafete <b>CREADO</b> correctamente.'));

        }
    }

    public function detallesView(GafeteEstacionamiento $gafete)
    {
        return view('web.gafete-estacionamiento.detalles', compact('gafete'));
    }


    //////////////////////////////////////////////////////////////////////////////////

    /**
     * para RECEPCIÓN
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \Exception
     */
    public function indexRecepcion(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = GafeteEstacionamiento::select(['gest_id', 'gest_anio', 'gest_lcal_id',
                'gest_tipo_solicitud', 'gest_estado', 'gest_gratuito', 'gest_cpag_id',
                'gest_numero', 'gest_numero_nfc',
                'lcal_nombre_comercial',
                'gest_comentario', 'gest_tipo'])
                ->join('locales', 'gest_lcal_id', 'lcal_id')
                ->whereIn('gest_estado', ['PENDIENTE', 'VALIDADA', 'IMPRESA', 'PENDIENTE DE COBRO']);

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->whereGestLcalId($filtro);
            }


            return Datatables::of($records)
                ->editColumn('get_comentario', function (GafeteEstacionamiento $model) {
                    $html = '<small>';

                    $html .= $model->gest_comentario;

                    if ($model->gest_comentario_admin != "")
                        $html .= '<br/><b class="text-info">' . $model->gest_comentario_admin . '</b>';

                    $html .= '</small>';

                    return $html;
                })
                ->editColumn('gest_tipo_solicitud', function (GafeteEstacionamiento $model) {

                    $color = 'badge-info';
                    if ($model->gest_tipo_solicitud == 'REPOSICIÓN') $color = 'badge-primary';
                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->gest_tipo_solicitud . '</small>';

                    if ($model->gest_gratuito == 1):
                        $html .= '<br/><small class="badge badge-success"> <i class="zmdi zmdi-money-off"></i> GRATUITO</small>';
                    endif;
                    $html .= '</div>';
                    return $html;

                })
                ->editColumn('gest_estado', function (GafeteEstacionamiento $model) {

                    $color = 'badge-primary';
                    if ($model->gest_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->gest_estado == 'IMPRESA') $color = 'badge-success';
                    if ($model->gest_estado == 'ENTREGADA') $color = 'badge-inverse';
                    if ($model->gest_estado == 'PENDIENTE DE COBRO') $color = 'badge-purple';
                    if ($model->gest_estado == 'CANCELADA') $color = 'badge-danger';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->gest_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
                ->addColumn('actions', function (GafeteEstacionamiento $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->gest_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-comprobante-pdf" title="Comprobante" data-id=' . $model->gest_id . '><i class="zmdi zmdi-collection-pdf"></i></span>';

                    if ($model->gest_cpag_id > 0 && $model->ComprobantePago->cpag_estado == 'CAPTURADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-prevalidar-comprobante" title="Prevalidar Comprobante" data-id=' . $model->gest_id . '><i class="fa fa-check-square-o"></i></span>';
                    }

                    if (in_array($model->gest_estado, ['VALIDADA'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato Gafete" data-id=' . $model->gest_id . '><i class="zmdi zmdi-accounts-list-alt"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-imprimir" title="Marcar como impreso" data-id=' . $model->gest_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    }

                    if (in_array($model->gest_estado, ['PENDIENTE'])
                        && (($model->gest_cpag_id == "") || in_array($model->ComprobantePago->cpag_estado, ['PREVALIDADO', 'VALIDADO', 'RECHAZADO']))
                    ) {
                        $html .= '<span class="btn btn-primary btn-sm btn-validar" title="Validar Solicitud" data-id=' . $model->gest_id . '><i class="zmdi zmdi-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar Solicitud" data-id=' . $model->gest_id . '><i class="zmdi zmdi-close"></i></span>';
                    }

                    if (in_array($model->gest_estado, ['IMPRESA'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-pendiente-cobro" title="Marcar como Pendiente de Cobro" data-id=' . $model->gest_id . '><i class="zmdi zmdi-money-off"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->gest_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    }

                    if (in_array($model->gest_estado, ['PENDIENTE DE COBRO'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-cobrado" title="Marcar como Cobrado" data-id=' . $model->gest_id . '><i class="zmdi zmdi-money"></i></span>';
                    }

                    $html .= '</div>';

                    return $html;

                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" locales.lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['actions', 'gest_comentario', 'gest_tipo_solicitud', 'gest_estado'])
                ->make(true);
        }

        //Definición del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('gafete-estacionamiento/recepcion'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

//        $htmlBuilder->ajax( [
//            'url'=> url('gafete-preimpreso/permisos-temporales') ,
////            'data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
//        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'gest_id', 'name' => 'gest_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local', 'search' => false])
            ->addColumn(['data' => 'gest_anio', 'name' => 'gest_anio', 'title' => 'Año', 'search' => true])
            ->addColumn(['data' => 'gest_tipo', 'name' => 'gest_tipo', 'title' => 'Clase', 'search' => true])
            ->addColumn(['data' => 'gest_tipo_solicitud', 'name' => 'gest_tipo_solicitud', 'title' => 'Tipo', 'search' => true])
//            ->addColumn(['data' => 'gest_numero', 'name' => 'gest_numero', 'title' => 'Consecutivo', 'search'=>true])
//            ->addColumn(['data' => 'gest_numero_nfc', 'name' => 'gest_numero_nfc', 'title' => 'Número Tarjeta', 'search'=>true])
            ->addColumn(['data' => 'gest_comentario', 'name' => 'gest_comentario', 'title' => 'Comentario'])
            ->addColumn(['data' => 'gest_estado', 'name' => 'gest_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $estadistica = [
            'espacios_totales_auto' => Local::all()->sum('lcal_espacios_autos'),
            'espacios_totales_moto' => Local::all()->sum('lcal_espacios_motos'),
            'gafetes_asignados_auto' => GafeteEstacionamiento::where('gest_estado', '<>', 'CANCELADA')
                ->whereRaw(" gest_anio = " . date('Y'))
                ->whereGestTipo('AUTO')
                ->whereNull('gest_disabled_at')
                ->count(),
            'gafetes_asignados_moto' => GafeteEstacionamiento::where('gest_estado', '<>', 'CANCELADA')
                ->whereRaw(" gest_anio = " . date('Y'))
                ->whereGestTipo('MOTO')
                ->whereNull('gest_disabled_at')
                ->count(),
        ];

        return view('web.gafete-estacionamiento.index-recepcion', compact('dataTable', 'locales', 'estadistica'));

    }

    /**
     * Vista para cambiar el estado de solicitud a IMPRESA
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function imprimirView(GafeteEstacionamiento $gafete)
    {

        $url = url('gafete-estacionamiento/do-imprimir');

        $puertas = Puerta::where('door_tipo', $gafete->gest_tipo)->with('Controladora')->get();

        return view('web.gafete-estacionamiento.imprimir', compact('gafete', 'url', 'puertas'));
    }

    /**
     * Genera la vista para prevalidar el COMPROBANTE DE PAGO,
     * al rechazarlo se debe rechazar tambien la solicitud que lo acompaña
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function prevalidarComprobanteView(GafeteEstacionamiento $gafete)
    {

        $url_aceptar = url('gafete-estacionamiento/do-aceptar-comprobante');
        $url_rechazar = url('gafete-estacionamiento/do-rechazar-comprobante');

        $costo_tarifa = $gafete->sgft_costo_tarifa;

        return view('web.gafete-estacionamiento.validar-comprobante', compact('gafete', 'url_aceptar', 'url_rechazar', 'costo_tarifa'));
    }

    /**
     * Vista para cambiar el estado de solicitud a ENTREGADA
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function entregarView(GafeteEstacionamiento $gafete)
    {

        $url = url('gafete-estacionamiento/do-entregar');

        return view('web.gafete-estacionamiento.entregar', compact('gafete', 'url'));
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
                $gafete = GafeteEstacionamiento::findOrFail($this->data['gest_id']);
                $comprobante = $gafete->ComprobantePago;

                if ($comprobante->cpag_estado != 'CAPTURADO') {
                    return response()->json($this->ajaxResponse(false, 'El comprobante ya se ha validado/rechazado anteriormente.'));
                }

                $comprobante->cpag_estado = 'RECHAZADO';
                $comprobante->save();

                $gafete->gest_comentario_admin = $this->data['gest_comentario_admin'];
                $gafete->gest_estado = 'CANCELADA';
                $gafete->gest_cpag_id = null;
                $gafete->save();

                $response_message = '"Solicitud y Comprobante <b>RECHAZADOS</b> correctamente."';
                $response_data = [];

                try {

                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($comprobante->cpag_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new ComprobanteRechazado($comprobante, $this->data['gest_comentario_admin']));
                    //-------------------------------------------------------------------------------------

                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }


                \DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message));

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }


        }


    }


    /**
     * Vista para rechazar únicamente la solicitud del gafete
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rechazarView(GafeteEstacionamiento $gafete)
    {

        $url = url('gafete-estacionamiento/do-rechazar');

        return view('web.gafete-estacionamiento.rechazar', compact('gafete', 'url'));
    }

    /**
     * Vista para rechazar únicamente la solicitud del gafete
     * @param SolicitudGafete $solicitud
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function validarView(GafeteEstacionamiento $gafete)
    {

        $url = url('gafete-estacionamiento/do-validar');

        $saldos = $gafete->Local->getSaldos();

        return view('web.gafete-estacionamiento.validar', compact('gafete', 'url', 'saldos'));
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
                $gafete = GafeteEstacionamiento::findOrFail($this->data['gest_id']);
                $comprobante = ComprobantePago::findOrFail($gafete->gest_cpag_id);

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
                $solicitud = GafeteEstacionamiento::findOrFail($this->data['gest_id']);

                $solicitud->gest_comentario_admin = $this->data['gest_comentario_admin'];
                $solicitud->gest_estado = 'CANCELADA';
                $solicitud->save();

                $response_message = "Solicitud <b>RECHAZADA</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->gest_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new GafeteEstacionamientoRechazado($solicitud, $this->data['gest_comentario_admin']));
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


                $gafete = GafeteEstacionamiento::findOrFail($this->data['gest_id']);

                $saldos = $gafete->Local->getSaldos();

                if ($gafete->gest_costo > $saldos['saldo_vigente']) {
                    return response()->json($this->ajaxResponse(false, 'El saldo vigente del local: $' .
                        number_format($saldos['saldo_vigente'], 2) . ' no es sufiente para cubrir el costo del ' .
                        'gafete: $' . number_format($gafete->gest_costo, 2)
                    )
                    );
                }

//                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
//                if($gafete->gest_tipo == 'AUTO'){
//                    $existentes = $gafete->Local->gafetesEstacionamientoAutoDisponibles();
//                }else{
//                    $existentes = $gafete->Local->gafetesEstacionamientoMotoDisponibles();
//                }

                if ($gafete->gest_gafete_reposicion) {
                    $numero = $gafete->gest_numero;
                } else {
                    $numero = $gafete->Local->numeroGafeteEstacionamiento($gafete->gest_tipo);

                    if ($numero == null) {
                        response()->json($this->ajaxResponse(false, 'Todos los Gafetes de Estacionamiento de ' . $gafete->gest_tipo . ' están ocupados!'));
                    }
                }
                $gafete->gest_numero = $numero;
                $gafete->gest_estado = 'VALIDADA';
                $gafete->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Solicitud <b>VALIDADA</b> correctamente."));
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

            \DB::beginTransaction();

            try {
                $gafete = GafeteEstacionamiento::findOrFail($this->data['gest_id']);

                if ($gafete->gest_estado != 'VALIDADA') {
                    return response()->json($this->ajaxResponse(false, 'PARA MARCAR UNA SOLICITUD COMO <b>IMPRESA</b>, SU ESTADO DEBE SER <b>VALIDADA</b>'));
                }

                // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $gafete->gest_estado = 'IMPRESA';
                $gafete->gest_numero_nfc = $this->data['gest_numero_nfc'];
                $gafete->save();

                //TODO desactivamos tarjetas anteriores
                if($gafete->gest_gafete_reposicion){
                    $gafeteRfid = VGafetesRfidV2::whereRefId($gafete->gest_gafete_reposicion)->whereTipo($gafete->gest_tipo)->first();
                    foreach ($gafeteRfid->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                        foreach ($doors as $door) {
                            $activar = new DesactivarTarjetaV2($gafeteRfid, $door->Controladora, $door->pin_value);
                            $res = $activar->execute();

                            sleep(1);

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al desactivar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
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
                $gafete->Puertas()->sync($puertas);

                //obtenemos la controladora correspondiente
//                $controller = Controladora::controladoraAccesoAutosMotos();

                // creamos tarjeta v2
                $gafeteRfid = $gafete->getVGafeteRfidV2();
                foreach ($gafete->Puertas->groupBy('door_controladora_id') as $key => $doors) {

                    $controller = Controladora::findOrFail($key);
                    // creamos tarjeta v2
                    $activar = new CrearTarjetaV2($gafeteRfid, $controller);
                    $res = $activar->execute();

                    if ($res == false) {
                        \DB::rollBack();
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                    }

                    // activamos tarjeta v2

                    foreach ($doors as $door) {
                        $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $door->pin_value);
                        $res = $activar->execute();

                        if ($res == false) {
                            \DB::rollBack();
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                        }
                    }
                }


                /*
                //activamos la tarjeta
                $gafeteRfid = $gafete->getVGafeteRfid();

                $activar = new ActivarTarjeta($gafeteRfid);
                $res = $activar->execute();

                if ($res == false) {
                    return response()->json($this->ajaxResponse(false, 'Ocurrió un error al activar la tarjeta en la controladora.', $res));
                }
                */

                $response_message = "Solicitud <b>MARCADA COMO IMPRESA</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($gafete->gest_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new GafeteEstacionamientoImpreso($gafete));
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
                $gafete = GafeteEstacionamiento::findOrFail($this->data['gest_id']);

                if ($gafete->gest_estado != 'IMPRESA') {
                    return response()->json($this->ajaxResponse(false, 'PARA MARCAR UNA SOLICITUD COMO <b>ENTREGADA</b>, SU ESTADO DEBE SER <b>IMPRESA</b>'));
                }

                // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
                $gafete->gest_estado = 'ENTREGADA';
                $gafete->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Solicitud <b>FINALIZADA</b> correctamente."));
        }


    }

    public function doMarcarPendienteCobro(GafeteEstacionamiento $gafete)
    {
        \DB::beginTransaction();

        try {
            // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
            $gafete->gest_estado = 'PENDIENTE DE COBRO';

            $gafete->save();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Gafete marcado como <b>PENDIENTE DE COBRO</b> correctamente."));
    }

    public function doMarcarCobrada(GafeteEstacionamiento $gafete)
    {
        \DB::beginTransaction();

        try {
            // $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
            $gafete->gest_estado = 'COBRADA';

            $gafete->save();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Gafete marcado como <b>COBRADA</b> correctamente."));
    }


    /**
     * Genera un pdf que sirve de comprobante de la solicitud para
     * procesos internos del negocio
     * @param SolicitudGafete $solicitud
     */
    public function comprobantePDF(GafeteEstacionamiento $gafete)
    {

        $report = new ComprobanteSolicitudEstacionamientoReport(null, true, false);
        $report->setSolicitud($gafete);

        return $report->exec();

    }


    public function doPdf(GafeteEstacionamiento $gafete)
    {

        $report = new GafeteEstacionamientoReport(null, true, false);
        $report->setGafete($gafete);

        return $report->exec();


//        return view('web.solicitud-gafete.imprimir', compact('solicitud', 'url'));
    }

    public function contraparteEstacionamientoPdf(GafeteEstacionamiento $gafete)
    {

        $report = new ContraparteGafeteEstacionamientoReport($gafete, true, false);
        $report->setSolicitud($gafete);

        return $report->exec();
    }

    public function getJSON(GafetePreimpreso $gafete)
    {
        return response()->json($gafete);
    }

//    /*Obtiene la información para un campo select2*/
//    public function getSelectOptions(Request $request){
//      $q = $request -> q;
//
//      $records = Local::select(\DB::raw('lcal_id as id, lcal_nombre_comercial as text'))
//                              ->where('lcal_nombre_comercial','like',"%$q%")
//                              ->get() -> toArray();
//      $records[0]['selected'] = true;
//      return response()->json( $records);
//    }


}
