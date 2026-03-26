@extends('reportes.main_reports')

@section('content')

    <style>

        .report-title{
            color: #0b0b0b;
            font-size: 18px;
            font-weight: bold;
        }

        html{
            background-color: white;
        }

        body{
            background-color: white;
        }

        table#tabla-contenido, tbody{
            color: #000; !important;

        }

        #contenido-reporte{
            color: #000000;
            font-size: large;
        }
    </style>

    @include('web.reportes.ajenos-en-casa',compact('records','dia'))


@endsection
