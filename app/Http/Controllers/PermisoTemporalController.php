<?php

namespace App\Http\Controllers;

use App\Actions\DesactivarTarjeta;
use App\Clases\PhotoSaver;
use App\Empleado;
use App\Notifications\ComprobanteRechazado;
use App\Notifications\PermisoTemporalAprobado;
use App\Notifications\PermisoTemporalCobrado;
use App\Notifications\PermisoTemporalRechazado;
use App\Reports\FormatoOficialPermisoTemporalReport;
use App\User;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Local;
use App\PermisoTemporal;
use App\GafetePreimpreso;
use App\CatCargo;


class PermisoTemporalController extends Controller
{

    protected $system_folder_empleados = "";

    protected $rules = [
        'insert' => [
            'ptmp_lcal_id' => 'required|exists:locales,lcal_id',
            'ptmp_nombre' => 'required',
            'ptmp_crgo_id' => 'required|exists:cat_cargos,crgo_id',
            'ptmp_correo' => 'nullable',
            'ptmp_telefono' => 'nullable',
            'ptmp_fecha' => 'required|date_format:Y-m-d',
            'ptmp_vigencia_inicial' => 'required|date_format:Y-m-d',
            'ptmp_vigencia_final' => 'required|date_format:Y-m-d',
            'ptmp_comentario' => 'nullable',
            'ptmp_vacunado' => 'nullable',
            'ptmp_cert_vacuna' => 'nullable',
            'data_photo' => 'required',
            // 'ptmp_caracter'     => 'nullable',
            // 'ptmp_objeto'     => 'nullable',
        ],

        'do-rechazar' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
            'ptmp_comentario_admin' => 'required',
        ],

        'do-aprobar' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
        ],

        'do-asignar' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
            'ptmp_gfpi_id' => 'required|exists:gafetes_preimpresos,gfpi_id',
        ],

        'do-entregar' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
        ],

        'do-recibir' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
        ],

        'do-pagar-extemporaneo' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
