<?php

namespace App\Http\Controllers;

use App\Banco;
use App\Controladora;
use App\Pin;
use App\Puerta;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PuertaController extends Controller
{

    protected $rules = [
        'insert' => [
            'door_nombre' => 'required',
            'door_tipo' => 'required',
            'door_numero' => 'required',
            'door_direccion' => ['required'],
            'door_modo' => ['required'],
            'door_observaciones' => 'nullable',
            'door_controladora_id' => ['required', 'exists:tb_controladoras,ctrl_id']
        ],

        'edit' => [
            'door_id' => 'required|exists:puertas,door_id',
            'door_nombre' => 'required',
            'door_tipo' => 'required',
            'door_numero' => 'required',
            'door_direccion' => ['required'],
            'door_modo' => ['required'],
            'door_observaciones' => 'nullable',
            'door_controladora_id' => ['required', 'exists:tb_controladoras,ctrl_id']
        ]
    ];


    protected $etiquetas = [
        'door_id' => 'Id',
        'door_nombre' => 'Nombre Puerta',
        'door_tipo' => 'Tipo de Acceso',
        'door_numero' => 'Número Puerta',
        'door_direccion' => 'Dirección',
        'door_modo' => 'Modalidad',
        'door_observaciones' => 'Observaciones',
        'door_controladora_id' => 'Controladora'
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
                Puerta::select(['door_id', 'door_nombre', 'door_controladora_id', 'door_tipo', 'door_numero', 'door_direccion', 'door_modo',
                    'ctrl_nombre', 'door_observaciones'])
                    ->join('tb_controladoras', 'door_controladora_id', 'ctrl_id')
                    ->orderBy('door_controladora_id', 'asc')
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
            ->addColumn(['data' => 'door_id', 'name' => 'door_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'door_nombre', 'name' => 'door_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'door_tipo', 'name' => 'door_tipo', 'title' => 'Tipo Acceso', 'search' => true])
            ->addColumn(['data' => 'door_numero', 'name' => 'door_numero', 'title' => 'Número Puerta', 'search' => true])
            ->addColumn(['data' => 'door_direccion', 'name' => 'door_direccion', 'title' => 'Dirección', 'search' => true])
            ->addColumn(['data' => 'door_modo', 'name' => 'door_modo', 'title' => 'Modalidad', 'search' => true])
            ->addColumn(['data' => 'ctrl_nombre', 'name' => 'ctrl_nombre', 'title' => 'Controladora', 'search' => true])
            ->addColumn(['data' => 'door_observaciones', 'name' => 'door_observaciones', 'title' => 'Observaciones', 'search' => true]);


        return view('web.puertas.index', compact('dataTable'));

    }

    public function form(Puerta $puerta = null, Request $request)
    {
        $url = ($puerta == null) ? url('puertas/insert') : url('puertas/edit', $puerta->getKey());

        $controladoras = Controladora::pluck('ctrl_nombre', 'ctrl_id');

        return view('web.puertas.form', compact('puerta', 'url', 'controladoras'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            // $puerta_con_pin = Puerta::where('door_controladora_id', $this->data['door_controladora_id'])
            //     ->where('door_pin_id', $this->data['door_pin_id'])
            //     ->get();
            // if ($puerta_con_pin->count() > 0) {
            //     $controladora = Controladora::find($this->data['door_controladora_id']);
            //     return response()->json($this->ajaxResponse(false, "Ya existe una Puerta con el Pin: ".$this->data['door_pin_id'].", para la Controladora: $controladora->ctrl_nombre!"));
            // }

            DB::beginTransaction();
            try {
                Puerta::create($this->data);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, 'Puerta <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(Puerta $puerta, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            // $puerta_con_pin = Puerta::where('door_controladora_id', $this->data['door_controladora_id'])
            //     ->where('door_pin_id', $this->data['door_pin_id'])
            //     ->where('door_id', '!=', $this->data['door_id'])
            //     ->get();
            // if ($puerta_con_pin->count() > 0) {
            //     $controladora = Controladora::find($this->data['door_controladora_id']);
            //     return response()->json($this->ajaxResponse(false, "Ya existe una Puerta con el Pin: ".$this->data['door_pin_id'].", para la Controladora: $controladora->ctrl_nombre!"));
            // }

            DB::beginTransaction();

            try {
                $puerta->update($this->data);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            DB::commit();
            return response()->json($this->ajaxResponse(true, "Puerta <b>EDITADA</b> correctamente."));
        }

    }

    public function delete(Puerta $puerta, Request $request)
    {

        DB::beginTransaction();
        try {
            $puerta->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        DB::commit();
        return response()->json($this->ajaxResponse(true, "Puerta <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(Puerta $puerta)
    {
        return response()->json($puerta);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = Puerta::select(DB::raw('id, door_nombre as text'))
            ->where('door_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
