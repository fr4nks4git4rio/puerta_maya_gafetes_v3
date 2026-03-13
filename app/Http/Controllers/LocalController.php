<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Local;
use App\LocalAcceso;
use App\CatAcceso;

class LocalController extends Controller
{

    protected $rules = [
        'insert' => [
            'lcal_nombre_comercial' => 'required',
//            'lcal_rfc' => 'nullable|max:13',
//            'lcal_razon_social' => 'nullable',
            'lcal_nombre_responsable' => 'required',
            'lcal_referencia_bancaria' => 'nullable|max:12',
            'lcal_identificador' => 'required|unique:locales,lcal_identificador|max:12',
            'lcal_espacios_autos' => 'nullable|numeric',
            'lcal_espacios_motos' => 'nullable|numeric',
            'lcal_gafetes_gratis' => 'nullable|numeric',
            'lcal_gafetes_gratis_auto' => 'nullable|numeric',
            'lcal_gafetes_gratis_moto' => 'nullable|numeric',
            'lcal_tipo' => 'required',
            'lcal_cacs_id' => 'required',
        ],

        'edit' => [
            'lcal_id' => 'required|exists:locales,lcal_id',
            'lcal_nombre_comercial' => 'required',
            'lcal_razon_social' => 'required',
            'lcal_rfc' => 'required|max:13',
            'lcal_nombre_responsable' => 'required',
            'lcal_referencia_bancaria' => 'nullable|max:12',
            'lcal_identificador' => 'required|max:12',
            'lcal_espacios_autos' => 'nullable|numeric',
            'lcal_espacios_motos' => 'nullable|numeric',
            'lcal_gafetes_gratis' => 'nullable|numeric',
            'lcal_gafetes_gratis_auto' => 'nullable|numeric',
            'lcal_gafetes_gratis_moto' => 'nullable|numeric',
            'lcal_tipo' => 'required',
            'lcal_cacs_id' => 'required',
        ],

        'edit-contabilidad' => [
            'lcal_id' => 'required|exists:locales,lcal_id',
            'lcal_nombre_comercial' => 'required',
            'lcal_razon_social' => 'required',
            'lcal_rfc' => 'required|max:13',
            'lcal_referencia_bancaria' => 'nullable|max:12',
            'lcal_codigo_postal' => 'nullable',
            'lcal_direccion_fiscal' => 'nullable',
            'lcal_regimen_fiscal_id' => 'nullable|exists:c_regimenfiscal,id',
        ],


    ];

