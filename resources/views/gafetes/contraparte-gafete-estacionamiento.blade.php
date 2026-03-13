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

        <div style="width: 20%; float: left">
            {{--            <span>logo</span>--}}
            <span><img src="{{asset('images/pm_logo_2.png')}}" alt="" style="width: 90px"></span>
        </div>
        <div style="width: 80%; float: left;">

            <div class="report-title" style="text-align: center" >PUERTA MAYA - SISTEMA DE GAFETES Y ACCESOS
                <br>
                <small>GAFETE DE ESTACIONAMIENTO</small>
            </div>
            <br>
        </div>

    </div>


    <div class="row" style="padding-top: 30px; clear: both">
        <div class="col-sm-12">
            <table class="table table-bordered mt-5">
                <tbody>
                <tr>
                    <th style="width: 15%"> Local </th>
                    <td style="width: 85%"> {{$gafete->Local->lcal_nombre_comercial  }}</td>
                </tr>
                <tr>
                    <th style="width: 15%"> Consecutivo </th>
                    <td style="width: 85%"> {{ $gafete->gest_numero  }} de {{ strtolower($gafete->gest_tipo) === 'auto' ? $gafete->Local->lcal_espacios_autos : $gafete->Local->lcal_espacios_motos }}</td>
                </tr>
                <tr>
                    <th style="width: 15%"> Tipo</th>
                    <td style="width: 85%"> {{ $gafete->gest_tipo}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>




@endsection