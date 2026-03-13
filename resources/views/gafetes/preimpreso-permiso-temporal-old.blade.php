@extends('reportes.main_gafete')

@section('content')

    @php
        $color = '#cd630d';
        $folio = $gafete->gfpi_numero;
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
            background-color: {{ $color }};
            margin: 0;
            width: 100%;
            height: 20mm;
        }

        .top-year{
            display: block;
            color: white;
            font-weight: bold;
            width: 100%;
            text-align: right;
            padding-right: 2mm;
            padding-top: 2mm;
        }

        .logo-container{
            display: inline-block;
            /* border: 1px solid red; */
            width: 35mm;
            /* margin-right: 4mm; */
        }

        .photo-container{
            display: inline-block;
            /* border: 1px solid blue; */
            width: 25mm;
            height: 45mm;
            /* margin-top: -10mm; */
            /* margin-left: 2mm; */
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

    </style>




    <div class="top-color">

    <span class="top-year">
        {{ substr($gafete->sgft_fecha,0,4)  }}
    </span>


    </div>



    {{-- <div class="row"> --}}

    <br>


    <div class="logo-container" style="margin:2mm; padding-left: 2mm; " class="text-center">

        <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:30mm;" >
        <div class="text-center">
            <h5> TERMINAL <br> MARÍTIMA <br> PUERTA MAYA </h5>
        </div>

    </div>

    <div class="photo-container" style="margin-top:2mm; position:fixed;">
        {{--    <img src="{{$gafete->sgft_foto_web}}" alt="" class="foto">--}}
        <br>
        <div class="text-center">
            <br>
            <br>
            <br>
            <h3>{{$gafete->gfpi_numero}}</h3>
        </div>
    </div>

    <div class="row">
        <br>
        <p class="col text-center nombre">
            <b>PERMISO TEMPORAL</b> <br>
            {{-- <b>Fabiola Cabrera Ruiz</b> <br> --}}
        </p>
    </div>

{{--    <div class="row">--}}

{{--        --}}{{-- <div class="col-12" style="border:1px solid green"> --}}
{{--        <div class="col-12">--}}
{{--            --}}{{-- <br> --}}
{{--            <b>{{$gafete->Local->lcal_nombre_comercial}}</b> <br>--}}
{{--            <small> {{ $gafete->sgft_cargo }} </small>--}}
{{--        </div>--}}



{{--    </div>--}}

    <div class="foot-color">
        <div class="text-right" style="color: white; padding: 5px;">
            <br>
            {{--        <small>Vencimiento</small>  <br>--}}
            <b>{{ $gafete->gfpi_created_at->format('Y')  }}</b>
        </div>
    </div>

    {{-- </div> --}}

    @include('gafetes.back-01',compact('gafete','folio'))

@endsection