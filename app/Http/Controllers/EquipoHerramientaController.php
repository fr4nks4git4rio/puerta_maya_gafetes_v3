<?php

namespace App\Http\Controllers;

use App\EquipoHerramienta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class EquipoHerramientaController extends Controller
{

    protected $rules = [
        'insert' => [
            'eqher_nombre' => ['required'],
            'eqher_comentarios' => 'nullable',
        ],

        'edit' => [
            'eqher_id' => 'required|exists:equipos_herramientas,eqher_id',
            'eqher_nombre' => ['required'],
            'eqher_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'eqher_id' => 'Id',
        'eqher_nombre' => 'Nombre',
        'eqher_comentarios' => 'Comentarios'
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
                EquipoHerramienta::select(['eqher_id', 'eqher_nombre', 'eqher_comentarios'])->orderBy('eqher_nombre')
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
            ->addColumn(['data' => 'eqher_id', 'name' => 'eqher_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'eqher_nombre', 'name' => 'eqher_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'eqher_comentarios', 'name' => 'eqher_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.equipos-herramientas.index', compact('dataTable'));
    }

    public function form(EquipoHerramienta $equipoHerramienta = null, Request $request)
    {

        $url = ($equipoHerramienta == null) ? url('equipos-herramientas/insert') : url('equipos-herramientas/edit', $equipoHerramienta->getKey());

        return view('web.equipos-herramientas.form', compact('equipoHerramienta', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['eqher_nombre'][] = Rule::unique('equipos_herramientas', 'eqher_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                EquipoHerramienta::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Equipo / Herramienta <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(EquipoHerramienta $equipoHerramienta, Request $request)
    {
        $this->rules['edit']['eqher_nombre'][] = Rule::unique('equipos_herramientas', 'eqher_nombre')->ignore($equipoHerramienta->getKey(), 'eqher_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $equipoHerramienta->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Equipo / Herramienta <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(EquipoHerramienta $equipoHerramienta, Request $request)
    {

        DB::beginTransaction();
        try {
            $equipoHerramienta->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Equipo / Herramienta <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(EquipoHerramienta $equipoHerramienta)
    {
        return response()->json($equipoHerramienta);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = EquipoHerramienta::select(DB::raw('eqher_id as id, eqher_nombre as text'))
            ->where('eqher_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
