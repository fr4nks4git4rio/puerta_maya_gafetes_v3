<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CMetodoPago;
use App\TipoComprobante;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class TipoComprobanteController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'descripcion' => 'required',
        ],

        'edit' => [
            'id' => 'required|exists:tipos_comprobante,id',
            'codigo' => 'required',
            'descripcion' => 'required',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'codigo' => 'Código',
        'descripcion' => 'Descripción'
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
                TipoComprobante::select(['id', 'codigo', 'descripcion'])
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
            ->addColumn(['data' => 'descripcion', 'name' => 'descripcion', 'title' => 'Descripción', 'search' => true]);


        return view('web.tipos_comprobante.index', compact('dataTable'));

    }

    public function form(TipoComprobante $tipo_comprobante = null, Request $request)
    {

        $url = ($tipo_comprobante == null) ? url('local/conta/tipos_comprobante/insert') : url('local/conta/tipos_comprobante/edit', $tipo_comprobante->getKey());

        return view('web.tipos_comprobante.form', compact('tipo_comprobante', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                TipoComprobante::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Tipo de Comprobante <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(TipoComprobante $tipo_comprobante, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $tipo_comprobante->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Tipo de Comprobante <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(TipoComprobante $tipo_comprobante, Request $request)
    {

        \DB::beginTransaction();
        try {
            $tipo_comprobante->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Tipo de Comprobante <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(TipoComprobante $tipo_comprobante)
    {
        return response()->json($tipo_comprobante);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = TipoComprobante::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
