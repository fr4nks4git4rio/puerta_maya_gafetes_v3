<?php

namespace App\Http\Controllers;

use App\Banco;
use App\EppBasico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class EppBasicoController extends Controller
{

    protected $rules = [
        'insert' => [
            'eppb_nombre' => ['required'],
            'eppb_comentarios' => 'nullable',
        ],

        'edit' => [
            'eppb_id' => 'required|exists:epp_basicos,eppb_id',
            'eppb_nombre' => ['required'],
            'eppb_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'eppb_id' => 'Id',
        'eppb_nombre' => 'Nombre',
        'eppb_comentarios' => 'Comentarios'
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
                EppBasico::select(['eppb_id', 'eppb_nombre', 'eppb_comentarios'])->orderBy('eppb_nombre')
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
            ->addColumn(['data' => 'eppb_id', 'name' => 'eppb_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'eppb_nombre', 'name' => 'eppb_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'eppb_comentarios', 'name' => 'eppb_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.epp-basicos.index', compact('dataTable'));
    }

    public function form(EppBasico $eppBasico = null, Request $request)
    {

        $url = ($eppBasico == null) ? url('epp-basicos/insert') : url('epp-basicos/edit', $eppBasico->getKey());

        return view('web.epp-basicos.form', compact('eppBasico', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['eppb_nombre'][] = Rule::unique('epp_basicos', 'eppb_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                EppBasico::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'EPP Básico <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(EppBasico $eppBasico, Request $request)
    {
        $this->rules['edit']['eppb_nombre'][] = Rule::unique('epp_basicos', 'eppb_nombre')->ignore($eppBasico->getKey(), 'eppb_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $eppBasico->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "EPP Básico <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(EppBasico $eppBasico, Request $request)
    {

        DB::beginTransaction();
        try {
            $eppBasico->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "EPP Básico <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(EppBasico $eppBasico)
    {
        return response()->json($eppBasico);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = EppBasico::select(DB::raw('eppb_id as id, eppb_nombre as text'))
            ->where('eppb_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
