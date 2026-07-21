<?php

namespace App\Http\Controllers;

use App\EppBasico;
use App\EppEspecifico;
use App\TipoMantenimiento;
use App\TipoRiesgo;
use App\TrabajoEspecifico;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;

class TrabajoEspecificoController extends Controller
{

    protected $rules = [
        'insert' => [
            'tesp_nombre' => ['required'],
            'tesp_epp_basicos' => ['required', 'array', 'min:1'],
            'tesp_epp_especificos' => ['nullable', 'array'],
            'tesp_tmtt_id' => ['required', 'exists:tipos_mantenimiento,tmtt_id'],
            'tesp_trgo_id' => ['required', 'exists:tipos_riesgo,trgo_id'],
            'tesp_comentarios' => 'nullable',
        ],

        'edit' => [
            'tesp_id' => 'required|exists:trabajos_especificos,tesp_id',
            'tesp_nombre' => ['required'],
            'tesp_epp_basicos' => ['required', 'array', 'min:1'],
            'tesp_epp_especificos' => ['nullable', 'array'],
            'tesp_tmtt_id' => ['required', 'exists:tipos_mantenimiento,tmtt_id'],
            'tesp_trgo_id' => ['required', 'exists:tipos_riesgo,trgo_id'],
            'tesp_comentarios' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'tesp_id' => 'Id',
        'tesp_nombre' => 'Nombre',
        'teso_epp_basicos' => 'EPP Básicos',
        'teso_epp_especificos' => 'EPP Específicos',
        'tesp_tmtt_id' => 'Tipo de Mantenimiento',
        'tesp_trgo_id' => 'Tipo de Riesgo',
        'tesp_comentarios' => 'Comentarios'
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
        if (!isset($this->data['tesp_epp_basicos']))
            $this->data['tesp_epp_basicos'] = [];
        if (!isset($this->data['tesp_epp_especificos']))
            $this->data['tesp_epp_especificos'] = [];
    }


    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                TrabajoEspecifico::select([
                    'tesp_id',
                    'tesp_nombre',
                    DB::raw("'' as tesp_epp_basicos"),
                    DB::raw("'' as tesp_epp_especificos"),
                    'tmtt.tmtt_nombre',
                    'trgo.trgo_nombre',
                    'tesp_comentarios'
                ])
                    ->leftJoin('tipos_mantenimiento as tmtt', 'tmtt.tmtt_id', '=', 'tesp_tmtt_id')
                    ->leftJoin('tipos_riesgo as trgo', 'trgo.trgo_id', '=', 'tesp_trgo_id')
                    ->orderBy('tesp_nombre')
            )
                ->editColumn('tesp_epp_basicos', function (TrabajoEspecifico $model) {
                    return join(', ', $model->EppBasicos()->get()->map(function ($element) {
                        return $element->eppb_nombre;
                    })->toArray());
                })
                ->editColumn('tesp_epp_especificos', function (TrabajoEspecifico $model) {
                    return $model->EppEspecificos()->exists()
                        ? join(', ', $model->EppEspecificos()->get()->map(function ($element) {
                            return $element->eppe_nombre;
                        })->toArray())
                        : 'N/A';
                })
                ->rawColumns(['tesp_epp_basicos', 'tesp_epp_especificos'])
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
            ->addColumn(['data' => 'tesp_id', 'name' => 'tesp_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'tesp_nombre', 'name' => 'tesp_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'tesp_epp_basicos', 'name' => 'tesp_epp_basicos', 'title' => 'EPP Básicos', 'search' => true])
            ->addColumn(['data' => 'tesp_epp_especificos', 'name' => 'tesp_epp_especificos', 'title' => 'EPP Específicos', 'search' => true])
            ->addColumn(['data' => 'tmtt_nombre', 'name' => 'tmtt_nombre', 'title' => 'Tipo de Mantenimiento', 'search' => true])
            ->addColumn(['data' => 'trgo_nombre', 'name' => 'trgo_nombre', 'title' => 'Tipo de Riesgo', 'search' => true])
            ->addColumn(['data' => 'tesp_comentarios', 'name' => 'tesp_comentarios', 'title' => 'Comentarios', 'search' => true]);


