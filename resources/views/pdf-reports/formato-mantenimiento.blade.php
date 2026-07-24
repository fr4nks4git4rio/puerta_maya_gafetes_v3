@extends('reportes.main_reports')

@section('content')
    <style>
        .report-title {
            color: #0b0b0b;
            font-size: 18px;
            font-weight: bold;
        }

        html {
            background-color: white;
        }

        body {
            background-color: white;
        }

        table#tabla-contenido,
        tbody {
            color: #000;
            !important;

        }

        #contenido-reporte {
            color: #000000;
            font-size: large;
        }
    </style>

    <div class="row" style="height: 140px">

        <div class="col-2 pull-left">
            <span><img src="{{ asset('images/pm_logo_2.png') }}" alt="" width="100px"></span>
        </div>

        <div class="col-8">&nbsp;</div>

        <div class="col-2 pull-right">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($permiso->toStringQr()) !!}
        </div>

    </div>

    <div class="row" style="" id="contenido-reporte">
        <div class="col">

            <p><b>SOLICITUD DE PERMISO TEMPORAL PARA TRABAJO DE MANTENIMIENTO</b></p>

            <table id="tabla-contenido" class="table table-bordered">
                <tr>
                    <td width="58%; color:black;">
                        <b>Folio:</b> {{ $permiso->pmtt_id }}
                    </td>
                    <td class="text-right">
                        <b>Fecha de solicitud:</b> {{ $permiso->pmtt_fecha }}
                    </td>
                </tr>
                <tr>
                    <td width="58%; color:black;">
                        <b>Local:</b> {{ $permiso->Local->lcal_identificador }} &nbsp;
                        {{ $permiso->Local->lcal_nombre_comercial }}
                    </td>
                    <td class="text-right">
                        <b>Días solicitados para los trabajos: </b> {{ $permiso->pmtt_dias }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Vigencia: </b> DEL {{ $permiso->pmtt_vigencia_inicial }} AL {{ $permiso->pmtt_vigencia_final }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <b>Empresa: </b> {{ $permiso->pmtt_empresa }}
                    </td>
                    <td>
                        <b>Representante: </b> {{ $permiso->pmtt_representante }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <b>Tipo de Mantenimiento: </b> {{ optional($permiso->TipoMantenimiento)->tmtt_nombre }}
                    </td>
                    <td>
                        <b>Trabajo Específico: </b> {{ optional($permiso->TrabajoEspecifico)->tesp_nombre }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Descripción de las actividades a realizar: </b> {{ $permiso->pmtt_trabajo }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Listado de EPP:</b><br>
                        <table class="table table-striped table-sm table-condensed m-t-10">
                            <tbody>
                                @if ($permiso->TrabajoEspecifico)
                                    @foreach ($permiso->TrabajoEspecifico->EppBasicos as $eppBasico)
                                        <tr>
                                            <td>{{ $eppBasico->eppb_nombre }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach ($permiso->TrabajoEspecifico->EppEspecificos as $eppEspecifico)
                                        <tr>
                                            <td>{{ $eppBasico->eppe_nombre }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Listado de Trabajadores: </b> <br>
                        <table class="table table-striped table-sm table-condensed m-t-10">
                            <tbody>
                                @foreach ($permiso->Trabajadores as $trabajador)
                                    <tr>
                                        <td>{{ $trabajador->pmtb_nombre }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Observaciones: </b> {{ $permiso->pmtt_observaciones }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Comentario de Aprobación: </b> {{ $permiso->pmtt_comentario_admon }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <b>Estado: </b> {{ $permiso->pmtt_estado }}
                    </td>
                </tr>



            </table>

            <br>

            <p style="text-align: justify">
                <!-- <small class="text-justify">
            Por este conducto hago saber y deslindo de toda responsabilidad civil, laboral y penal a la empresa Cozumel Cruise Terminal S.A de C.V. y a sus representantes legales, derivado de cualquier accidente de los trabajos que se realicen dentro de sus instalaciones, trabajos a realizar por el personal de la empresa ( <b>{{ $permiso->pmtt_empresa }}</b> ) Representada por <b>{{ $permiso->pmtt_representante }}</b> hago constar que los empleados de <b>{{ $permiso->pmtt_empresa }}</b> están capacitados y tienen experiencia para realizar las actividades encomendadas a si mismo tienen el conocimiento de conocer y tomar las medidas de seguridad necesarias para desempeñar su trabajo y cuidar de su personal, así como también como de las personas que los ayudan a realizar los trabajos para los cuales fueron contratados.<br> Además confirmo conocer, seguir y estar de acuerdo en los derechos y obligaciones estipuladas en las normas de seguridad para contratistas de la empresa  <b>{{ $permiso->pmtt_empresa }}</b>
                            </small> -->
            <p class="text-justify" style="line-height: 1.2; font-size: 14px;">
                Por este conducto hago saber y deslindo de toda responsabilidad civil, laboral y penal a la empresa Cozumel
                Cruise Terminal S.A de C.V.
                y a sus representantes legales, derivado de cualquier accidente de las actividades de mantenimiento que se
                realicen dentro de sus instalaciones
                por el personal de la empresa ( <b>{{ $permiso->pmtt_empresa }}</b> ) Representada por
                <b>{{ $permiso->pmtt_representante }}</b>. Hago constar que los empleados de
                <b>{{ $permiso->pmtt_empresa }}</b> están
                capacitados y tienen la experiencia para realizar las actividades encomendadas, a si mismo tienen el
                conocimiento sobre las medidas de
                seguridad y uso de equipo de protección personal necesarios para desempeñar sus actividades de manera
                segura, así también, de las personas
                que los ayudan a realizar los trabajos para los cuales fueron contratados. <br> Además confirmo conocer,
                seguir y estar de acuerdo en los derechos y
                obligaciones estipuladas en las normas de seguridad para contratistas de la empresa
                <b>{{ $permiso->pmtt_empresa }}</b>.
            </p>

            </p>

            <br>
            <br>
            <br>
            {{--            <br> --}}

            <table style="width: 100%; font-size: medium">
                <tr>
                    <td class="text-center">
                        <p><b>Solicitado por</b></p>
                        {{--                        <br> --}}
                        {{--                        <br> --}}
                        {{--                        <p>__________________________________</p> --}}
                        <p>{{ $permiso->pmtt_solicitante }}<br>&nbsp;</p>
                    </td>
                    <td class="text-center">
                        <p><b>Autorizado por</b></p>
                        {{--                        <br> --}}
                        {{--                        <br> --}}
                        {{--                        <p>__________________________________</p> --}}
                        <p>
                            {{ $permiso->MantenimientoAprobadoPor->name }}
                            @if ($permiso->HessAprobadoPor)
                                y {{ $permiso->HessAprobadoPor->name }}
                            @endif
                            <br>
                            Puerta Maya
                        </p>

                    </td>

                </tr>

            </table>

        </div>
    </div>
@endsection
