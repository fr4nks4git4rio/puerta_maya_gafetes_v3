<?php
namespace App\Http\Controllers;

//Framework
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;

//App Clases
use App\VActivity;


class ActivityController extends Controller
{

    protected $rules = [

    ];

    protected $etiquetas = [
        // 'clie_id'       => 'ID',
        // 'clie_apellido_paterno'  => 'Apellido Paterno',

    ];


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }

    public function index(Request $request, Builder $htmlBuilder){

        if ($request->ajax()) {
            return Datatables::of(VActivity::select(['id', 'log_name', 'description',
                'name','created_at']))
                ->rawColumns(['description'])
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
            ->addColumn(['data' => 'log_name', 'name' => 'log_name', 'title' => 'Clase'])
            ->addColumn(['data' => 'description', 'name' => 'description', 'title' => 'Descripción'])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Usuario'])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Fecha / Hora']);

        return view('web.activity.index', compact('dataTable'));

    }

    public function indexSeguridad(Request $request, Builder $htmlBuilder){

        return $this->index($request,$htmlBuilder);

    }

    public function detailsView(VActivity $activity){
        return view('web.activity.details', compact('activity'));
    }

}
