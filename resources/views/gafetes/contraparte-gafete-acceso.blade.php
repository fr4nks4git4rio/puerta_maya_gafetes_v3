@extends('reportes.main_reports')

@section('content')
    <style>

        .report-title{
            color: #0b0b0b;
            font-size: 16px;
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
                <small>GAFETE DE ACCESO</small>
            </div>
            <br>
        </div>
    </div>


    <div class="row" style="clear: both; padding-top: 50px">
        <div style="width: 20%; float: left">
            <img src="{{$gafete->sgft_foto_web}}" alt="" style="width: 100px">
        </div>
        <div style="width: 80%; float: left">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th style="width: 25%"> Nombre </th>
                    <td style="width: 75%"> {{$gafete->sgft_nombre  }}</td>
                </tr>
                <tr>
                    <th style="width: 25%"> Completamente Vacunado </th>
                    <td style="width: 75%"> {{$gafete->Empleado->empl_vacuna_validada ? 'SI' : 'NO'}}</td>
                </tr>
                <tr>
                    <th style="width: 25%"> Local</th>
                    <td style="width: 75%"> {{ $gafete->Local->lcal_nombre_comercial}}</td>
                </tr>
                <tr>
                    <th style="width: 25%"> Año de vencimiento</th>
                    <td style="width: 75%"> {{ settings()->get('anio_impresion') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>




@endsection