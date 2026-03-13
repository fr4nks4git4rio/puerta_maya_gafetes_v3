<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CUsoCfdi;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class CfdiController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'descripcion' => 'required',
        ],

        'edit' => [
            'id' => 'required|exists:c_usocfdi,id',
            'codigo' => 'required',
            'descripcion' => 'required',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'codigo' => 'Código CFDI',
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
                CUsoCfdi::select(['id', 'codigo', 'descripcion'])->orderBy('codigo')->where('activo', 1)
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


        return view('web.cfdis.index', compact('dataTable'));

    }

    public function form(CUsoCfdi $cfdi = null, Request $request)
    {

        $url = ($cfdi == null) ? url('local/conta/cfdis/insert') : url('local/conta/cfdis/edit', $cfdi->getKey());

        return view('web.cfdis.form', compact('cfdi', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CUsoCfdi::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Cfdi <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(CUsoCfdi $cfdi, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $cfdi->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Cfdi <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(CUsoCfdi $cfdi, Request $request)
    {

        \DB::beginTransaction();
        try {
            $cfdi->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Cfdi <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(CUsoCfdi $cfdi)
    {
        return response()->json($cfdi);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CUsoCfdi::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
