<?php

namespace App\Http\Controllers;

use App\Banco;
use App\EppEspecifico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class EppEspecificoController extends Controller
{

    protected $rules = [
        'insert' => [
            'eppe_nombre' => ['required'],
            'eppe_comentarios' => 'nullable',
        ],

        'edit' => [
            'eppe_id' => 'required|exists:epp_especificos,eppe_id',
            'eppe_nombre' => ['required'],
            'eppe_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'eppe_id' => 'Id',
        'eppe_nombre' => 'Nombre',
        'eppe_comentarios' => 'Comentarios'
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
                EppEspecifico::select(['eppe_id', 'eppe_nombre', 'eppe_comentarios'])->orderBy('eppe_nombre')
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
            ->addColumn(['data' => 'eppe_id', 'name' => 'eppe_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'eppe_nombre', 'name' => 'eppe_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'eppe_comentarios', 'name' => 'eppe_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.epp-especificos.index', compact('dataTable'));
    }

    public function form(EppEspecifico $eppEspecifico = null, Request $request)
    {

        $url = ($eppEspecifico == null) ? url('epp-especificos/insert') : url('epp-especificos/edit', $eppEspecifico->getKey());

        return view('web.epp-especificos.form', compact('eppEspecifico', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['eppe_nombre'][] = Rule::unique('epp_especificos', 'eppe_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                EppEspecifico::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'EPP Específico <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(EppEspecifico $eppEspecifico, Request $request)
    {
        $this->rules['edit']['eppe_nombre'][] = Rule::unique('epp_especificos', 'eppe_nombre')->ignore($eppEspecifico->getKey(), 'eppe_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $eppEspecifico->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "EPP Específico <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(EppEspecifico $eppEspecifico, Request $request)
    {

        DB::beginTransaction();
        try {
            $eppEspecifico->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "EPP Específico <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(EppEspecifico $eppEspecifico)
    {
        return response()->json($eppEspecifico);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = EppEspecifico::select(DB::raw('eppe_id as id, eppe_nombre as text'))
            ->where('eppe_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
