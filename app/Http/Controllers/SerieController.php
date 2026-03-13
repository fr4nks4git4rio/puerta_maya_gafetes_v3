<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CFormaPago;
use App\CRegimenFiscal;
use App\CSerie;
use App\CUsoCfdi;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class SerieController extends Controller
{

    protected $rules = [
        'insert' => [
            'descripcion' => 'required'
        ],

        'edit' => [
            'id' => 'required|exists:c_serie,id',
            'descripcion' => 'required'
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
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
                CSerie::select(['id', 'descripcion'])
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
            ->addColumn(['data' => 'descripcion', 'name' => 'descripcion', 'title' => 'Descripción', 'search' => true]);


        return view('web.series.index', compact('dataTable'));

    }

    public function form(CSerie $serie = null, Request $request)
    {

        $url = ($serie == null) ? url('local/conta/series/insert') : url('local/conta/series/edit', $serie->getKey());

        return view('web.series.form', compact('serie', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CSerie::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Serie <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(CSerie $serie, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $serie->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Serie <b>EDITADA</b> correctamente."));
        }

    }

    public function delete(CSerie $serie, Request $request)
    {

        \DB::beginTransaction();
        try {
            $serie->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Serie <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(CSerie $serie)
    {
        return response()->json($serie);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CSerie::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
