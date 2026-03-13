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
            background-image: url("{{ asset('storage/bg_gafetes/2022/'.$bg_img) }}");
            background-size: cover;
            width: 82.20mm; /*53.98*/
            height: 130.56mm; /*85.6*/
            /*background-color: #cccccc;*/
            /*border: 1px solid red;*/
        }

        .page-container{
            /*border: 1px solid black;*/
            display: block;
            width: 82mm;
            height: 130mm;
            margin: 0 auto;
            padding: 5px;
        }

        h5{
            color:black;
        }

        .tag-frame {
            height: 125mm;      /* Equals maximum image height */
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

    <div class="page-container front" style="padding-bottom:1px;">

        <table id="table-layout" class="" style="width: 100%;">

            <tr>
                <td style="
                            width: 18mm;
                            /*height: 126mm;*/
                            /*background-color: lightblue;*/

                            border: 1px dotted transparent;
                            " rowspan="5">

                    <div class="tag-frame">
                        <img
                            src="{{ asset('storage/'.$tag_image) }}"
                            alt="tag_local"
                            class="tag-image"
                        >
                    </div>

                </td>

                <td style="
                    height: 20mm;
                    border: 1px dotted transparent;
                    ">
                    <div class="text-center" style="padding-left:10px">
                        <h5 style="font-size:14px"> TERMINAL MARÍTIMA <br> PUERTA MAYA</h5>
                    </div>
                </td>
                <td style="width:11mm;
                            /*border: 1px dotted gray*/
                            ">
                    <div class="text-center">
                        <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:10mm;"  alt="logo">
                    </div>
                </td>
            </tr>


            <tr style="
                height: 45mm;
                border: 1px dotted transparent;
                /*background-color: lightblue;*/
                    ">
                <td colspan="2">
                    <div class="text-center">
                        <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto"
                             style="
                                border: 1px dotted transparent;
                                width: 35mm
                            ">
                    </div>
                </td>
            </tr>

            <tr style="
                height: 35mm;
                border: 1px dotted transparent;
                /*background-color: #f2ff3f;*/
                ">

                <td colspan="2">
                    <div class="text-center" style="">
                        <h3 style="color: #000; font-size:20px; line-height: 22px; font-weight: 700" class="text-uppercase">
                                {{ $gafete->sgft_nombre }}
                            {{--<div style="color: #000; font-weight:700; font-size:20px;">{{ trim($gafete->Local->lcal_nombre_comercial) }} </div>--}}
                            <div style="color: #000; font-weight:200; font-size:20px;">{{ trim($gafete->sgft_cargo) }} </div>
                        </h3>
                    </div>
                </td>
            </tr>

            <tr style="height: 24mm;
                    border: 1px solid transparent;
                    /*background-color: #ffa0b2;*/
                ">
                <td>
                    <div class="text-left"
                            style="padding-left: 10px;
                            /*padding-bottom: 10px*/
                                ">
                        {!! QrCode::size(70)->generate($gafete->toStringQr()); !!}
                    </div>
                </td>
                <td  class="text-center">
                    VIGENCIA <br>
                    <b style="font-size: 20px; color: black;">{{$anio_impresion}}</b>
                </td>
            </tr>

    </table>
    </div>


    <div class="page-container back" style="padding-left: 15px">

    @if($gafete->Local->lcal_cacs_id == 1)
{{--        @include('gafetes.back-horizontal-admin',compact('gafete','folio'))--}}
        @include('gafetes.2022.back-horizontal-admin-2',compact('gafete','empleado','folio'))
    @else
        @include('gafetes.2022.back-horizontal',compact('gafete','empleado','folio'))
    @endif

    </div>

@endsection

