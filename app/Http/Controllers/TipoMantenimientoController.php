<?php

namespace App\Http\Controllers;

use App\Banco;
use Illuminate\Http\Request;
use App\TipoMantenimiento;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class TipoMantenimientoController extends Controller
{

    protected $rules = [
        'insert' => [
            'tmtt_nombre' => ['required'],
            'tmtt_comentarios' => 'nullable',
        ],

        'edit' => [
            'tmtt_id' => 'required|exists:tipos_mantenimiento,tmtt_id',
            'tmtt_nombre' => ['required'],
            'tmtt_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'tmtt_id' => 'Id',
        'tmtt_nombre' => 'Nombre',
        'tmtt_comentarios' => 'Comentarios'
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
                TipoMantenimiento::select(['tmtt_id', 'tmtt_nombre', 'tmtt_comentarios'])->orderBy('tmtt_nombre')
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
            ->addColumn(['data' => 'tmtt_id', 'name' => 'tmtt_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'tmtt_nombre', 'name' => 'tmtt_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'tmtt_comentarios', 'name' => 'tmtt_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.tipos-mantenimiento.index', compact('dataTable'));
    }

    public function form(TipoMantenimiento $tipoMantenimiento = null, Request $request)
    {

        $url = ($tipoMantenimiento == null) ? url('tipos-mantenimiento/insert') : url('tipos-mantenimiento/edit', $tipoMantenimiento->getKey());

        return view('web.tipos-mantenimiento.form', compact('tipoMantenimiento', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['tmtt_nombre'][] = Rule::unique('tipos_mantenimiento', 'tmtt_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                TipoMantenimiento::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Tipo de Mantenimiento <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(TipoMantenimiento $tipoMantenimiento, Request $request)
    {
        $this->rules['edit']['tmtt_nombre'][] = Rule::unique('tipos_mantenimiento', 'tmtt_nombre')->ignore($tipoMantenimiento->getKey(), 'tmtt_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $tipoMantenimiento->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Tipo de Mantenimiento <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(TipoMantenimiento $tipoMantenimiento, Request $request)
    {

        DB::beginTransaction();
        try {
            $tipoMantenimiento->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Tipo de Mantenimiento <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(TipoMantenimiento $tipoMantenimiento)
    {
        return response()->json($tipoMantenimiento);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = TipoMantenimiento::select(DB::raw('tmtt_id as id, tmtt_nombre as text'))
            ->where('tmtt_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
