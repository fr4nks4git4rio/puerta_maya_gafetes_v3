<?php

namespace App\Http\Controllers;

use App\Clases\InventoryHelper;
use App\InventarioBaja;
use App\InventarioCompra;
use App\VInventarioCompra;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;



use App\Inventario;


class InventarioController extends Controller
{

    protected $rules = [
        'insert' =>[
            'icmp_fecha'    => 'required|date_format:Y-m-d',
            'icmp_cantidad'      => 'required|integer|min:1',
            'inventory_low_limit'   => 'nullable|integer|min:1'
        ],

        'insert-baja' =>[
            'ibaj_icmp_id'    => 'required|integer',
            'ibaj_acceso'    => 'required|integer|min:0',
            'ibaj_estacionamiento'    => 'required|integer|min:0',
            'ibaj_merma'    => 'required|integer|min:0',
            'ultima_compra'    => 'required|integer|min:0',
        ],

//        'edit' => [
//            'invt_id'       => 'required|exists:inventario_tarjetas,invt_id',
//            'invt_fecha'    => 'required|date_format:Y-m-d',
//            'invt_tipo'     => 'required',
//            'invt_cantidad'      => 'required|integer|min:1',
//            'invt_tipo_baja'    => 'nullable',
//            'invt_comentario'   => 'nullable',
//            'inventory_low_limit'   => 'nullable|integer|min:1'
//        ],


    ];

