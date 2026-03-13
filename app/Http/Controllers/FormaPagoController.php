<?php

namespace App\Http\Controllers;

use App\Banco;
use App\CFormaPago;
use App\CUsoCfdi;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;

class FormaPagoController extends Controller
{

    protected $rules = [
        'insert' => [
            'codigo' => 'required',
            'descripcion' => 'required',
            'bancarizado' => 'nullable'
        ],

        'edit' => [
            'id' => 'required|exists:c_formapago,id',
            'codigo' => 'required',
            'descripcion' => 'required',
            'bancarizado' => 'nullable'
        ],
    ];


    protected $etiquetas = [
        'id' => 'Id',
        'codigo' => 'Código CFDI',
        'descripcion' => 'Descripción',
        'bancarizado' => 'Bancarizado'
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
                CFormaPago::select(['id', 'codigo', 'descripcion', 'bancarizado'])->where('activo', 1)
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
            ->addColumn(['data' => 'codigo', 'name' => 'codigo', 'title' => 'Código', 'search' => true])
            ->addColumn(['data' => 'descripcion', 'name' => 'descripcion', 'title' => 'Descripción', 'search' => true])
            ->addColumn(['data' => 'bancarizado', 'name' => 'bancarizado', 'title' => 'Bancarizado', 'search' => true]);


        return view('web.formas_pago.index', compact('dataTable'));

    }

    public function form(CFormaPago $forma_pago = null, Request $request)
    {

        $url = ($forma_pago == null) ? url('formas_pago/insert') : url('formas_pago/edit', $forma_pago->getKey());

        return view('web.formas_pago.form', compact('forma_pago', 'url'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                CFormaPago::create($this->data);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Forma de Pago <b>CREADA</b> correctamente.'));

        }
    }

    public function edit(CFormaPago $forma_pago, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $forma_pago->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Forma de Pago <b>EDITADA</b> correctamente."));
        }

    }

    public function delete(CFormaPago $forma_pago, Request $request)
    {

        \DB::beginTransaction();
        try {
            $forma_pago->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Forma de Pago <b>ELIMIADA</b> correctamente."));


    }

    public function getJSON(CFormaPago $forma_pago)
    {
        return response()->json($forma_pago);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = CFormaPago::select(\DB::raw('id, descripcion as text'))
            ->where('descripcion', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
