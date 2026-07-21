<?php

namespace App\Http\Controllers;

use App\RiesgoAsociado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class RiesgoAsociadoController extends Controller
{

    protected $rules = [
        'insert' => [
            'rasoc_nombre' => ['required'],
            'rasoc_comentarios' => 'nullable',
        ],

        'edit' => [
            'rasoc_id' => 'required|exists:riesgos_asociados,rasoc_id',
            'rasoc_nombre' => ['required'],
            'rasoc_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'rasoc_id' => 'Id',
        'rasoc_nombre' => 'Nombre',
        'rasoc_comentarios' => 'Comentarios'
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
                RiesgoAsociado::select(['rasoc_id', 'rasoc_nombre', 'rasoc_comentarios'])->orderBy('rasoc_nombre')
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
            ->addColumn(['data' => 'rasoc_id', 'name' => 'rasoc_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'rasoc_nombre', 'name' => 'rasoc_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'rasoc_comentarios', 'name' => 'rasoc_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.riesgos-asociados.index', compact('dataTable'));
    }

    public function form(RiesgoAsociado $riesgoAsociado = null, Request $request)
    {

        $url = ($riesgoAsociado == null) ? url('riesgos-asociados/insert') : url('riesgos-asociados/edit', $riesgoAsociado->getKey());

        return view('web.riesgos-asociados.form', compact('riesgoAsociado', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['rasoc_nombre'][] = Rule::unique('riesgos_asociados', 'rasoc_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                RiesgoAsociado::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Riesgo Asociado <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(RiesgoAsociado $riesgoAsociado, Request $request)
    {
        $this->rules['edit']['rasoc_nombre'][] = Rule::unique('riesgos_asociados', 'rasoc_nombre')->ignore($riesgoAsociado->getKey(), 'rasoc_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $riesgoAsociado->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Riesgo Asociado <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(RiesgoAsociado $riesgoAsociado, Request $request)
    {

        DB::beginTransaction();
        try {
            $riesgoAsociado->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Riesgo Asociado <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(RiesgoAsociado $riesgoAsociado)
    {
        return response()->json($riesgoAsociado);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = RiesgoAsociado::select(DB::raw('rasoc_id as id, rasoc_nombre as text'))
            ->where('rasoc_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
