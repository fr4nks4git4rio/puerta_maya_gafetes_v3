<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


// use App\Local;

class RoleController extends Controller
{

    protected $rules = [


    ];

    protected  $etiquetas = [

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
                        \DB::table('roles')
                        ->select(['id', 'name'])
                        )
                        // ->filterColumn('con_nombre', function($query, $keyword) {
                        //         $sql = "con_nombre  like ?";
                        //         $query->whereRaw($sql, ["%{$keyword}%"]);
                        // })
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
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'visible'=>false])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Rol', 'search'=>true]);
            // ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search'=>true])
            // ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            // ->addColumn(['data' => 'lcal_espacios_est', 'name' => 'lcal_espacios_est', 'title' => 'Espacios Est.','search'=>false])
            // ->addColumn(['data' => 'lcal_gafetes_est', 'name' => 'lcal_gafetes_est', 'title' => 'Gafetes Est.','search'=>false])
            // ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.rol.index', compact('dataTable'));

    }


}
