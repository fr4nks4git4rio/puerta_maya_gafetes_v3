<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CFormaPago;
use App\CRegimenFiscal;
use App\CUsoCfdi;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class RegimenFiscalController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'descripcion' => 'required'
        ],

        'edit' => [
            'id' => 'required|exists:c_regimenfiscal,id',
            'codigo' => 'required',
            'descripcion' => 'required'
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
                CRegimenFiscal::select(['id', 'codigo', 'descripcion'])->orderBy('codigo')
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


        return view('web.regimenes_fiscales.index', compact('dataTable'));

    }

    public function form(CRegimenFiscal $regimen_fiscal = null, Request $request)
    {

        $url = ($regimen_fiscal == null) ? url('local/conta/regimenes_fiscales/insert') : url('local/conta/regimenes_fiscales/edit', $regimen_fiscal->getKey());

        return view('web.regimenes_fiscales.form', compact('regimen_fiscal', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CRegimenFiscal::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Régimen Fiscal <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(CRegimenFiscal $regimen_fiscal, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $regimen_fiscal->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Régimen Fiscal <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(CRegimenFiscal $regimen_fiscal, Request $request)
    {

        \DB::beginTransaction();
        try {
            $regimen_fiscal->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Régimen Fiscal <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(CRegimenFiscal $regimen_fiscal)
    {
        return response()->json($regimen_fiscal);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CRegimenFiscal::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
