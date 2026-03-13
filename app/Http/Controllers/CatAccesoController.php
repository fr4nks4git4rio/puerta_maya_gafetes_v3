<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class CatAccesoController extends Controller
{

    protected $rules = [
        'insert' =>[
            'cacs_descripcion'    => 'required',
            'cacs_color'        => 'nullable',
        ],

        'edit' => [
            'cacs_id'    => 'required|exists:cat_accesos,cacs_id',
            'cacs_descripcion'    => 'required',
            'cacs_color'        => 'nullable',
        ],
    ];


    protected  $etiquetas = [
        'cacs_id'    => 'Id',
        'cacs_descripcion'    => 'Nombre Acceso',
        'cacs_color' => 'Color'
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
                        CatAcceso::select(['cacs_id', 'cacs_descripcion','cacs_color'])
                        )
                        ->editColumn('cacs_color', function(CatAcceso $model) {
                            return '<div style="background-color: '. $model->cacs_color .'; max-width:75px; border-radius:3px;"> &nbsp; </div>';
                        })
                        ->rawColumns(['cacs_color'])
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
            ->addColumn(['data' => 'cacs_id', 'name' => 'cacs_id', 'title' => 'Id', 'visible'=>false])
            ->addColumn(['data' => 'cacs_descripcion', 'name' => 'cacs_descripcion', 'title' => 'Acceso', 'search'=>true])
            ->addColumn(['data' => 'cacs_color', 'name' => 'cacs_color', 'title' => 'Color', 'search'=>true]);


        return view('web.cat-acceso.index', compact('dataTable'));

    }

    public function form(CatAcceso $acceso = null, Request $request){

        $url = ($acceso == null)? url('cat-acceso/insert') : url('cat-acceso/edit',$acceso->getKey() );

        return view('web.cat-acceso.form', compact('acceso','url'));

    }

    public function insert(Request $request)
    {

        if(! $this->validateAction('insert')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {
                CatAcceso::create($this -> data);

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() ));
            }

            \DB::commit();
            return response()->json($this -> ajaxResponse(true,'Acceso <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(CatAcceso $acceso, Request $request)
    {
        if(! $this->validateAction('edit')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            \DB::beginTransaction();

            try
            {
                $acceso->update($this->data);
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
            }

            \DB::commit();
            return response() -> json($this->ajaxResponse(true,"Acceso <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(CatAcceso $acceso,Request $request)
    {

        \DB::beginTransaction();
        try
        {
            $acceso->delete();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
        }

        \DB::commit();
        return response() -> json($this->ajaxResponse(true,"Acceso <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(CatAcceso $acceso){
        return response()->json( $acceso );
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request){
      $q = $request -> q;

      $records = CatAcceso::select(\DB::raw('cacs_id as id, cacs_descripcion as text'))
                              ->where('cacs_descripcion','like',"%$q%")
                              ->get() -> toArray();
      $records[0]['selected'] = true;
      return response()->json( $records);
    }


}
