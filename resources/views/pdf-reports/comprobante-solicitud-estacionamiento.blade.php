@extends('reportes.main_reports')

@section('content')

    <style>

        .report-title{
            color: #0b0b0b;
            font-size: 18px;
            font-weight: bold;
        }

        html{
            background-color: white;
        }

        body{
            background-color: white;
        }
    </style>

    <div class="row">

        <div class="col-2 float-left">
{{--            <span>logo</span>--}}
            <span><img src="{{asset('images/pm_logo_2.png')}}" alt="" width="100px"></span>
        </div>
        <div class="col-10 float-right">

            <div class="report-title" >PUERTA MAYA - SISTEMA DE GAFETES Y ACCESOS
                <br>
                <small>COMPROBANTE DE SOLCITUD DE GAFETE DE ESTACIONAMIENTO</small>
            </div>
            <br>
            <span class="report-caption">GENERADO: {{date('Y-m-d H:i:s')}}</span>


        </div>

    </div>


    <div class="row">

        <div class="col float-left">

            <table class="table table-bordered mt-5">
                <tbody>
                    <tr>
                        <th colspan="3"> Clase </th>
                        <td colspan="3"> {{$solicitud->gest_tipo  }}</td>
                        <th colspan="3"> Local </th>
                        <td colspan="3"> {{$solicitud->Local->lcal_nombre_comercial}}</td>
                    </tr>
                    <tr>
                        <th colspan="3"> Tipo de Gafete</th>
                        <td colspan="3" class="text-left"> {{ $solicitud->gest_tipo_solicitud }}</td>
                        <th colspan="3"> Costo gafete</th>
                        <td colspan="3" class="text-left"> $ {{ number_format($solicitud->gest_costo,2) }}</td>
                    </tr>
                    @if($solicitud->ComprobantePago != null)

                    <tr>
                        <th colspan="3"> Folio</th>
                        <td colspan="9" class="text-left"> {{ $solicitud->ComprobantePago->cpag_folio_bancario}}</td>
{{--                        <th colspan="3"> AUT Bancario</th>--}}
{{--                        <td colspan="3" class="text-left"> {{ $solicitud->ComprobantePago->cpag_aut_bancario}}</td>--}}
                    </tr>

                    <tr>
                        <th colspan="3"> Importe</th>
                        <td colspan="3" class="text-left">$ {{ number_format($solicitud->ComprobantePago->cpag_importe_pagado,2) }}</td>
                        <th colspan="3"> Fecha de pago</th>
                        <td colspan="3" class="text-left"> {{ $solicitud->ComprobantePago->cpag_fecha_pago }}</td>
{{--                        <th colspan="3"> Cantidad</th>--}}
{{--                        <td colspan="3" class="text-left"> {{ $solicitud->ComprobantePago->cpag_cantidad_pv}} PRIMERA VEZ, {{ $solicitud->ComprobantePago->cpag_cantidad_rp}} REPOSICIÓN</td>--}}
                    </tr>
                    <tr>

{{--                        <th colspan="3"> Contador de usos</th>--}}
{{--                        <td colspan="3" class="text-left"> {{ $solicitud->sgft_numero_uso_comprobante }}--}}
{{--                            de {{ ($solicitud->sgft_tipo=="PRIMERA VEZ")? $solicitud->ComprobantePago->cpag_cantidad_pv : $solicitud->ComprobantePago->cpag_cantidad_rp  }} --}}
                        </td>
                    </tr>

                    @else
                        <tr>
                            <th colspan="12" class="text-center"> SOLICITUD DE GAFETE GRATUITO</th>
{{--                            <td colspan="3" class="text-center"> {{ $solicitud->ComprobantePago->cpag_fecha_pago }}</td>--}}
{{--                            <td colspan="9"> &nbsp;</td>--}}
                        </tr>

                    @endif

                    @if($solicitud->ComprobantePago != null)
                    <tr>
                        <th colspan="3"> Forma de Pago</th>
                        <td colspan="3" class="text-left"> {{ $solicitud->ComprobantePago->FormaPago->descripcion ?? '' }} </td>

{{--                        @if($solicitud->ComprobantePago->cpag_requiere_factura == 1)--}}
                        <th colspan="3"> Uso CFDI</th>
                        <td colspan="3" class="text-left"> {{ $solicitud->ComprobantePago->UsoCfdi->descripcion ?? '' }}</td>
{{--                        @endif--}}
                    </tr>
                    @endif

                </tbody>
            </table>


        </div>
    </div>


@endsection