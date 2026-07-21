<?php

namespace App\Http\Controllers;

use App\MedidaControlRiesgo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class MedidaControlRiesgoController extends Controller
{

    protected $rules = [
        'insert' => [
            'medcr_nombre' => ['required'],
            'medcr_comentarios' => 'nullable',
        ],

        'edit' => [
            'medcr_id' => 'required|exists:medidas_control_riesgo,medcr_id',
            'medcr_nombre' => ['required'],
            'medcr_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'medcr_id' => 'Id',
        'medcr_nombre' => 'Nombre',
        'medcr_comentarios' => 'Comentarios'
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
                MedidaControlRiesgo::select(['medcr_id', 'medcr_nombre', 'medcr_comentarios'])->orderBy('medcr_nombre')
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
            ->addColumn(['data' => 'medcr_id', 'name' => 'medcr_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'medcr_nombre', 'name' => 'medcr_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'medcr_comentarios', 'name' => 'medcr_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.medidas-control-riesgo.index', compact('dataTable'));
    }

    public function form(MedidaControlRiesgo $medidaControlRiesgo = null, Request $request)
    {

        $url = ($medidaControlRiesgo == null) ? url('medidas-control-riesgo/insert') : url('medidas-control-riesgo/edit', $medidaControlRiesgo->getKey());

        return view('web.medidas-control-riesgo.form', compact('medidaControlRiesgo', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['medcr_nombre'][] = Rule::unique('medidas_control_riesgo', 'medcr_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                MedidaControlRiesgo::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Medida de Control de Riesgo <b>CREADA</b> correctamente.'));
        }
    }

    public function edit(MedidaControlRiesgo $medidaControlRiesgo, Request $request)
    {
        $this->rules['edit']['medcr_nombre'][] = Rule::unique('medidas_control_riesgo', 'medcr_nombre')->ignore($medidaControlRiesgo->getKey(), 'medcr_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $medidaControlRiesgo->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Medidas de Control de Riesgo <b>EDITADA</b> correctamente."));
        }
    }

    public function delete(MedidaControlRiesgo $medidaControlRiesgo, Request $request)
    {

        DB::beginTransaction();
        try {
            $medidaControlRiesgo->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Medida de Control de Riesgo <b>ELIMIADA</b> correctamente."));
    }

    public function getJSON(MedidaControlRiesgo $medidaControlRiesgo)
    {
        return response()->json($medidaControlRiesgo);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = MedidaControlRiesgo::select(DB::raw('medcr_id as id, medcr_nombre as text'))
            ->where('medcr_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
