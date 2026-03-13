<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CClaveProdServ;
use App\CMetodoPago;
use App\CUnidad;
use App\TipoComprobante;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class ClaveProdServController extends Controller
{

    protected $rules = [
        'insert' => [
            'clave' => 'required',
            'descripcion' => 'required',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
            'palabras_similares' => 'nullable',
        ],

        'edit' => [
            'id' => 'required|exists:c_claveprodserv,id',
            'clave' => 'required',
            'descripcion' => 'required',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
            'palabras_similares' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'clave' => 'Clave',
        'descripcion' => 'Descripción',
        'fecha_inicio' => 'Fecha Inicio',
        'fecha_fin' => 'Fecha Fin',
        'palabras_similares' => 'Palabras Similares',
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
                CClaveProdServ::select(['id', 'clave', 'descripcion', 'palabras_similares'])->where('activo', 1)
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
            ->addColumn(['data' => 'clave', 'name' => 'clave', 'title' => 'Clave', 'search' => true])
            ->addColumn(['data' => 'descripcion', 'name' => 'descripcion', 'title' => 'Descripción', 'search' => true])
            ->addColumn(['data' => 'palabras_similares', 'name' => 'palabras_similares', 'title' => 'Palbras Similares', 'search' => true]);


        return view('web.claves_prod_serv.index', compact('dataTable'));

    }

    public function form(CClaveProdServ $clave_prod_serv = null, Request $request)
    {

        $url = ($clave_prod_serv == null) ? url('local/conta/claves_prod_servs/insert') : url('local/conta/claves_prod_servs/edit', $clave_prod_serv->getKey());

        return view('web.claves_prod_serv.form', compact('clave_prod_serv', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CClaveProdServ::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Clave Prod. Serv. <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(CClaveProdServ $clave_prod_serv, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $clave_prod_serv->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Clave Prod. Serv.<b>EDITADA</b> correctamente."));
        }

    }

    public function delete(CClaveProdServ $clave_prod_serv, Request $request)
    {

        \DB::beginTransaction();
        try {
            $clave_prod_serv->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Clave Prod. Serv. <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(CClaveProdServ $clave_prod_serv)
    {
        return response()->json($clave_prod_serv);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CClaveProdServ::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
