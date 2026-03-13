@extends('reportes.main_gafete')

@section('content')

    @php

        $tipo = strtolower($gafete->gest_tipo);
        $folio = "";
            //        $color =  ($tipo == "moto")? 'white' : 'black';--}}
        $color =  '#000';

        $bg_img = ($tipo == "moto") ? \App\CatAcceso::find(8)->cacs_bg_img : \App\CatAcceso::find(7)->cacs_bg_img;

        $cat_acceso_id = $tipo == "moto" ? 8 : 7;

        $name_cat_acceso_arr = explode('.', $bg_img);
        //$front = $name_cat_acceso_arr[0] . "_front." . $name_cat_acceso_arr[1];
        //$back = $name_cat_acceso_arr[0] . "_back." . $name_cat_acceso_arr[1];
        $paquete = \App\DisennoGafetePaquete::PaqueteSeleccionado();
        $front = $paquete->ImagenesFront()->where('dgpi_acceso_id', $cat_acceso_id)->first()->src_imagen;
        $back = $paquete->ImagenBack->src_imagen;

    @endphp

    <style>

        .foto {
            margin-left: 10px;
            width: 34mm;
        }

        .front {
            background: url("{{ asset($front) }}") no-repeat bottom;
            /*background-position-x: 20px;*/
            background-size: contain;
        }

        .back {
            background-image: url("{{ asset($back) }}");
        }

        body {
            background-size: cover;
            width: 82.20mm; /*53.98*/
            height: 130.56mm; /*85.6*/
            /*background-color: #cccccc;;*/
            /*border: 1px solid red;*/
        }

        .page-container {
            /*border: 1px solid red;*/
            display: block;
            width: 82mm;
            height: 130mm;
            margin: 0 auto;
            padding: 10px;
        }


    </style>

    <div class="page-container front">
        <br>
        <table id="table-layout" class="" style="width: 100%; height: 20mm;">
            <tr style="height: 40mm"></tr>
            <tr>
                <td width="25%">
{{--                    <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:17mm;">--}}
                </td>
                <td width="60%">
{{--                    <div class="text-center">--}}
{{--                        <b style="text-transform: uppercase; color: {{$color}}; font-size: 18px;">ESTACIONAMIENTO--}}
{{--                            <br> {{$gafete->gest_tipo}}</b>--}}
{{--                    </div>--}}
                </td>
                <td width="15%"></td>
            </tr>

            <tr style="height: 40mm"></tr>

            <tr>
                <td colspan="3">
                    <div class="text-center" style="padding-top: 30px; padding-left: 125px">
                        {{-- <h5 style="margin-bottom: 0; color: #000; font-size: 20px">{{ $gafete->gest_anio  }}</h5> --}}
                        <h5 style="margin-bottom: 0; color: #fff; font-size: 14px; line-height: 1; font-weight: 100;">{{$gafete->Local->lcal_nombre_comercial}}</h5>
                        <h5 style="margin-bottom: 0; color: #fff; font-size: 14px; font-weight: 100;">
                            {{ $gafete->gest_numero  }}
                            de {{ $tipo == 'auto' ? $gafete->Local->lcal_espacios_autos : $gafete->Local->lcal_espacios_motos }}
                        </h5>
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

        <div style="position: absolute; left: 8px; bottom: 8px">
{{--            {!! QrCode::size(60)->generate($gafete->toStringQr()); !!}--}}
        </div>

        <div class="text-center">
            {{--<img src="{{ asset('storage/bg_gafetes/silueta_'.$tipo.'_'.$color.'.png')  }}"--}}
            {{--class="img-fluid"--}}
            {{--style=" margin: 0; padding: 0; width: 200px;" />--}}
        </div>
        <div class="text-center">
{{--            <span style="color: {{$color}}; font-weight: bold; font-size: 16px"> VIGENCIA &nbsp;{{ $gafete->gest_anio  }}</span>--}}
        </div>

    </div>

    <div class="page-container back">

        @include('gafetes.back-estacionamiento',compact('gafete','folio'))

    </div>

@endsection
