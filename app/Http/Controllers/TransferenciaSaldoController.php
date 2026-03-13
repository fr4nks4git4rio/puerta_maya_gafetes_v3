<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CMetodoPago;
use App\Local;
use App\TransferenciaSaldo;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class TransferenciaSaldoController extends Controller
{

    protected $rules = [
        'insert' => [
            'saldo' => 'required',
            'local_desde_id' => 'required',
            'local_para_id' => 'required'
        ],

        'edit' => [
            'id' => 'required|exists:transferencias_pago,id',
            'saldo' => 'required',
            'local_desde_id' => 'required',
            'local_para_id' => 'required',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'saldo' => 'Saldo',
        'local_desde_id' => 'Local Origen',
        'local_para_id' => 'Local Destino',
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
                DB::table('transferencias_saldo as ts')
                ->selectRaw("
                ts.id,
                DATE_FORMAT(ts.fecha, '%d/%m/%Y') as fecha,
                ts.saldo,
                ld.lcal_nombre_comercial as local_desde,
                lp.lcal_nombre_comercial as local_para")
                    ->leftJoin('locales as ld', 'ld.lcal_id', '=', 'ts.local_desde_id')
                    ->leftJoin('locales as lp', 'lp.lcal_id', '=', 'ts.local_para_id')
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
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'fecha', 'name' => 'fecha', 'title' => 'Fecha', 'search' => true])
            ->addColumn(['data' => 'local_desde', 'name' => 'local_desde', 'title' => 'Local Origen', 'search' => true])
            ->addColumn(['data' => 'local_para', 'name' => 'local_para', 'title' => 'Local Destino', 'search' => true])
            ->addColumn(['data' => 'saldo', 'name' => 'saldo', 'title' => 'Saldo', 'search' => true]);


        return view('web.transferencias_saldo.index', compact('dataTable'));

    }

    public function form(TransferenciaSaldo $transferencia = null, Request $request)
    {

        $url = ($transferencia == null) ? url('transferencias-saldo/insert') : url('transferencias-saldo/edit', $transferencia->getKey());
        $razonsociales = Local::selectRaw('lcal_razon_social as razon_social')
            ->distinct('razon_social')
            ->get()
            ->pluck('razon_social', 'razon_social')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.transferencias_saldo.form', compact('transferencia', 'url', 'razonsociales'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            if($this->data['local_desde_id'] === $this->data['local_para_id'])
                return response()->json($this->ajaxResponse(false, 'El Local Origen no puede coincidir con el Local Destino!'));

            $local_desde = Local::find($this->data['local_desde_id']);
            if($local_desde->getSaldos()['saldo_vigente'] < $this->data['saldo'])
                return response()->json($this->ajaxResponse(false, 'El Local Origen no cuenta con el Saldo suficiente!'));


            \DB::beginTransaction();
            try {
                $this->data['fecha'] = now();
                TransferenciaSaldo::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Transferencia de Saldo <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(TransferenciaSaldo $transferencia, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $transferencia->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Tranferencia de Saldo <b>EDITADA</b> correctamente."));
        }

    }

    public function delete(TransferenciaSaldo $transferencia, Request $request)
    {

        \DB::beginTransaction();
        try {
            $transferencia->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Transferencia de Saldo <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(TransferenciaSaldo $transferencia)
    {
        return response()->json($transferencia);
    }
}
