<?php

namespace App\Http\Controllers;

use App\Notifications\PermisoMantenimientoAprobado;
use App\Notifications\PermisoTemporalVencido;
use App\Reports\FormatoMantenimientoReport;
use App\User;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Local;
use App\PermisoMantenimiento;

use App\Notifications\PermisoMantenimientoRechazado;
use Illuminate\Support\Facades\DB;

class PermisoMantenimientoController extends Controller
{

    protected $rules = [
        'insert' => [
            'pmtt_lcal_id'    => 'required:exists:locales,lcal_id',
            'pmtt_empresa'     => 'required',
            'pmtt_solicitante'     => 'required',
            'pmtt_representante'     => 'required',
            'pmtt_trabajo'     => 'required',
            'pmtt_observaciones'     => 'nullable',
            'pmtt_listado_trabajadores'     => 'nullable',
            'pmtt_fecha'     => 'required|date_format:Y-m-d H:i:s',
            'pmtt_vigencia_inicial'     => 'required|date_format:Y-m-d',
            // 'pmtt_vigencia_final'     => 'required|date_format:Y-m-d',
            'pmtt_dias'     => 'required|numeric|min:1|max:7',
        ],

        'verify' => [
            'pmtt_aprobar'    => 'required',
            'pmtt_comentario_admon'     => 'nullable'
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
        'pmtt_trabajo'     => 'Trabajo',
        'pmtt_observaciones'     => 'Observaciones',
        'pmtt_listado_trabajadores'     => 'Listado de trabajadores',
        'pmtt_fecha'     => 'Fecha',
        'pmtt_vigencia_inicial'     => 'Vigencia inicial',
        // 'pmtt_vigencia_final'     => 'required|date_format:Y-m-d',
        'pmtt_dias'     => 'Dias solicitados',
        'pmtt_comentario_admon' => 'Comentario'
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
                'pmtt_trabajo',
                'pmtt_fecha',
                'pmtt_vigencia_inicial',
                'pmtt_vigencia_final',
                'pmtt_estado',
                'pmtt_comentario_admon'
            ])
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
                    }

                    //                    $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
                    $html .= '</div>';

                    return $html;
                })

                ->addColumn('actions', function (PermisoMantenimiento $model) {

                    $html = '<div class="btn-group">';
                    //                $html .= '<span class="btn btn-primary btn-sm btn-detalles" title="Detalles" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment"></i></span>';

                    //                if($model->pmtt_estado == 'PENDIENTE'){
                    //                    $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                    //                    $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    //                }

                    if ($model->pmtt_estado == 'APROBADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato" data-id=' . $model->pmtt_id . '><i class="fa fa-file-pdf-o"></i></span>';
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
            ->addColumn(['data' => 'pmtt_trabajo', 'name' => 'pmtt_trabajo', 'title' => 'Trabajo', 'width' => '40%'])
            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado', 'responsivePriority' => 3])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'responsivePriority' => 0]);

        return view('web.permiso-mantenimiento.index', compact('dataTable', 'local'));
    }

    public function form(PermisoMantenimiento $permiso = null, Request $request)
    {

        $url = ($permiso == null) ? url('permiso-mantenimiento/insert') : url('permiso-mantenimiento/edit', $permiso->getKey());

        return view('web.permiso-mantenimiento.form', compact('permiso', 'url'));
    }

    public function insert(Request $request)
    {

        if (! $this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            //validamos la hora de la solicitud
            $hora = date('H');
            if ($hora < 7 || $hora > 14) {
                return response()->json($this->ajaxResponse(false, 'Solicitud fuera de horario, deberá solicitar su solicitud de mantenimiento en el horario de 7am a 3pm'));
            }


            \DB::beginTransaction();
            try {

                $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $this->data['pmtt_vigencia_inicial']);

                $this->data['pmtt_created_by'] = auth()->user()->id;
                $this->data['pmtt_vigencia_final'] = $inicio->addDays($this->data['pmtt_dias'] - 1)->format('Y-m-d');
                $permiso = PermisoMantenimiento::create($this->data);
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

        return view('web.permiso-mantenimiento.form', compact('permiso', 'url'));
    }

    public function reapply(PermisoMantenimiento $permiso, Request $request)
    {
        if (! $this->validateAction('reapply')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {

                $inicio = \Carbon\Carbon::createFromFormat('Y-m-d', $this->data['pmtt_vigencia_inicial']);

                $this->data['pmtt_comentario_admon'] = "";
                $this->data['pmtt_estado'] = "PENDIENTE";
                $this->data['pmtt_created_by'] = auth()->user()->id;
                $this->data['pmtt_vigencia_final'] = $inicio->addDays($this->data['pmtt_dias'] - 1)->format('Y-m-d');

                $permiso->update($this->data);
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
                            (pmtt_estado IN ("PENDIENTE","APROBADO") AND  CURDATE() <= pmtt_vigencia_final)
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
                'pmtt_fecha',
                'pmtt_dias',
                'lcal_nombre_comercial',
                'pmtt_fecha',
                'pmtt_vigencia_inicial',
                'pmtt_vigencia_final',
                'pmtt_estado',
                'pmtt_comentario_admon'
            ])
                ->join('locales', 'pmtt_lcal_id', 'lcal_id')
                ->whereIn('pmtt_estado', ['PENDIENTE', 'APROBADO']) // VENCIDO -> mutator
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

                    if ($model->pmtt_estado == 'PENDIENTE') {
                        $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->pmtt_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    }

                    if ($model->pmtt_estado == 'APROBADO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato" data-id=' . $model->pmtt_id . '><i class="fa fa-file-pdf-o"></i></span>';
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
            ->addColumn(['data' => 'pmtt_dias', 'name' => 'pmtt_dias', 'title' => 'Dias Solc.'])
            ->addColumn(['data' => 'pmtt_vigencia_inicial', 'name' => 'pmtt_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'pmtt_vigencia_final', 'name' => 'pmtt_vigencia_final', 'title' => 'Fin'])
            //            ->addColumn(['data' => 'pmtt_trabajo', 'name' => 'pmtt_trabajo', 'title' => 'Trabajo'])
            //            ->addColumn(['data' => 'pmtt_comentario_admon', 'name' => 'pmtt_comentario_admon', 'title' => 'Comentario Admon.'])
            ->addColumn(['data' => 'pmtt_estado', 'name' => 'pmtt_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-mantenimiento.index-mantenimiento', compact('dataTable', 'locales'));
    }

    public function rechazarView(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/do-rechazar', $permiso->pmtt_id);

        return view('web.permiso-mantenimiento.form-rechazar', compact('permiso', 'url'));
    }

    public function aprobarView(PermisoMantenimiento $permiso)
    {

        $url = url('permiso-mantenimiento/do-aprobar', $permiso->pmtt_id);

        return view('web.permiso-mantenimiento.form-aprobar', compact('permiso', 'url'));
    }

    public function rechazar(PermisoMantenimiento $permiso, Request $request)
    {

        if (! $this->validateAction('rechazar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        }


        \DB::beginTransaction();

        try {

            //dd($permiso);

            if ($permiso->pmtt_estado != 'PENDIENTE') {
                return response()->json($this->ajaxResponse(false, 'Solo se pueden rechazar solicitudes en estado <b>PENDIENTE</b> ' . $permiso->pmtt_estado));
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
                    ->orWhereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])
                    ->get();

                \Notification::send($locatarios, new PermisoMantenimientoRechazado($permiso, $this->data['pmtt_comentario_admon']));
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

    public function aprobar(PermisoMantenimiento $permiso, Request $request)
    {

        \DB::beginTransaction();

        try {

            if ($permiso->pmtt_estado != 'PENDIENTE') {
                return response()->json($this->ajaxResponse(false, 'Solo se pueden aprobar solicitudes en estado <b>PENDIENTE</b>'));
            }

            $permiso->pmtt_estado  = 'APROBADO';
            $permiso->pmtt_approved_by  = auth()->user()->id;

            if ($request->get('pmtt_comentario_admon') != "") {
                $permiso->pmtt_comentario_admon = $request->pmtt_comentario_admon;
            }

            $permiso->save();

            $response_message = "Permiso de mantenimiento <b>APROBADO</b>.";
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($permiso->pmtt_lcal_id)
                    ->orWhereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])
                    ->get();

                \Notification::send($locatarios, new PermisoMantenimientoAprobado($permiso));
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
            return response()->json($this->ajaxResponse(false, 'El permiso debe ser <b>APROBADO</b>   primero.'));
        }
        $report = new FormatoMantenimientoReport(null, true, false);
        $report->setPermiso($permiso);

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
                            ->orWhereIn('email', ['jortiz2@carnival.com', 'rvizcaino@carnival.com', 'lvargas@carnival.com'])
                            ->get();

                        \Notification::send($locatarios, new PermisoMantenimientoRechazado($permiso));
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
