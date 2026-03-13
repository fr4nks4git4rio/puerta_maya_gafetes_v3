<?php

namespace App\Http\Controllers;

use App\Banco;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class BancoController extends Controller
{

    protected $rules = [
        'insert' => [
            'nombre' => 'required',
            'rfc' => 'required',
        ],

        'edit' => [
            'id' => 'required|exists:bancos,id',
            'nombre' => 'required',
            'rfc' => 'required',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'nombre' => 'Nombre Banco',
        'rfc' => 'RFC'
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
                Banco::select(['id', 'nombre', 'rfc'])
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
            ->addColumn(['data' => 'nombre', 'name' => 'nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'rfc', 'name' => 'rfc', 'title' => 'RFC', 'search' => true]);


        return view('web.bancos.index', compact('dataTable'));

    }

    public function form(Banco $banco = null, Request $request)
    {

        $url = ($banco == null) ? url('local/conta/bancos/insert') : url('local/conta/bancos/edit', $banco->getKey());

        return view('web.bancos.form', compact('banco', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                Banco::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Banco <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(Banco $banco, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $banco->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Banco <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(Banco $banco, Request $request)
    {

        \DB::beginTransaction();
        try {
            $banco->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Banco <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(Banco $banco)
    {
        return response()->json($banco);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = Banco::select(\DB::raw('id, nombre as text'))
            ->where('nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
