<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjetaV2;
use App\Actions\CrearTarjetaV2;
use App\Actions\DesactivarTarjeta;
use App\Actions\DesactivarTarjetaV2;
use App\Actions\EliminarTarjetaV3;
use App\Controladora;
use App\SolicitudGafete;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;

use App\Clases\PhotoSaver;

use App\Empleado;
use App\CatCargo;
use App\Local;
use App\VGafetesRfidV3;

class EmpleadoController extends Controller
{

    protected $rules = [
        'insert' => [
            'empl_lcal_id' => 'required:exists:locales,lcal_id',
            'empl_nombre' => 'required',
            'empl_email' => 'nullable|email',
            'empl_crgo_id' => 'required|exists:cat_cargos,crgo_id',
            'empl_telefono' => 'nullable|numeric',
            'empl_foto' => 'nullable',
            'empl_nss' => 'nullable',
            'empl_vacunado' => 'nullable',
            'empl_cert_vacuna' => 'nullable',
            'empl_comentario' => 'nullable',
            'data_photo' => 'nullable|required',
        ],

        'edit' => [
            'empl_id' => 'required|exists:empleados,empl_id',
            'empl_lcal_id' => 'required:exists:locales,lcal_id',
            'empl_nombre' => 'required',
            'empl_email' => 'nullable|email',
            'empl_crgo_id' => 'required|exists:cat_cargos,crgo_id',
            'empl_telefono' => 'nullable|numeric',
            'empl_foto' => 'nullable',
            'empl_nss' => 'nullable',
            'empl_vacunado' => 'nullable',
            'empl_vacuna_validada' => 'nullable',
            'empl_cert_vacuna' => 'nullable',
            'empl_comentario' => 'nullable',
            'data_photo' => 'nullable'
        ],


    ];