    protected  $etiquetas = [
        'icmp_id'       => 'Id',
        'icmp_fecha'    => 'Fecha',
        'icmp_cantidad'      => 'Cantidad',
        'inventory_low_limit'   => 'Limite mínimo de tarjetas',

        'ibaj_icmp_id'    => 'Compra',
        'ibaj_acceso'    => 'Accesos impresos',
        'ibaj_estacionamiento'    => 'Estacionamiento impresos',
        'ibaj_merma'    => 'Merma',
        'ultima_compra'    => 'Ultima Compra',
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
                        VInventarioCompra::select(['icmp_id', 'icmp_cantidad','icmp_fecha', 'icmp_saldo_registro',
                            'icmp_saldo_anterior', 'ibaj_acceso', 'ibaj_estacionamiento','ibaj_permiso_temporal','ibaj_merma'])
                                ->orderBy('icmp_created_at','desc')
                        )
                        ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select'=>'single',
            'autoWidth'  => false,
            'language' => [
                'url'=> asset('plugins/datatables/datatables_local_es_ES.json')
            ]
//            'order'=>[[1,'desc']]
        ]);

         $dataTable = $htmlBuilder
            ->addColumn(['data' => 'icmp_id', 'name' => 'icmp_id', 'title' => 'Id', 'visible'=>false])
            ->addColumn(['data' => 'icmp_fecha', 'name' => 'icmp_fecha', 'title' => 'Fecha', 'search'=>false ])
             ->addColumn(['data' => 'icmp_saldo_anterior', 'name' => 'icmp_saldo_anterior', 'title' => 'Saldo Anterior','search'=>true])
             ->addColumn(['data' => 'icmp_cantidad', 'name' => 'icmp_cantidad', 'title' => 'Cantidad Compra','search'=>true])

             ->addColumn(['data' => 'ibaj_acceso', 'name' => 'ibaj_acceso', 'title' => 'Accesos Impresos', 'search'=>false])
             ->addColumn(['data' => 'ibaj_estacionamiento', 'name' => 'ibaj_estacionamiento', 'title' => 'Estacionamiento Impresos', 'search'=>false])
             ->addColumn(['data' => 'ibaj_merma', 'name' => 'ibaj_merma', 'title' => 'Merma', 'search'=>false])
             ->addColumn(['data' => 'icmp_saldo_registro', 'name' => 'icmp_saldo_registro', 'title' => 'Saldo Registro','search'=>true]);

        $currentUsedCards = InventoryHelper::getCurrentUsedCards();


        return view('web.inventario.index', compact('dataTable',$currentUsedCards));

    }


    public function form(Inventario $record = null, Request $request){

        $url = ($record == null)? url('inventario/insert') : url('inventario/edit',$record->getKey() );


        return view('web.inventario.form', compact('record','url'));

    }

    public function formBaja( Request $request){

        $url = url('inventario/insert-baja');

        //ultima compra
        $compra = VInventarioCompra::where('count_bajas',0)->latest()->first();

        $currentUsedCards = InventoryHelper::getCurrentUsedCards();

//        dd($currentUsedCards,$compra);

        return view('web.inventario.form_baja', compact('url','compra','currentUsedCards'));

    }

    public function insert(Request $request)
    {

        if(! $this->validateAction('insert')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            //ultima compra
            $compra = VInventarioCompra::where('count_bajas',0)->latest()->first();
            if($compra!= null){
                return response() -> json($this->ajaxResponse(false,'No se puede capturar una nueva compra hasta completar la baja de la última'));
            }

            \DB::beginTransaction();
            try
            {
//                dd($this->data);
                $inventory_low_limit = $this->data['inventory_low_limit'];


                $compra_anterior = $compra = VInventarioCompra::latest()->first();
                $saldo_anterior = ($compra_anterior != null)? $compra_anterior->icmp_saldo_registro : 0;


                $data = \Arr::except($this->data, ['inventory_low_limit']);
                $data['icmp_saldo_anterior'] = $saldo_anterior;

//                dd($compra_anterior,$saldo_anterior,$data);

                $record = InventarioCompra::create($data);

                if($inventory_low_limit > 0){
                    settings()->set('inventory_low_limit',$inventory_low_limit);
                    settings()->save();
                }

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() . $e -> getFile() . $e -> getLine()  ));
            }

            \DB::commit();
            return response()->json($this -> ajaxResponse(true,'Registro <b>CREADO</b> correctamente.'));

        }
    }

    public function insertBaja(Request $request)
    {

        if(! $this->validateAction('insert-baja')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            //ultima compra
            $suma = $this->data['ibaj_acceso'] + $this->data['ibaj_estacionamiento'] + $this->data['ibaj_merma'];
//            if($suma != $this->data['ultima_compra']){
//                return response() -> json($this->ajaxResponse(false,'La suma de los gafetes impresos y merma debe ser igual a la última compra'));
//            }

            \DB::beginTransaction();
            try
            {
                $data = \Arr::except($this->data, ['ultima_compra']);
                $record = InventarioBaja::create($data);

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() . $e -> getFile() . $e -> getLine()  ));
            }

            \DB::commit();
            return response()->json($this -> ajaxResponse(true,'Registro <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(Inventario $registro, Request $request)
    {
        if(! $this->validateAction('edit')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            \DB::beginTransaction();

            try
            {
                $data = Arr::except($this->data, ['inventory_low_limit']);
                $registro->update($data);

                $invetory_low_limit = $this->data['inventory_low_limit'];
                settings()->set('inventory_low_limit',$invetory_low_limit);


            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
            }

            \DB::commit();
            return response() -> json($this->ajaxResponse(true,"Regsitro <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(Inventario $record,Request $request)
    {

        \DB::beginTransaction();
        try
        {
            $record->delete();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
        }

        \DB::commit();
        return response() -> json($this->ajaxResponse(true,"Registro <b>ELIMIADO</b> correctamente."));


    }


    public function getStockData(){

        $ultima_compra = 0;
        $ultimo_saldo = 0;
//        $compra = VInventarioCompra::where('count_bajas',0)->latest()->first();
        $compra = VInventarioCompra::latest()->first();
        if($compra!= null){
            $ultima_compra = $compra->icmp_cantidad;
            $ultimo_saldo = $compra->icmp_saldo_registro;
        }




        $currentUsedCards = InventoryHelper::getCurrentUsedCards();

        $stock = $ultimo_saldo - $currentUsedCards['acceso'] - $currentUsedCards['estacionamiento'];

        $data=[
            'inventory_low_limit' => settings()->get('inventory_low_limit',0),
            'current_stock' => $stock,
            'current_used_cards' => $currentUsedCards,
            'last_buy' => $ultima_compra
            ];
        // $empleado->empl_foto_web = $empleado->empl_foto_web;
//        dd($data);
        return response()->json( $data );

    }

    public function getJSON(Invetario $registro){

        // $empleado->empl_foto_web = $empleado->empl_foto_web;
        return response()->json( $registro );

    }





}
