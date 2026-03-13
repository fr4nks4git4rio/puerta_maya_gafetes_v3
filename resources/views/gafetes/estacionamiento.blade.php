@extends('reportes.main_gafete')

@section('content')

    @php

        $tipo = strtolower($gafete->gest_tipo);
        $folio = "";
        $color =  ($tipo == "moto")? 'white' : 'black';

    @endphp

    <style>

        .foto{
            margin-left: 10px;
            width: 34mm;
        }

        body{
            background-image: url("{{ asset('storage/bg_gafetes/'.$tipo.'.png') }}");
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
        <table id="table-layout" class="" style="width: 100%; height: 82mm;">

            <tr>
                <td width="40%">

                    <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:40mm;" >

                </td>
                <td>
                    <div class="text-center">
                        <b style="color: black">
                            {{ $gafete->gest_numero  }} de {{ $tipo == 'auto' ? $gafete->Local->lcal_espacios_autos : $gafete->Local->lcal_espacios_motos }}
                        </b>
                    </div>
                    <br>
                    <div class="text-center">
                        <h4 style="color:#000;">{{$gafete->Local->lcal_identificador}} <br> {{$gafete->Local->lcal_nombre_comercial}}</h4>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style=" color: {{$color}}; font-size: 16px;">
                    <br>
                    <br>
                    <br>

                    <div class="text-center">
                        <b>ESTACIONAMIENTO</b> <br> <b>{{$gafete->gest_tipo}}</b>

{{--                        <br>--}}

{{--                        <img src="{{ asset('storage/bg_gafetes/silueta_'.$tipo.'.png')  }}" style="width: 100px" />--}}

                    </div>
                </td>
            </tr>

{{--            <tr style="border: 1px solid red;" class="text-center">--}}
{{--                <td colspan="2" style="border: 1px solid black; padding: 0px; margin: 0px; ">--}}
{{--                    <br>--}}
{{--                    <span>HOLA MUNDO</span>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td class="text-right" colspan="2">--}}

{{--                </td>--}}
{{--            </tr>--}}
        </table>

        <div style="position: absolute; left: 4px; bottom: 8px">
            {!! QrCode::size(60)->generate($gafete->toStringQr()); !!}
        </div>

        <div class="text-center">
        <img src="{{ asset('storage/bg_gafetes/silueta_'.$tipo.'_'.$color.'.png')  }}"
             class="img-fluid"
             style=" margin: 0; padding: 0; width: 200px;" />
        </div>
        <div class="text-right">
            <br>

            <span style="color: {{$color}}; font-weight: bold; font-size: 16px"> {{ $gafete->gest_anio  }}</span>
        </div>

    </div>

    <div class="page-container">

        @include('gafetes.back-horizontal',compact('gafete','folio'))

    </div>

@endsection
