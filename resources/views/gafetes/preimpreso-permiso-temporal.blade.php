@extends('reportes.main_gafete')

@section('content')

    @php
        //$color = $gafete->Empleado->Cargo->crgo_color;
        $color  = "brown";
        $folio  = $gafete->gfpi_id;
        $bg_img = "Pase_temporal.png"
    @endphp
    <style>

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

        <table id="table-layout" class="" style="width: 100%; margin-top: 25px; height: 82mm;">

            <tr>
                <td width="55%">

                    <img src="{{ url("images/pm_logo_2.png") }}" class="img-pm" style="width:33mm;" >
                    <div class="text-center">
                        <h5> TERMINAL <br> MARÍTIMA <br> PUERTA MAYA </h5>
                    </div>

                </td>
                <td>
                    <div class="text-center">
                        <br>
                        <br>
                        <br>
                        <h3 style="color: #000;">{{$gafete->gfpi_numero}}</h3>
                    </div>
                </td>
            </tr>

            <tr style="height: 35mm">
                <td colspan="2">
                    <div class="text-center" style="color: black">
                        <h3 style="color: #000;">PERMISO TEMPORAL</h3>
                    </div>
                </td>
            </tr>

            <tr style="height: 25mm;">
                <td colspan="2">
                    <div class="text-right" style="padding-right: 20px; padding-top: 12mm; color:white; ">
                        <h4 style="color: white;">{{$gafete->gfpi_created_at->format('Y')}}</h4>
                    </div>
                </td>
            </tr>


        </table>

    </div>

    <div class="page-container">

        @include('gafetes.back-02',compact('gafete','folio'))

    </div>


@endsection

