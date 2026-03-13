<?php

namespace App\Http\Controllers;

use App\ComprobantePago;
use App\Exports\FacturasExport;
use App\Mail\FacturaTimbrada;
use App\Notifications\FacturaTimbrada as NotificacionFacturaTimbrada;
use App\Reports\FormatoFacturaPDF;
use App\Reports\FacturaListadoComprobantesPDF;
use App\User;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Notifications\AnonymousNotifiable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;

//Clases especiales
use App\Clases\CFDI\CfdiConstructor;
use App\Clases\CFDI\CfdiTimbrador;


use App\Local;
use App\Factura;
use App\FacturaDetalle;


class FacturaController extends Controller
{

    protected $system_folder_empleados = "";

    protected $rules = [
        'insert' =>[
            'fact_lcal_id'    => 'nullable|exists:locales,lcal_id',

            'fact_rfc_emisor'       => 'required',
            'fact_nombre_emisor'    => 'required',
            'fact_fecha_creacion'   => 'required|date_format:Y-m-d',

            'fact_nombre_receptor'  => 'required',
            'fact_rfc_receptor'     => 'required',
            'fact_lugar_expedicion'     => 'required',
            'fact_serie_id'     => 'required|exists:c_serie,id',
            'fact_usocfdi_id'     => 'required|exists:c_usocfdi,id',
            'fact_formapago_id'     => 'required|exists:c_formapago,id',
            'fact_metodopago_id'     => 'required|exists:c_metodopago,id',
            'fact_moneda_id'     => 'required|exists:c_moneda,id',
            'fact_cantidad_letra'     => 'required',
            'fact_observaciones'     => 'nullable',

            'total_importe'     => 'required|min:1',
            'total_iva'         => 'required|min:1',
            'total_subtotal'    => 'required|min:1',

            'conceptos' => 'required'

            // 'ptmp_caracter'     => 'nullable',
            // 'ptmp_objeto'     => 'nullable',
        ],

        'validar-concepto' => [
            'fcdt_cantidad' => 'required|integer|min:1|max:99',
            'fcdt_claveunidad_id' => 'required|exists:c_claveunidad,id',
            'fcdt_claveproducto_id' => 'required|exists:c_claveprodserv,id',
            'fcdt_concepto' => 'required',
            'fcdt_importe' => 'required|numeric|min:1',
            'fcdt_precio' => 'required|numeric|min:1',
            'fcdt_iva' => 'required|numeric|min:1',
            'fcdt_cpag_id' => 'required|exists:comprobantes_pago,cpag_id'
        ],

        'do-edit-sm' =>[
            'fact_id'    => 'nullable|exists:facturas',
            'fact_usocfdi_id'     => 'required|exists:c_usocfdi,id',
            'fact_formapago_id'     => 'required|exists:c_formapago,id',

        ],


        'do-cancelar' =>[
            'ptmp_id'    => 'required|exists:permisos_temporales,ptmp_id',
        ],

        'do-send-mail' =>[
            'fact_id'    => 'required|exists:facturas,fact_id',
            'email_to'    => 'required|email',
        ],

    ];

    protected  $etiquetas = [
        'ptmp_id'    => 'Permiso',
        'ptmp_lcal_id'    => 'Local',
        'ptmp_nombre'     => 'Nombre',
        'ptmp_cargo'     => 'Cargo',
        'ptmp_correo'     => 'Correo',
        'ptmp_telefono'     => 'Teléfono',
        'ptmp_fecha'     => 'Fecha',
        'ptmp_vigencia_inicial'     => 'Vigencia Inicial',
        'ptmp_vigencia_final'     => 'Vigencia Final',
        'ptmp_caracter'     => 'Carácter',
        'ptmp_objeto'     => 'Objeto',
        'ptmp_comentario'     => 'Comentario',
        'ptmp_comentario_admin'     => 'Comentario',

        'fcdt_cantidad' => 'Cantidad',
        'fcdt_claveunidad_id' => 'Unidad',
        'fcdt_claveproducto_id' => 'Producto o Servicio',
        'fcdt_concepto' => 'Concepto',
        'fcdt_importe' => 'Total',
        'fcdt_precio' => 'Subtotal',
        'fcdt_iva' => 'IVA',
        'fcdt_cpag_id' => 'Comprobante de pago',

        'fact_id'     => 'Factura',
        'fact_usocfdi_id'     => 'Uso CFDI',
        'fact_formapago_id'     => 'Forma de Pago',

        'email_to' => 'Destinatario'
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

        $this->system_folder_cfdi = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'cfdi';
    }