    protected $etiquetas = [
        'empl_id' => 'Id',
        'empl_lcal_id' => 'Local',
        'empl_nombre' => 'Nombre',
        'empl_email' => 'Correo',
        'empl_crgo_id' => 'Cargo',
        'empl_telefono' => 'Telefono',
        'empl_foto' => 'Foto',
        'empl_comentario' => 'Comentario',
        'empl_vacunado' => 'Vacunado',
        'empl_vacuna_validada' => 'Vacuna Validada',
        'empl_cert_vacuna' => 'Certificado',
        'data_photo' => 'Fotografía'
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
        if (isset($this->data['empl_vacunado']) && $this->data['empl_vacunado'] === 'on')
            $this->data['empl_vacunado'] = 1;
        else {
            $this->data['empl_vacunado'] = 0;
            $this->data['empl_vacuna_validada'] = 0;
        }

        if (isset($this->data['empl_vacuna_validada']) && $this->data['empl_vacuna_validada'] === 'on')
            $this->data['empl_vacuna_validada'] = 1;
        else
            $this->data['empl_vacuna_validada'] = 0;
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
                Empleado::select([
                    'empl_id',
                    'empl_nombre',
                    'empl_crgo_id',
                    'empl_email',
                    'empl_telefono',
                    'empl_vacunado',
                    'crgo_descripcion',
                    'empl_thumb',
                    'sgftre_id',
                    'sgftre_permisos',
                    'sgftre_estado'
                ])
                    ->join('cat_cargos', 'empl_crgo_id', 'crgo_id')
                    ->leftJoin('solicitudes_gafetes_reasignar', 'empl_id', '=', 'sgftre_empl_id')
                    // ->whereNull('sgftre_deleted_at')
                    ->whereEmplLcalId($local->lcal_id)
                    ->groupBy('empl_id')
                    ->orderBy('empl_nombre')
            )
                ->editColumn('empl_thumb', function (Empleado $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->empl_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('sgftre_permisos', function (Empleado $model) {
                    if (!$model->GafeteReasignado) return '';

                    $color = 'badge-primary';
                    if ($model->GafeteReasignado->sgftre_estado == 'PENDIENTE') $color = 'badge-inverse';
                    if ($model->GafeteReasignado->sgftre_estado == 'ASIGNADO') $color = 'badge-success';
                    if ($model->GafeteReasignado->sgftre_estado == 'DENEGADO') $color = 'badge-warning';
                    if ($model->GafeteReasignado->sgftre_estado == 'CANCELADO') $color = 'badge-danger';
                    $html = '<div class="text-center">' . Str::replaceLast(',', ' y ', str_replace(['PEATONAL,', 'PEATONAL'], '', $model->GafeteReasignado->sgftre_permisos)) . ' <br><small class="badge ' . $color . '">' . $model->GafeteReasignado->sgftre_estado . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('empl_vacunado', function (Empleado $model) {
                    if ($model->empl_vacunado)
                        return '<div class="text-center"><span class="fa fa-check-circle-o text-success"></span></div>';
                    else
                        return '<div class="text-center"><span class="fa fa-remove text-danger"></span></div>';
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $sql = "crgo_descripcion  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('sgftre_permisos', function ($query, $keyword) {
                    $sql = "sgftre_permisos  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['empl_thumb', 'sgftre_permisos', 'empl_vacunado'])
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
            ->addColumn(['data' => 'empl_id', 'name' => 'empl_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'empl_thumb', 'name' => 'empl_thumb', 'title' => 'Foto', 'search' => false, 'width' => '5%'])
            ->addColumn(['data' => 'empl_nombre', 'name' => 'empl_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo'])
            ->addColumn(['data' => 'empl_email', 'name' => 'empl_email', 'title' => 'Correo', 'visible' => false])
            ->addColumn(['data' => 'sgftre_permisos', 'name' => 'sgftre_permisos', 'title' => 'Tipo de vehículo'])
            ->addColumn(['data' => 'empl_vacunado', 'name' => 'empl_vacunado', 'title' => 'Vacunado'])
            ->addColumn(['data' => 'empl_telefono', 'name' => 'empl_telefono', 'title' => 'Teléfono']);


        return view('web.empleado.index', compact('dataTable', 'local'));
    }


    public function indexAdmin(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = Empleado::select([
                'empl_id',
                'empl_nombre',
                'empl_crgo_id',
                'lcal_nombre_comercial',
                'empl_vacunado',
                'empl_vacuna_validada',
                'empl_email',
                'empl_telefono',
                'crgo_descripcion',
                'empl_thumb'
            ])
                ->join('cat_cargos', 'empl_crgo_id', 'crgo_id')
                ->join('locales', 'empl_lcal_id', 'lcal_id')
                ->orderBy('empl_nombre');


            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                // dd($filtro);
                $records->whereEmplLcalId($filtro);
            }

            return Datatables::of(
                $records
            )
                ->editColumn('empl_thumb', function (Empleado $model) {
                    return '<img class=" mx-auto d-block img-fluid" src="' . $model->empl_thumb_web . '" style="max-height:35px" />';
                })
                ->editColumn('empl_vacunado', function (Empleado $model) {
                    if ($model->empl_vacunado)
                        return '<div class="text-center"><span class="fa fa-check-circle-o text-success"></span></div>';
                    else
                        return '<div class="text-center"><span class="fa fa-remove text-danger"></span></div>';
                })
                ->editColumn('empl_vacuna_validada', function (Empleado $model) {
                    if ($model->empl_vacuna_validada)
                        return '<div class="text-center"><span class="fa fa-check-circle-o text-success"></span></div>';
                    else
                        return '<div class="text-center"><span class="fa fa-remove text-danger"></span></div>';
                })
                ->filterColumn('crgo_descripcion', function ($query, $keyword) {
                    $sql = "crgo_descripcion  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $sql = "lcal_nombre_comercial  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['empl_thumb', 'empl_vacunado', 'empl_vacuna_validada'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            // 'ajax'=>  url('empleado/admin'),
            // 'ajax'=>  ['data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'],
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('empleado/admin'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'empl_id', 'name' => 'empl_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local'])
            ->addColumn(['data' => 'empl_thumb', 'name' => 'empl_thumb', 'title' => 'Foto', 'search' => false, 'width' => '5%'])
            ->addColumn(['data' => 'empl_nombre', 'name' => 'empl_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo'])
            ->addColumn(['data' => 'empl_email', 'name' => 'empl_email', 'title' => 'Correo'])
            ->addColumn(['data' => 'empl_vacunado', 'name' => 'empl_vacunado', 'title' => 'Vacunado'])
            ->addColumn(['data' => 'empl_vacuna_validada', 'name' => 'empl_vacuna_validada', 'title' => 'Vacuna Validada'])
            ->addColumn(['data' => 'empl_telefono', 'name' => 'empl_telefono', 'title' => 'Teléfono']);


        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.empleado.index-admin', compact('dataTable', 'locales'));
    }

    public function formAdmin(Empleado $empleado = null, Request $request)
    {
        $url = ($empleado == null) ? url('empleado/insert-admin') : url('empleado/edit-admin', $empleado->getKey());

        $cargos = CatCargo::selectRaw('crgo_id , crgo_descripcion')
            ->get()
            ->pluck('crgo_descripcion', 'crgo_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');
        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.empleado.form-admin', compact('empleado', 'url', 'cargos', 'locales'));
    }

    public function form(Empleado $empleado = null, Request $request)
    {

        $url = ($empleado == null) ? url('empleado/insert') : url('empleado/edit', $empleado->getKey());

        $cargos = CatCargo::selectRaw('crgo_id , crgo_descripcion')
            ->get()
            ->pluck('crgo_descripcion', 'crgo_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.empleado.form', compact('empleado', 'url', 'cargos'));
    }

    public function insert(Request $request)
    {
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            if ($this->data['empl_vacunado'] && !$request->hasFile('empl_cert_vacuna')) {
                return response()->json($this->ajaxResponse(false, 'Debe subir el Certificado de Vacunación!'));
            }

            //            dd($this->data);

            DB::beginTransaction();
            try {
                $data = Arr::except($this->data, ['data_photo', 'empl_cert_vacuna']);

                $empl = DB::select("select empl_id from empleados where TRIM(UCASE(empl_nombre)) = '" . trim(Str::upper($data['empl_nombre'])) . "' and empl_deleted_at = null");

                if (count($empl) > 0)
                    return response()->json($this->ajaxResponse(false, 'Ya existe un trabajador con el nombre proporcionado!'));

                $empleado = Empleado::create($data);

                if ($request->empl_cert_vacuna) {
                    $empleado->empl_cert_vacuna = $request->file('empl_cert_vacuna')
                        ->storeAs('', $empleado->getKey() . "_Empleado." . $request->file('empl_cert_vacuna')->extension(), 'certificados_vacunacion');
                    $empleado->save();
                }

                if ($this->data['data_photo'] != "") {

                    $fileFoto = $empleado->empl_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = $empleado->empl_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {

                        $empleado->empl_foto = $fileFoto;
                        $empleado->empl_thumb = $fileThumb;
                        $empleado->save();
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Empleado <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(Empleado $empleado, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            if ($this->data['empl_vacunado'] && (!$empleado->empl_cert_vacuna && !$request->hasFile('empl_cert_vacuna'))) {
                return response()->json($this->ajaxResponse(false, 'Debe subir el Certificado de Vacunación!'));
            }

            DB::beginTransaction();

            try {
                if ($empleado->empl_foto == "" && $this->data['data_photo'] == "") {
                    return response()->json($this->ajaxResponse(false, 'Es necesaria la fotografía del empleado'));
                }

                $data = Arr::except($this->data, ['data_photo', 'empl_cert_vacuna']);
                if ($request->empl_vacunado) {
                    if ($request->hasFile('empl_cert_vacuna')) {
                        if ($empleado->empl_cert_vacuna && Storage::disk('certificados_vacunacion')->exists($empleado->empl_cert_vacuna)) {
                            Storage::disk('certificados_vacunacion')->delete($empleado->empl_cert_vacuna);
                        }
                        $data['empl_cert_vacuna'] = $request->file('empl_cert_vacuna')
                            ->storeAs('', $empleado->getKey() . "_Empleado." . $request->file('empl_cert_vacuna')->extension(), 'certificados_vacunacion');
                    }
                } else {
                    $data['empl_cert_vacuna'] = null;
                }
                $empleado->update($data);

                // ----------------

                if ($this->data['data_photo'] != "") {

                    $fileFoto = $empleado->empl_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = $empleado->empl_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {

                        $empleado->empl_foto = $fileFoto;
                        $empleado->empl_thumb = $fileThumb;
                        $empleado->save();
                    }
                }

                if ($empleado->GafeteAcceso()) {
                    //                    $controller = Controladora::controladoraAccesoPeatonal();
                    if ($empleado->GafeteAcceso()->is_active && ($empleado->empl_vacunado == 0 || $empleado->empl_vacuna_validada == 0)) {
                        foreach ($empleado->GafeteAcceso()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                            $pin_value = 0;

                            foreach ($doors as $door) {
                                $pin_value += $door->pin_value;
                            }

                            $controller = Controladora::findOrFail($key);

                            // desactivamos tarjeta v2
                            $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV2();
                            $activar = new DesactivarTarjetaV2($gafeteRfid, $controller, $pin_value);
                            $res = $activar->execute();

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al desactivar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                            }
                        }
                    } elseif ($empleado->GafeteAcceso()->is_disabled && ($empleado->empl_vacunado == 1 && $empleado->empl_vacuna_validada == 1)) {
                        foreach ($empleado->GafeteAcceso()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                            $pin_value = 0;

                            foreach ($doors as $door) {
                                $pin_value += $door->pin_value;
                            }

                            $controller = Controladora::findOrFail($key);

                            // activamos tarjeta v2
                            $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV2();
                            $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $pin_value);
                            $res = $activar->execute();

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                            }
                        }
                    }
                }
                // ----------------
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Empleado <b>EDITADO</b> correctamente."));
        }
    }

    public function insertAdmin(Request $request)
    {
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            if ($this->data['empl_vacunado'] && !$request->hasFile('empl_cert_vacuna')) {
                return response()->json($this->ajaxResponse(false, 'Debe subir el Certificado de Vacunación!'));
            }

            DB::beginTransaction();
            try {
                $data = Arr::except($this->data, ['data_photo', 'empl_cert_vacuna']);
                $data['empl_vacuna_validada'] = $request->empl_vacunado ? 1 : 0;
                $empl = DB::select("select empl_id from empleados where TRIM(UCASE(empl_nombre)) = '" . $data['empl_nombre'] . "'");
                if (count($empl) > 0)
                    return response()->json($this->ajaxResponse(false, 'Ya existe un trabajador con el nombre proporcionado!'));

                $empleado = Empleado::create($data);

                if ($request->empl_cert_vacuna) {
                    $empleado->empl_cert_vacuna = $request->file('empl_cert_vacuna')
                        ->storeAs('', $empleado->getKey() . "_Empleado." . $request->file('empl_cert_vacuna')->extension(), 'certificados_vacunacion');
                    $empleado->save();
                }

                if ($this->data['data_photo'] != "") {

                    $fileFoto = $empleado->empl_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = $empleado->empl_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {

                        $empleado->empl_foto = $fileFoto;
                        $empleado->empl_thumb = $fileThumb;
                        $empleado->save();
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage() . $e->getFile() . $e->getLine()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Empleado <b>CREADO</b> correctamente.'));
        }
    }

    public function editAdmin(Empleado $empleado, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {
            if ($this->data['empl_vacunado'] && (!$empleado->empl_cert_vacuna && !$request->hasFile('empl_cert_vacuna'))) {
                return response()->json($this->ajaxResponse(false, 'Debe subir el Certificado de Vacunación!'));
            }
            DB::beginTransaction();

            try {
                if ($empleado->empl_foto == "" && $this->data['data_photo'] == "") {
                    return response()->json($this->ajaxResponse(false, 'Es necesaria la fotografía del empleado'));
                }

                $data = Arr::except($this->data, ['data_photo', 'empl_cert_vacuna']);
                if ($request->empl_vacunado) {
                    if ($request->hasFile('empl_cert_vacuna')) {
                        if ($empleado->empl_cert_vacuna && Storage::disk('certificados_vacunacion')->exists($empleado->empl_cert_vacuna)) {
                            Storage::disk('certificados_vacunacion')->delete($empleado->empl_cert_vacuna);
                        }
                        $data['empl_cert_vacuna'] = $request->file('empl_cert_vacuna')
                            ->storeAs('', $empleado->getKey() . "_Empleado." . $request->file('empl_cert_vacuna')->extension(), 'certificados_vacunacion');
                    }
                } else {
                    $data['empl_cert_vacuna'] = null;
                }
                $empleado->update($data);

                // ----------------

                if ($this->data['data_photo'] != "") {

                    $fileFoto = $empleado->empl_id . '_' . date('YmdHis') . '.jpg';
                    $fileThumb = $empleado->empl_id . '_' . date('YmdHis') . '_sm.jpg';
                    $pathFoto = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileFoto;
                    $pathThumb = $this->system_folder_empleados . DIRECTORY_SEPARATOR . $fileThumb;

                    if (PhotoSaver::savePhoto($this->data['data_photo'], $pathFoto, $pathThumb) !== false) {

                        $empleado->empl_foto = $fileFoto;
                        $empleado->empl_thumb = $fileThumb;
                        $empleado->save();
                    }
                }

                if ($empleado->GafeteAcceso()) {
                    //                    $controller = Controladora::controladoraAccesoPeatonal();
                    if ($empleado->GafeteAcceso()->is_active && ($empleado->empl_vacunado == 0 || $empleado->empl_vacuna_validada == 0)) {
                        foreach ($empleado->GafeteAcceso()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                            $pin_value = 0;

                            foreach ($doors as $door) {
                                $pin_value += $door->pin_value;
                            }

                            $controller = Controladora::findOrFail($key);

                            // desactivamos tarjeta v2
                            $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV2();
                            $activar = new DesactivarTarjetaV2($gafeteRfid, $controller, $pin_value);
                            $res = $activar->execute();

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al desactivar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                            }
                        }
                    } elseif ($empleado->GafeteAcceso()->is_disabled && ($empleado->empl_vacunado == 1 && $empleado->empl_vacuna_validada == 1)) {
                        foreach ($empleado->GafeteAcceso()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                            $pin_value = 0;

                            foreach ($doors as $door) {
                                $pin_value += $door->pin_value;
                            }

                            $controller = Controladora::findOrFail($key);

                            // activamos tarjeta v2
                            $gafeteRfid = $empleado->GafeteAcceso()->getVGafeteRfidV2();
                            $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $pin_value);
                            $res = $activar->execute();

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                            }
                        }
                    }
                }
                // ----------------
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Empleado <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(Empleado $empleado, Request $request)
    {

        \DB::beginTransaction();
        try {
            $empleado->delete();

            //buscamos solicitudes en proceso para terminarlas
            $solicitudes = SolicitudGafete::whereSgftEmplId($empleado->empl_id)
                ->whereIn('sgft_estado', ['PENDIENTE', 'VALIDADA', 'IMPRESA'])
                ->get();
            foreach ($solicitudes as $solicitud) {
                $solicitud->sgft_comentario_admin = 'FIN DE PROCESO DE SOLICITUD POR BAJA DEL EMPLEADO';
                $solicitud->sgft_estado = ($solicitud->sgft_estado == 'PENDIENTE') ? 'CANCELADA' : 'ENTREGADA';
                $solicitud->save();
            }

            //desactivamos tarjetas
            $records = VGafetesRfidV3::whereEmplId($empleado->empl_id)
                ->whereNull('disabled_at')
                ->get();

            foreach ($records as $r) {
                $crear = new EliminarTarjetaV3($r);
                $res = $crear->execute();

                if ($res == false) {
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error intentando eliminar la tarjeta: {$r->getOriginalRecord()->sgft_numero}.", $res));
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Empleado <b>DADO DE BAJA</b> correctamente."));
    }

    public function getJSON(Empleado $empleado)
    {

        // $empleado->empl_foto_web = $empleado->empl_foto_web;
        return response()->json($empleado->load('Cargo'));
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

    public function getSolicitudesEnProceso(Empleado $empleado)
    {
        $recordCount = SolicitudGafete::whereIn('sgft_estado', ['PENDIENTE', 'VALIDADA', 'IMPRESA'])
            ->whereSgftEmplId($empleado->empl_id)
            ->count();


        return response()->json(['solicitudes_en_proceso' => $recordCount]);
    }

    ///////////////////////////////////////////////////////////////////////////


}
