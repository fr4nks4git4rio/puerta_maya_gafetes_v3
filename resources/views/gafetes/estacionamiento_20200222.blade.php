@extends('reportes.main_gafete')

@section('content')

    @php
        $color = '#FFF';
        $folio = "";
    @endphp
    <style>

        .top-color{
            display: block;
            background-color: {{ $color }};
            margin: 0;
            width: 100%;
            height: 10mm;
        }

        .foot-color{
            /* display: block; */
            /* float: left; */
            position: absolute;
            top: 346px;
            /* right:10px; */

            /* margin-bottom: 0; */
            {{--background-color: {{ $color }};--}}
            color: black;
            margin: 0;
            width: 100%;
            height: 20mm;
        }

        .top-year{
            display: block;
            color: black;
            font-weight: bold;
            width: 100%;
            text-align: right;
            padding-right: 5mm;
            padding-top: 5mm;
            font-size: 18px;
        }

        .logo-container{
            display: inline-block;
            /* border: 1px solid red; */
            width: 25mm;
            /* margin-right: 4mm; */
        }

        .photo-container{
            display: inline-block;
             /*border: 1px solid blue;*/

            /* margin-top: -10mm; */
             margin-left: 2mm;
        }

        /* .im-pm{
            width: 85%;
        } */

        .foto{
            width: 35mm;
        }

        .nombre{
            font-size: 18px;
            font-weight: bold;
            color:black;
        }

        .page-container{
            /*border: 1px solid red;*/
            display: block;
            width: 82mm;
            height: 130mm;
            margin: 0 auto;
            padding: 10px;


        }

        .top-color{
            background-image: url("{{ url("images/pm_logo_2.png") }}");

        }

    </style>




    <div class="top-color">

    <span class="top-year">
        {{ $gafete->gest_numero  }} de {{ $gafete->gest_tipo == 'AUTO' ? $gafete->Local->lcal_espacios_autos : $gafete->Local->lcal_espacios_motos }}
    </span>


    </div>



    {{-- <div class="row"> --}}

    <br>


    <div class="logo-container" style="margin:2mm; padding-left: 2mm; " class="text-center">

        <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:28mm;" >
{{--        <div class="text-center">--}}
{{--            <h5> TERMINAL <br> MARÍTIMA <br> PUERTA MAYA </h5>--}}
{{--        </div>--}}

    </div>

    <div class="photo-container" style="margin-top:2mm; position:fixed;">
        {{--    <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto">--}}
{{--        <br>--}}
        <div class="text-center" style="padding: 4px">
            <br>
            <h4 style="color:#000;">{{$gafete->Local->lcal_identificador}} <br> {{$gafete->Local->lcal_nombre_comercial}}</h4>
        </div>
    </div>

    <div class="row">
        <br>
        <p class="col text-center nombre">

        </p>


    </div>

    <div class="row">
        <div class="col text-center imagen" style="margin: 0 auto">
            @if($gafete->gest_tipo == 'AUTO')
                <img src="{{ asset('images/automovil.jpg')  }}" alt="" style="height: 25mm">
            @else
                <img src="{{ asset('images/moto.jpg')  }}" alt="" style="height: 25mm">
            @endif
        </div>
    </div>

{{--    <div class="row">--}}

{{--        --}}{{-- <div class="col-12" style="border:1px solid green"> --}}
{{--        <div class="col-12">--}}
{{--            --}}{{-- <br> --}}
{{--            <b>{{$gafete->Local->lcal_nombre_comercial}}</b> <br>--}}
{{--            <small> {{ $gafete->sgft_cargo }} </small>--}}
{{--        </div>--}}



{{--    </div>--}}

    <div class="foot-color" style="padding: 15px">
        <br>
        <div style="display: inline-block">{{$gafete->gest_numero_nfc}}</div>
        <div  style="display: inline-block" class="text-right pull-right" style="padding: 15px;">
            <br>
            <br>
{{--                    <small>Vencimiento</small>  <br>--}}
            <b style="font-size: 25px" >{{ $gafete->gest_anio  }}</b>
        </div>
    </div>

     </div>

    <div class="page-container">
        <br>
        <br>
        <br>
        <br>
        <br>
        @include('gafetes.back-horizontal',compact('gafete','folio'))
    </div>

@endsection
