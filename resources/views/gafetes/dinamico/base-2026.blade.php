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

        $width_body = '82.20mm';
        $height_body = '130.56mm';

        $width_page = '82mm';
        $height_page = '130mm';

        $empleado = $gafete->Empleado()->withTrashed()->first();
//    @endphp
    <style>

        .foto {
            margin-left: 10px;
            width: 20mm;
            height: 20mm;
            border-radius: 12px;
            object-fit: cover;
        }

        .front {
            background-image: url("{{ asset($front) }}");
            /*background-image: url("../../../../public/storage/bg_gafetes/2023/Administracion_back.png");*/
            /*background-size: 100%;*/
            /*background-repeat: no-repeat;*/

        }

        #front {
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
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
            width: {{$width_page}}   !important;
            height: {{$height_page}}   !important;
            margin: 0 auto;
            padding: 5px;
        }

        h5 {
            color: black;
        }

        .tag-frame {
            height: 125mm; /* Equals maximum image height */
            width: 17mm;
            /*border: 1px solid red;*/
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0;
            margin: 0;
        }

        .tag-image {
            /*background: #3A6F9A;*/
            vertical-align: middle;
            width: 10mm;
            margin: 0;
            padding: 0;
            max-height: 120mm;
            max-width: 12mm;
        }


    </style>

    <div class="page-container" style="padding-bottom:0; padding-top: 9.6mm">

        <img src="{{asset($front)}}" alt="" id="front">

        <table id="table-layout" class="" style="width: 100%; display: inline-flex; z-index: 4; position: absolute; top: 11mm; left: 0">

            <tr>
                <td style="width: 1mm;border: 1px dotted transparent;">

                    {{--<div class="tag-frame">--}}
                        {{--<img--}}
                        {{--src="{{ asset('storage/'.$tag_image) }}"--}}
                        {{--alt="tag_local"--}}
                        {{--class="tag-image"--}}
                        {{-->--}}
                    {{--</div>--}}

                </td>
                <td style="width: 23mm">
                    <div class="text-right">
{{--                        <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:12mm;" alt="logo">--}}
                    </div>
                </td>
                <td style="height: 7mm;border: 1px dotted transparent;">
                    <div class="text-center" style="padding-right:20mm">
{{--                        <h5 style="font-size:16px; font-weight: 600; color: rgb(29,79,123); line-height: 15px"> PUERTA MAYA <br> <span style="font-size: 12px; color: #000; font-weight: 200;">TERMINAL MARÍTIMA</span></h5>--}}
                    </div>
                </td>
            </tr>


            <tr style="
                        /*height: 45mm;*/
                        border: 1px dotted transparent;
                        /*background-color: lightblue;*/
                        ">
                <td colspan="2">
                    <div>
                        <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto"
                             style="border: 1px dotted transparent;width: 33.7mm;height: 35mm; margin-left: 24mm; margin-top: 5mm">
                    </div>
                </td>
            </tr>

            <tr style="
                height: 15mm;
                border: 1px dotted transparent;
                /*background-color: #f2ff3f;*/
                ">

                <td colspan="3">
                    <div class="text-center">
                        <h3 style="color: #000; font-size:20px; font-weight: 700; line-height: 1; margin-top: -4mm"
                            class="text-uppercase">
                            {{ $gafete->Local->lcal_nombre_comercial }}
                        </h3>
                    </div>
                </td>
            </tr>
            <tr style="
                height: 15mm;
                border: 1px dotted transparent;
                /*background-color: #f2ff3f;*/
                ">

                <td colspan="3" style="padding-right: 40px">
                    <div class="text-center">
                        <h3 style="color: #000; font-size:15px;line-height: 1; font-weight: 700; margin-top: -7mm;"
                            class="text-uppercase">
                            {{ $gafete->sgft_nombre }}
                        </h3>
                    </div>
                </td>
            </tr>
            <tr style="
                height: 15mm;
                border: 1px dotted transparent;
                /*background-color: #f2ff3f;*/
                ">

                <td colspan="3">
                    <div class="text-center" style="margin-top: -4mm">
                        <h3 style="color: #000; font-size:17px; font-weight: 700; margin-top: -10mm"
                            class="text-uppercase">
                            {{ trim($gafete->sgft_cargo) }}
                        </h3>
                    </div>
                </td>
            </tr>
            <tr style="border: 1px solid transparent">
                <td class="text-center" colspan="3" style="color: #000; font-size: 20px; font-weight: 700; line-height: 1;">
                    <div style="margin-top: -4mm">{{$anio_impresion}}</div>
                </td>
            </tr>

        </table>
    </div>


    <div class="page-container back" style="padding-left: 15px">

        @if($gafete->Local->lcal_cacs_id == 1)
            {{--            @include('gafetes.back-horizontal-admin',compact('gafete','folio'))--}}
            @include('gafetes.dinamico.back-horizontal-admin-2',compact('gafete','empleado','folio', 'es_vertical'))
        @else
            @include('gafetes.dinamico.back-horizontal',compact('gafete','empleado','folio', 'es_vertical'))
        @endif

    </div>

@endsection

