<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CUsoCfdi;
use App\DisennoGafete;
use App\DisennoGafetePaquete;
use App\DisennoGafetePaqueteVigencia;
use App\DisennoGafeteVigencia;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class DisennoGafeteController extends Controller
{

    protected $rules = [
        'insert' => [
            'nombre' => 'required',
            'cat_acceso_id' => 'required|exists:cat_accesos,cacs_id',
        ],

        'edit' => [
            'id' => 'required|exists:disennos_gafetes,id',
            'nombre' => 'required',
            'cat_acceso_id' => 'required|exists:cat_accesos,cacs_id',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'nombre' => 'Nombre',
        'imagen_front' => 'Imagen Delantera',
        'imagen_back' => 'Imagen Trasera',
        'cat_acceso_id' => 'Categoría de Acceso',
    ];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                DisennoGafetePaquete::select(['dgp_id', 'dgp_nombre as nombre', 'dgp_seleccionado as seleccionado'])
            )
                ->editColumn('seleccionado', function (DisennoGafetePaquete $model) {
                    if ($model->seleccionado)
                        return '<div class="text-center"><i class="fa fa-check-circle-o text-success fa-4x"></i></div>';
                    return '';
                })
                ->editColumn('imagenes', function (DisennoGafetePaquete $model) {
                    $imagenes = '';
                    foreach ($model->ImagenesPocas as $imagen)
                        $imagenes .= '<div class="col-sm-6 text-center">
                                        <img src="' . $imagen->src_imagen . '" class="thumb-xl img-thumbnail"/>                                     
                                    </div>';
                    return '<div class="row">' . $imagenes . '</div>';
                })
                ->rawColumns(['seleccionado', 'imagenes'])
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
            'order' => [[1, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'dgp_id', 'name' => 'dgp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'imagenes', 'name' => 'imagenes', 'title' => 'Imágenes', 'searchable' => false, 'orderable' => false])
            ->addColumn(['data' => 'nombre', 'name' => 'nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'seleccionado', 'name' => 'seleccionado', 'title' => 'Seleccionado', 'search' => true, 'class' => 'align-middle']);

//        $cantidadGafetesSeleccionados = $this->cantidadGafetesSeleccionados();

        return view('web.disennos-gafete.index', compact('dataTable'));

    }

    public function form(DisennoGafete $disenno_gafete = null, Request $request)
    {
        $url = ($disenno_gafete == null) ? url('disenno_gafetes/insert') : url('disenno_gafetes/edit', $disenno_gafete->getKey());

        $cat_accesos = CatAcceso::selectRaw('cacs_id , cacs_descripcion')
            ->get()
            ->pluck('cacs_descripcion', 'cacs_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.disennos-gafete.form', compact('disenno_gafete', 'url', 'cat_accesos'));

    }

    public function insert(Request $request)
    {
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            if (!$request->hasFile('imagen_front') || !$request->hasFile('imagen_back')) {
                return response()->json($this->ajaxResponse(false, 'Debe seleccionar una Imagen Delantera y una Imagen Trasera!'));
            }

            \DB::beginTransaction();
            try {
                $disenno = DisennoGafete::create($this->data);
                $disenno->imagen_front = $request->file('imagen_front')
                    ->storeAs('', $disenno->getKey() . "_Front." . $request->file('imagen_front')->extension(), 'disennos_gafetes');
                $disenno->imagen_back = $request->file('imagen_back')
                    ->storeAs('', $disenno->getKey() . "_Back." . $request->file('imagen_back')->extension(), 'disennos_gafetes');
                $disenno->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Diseño de Gafete <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(DisennoGafete $disenno_gafete, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                if ($request->hasFile('imagen_front')) {
                    Storage::disk('disennos_gafetes')->delete($disenno_gafete->imagen_front);
                    $this->data['imagen_front'] = $request->file('imagen_front')
                        ->storeAs('', $disenno_gafete->getKey() . "_Front." . $request->file('imagen_front')->extension(), 'disennos_gafetes');
                }
                if ($request->hasFile('imagen_back')) {
                    Storage::disk('disennos_gafetes')->delete($disenno_gafete->imagen_back);
                    $this->data['imagen_back'] = $request->file('imagen_back')
                        ->storeAs('', $disenno_gafete->getKey() . "_Back." . $request->file('imagen_back')->extension(), 'disennos_gafetes');
                }
                $disenno_gafete->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Diseño de Gafete <b>EDITADO</b> correctamente."));
        }

    }

    public function formSeleccionar(DisennoGafetePaquete $paquete_disenno_gafete)
    {
        $url = url('disenno_gafetes/seleccionar_paquete');
        $disennos = $paquete_disenno_gafete->Imagenes()->whereHas('CatAcceso')->get();

//        dd($disennos);
        return view('web.disennos-gafete.seleccionar-paquete', compact('url', 'disennos', 'paquete_disenno_gafete'));

    }

    public function seleccionarPaquete(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'dgpv_fecha_inicio' => ['required', 'date'],
            'dgpv_anno' => ['required', 'date_format:Y'],
            'dgpv_paquete_id' => 'required|exists:disennos_gafetes_paquetes,dgp_id',
            'dgpv_comentarios' => 'nullable'
        ], [
            'dgpv_fecha_inicio.required' => 'La Fecha de Inicio es requerida!',
            'dgpv_fecha_inicio.date' => 'El campo Fecha de Inicio debe ser una fecha válida!',
            'dgpv_anno.required' => 'El Año es requerido!',
            'dgpv_anno.date_format' => 'El campo Año debe ser un año válido!',
            'dgpv_paquete_id.required' => 'El Paquete es requerido',
            'dgpv_paquete_id.exists' => 'El Paquete no es reconocido'
        ]);

        if ($validate->fails()) {
            return response()->json($this->ajaxResponse(false, $validate->errors()->first()));
        }

        $data = $validate->validate();
        $paquete = DisennoGafetePaquete::find($data['dgpv_paquete_id']);

        if ($paquete->dgp_seleccionado)
            return response()->json($this->ajaxResponse(false, "Un mismo Paquete no puede ser seleccionado dos veces de forma consecutiva!"));

        \DB::beginTransaction();
        try {
            DisennoGafetePaquete::where('dgp_seleccionado', 1)->update(['dgp_seleccionado' => 0]);

            DisennoGafetePaqueteVigencia::create($data);
            $paquete->dgp_seleccionado = 1;
            $paquete->save();

            settings()->set('fecha_inicio_impresion', $data['dgpv_fecha_inicio']);
            settings()->save();

            DisennoGafetePaqueteVigencia::checkFechaIncioImpresion();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Paquete <b>SELECCIONADO</b> correctamente."));

    }

    public function formTextoBackLocales()
    {
        $url = url('disenno_gafetes/guardar_texto_back_locales');

        return view('web.disennos-gafete.texto-back-gafetes',['texto' => settings()->get('gft_back_text'), 'url' => $url]);
    }

    public function guardarTextoBackLocales(Request $request){
        \DB::beginTransaction();

        try {

            settings()->set('gft_back_text', $request->texto_back_gafetes);
            settings()->save();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Texto <b>GUARDADO</b> correctamente."));
    }

    public function formTextoBackAdmin()
    {
        $url = url('disenno_gafetes/guardar_texto_back_admin');

        return view('web.disennos-gafete.texto-back-gafetes',['texto' => settings()->get('gft_admin_back_text'), 'url' => $url]);
    }

    public function guardarTextoBackAdmin(Request $request){
        \DB::beginTransaction();

        try {

            settings()->set('gft_admin_back_text', $request->texto_back_gafetes);
            settings()->save();

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Texto <b>GUARDADO</b> correctamente."));
    }

    public function delete(DisennoGafete $disenno_gafete, Request $request)
    {

        \DB::beginTransaction();
        try {
            $disenno_gafete->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Diseño de Gafete <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(DisennoGafete $disenno_gafete)
    {
        return response()->json($disenno_gafete);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = DisennoGafete::select(\DB::raw('id, nombre as text'))
            ->where('nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }

    private function cantidadGafetesSeleccionados()
    {
        $vigencia_activa = DisennoGafeteVigencia::where('activo', 1)->get();
        $cantidad = 0;
        if ($vigencia_activa->count() > 0) {
            $vigencia_activa = $vigencia_activa->first();
            $cantidad = $vigencia_activa->disennos_gafetes->count();
        }

        return $cantidad;
    }

    private function cantidadDisennosGafetesSeleccionar()
    {
        return CatAcceso::all()->count();
    }

}
