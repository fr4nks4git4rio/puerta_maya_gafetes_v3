<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CMetodoPago;
use App\CUnidad;
use App\TipoComprobante;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class ClaveUnidadController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'nota' => 'nullable',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
        ],

        'edit' => [
            'id' => 'required|exists:c_claveunidad,id',
            'codigo' => 'required',
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'nota' => 'nullable',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'codigo' => 'Código',
        'nombre' => 'Nombre',
        'descripcion' => 'Descripción',
        'nota' => 'Nota',
        'fecha_inicio' => 'Fecha de Inicio',
        'fecha_fin' => 'Fecha de Fin',
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
            return Datatables::of(
                CUnidad::select(['id', 'codigo', 'nombre', 'descripcion'])
            )->make(true);
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
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'codigo', 'name' => 'codigo', 'title' => 'Código', 'search' => true])
            ->addColumn(['data' => 'nombre', 'name' => 'nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'descripcion', 'name' => 'descripcion', 'title' => 'Descripción', 'search' => true]);


        return view('web.claves_unidad.index', compact('dataTable'));

    }

    public function form(CUnidad $clave_unidad = null, Request $request)
    {

        $url = ($clave_unidad == null) ? url('local/conta/claves_unidades/insert') : url('local/conta/claves_unidades/edit', $clave_unidad->getKey());

        return view('web.claves_unidad.form', compact('clave_unidad', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CUnidad::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Clave de Unidad <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(CUnidad $clave_unidad, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $clave_unidad->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Clave de Unidad <b>EDITADA</b> correctamente."));
        }

    }

    public function delete(CUnidad $clave_unidad, Request $request)
    {

        \DB::beginTransaction();
        try {
            $clave_unidad->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Clave de Unidad <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(CUnidad $clave_unidad)
    {
        return response()->json($clave_unidad);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CUnidad::select(\DB::raw('id, nombre as text'))
            ->where('nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
