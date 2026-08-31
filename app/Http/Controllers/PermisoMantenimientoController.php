<?php

namespace App\Http\Controllers;

use App\ActividadAltoRiesgo;
use App\EppBasico;
use App\EppEspecifico;
use App\EquipoHerramienta;
use App\Notifications\PermisoMantenimientoAprobado;
use App\Notifications\PermisoTemporalVencido;
use App\Reports\FormatoMantenimientoReport;
use App\User;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Local;
use App\MedidaControlRiesgo;
use App\PermisoMantenimiento;

use App\Notifications\PermisoMantenimientoRechazado;
use App\Reports\FormatoMantenimientoAnalisisRiesgoReport;
use App\RiesgoAsociado;
use App\TipoMantenimiento;
use App\TrabajoEspecifico;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermisoMantenimientoController extends Controller
{

    protected $rules = [
        'insert' => [
            'pmtt_lcal_id'    => 'required:exists:locales,lcal_id',
            'pmtt_empresa'     => 'required',
            'pmtt_solicitante'     => 'required',
            'pmtt_representante'     => 'required',
            'pmtt_trabajo'     => 'required',
            'pmtt_listado_trabajadores'     => 'nullable',
            'pmtt_fecha'     => 'required|date_format:Y-m-d H:i:s',
            'pmtt_vigencia_inicial'     => 'required|date_format:Y-m-d',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'     => 'required|date_format:H:i',
            'pmtt_tmtt_id' => 'required|exists:tipos_mantenimiento,tmtt_id',
            'pmtt_tesp_id' => 'required|exists:trabajos_especificos,tesp_id',
            // 'pmtt_vigencia_final'     => 'required|date_format:Y-m-d',
            'pmtt_dias'     => 'required|numeric|min:1|max:7',
        ],

        'verify' => [
            'pmtt_aprobar'    => 'required',
            'pmtt_comentario_admon'     => 'nullable'
        ],
        'aprobar-hess' => [
            'pmtt_tipo_actividad'     => 'required',
            'pmtt_comentario_hess' => 'nullable',
            'actividades_alto_riesgo' => ['required', 'array', 'min:1'],
            'pmtt_otra_actividad_riesgo' => ['nullable'],
            'riesgos_asociados' => ['required', 'array', 'min:1'],
            'medidas_control_riesgo' => ['required', 'array', 'min:1'],
            'pmtt_dispositivo_bloquear' => ['nullable'],
            'equipos_herramientas' => ['required', 'array', 'min:1']
        ],
        'rechazar' => [
            'pmtt_comentario_admon'     => 'required'
        ],

        'reapply' => [
            'pmtt_id'    => 'required:exists:permisos_mantenimiento,pmtt_id',
            'pmtt_lcal_id'    => 'required:exists:locales,lcal_id',
            'pmtt_empresa'     => 'required',
            'pmtt_solicitante'     => 'required',
            'pmtt_representante'     => 'required',
            'pmtt_trabajo'     => 'required',
            'pmtt_observaciones'     => 'nullable',
            'pmtt_listado_trabajadores'     => 'nullable',
            'pmtt_fecha'     => 'required|date_format:Y-m-d H:i:s',
            'pmtt_vigencia_inicial'     => 'required|date_format:Y-m-d',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'     => 'required|date_format:H:i',
            // 'pmtt_vigencia_final'     => 'required|date_format:Y-m-d',
            'pmtt_dias'     => 'required|numeric|min:1|max:7',
        ],

        // 'edit' => [
        //     'empl_id'       => 'required|exists:empleados,empl_id',
        //     'empl_lcal_id'  => 'required:exists:locales,lcal_id',
        //     'empl_nombre'   => 'required',
        //     'empl_email'    => 'nullable|email',
        //     'empl_crgo_id'  => 'required|exists:cat_cargos,crgo_id',
        //     'empl_telefono'      => 'nullable|numeric',
        //     'empl_foto'      => 'nullable',
        //     'empl_comentario'      => 'nullable',
        //     'data_photo' => 'nullable'
        // ],


    ];

    protected  $etiquetas = [
        'pmtt_lcal_id'    => 'Local',
        'pmtt_empresa'     => 'Empresa',
        'pmtt_solicitante'     => 'Solicitante',
        'pmtt_representante'     => 'Representante',
        'pmtt_trabajo'     => 'Descripción de las actividades a realizar',
        'pmtt_observaciones'     => 'Observaciones',
        'pmtt_listado_trabajadores'     => 'Listado de trabajadores',
        'pmtt_fecha'     => 'Fecha',
        'pmtt_vigencia_inicial'     => 'Vigencia inicial',
        'hora_inicio'     => 'Hora inicio',
        'hora_fin'     => 'Hora fin',
        'pmtt_tmtt_id' => 'Tipo de mantenimiento',
        'pmtt_tesp_id' => 'Trabajo Específico',
        // 'pmtt_vigencia_final'     => 'required|date_format:Y-m-d',
        'pmtt_dias'     => 'Dias solicitados',
        'pmtt_comentario_admon' => 'Comentario',
        'pmtt_tipo_actividad' => 'Tipo de actividad',
        'pmtt_otra_actividad_riesgo' => 'Otra actividad',
        'pmtt_dispositivo_bloquear' => 'Dispositivo a bloquear'
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
     * Para el rol de LOCATARIO
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \Exception
     */
    public function index(Request $request, Builder $htmlBuilder)
    {

        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;

        if ($request->ajax()) {

            $records = PermisoMantenimiento::select([
                'pmtt_id',
                'pmtt_empresa',
                'tesp_nombre',
                'pmtt_fecha',
                'pmtt_vigencia_inicial',
                'pmtt_vigencia_final',
                'pmtt_estado',
                'pmtt_comentario_admon',
                'pmtt_hess_approved_by'
            ])
                ->leftJoin('trabajos_especificos', 'tesp_id', '=', 'pmtt_tesp_id')
                ->wherePmttLcalId($local->lcal_id)
                //                ->whereIn('pmtt_estado',['PENDIENTE','APROBADO']) // VENCIDO -> mutator
                ->whereRaw('(
                            (pmtt_estado IN ("PENDIENTE","APROBADO") AND  CURDATE() <= pmtt_vigencia_final)
                                OR
                            (pmtt_estado = "RECHAZADO" AND  date(pmtt_updated_at) >= CURDATE() - INTERVAL 3 DAY )
                            )');

            return Datatables::of(
                $records
            )
                ->addColumn('actions', function (PermisoMantenimiento $model) {

                    $html = '<div class="btn-group">';
                    //                    $html .= '<span class="btn btn-primary btn-sm btn-detalles" title="Detalles" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    //
                    //                    if($model->pmtt_estado == 'PENDIENTE'){
                    //                        $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    //                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    //                    }

                    if ($model->pmtt_estado == 'APROBADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato" data-id=' . $model->pmtt_id . '><i class="fa fa-file-pdf-o"></i></span>';
                        if ($model->HessAprobadoPor()->exists())
                            $html .= '<span class="btn btn-primary btn-sm btn-pdf-analisis-riesgo" title="Análisis de Riesgo" data-id=' . $model->pmtt_id . '><i class="fa fa-file-code-o"></i></span>';
                    }

                    //                    $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->editColumn('pmtt_estado', function (PermisoMantenimiento $model) {

                    $color = 'badge-primary';
                    if ($model->pmtt_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->pmtt_estado == 'APROBADO') $color = 'badge-success';
                    //                if($model->pmtt_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->pmtt_estado == 'RECHAZADO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->pmtt_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['pmtt_estado', 'actions'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth'  => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            //            'responsive'=>[
            //                    'details'=>
            //                        ['display'=>'$.fn.dataTable.Responsive.display.modal()']
            //            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'pmtt_id', 'name' => 'pmtt_id', 'title' => 'Id', 'visible' => false])

            ->addColumn(['data' => 'pmtt_empresa', 'name' => 'pmtt_empresa', 'title' => 'Empresa', 'responsivePriority' => 1])
            // ->addColumn(['data' => 'pmtt_cargo', 'name' => 'pmtt_cargo', 'title' => 'Cargo', 'search'=>true])
            ->addColumn(['data' => 'pmtt_fecha', 'name' => 'pmtt_fecha', 'title' => 'Fecha', 'responsivePriority' => 2])
            //             ->addColumn(['data' => 'pmtt_dias', 'name' => 'pmtt_dias', 'title' => 'Dias Solc.'])
            ->addColumn(['data' => 'pmtt_vigencia_inicial', 'name' => 'pmtt_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'pmtt_vigencia_final', 'name' => 'pmtt_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'tesp_nombre', 'name' => 'tesp_nombre', 'title' => 'Trabajo', 'width' => '40%'])
            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado', 'responsivePriority' => 3])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'responsivePriority' => 0]);

        return view('web.permiso-mantenimiento.index', compact('dataTable', 'local'));
    }

    public function form(PermisoMantenimiento $permiso = null, Request $request)
    {

        $url = ($permiso == null) ? url('permiso-mantenimiento/insert') : url('permiso-mantenimiento/edit', $permiso->getKey());

        $tiposMantenimiento = TipoMantenimiento::all()->pluck('tmtt_nombre', 'tmtt_id');
        $trabajadores = $permiso == null ? '' : $permiso->Trabajadores;

        return view('web.permiso-mantenimiento.form', compact('permiso', 'url', 'tiposMantenimiento', 'trabajadores'));
    }

    public function insert(Request $request)
    {

        if (! $this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {
            //validamos la hora de la solicitud
            $hora = date('H');
            // if ($hora < 7 || $hora > 14) {
            //     return response()->json($this->ajaxResponse(false, 'Solicitud fuera de horario, deberá solicitar su solicitud de mantenimiento en el horario de 7am a 3pm'));
            // }

            \DB::beginTransaction();
            try {

                $this->data['pmtt_vigencia_inicial'] .= " " . $this->data['hora_inicio'] . ":00";
                $inicio = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->data['pmtt_vigencia_inicial']);

                $this->data['pmtt_created_by'] = auth()->user()->id;
                $this->data['pmtt_vigencia_final'] = $inicio->addDays($this->data['pmtt_dias'] - 1)->format('Y-m-d') . " " . $this->data['hora_fin'] . ":00";
                $listado_trabajadores = json_decode($this->data['pmtt_listado_trabajadores'], true);
                $permiso = PermisoMantenimiento::create(Arr::except($this->data, ['pmtt_listado_trabajadores', 'hora_inicio', 'hora_fin']));
                foreach ($listado_trabajadores as $trabajador) {
                    $res = $permiso->Trabajadores()->create([
                        'pmtb_nombre' => $trabajador['nombre']
                    ]);
                    $ids[] = $res->getKey();
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Permiso <b>CREADO</b> correctamente.'));
        }
    }

    public function formReapply(PermisoMantenimiento $permiso, Request $request)
    {

        $url =  url('permiso-mantenimiento/reapply', $permiso->getKey());

        $tiposMantenimiento = TipoMantenimiento::all()->pluck('tmtt_nombre', 'tmtt_id');

        $trabajadores = $permiso->Trabajadores;

        return view('web.permiso-mantenimiento.form', compact('permiso', 'url', 'tiposMantenimiento', 'trabajadores'));
    }

    public function reapply(PermisoMantenimiento $permiso, Request $request)
    {
        if (! $this->validateAction('reapply')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {

                $this->data['pmtt_vigencia_inicial'] .= " " . $this->data['hora_inicio'] . ":00";
                $inicio = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->data['pmtt_vigencia_inicial']);

                $this->data['pmtt_comentario_admon'] = "";
                $this->data['pmtt_estado'] = "PENDIENTE";
                $this->data['pmtt_created_by'] = auth()->user()->id;
                $this->data['pmtt_vigencia_final'] = $inicio->addDays($this->data['pmtt_dias'] - 1)->format('Y-m-d') . " " . $this->data['hora_fin'] . ":00";

                $listado_trabajadores = json_decode($this->data['pmtt_listado_trabajadores'], true);
                $permiso->update(Arr::except($this->data, ['pmtt_listado_trabajadores', 'hora_inicio', 'hora_fin']));
                $ids = [];
                foreach ($listado_trabajadores as $trabajador) {
                    if ($trabajador['id']) {
                        $permiso->Trabajadores()->where('pmtb_id', $trabajador['id'])->update(['pmtb_nombre' => $trabajador['nombre']]);
                        $ids[] = $trabajador['id'];
                    } else {
                        $res = $permiso->Trabajadores()->create([
                            'pmtb_nombre' => $trabajador['nombre']
                        ]);
                        $ids[] = $res->getKey();
                    }
                }
                $permiso->Trabajadores()->whereNotIn('pmtb_id', $ids)->delete();
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();

            return response()->json($this->ajaxResponse(true, "Permiso <b>EDITADO</b> correctamente."));
        }
    }


    public function edit(PermisoMantenimiento $permiso, Request $request)
    {
        if (! $this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $permiso->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Permiso de mantenimiento <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(PermisoMantenimiento $permiso, Request $request)
    {

        \DB::beginTransaction();
        try {
            $permiso->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Permiso de mantenimiento <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(PermisoMantenimiento $permiso)
    {
        return response()->json($permiso);
    }


    ///////////////////////////////////////////////////////////////////////////

    public function indexRecepcion(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = DB::table('permisos_mantenimiento')
                ->select(
                    'pmtt_id',
                    'pmtt_empresa',
                    'pmtt_trabajo',
                    'pmtt_fecha',
                    'pmtt_dias',
                    'lcal_nombre_comercial',
                    'pmtt_fecha',
                    'pmtt_vigencia_inicial',
                    'pmtt_vigencia_final',
                    'pmtt_estado',
                    'pmtt_comentario_admon'
                )
                ->leftJoin('locales', 'pmtt_lcal_id', '=', 'lcal_id')
                ->whereRaw('(
                            (pmtt_estado IN ("PENDIENTE", "PENDIENTE POR HESS","APROBADO") AND  CURDATE() <= pmtt_vigencia_final)
                                OR
                            (pmtt_estado = "RECHAZADO" AND  date(pmtt_updated_at) >= CURDATE() - INTERVAL 7 DAY )
                            )');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePmttLcalId($filtro);
            }

            return Datatables::of(
                $records
            )
                ->addColumn('actions', function ($model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment"></i></span>';
                    //                $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato Gafete" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-accounts-list-alt"></i></span>';
                    //                $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    //                $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    //                $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->editColumn('pmtt_estado', function ($model) {

                    $color = 'badge-primary';
                    if ($model->pmtt_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->pmtt_estado == 'PENDIENTE POR HESS') $color = 'badge-pink';
                    if ($model->pmtt_estado == 'APROBADO') $color = 'badge-success';
                    //                if($model->pmtt_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->pmtt_estado == 'RECHAZADO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->pmtt_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['pmtt_estado', 'actions'])
                ->make(true);
        }

        //Definicion del script de frontend
        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => false,
            'autoWidth'  => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('permiso-mantenimiento/recepcion'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'pmtt_id', 'name' => 'pmtt_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local', 'responsivePriority' => 2])
            ->addColumn(['data' => 'pmtt_empresa', 'name' => 'pmtt_empresa', 'title' => 'Empresa'])
            ->addColumn(['data' => 'pmtt_fecha', 'name' => 'pmtt_fecha', 'title' => 'Fecha', 'responsivePriority' => 3])
            ->addColumn(['data' => 'pmtt_dias', 'name' => 'pmtt_dias', 'title' => 'Dias Solc.', 'responsivePriority' => 4])
            ->addColumn(['data' => 'pmtt_vigencia_inicial', 'name' => 'pmtt_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'pmtt_vigencia_final', 'name' => 'pmtt_vigencia_final', 'title' => 'Fin'])
            //            ->addColumn(['data' => 'pmtt_trabajo', 'name' => 'pmtt_trabajo', 'title' => 'Trabajo'])
            //            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'responsivePriority' => 1]);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-mantenimiento.index-recepcion', compact('dataTable', 'locales'));
    }

    public function detallesView(PermisoMantenimiento $permiso)
    {
        return view('web.permiso-mantenimiento.detalles-form', compact('permiso'));
    }


    ///////////////////////////////////////////////////////////////////////////////////////////
    public function indexMantenimiento(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = PermisoMantenimiento::select([
                'pmtt_id',
                'pmtt_empresa',
                'pmtt_trabajo',
                'tesp_nombre',
                'trgo_nombre',
                'pmtt_fecha',
                'lcal_nombre_comercial',
                'pmtt_vigencia_inicial',
                'pmtt_vigencia_final',
                'pmtt_estado',
                'pmtt_comentario_admon',
                'pmtt_mtt_approved_by',
                'pmtt_hess_approved_by'
            ])
                ->leftJoin('trabajos_especificos', 'tesp_id', '=', 'pmtt_tesp_id')
                ->leftJoin('tipos_riesgo', 'trgo_id', '=', 'tesp_trgo_id')
                ->join('locales', 'pmtt_lcal_id', 'lcal_id')
                ->whereIn('pmtt_estado', ['PENDIENTE', 'PENDIENTE POR HESS', 'APROBADO']) // VENCIDO -> mutator
                ->whereRaw('CURDATE() <= pmtt_vigencia_final ');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePmttLcalId($filtro);
            }

            return Datatables::of(
                $records
            )
                ->addColumn('actions', function (PermisoMantenimiento $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalles" title="Detalles" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment"></i></span>';

                    if ($model->pmtt_estado == 'PENDIENTE' && !$model->pmtt_mtt_approved_by) {
                        $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    }

                    if ($model->pmtt_estado == 'APROBADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato" data-id=' . $model->pmtt_id . '><i class="fa fa-file-pdf-o"></i></span>';
                        if ($model->pmtt_hess_approved_by)
                            $html .= '<span class="btn btn-primary btn-sm btn-pdf-analisis-riesgo" title="Análisis de Riesgo" data-id=' . $model->pmtt_id . '><i class="fa fa-file-code-o"></i></span>';
                    }

                    //                    $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->editColumn('pmtt_estado', function (PermisoMantenimiento $model) {

                    $color = 'badge-primary';
                    if ($model->pmtt_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->pmtt_estado == 'PENDIENTE POR HESS') $color = 'badge-pink';
                    if ($model->pmtt_estado == 'APROBADO') $color = 'badge-success';
                    //                if($model->pmtt_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->pmtt_estado == 'RECHAZADO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->pmtt_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['pmtt_estado', 'actions'])
                ->make(true);
        }

        //Definicion del script de frontend
        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => false,
            'autoWidth'  => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('permiso-mantenimiento/mantenimiento'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'pmtt_id', 'name' => 'pmtt_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'pmtt_empresa', 'name' => 'pmtt_empresa', 'title' => 'Empresa'])
            ->addColumn(['data' => 'pmtt_fecha', 'name' => 'pmtt_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'pmtt_vigencia_inicial', 'name' => 'pmtt_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'pmtt_vigencia_final', 'name' => 'pmtt_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'tesp_nombre', 'name' => 'tesp_nombre', 'title' => 'Trabajo específico'])
            ->addColumn(['data' => 'pmtt_trabajo', 'name' => 'pmtt_trabajo', 'title' => 'Trabajo a realizar'])
            ->addColumn(['data' => 'trgo_nombre', 'name' => 'trgo_nombre', 'title' => 'Tipo riesgo'])
            //            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-mantenimiento.index-mantenimiento', compact('dataTable', 'locales'));
    }

    public function indexHess(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {

            $records = PermisoMantenimiento::select([
                'pmtt_id',
                'pmtt_empresa',
                'pmtt_trabajo',
                'tesp_nombre',
                'trgo_nombre',
                'pmtt_fecha',
                'lcal_nombre_comercial',
                'pmtt_fecha',
                'pmtt_vigencia_inicial',
                'pmtt_vigencia_final',
                'pmtt_estado',
                'pmtt_comentario_admon',
                'pmtt_mtt_approved_by',
                'pmtt_hess_approved_by'
            ])
                ->leftJoin('trabajos_especificos', 'tesp_id', '=', 'pmtt_tesp_id')
                ->leftJoin('tipos_riesgo', 'trgo_id', '=', 'tesp_trgo_id')
                ->leftJoin('locales', 'pmtt_lcal_id', 'lcal_id')
                ->whereIn('pmtt_estado', ['PENDIENTE POR HESS', 'APROBADO']) // VENCIDO -> mutator
                ->whereNotNull('pmtt_mtt_approved_by')
                ->where('trgo_requiere_doble_aprob', 1)
                ->whereRaw('CURDATE() <= pmtt_vigencia_final ');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePmttLcalId($filtro);
            }

            return Datatables::of(
                $records
            )
                ->addColumn('actions', function (PermisoMantenimiento $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalles" title="Detalles" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment"></i></span>';

                    if ($model->pmtt_estado == 'PENDIENTE POR HESS' && $model->pmtt_mtt_approved_by) {
                        $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    }

                    if ($model->pmtt_estado == 'APROBADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato" data-id=' . $model->pmtt_id . '><i class="fa fa-file-pdf-o"></i></span>';
                        if ($model->pmtt_hess_approved_by)
                            $html .= '<span class="btn btn-primary btn-sm btn-pdf-analisis-riesgo" title="Análisis de Riesgo" data-id=' . $model->pmtt_id . '><i class="fa fa-file-code-o"></i></span>';
                    }

                    //                    $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })
                ->editColumn('pmtt_estado', function (PermisoMantenimiento $model) {

                    $color = 'badge-primary';
                    if ($model->pmtt_estado == 'PENDIENTE POR HESS') $color = 'badge-pink';
                    if ($model->pmtt_estado == 'APROBADO') $color = 'badge-success';
                    //                if($model->pmtt_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->pmtt_estado == 'RECHAZADO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->pmtt_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['pmtt_estado', 'actions'])
                ->make(true);
        }

        //Definicion del script de frontend
        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => false,
            'autoWidth'  => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('permiso-mantenimiento/hess'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'pmtt_id', 'name' => 'pmtt_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'pmtt_empresa', 'name' => 'pmtt_empresa', 'title' => 'Empresa'])
            ->addColumn(['data' => 'pmtt_fecha', 'name' => 'pmtt_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'pmtt_vigencia_inicial', 'name' => 'pmtt_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'pmtt_vigencia_final', 'name' => 'pmtt_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'tesp_nombre', 'name' => 'tesp_nombre', 'title' => 'Trabajo específico'])
            ->addColumn(['data' => 'pmtt_trabajo', 'name' => 'pmtt_trabajo', 'title' => 'Trabajo a realizar'])
            ->addColumn(['data' => 'trgo_nombre', 'name' => 'trgo_nombre', 'title' => 'Tipo riesgo'])
            //            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-mantenimiento.index-hess', compact('dataTable', 'locales'));
    }

    public function rechazarView(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/do-rechazar', $permiso->pmtt_id);
        $permiso->pmtt_comentario_admon = '';

        return view('web.permiso-mantenimiento.form-rechazar', compact('permiso', 'url'));
    }

    public function aprobarViewMantenimiento(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/do-aprobar-mantenimiento', $permiso->pmtt_id);

        return view('web.permiso-mantenimiento.form-aprobar', compact('permiso', 'url'));
    }
    public function aprobarViewHess(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/do-aprobar-hess', $permiso->pmtt_id);

        $actividadesAltoRiesgo = ActividadAltoRiesgo::all()->pluck('actar_nombre', 'actar_id');
        $riesgosAsociados = RiesgoAsociado::all()->pluck('rasoc_nombre', 'rasoc_id');
        $medidasControlRiesgo = MedidaControlRiesgo::all()->pluck('medcr_nombre', 'medcr_id');
        $equiposHerramientas = EquipoHerramienta::all()->pluck('eqher_nombre', 'eqher_id');

        return view(
            'web.permiso-mantenimiento.form-aprobar-hess',
            compact(
                'permiso',
                'actividadesAltoRiesgo',
                'riesgosAsociados',
                'medidasControlRiesgo',
                'equiposHerramientas',
                'url'
            )
        );
    }

    public function rechazar(PermisoMantenimiento $permiso, Request $request)
    {

        if (! $this->validateAction('rechazar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        }


        \DB::beginTransaction();

        try {

            //dd($permiso);

            if ($permiso->pmtt_estado != 'PENDIENTE' && $permiso->pmtt_estado != 'PENDIENTE POR HESS') {
                return response()->json($this->ajaxResponse(false, 'Solo se pueden rechazar solicitudes en estado <b>PENDIENTE</b> y <b>PENDIENTE POR HESS</b> '));
            }

            $permiso->pmtt_estado  = 'RECHAZADO';
            $permiso->pmtt_comentario_admon = $this->data['pmtt_comentario_admon'];
            $permiso->save();

            $response_message = "Permiso de mantenimiento <b>RECHAZADO</b>.";
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($permiso->pmtt_lcal_id)
                    ->get();

                \Notification::send($locatarios, new PermisoMantenimientoRechazado($permiso, $this->data['pmtt_comentario_admon']));

                $otros = User::whereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])->get();

                \Notification::send($otros, new PermisoMantenimientoRechazado($permiso, $this->data['pmtt_comentario_admon']));
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

    public function aprobarMantenimiento(PermisoMantenimiento $permiso, Request $request)
    {

        \DB::beginTransaction();

        try {

            if ($permiso->pmtt_estado != 'PENDIENTE') {
                return response()->json($this->ajaxResponse(false, 'Solo se pueden aprobar solicitudes en estado <b>PENDIENTE</b>'));
            }
            if ($permiso->pmtt_mtt_approved_by) {
                return response()->json($this->ajaxResponse(false, 'La solicitud ya ha sido <b>APROBADA</b> por mantenimiento'));
            }

            if ($permiso->TrabajoEspecifico->TipoRiesgo->trgo_requiere_doble_aprob)
                $permiso->pmtt_estado  = 'PENDIENTE POR HESS';
            else
                $permiso->pmtt_estado  = 'APROBADO';
            $permiso->pmtt_mtt_approved_by  = auth()->user()->id;

            if ($request->get('pmtt_comentario_admon') != "") {
                $permiso->pmtt_comentario_admon = $request->pmtt_comentario_admon;
            }

            $permiso->save();

            $response_message = "Permiso de mantenimiento <b>APROBADO</b>.";
            if ($permiso->TrabajoEspecifico->TipoRiesgo->trgo_requiere_doble_aprob)
                $response_message .= " Queda pendiente de aprobación por parte de <b>HESS</b>.";
            $response_data = [];

            if ($permiso->pmtt_estado == 'APROBADO') {
                try {

                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($permiso->pmtt_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new PermisoMantenimientoAprobado($permiso));

                    $otros = User::whereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])->get();

                    \Notification::send($otros, new PermisoMantenimientoAprobado($permiso));
                    //-------------------------------------------------------------------------------------

                } catch (\Exception $e) {
                    $response_message .= ' Error al notificar';
                    $response_data['notification_error'] = $e->getMessage();
                }
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, $response_message, $response_data));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }
    }

    public function aprobarHess(PermisoMantenimiento $permiso, Request $request)
    {
        if (isset($this->data['actividades_alto_riesgo']) && in_array(7, $this->data['actividades_alto_riesgo']))
            $this->rules['aprobar-hess']['pmtt_otra_actividad_riesgo'][] = 'required';
        if (isset($this->data['medidas_control_riesgo']) && in_array(2, $this->data['medidas_control_riesgo']))
            $this->rules['aprobar-hess']['pmtt_dispositivo_bloquear'][] = 'required';
        if (!$this->validateAction('aprobar-hess')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        }

        \DB::beginTransaction();

        try {

            if ($permiso->pmtt_estado != 'PENDIENTE POR HESS') {
                return response()->json($this->ajaxResponse(false, 'Solo se pueden aprobar solicitudes en estado <b>PENDIENTE POR HESS</b>'));
            }
            if (!$permiso->pmtt_mtt_approved_by) {
                return response()->json($this->ajaxResponse(false, 'La solicitud debe ser <b>APROBADA</b> primeramente por MANTENIMIENTO.'));
            }

            $permiso->pmtt_estado  = 'APROBADO';
            $permiso->pmtt_hess_approved_by  = auth()->user()->id;
            $permiso->pmtt_comentario_hess = $this->data['pmtt_comentario_hess'];
            $permiso->pmtt_tipo_actividad = $this->data['pmtt_tipo_actividad'];
            $permiso->pmtt_otra_actividad_riesgo = $this->data['pmtt_otra_actividad_riesgo'];
            $permiso->pmtt_dispositivo_bloquear = $this->data['pmtt_dispositivo_bloquear'];
            $permiso->save();

            $permiso->ActividadesAltoRiesgo()->sync($this->data['actividades_alto_riesgo']);
            $permiso->RiesgosAsociados()->sync($this->data['riesgos_asociados']);
            $permiso->MedidasControlRiesgo()->sync($this->data['medidas_control_riesgo']);
            $permiso->EquiposHerramientas()->sync($this->data['equipos_herramientas']);

            $response_message = "Permiso de mantenimiento <b>APROBADO</b>.";
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($permiso->pmtt_lcal_id)
                    ->get();

                \Notification::send($locatarios, new PermisoMantenimientoAprobado($permiso));

                $otros = User::whereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])
                    ->get();

                \Notification::send($otros, new PermisoMantenimientoAprobado($permiso));
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

    public function formatoPdfFirmante(PermisoMantenimiento $permiso)
    {

        if ($permiso->pmtt_estado != 'APROBADO') {
            return response()->json($this->ajaxResponse(false, 'El permiso debe ser <b>APROBADO</b> primero.'));
        }
        $report = new FormatoMantenimientoReport(null, true, false);
        $report->setPermiso($permiso);

        return $report->exec();
    }

    public function formatoPdfAnalisisRiesgo(PermisoMantenimiento $permiso)
    {

        if ($permiso->pmtt_estado != 'APROBADO') {
            return response()->json($this->ajaxResponse(false, 'El permiso debe ser <b>APROBADO</b> primero.'));
        }

        $report = new FormatoMantenimientoAnalisisRiesgoReport(null, true, false);
        $report->setPermiso($permiso);
        $report->setTiposMantenimiento(TipoMantenimiento::all()->pluck('tmtt_nombre', 'tmtt_id')->toArray());
        $report->setActividadesAltoRiesgo(ActividadAltoRiesgo::all()->pluck('actar_nombre', 'actar_id')->toArray());
        $report->setRiesgosAsociados(RiesgoAsociado::all()->map(function ($element) {
            return ['id' => $element->rasoc_id, 'nombre' => $element->rasoc_nombre];
        })->toArray());
        $report->setMedidasControlRiesgo(MedidaControlRiesgo::all()->map(function ($element) {
            return ['id' => $element->medcr_id, 'nombre' => $element->medcr_nombre];
        })->toArray());
        $report->setEquiposHerramientas(EquipoHerramienta::all()->map(function ($element) {
            return ['id' => $element->eqher_id, 'nombre' => $element->eqher_nombre];
        })->toArray());
        $report->setTrabajadores($permiso->Trabajadores()->get()->map(function ($element) {
            return ['id' => $element->pmtb_id, 'nombre' => $element->pmtb_nombre, 'nss' => $element->pmtb_nss];
        })->toArray());

        return $report->exec();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    //D E P R E C A T E D
    ///////////////////////////////////////////////////////////////////////////////////////////
    public function formVerify(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/verify', $permiso->pmtt_id);
        return view('web.permiso-mantenimiento.form-verify', compact('permiso', 'url'));
    }

    public function verify(PermisoMantenimiento $permiso, Request $request)
    {
        if (! $this->validateAction('verify')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {

                $permiso->pmtt_comentario_admon = $this->data['pmtt_comentario_admon'];

                if ($this->data['pmtt_aprobar'] == 1) {
                    $permiso->pmtt_estado  = 'APROBADO';
                } else {
                    $permiso->pmtt_estado  = 'RECHAZADO';
                }

                $permiso->save();

                $response_message = "Permiso de mantenimiento <b>PROCESADO</b> correctamente.";
                $response_data = [];

                if ($permiso->pmtt_estado == 'RECHAZADO') {
                    try {
                        $locatarios = User::role('LOCATARIO')
                            ->whereUsrLcalId($permiso->pmtt_lcal_id)
                            ->get();

                        \Notification::send($locatarios, new PermisoMantenimientoRechazado($permiso));

                        $otros = User::whereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])
                            ->get();

                        \Notification::send($otros, new PermisoMantenimientoRechazado($permiso));
                    } catch (\Exception $e) {
                        $response_message .= ' Error al notificar';
                        $response_data['notification_error'] = $e->getMessage();
                    }
                }

                \DB::commit();
                return response()->json($this->ajaxResponse(true, $response_message, $response_data));
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }
        }
    }
}
