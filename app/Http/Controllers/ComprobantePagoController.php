<?php

namespace App\Http\Controllers;

use App\CSerie;
use App\Factura;
use App\FacturaDetalle;
use App\GafeteEstacionamiento;
use App\Notifications\ComprobanteRechazado;
use App\Notifications\ComprobanteValidado;
use App\SolicitudGafete;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Routing\Annotation\Route;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\Local;
use App\ComprobantePago;

class ComprobantePagoController extends Controller
{

    protected $rules = [
        'insert' => [
            'cpag_fecha_pago' => 'required|date_format:Y-m-d',
            'cpag_lcal_id' => 'required|exists:locales,lcal_id',
//            'cpag_tipo'    => 'required',
//            'cpag_costo_unitario'    => 'required|numeric',
//            'cpag_cantidad_pv'        => 'required|integer|min:0|max:100',
//            'cpag_cantidad_rp'        => 'required|integer|min:0|max:100',
            'cpag_importe_pagado' => 'required|numeric|min:1|max:20000',
            'cpag_folio_bancario' => ['required', 'digits_between:3,7'],
//            'cpag_aut_bancario'    => 'required|digits:6',


            'cpag_requiere_factura' => 'required',
            'cpag_uso_cfdi' => 'required',
            'cpag_forma_pago' => 'required',
            'cpag_file' => 'required|file|max:2560',

            'cpag_cantidad_letra' => 'required'

//            'cpag_comprobante'      => 'required|file|max:1024'
        ],

//        'edit' => [
//            'lcal_id'    => 'required|exists:locales,lcal_id',
//            'lcal_nombre_comercial'    => 'required',
//            'lcal_rfc'    => 'required',
//            'lcal_nombre_responsable'      => 'required',
//            'lcal_identificador'  => 'required',
//            'lcal_espacios_est'   => 'nullable|numeric',
//            'lcal_gafetes_gratis'    => 'nullable|numeric',
//            'lcal_tipo'      => 'required',
//            'lcal_cacs_id'      => 'required',
//        ],


    ];