    protected $etiquetas = [
        'lcal_id' => 'Id',
        'lcal_nombre_comercial' => 'Nombre Comercial',
        'lcal_razon_social' => 'Razón Social',
        'lcal_rfc' => 'RFC',
        'lcal_nombre_responsable' => 'Responsable',
        'lcal_identificador' => 'Identificador',
        'lcal_referencia_bancaria' => 'Referencia bancaria',
        'lcal_espacios_autos' => 'Espacios de estacionamiento para autos',
        'lcal_espacios_motos' => 'Espacios de estacionamiento para motos',
        'lcal_gafetes_gratis' => 'Gafetes de acceso gratuitos',
        'lcal_gafetes_gratis_auto' => 'Gafetes gratuitos para autos',
        'lcal_gafetes_gratis_moto' => 'Gafetes gratuitos para motos',
        'lcal_tipo' => 'Tipo',
        'lcal_direccion_fiscal' => 'Dirección Fiscal',
        'lcal_regimen_fiscal_id' => 'Régimen Fiscal'
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

    /**
     * Index para el tipo normal del locales
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('LOCAL')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index', compact('dataTable'));

    }

    public function indexCarretas(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('CARRETA')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-carreta', compact('dataTable'));

    }

    public function indexTours(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('TOUR')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-tour', compact('dataTable'));

    }

    public function indexMuelle(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('MUELLE')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-muelle', compact('dataTable'));

    }

    public function indexAgencia(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('AGENCIA')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-agencia', compact('dataTable'));

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Index para el tipo normal del locales CONTABILDAD
     * @param Request $request
     * @param Builder $htmlBuilder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function indexConta(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('LOCAL')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-conta', compact('dataTable'));

    }

    public function indexCarretasConta(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('CARRETA')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-carreta-conta', compact('dataTable'));

    }

    public function indexToursConta(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('TOUR')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-tour-conta', compact('dataTable'));

    }

    public function indexMuelleConta(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('MUELLE')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-muelle-conta', compact('dataTable'));

    }

    public function indexAgenciaConta(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Local::select(['lcal_id', 'lcal_nombre_comercial', 'lcal_rfc', 'lcal_razon_social', 'lcal_identificador',
                    'lcal_espacios_autos', 'lcal_espacios_motos', 'lcal_gafetes_gratis', 'lcal_gafetes_gratis_auto', 'lcal_gafetes_gratis_moto',
                    'lcal_nombre_responsable'])
                    ->whereLcalTipo('AGENCIA')
                    ->orderBy('lcal_nombre_comercial')
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
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'lcal_id', 'name' => 'lcal_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_identificador', 'name' => 'lcal_identificador', 'title' => 'Identificador', 'search' => true])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Nombre Comercial', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social'])
            ->addColumn(['data' => 'lcal_rfc', 'name' => 'lcal_rfc', 'title' => 'RFC'])
            ->addColumn(['data' => 'lcal_espacios_autos', 'name' => 'lcal_espacios_autos', 'title' => 'Espacios Autos', 'search' => false])
            ->addColumn(['data' => 'lcal_espacios_motos', 'name' => 'lcal_espacios_motos', 'title' => 'Espacios Motos', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis', 'name' => 'lcal_gafetes_gratis', 'title' => 'GFTS ACCESSO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_auto', 'name' => 'lcal_gafetes_gratis_auto', 'title' => 'GFTS AUTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_gafetes_gratis_moto', 'name' => 'lcal_gafetes_gratis_moto', 'title' => 'GFTS MOTO GRATIS', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_responsable', 'name' => 'lcal_nombre_responsable', 'title' => 'Responsable']);


        return view('web.local.index-agencia-conta', compact('dataTable'));

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////


    public function form(string $tipo, Local $local = null, Request $request)
    {

        $url = ($local == null) ? url('local/insert') : url('local/edit', $local->getKey());
        $tipo = strtoupper($tipo);

        $id_acceso = 2;

        if ($tipo == 'CARRETA') $id_acceso = 3;
        if ($tipo == 'MUELLE') $id_acceso = 4;
        if ($tipo == 'TOUR') $id_acceso = 5;
        if ($tipo == 'AGENCIA') $id_acceso = 6;


        return view('web.local.form', compact('local', 'url', 'tipo', 'id_acceso'));

    }

    public function formContabilidad(string $tipo, Local $local, Request $request)
    {

//        $url = ($local == null)? url('local/insert') : url('local/edit-contabilidad',$local->getKey() );
        $url = url('local/edit-contabilidad', $local->getKey());
        $tipo = strtoupper($tipo);

        $id_acceso = 2;

        if ($tipo == 'CARRETA') $id_acceso = 3;
        if ($tipo == 'MUELLE') $id_acceso = 4;
        if ($tipo == 'TOUR') $id_acceso = 5;
        if ($tipo == 'AGENCIA') $id_acceso = 6;


        return view('web.local.form-contabilidad', compact('local', 'url', 'tipo', 'id_acceso'));

    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {


            \DB::beginTransaction();
            try {
                $local = Local::create($this->data);

                //insertamos el acceso

//                $record = LocalAcceso::create(['lcac_cacs_id' => $cacs_id,'lcac_lcal_id' => $local->lcal_id]);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Local <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(Local $local, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $local->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Local <b>EDITADO</b> correctamente."));
        }

    }

    public function editContabilidad(Local $local, Request $request)
    {
        if (!$this->validateAction('edit-contabilidad')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            \DB::beginTransaction();

            try {
                $local->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Local <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(Local $local, Request $request)
    {

        \DB::beginTransaction();
        try {
            $local->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Local <b>ELIMIADO</b> correctamente."));


    }

    public function getJSON(Local $local)
    {
        return response()->json($local);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = Local::select(\DB::raw('lcal_id as id, lcal_nombre_comercial as text'))
            ->where('lcal_nombre_comercial', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }

    /*Obtiene la información para un campo select2*/
    public function getLocalsByRazonSocial(Request $request)
    {
        $q = $request->q;

        $records = Local::select(\DB::raw('lcal_id as id, lcal_nombre_comercial as text'))
            ->where('lcal_razon_social', '=', $q)
            ->get()->toArray();
        foreach ($records as &$record){
            $local = Local::find($record['id']);
            $record['text'] .= ' (Disponible: '.$local->getSaldos()['saldo_vigente'].')';
        }
//        $records[0]['selected'] = true;
        return response()->json($records);
    }


}