//            'ptmp_cpag_id'    => 'required|exists:comprobantes_pago,cpag_id',
        ],


        'reapply' => [
            'ptmp_id' => 'required|exists:permisos_temporales,ptmp_id',
            'ptmp_lcal_id' => 'required|exists:locales,lcal_id',
            'ptmp_nombre' => 'required',
            'ptmp_crgo_id' => 'required|exists:cat_cargos,crgo_id',
            'ptmp_correo' => 'nullable',
            'ptmp_telefono' => 'nullable',
            'ptmp_fecha' => 'required|date_format:Y-m-d',
            'ptmp_vigencia_inicial' => 'required|date_format:Y-m-d',
            'ptmp_vigencia_final' => 'required|date_format:Y-m-d',
            'ptmp_comentario' => 'nullable',
            'ptmp_vacunado' => 'nullable',
            'ptmp_cert_vacuna' => 'nullable',
            'data_photo' => 'nullable',
            // 'ptmp_caracter'     => 'nullable',
            // 'ptmp_objeto'     => 'nullable',
        ],


    ];

    protected $etiquetas = [
        'ptmp_id' => 'Permiso',
        'ptmp_lcal_id' => 'Local',
        'ptmp_nombre' => 'Nombre',
        'ptmp_cargo' => 'Cargo',
        'ptmp_correo' => 'Correo',
        'ptmp_telefono' => 'Teléfono',
        'ptmp_fecha' => 'Fecha',
        'ptmp_vigencia_inicial' => 'Vigencia Inicial',
        'ptmp_vigencia_final' => 'Vigencia Final',
        'ptmp_caracter' => 'Carácter',
        'ptmp_objeto' => 'Objeto',
        'ptmp_comentario' => 'Comentario',
        'ptmp_comentario_admin' => 'Comentario',
        'ptmp_vacunado' => 'Vacunado',
        'ptmp_cert_vacuna' => 'Certificado',
        'data_photo' => 'Fotografía'
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
        if (isset($this->data['ptmp_vacunado']) && $this->data['ptmp_vacunado'] === 'on')
            $this->data['ptmp_vacunado'] = 1;
        else {
            $this->data['ptmp_vacunado'] = 0;
        }
//        $this->system_folder_empleados = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $this->system_folder_empleados = public_path("storage") . DIRECTORY_SEPARATOR . 'empleados';
    }


    public function index(Request $request, Builder $htmlBuilder)
    {

        //detrminamos el local
        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;
        // dd($usuario,$local);

        if ($request->ajax()) {
            return Datatables::of(
                PermisoTemporal::select(['ptmp_id', 'ptmp_nombre', 'ptmp_crgo_id', 'crgo_descripcion',
                    'ptmp_fecha', 'ptmp_vigencia_inicial', 'ptmp_vigencia_final',
                    'ptmp_thumb',
                    'ptmp_estado', 'ptmp_comentario', 'ptmp_comentario_admin'])
                    ->join('cat_cargos', 'ptmp_crgo_id', 'crgo_id')
                    ->leftJoin('gafetes_preimpresos', 'ptmp_gfpi_id', 'gfpi_id')
                    ->wherePtmpLcalId($local->lcal_id)
                    // ->whereIn('ptmp_estado',['PENDIENTE','APROBADO'])
                    ->whereRaw('CURDATE() <= ptmp_vigencia_final ')
            )
                ->addColumn('actions', function (PermisoTemporal $model) {

                    $html = '<div class="btn-group">';

                    if (in_array($model->ptmp_estado, ['APROBADO', 'CONCLUIDO', 'ASIGNADO', 'VENCIDO'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-formato-oficial" title="Formato oficial" data-id=' . $model->ptmp_id . '><i class="fa fa-file-pdf-o"></i></span>';
                    }
                    $html .= '</div>';

                    return $html;

                })
                ->editColumn('ptmp_thumb', function (PermisoTemporal $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->ptmp_thumb_web . '" style="max-height:35px" />';
//                    return '<img class=" mx-auto d-block img-fluid" src="'.asset($model->ptmp_thumb_web).'" style="max-height:35px" />';
                })
                ->editColumn('ptmp_comentario', function (PermisoTemporal $model) {
                    $html = '<small>';

                    $html .= $model->ptmp_comentario;

                    if ($model->ptmp_comentario_admin != "")
                        $html .= '<br/><b class="text-info">' . $model->ptmp_comentario_admin . '</b>';

                    $html .= '</small>';

                    return $html;
                })
                ->editColumn('ptmp_estado', function (PermisoTemporal $model) {

                    $color = 'badge-primary';
                    if ($model->ptmp_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->ptmp_estado == 'APROBADO') $color = 'badge-success';
                    if ($model->ptmp_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->ptmp_estado == 'RECHAZADO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->ptmp_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
                ->filterColumn('ptmp_comentario', function ($query, $keyword) {
                    $query->whereRaw(" CONCAT(ptmp_comentario, ' ', ptmp_comentario_admin) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $query->whereRaw(" crgo_descripcion like ?", ["%{$keyword}%"]);
                })
//                        ->filterColumn('gfpi_numero', function($query, $keyword) {
//                                $query->whereRaw(" gfpi_numero like ?", ["%{$keyword}%"]);
//                            })
                ->rawColumns(['ptmp_comentario', 'ptmp_thumb', 'ptmp_estado', 'actions'])
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
            ->addColumn(['data' => 'ptmp_id', 'name' => 'ptmp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'ptmp_thumb', 'name' => 'ptmp_thumb', 'title' => 'Foto'])
            ->addColumn(['data' => 'ptmp_nombre', 'name' => 'ptmp_nombre', 'title' => 'Nombre'])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo', 'search' => true])
            ->addColumn(['data' => 'ptmp_fecha', 'name' => 'ptmp_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'ptmp_vigencia_inicial', 'name' => 'ptmp_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'ptmp_vigencia_final', 'name' => 'ptmp_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'ptmp_estado', 'name' => 'ptmp_estado', 'title' => 'Estado'])
//            ->addColumn(['data' => 'gfpi_numero', 'name' => 'gfpi_numero', 'title' => 'Gafete' ])
            ->addColumn(['data' => 'ptmp_comentario', 'name' => 'ptmp_comentario', 'title' => 'Comentarios'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'responsivePriority' => 1]);


        return view('web.permiso-temporal.index', compact('dataTable', 'local'));

    }

    public function form(PermisoTemporal $permiso = null, Request $request)
    {

        $url = ($permiso == null) ? url('permiso-temporal/insert') : url('permiso-temporal/edit', $permiso->getKey());

        $cargos = CatCargo::selectRaw('crgo_id , crgo_descripcion')
            ->get()
            ->pluck('crgo_descripcion', 'crgo_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-temporal.form', compact('permiso', 'url', 'cargos'));
    }

    public function formReapply(PermisoTemporal $permiso, Request $request)
    {

        $url = url('permiso-temporal/reapply', $permiso->getKey());

        $cargos = CatCargo::selectRaw('crgo_id , crgo_descripcion')
            ->get()
            ->pluck('crgo_descripcion', 'crgo_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.permiso-temporal.form', compact('permiso', 'url', 'cargos'));
    }

    public function insert(Request $request)
    {

//        dd($request->input());
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {

                //validamos la hora de la solicitud
                $hora = date('H');
                if ($hora < 7 || $hora > 14) {
                    return response()->json($this->ajaxResponse(false, 'Permiso fuera de horario, deberá solicitar su permiso temporal en el horario de 7am a 3pm'));
                }

                // validamos que no vuelvan a pedir permiso por el mismo trabajador
                $record = PermisoTemporal::where('ptmp_lcal_id', $this->data['ptmp_lcal_id'])->where('ptmp_nombre', $this->data['ptmp_nombre'])->first();
                if ($record != null) {
                    return response()->json($this->ajaxResponse(false, 'Ya se ha solicitado un permiso temporal anteriormente para este trabajador en este Local.'));
                }

                // validamos que no exista el trabajador en otro local
                $record = Empleado::where('empl_lcal_id', '<>', $this->data['ptmp_lcal_id'])
                    ->where('empl_nombre', $this->data['ptmp_nombre'])
                    ->first();
                if ($record != null) {
                    return response()->json($this->ajaxResponse(false, 'El empleado esta dado de alta en: <b>' . $record->Local->lcal_nombre_comercial . '</b>. <br/> Solicite su baja para poder capturar el permiso temporal. '));
                }


                $this->data['ptmp_created_by'] = auth()->user()->id;
                $data = \Arr::except($this->data, ['data_photo', 'ptmp_cert_vacuna']);
                $data['ptmp_fecha'] = $data['ptmp_fecha'] . ' ' . now()->format('H:i:s');

                $permiso = PermisoTemporal::create($data);

                if ($request->ptmp_cert_vacuna) {
                    $permiso->ptmp_cert_vacuna = $request->file('ptmp_cert_vacuna')
                        ->storeAs('', $permiso->getKey() . "_Permiso_Temporal." . $request->file('ptmp_cert_vacuna')->extension(), 'certificados_vacunacion');
                    $permiso->save();
                }

                if ($this->data['data_photo'] != "") {

                    $fileFoto = 'pt_' . $permiso->ptmp_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = 'pt_' . $permiso->ptmp_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {

                        $permiso->ptmp_foto = $fileFoto;
                        $permiso->ptmp_thumb = $fileThumb;
                        $permiso->save();

                    }
                }

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Permiso <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(PermisoTemporal $permiso, Request $request)
    {
        if (!$this->validateAction('reapply')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                if ($request->ptmp_cert_vacuna) {
                    if ($request->hasFile('ptmp_cert_vacuna')) {
                        if ($permiso->ptmp_cert_vacuna && Storage::disk('certificados_vacunacion')->exists($permiso->ptmp_cert_vacuna)) {
                            Storage::disk('certificados_vacunacion')->delete($permiso->ptmp_cert_vacuna);
                        }
                        $data['ptmp_cert_vacuna'] = $request->file('ptmp_cert_vacuna')
                            ->storeAs('', $permiso->getKey() . "_Permiso_Temporal." . $request->file('ptmp_cert_vacuna')->extension(), 'certificados_vacunacion');
                    }
                } else {
                    $data['ptmp_cert_vacuna'] = null;
                }

                $permiso->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Empleado <b>EDITADO</b> correctamente."));
        }

    }

    public function reapply(PermisoTemporal $permiso, Request $request)
    {
        if (!$this->validateAction('reapply')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {


                $data = \Arr::except($this->data, ['data_photo']);

                $data['ptmp_created_by'] = auth()->user()->id;
                $data['ptmp_comentario_admin'] = "";
                $data['ptmp_estado'] = "PENDIENTE";
                $data['ptmp_fecha'] = $data['ptmp_fecha'] . ' ' . now()->format('H:i:s');


                if ($this->data['data_photo'] != "") {

                    $fileFoto = 'pt_' . $permiso->ptmp_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = 'pt_' . $permiso->ptmp_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {
                        $data['ptmp_foto'] = $fileFoto;
                        $data['ptmp_thumb'] = $fileThumb;
                    }
                }

                $permiso->update($data);


            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();

            return response()->json($this->ajaxResponse(true, "Permiso <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(Empleado $empleado, Request $request)
    {

        \DB::beginTransaction();
        try {
            $empleado->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Local <b>ELIMIADO</b> correctamente."));


    }


    /////////////////////////////////////////////////////////////////////////////////////

    public function indexRecepcion(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = PermisoTemporal::select(['ptmp_id', 'ptmp_nombre', 'ptmp_crgo_id', 'crgo_descripcion',
                'ptmp_lcal_id', 'lcal_nombre_comercial', 'ptmp_gfpi_id',
                'ptmp_thumb',
                'ptmp_fecha', 'ptmp_vigencia_inicial', 'ptmp_vigencia_final', 'ptmp_estado', 'ptmp_comentario'])
                ->join('cat_cargos', 'ptmp_crgo_id', 'crgo_id')
                ->join('locales', 'ptmp_lcal_id', 'lcal_id')
                ->leftJoin('gafetes_preimpresos', 'ptmp_gfpi_id', 'gfpi_id')
                ->whereRaw('CURDATE() <= ptmp_vigencia_final ')
                ->whereIn('ptmp_estado', ['PENDIENTE', 'APROBADO', 'ASIGNADO', 'ENTREGADO']);
//                                ->whereRaw('CURDATE() <= ptmp_vigencia_final ');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePtmpLcalId($filtro);
            }

            return Datatables::of($records)
                ->addColumn('actions', function (PermisoTemporal $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment"></i></span>';


//                    if ($model->ptmp_estado == 'APROBADO') {
//                        $html .= '<span class="btn btn-primary btn-sm btn-asignar" title="Asignar gafete" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
//                    }

//                    if ($model->ptmp_estado == 'ASIGNADO') {
//                        $html .= '<span class="btn btn-primary btn-sm btn-entregar" title="Marcar como entregado" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-returned"></i></span>';
//                    }

                    if ($model->ptmp_estado == 'ENTREGADO' || $model->ptmp_estado == 'VENCIDO') {
                        $html .= '<span class="btn btn-primary btn-sm btn-recibir" title="Recibir gafete" data-id=' . $model->ptmp_id . '><i class="fa fa-reply"></i></span>';
                    }

                    $html .= '</div>';

                    return $html;

                })
                ->editColumn('ptmp_thumb', function (PermisoTemporal $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->ptmp_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('ptmp_estado', function (PermisoTemporal $model) {

                    $color = 'badge-primary';
                    if ($model->ptmp_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->ptmp_estado == 'APROBADO') $color = 'badge-success';
                    if ($model->ptmp_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->ptmp_estado == 'RECHAZADO') $color = 'badge-danger';
                    if ($model->ptmp_estado == 'VENCIDO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->ptmp_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $query->whereRaw(" crgo_descripcion like ?", ["%{$keyword}%"]);
                })
//                            ->filterColumn('gfpi_numero', function($query, $keyword) {
//                                $query->whereRaw(" gfpi_numero like ?", ["%{$keyword}%"]);
//                            })
                ->rawColumns(['actions', 'ptmp_thumb', 'ptmp_estado'])
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
            'url' => url('permiso-temporal/recepcion'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'ptmp_id', 'name' => 'ptmp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'ptmp_thumb', 'name' => 'ptmp_thumb', 'title' => 'Foto'])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'ptmp_nombre', 'name' => 'ptmp_nombre', 'title' => 'Nombre'])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo', 'search' => true])
            ->addColumn(['data' => 'ptmp_fecha', 'name' => 'ptmp_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'ptmp_vigencia_inicial', 'name' => 'ptmp_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'ptmp_vigencia_final', 'name' => 'ptmp_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'ptmp_estado', 'name' => 'ptmp_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'ptmp_comentario', 'name' => 'ptmp_comentario', 'title' => 'Comentario'])
//            ->addColumn(['data' => 'gfpi_numero', 'name' => 'gfpi_numero', 'title' => 'Gafete' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');


        return view('web.permiso-temporal.index-recepcion', compact('dataTable', 'locales'));

    }

    public function detallesView(PermisoTemporal $permiso)
    {
        return view('web.permiso-temporal.detalles', compact('permiso'));
    }

    public function asignarView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-asignar');

        $gafetes = GafetePreimpreso::selectRaw('gfpi_id , gfpi_numero')
//                                ->leftJoin('permisos_temporales','gfpi_permiso_actual','=','ptmp_id')
//                                ->whereRaw( 'ptmp_id IS NULL' )
            ->whereNull('gfpi_permiso_actual')
            ->whereGfpiTipo('PERMISO')
            ->get()
            ->pluck('gfpi_numero', 'gfpi_id')
            ->put('', 'SELECCIONE UN GAFETE');

        return view('web.permiso-temporal.asignar', compact('permiso', 'url', 'gafetes'));
    }

    public function entregarView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-entregar');

        return view('web.permiso-temporal.entregar', compact('permiso', 'url'));
    }

    public function recibirView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-recibir');

        return view('web.permiso-temporal.recibir', compact('permiso', 'url'));
    }

    public function doEntregar(Request $request)
    {
        if (!$this->validateAction('do-entregar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                if ($permiso->ptmp_estado != 'ASIGNADO') {
                    return response()->json($this->ajaxResponse(false, 'PARA MARCAR UN PERMISO COMO <b>ENTREGADO</b>, SU ESTADO DEBE SER <b>ASIGNADO</b>'));
                }

                $permiso->ptmp_estado = 'ENTREGADO';
                $permiso->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Permiso <b>ENTREGADO</b> correctamente."));
        }


    }

    public function doAsignar(Request $request)
    {
        if (!$this->validateAction('do-asignar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                if ($permiso->ptmp_estado != 'APROBADO') {
                    return response()->json($this->ajaxResponse(false, 'PARA  <b>ASIGNAR</b> UN GAFETE A UN PERMISO, SU ESTADO ESTAR <b>APROBADO</b>'));
                }

                //el cambio de estado lo maneja laravel de manera lógica al tener un gafete ligado
                $permiso->ptmp_gfpi_id = $this->data['ptmp_gfpi_id'];
                $permiso->save();

                $g_preimpreso = GafetePreimpreso::findOrFail($this->data['ptmp_gfpi_id']);
                $g_preimpreso->gfpi_permiso_actual = $permiso->ptmp_id;
                $g_preimpreso->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Permiso <b>APROBADO</b> correctamente."));
        }


    }

    public function doRecibir(Request $request)
    {
        if (!$this->validateAction('do-recibir')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                if (!in_array($permiso->ptmp_estado, ['ENTREGADO', 'VENCIDO'])) {
                    return response()->json($this->ajaxResponse(false, 'EL ESTADO DEL PERMISO NO PERMITE ESTA ACCIÓN'));
                }

                $permiso->ptmp_estado = 'CONCLUIDO';
                $permiso->save();

                //liberamos el gafete preimpreso
                $g_preimpreso = $permiso->GafetePreimpreso;
                $g_preimpreso->gfpi_permiso_actual = null;
                $g_preimpreso->save();

                \DB::commit();
                return response()->json($this->ajaxResponse(true, "Permiso <b>CONCLUIDO</b> correctamente."));

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }


        }


    }

    /////////////////////////////////////////////////////////////////////////////////////
    public function indexSeguridad(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = PermisoTemporal::select(['ptmp_id', 'ptmp_nombre', 'ptmp_crgo_id', 'crgo_descripcion',
                'ptmp_lcal_id', 'lcal_nombre_comercial', 'ptmp_gfpi_id',
                'ptmp_thumb',
                'ptmp_fecha', 'ptmp_vigencia_inicial', 'ptmp_vigencia_final', 'ptmp_estado', 'ptmp_comentario'])
                ->join('cat_cargos', 'ptmp_crgo_id', 'crgo_id')
                ->join('locales', 'ptmp_lcal_id', 'lcal_id')
                ->leftJoin('gafetes_preimpresos', 'ptmp_gfpi_id', 'gfpi_id')
                ->whereNotIn('ptmp_estado', ['APROBADO', 'RECHAZADO', 'CONCLUIDO', 'VENCIDO'])
//                ->whereRaw(' ( ptmp_estado NOT IN ("RECHAZADO","CONCLUIDO")
//                                    or
//                             ( ptmp_estado= "RECHAZADO" AND  (CURDATE() - INTERVAL 7 DAY) <=  DATE(ptmp_updated_at)  ) )');
                ->whereRaw('CURDATE() <= ptmp_vigencia_final ');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePtmpLcalId($filtro);
            }

            return Datatables::of($records)
                ->addColumn('actions', function (PermisoTemporal $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment"></i></span>';

                    if ($model->ptmp_estado == 'PENDIENTE') {
                        $html .= '<span class="btn btn-primary btn-sm btn-aprobar" title="Aprobar permiso" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment-check"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-rechazar" title="Rechazar permiso" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-close-circle"></i></span>';
                    }

                    if (in_array($model->ptmp_estado, ['APROBADO', 'CONCLUIDO', 'ASIGNADO', 'VENCIDO'])) {
                        $html .= '<span class="btn btn-primary btn-sm btn-formato-oficial" title="Formato oficial" data-id=' . $model->ptmp_id . '><i class="fa fa-file-pdf-o"></i></span>';
                    }
                    $html .= '</div>';

                    return $html;

                })
                ->editColumn('ptmp_thumb', function (PermisoTemporal $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->ptmp_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('ptmp_estado', function (PermisoTemporal $model) {

                    $color = 'badge-primary';
                    if ($model->ptmp_estado == 'PENDIENTE') $color = 'badge-warning';
                    if ($model->ptmp_estado == 'APROBADO') $color = 'badge-success';
                    if ($model->ptmp_estado == 'ENTREGADO') $color = 'badge-inverse';
                    if ($model->ptmp_estado == 'RECHAZADO') $color = 'badge-danger';
                    if ($model->ptmp_estado == 'VENCIDO') $color = 'badge-danger';
//                    if($model->ptmp_estado == 'CONCLUIDO')   $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->ptmp_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $query->whereRaw(" crgo_descripcion like ?", ["%{$keyword}%"]);
                })
//                ->filterColumn('gfpi_numero', function($query, $keyword) {
//                    $query->whereRaw(" gfpi_numero like ?", ["%{$keyword}%"]);
//                })
                ->rawColumns(['actions', 'ptmp_thumb', 'ptmp_estado'])
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
            'url' => url('permiso-temporal/seguridad'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'ptmp_id', 'name' => 'ptmp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'ptmp_thumb', 'name' => 'ptmp_thumb', 'title' => 'Foto', 'responsivePriority' => 0])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'ptmp_nombre', 'name' => 'ptmp_nombre', 'title' => 'Nombre'])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo', 'search' => true])
            ->addColumn(['data' => 'ptmp_fecha', 'name' => 'ptmp_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'ptmp_vigencia_inicial', 'name' => 'ptmp_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'ptmp_vigencia_final', 'name' => 'ptmp_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'ptmp_estado', 'name' => 'ptmp_estado', 'title' => 'Estado'])
            ->addColumn(['data' => 'ptmp_comentario', 'name' => 'ptmp_comentario', 'title' => 'Comentario'])
//            ->addColumn(['data' => 'gfpi_numero', 'name' => 'gfpi_numero', 'title' => 'Gafete' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones', 'responsivePriority' => 1]);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');


        return view('web.permiso-temporal.index-seguridad', compact('dataTable', 'locales'));

    }

    public function rechazarView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-rechazar');

        return view('web.permiso-temporal.rechazar', compact('permiso', 'url'));
    }

    public function doRechazar(Request $request)
    {
        if (!$this->validateAction('do-rechazar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                $permiso->ptmp_comentario_admin = $this->data['ptmp_comentario_admin'];
                $permiso->ptmp_estado = 'RECHAZADO';
                $permiso->ptmp_gfpi_id = null;
                $permiso->save();

                $response_message = "Permiso <b>RECHAZADO</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($permiso->ptmp_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new PermisoTemporalRechazado($permiso, $this->data['ptmp_comentario_admin']));
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

    public function aprobarView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-aprobar');

        return view('web.permiso-temporal.aprobar', compact('permiso', 'url'));
    }


    public function doAprobar(Request $request)
    {
        if (!$this->validateAction('do-aprobar')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);
                $permiso->ptmp_estado = 'APROBADO';

                $permiso->ptmp_approved_by = auth()->user()->id;
                $permiso->save();

                $response_message = "Permiso <b>APROBADO</b> correctamente.";
                $response_data = [];

                try {

                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($permiso->ptmp_lcal_id)
                        ->get();

                    Notification::send($locatarios, new PermisoTemporalAprobado($permiso));
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

    public function formatoOficialPdfTest(PermisoTemporal $permiso, \Codedge\Fpdf\Fpdf\Fpdf $fpdf)
    {

//        if($permiso->pmtt_estado != 'APROBADO'){
//            return response() -> json($this->ajaxResponse(false,'El permiso debe ser <b>APROBADO</b>   primero.'));
//        }

//        $report = new FormatoOficialPermisoTemporalReport(null,true,false);
//        $report->setPermiso($permiso);

        $border = 1;
        $font_size = 12;
        $font_name = 'Arial';

//        return $report->exec();

        $fpdf->AddPage();
        $fpdf->SetFont($font_name, '', $font_size);


        $fpdf->SetFont($font_name, 'B', $font_size);
        $fpdf->Cell(190, 12, 'PERMISO DE INGRESO A PLAZA COMERCIAL', 0, 0, 'C');


        $fpdf->Ln();

        $x = $fpdf->GetX();
        $y = $fpdf->GetY();

        $cell_w = 30;
        $cel_m = (190 - ($cell_w * 3)) / 2;

        $fpdf->SetX($x + $cel_m);
        $fpdf->Cell($cell_w, 11, 'VIGENCIA', 1, 0, 'C');

        $fpdf->SetFont($font_name, '', $font_size);

        $fpdf->SetXY($x + $cel_m + $cell_w, $y);
        $fpdf->Cell($cell_w, 11, 'Del _______', 1, 0, 'C');

        $fpdf->SetXY($x + $cel_m + $cell_w + $cell_w, $y);
        $fpdf->Cell($cell_w, 11, 'al ________', 1, 0, 'C');


        $fpdf->Output('I');
        exit();

    }

    public function formatoOficialPdf(PermisoTemporal $permiso)
    {

        $report = new FormatoOficialPermisoTemporalReport(null, true, false);
        $report->setPermiso($permiso);

        return $report->exec();


    }

    /////////////////////////////////////////////////////////////////////////////////////

    public function indexVencidos(Request $request, Builder $htmlBuilder)
    {

        $dias_vencidos = settings()->get('ptmp_max_dias_vencido', 30);

        if ($request->ajax()) {

            $records = PermisoTemporal::select(['ptmp_id', 'ptmp_nombre', 'ptmp_crgo_id', 'crgo_descripcion',
                'ptmp_lcal_id', 'lcal_nombre_comercial', 'ptmp_gfpi_id',
                'ptmp_thumb', 'ptmp_estado_extemporaneo',
                'ptmp_fecha', 'ptmp_vigencia_inicial', 'ptmp_vigencia_final', 'ptmp_estado', 'ptmp_comentario'])
                ->join('cat_cargos', 'ptmp_crgo_id', 'crgo_id')
                ->join('locales', 'ptmp_lcal_id', 'lcal_id')
                ->leftJoin('gafetes_preimpresos', 'ptmp_gfpi_id', 'gfpi_id')
//                ->whereIn('ptmp_estado',['ENTREGADO'])
//                ->whereRaw('
//                            (ptmp_estado = "ENTREGADO" AND CURDATE() > ( ptmp_vigencia_final + INTERVAL '.$dias_vencidos.' DAY ))
//                            or
//                            (ptmp_estado = "CONCLUIDO" AND ptmp_estado_extemporaneo <> "PENDIENTE" )
//                            ');
                ->whereRaw('
                            (ptmp_estado = "ENTREGADO" AND CURDATE() > ( ptmp_vigencia_final + INTERVAL ' . $dias_vencidos . ' DAY ))
                            ');

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->wherePtmpLcalId($filtro);
            }

            return Datatables::of($records)
                ->addColumn('actions', function (PermisoTemporal $model) {

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-detalle" title="Detalles" data-id=' . $model->ptmp_id . '><i class="zmdi zmdi-assignment"></i></span>';


                    if ($model->ptmp_estado_extemporaneo == 'PENDIENTE') {
                        $html .= '<span class="btn btn-primary btn-sm btn-devolver" title="Devolver gafete" data-id=' . $model->ptmp_id . '><i class="fa fa-reply"></i></span>';
                        $html .= '<span class="btn btn-primary btn-sm btn-pagar" title="Pagar gafete" data-id=' . $model->ptmp_id . '><i class="fa fa-money"></i></span>';
                    }

                    $html .= '</div>';

                    return $html;

                })
                ->editColumn('ptmp_thumb', function (PermisoTemporal $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->ptmp_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('ptmp_estado', function (PermisoTemporal $model) {

                    $color = 'badge-primary';
                    if ($model->ptmp_estado == 'VENCIDO') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . $model->ptmp_estado . '</small>';
                    $html .= '</div>';
                    return $html;

                })
//                ->editColumn('ptmp_estado_extemporaneo', function(PermisoTemporal $model) {
//
//                    $color = 'badge-primary';
//                    if($model->ptmp_estado_extemporaneo == 'PENDIENTE') $color = 'badge-warning';
//                    if($model->ptmp_estado_extemporaneo == 'DEVUELTO') $color = 'badge-inverse';
//                    if($model->ptmp_estado_extemporaneo == 'PAGADO') $color = 'badge-success';
//
//                    $html = '<div class="text-center"><small class="badge '. $color .'">'. $model->ptmp_estado_extemporaneo .'</small>';
//                    $html.='</div>';
//                    return $html;
//
//                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $query->whereRaw(" crgo_descripcion like ?", ["%{$keyword}%"]);
                })
//                ->filterColumn('gfpi_numero', function($query, $keyword) {
//                    $query->whereRaw(" gfpi_numero like ?", ["%{$keyword}%"]);
//                })
                ->rawColumns(['actions', 'ptmp_thumb', 'ptmp_estado'])
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
            'url' => url('permiso-temporal/vencidos'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'ptmp_id', 'name' => 'ptmp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'ptmp_thumb', 'name' => 'ptmp_thumb', 'title' => 'Foto'])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'ptmp_nombre', 'name' => 'ptmp_nombre', 'title' => 'Nombre'])
//            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo', 'search'=>true])
//            ->addColumn(['data' => 'ptmp_fecha', 'name' => 'ptmp_fecha', 'title' => 'Fecha'])
            ->addColumn(['data' => 'ptmp_vigencia_inicial', 'name' => 'ptmp_vigencia_inicial', 'title' => 'Inicio'])
            ->addColumn(['data' => 'ptmp_vigencia_final', 'name' => 'ptmp_vigencia_final', 'title' => 'Fin'])
            ->addColumn(['data' => 'ptmp_estado', 'name' => 'ptmp_estado', 'title' => 'Estado'])
//            ->addColumn(['data' => 'ptmp_estado_extemporaneo', 'name' => 'ptmp_estado_extemporaneo', 'title' => 'Estado Extemporáneo' ,'width'=>'10%'])
//            ->addColumn(['data' => 'ptmp_comentario', 'name' => 'ptmp_comentario', 'title' => 'Comentario' ])
//            ->addColumn(['data' => 'gfpi_numero', 'name' => 'gfpi_numero', 'title' => 'Gafete' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');


        return view('web.permiso-temporal.index-vencidos', compact('dataTable', 'locales', 'dias_vencidos'));

    }

    public function recibirExtemporaneoView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-recibir-extemporaneo');

        return view('web.permiso-temporal.recibir', compact('permiso', 'url'));
    }

    public function pagarExtemporaneoView(PermisoTemporal $permiso)
    {

        $url = url('permiso-temporal/do-pagar-extemporaneo');

        $comprobantes = ['' => 'Recuperando comprobantes capturados...'];

        $saldos = $permiso->Local->getSaldos();
//        dd($saldos);

        return view('web.permiso-temporal.pagar-extemporaneo', compact('permiso', 'url', 'comprobantes', 'saldos'));
    }

    public function doRecibirExtemporaneo(Request $request)
    {
        if (!$this->validateAction('do-recibir')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                if (!in_array($permiso->ptmp_estado, ['ENTREGADO', 'VENCIDO'])) {
                    return response()->json($this->ajaxResponse(false, 'EL ESTADO DEL PERMISO NO PERMITE ESTA ACCIÓN'));
                }

                $permiso->ptmp_estado = 'CONCLUIDO';

                $permiso->ptmp_estado_extemporaneo = 'DEVUELTO';
                $permiso->ptmp_fecha_resolucion_extemporanea = date('Y-m-d H:i:s');

                $permiso->save();

                //liberamos el gafete preimpreso
                $g_preimpreso = $permiso->GafetePreimpreso;
                $g_preimpreso->gfpi_permiso_actual = null;
                $g_preimpreso->save();

                \DB::commit();
                return response()->json($this->ajaxResponse(true, "Permiso <b>CONCLUIDO</b> correctamente."));

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }


        }


    }

    public function doPagarExtemporaneo(Request $request)
    {
        if (!$this->validateAction('do-pagar-extemporaneo')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $permiso = PermisoTemporal::findOrFail($this->data['ptmp_id']);

                if (!in_array($permiso->ptmp_estado, ['ENTREGADO', 'VENCIDO'])) {
                    return response()->json($this->ajaxResponse(false, 'EL ESTADO DEL PERMISO NO PERMITE ESTA ACCIÓN'));
                }

                $tarifa = settings()->get('gft_tarifa_2');

                $permiso->ptmp_estado = 'CONCLUIDO';
                $permiso->ptmp_estado_extemporaneo = 'PAGADO';

                $permiso->ptmp_costo = $tarifa;
                $permiso->ptmp_fecha_resolucion_extemporanea = date('Y-m-d H:i:s');
                $permiso->save();

                //desactivamos la tarjeta
                $gafete = $permiso->GafetePreimpreso;
                $gafeteRfid = $gafete->getVGafeteRfid();

                $desactivar = new DesactivarTarjeta($gafeteRfid);
                $res = $desactivar->execute();
                if ($res == false) {
                    return response()->json($this->ajaxResponse(false, 'Ocurrió un error al desactivar la tarjeta en la controladora.', $res));
                }

                // ----------------------------------------------------------
                //No liberamos porque si pagaron es porque se extravió
//                $g_preimpreso = $permiso->GafetePreimpreso;
//                $g_preimpreso->gfpi_permiso_actual = null;
//                $g_preimpreso ->save();

                $response_message = "Permiso <b>CONCLUIDO</b> correctamente.";
                $response_data = [];

                try {
                    // N o t i f i c a ci o n -------------------------------------------------------------
                    $locatarios = User::role('LOCATARIO')
                        ->whereUsrLcalId($permiso->ptmp_lcal_id)
                        ->get();

                    \Notification::send($locatarios, new PermisoTemporalCobrado($permiso));
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

    /////////////////////////////////////////////////////////////////////////////////////

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
