@extends('reportes.main_gafete')

@section('content')

    @php

        $color  = $gafete->Local->Acceso->cacs_color;
        $folio  = $gafete->sgft_id;
        $bg_img = $gafete->Local->Acceso->cacs_bg_img;

        $name_cat_acceso_arr = explode('.', $bg_img);
        //$front = $name_cat_acceso_arr[0] . "_front." . $name_cat_acceso_arr[1];
        //$back = $name_cat_acceso_arr[0] . "_back." . $name_cat_acceso_arr[1];
        $paquete = \App\DisennoGafetePaquete::PaqueteSeleccionado();
        $front = $paquete->ImagenesFront()->where('dgpi_acceso_id', $gafete->Local->lcal_cacs_id)->first()->src_imagen;
        if($gafete->Local->lcal_cacs_id == 1)
            $back = $paquete->ImagenBackAdmin->src_imagen;
        else
            $back = $paquete->ImagenBack->src_imagen;

        $es_vertical = stripos(strtolower($paquete->dgp_nombre), 'vertical') !== false;

        $width_body = '130mm';
        $height_body = '82mm';

        $width_page = '130mm';
        $height_page = '82mm';

        $empleado = $gafete->Empleado()->withTrashed()->first();
    @endphp
    <style>

        .foto {
            margin-left: 10px;
        }

        .front {
            background-image: url("{{ asset($front) }}");
            /*background-image: url("../../../../public/storage/bg_gafetes/2023/Administracion_back.png");*/
            background-size: cover;
        }

        .back {
            background-image: url("{{ asset($back) }}");
            background-size: cover;
        }

        body {
            width: {{$width_body}}; /*53.98*/
            height: {{$height_body }}; /*85.6*/
            /*background-color: #cccccc;*/
            /*border: 1px solid red;*/
        }

        .page-container {
            /*border: 1px solid black;*/
            display: block;
            width: {{$width_page}}      !important;
            height: {{$height_page}}      !important;
            margin: 0 auto;
            padding: 2px;
        }

        h5 {
            color: black;
        }

        /*.tag-frame {*/
        /*height: 125mm; !* Equals maximum image height *!*/
        /*width: 17mm;*/
        /*!*border: 1px solid red;*!*/
        /*display: table-cell;*/
        /*vertical-align: middle;*/
        /*text-align: center;*/
        /*padding: 0;*/
        /*margin: 0;*/
        /*}*/

        /*.tag-image {*/
        /*!*background: #3A6F9A;*!*/
        /*vertical-align: middle;*/
        /*width: 10mm;*/
        /*margin: 0;*/
        /*padding: 0;*/
        /*max-height: 120mm;*/
        /*max-width: 12mm;*/
        /*}*/


    </style>

    <div class="page-container front" style="padding-bottom:0px;">

        <table id="table-layout" class="" style="width: 100%;">
            <tr style="
                        /*height: 100%;*/
                        border: 1px dotted transparent;
                        /*background-color: lightblue;*/
                        ">
                <td style="width: 45%">
                    <div class="text-center">
                        <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto"
                             style="border: 1px dotted transparent;border-radius: 50%;width: 50mm;height: 50mm;">
                    </div>
                </td>
                <td style="width: 55%; padding-top: 10px; padding-bottom: 0; padding-left: 0">
                    <div class="text-left" style="">
                        <div>
                            <div style="width: 40%; float: left; text-align: right; padding-right: 0">
                                <img src="{{ url("images/pm_logo_2.png") }}" style="width:16mm;"
                                     alt="logo">
                            </div>
                            <div style="width: 60%; float: left; padding-top: 15px">
                                <h5 style="font-size:12px; text-transform: uppercase; text-align: center"><span
                                            style="color: rgb(37, 96, 156); font-size: 17px">PUERTA MAYA</span> <br>
                                    TERMINAL MARÍTIMA
                                </h5>
                            </div>
                        </div>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <h3 style="color: rgb(37, 96, 156); font-size:22px; line-height: 0; font-weight: 700; text-transform: uppercase;">
                            {{ $gafete->Local->lcal_nombre_comercial }}</h3>
                        <br>
                        <h3 style="color: #000; font-size:20px; line-height: 1;font-weight: 700"
                            class="text-uppercase">
                            {{ $gafete->sgft_nombre }}
                            <div style="color: #000; font-weight:200; font-size:20px">{{ trim($gafete->sgft_cargo) }} </div>
                        </h3>
                        <div>
                            <div style="width: 50%; float: left; text-align: right; padding-top: 10px">
                                <span style="color: #000">VIGENCIA</span> <b
                                        style="font-size: 16px; color: black;">{{$anio_impresion}}</b>
                            </div>
                            <div style="width: 50%; float: right; text-align: center">
                                {!! QrCode::size(50)->errorCorrection('L')->generate($gafete->toStringQr()); !!}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <div class="page-container back" style="padding-left: 15px">

        @if($gafete->Local->lcal_cacs_id == 1)
            {{--            @include('gafetes.back-horizontal-admin',compact('gafete','folio'))--}}
            @include('gafetes.dinamico.back-horizontal-admin-2',compact('gafete','empleado','folio'))
        @else
            @include('gafetes.dinamico.back-horizontal',compact('gafete','empleado','folio'))
        @endif

    </div>

@endsection