    protected $etiquetas = [
        'cpag_fecha_pago' => 'Fecha de Pago',
        'cpag_lcal_id' => 'Local',
        'cpag_tipo' => 'Tipo',
        'cpag_costo_unitario' => 'Costo unitario',
        'cpag_cantidad_pv' => 'Cantidad de solicitudes PRIMERA VEZ',
        'cpag_cantidad_rp' => 'Cantidad de solicitudes REPOSICIÓN',
        'cpag_importe_pagado' => 'Importe pagado',
        'cpag_folio_bancario' => 'Folio',
        'cpag_aut_bancario' => 'Referencia',
        'cpag_comprobante' => 'Comprobante',
        'cpag_file' => 'Archivo de comprobante original',

        'cpag_requiere_factura' => 'Facturar',
        'cpag_uso_cfdi' => 'Uso CFDI',
        'cpag_forma_pago' => 'Forma de pago',
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


    public function indexContabilidad(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {

            $records = ComprobantePago::select(['cpag_id', 'cpag_lcal_id', 'cpag_fecha_pago',
                \DB::raw(' CONCAT("$ ",FORMAT(cpag_importe_pagado,2)) as cpag_importe_pagado'), 'cpag_forma_pago',
                \DB::raw(' CONCAT(c_formapago.codigo," ",c_formapago.descripcion) as forma_pago '),
                'lcal_nombre_comercial', 'lcal_razon_social',
                'cpag_folio_bancario', 'cpag_aut_bancario', 'cpag_file', 'cpag_requiere_factura'])
                ->join('locales', 'cpag_lcal_id', 'lcal_id')
                ->join('c_formapago', 'cpag_forma_pago', 'c_formapago.id')
                ->whereIn('cpag_estado', ['PREVALIDADO']);

            if ($request->has('filter_local') && $request->get('filter_local') > 0) {
                $filtro = $request->get('filter_local');
                $records->whereCpagLcalId($filtro);
            }

            return Datatables::of($records)
                ->editColumn('cpag_requiere_factura', function (ComprobantePago $model) {

                    if ($model->cpag_requiere_factura == 1) {
                        return '<span class="badge badge-primary"> <i class="fa fa-check"></i> SI</span>';
                    } else {
                        return '<span class="badge badge-inverse"> <i class="fa fa-times"></i> NO </span>';
                    }

                })
                ->addColumn('actions', function (ComprobantePago $model) {

                    $html = '<div class="btn-group">';

                    $html .= '<span class="btn btn-primary btn-sm btn-validar-comprobante" title="Validar Comprobante" data-id=' . $model->cpag_id . '><i class="fa fa-check-square-o"></i></span>';

                    if ($model->cpag_file != "") {
                        $html .= '<a class="btn btn-primary btn-sm btn-comprobante-original"
                                href="' . asset('storage/comprobantes/' . $model->cpag_file) . '" target="_blank"
                                title="Comprobante Original"><i class="fa fa-file-pdf-o"></i></a>';
                    }

                    $html .= '</div>';

                    return $html;

                })
                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['actions', 'cpag_requiere_factura'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => false,
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $htmlBuilder->ajax([
            'url' => url('comprobante-pago/'),
            'data' => 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'cpag_id', 'name' => 'cpag_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local',])
            ->addColumn(['data' => 'cpag_fecha_pago', 'name' => 'cpag_fecha_pago', 'title' => 'Fecha Pago', 'search' => false, 'width' => '10%'])
            ->addColumn(['data' => 'cpag_folio_bancario', 'name' => 'cpag_folio_bancario', 'title' => 'Folio', 'search' => true])
            ->addColumn(['data' => 'lcal_razon_social', 'name' => 'lcal_razon_social', 'title' => 'Razón Social', 'search' => true])
            ->addColumn(['data' => 'forma_pago', 'name' => 'forma_pago', 'title' => 'Forma Pago', 'search' => true])
//            ->addColumn(['data' => 'cpag_aut_bancario', 'name' => 'cpag_aut_bancario', 'title' => 'Autorización'])
            ->addColumn(['data' => 'cpag_importe_pagado', 'name' => 'cpag_importe_pagado', 'title' => 'Importe', 'search' => false, 'class' => 'text-right'])
            ->addColumn(['data' => 'cpag_requiere_factura', 'name' => 'cpag_requiere_factura', 'title' => 'Requiere Factura', 'search' => false, 'class' => 'text-center'])
//            ->addColumn(['data' => 'sgft_tipo', 'name' => 'sgft_tipo', 'title' => 'Tipo'])
//            ->addColumn(['data' => 'sgft_comentario', 'name' => 'sgft_comentario', 'title' => 'Comentario'])
//            ->addColumn(['data' => 'sgft_estado', 'name' => 'sgft_estado', 'title' => 'Estado' ])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);

        $locales = Local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $url_form_validar = url('comprobante-pago/form-validar');

        return view('web.comprobante-pago.index-contabilidad', compact('dataTable', 'locales', 'url_form_validar'));

    }


    /**
     * El locatario debe capturar sus comprobantes de pago
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form(Request $request)
    {
        $url = url('comprobante-pago/insert');

        $usuario = \Auth::getUser();
        if ($usuario->usr_lcal_id == null) {
            return '<p class="alert alert-danger"> No existe un local asignado al usuario. <br> Contacte al administrador.</p>';
        }
        $local = $usuario->Local;

        $formas_pago = [
            '1' => 'Efectivo',
            '3' => 'Cheque nominativo',
            '4' => 'Transferencia electrónica',
        ];

        $usos_cfdi = [
            '1' => 'Gastos en general',
            '2' => 'Por definir'
        ];

        $solicitud = $request->solicitud;

        return view('web.comprobante-pago.form', compact('local', 'url', 'formas_pago', 'usos_cfdi', 'solicitud'));

    }

    public function formValidar(ComprobantePago $comprobante, Request $request)
    {

        $url_validar = url('comprobante-pago/do-validar', $comprobante->cpag_id);
        $url_rechazar = url('comprobante-pago/do-rechazar', $comprobante->cpag_id);

//        $formas_pago = \DB::table('c_formapago')
//                            ->select(\DB::raw(' CONCAT(codigo," ", descripcion ) as descripcion'),'id')
//                            ->get()
//                            ->toArray();

        $formas_pago = [
            '1' => 'Efectivo',
            '3' => 'Cheque nominativo',
            '4' => 'Transferencia electrónica',
        ];

        $usos_cfdi = [
            '1' => 'Gastos en general',
            '2' => 'Por definir'
        ];


        return view('web.comprobante-pago.validar', compact('url_rechazar', 'url_validar', 'comprobante', 'formas_pago', 'usos_cfdi'));

    }

    /**
     * Para el rol de Contabilidad
     * @param ComprobantePago $comprobante
     * @return \Illuminate\Http\JsonResponse
     */
    public function doValidar(ComprobantePago $comprobante, Request $request)
    {

        \DB::beginTransaction();
        try {

            $comprobante->cpag_estado = 'VALIDADO';
            $comprobante->cpag_forma_pago = $request->get('cpag_forma_pago');
//            $comprobante->cpag_uso_cfdi = $request->get('cpag_uso_cfdi');
            $comprobante->save();

            //si el comprobante tiene factura directa editar la factura también

            $factura = Factura::whereFactCpagId($comprobante->cpag_id)->first();
            if ($factura != null) {
                activity()->disableLogging();
                $factura->fact_formapago_id = $comprobante->cpag_forma_pago;
                $factura->fact_usocfdi_id = $comprobante->cpag_uso_cfdi;
                $factura->save();
                activity()->enableLogging();
            }


            $response_message = 'Comprobante <b>VALIDADO</b> correctamente.';
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($comprobante->cpag_lcal_id)
                    ->get();

                $recepcionistas = User::role('RECEPCIÓN')
                    ->get();

                \Notification::send($locatarios, new ComprobanteValidado($comprobante));
                \Notification::send($recepcionistas, new ComprobanteValidado($comprobante));
                //-------------------------------------------------------------------------------------

            } catch (\Exception $e) {
                $response_message .= ' Error al notificar';
                $response_data['notification_error'] = $e->getMessage();
            }


            \DB::commit();
            return response()->json($this->ajaxResponse(true, $response_message, $response_data));

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
        }


    }

    public function doRechazar(ComprobantePago $comprobante)
    {

        try {
            \DB::beginTransaction();

            $comprobante->cpag_estado = 'RECHAZADO';
            $comprobante->cpag_comentario_admin = $this->data['sgft_comentario_admin'];
            $comprobante->save();


            //cancelamos la solicitud (SI ESTA EN ESTADO PENDIENTE, si esta en otro estado es que RECEPCIÓN ya la cobró)
            $data_update = [
                'sgft_comentario_admin' => $this->data['sgft_comentario_admin'],
                'sgft_estado' => 'CANCELADA',
                'sgft_cpag_id' => null,
            ];

            SolicitudGafete::whereSgftCpagId($comprobante->cpag_id)
                ->whereSgftEstado('PENDIENTE')
                ->update($data_update);

            // -- old version  1 - 1
//            $solicitud = SolicitudGafete::whereSgftCpagId($comprobante->cpag_id)
//                            ->whereSgftEstado('PENDIENTE')
//                            ->first();
//
//            if($solicitud != null){
//                $solicitud->sgft_comentario_admin = $this->data['sgft_comentario_admin'];
//                $solicitud->sgft_estado = 'CANCELADA';
//                $solicitud->sgft_cpag_id = null;
//                $solicitud->save();
//            }

            //cancelamos la solicitud de estacionamiento (SI ESTA EN ESTADO PENDIENTE, si esta en otro estado es que RECEPCIÓN ya la cobró)
            $data_update = [
                'gest_comentario_admin' => $this->data['sgft_comentario_admin'],
                'gest_estado' => 'CANCELADA',
                'gest_cpag_id' => null,
            ];

            GafeteEstacionamiento::whereGestCpagId($comprobante->cpag_id)
                ->whereGestEstado('PENDIENTE')
                ->update($data_update);


            // -- old version  1 - 1
//            $gesta = GafeteEstacionamiento::whereGestCpagId($comprobante->cpag_id)
//                ->whereGestEstado('PENDIENTE')
//                ->first();
//
//            if($gesta != null){
//                $gesta->gest_comentario_admin = $this->data['sgft_comentario_admin'];
//                $gesta->gest_estado = 'CANCELADA';
//                $gesta->gest_cpag_id = null;
//                $gesta->save();
//            }


            //si hay una factura PRECAPTURADA la eliminamos (softdelete)
            if ($comprobante->cpag_requiere_factura == 1 && $comprobante->Factura != null) {
                $factura = $comprobante->Factura;
                $factura->delete();
            }


            \DB::commit();

            $response_message = 'Comprobante <b>RECHAZADO</b> correctamente.';
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($comprobante->cpag_lcal_id)
                    ->get();

                \Notification::send($locatarios, new ComprobanteRechazado($comprobante, $this->data['sgft_comentario_admin']));
                //-------------------------------------------------------------------------------------

            } catch (\Exception $e) {
                $response_message .= ' Error al notificar';
                $response_data['notification_error'] = $e->getMessage();
            }


            return response()->json($this->ajaxResponse(true, $response_message, $response_data));

        } catch (\Exception $e) {

            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));

        }

    }

    public function insert(Request $request)
    {
        $solicitud = null;
        if ($this->data['solicitud_id'])
            $solicitud = SolicitudGafete::find($this->data['solicitud_id']);
        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));

        } else {

            $existe = ComprobantePago::where('cpag_fecha_pago', $this->data['cpag_fecha_pago'])
                ->where('cpag_folio_bancario', $this->data['cpag_folio_bancario'])
                ->where('cpag_importe_pagado', $this->data['cpag_importe_pagado'])
                ->where('cpag_lcal_id', $this->data['cpag_lcal_id'])
                ->whereNotIn('cpag_estado', ['RECHAZADO'])
                ->get();
            if ($existe->count() > 0) {
                return response()->json($this->ajaxResponse(false, 'Ya existe un comprobante con los datos entrados!'));
            }

            \DB::beginTransaction();
            try {
                $this->data['cpag_file'] = $request->cpag_file->store('', 'comprobantes');

                $comprobante = ComprobantePago::create($this->data);

                //si el comprobante requiere factura generar registro precapturado
                if ($comprobante->cpag_requiere_factura == 1) {

                    // insertamos la factura

                    $importe_pagado = (float)$comprobante->cpag_importe_pagado;
                    $subtotal = round($importe_pagado / 1.16, 2);
                    $iva = $importe_pagado - $subtotal;

                    $factura = new Factura();
                    $factura->fact_lcal_id = $comprobante->cpag_lcal_id;

                    $factura->fact_rfc_emisor = settings()->get('cfdi_rfc_emisor');
                    $factura->fact_nombre_emisor = settings()->get('cfdi_nombre_emisor');
                    $factura->fact_fecha_emision = date('Y-m-d H:i:s');

                    $factura->fact_nombre_receptor = $comprobante->Local->lcal_razon_social;
                    $factura->fact_rfc_receptor = $comprobante->Local->lcal_rfc;
                    $factura->fact_lugar_expedicion = settings()->get('cfdi_lugar_expedicion');
                    $factura->fact_serie_id = CSerie::first()->id;
                    $factura->fact_usocfdi_id = $comprobante->cpag_uso_cfdi;
                    $factura->fact_formapago_id = $comprobante->cpag_forma_pago;
                    $factura->fact_metodopago_id = 1; //pago en una sola exhibición PUE
                    $factura->fact_moneda_id = 1; //MXN
                    $factura->fact_cantidad_letra = $comprobante->cpag_cantidad_letra;
                    $factura->fact_observaciones = null;

                    $factura->fact_total = (string)$importe_pagado;
                    $factura->fact_iva = (string)$iva;
                    $factura->fact_subtotal = (string)$subtotal;

                    $factura->fact_tipo_cambio = 1;
                    $factura->fact_estado = 'PENDIENTE'; //cuando recepción prevalide el comprobante pasará a PRECAPTURADA
                    $factura->fact_efecto_comprobante = 'I';
                    $factura->fact_regimenfiscal_id = 1; //personas morales

                    $factura->fact_cpag_id = $comprobante->cpag_id;
                    $factura->save();

                    //insertamos el concepto

                    $data_concepto = [
                        'fcdt_fact_id' => $factura->fact_id,
                        'fcdt_cantidad' => 1,
                        'fcdt_claveunidad_id' => 2, //Pieza
                        'fcdt_claveproducto_id' => 3, //Gafetes
                        'fcdt_concepto' => 'GAFETES SEGUN COMPROBANTE ' . $comprobante->cpag_folio_bancario,
                        'fcdt_importe' => (string)$importe_pagado,
                        'fcdt_precio' => (string)$subtotal,
                        'fcdt_iva' => (string)$iva,
                        'fcdt_cpag_id' => $comprobante->cpag_id
                    ];

                    $concepto = FacturaDetalle::create($data_concepto);

                }


            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();

            $mensaje = 'Comprobante <b>CAPTURADO</b> correctamente.';

            if ($comprobante->cpag_requiere_factura == 1) {

//                $dia = date('d');
                $dia = now()->lastOfMonth()->day - now()->day;

                if ($dia <= 3) {
                    $mensaje = 'Su factura se contabilizará en el próximo mes y se enviará  por correo';
                } else {
                    $mensaje = 'Su factura está en proceso, en breve le llegará por correo';
                }


            }
            if ($solicitud) {
                $solicitud->sgft_cpag_id = $comprobante->cpag_id;
                $solicitud->save();
            }

            return response()->json($this->ajaxResponse(true, $mensaje));

        }
    }

    public function doFacturaManual($id)
    {
        $comprobante = ComprobantePago::find($id);

        if (!$comprobante) {
            return response()->json($this->ajaxResponse(false, "Comprobante de Pago no encontrado!"));
        }

        DB::beginTransaction();
        try {

//            $comprobante->cpag_requiere_factura = 1;
//            $comprobante->save();

            // insertamos la factura

            $subtotal = round($comprobante->cpag_importe_pagado / 1.16, 2);
            $iva = $comprobante->cpag_importe_pagado - $subtotal;

            $data_factura = [
                'fact_lcal_id' => $comprobante->cpag_lcal_id,

                'fact_rfc_emisor' => settings()->get('cfdi_rfc_emisor'),
                'fact_nombre_emisor' => settings()->get('cfdi_nombre_emisor'),
                'fact_fecha_emision' => date('Y-m-d H:i:s'),

                'fact_nombre_receptor' => $comprobante->Local->lcal_razon_social,
                'fact_rfc_receptor' => $comprobante->Local->lcal_rfc,
                'fact_lugar_expedicion' => settings()->get('cfdi_lugar_expedicion'),
                'fact_serie_id' => CSerie::first()->id,
                'fact_usocfdi_id' => $comprobante->cpag_uso_cfdi,
                'fact_formapago_id' => $comprobante->cpag_forma_pago,
                'fact_metodopago_id' => 1, //pago en una sola exhibición PUE
                'fact_moneda_id' => 1, //MXN
                'fact_cantidad_letra' => $comprobante->cpag_cantidad_letra,
                'fact_observaciones' => null,

                'fact_total' => $comprobante->cpag_importe_pagado,
                'fact_iva' => (string)$iva,
                'fact_subtotal' => (string)$subtotal,

                'fact_tipo_cambio' => 1,
                'fact_estado' => 'PRECAPTURADA', //cuando recepción prevalide el comprobante pasará a PRECAPTURADA
                'fact_efecto_comprobante' => 'I',
                'fact_regimenfiscal_id' => 1, //personas morales

                'fact_cpag_id' => $comprobante->cpag_id

            ];

            $factura = Factura::create($data_factura);

            //insertamos el concepto

            $data_concepto = [
                'fcdt_fact_id' => $factura->fact_id,
                'fcdt_cantidad' => 1,
                'fcdt_claveunidad_id' => 2, //Pieza
                'fcdt_claveproducto_id' => 3, //Gafetes
                'fcdt_concepto' => 'GAFETES SEGUN COMPROBANTE ' . $comprobante->cpag_folio_bancario,
                'fcdt_importe' => $comprobante->cpag_importe_pagado,
                'fcdt_precio' => (string)$subtotal,
                'fcdt_iva' => (string)$iva,
                'fcdt_cpag_id' => $comprobante->cpag_id
            ];

            $concepto = FacturaDetalle::create($data_concepto);

            $dia = now()->lastOfMonth()->day - now()->day;

            if ($dia <= 3) {
                $response_message = 'Su factura se contabilizará en el próximo mes y se enviará  por correo';
            } else {
                $response_message = 'Su factura está en proceso, en breve le llegará por correo';
            }
            $response_data = [];

            try {

                // N o t i f i c a ci o n -------------------------------------------------------------
                $locatarios = User::role('LOCATARIO')
                    ->whereUsrLcalId($comprobante->cpag_lcal_id)
                    ->get();

                $recepcionistas = User::role('RECEPCIÓN')
                    ->get();

                Notification::send($locatarios, new ComprobanteValidado($comprobante));
                Notification::send($recepcionistas, new ComprobanteValidado($comprobante));
                //-------------------------------------------------------------------------------------

            } catch (\Exception $e) {
                $response_message .= ' Error al notificar';
                $response_data['notification_error'] = $e->getMessage();
            }


            \DB::commit();
            return response()->json($this->ajaxResponse(true, $response_message, $response_data));

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
        }

        return response()->json($this->ajaxResponse(true, $response_message));
    }

    public
    function getJSON(Local $local)
    {
        return response()->json($local);
    }

    /*Obtiene la información para un campo select2*/
    public
    function getSelectOptionsGafete(Local $local, Request $request)
    {
        $q = $request->q;

//      $comprobantes = ComprobantePago::getComprobantesDisponiblesParaGafete($local);
        $comprobantes = ComprobantePago::getCapturados($local);
        $comprobantes = $comprobantes->filter(static function (ComprobantePago $comprobante) {
            return $comprobante->saldo_disponible > 0;
        });

        if ($comprobantes != null) {
//            $comprobantes = \Arr::pluck($comprobantes,'descripcion','cpag_id');
            $records = [];
//            foreach ($comprobantes as $id=>$text):
            foreach ($comprobantes as $c):
                $records[] = ['id' => $c->cpag_id, 'text' => "FOLIO: " . $c->cpag_folio_bancario . " $: " . number_format($c->cpag_importe_pagado, 2),
                    'result_template' => "<b>FOLIO: " . $c->cpag_folio_bancario . "<br>" .
                        "<span>FECHA PAGO: " . $c->cpag_fecha_pago .
                        "<br><span>IMPORTE: $" . number_format($c->cpag_importe_pagado, 2) .
                        "</span>"];
            endforeach;


        } else {
            $records = [0 => 'SIN COMPROBANTES DISPONIBLES'];
        }

//        $comprobantes[0]['selected'] = true;
        return response()->json($records);
    }

    /*Obtiene la información para un campo select2*/
    public
    function getSelectOptionsPermisoTemporal(Local $local, Request $request)
    {
        $q = $request->q;

        $comprobantes = ComprobantePago::getComprobantesDisponiblesParaPermisoTemporal($local);

        if ($comprobantes != null) {
//            $comprobantes = \Arr::pluck($comprobantes,'descripcion','cpag_id');
            $records = [];
//            foreach ($comprobantes as $id=>$text):
            foreach ($comprobantes as $c):
                $records[] = ['id' => $c->cpag_id, 'text' => $c->descripcion,
                    'result_template' => "<b>" . $c->descripcion . "</b><br>" .
                        "<br>RESTANTES - REPOSICIÓN: " . $c->disponibles_rp . "</span>"];
            endforeach;


        } else {
            $records = ['id' => "", 'text' => 'SIN COMPROBANTES DISPONIBLES',
                'result_template' => 'SIN COMPROBANTES DISPONIBLES',
            ];
        }

//        $comprobantes[0]['selected'] = true;
        return response()->json($records);
    }


    /*Obtiene la información para un campo select2*/
    public
    function getSelectOptionsFactura(Local $local = null, Request $request)
    {
        $q = $request->q;

        $comprobantes = ComprobantePago::getComprobantesDisponiblesParaFactura($local);

        if ($comprobantes != null) {
//            $comprobantes = \Arr::pluck($comprobantes,'descripcion','cpag_id');
            $records = [];
//            foreach ($comprobantes as $id=>$text):
            foreach ($comprobantes as $c):
                $records[] = ['id' => $c->cpag_id, 'text' => $c->descripcion,
                    'folio' => $c->cpag_folio_bancario,
                    'importe_pagado' => $c->cpag_importe_pagado,
                    'importe_facturado' => $c->importe_facturado,
                    'result_template' => "<b class='text-primary'>" . $c->descripcion . "</b><br>" .
                        "<span style='font-size: 10px; line-height: 12px'><b>PRIMERA VEZ: </b>" . $c->cantidad_pv . " <b>REPOSICIÓN: " . $c->cantidad_rp . "</b>" .
                        "<br><b>PAGADO: </b>$ " . number_format($c->cpag_importe_pagado, 2) .
                        "<br><b>FACTURADO: </b>$ " . number_format($c->importe_facturado, 2) .
                        "</span>"];
            endforeach;


        } else {
            $records = [0 => 'SIN COMPROBANTES DISPONIBLES'];
        }

//        $comprobantes[0]['selected'] = true;
        return response()->json($records);
    }


}