    public function indexLocatario(Request $request, Builder $htmlBuilder)
    {

        //detrminamos el local
        $usuario = \Auth::getUser();
        if($usuario->usr_lcal_id == null){
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;
        // dd($usuario,$local);

        if ($request->ajax()) {
            return Datatables::of(
                        Factura::select(['fact_id', 'fact_fecha_emision','fact_fecha_certificacion',
                                'fact_nombre_receptor','fact_tipo_cambio', 'fact_uuid',
                                'fact_subtotal', 'fact_iva', 'fact_total',
                                'fact_estado', 'fact_test_mode'])
                                ->whereFactLcalId($local->lcal_id)
                        )

                        ->editColumn('fact_estado', function(Factura $model) {

                            $color = 'badge-primary';
                            if($model->fact_estado  == 'CAPTURADA') $color = 'badge-warning';
                            if($model->fact_estado  == 'TIMBRADA')  $color = 'badge-success';
                            if($model->fact_estado  == 'CANCELADA') $color = 'badge-danger';

                            $html = '<div class="text-center"><small class="badge '. $color .'">'. $model->fact_estado .'</small>';
                            $html.='</div>';
                            return $html;

                        })

                        ->addColumn('actions', function (Factura $model) {

                            $html = '<div class="btn-group">';


                            $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato PDF" data-id=' . $model->fact_id . '><i class="fa fa-file-pdf-o"></i></span>';

                            if($model->fact_estado == 'CAPTURADA'){
                                $html .= '<span class="btn btn-primary btn-sm btn-timbrar" title="Timbrar" data-id=' . $model->fact_id. '><i class="fa fa-bell"></i></span>';
                                $html .= '<span class="btn btn-primary btn-sm btn-eliminar" title="Eliminar" data-id=' . $model->fact_id. '><i class="fa fa-times"></i></span>';
                            }

                            if($model->fact_estado != 'CAPTURADA'){
                                $html .= '<span class="btn btn-primary btn-sm btn-xml" title="Descargar XML" data-id=' . $model->fact_id . '><i class="fa fa-download"></i></span>';
                            }

                            if($model->fact_estado == 'TIMBRADA'){
                                $html .= '<span class="btn btn-primary btn-sm btn-cancelar" title="Cancelar" data-id=' . $model->fact_id . '><i class="fa fa-ban"></i></span>';
                            }


                            $html .= '</div>';

                            return $html;
                        })
//                        ->filterColumn('ptmp_comentario', function($query, $keyword) {
//                                $query->whereRaw(" CONCAT(ptmp_comentario, ' ', ptmp_comentario_admin) like ?", ["%{$keyword}%"]);
//                            })

                        ->rawColumns(['fact_estado','actions'])
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
            ->addColumn(['data' => 'fact_id', 'name' => 'fact_id', 'title' => 'Folio', 'visible'=>true])
            ->addColumn(['data' => 'fact_fecha_emision', 'name' => 'fact_fecha_emision', 'title' => 'Emisión'])
            ->addColumn(['data' => 'fact_fecha_certificacion', 'name' => 'fact_fecha_certificacion', 'title' => 'Certificación'])
            ->addColumn(['data' => 'fact_nombre_receptor', 'name' => 'fact_nombre_receptor', 'title' => 'Receptor'])
            ->addColumn(['data' => 'fact_tipo_cambio', 'name' => 'fact_tipo_cambio', 'title' => 'TC', 'search'=>false])
            ->addColumn(['data' => 'fact_uuid', 'name' => 'fact_uuid', 'title' => 'UUID'])
            ->addColumn(['data' => 'fact_subtotal', 'name' => 'fact_subtotal', 'title' => 'Subtotal', 'search'=>false])
            ->addColumn(['data' => 'fact_iva', 'name' => 'fact_iva', 'title' => 'IVA', 'search'=>false])
            ->addColumn(['data' => 'fact_total', 'name' => 'fact_total', 'title' => 'Total', 'search'=>false])
            ->addColumn(['data' => 'fact_estado', 'name' => 'fact_estado', 'title' => 'Estado' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones' ]);


        return view('web.factura.index', compact('dataTable','local'));

    }

    public function form(Factura $factura = null, Request $request){

        $url = ($factura == null)? url('factura/insert') : url('factura/edit', $factura->getKey() );

        $series = \DB::table('c_serie')
                    ->select(['id' , 'descripcion'])
                    ->pluck('descripcion','id');

        $usocfdi = \DB::table('c_usocfdi')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $monedas = \DB::table('c_moneda')
            ->select(['id' , \DB::raw('codigo as descripcion') ])
            ->pluck('descripcion','id');

        $formaspago = \DB::table('c_formapago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $metodospago = \DB::table('c_metodopago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        ////////////////////////////////////////////////////////
        ///
        $productos = \DB::table('c_claveprodserv')
            ->select(['id' , \DB::raw('CONCAT(clave, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $unidades = \DB::table('c_claveunidad')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", nombre) as descripcion') ])
            ->pluck('descripcion','id');
        /////////////////////////////////////////////////////////

        return view('web.factura.form', compact('factura','url', 'series','usocfdi','monedas','formaspago','metodospago','productos','unidades'));
    }


    public function formAgregarConcepto(Request $request){

        $test ='test';

        $url =url('factura/validar-concepto');

        $productos = \DB::table('c_claveprodserv')
            ->select(['id' , \DB::raw('CONCAT(clave, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->orderBy('clave')
            ->pluck('descripcion','id');

        $unidades = \DB::table('c_claveunidad')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", nombre) as descripcion') ])
            ->orderBy('codigo','desc')
            ->pluck('descripcion','id');

        $conceptos = [
            'Gafetes' => 'Gafetes',
            'Reposición de gafetes' => 'Reposición de gafetes',
            'Renovación de gafetes' => 'Renovación de gafetes',
        ];

        $comprobantes = [''=>'Recuperando comprobantes capturados...'];

        return view('web.factura.form-agregar-concepto', compact('productos','unidades','url', 'conceptos', 'comprobantes'));

    }

    public function formSendMail(Factura $factura){

        if($factura->fact_estado != 'TIMBRADA'){
            return '<b class="text-danger">La factura debe estar timbrada</b>';
        }

//        dd($factura);

        $url =url('factura/do-send-mail');

        $receptor = \App\User::whereUsrLcalId($factura->Local->lcal_id)
            ->first();

        if($receptor != null){
            $receptor  = $receptor->email;
        }

        return view('web.factura.form-send-mail', compact('url', 'factura', 'receptor'));

    }

    public function validarConcepto(Request $request){

        if(! $this->validateAction('validar-concepto')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            $comprobantes = collect(ComprobantePago::getComprobantesDisponiblesParaFactura());

            $comprobante = $comprobantes->where('cpag_id',$this->data['fcdt_cpag_id'])->first();

            $pagado = $comprobante->cpag_importe_pagado;
            $facturado = $comprobante->importe_facturado;
            $importe_concepto = $this->data['fcdt_importe'];

            if(($facturado + $importe_concepto) > $pagado){
                return response() -> json($this->ajaxResponse(false,'El importe del concepto excede el importe del comprobante.'));
            }

            return response()->json($this -> ajaxResponse(true,'Datos validados!', $this->data));
        }

    }

    public function insert(Request $request)
    {

        if(! $this->validateAction('insert')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {

                $data = $this->data;

                // ---PROCESAMOS EL INPUT DE CONCEPTOS
                $uri_conceptos = $this->data['conceptos'];
                $uri_conceptos = urldecode($uri_conceptos);
                parse_str($uri_conceptos,$conceptos);
                $conceptos = $conceptos['conceptos'];
                //----------------------------------------

                $data_factura = \Arr::except($data,['fact_fecha_creacion','conceptos','total_importe','total_subtotal','total_iva']);

                $data_factura['fact_efecto_comprobante'] = 'I';
                $data_factura['fact_fecha_emision'] = date('Y-m-d H:i:s');
                $data_factura['fact_subtotal'] = $data['total_subtotal'];
                $data_factura['fact_iva'] = $data['total_iva'];
                $data_factura['fact_total'] = $data['total_importe'];
                $data_factura['fact_tipo_cambio'] = 1; //MXN
                $data_factura['fact_regimenfiscal_id'] = 1; //601 - General de Ley de Personas Morales
//                $data_factura['fact_test_mode'] = 1; //MXN


                if($data_factura['fact_moneda_id'] == 2){
                    $data_factura['fact_tipo_cambio'] = settings()->get('tipo_cambio'); //USD
                }

                $factura = Factura::create($data_factura);

                foreach($conceptos as $c){
                    $c['fcdt_fact_id'] = $factura->fact_id;
                    FacturaDetalle::create($c);

                    $comprobante = ComprobantePago::find($c['fcdt_cpag_id']);
                    $comprobante->cpag_fact_id = $factura->fact_id;
                    $comprobante->save();
                 }

                \DB::commit();
                return response()->json($this -> ajaxResponse(true,'Factura <b>CREADA</b> correctamente.'));


            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() . $e -> getFile() . $e -> getLine()  ));
            }



        }
    }


    /////////////////////////////////////////////////////////////////////////////////////


    public function indexContabilidad(Request $request, Builder $htmlBuilder)
    {

//        //detrminamos el local
//        $usuario = \Auth::getUser();
//        if($usuario->usr_lcal_id == null){
//            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
//        }
//        $local = $usuario->Local;
        // dd($usuario,$local);

        if ($request->ajax()) {
            return Datatables::of(
                Factura::select(['fact_id', \DB::raw(" DATE(fact_fecha_emision) as fact_fecha_emision"),'fact_fecha_certificacion', 'fact_rfc_receptor',
                    'fact_nombre_receptor','fact_tipo_cambio', 'fact_uuid', 'fact_cpag_id',
                    'fact_subtotal', 'fact_iva', 'fact_total',
                    \DB::raw("CONCAT( c_usocfdi.codigo, '',c_usocfdi.descripcion) as uso_cfdi"),
                    \DB::raw("CONCAT( c_formapago.codigo, '',c_formapago.descripcion) as forma_pago"),
                    'fact_estado', 'fact_test_mode'])
                    ->join('c_usocfdi','fact_usocfdi_id','c_usocfdi.id')
                    ->join('c_formapago','fact_formapago_id','c_formapago.id')
                ->leftJoin('comprobantes_pago','fact_cpag_id','=','cpag_id')
//                ->whereIn('fact_estado',['PRECAPTURADA','CAPTURADA'])
                ->whereRaw("fact_estado = 'CAPTURADA' OR ( fact_estado = 'PRECAPTURADA' AND cpag_estado = 'VALIDADO'  )")
//                    ->where('fact_rfc_receptor','XAXX010101000')
//                    ->whereFactLcalId($local->lcal_id)
            )

                ->editColumn('fact_estado', function(Factura $model) {

                    $color = 'badge-primary';
                    if($model->fact_estado  == 'CAPTURADA') $color = 'badge-warning';
                    if($model->fact_estado  == 'TIMBRADA')  $color = 'badge-success';
                    if($model->fact_estado  == 'CANCELADA') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge '. $color .'">'. $model->fact_estado .'</small>';
                    $html.='</div>';
                    return $html;

                })

                ->addColumn('actions', function (Factura $model) {

                    $html = '<div class="btn-group">';

                    if($model->fact_estado == 'PRECAPTURADA'){

//                        dd($model);
                        $html .= '<a class="btn btn-primary btn-sm" title="Mostrar comprobante de pago"
                                        target="_blank" href="'.asset('storage/comprobantes/'.$model->Comprobante->cpag_file).'" >
                                        <i class="ti-link"></i></a>';
//                        $html .= '<span class="btn btn-primary btn-sm btn-validar" title="Validar Comprobante" data-id=' . $model->fact_cpag_id. '><i class="ti-check"></i></span>';

                        $html .= '<span class="btn btn-primary btn-sm btn-capturar" title="Capturar" data-id=' . $model->fact_id . '><i class="ti-save"></i></span>';

                    }else{

                        $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Mostrar PDF Capturado" data-id=' . $model->fact_id . '><i class="fa fa-file-pdf-o"></i></span>';

//                        if($model->fact_rfc_receptor == 'XAXX010101000'){
                            $html .= '<span class="btn btn-primary btn-sm btn-comprobantes" title="Listado de comprobantes" data-id=' . $model->fact_id . '><i class="fa fa-list"></i></span>';
//                        }

                        if($model->fact_estado == 'CAPTURADA'){
                            $html .= '<span class="btn btn-primary btn-sm btn-editar" title="Editar" data-id=' . $model->fact_id. '><i class="fa fa-edit"></i></span>';
                            $html .= '<span class="btn btn-primary btn-sm btn-timbrar" title="Timbrar" data-id=' . $model->fact_id. '><i class="fa fa-bell"></i></span>';
                            $html .= '<span class="btn btn-primary btn-sm btn-eliminar" title="Eliminar" data-id=' . $model->fact_id. '><i class="fa fa-times"></i></span>';
                        }

                        if($model->fact_estado != 'CAPTURADA'){
                            $html .= '<span class="btn btn-primary btn-sm btn-xml" title="Descargar XML" data-id=' . $model->fact_id . '><i class="fa fa-download"></i></span>';
                        }

                        if($model->fact_estado == 'TIMBRADA'){
                            $html .= '<span class="btn btn-primary btn-sm btn-cancelar" title="Cancelar" data-id=' . $model->fact_id . '><i class="fa fa-ban"></i></span>';
                        }

                    }







                    $html .= '</div>';

                    return $html;
                })
//                        ->filterColumn('ptmp_comentario', function($query, $keyword) {
//                                $query->whereRaw(" CONCAT(ptmp_comentario, ' ', ptmp_comentario_admin) like ?", ["%{$keyword}%"]);
//                            })

                ->rawColumns(['fact_estado','actions'])
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
            ->addColumn(['data' => 'fact_id', 'name' => 'fact_id', 'title' => 'Folio', 'visible'=>false])
            ->addColumn(['data' => 'fact_cpag_id', 'name' => 'fact_cpag_id', 'title' => 'Comprobante', 'visible'=>true])
            ->addColumn(['data' => 'fact_fecha_emision', 'name' => 'fact_fecha_emision', 'title' => 'Creación'])
//            ->addColumn(['data' => 'fact_fecha_certificacion', 'name' => 'fact_fecha_certificacion', 'title' => 'Certificación'])
            ->addColumn(['data' => 'fact_nombre_receptor', 'name' => 'fact_nombre_receptor', 'title' => 'Receptor'])
//            ->addColumn(['data' => 'fact_tipo_cambio', 'name' => 'fact_tipo_cambio', 'title' => 'TC', 'search'=>false])
//            ->addColumn(['data' => 'fact_uuid', 'name' => 'fact_uuid', 'title' => 'UUID'])
//            ->addColumn(['data' => 'fact_subtotal', 'name' => 'fact_subtotal', 'title' => 'Subtotal', 'search'=>false])
//            ->addColumn(['data' => 'fact_iva', 'name' => 'fact_iva', 'title' => 'IVA', 'search'=>false])

            ->addColumn(['data' => 'uso_cfdi', 'name' => 'uso_cfdi', 'title' => 'Uso CFDI', 'search'=>false])
            ->addColumn(['data' => 'forma_pago', 'name' => 'forma_pago', 'title' => 'Forma de Pago', 'search'=>false])


            ->addColumn(['data' => 'fact_total', 'name' => 'fact_total', 'title' => 'Total', 'search'=>false])
            ->addColumn(['data' => 'fact_estado', 'name' => 'fact_estado', 'title' => 'Estado' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones' ]);


        return view('web.factura.index-contabilidad', compact('dataTable'));

    }

    public function formContabilidad(Factura $factura = null, Request $request){

        $url = ($factura == null)? url('factura/insert') : url('factura/edit', $factura->getKey() );

        $series = \DB::table('c_serie')
            ->select(['id' , 'descripcion'])
            ->pluck('descripcion','id');

        $usocfdi = \DB::table('c_usocfdi')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $monedas = \DB::table('c_moneda')
            ->select(['id' , \DB::raw('codigo as descripcion') ])
            ->pluck('descripcion','id');

        $formaspago = \DB::table('c_formapago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $metodospago = \DB::table('c_metodopago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $locales = local::selectRaw('lcal_id , concat(lcal_identificador, " ", lcal_nombre_comercial) as lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial','lcal_id')
            ->put( '' ,'SELECCIONE UNA OPCIÓN');


        ////////////////////////////////////////////////////////

        $productos = \DB::table('c_claveprodserv')
            ->select(['id' , \DB::raw('CONCAT(clave, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $unidades = \DB::table('c_claveunidad')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", nombre) as descripcion') ])
            ->pluck('descripcion','id');
        /////////////////////////////////////////////////////////

        return view('web.factura.form-contabilidad', compact('factura','url', 'series','usocfdi',
            'monedas','formaspago','metodospago','productos','unidades','locales'));
    }

    public function formContabilidadGlobal(Factura $factura = null, Request $request){

        $url = ($factura == null)? url('factura/insert') : url('factura/edit', $factura->getKey() );

        $series = \DB::table('c_serie')
            ->select(['id' , 'descripcion'])
            ->pluck('descripcion','id');

        $usocfdi = \DB::table('c_usocfdi')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $monedas = \DB::table('c_moneda')
            ->select(['id' , \DB::raw('codigo as descripcion') ])
            ->pluck('descripcion','id');

        $formaspago = \DB::table('c_formapago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $metodospago = \DB::table('c_metodopago')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        ////////////////////////////////////////////////////////
        ///
        $productos = \DB::table('c_claveprodserv')
            ->select(['id' , \DB::raw('CONCAT(clave, " ", descripcion) as descripcion') ])
            ->where('activo',1)
            ->pluck('descripcion','id');

        $unidades = \DB::table('c_claveunidad')
            ->select(['id' , \DB::raw('CONCAT(codigo, " ", nombre) as descripcion') ])
            ->pluck('descripcion','id');
        /////////////////////////////////////////////////////////

        $comprobantes =  ComprobantePago::getFacturablesGlobal();

        return view('web.factura.form-contabilidad-global',
                        compact('factura','url', 'series','usocfdi',
                            'monedas','formaspago','metodospago','productos',
                            'unidades','comprobantes'));
    }

    public function formEditarSm(Factura $factura, Request $request){

        if($factura->fact_estado != 'CAPTURADA'){
            return '<b class="text-danger">La factura debe estar en estado: CAPTURADA</b>';
        }

        $url =url('factura/do-editar-sm');

        $formas_pago = [
            '1' => 'Efectivo',
            '3' => 'Cheque nominativo',
            '4' => 'Transferencia electrónica',
        ];

        $usos_cfdi = [
            '1' => 'Gastos en general',
            '2' => 'Por definir'
        ];

        return view('web.factura.form-editar-sm', compact('url', 'factura','formas_pago','usos_cfdi'));


    }

    /////////////////////////////////////////////////////////////////////////////////////

    public function indexAlmacenFacturas(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = Factura::select(['fact_id', \DB::raw('DATE(fact_fecha_emision) as fact_fecha_emision'),\DB::raw('DATE(fact_fecha_certificacion) as fact_fecha_certificacion'),'fact_cpag_id',
                'fact_nombre_receptor','fact_tipo_cambio', 'fact_uuid','fact_rfc_receptor','fact_lcal_id','fact_folio',
                'fact_subtotal', 'fact_iva', 'fact_total',\DB::raw('c_serie.descripcion as fact_serie'),
                'fact_estado', 'fact_test_mode'])
                ->join('c_serie','fact_serie_id','c_serie.id')
                ->whereIn('fact_estado',['TIMBRADA','CANCELADA']);

            ////// F I L T R O S //////////////////////////////////////////////////////////////////
            if ($request->has('filter_estado') && $request->get('filter_estado') != "" ) {
                $filtro = $request->get('filter_estado');
                $records -> whereFactEstado($filtro);
            }

            if ($request->has('filter_folio') && $request->get('filter_folio') != "" ) {
                $filtro = $request->get('filter_folio');
                $records -> whereFactId($filtro);
            }


            if ($request->has('filter_fecha_inicio') && $request->get('filter_fecha_inicio') != "" ) {
                $filtro = $request->get('filter_fecha_inicio');
                $records -> whereRaw(" DATE(fact_fecha_emision) >= '".$filtro."' ");
            }

            if ($request->has('filter_fecha_fin') && $request->get('filter_fecha_fin') != "" ) {
                $filtro = $request->get('filter_fecha_fin');
                $records -> whereRaw(" DATE(fact_fecha_emision) <= '".$filtro."' ");
            }

            if ($request->has('filter_receptor') && $request->get('filter_receptor') != "" ) {
                $filtro = $request->get('filter_receptor');
                $records -> whereRaw("( fact_rfc_receptor LIKE '%".$filtro."%' or fact_nombre_receptor LIKE '%".$filtro."%' )");
            }


            ////// E N D  F I L T R O S //////////////////////////////////////////////////////////////////


            return Datatables::of( $records )

                ->editColumn('fact_estado', function(Factura $model) {

                    $color = 'badge-primary';
//                    if($model->fact_estado  == 'CAPTURADA') $color = 'badge-warning';
                    if($model->fact_estado  == 'TIMBRADA')  $color = 'badge-success';
                    if($model->fact_estado  == 'CANCELADA') $color = 'badge-danger';

                    $html = '<div class="text-center"><small class="badge '. $color .'">'. $model->fact_estado .'</small>';
                    $html.='</div>';
                    return $html;

                })

                ->editColumn('fact_uuid', function(Factura $model) {

                    if( config('app.debug') == true && $model->fact_test_mode == 1){
                        return '<span class="text-danger">' . $model->fact_uuid . '</span>';
                    }

                    return $model->fact_uuid;

                })

                ->addColumn('tipo', function (Factura $model) {

                    if($model->fact_rfc_receptor == 'XAXX010101000')
                        return 'COMPROBANTE GLOBAL';

                    return 'FACTURA';


                })

                ->addColumn('actions', function (Factura $model) {

                    $html = '<div class="btn-group">';

                    if($model->fact_cpag_id != null):
                    $html.='<a class="btn btn-primary btn-sm" title="Mostrar comprobante de pago"
                                        target="_blank" href="'.asset('storage/comprobantes/'.$model->Comprobante->cpag_file).'" >
                                        <i class="ti-link"></i></a>';
                    endif;


//                    if($model->fact_estado == 'CAPTURADA'){
//                        $html .= '<span class="btn btn-primary btn-sm btn-timbrar" title="Timbrar" data-id=' . $model->fact_id. '><i class="fa fa-bell"></i></span>';
//
//                    }

                    $html .= '<span class="btn btn-primary btn-sm btn-pdf" title="Formato PDF" data-id=' . $model->fact_id . '><i class="fa fa-file-pdf-o"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-comprobantes" title="Listado de comprobantes" data-id=' . $model->fact_id . '><i class="fa fa-list"></i></span>';
                    $html .= '<span class="btn btn-primary btn-sm btn-xml" title="Descargar XML" data-id=' . $model->fact_id . '><i class="fa fa-download"></i></span>';


                    if($model->fact_estado == 'TIMBRADA'){

                        if($model->fact_lcal_id > 0){
                            $html .= '<span class="btn btn-primary btn-sm btn-send-mail" title="Enviar por correo" data-id=' . $model->fact_id . '><i class="fa fa-envelope"></i></span>';
                        }
                        $html .= '<span class="btn btn-primary btn-sm btn-cancelar" title="Cancelar" data-id=' . $model->fact_id . '><i class="fa fa-ban"></i></span>';
                    }


                    $html .= '</div>';

                    return $html;
                })

                ->filterColumn('fact_uuid', function($query, $keyword) {
                        $query->whereRaw(" fact_uuid like ?", ["%{$keyword}%"]);
                    })

                ->rawColumns(['fact_estado','actions','tipo','fact_uuid'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select'=>false,
            'autoWidth'  => false,
            'language' => [
                'url'=> asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order'=>[[4,'desc']]
        ]);

        $htmlBuilder->ajax( [
            'url'=> url('factura/almacen-facturas') ,
            'data'=> 'function(d){
                d.filter_estado = $(\'select[name=filter_estado]\').val();
                d.filter_fecha_inicio = $(\'input[name=filter_fecha_inicio]\').val();
                d.filter_fecha_fin = $(\'input[name=filter_fecha_fin]\').val();
                d.filter_folio = $(\'input[name=filter_folio]\').val();
                d.filter_receptor = $(\'input[name=filter_receptor]\').val();
                }'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'fact_id', 'name' => 'fact_id', 'title' => 'id', 'visible'=>false])

            ->addColumn(['data' => 'fact_fecha_emision', 'name' => 'fact_fecha_emision', 'title' => 'Emisión'])
            ->addColumn(['data' => 'fact_fecha_certificacion', 'name' => 'fact_fecha_certificacion', 'title' => 'Certificación'])

            ->addColumn(['data' => 'fact_serie', 'name' => 'fact_serie', 'title' => 'Serie', 'visible'=>true])
            ->addColumn(['data' => 'fact_folio', 'name' => 'fact_folio', 'title' => 'Folio', 'visible'=>true])

            ->addColumn(['data' => 'tipo', 'name' => 'tipo', 'title' => 'Tipo', 'visible'=>false])
            ->addColumn(['data' => 'fact_nombre_receptor', 'name' => 'fact_nombre_receptor', 'title' => 'Receptor'])

            ->addColumn(['data' => 'fact_subtotal', 'name' => 'fact_subtotal', 'title' => 'Subtotal', 'search'=>false])
            ->addColumn(['data' => 'fact_iva', 'name' => 'fact_iva', 'title' => 'IVA', 'search'=>false])
            ->addColumn(['data' => 'fact_total', 'name' => 'fact_total', 'title' => 'Total', 'search'=>false])
            ->addColumn(['data' => 'fact_tipo_cambio', 'name' => 'fact_tipo_cambio', 'title' => 'TC', 'search'=>false])

            ->addColumn(['data' => 'fact_uuid', 'name' => 'fact_uuid', 'title' => 'UUID'])

            ->addColumn(['data' => 'fact_estado', 'name' => 'fact_estado', 'title' => 'Estado' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones' ]);


        return view('web.factura.index-almacen-facturas', compact('dataTable'));

    }


    /////////////////////////////////////////////////////////////////////////////////////

    public function doEliminar(Factura $factura){

        if($factura->fact_estado != 'CAPTURADA'){
            return response() -> json($this->ajaxResponse(false,'Solo se pueden eliminar facturas en estado <b>CAPTURADA</b>') );
        }

        $factura->fact_estado = 'ELIMINADA';
        $factura->save();

        $factura->delete();

        //desligamos los comprobantes a la factura
        ComprobantePago::where('cpag_fact_id',$factura->fact_id)
            ->update(['cpag_fact_id'=>null]);

        return response() -> json($this->ajaxResponse(true,'Factura eliminada.') );

    }

    public function doEditarSm(Request $request){

        if(! $this->validateAction('do-edit-sm')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {

//                dd($this->data);

                $factura = Factura::findOrFail($this->data['fact_id']);

                $factura->fact_usocfdi_id = $this->data['fact_usocfdi_id'];
                $factura->fact_formapago_id = $this->data['fact_formapago_id'];
                $factura->save();

                if($factura->Comprobante != null){
                    activity()->disableLogging();
                    $comprobante = $factura->Comprobante;
                    $comprobante->cpag_uso_cfdi = $factura->fact_usocfdi_id;
                    $comprobante->cpag_forma_pago = $factura->fact_formapago_id;
                    $comprobante->save();
                    activity()->enableLogging();
                }

                \DB::commit();

                return response()->json($this -> ajaxResponse(true,'Factura editada correctamente.'));

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() ));
            }




        }

    }

    /**
     * Cambia el estado de la factura de PRECAPTURADA A CAPTURADA
     * @param Factura $factura
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function doCapturar(Factura $factura){

        if($factura->fact_estado != 'PRECAPTURADA'){
            return response() -> json($this->ajaxResponse(false,'La factura debe estar en estado: <b>PRECAPTURADA</b>') );
        }

        if($factura->Comprobante != null){
            if($factura->Comprobante->cpag_estado != 'VALIDADO'){
                return response() -> json($this->ajaxResponse(false,'Primero debe <b> VALIDAR EL COMPROBANTE</b>') );
            }
        }

        $factura->fact_estado = 'CAPTURADA';
        $factura->save();

//        $factura->delete();
//
//        ComprobantePago::where('cpag_fact_id',$factura->fact_id)
//            ->update(['cpag_fact_id'=>null]);

        return response() -> json($this->ajaxResponse(true,'Cambio de estado exitoso.') );


    }

    public function doTimbrar(Factura $factura){

        if($factura->fact_uuid != null){
            return response() -> json($this->ajaxResponse(false,'La factura ya fue timbrada anteriormente'));
        }

        try{
            //establecemos la hora  de CDMX que es la usa el PAC/SAT
            date_default_timezone_set('America/Mexico_City');

            //construimos el XML
            $Builder = new CfdiConstructor();

            $test_mode = settings()->get('cfdi_test_mode');

            if($test_mode == 1){
                $Builder->modoPruebas();
            }else{
                $Builder->modoProductivo();
            }


            $folio =  Factura::obtenerSiguienteFolio();
            $fecha_emision = date("Y-m-d")."T".date("H:i:s");
            ///////////////////////////////////////////////////////////////////////
            // HEAD SECTION

            $Builder->setAtributoFactura('Folio', $folio);
            $Builder->setAtributoFactura('LugarExpedicion', $factura->fact_lugar_expedicion);

            $Builder->setAtributoFactura('Serie', $factura->Serie->descripcion);
            $Builder->setAtributoFactura('Fecha', $fecha_emision);


            $Builder->setAtributoFactura('SubTotal', $factura->fact_subtotal );
            $Builder->setAtributoFactura('Total', $Builder->fnumero( $factura->fact_total ));

            $Builder->setAtributoFactura('Moneda',$factura->Moneda->codigo);
            if($factura->Moneda->codigo != "MXN"){
                $Builder->setAtributoFactura('TipoCambio',$factura->fact_tipo_cambio);
            }


            $Builder->setAtributoFactura('FormaPago',$factura->FormaPago->codigo );
            $Builder->setAtributoFactura('MetodoPago',$factura->MetodoPago->codigo);

            ///////////////////////////////////////////////////////////////////////
            // RECEPTOR SECTION

            $Builder->setAtributoReceptor('UsoCFDI',$factura->UsoCfdi->codigo);

            $Builder->setAtributoReceptor('Rfc',$factura->fact_rfc_receptor);
            $Builder->setAtributoReceptor('Nombre',$factura->fact_nombre_receptor);


            ///////////////////////////////////////////////////////////////////////
            // ITEM SECTION

            $conceptos = $factura->Conceptos;

            foreach($conceptos as $facturaDetalle):

                $cod_unidad = $facturaDetalle->Unidad->codigo;
                $unidad = strtoupper($facturaDetalle->Unidad->nombre);
                $producto = $facturaDetalle->Producto->clave;
                $descripcion = strtoupper($facturaDetalle->fcdt_concepto);

                $valor_unitario = round( $facturaDetalle->fcdt_precio / $facturaDetalle->fcdt_cantidad ,2);

                $data_concepto = [
                    'ValorUnitario' => $valor_unitario,
                    'Unidad' => $unidad,
                    'Importe' => $facturaDetalle->fcdt_precio,
                    'Descripcion' => $descripcion,
                    'ClaveUnidad' => $cod_unidad,
                    'ClaveProdServ' => $producto,
                    'Cantidad' => $facturaDetalle->fcdt_cantidad,


                    'Base' => $facturaDetalle->fcdt_precio,
                    'IVA' => $Builder->fnumero( $facturaDetalle->fcdt_iva ) ,
                ];

                $Builder->addConcepto($data_concepto);

            endforeach;

            ///////////////////////////////////////////////////////////////////////
            // GENERAR XML

            $xmlOrig  = $Builder->generarXML();

            if($xmlOrig == false){
                throw new \Exception("Error al formar archivo XML ");
                return false;
            }



            //Guardamos el XML previo en disco
            $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cfdi' . DIRECTORY_SEPARATOR . 'FD' . $factura->fact_id . DIRECTORY_SEPARATOR;
            if (!is_dir($path)) {
                // dir doesn't exist, make it
                mkdir($path,0777,true);
//                chmod($path,0775);
            }

            $filename = $path.'PRE_'.date('YmdHis').'.xml';
            file_put_contents($filename, $xmlOrig);

            ///////////////////////////////////////////////////////////////////////
            // CERTIFICACION CFDI

            $Timbrador = new CfdiTimbrador();

            if($test_mode == 1){
                $Timbrador->modo_pruebas();
            }else{
                $Timbrador->modo_productivo();
            }

            //forzar pruebas
//            $Timbrador->modo_pruebas();


            $result = $Timbrador->timbra($factura->fact_id, $xmlOrig);

            if($result['res'] == 1){

                //guardamos el codigo qr
                $file_qr =  $path.'FD_'.$factura->fact_id.'_'.$result['uuid'].'_codigoQr.jpg';
                file_put_contents($file_qr, $result['codigoQr']);
                $factura->fact_qr_code_path = $file_qr;

                //guardamos el xml timbrado
                $file_xml =  $path.'FD_'.$factura->id.'_'.$result['uuid'].'.xml';
                file_put_contents($file_xml, $result['xmlTimbrado']);
                $factura->fact_xml_path = $file_xml;

                //actualizamos la fecha de emisión
                $factura->fact_fecha_emision = str_replace("T"," ",$fecha_emision);

                //seteamos los campos faltantes en factura
                $factura->fact_uuid = $result['uuid'];
                $factura->fact_cadena_original = $result['cadenaOriginal'];
                $factura->fact_estado = 'TIMBRADA';

                $factura->fact_folio = $folio;

                //actualizamos la fecha de certificación al recibir el resultado
                $factura->fact_fecha_certificacion = date('Y-m-d H:i:s');

                //si esta en modo pruebas avisar en bd
                if($test_mode == 1){
                    $factura->fact_test_mode = 1;
                }

                $factura->save();

                $response_message = "Timbrado exitoso.";
                $response_data = [];

                if($factura->fact_cpag_id > 0){
                    try{
                        // N o t i f i c a ci o n -------------------------------------------------------------
                        $locatarios = User::role('LOCATARIO')
                            ->whereUsrLcalId($factura->Comprobante->cpag_lcal_id)
                            ->get();

                        $pdfData = $this->doFormatoPdf($factura);

                        \Notification::send($locatarios , new NotificacionFacturaTimbrada($factura, $pdfData));

                    }catch (\Exception $e){
                        $response_message.= ' Error al notificar';
                        $response_data['notification_error'] = $e->getMessage();
                    }
                }

                return response() -> json($this->ajaxResponse(true, $response_message, $response_data));

            }else{

                $error =  'Error al timbrar: '.$result['msg'];
                return response() -> json($this->ajaxResponse(false,$error));

            }

        }catch (\Exception $e){

            return response() -> json($this->ajaxResponse(false,$e->getMessage()));

        }



    }

    public function doCancelar(Factura $factura){

        try{

            $Timbrador = new CfdiTimbrador();

            $test_mode = settings()->get('cfdi_test_mode');

            if($test_mode == 1){
                $Timbrador->modo_pruebas();
            }else{
                $Timbrador->modo_productivo();
            }


            if($factura->fact_test_mode == 0){
                $result = $Timbrador->cancela($factura->fact_uuid);
            }else{
                //si la factura fue timbrada en pruebas cancelar en automático
                $result = ['res'=>1];
            }


            if($result['res'] == 1 || ($result['res'] == 0 && $result['msg']=='UUID Previamente cancelado.' )){

                $factura->fact_estado = 'CANCELADA';
                $factura->save();

                $clon = $this->clonarFactura($factura);

                ComprobantePago::where('cpag_fact_id',$factura->fact_id)
                    ->update(['cpag_fact_id'=>$clon->fact_id]);

                return response() -> json($this->ajaxResponse(true,'Se canceló el CFDI exitosamente.',$result) );

            }else{

                $error =  'Error al cancelar: '.$result['msg'];
                return response() -> json($this->ajaxResponse(false,$error) );
            }

        }catch (\Exception $e){
            return response() -> json( $this->ajaxResponse(false,$e->getMessage()) );
        }


    }

    /**
     * Descarga archivo XML de la factura
     * @param Factura $factura
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function doDownloadXml(Factura $factura){

        if(!is_file($factura->fact_xml_path)){
            abort(404,'El archivo no existe');
        }

        return response()->download($factura->fact_xml_path,$factura->fact_uuid.'.xml');

    }

    public function doFormatoPdf(Factura $factura){


        if($factura->fact_uuid == ""){
//            return response() -> json($this->ajaxResponse(false,'La factura no se ha timbrado todavía.'));
        }

        $report = new FormatoFacturaPDF(null,true,false);
        $report->setFactura($factura);

        return $report->exec();

    }

    public function doListadoComprobantesPdf(Factura $factura){


        $report = new FacturaListadoComprobantesPDF(null,true,false);
        $report->setFactura($factura);

        return $report->exec();

    }

    public function doExportExcel(Request $request){


        $records = Factura::select([
            \DB::raw(" date(fact_fecha_emision) "),
            \DB::raw(" date(fact_fecha_certificacion) "),
            \DB::raw('c_serie.descripcion as fact_serie'),
            'fact_folio',
//            'fact_rfc_receptor',
            'fact_nombre_receptor',
            'fact_subtotal',
            'fact_iva',
            'fact_total',
            'fact_tipo_cambio',
            'fact_uuid',
            'fact_estado'])
            ->join('c_serie','fact_serie_id','c_serie.id')
            ->whereIn('fact_estado',['TIMBRADA','CANCELADA']);


        ////// F I L T R O S //////////////////////////////////////////////////////////////////
        if ($request->has('filter_estado') && $request->get('filter_estado') != "" ) {
            $filtro = $request->get('filter_estado');
            $records -> whereFactEstado($filtro);
        }

        if ($request->has('filter_folio') && $request->get('filter_folio') != "" ) {
            $filtro = $request->get('filter_folio');
            $records -> whereFactId($filtro);
        }


        if ($request->has('filter_fecha_inicio') && $request->get('filter_fecha_inicio') != "" ) {
            $filtro = $request->get('filter_fecha_inicio');
            $records -> whereRaw(" DATE(fact_fecha_emision) >= '".$filtro."' ");
        }

        if ($request->has('filter_fecha_fin') && $request->get('filter_fecha_fin') != "" ) {
            $filtro = $request->get('filter_fecha_fin');
            $records -> whereRaw(" DATE(fact_fecha_emision) <= '".$filtro."' ");
        }

        if ($request->has('filter_receptor') && $request->get('filter_receptor') != "" ) {
            $filtro = $request->get('filter_receptor');
            $records -> whereRaw("( fact_rfc_receptor LIKE '%".$filtro."%' or fact_nombre_receptor LIKE '%".$filtro."%' )");
        }

        ////// E N D  F I L T R O S //////////////////////////////////////////////////////////////////
        $records->orderBy('fact_folio','desc');

        $records = $records->get();
        try{
            return \Excel::download(new FacturasExport($records), 'facturas_'.date('Ymd_His').'.xlsx');
        }catch (\Exception $e){
            dd($e);
        }







    }


    public function doSendMail(Request $request){

        if(! $this->validateAction('do-send-mail')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            try{

                $factura = Factura::findOrFail($this->data['fact_id']);
                $user = User::whereEmail($this->data['email_to'])->first();

                if($user != null){

                    $pdfData = $this->doFormatoPdf($factura);

                    \Notification::send( $user, new NotificacionFacturaTimbrada($factura, $pdfData));

                    return response() -> json( $this->ajaxResponse(true, 'Proceso exitoso.') );

                }else{

                    return response() -> json( $this->ajaxResponse(false, 'No se encontró ningun usuario con el correo indicado.') );
                }

            }catch (\Exception $e){
                return response() -> json( $this->ajaxResponse(false,$e->getMessage()) );
            }



        }


    }


    public function getLocalData(Local $local){


        $comprobantes = ComprobantePago::getFacturablesDirectos($local);

        $vista_comprobantes = view('web.factura.tabla-comprobantes-directos',compact('comprobantes'))
                              ->render();

        return response() -> json(compact('comprobantes','local', 'vista_comprobantes'));


    }


    /**
     * Duplica una factura con sus conceptos y la deja es status CAPTURADA
     * @param Factura $facturaOriginal
     * @return Factura
     */
    private function clonarFactura(Factura $facturaOriginal){

        //Copiamos la factura para pasarla a estado CAPTURADA
        $except_fields = [
                'fact_uuid','fact_folio','fact_test_mode',
                'fact_fecha_certificacion','fact_cadena_original',
                'fact_qr_code_path','fact_xml_path'
        ];
        $clonFactura = $facturaOriginal->replicate($except_fields);
        $clonFactura->fact_estado = 'CAPTURADA';
        $clonFactura->save();

        foreach($facturaOriginal->Conceptos as $c){

            $c_clon = $c->replicate();
            $c_clon->fcdt_fact_id = $clonFactura->fact_id;
            $c_clon->save();

        }


        return $clonFactura;


    }

}
