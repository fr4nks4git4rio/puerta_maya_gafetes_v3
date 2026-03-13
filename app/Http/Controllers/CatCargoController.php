<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatCargo;
use App\CatAcceso;
use App\CargoAcceso;

class CatCargoController extends Controller
{

    protected $rules = [
        'insert' =>[
            'crgo_descripcion'    => 'required',
//            'crgo_color'        => 'nullable',
        ],

        'edit' => [
            'crgo_id'    => 'required|exists:cat_cargos,crgo_id',
            'crgo_descripcion'    => 'required',
//            'crgo_color'  => 'nullable'
        ],


    ];

    protected  $etiquetas = [
        'crgo_id'    => 'Id',
        'crgo_descripcion'    => 'Nombre Cargo',
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
                        CatCargo::select(['crgo_id', 'crgo_descripcion','crgo_color'])
//                            ->with('Accesos')
                        )
//                        ->editColumn('crgo_color', function(CatCargo $model) {
//                            return '<div style="background-color: '. $model->crgo_color .'; max-width:75px; border-radius:3px;"> &nbsp; </div>';
//                        })
//                        ->addColumn('accesos', function(CatCargo $model) {
//
//                            return implode(" | ",$model->Accesos->sortBy('cacs_descripcion')->pluck('cacs_descripcion')->toArray());
//                        })
//                        ->rawColumns(['crgo_color','accesos'])
                        ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select'=>'single',
            'autoWidth'  => false,
            'language' => [
                'url'=> asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order'=>[[0,'desc']]
        ]);

         $dataTable = $htmlBuilder
            ->addColumn(['data' => 'crgo_id', 'name' => 'crgo_id', 'title' => 'Id', 'visible'=>false])
            ->addColumn(['data' => 'crgo_descripcion', 'name' => 'crgo_descripcion', 'title' => 'Cargo', 'search'=>true]);
//            ->addColumn(['data' => 'crgo_color', 'name' => 'crgo_color', 'title' => 'Color', 'search'=>true])
//            ->addColumn(['data' => 'accesos', 'name' => 'accesos', 'title' => 'Accesos', 'search'=>false]);


        return view('web.cat-cargo.index', compact('dataTable'));

    }

    public function form(CatCargo $cargo = null, Request $request){

        $url = ($cargo == null)? url('cat-cargo/insert') : url('cat-cargo/edit',$cargo->getKey() );

        return view('web.cat-cargo.form', compact('cargo','url'));

    }

    public function insert(Request $request)
    {

        if(! $this->validateAction('insert')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {
                CatCargo::create($this -> data);

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() ));
            }

            \DB::commit();
            return response()->json($this -> ajaxResponse(true,'Cargo <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(CatCargo $cargo, Request $request)
    {
        if(! $this->validateAction('edit')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            \DB::beginTransaction();

            try
            {
                $cargo->update($this->data);
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
            }

            \DB::commit();
            return response() -> json($this->ajaxResponse(true,"Cargo <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(CatCargo $cargo,Request $request)
    {

        \DB::beginTransaction();
        try
        {
            $cargo->delete();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
        }

        \DB::commit();
        return response() -> json($this->ajaxResponse(true,"Local <b>ELIMIADO</b> correctamente."));


    }


    public function formAccesos(CatCargo $cargo, Request $request){

        $url = url('cat-cargo/set-accesos',$cargo->crgo_id);

        $accesos = CatAcceso::all();
        $selected = $cargo->Accesos->pluck('cacs_id')->toArray();

        return view('web.cat-cargo.form_accesos', compact('cargo','url','accesos','selected'));
    }


    public function setAccesos(CatCargo $cargo, Request $request){

        $accesos = $request->get('accesos',[]);
        $accesos = array_keys($accesos);


        CargoAcceso::whereCgacCrgoId($cargo->crgo_id)-> delete();

        foreach($accesos as $cacs_id)
        {
            $record = CargoAcceso::create(['cgac_cacs_id' => $cacs_id,'cgac_crgo_id' => $cargo->crgo_id]);

        }


        //Activity Log/////////////////////////////////////////////////////////////////////////
        activity()
        ->performedOn($cargo)
        ->inLog('Cargo')
        ->withProperties(['accesos' => $accesos])
        ->log("Cambió los accesos asignados al cargo <b>{$cargo->crgo_descripcion}</b>");
        ///////////////////////////////////////////////////////////////////////////////////////

        return response()->json($this -> ajaxResponse(true,'Accesos asignados correctamente.'));

    }

    public function getJSON(CatCargo $cargo){
        return response()->json( $cargo );
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request){
      $q = $request -> q;

      $records = CatCargo::select(\DB::raw('crgo_id as id, crgo_descripcion as text'))
                              ->where('crgo_descripcion','like',"%$q%")
                              ->get() -> toArray();
      $records[0]['selected'] = true;
      return response()->json( $records);
    }


}
