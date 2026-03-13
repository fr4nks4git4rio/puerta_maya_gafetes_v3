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

        .debug-table{
            /*border: 1px solid red;*/
            /*span: 5px;*/
        }

        .debug-table  td{
            /*border: 1px solid blue;*/
        }

        .bg-gray{
            background-color: #aac9d7;
        }

        .table-vigencia td{
            border: 1px solid black;
            padding: 15px;
        }

        .table-002 td{
            border: 1px solid black;
            padding: 4px;
        }

        td.label-cell{
            padding: 6px;
            background-color: #aac9d7;
            font-weight: bold;
        }
    </style>

    @php
        $inicio = \Carbon\Carbon::createFromFormat('Y-m-d',$permiso->ptmp_vigencia_inicial);
        $fin = \Carbon\Carbon::createFromFormat('Y-m-d',$permiso->ptmp_vigencia_final);
        $fecha = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$permiso->ptmp_fecha);

        $dias = $inicio->diffInDays($fin);

    @endphp


    <div class="row">
        <div class="col-sm-12">

            <table class="debug-table" style="width: 100%">
                <tr>
                    <td style="width: 25%">
                        <span><img src="{{asset('images/pm_logo_2.png')}}" alt="" width="100px"></span>
                    </td>
                    <td style="width: 50%">
                        <div class="text-center"><b style="font-size: 18px">PERMISO DE INGRESO A LA PLAZA COMERCIAL</b></div>
                        <br>
                        <table class="table-vigencia" style="boder:1px solid black; margin: auto">
                            <tr>
                                <td class="bg-gray"><b>VIGENCIA</b></td>
                                <td>Del {{$inicio->format('d / m / Y')}}</td>
                                <td>al {{$fin->format('d / m / Y') }}</td>
                            </tr>
                        </table>

                    </td>
                    <td>
                        <div style="margin: auto; font-weight: bold; font-size: 18px;" class="text-danger text-center">
                            No. {{$permiso->ptmp_id}}</div>
                        <div class="text-center">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($permiso->toStringQr()) !!}
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">

            <table class="table-002" style="margin: auto; width: 100%">
                <tr>
                    <td><b>Visita con carácter de</b></td>
                    <td class="text-center">
                        CONTRATISTA <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;
                        PROVEEDOR <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;
                        COBRATARIO <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;
                        TECNICO <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;
                        VISITA <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;
                        OTRO(COMENTAR) <i class="fa fa-square-o"></i>
                    </td>
                </tr>
                <tr>
                    <td><b>Objeto</b></td>
                    <td class="text-center">
                        CARRETA <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        MAQUINARIA <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        EQUIPO <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        MATEARIAL <i class="fa fa-square-o"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        OTRO(COMENTAR) <i class="fa fa-square-o"></i>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table class="table-002" style="margin: auto; width: 100%">
                <tr>
                    <td class="label-cell" style="width: 10%">Nombre</td>
                    <td>{{$permiso->ptmp_nombre}}</td>
                    <td class="label-cell text-center" style="width: 20%">FECHA DE EMISION</td>
                    <td class="text-center" style="width: 20%">{{ $fecha->format('d / m / Y H:i:s') }}</td>
                </tr>

                <tr>
                    <td class="label-cell" style="width: 10%">Compañia</td>
                    <td>{{$permiso->Local->lcal_nombre_comercial}}</td>
                    <td class="label-cell text-center" style="width: 20%">Hora de Ingreso</td>
                    <td class="label-cell text-center" style="width: 20%">Hora de Salida</td>
                </tr>

                <tr>
                    <td class="label-cell" style="width: 10%">Asunto</td>
                    <td>Pase temporal</td>
                    <td class="label-cell text-center" style="width: 20%"> -------- </td>
                    <td class="label-cell text-center" style="width: 20%"> -------- </td>
                </tr>

            </table>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-12">

            <table class="table-002" style="width: 100%">
                <tr>
                    <td><b>Comentarios:</b></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td> <div class="text-center"> Válido por {{$dias}} días </div></td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>

            </table>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="text-justify" style="font-size: 10px">
                <br>
                EL VISITANTE ACEPTA INRESAR BAJO SU PROPIA RESPONSABLIDAD Y RIESGO, Y LIBERAR A PUERTA MAYA DE CUALQUIER RESPONSABILIDAD DE CARÁCTER LABORAL, PENAL Y CIVIL, Y ASIMISMO SE COMPROMETE A RESPETAR LAS NORMAS Y DISPOSICIONES DE SEGURIDAD DE LA EMPRESA DURANTE SU ESTANCIA PORTANDO EN TODO MOMENTO EL GAFETE DE IDENTIFICACIÓN PROPORCIONADA POR LA MISMA.
            </p>
        </div>
    </div>

    <br>

            <table style="width: 100%; font-size: 12px">
                <tr>
                    <td class="text-center">
                        <p><b>AUTORIZÓ</b></p>
                        <br>
                        <br>
                        <br>
                        <p>__________________________________</p>
{{--                        <p class="text-center"> <b class="text-uppercase">{{ auth()->user()->name }}</b>&nbsp;</p>--}}
                        <p class="text-center"> <b class="text-uppercase">{{ $permiso->AprobadoPor->name ?? 'SEGURIDAD' }}</b>&nbsp;</p>
                    </td>

                    <td class="text-center">
                        <p><b>DE CONFORMIDAD CON LOS <br>TERMINOS DE LA EMPRESA</b></p>
                        <br>
                        <br>
                        <p>__________________________________</p>
                        <p>
                            <b>VISITANTE</b>
                        </p>

                    </td>

                    <td class="text-center">
                        <p><b>SUPERVISO</b></p>
                        <br>
                        <br>
                        <br>
                        <p>__________________________________</p>
                        <p>
                            <b>NOMBRE Y FIRMA DEL GUARDIA</b>
                        </p>

                    </td>

                </tr>

            </table>

        </div>
    </div>

@endsection
