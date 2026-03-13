@extends('reportes.main_gafete')

@section('content')

    @php

        $color  = $gafete->Local->Acceso->cacs_color;
        $folio  = $gafete->sgft_id;
        $bg_img = $gafete->Local->Acceso->cacs_bg_img;

        $empleado = $gafete->Empleado()->withTrashed()->first();
    @endphp
    <style>

        .foto{
            margin-left: 10px;
            width: 34mm;
        }

        body{
            background-image: url("{{ asset('storage/bg_gafetes/'.$bg_img) }}");
            background-size: cover;
            width: 82.20mm; /*53.98*/
            height: 130.56mm; /*85.6*/
            /*background-color: #cccccc;;*/
            /*border: 1px solid red;*/
        }

        .page-container{
            /*border: 1px solid red;*/
            display: block;
            width: 82mm;
            height: 130mm;
            margin: 0 auto;
            padding: 10px;
        }


    </style>

    <div class="page-container">
        <br>
        <table id="table-layout" class="" style="width: 100%; height: 81mm;">

            <tr>
                <td width="40%">

                    <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:30mm;" >
                    <div class="text-center">
                        <h5> TERMINAL <br> MARÍTIMA <br> PUERTA MAYA </h5>
                    </div>

                </td>
                <td>
                    <div class="text-center">
                        <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto" style="width: 40mm">
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="height: 30mm">
                    <div class="text-center">
                        <h3 style="color: #000;">{{ $gafete->sgft_nombre }}</h3>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="height: 72px; font-size: 20px; color:black;" class="text-center text-uppercase">
                    <b>{{$gafete->Local->lcal_nombre_comercial}}</b>
                    <br>
                    {{ $gafete->sgft_cargo }}
                </td>
            </tr>

            <tr style="height: 15mm; padding-top: 5mm ">
                @php
                    $color = 'black';
                    $color = ($gafete->Local->lcal_cacs_id == 1)? 'white' : $color;
                    $color = ($gafete->Local->Acceso->cacs_descripcion == 'Muelle')? 'white' : $color;
                @endphp
                <td>
                    <b style="font-size: 20px; color: {{$color}};">{{substr($gafete->sgft_fecha,0,4)}}</b>
                </td>
                <td colspan="2" class="text-right">
                    <div style="padding-top: 4px; font-size:18px; line-height:20px;color: {{$color}};" >
                        Vencimiento<br><b>31/12/{{ substr($gafete->sgft_fecha,0,4)  }}</b>
                    </div>
                </td>
            </tr>

    </table>
    </div>


    <div class="page-container">

    @if($gafete->Local->lcal_cacs_id == 1)
{{--        @include('gafetes.back-horizontal-admin',compact('gafete','folio'))--}}
        @include('gafetes.back-horizontal-admin-2',compact('gafete','empleado','folio'))
    @else
        @include('gafetes.back-horizontal',compact('gafete','empleado','folio'))
    @endif

    </div>

@endsection

