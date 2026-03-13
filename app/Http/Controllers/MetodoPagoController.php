<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CMetodoPago;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class MetodoPagoController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'descripcion' => 'required',
        ],

        'edit' => [
            'id' => 'required|exists:c_metodopago,id',
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
                CMetodoPago::select(['id', 'codigo', 'descripcion'])->where('activo', 1)
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


        return view('web.metodos_pago.index', compact('dataTable'));

    }

    public function form(CMetodoPago $metodo_pago = null, Request $request)
    {

        $url = ($metodo_pago == null) ? url('local/conta/metodos_pago/insert') : url('local/conta/metodos_pago/edit', $metodo_pago->getKey());

        return view('web.metodos_pago.form', compact('metodo_pago', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CMetodoPago::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Método de Pago <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(CMetodoPago $metodo_pago, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $metodo_pago->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Método de Pago <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(CMetodoPago $metodo_pago, Request $request)
    {

        \DB::beginTransaction();
        try {
            $metodo_pago->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Método de Pago <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(CMetodoPago $metodo_pago)
    {
        return response()->json($metodo_pago);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CMetodoPago::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
