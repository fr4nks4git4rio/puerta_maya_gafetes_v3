<?php

namespace App\Http\Controllers;

use App\TipoRiesgo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class TipoRiesgoController extends Controller
{

    protected $rules = [
        'insert' => [
            'trgo_nombre' => ['required'],
            'trgo_requiere_analisis' => ['nullable'],
            'trgo_requiere_doble_aprob' => ['nullable'],
            'trgo_comentarios' => 'nullable',
        ],

        'edit' => [
            'trgo_id' => 'required|exists:tipos_riesgo,trgo_id',
            'trgo_nombre' => ['required'],
            'trgo_requiere_analisis' => ['nullable'],
            'trgo_requiere_doble_aprob' => ['nullable'],
            'trgo_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'trgo_id' => 'Id',
        'trgo_nombre' => 'Nombre',
        'trgo_requiere_analisis' => 'Requiere análisis',
        'trgo_requiere_doble_aprob' => 'Requiere doble aprobación',
        'trgo_comentarios' => 'Comentarios'
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
        if (isset($this->data['trgo_requiere_analisis']) && $this->data['trgo_requiere_analisis'] === 'on')
            $this->data['trgo_requiere_analisis'] = 1;
        else {
            $this->data['trgo_requiere_analisis'] = 0;
        }
        if (isset($this->data['trgo_requiere_doble_aprob']) && $this->data['trgo_requiere_doble_aprob'] === 'on')
            $this->data['trgo_requiere_doble_aprob'] = 1;
        else {
            $this->data['trgo_requiere_doble_aprob'] = 0;
        }
    }


    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                TipoRiesgo::select(['trgo_id', 'trgo_nombre', 'trgo_requiere_analisis', 'trgo_requiere_doble_aprob', 'trgo_comentarios'])->orderBy('trgo_nombre')
            )
                ->editColumn('trgo_requiere_analisis', function (TipoRiesgo $model) {

                    $color = $model->trgo_requiere_analisis ? 'badge-danger' : 'badge-inverse';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . ($model->trgo_requiere_analisis ? 'SI' : 'NO') . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('trgo_requiere_doble_aprob', function (TipoRiesgo $model) {

                    $color = $model->trgo_requiere_doble_aprob ? 'badge-danger' : 'badge-inverse';

                    $html = '<div class="text-center"><small class="badge ' . $color . '">' . ($model->trgo_requiere_doble_aprob ? 'SI' : 'NO') . '</small>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['trgo_requiere_analisis', 'trgo_requiere_doble_aprob'])
                ->make(true);
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
            ->addColumn(['data' => 'trgo_id', 'name' => 'trgo_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'trgo_nombre', 'name' => 'trgo_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'trgo_requiere_analisis', 'name' => 'trgo_requiere_analisis', 'title' => 'Requiere análisis', 'search' => true])
            ->addColumn(['data' => 'trgo_requiere_doble_aprob', 'name' => 'trgo_requiere_doble_aprob', 'title' => 'Requiere doble aprobación', 'search' => true])
            ->addColumn(['data' => 'trgo_comentarios', 'name' => 'trgo_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.tipos-riesgo.index', compact('dataTable'));
    }

    public function form(TipoRiesgo $tipoRiesgo = null, Request $request)
    {

        $url = ($tipoRiesgo == null) ? url('tipos-riesgo/insert') : url('tipos-riesgo/edit', $tipoRiesgo->getKey());

        return view('web.tipos-riesgo.form', compact('tipoRiesgo', 'url'));
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['trgo_nombre'][] = Rule::unique('tipos_riesgo', 'trgo_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                TipoRiesgo::create($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Tipo de Riesgo <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(TipoRiesgo $tipoRiesgo, Request $request)
    {
        $this->rules['edit']['trgo_nombre'][] = Rule::unique('tipos_riesgo', 'trgo_nombre')->ignore($tipoRiesgo->getKey(), 'trgo_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $tipoRiesgo->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Tipo de Riesgo <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(TipoRiesgo $tipoRiesgo, Request $request)
    {

        DB::beginTransaction();
        try {
            $tipoRiesgo->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Tipo de Riesgo <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(TipoRiesgo $tipoRiesgo)
    {
        return response()->json($tipoRiesgo);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = TipoRiesgo::select(DB::raw('trgo_id as id, trgo_nombre as text'))
            ->where('trgo_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }
}
