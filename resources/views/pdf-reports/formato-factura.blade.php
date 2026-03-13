@extends('reportes.main_reports')

@section('content')

    <style>

        .report-title {
            color: #0b0b0b;
            font-size: 18px;
            font-weight: bold;
        }

        html {
            background-color: white;
        }

        body {
            background-color: white;
        }

        table#tabla-contenido, tbody {
            color: #000;
        !important;

        }

        .bg-gray {
            background-color: #a6b8ce;
            height: 30px;
        }

        #contenido-reporte {
            color: #000000;
            font-size: large;
        }

        .debug-table {
            /*border: 1px solid red;*/
        }

        .debug-table td {
            /*border: 1px solid blue;*/
        }
    </style>


    <div class="row">

        <table style="width:100%" class="debug-table">

            <tr>
                <td style="width: 50%; vertical-align: top">

                    <b style="font-size: 18px">{{$factura->fact_nombre_emisor}}</b><br>
                    <b>RFC: </b>{{$factura->fact_rfc_emisor}}<br>
                    <?php
                    $regimen_fiscal = \App\CRegimenFiscal::find(settings()->get('cfdi_regimenfiscal_emisor'));
                    ?>
                    <b>Régimen Fiscal: </b> {{$regimen_fiscal->nombre }}
                    <br>
                    <b>Dirección Fiscal: </b> {{settings()->get('cfdi_direccionfiscal_emisor') }}
                    <br>
                    <b>{{settings()->get('cfdi_sitioweb_emisor')}}</b>
                    <br>
                    <p style="padding-left: 120px; padding-top: 20px">
                        <img src="{{asset('images/pm_logo_2.png')}}" alt="" width="160px">
                    </p>

                </td>
                <td>

                    <table style="width: 100%; vertical-align: top" border="1px solid black">

                        <tr class="bg-gray">
                            <td class="text-center" style="font-size: 18px; height: 45px"><b>FACTURA DE INGRESO</b></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>Serie:</b> {{$factura->Serie->descripcion}}
                                <b>Folio:</b> {{$factura->fact_folio}} </td>
                        </tr>

                        <tr class="bg-gray">
                            <td class="text-center"><b>Folio Fiscal</b></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b> {!! $factura->fact_uuid ?? '&nbsp;' !!}</b></td>
                        </tr>

                        <tr class="bg-gray">
                            <td class="text-center"><b>No. de Serie de Certificado</b></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>00001000000406558763</b></td>
                        </tr>

                        <tr class="bg-gray">
                            <td class="text-center"><b>Fecha de Certificación</b></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>{!! $factura->fact_fecha_certificacion ?? '&nbsp;'!!}</b></td>
                        </tr>

                        <tr class="bg-gray">
                            <td class="text-center"><b>Lugar y Fecha de Expedición</b></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <b>COZUMEL, QUINTANA ROO, MEXICO, C.P. 77600</b>
                                <br>
                                {{$factura->fact_fecha_emision}}
                            </td>
                        </tr>


                    </table>

                </td>


            </tr>

        </table>

    </div>

    <br>
    <div class="row">

        <table width="100%">
            <tr>
                <td style="width: 75%">
                    <b>Receptor: </b> <span class="text-uppercase">{{$factura->fact_nombre_receptor}}</span><br>
                    <b>RFC: </b> {{$factura->fact_rfc_receptor}}<br>
                    <b>Uso del CFDI: </b> {{$factura->UsoCfdi->codigo}} | {{$factura->UsoCfdi->descripcion}}<br>
                    <b>Régimen
                        Fiscal: </b> {{$factura->Local ? ($factura->Local->RegimenFiscal ? $factura->Local->RegimenFiscal->nombre : '') : '616 | Sin obligaciones fiscales'}}
                    <br>
                    <b>Dirección
                        Fiscal: </b> {{$factura->Local ? $factura->Local->lcal_direccion_fiscal : 'Carretera a Chankanaab KM 4.5, Interior Muelle Puerta Maya,Cozumel, Quintana Roo C.P. 77600'}}
                    <br>
                </td>
                <td>
                    @if($factura->fact_estado == 'CANCELADA')
                        <h4><b class="text-danger">CANCELADA</b></h4><br>
                    @endif

                    @if($factura->fact_estado == 'CAPTURADA')
                        <h4><b class="text-warning">SIN TIMBRADO FISCAL</b></h4><br>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <br>

    <div class="row">
        <table style="width: 100%; font-size: 12px">
            <tr class="bg-gray">
                <td class="text-center" style="border: 1px solid black"><b>Cantidad</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Clave Unidad</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Calve Prod Serv</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Objeto de Impuesto</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Concepto/Descripción</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Valor Unitario</b></td>
                <td class="text-center" style="border: 1px solid black"><b>Importe</b></td>
            </tr>

            @foreach($factura->Conceptos as $concepto)

                <tr>
                    <td class="text-center" style="border: 1px solid black">{{$concepto->fcdt_cantidad}}</td>
                    <td class="text-center"
                        style="border: 1px solid black">{{$concepto->Unidad->codigo}} {{$concepto->Unidad->nombre}}</td>
                    <td class="text-center"
                        style="border: 1px solid black">{{$concepto->Producto->clave}} {{$concepto->Producto->descripcion}}</td>
                    <td class="text-center"
                        style="border: 1px solid black">{{$concepto->ObjetoImpuesto ? $concepto->ObjetoImpuesto->nombre : ''}}</td>
                    <td class="text-center" style="border: 1px solid black">{{$concepto->fcdt_concepto}}</td>
                    <td class="text-center"
                        style="border: 1px solid black">{{number_format( $concepto->fcdt_precio / $concepto->fcdt_cantidad , 2)}}</td>
                    <td class="text-center"
                        style="border: 1px solid black">{{ number_format($concepto->fcdt_precio,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" rowspan="3">&nbsp;</td>
                <td class="text-center" style="border: 1px solid black"><b>Subtotal</b></td>
                <td class="text-center"
                    style="border: 1px solid black"> {{number_format($factura->fact_subtotal,2)}} </td>
            </tr>
            <tr>
                {{--                    <td colspan="4">&nbsp;</td>--}}
                <td class="text-center" style="border: 1px solid black"><b>IVA 16%</b></td>
                <td class="text-center" style="border: 1px solid black"> {{number_format($factura->fact_iva,2)}} </td>
            </tr>
            <tr>
                {{--                    <td colspan="4">&nbsp;</td>--}}
                <td class="text-center" style="border: 1px solid black"><b>Total</b></td>
                <td class="text-center" style="border: 1px solid black"> {{number_format($factura->fact_total,2)}} </td>
            </tr>
        </table>

        <br>

        <div class="row">

            <div class="col-sm-12">
                <table width="100%">
                    <tr>
                        <td>
                            <b style="font-size: 16px">Importe con letra: </b> <span
                                    style="font-size: 16px">{{$factura->fact_cantidad_letra}}</span> <br>
                            <b>Moneda: </b> {{$factura->Moneda->codigo}} <br>
                            {{--<b>Tipo de cambio: </b> {{$factura->fact_tipo_cambio}} <br>--}}
                            <b>Metodo de
                                pago: </b> {{$factura->MetodoPago->codigo}} {{$factura->MetodoPago->descripcion}} <br>
                            <b>Forma de pago: </b> {{$factura->FormaPago->codigo}} {{$factura->FormaPago->descripcion}}
                            <br>
                            @if(!$factura->Local || $factura->Local->lcal_id == '129')
                                <b>Periodicidad: </b> {{$factura->Periodicidad ? $factura->Periodicidad->nombre : ''}}
                                <br>
                                <b>Mes: </b> {{$factura->Mes ? $factura->Mes->nombre : ''}}<br>
                                <b>Año: </b> {{$factura->fact_anio}}<br>
                            @endif

                            <br>
                            Este documento es una representación impresa de un CFDI v4.0

                        </td>
                    </tr>

                </table>
            </div>

        </div>

        <br>

        @php
            $img = @file_get_contents($factura->fact_qr_code_path);
            $img_b64 = 'data:image/jpeg;base64, ' . base64_encode($img);
        @endphp

        <div class="row">
            <div class="col-sm-12">
                <table style="width: 100%">
                    <tr>
                        <td style="padding: 5px; vertical-align: top">
                            <img src="{{$img_b64}}">
                        </td>
                        <td style="font-size: 12px; vertical-align: top">
                            <b>Cadena original del complemento de certificacion digital del SAT</b><br>
                            <span style="word-break: break-all"> {{$factura->fact_cadena_original}} </span><br>
                            <br>

                        </td>
                    </tr>
                </table>
            </div>
        </div>


@endsection