        return view('web.trabajos-especificos.index', compact('dataTable'));
    }

    public function form(TrabajoEspecifico $trabajoEspecifico = null, Request $request)
    {

        $url = ($trabajoEspecifico == null) ? url('trabajos-especificos/insert') : url('trabajos-especificos/edit', $trabajoEspecifico->getKey());

        $tiposMantenimiento = TipoMantenimiento::all()->pluck('tmtt_nombre', 'tmtt_id');
        $tiposRiesgo = TipoRiesgo::all()->pluck('trgo_nombre', 'trgo_id');
        $eppBasicos = EppBasico::all()->pluck('eppb_nombre', 'eppb_id');
        $eppEspecificos = EppEspecifico::all()->pluck('eppe_nombre', 'eppe_id');

        $tesp_epp_basicos = $trabajoEspecifico == null ? null : $trabajoEspecifico->EppBasicos()->pluck('eppb_id');
        $tesp_epp_especificos = $trabajoEspecifico == null ? null : $trabajoEspecifico->EppEspecificos()->pluck('eppe_id');

        return view(
            'web.trabajos-especificos.form',
            compact(
                'trabajoEspecifico',
                'url',
                'tiposMantenimiento',
                'tiposRiesgo',
                'eppBasicos',
                'eppEspecificos',
                'tesp_epp_basicos',
                'tesp_epp_especificos'
            )
        );
    }

    public function insert(Request $request)
    {
        $this->rules['insert']['tesp_nombre'][] = Rule::unique('trabajos_especificos', 'tesp_nombre');
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            DB::beginTransaction();
            try {
                $trabajo = TrabajoEspecifico::create(Arr::except($this->data, ['tesp_epp_basicos', 'tesp_epp_especificos']));
                $trabajo->EppBasicos()->sync($this->data['tesp_epp_basicos']);
                $trabajo->EppEspecificos()->sync($this->data['tesp_epp_especificos']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Trabajo específico <b>CREADO</b> correctamente.'));
        }
    }

    public function edit(TrabajoEspecifico $trabajoEspecifico, Request $request)
    {
        $this->rules['edit']['tesp_nombre'][] = Rule::unique('trabajos_especificos', 'tesp_nombre')->ignore($trabajoEspecifico->getKey(), 'tesp_id');
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            DB::beginTransaction();

            try {
                $trabajoEspecifico->update(Arr::except($this->data, ['tesp_epp_basicos', 'tesp_epp_especificos']));
                $trabajoEspecifico->EppBasicos()->sync($this->data['tesp_epp_basicos']);
                $trabajoEspecifico->EppEspecificos()->sync($this->data['tesp_epp_especificos']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Trabajo específico <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(TrabajoEspecifico $trabajoEspecifico, Request $request)
    {

        DB::beginTransaction();
        try {
            $trabajoEspecifico->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Trabajo específico <b>ELIMIADO</b> correctamente."));
    }

    public function getJSON(TrabajoEspecifico $trabajoEspecifico)
    {
        return response()->json($trabajoEspecifico);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = TrabajoEspecifico::select(DB::raw('tesp_id as id, tesp_nombre as text'))
            ->where('tesp_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }

    public function loadTrabEspByTipoMante(TipoMantenimiento $tipoMantenimiento)
    {
        return response()->json($tipoMantenimiento->TrabajosEspecificos()->get()->map(function ($value) {
            return ['id' => $value->tesp_id, 'text' => $value->tesp_nombre];
        })->toArray());
    }

    public function getResumenTrabEsp(TrabajoEspecifico $trabajoEspecifico)
    {
        $data = [
            'epp_basicos' => join(', ', $trabajoEspecifico->EppBasicos()->get()->map(function ($element) {
                return $element->eppb_nombre;
            })->toArray()),
            'epp_especificos' => join(', ', $trabajoEspecifico->EppEspecificos()->get()->map(function ($element) {
                return $element->eppe_nombre;
            })->toArray()),
            'tipo_riesgo' => $trabajoEspecifico->TipoRiesgo->trgo_nombre
        ];
        return response()->json($data);
    }
}
