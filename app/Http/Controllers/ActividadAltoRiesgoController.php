<?php

namespace App\Http\Controllers;

use App\ActividadAltoRiesgo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class ActividadAltoRiesgoController extends Controller
{

    protected $rules = [
        'insert' => [
            'actar_nombre' => ['required'],
            'actar_comentarios' => 'nullable',
        ],

        'edit' => [
            'actar_id' => 'required|exists:actividades_alto_riesgo,actar_id',
            'actar_nombre' => ['required'],
            'actar_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'actar_id' => 'Id',
        'actar_nombre' => 'Nombre',
        'actar_comentarios' => 'Comentarios'
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
                ActividadAltoRiesgo::select(['actar_id', 'actar_nombre', 'actar_comentarios'])->orderBy('actar_nombre')
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
            ->addColumn(['data' => 'actar_id', 'name' => 'actar_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'actar_nombre', 'name' => 'actar_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'actar_comentarios', 'name' => 'actar_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.actividades-alto-riesgo.index', compact('dataTable'));
    }

    public function form(ActividadAltoRiesgo $actividadAltoRiesgo = null, Request $request)
    {

        $url = ($actividadAltoRiesgo == null) ? url('actividades-alto-riesgo/insert') : url('actividades-alto-riesgo/edit', $actividadAltoRiesgo->getKey());

        return view('web.actividades-alto-riesgo.form', compact('actividadAltoRiesgo', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['actar_nombre'][] = Rule::unique('actividades_alto_riesgo', 'actar_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                ActividadAltoRiesgo::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Actividad de Alto Riesgo <b>CREADA</b> correctamente.'));
        }
    }

    public function edit(ActividadAltoRiesgo $actividadAltoRiesgo, Request $request)
    {
        $this->rules['edit']['actar_nombre'][] = Rule::unique('actividades_alto_riesgo', 'actar_nombre')->ignore($actividadAltoRiesgo->getKey(), 'actar_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $actividadAltoRiesgo->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Actividad de Alto Riesgo <b>EDITADA</b> correctamente."));
        }
    }

    public function delete(ActividadAltoRiesgo $actividadAltoRiesgo, Request $request)
    {

        DB::beginTransaction();
        try {
            $actividadAltoRiesgo->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Actividad de Alto Riesgo <b>ELIMIADA</b> correctamente."));
    }

    public function getJSON(ActividadAltoRiesgo $actividadAltoRiesgo)
    {
        return response()->json($actividadAltoRiesgo);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = ActividadAltoRiesgo::select(DB::raw('actar_id as id, actar_nombre as text'))
            ->where('actar_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
