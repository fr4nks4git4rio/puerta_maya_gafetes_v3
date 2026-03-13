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
    </style>

    <div class="row">

        <div class="col-2 float-left">
            {{--            <span>logo</span>--}}
            <span><img src="{{asset('images/pm_logo_2.png')}}" alt="" width="100px"></span>
        </div>
        <div class="col-10 float-right">

            <div class="report-title">PUERTA MAYA - SISTEMA DE GAFETES Y ACCESOS
                <br>
                <small>LISTADO DE COMPROBANTES ASOCIADOS</small>
                <br>
                <small>FACTURA: {{$factura->fact_id}} UUID: {{$factura->fact_uuid}}</small>
            </div>
            <br>
            <span class="report-caption">GENERADO: {{date('Y-m-d H:i:s')}}</span>


        </div>

    </div>


    <div class="row">

        <div class="col float-left">

            <table class="table table-bordered mt-5">
                <tbody>
                <tr class="bg-light">
                    <td>No.</td>
                    <td>Fecha</td>
                    <td>Folio</td>
                    <td>Referencia</td>
                    {{--                        <td>Forma pago</td>--}}
                    {{--                        <td>Descargar</td>--}}
                    <td>Importe</td>
                </tr>

                @foreach($factura->Conceptos as $fd)
                    @if($fd->Comprobante)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$fd->Comprobante->cpag_fecha_pago}}</td>
                            <td>{{$fd->Comprobante->cpag_folio_bancario}}</td>
                            <td>{{$fd->Comprobante->cpag_aut_bancario}}</td>
                            <td class="text-right">$ {{ number_format($fd->Comprobante->cpag_importe_pagado,2) }}</td>
                        </tr>
                    @endif
                @endforeach

                </tbody>


            </table>


        </div>
    </div>




@endsection