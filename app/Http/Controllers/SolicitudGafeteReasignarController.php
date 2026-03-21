<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjetaV3;
use App\Actions\CrearTarjetaV3;
use App\Controladora;
use App\Empleado;
use App\Local;
use App\Notifications\PermisosGafeteEstacionamientoValidado;
use App\Notifications\SolicitudGafeteImpreso;
use App\Puerta;
use App\SolicitudGafete;
use App\SolicitudGafeteReasignar;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class SolicitudGafeteReasignarController extends Controller
{
    protected $rules = [
        'insert' => [
            'sgftre_lcal_id' => 'required|exists:locales,lcal_id',
            'sgftre_empl_id' => 'required|exists:empleados,empl_id',
            'sgftre_sgft_id' => 'required|exists:solicitudes_gafetes,sgft_id',
            'sgftre_permisos' => ['required', 'array', 'min:2'],
            'sgftre_anio' => 'required'
        ],
        'update' => [
            'sgftre_id' => 'required|exists:solicitudes_gafetes_reasignar,sgftre_id',
            'sgftre_lcal_id' => 'required|exists:locales,lcal_id',
            'sgftre_empl_id' => 'required|exists:empleados,empl_id',
            'sgftre_sgft_id' => 'required|exists:solicitudes_gafetes,sgft_id',
            'sgftre_permisos' => ['required', 'array', 'min:2'],
            'sgftre_anio' => 'required'
        ],
        'do-validar' => [
            'sgftre_id' => 'required|exists:solicitudes_gafetes_reasignar,sgftre_id',
            'sgft_numero' => 'required|digits_between:5,10',
            // 'sgft_comentario_admin'     => 'required',
        ],
        'do-rechazar' => [
            'sgftre_id' => 'required|exists:solicitudes_gafetes_reasignar,sgftre_id',
            'sgftre_comentarios_rechazo' => 'required',
            // 'sgft_comentario_admin'     => 'required',
        ],
    ];

    protected $etiquetas = [
        'sgftre_lcal_id' => 'Local',
        'sgftre_empl_id' => 'Empleado',
        'sgftre_sgft_id' => 'Gafete',
        'sgft_numero' => 'Número de Tarjeta',
        'sgftre_permisos' => 'Permisos',
        'sgftre_anio' => 'Año',
        'sgftre_comentarios_rechazo' => 'Comentarios'
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

    /**
     * para el RECEPCION
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

        if ($request->ajax()) {
            return DataTables::of(
                SolicitudGafeteReasignar::select([
                    'sgftre_id',
                    'sgftre_empl_id',
                    'sgftre_sgft_id',
                    DB::raw("'' as empl_thumb"),
                    DB::raw("'' as empl_nombre"),
                    DB::raw("'' as sgft_numero"),
                    'sgftre_permisos',
                    'sgftre_anio',
                    'sgftre_estado',
                    'lcal_nombre_comercial',
                    'sgft_tipo',
                    DB::raw("IF(sgftre_estado = 'DENEGADO', sgftre_comentarios_rechazo, sgft_comentario) as sgft_comentario")
                ])
                    ->leftJoin('locales', 'lcal_id', '=', 'sgftre_lcal_id')
                    ->leftJoin('solicitudes_gafetes', 'sgft_id', '=', 'sgftre_sgft_id')
                    ->where('sgftre_estado', 'PENDIENTE')
                    ->orWhereRaw("GREATEST(IFNULL(DATE(sgftre_fecha_asignado), '1000-01-01'), IFNULL(DATE(sgftre_fecha_denegado), '1000-01-01'), IFNULL(DATE(sgftre_fecha_autorizado), '1000-01-01')) = ?", [today()->format('Y-m-d')])
            )
                ->editColumn('empl_thumb', function (SolicitudGafeteReasignar $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . Storage::disk('public')->url("empleados/{$model->Empleado->empl_thumb}") . '" style="max-height:35px" />';
                })
                ->editColumn('empl_nombre', function (SolicitudGafeteReasignar $model) {
                    return $model->Empleado->empl_nombre;
                })
                ->editColumn('sgft_numero', function (SolicitudGafeteReasignar $model) {
                    return $model->Gafete->sgft_numero;
                })
                ->editColumn('sgftre_permisos', function (SolicitudGafeteReasignar $model) {
                    return Str::replaceLast(',', ' y ', str_replace(['PEATONAL,', 'PEATONAL'], '', $model->sgftre_permisos));
                })
                ->editColumn('sgft_tipo', function (SolicitudGafeteReasignar $model) {
                    $color = 'badge-primary';
                    if ($model->sgft_tipo == 'PRIMERA VEZ') $color = 'badge-info';
                    if ($model->sgftre_estado == 'REPOSICIÓN') $color = 'badge-primary';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgft_tipo . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('sgftre_estado', function (SolicitudGafeteReasignar $model) {

                    $color = 'badge-primary';
                    if ($model->sgftre_estado == 'PENDIENTE') $color = 'badge-inverse';
                    if ($model->sgftre_estado == 'ASIGNADO') $color = 'badge-success';
                    if ($model->sgftre_estado == 'DENEGADO') $color = 'badge-warning';
                    if ($model->sgftre_estado == 'CANCELADO') $color = 'badge-danger';


                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->sgftre_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('actions', function (SolicitudGafeteReasignar $model) {

                    $nombre_empleado = $model->Empleado->empl_nombre;
                    if ($model->sgftre_estado != 'PENDIENTE') return '<p class="text-center">--</p>';
                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-validar" title="Validar solicitud" data-id=' . $model->sgftre_id . '><i class="zmdi zmdi-check"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar solicitud" data-id=' . $model->sgftre_id . ' data-empleado="' . $nombre_empleado . '"><i class="zmdi zmdi-close"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->rawColumns(['empl_thumb', 'empl_nombre', 'sgft_numero', 'sgftre_permisos', 'sgft_tipo', 'sgftre_estado', 'actions'])
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
            ->addColumn(['data' => 'sgftre_id', 'name' => 'sgftre_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'empl_thumb', 'name' => 'empl_thumb', 'title' => 'Foto', 'search' => false, 'width' => '5%'])
            ->addColumn(['data' => 'empl_nombre', 'name' => 'empl_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local', 'search' => true])
            ->addColumn(['data' => 'sgftre_anio', 'name' => 'sgftre_anio', 'title' => 'Año', 'search' => true])
            ->addColumn(['data' => 'sgftre_permisos', 'name' => 'sgftre_permisos', 'title' => 'Clase', 'search' => true])
            ->addColumn(['data' => 'sgft_numero', 'name' => 'sgft_numero', 'title' => 'Número de gafete', 'search' => true])
            ->addColumn(['data' => 'sgft_tipo', 'name' => 'sgft_tipo', 'title' => 'Tipo', 'search' => true])
            ->addColumn(['data' => 'sgftre_estado', 'name' => 'sgftre_estado', 'title' => 'Estado', 'search' => true])
            ->addColumn(['data' => 'sgft_comentario', 'name' => 'sgft_comentario', 'title' => 'Comentarios', 'search' => true])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $estadistica = [
            'espacios_totales_auto' => Local::all()->sum('lcal_espacios_autos'),
            'espacios_totales_moto' => Local::all()->sum('lcal_espacios_motos'),
            'gafetes_asignados_auto' => SolicitudGafeteReasignar::whereIn('sgftre_estado', ['ASIGNADO', 'AUTORIZADO'])
                ->whereRaw("sgftre_anio = " . date('Y'))
                ->whereRaw("sgftre_permisos like '%AUTO%'")
                ->count(),
            'gafetes_asignados_moto' => SolicitudGafeteReasignar::whereIn('sgftre_estado', ['ASIGNADO', 'AUTORIZADO'])
                ->whereRaw("sgftre_anio = " . date('Y'))
                ->whereRaw("sgftre_permisos like '%MOTO%'")
                ->count(),
        ];

        return view('web.solicitud-gafete-reasignar.index', compact(
            'dataTable',
            'estadistica'
        ));
    }

    public function form(Empleado $empleado, Request $request)
    {
        if ($empleado->GafeteReasignado && in_array($empleado->GafeteReasignado->sgftre_estado, ['ASIGNADO', 'AUTORIZADO']))
            return '<p class="alert alert-danger"> El empleado ya cuenta con un gafete asignado.</p>';

        $solicitud = $empleado->GafeteReasignado ? $empleado->GafeteReasignado : new SolicitudGafeteReasignar();

        $url = !$solicitud->sgftre_id ? url('solicitud-gafete-reasignar/insert') : url('solicitud-gafete-reasignar/update/' . $solicitud->getKey());
        $solicitud->sgftre_permisos = $solicitud->sgftre_id ? explode(',', $solicitud->sgftre_permisos) : [];

        //determinamos el local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        if (
            !$empleado->GafeteAcceso()
            || !($empleado->GafeteAcceso()->sgft_numero > 0)
            || Carbon::createFromFormat('Y-m-d', $empleado->GafeteAcceso()->sgft_fecha)->year != settings()->get('anio_impresion', date('Y'))
        )
            return '<p class="alert alert-danger"> El empleado no cuenta con un gafete activo vigente. <br> Contacte al administrador.</p>';

        $local = $usuario->Local;

        $saldos = $local->getSaldos();

        $gafetesAuto       = $local->gafetesAutoReasignadosAprobados(true);
        $gafetesMoto       = $local->gafetesMotoReasignadosAprobados(true);

        if ($solicitud->sgftre_id && $solicitud->sgftre_estado != 'DENEGADO') {
            $gafetesAuto->where('sgftre_id', '!=', $solicitud->getKey());
            $gafetesMoto->where('sgftre_id', '!=', $solicitud->getKey());
        }

        $gafetesAuto = $local->lcal_espacios_autos - $gafetesAuto->count();
        $gafetesMoto = $local->lcal_espacios_motos - $gafetesMoto->count();

        return view('web.solicitud-gafete-reasignar.form', compact(
            'solicitud',
            'empleado',
            'url',
            'local',
            'gafetesAuto',
            'gafetesMoto'
        ));
    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();
            try {
                $local = Local::find($this->data['sgftre_lcal_id']);
                if (in_array('AUTO', $this->data['sgftre_permisos'])) {
                    if (($local->lcal_espacios_autos - $local->gafetesAutoReasignadosAprobados(true)->count()) < 1)
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de AUTO disponibles."));
                }
                if (in_array('MOTO', $this->data['sgftre_permisos'])) {
                    if (($local->lcal_espacios_motos - $local->gafetesMotoReasignadosAprobados(true)->count()) < 1)
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de MOTO disponibles."));
                }

                $solicitud = new SolicitudGafeteReasignar();
                $permisos = implode(',', $this->data['sgftre_permisos']);

                $solicitud->sgftre_empl_id = $this->data['sgftre_empl_id'];
                $solicitud->sgftre_lcal_id = $this->data['sgftre_lcal_id'];
                $solicitud->sgftre_sgft_id = $this->data['sgftre_sgft_id'];
                $solicitud->sgftre_anio = $this->data['sgftre_anio'];
                $solicitud->sgftre_permisos = $permisos;
                $solicitud->sgftre_fecha_solicitado = now();
                $solicitud->save();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Permisos <b>SOLICITADOS</b> correctamente.'));
        }
    }

    public function update(SolicitudGafeteReasignar $solicitud, Request $request)
    {

        if (!$this->validateAction('update')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {
            if (in_array($solicitud->sgftre_estado, ['ASIGNADO', 'AUTORIZADO']))
                return response()->json($this->ajaxResponse(false, "La solicitud ya se encuentra en estado <b>$solicitud->sgftre_estado</b>."));

            DB::beginTransaction();
            try {
                $local = Local::find($this->data['sgftre_lcal_id']);
                if (in_array('AUTO', $this->data['sgftre_permisos'])) {
                    if (($local->lcal_espacios_autos - $local->gafetesAutoReasignadosAprobados(true)->where('sgftre_id', '!=', $solicitud->getKey())->count()) < 1)
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de AUTO disponibles."));
                }
                if (in_array('MOTO', $this->data['sgftre_permisos'])) {
                    if (($local->lcal_espacios_motos - $local->gafetesMotoReasignadosAprobados(true)->where('sgftre_id', '!=', $solicitud->getKey())->count()) < 1)
                        return response()->json($this->ajaxResponse(false, "Ya se han utilizado todas las solicitudes de MOTO disponibles."));
                }

                $permisos = implode(',', $this->data['sgftre_permisos']);

                $solicitud->sgftre_empl_id = $this->data['sgftre_empl_id'];
                $solicitud->sgftre_lcal_id = $this->data['sgftre_lcal_id'];
                $solicitud->sgftre_sgft_id = $this->data['sgftre_sgft_id'];
                $solicitud->sgftre_anio = $this->data['sgftre_anio'];
                $solicitud->sgftre_permisos = $permisos;
                $solicitud->sgftre_estado = 'PENDIENTE';
                $solicitud->sgftre_fecha_solicitado = now();
                $solicitud->save();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Permisos <b>SOLICITADOS</b> correctamente.'));
        }
    }

    public function delete(SolicitudGafeteReasignar $solicitud)
    {
        try {
            switch ($solicitud->sgftre_estado) {
                case 'PENDIENTE':
                case 'DENEGADO':
                    $solicitud->delete();
                    break;
                case 'ASIGNADO':
                    $solicitudGafete = SolicitudGafete::find($solicitud->sgftre_sgft_id);
                    $solicitudGafete->Puertas()->detach($solicitudGafete->Puertas()->where('door_tipo', '!=', 'PEATONAL')->pluck('door_id'));
                    $solicitudGafete->sgft_permisos = 'PEATONAL';
                    $solicitudGafete->save();
                    $solicitud->delete();
                    break;
                case 'AUTORIZADO':
                    DB::beginTransaction();
                    try {
                        $solicitud->sgftre_estado = 'CANCELADO';
                        $solicitud->save();

                        $gafete = $solicitud->Gafete;
                        $gafeteRfid = $solicitud->Gafete->getVGafeteRfidV3();


                        $gafete->Puertas()->detach($gafete->Puertas()->where('door_tipo', '!=', 'PEATONAL')->pluck('door_id'));

                        $gafete->sgft_permisos = 'PEATONAL';
                        $gafete->save();


                        $controladora = Controladora::find($gafeteRfid->controladora_id);

                        $activar = new ActivarTarjetaV3($gafeteRfid);
                        $res = $activar->execute();
                        if ($res == false) {
                            DB::rollBack();
                            Log::error("Ocurrió un error al cambiar los permisos de la tarjeta $gafeteRfid->numero_rfid en la controladora $controladora->ctrl_nombre.");
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al cambiar los permisos de la tarjeta $gafeteRfid->numero_rfid en la controladora $controladora->ctrl_nombre."));
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
                    }
                    DB::commit();
                    break;
                case 'CANCELADO':
                    return response()->json($this->ajaxResponse(false, 'La solicitud está en proceso de <b>CANCELACIÓN</b>. No puede ser eliminada manualmente.'));
            }
            return response()->json($this->ajaxResponse(true, 'Permisos <b>QUITADOS</b> correctamente.'));
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
        }
    }

    /**
     * Vista para validar los permisos asignados por el locatario a los empleados
     * @param SolicitudGafete $solicitudF
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function formValidar(SolicitudGafeteReasignar $solicitud)
    {
        if ($solicitud->sgftre_estado != 'PENDIENTE')
            return '<p class="alert alert-danger"> La solicitud debe estar en estado <b>PENDIENTE</b>.</p>';
        $url = url('solicitud-gafete-reasignar/do-validar/' . $solicitud->getKey());

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
                ->where('door_modo', 'FISICA')
                ->whereIn('door_tipo', explode(',', $solicitud->sgftre_permisos))->get();

        return view('web.solicitud-gafete-reasignar.validar', compact('solicitud', 'url', 'puertas'));
    }

    /**
     * Ejecuta la acción de valida los permisos otorgados por el locatario y le asigna las puertas por las cual tendra permiso acceder y salir
     * en BD
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doValidar(Request $request)
    {
        $input = $request->input();
        if (!$this->validateAction('do-validar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $solicitud = SolicitudGafeteReasignar::findOrFail($this->data['sgftre_id']);

            if ($solicitud->sgftre_estado != 'PENDIENTE') {
                return response()->json($this->ajaxResponse(false, 'PARA <b>VALIDAR</b> UNA SOLICITUD DE PERMISOS, SU ESTADO DEBE SER <b>PENDIENTE</b>'));
            }

            DB::beginTransaction();

            try {
                $solicitud = SolicitudGafeteReasignar::findOrFail($this->data['sgftre_id']);
                $solicitud->sgftre_estado = 'ASIGNADO';
                $solicitud->sgftre_fecha_asignado = now();
                $solicitud->save();

                $solicitud->Gafete->sgft_permisos = $solicitud->sgftre_permisos;
                $solicitud->Gafete->save();

                $puertas = [];
                foreach ($input as $key => $data) {
                    if (stripos($key, 'puerta_') === 0)
                        $puertas[] = explode('_', $key)[1];
                }

                $solicitud->Gafete->Puertas()->sync($puertas);

                $response_message = "Permisos <b>ASIGNADOS</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($solicitud->sgft_lcal_id)
                        ->get();

                    Notification::send($locatarios, new PermisosGafeteEstacionamientoValidado($solicitud));
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

    public function formRechazar(SolicitudGafeteReasignar $solicitud)
    {
        if ($solicitud->sgftre_estado != 'PENDIENTE')
            return '<p class="alert alert-danger"> La solicitud debe estar en estado <b>PENDIENTE</b>.</p>';
        $url = url('solicitud-gafete-reasignar/do-rechazar');

        return view('web.solicitud-gafete-reasignar.rechazar', compact('solicitud', 'url'));
    }

    public function doRechazar(Request $request)
    {
        if (!$this->validateAction('do-rechazar')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            $solicitud = SolicitudGafeteReasignar::findOrFail($this->data['sgftre_id']);

            if ($solicitud->sgftre_estado != 'PENDIENTE') {
                return response()->json($this->ajaxResponse(false, 'PARA <b>RECHAZAR</b> UNA SOLICITUD DE PERMISOS, SU ESTADO DEBE SER <b>PENDIENTE</b>'));
            }

            DB::beginTransaction();

            try {
                $solicitud = SolicitudGafeteReasignar::findOrFail($this->data['sgftre_id']);
                $solicitud->sgftre_estado = 'DENEGADO';
                $solicitud->sgftre_comentarios_rechazo = $this->data['sgftre_comentarios_rechazo'];
                $solicitud->sgftre_fecha_denegado = now();
                $solicitud->save();

                DB::commit();
                return response()->json($this->ajaxResponse(true, 'Solicitud <b>RECHAZADA</b> correctamente.'));
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage() . $e->getFile() . $e->getLine()));
            }
        }
    }
}
