@extends('reportes.main_reports')

@section('content')
    <style>
        html {
            background-color: white;
        }

        body {
            background-color: white;
            font-size: 11px;
        }

        table.tabla-contenido,
        tbody {
            color: #000 !important;

        }

        .align-middle {
            vertical-align: middle !important;
        }

        #contenido-reporte {
            color: #000000;
        }

        .text-bold {
            font-weight: 700;
        }

        .text-blue-dark {
            color: #538dd5 !important;
        }

        .text-blue-light {
            color: #b7dee8 !important;
        }

        .bg-blue-dark {
            background: #538dd5 !important;
        }

        .bg-blue-light {
            background: #b7dee8 !important;
        }

        .bg-dark {
            background: #404040 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important
        }

        .p-0 {
            padding: 0 !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .p-2 {
            padding: 1rem !important;
        }

        .fw-bold {
            font-weight: bold !important;
        }
    </style>

    <div class="row" style="" id="contenido-reporte">
        <div class="col">

            <table class="table table-bordered tabla-contenido mb-0">
                <tr>
                    <td class="text-center align-middle p-1" style="width: 200px">
                        <img src="{{ asset('images/pm_logo_2.png') }}" alt="" style="height: 35px; margin: auto;">
                    </td>
                    <td class="text-center align-middle text-bold p-1">
                        <h4 class="text-blue-dark mb-0">
                            ANALISIS DE TRABAJO SEGURO (ATS) Y PERMISO DE TRABAJO DE "PUERTA MAYA"
                        </h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="bg-blue-light p-0">
                        <b>DATOS GENERALES:</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="p-0">
                        {{ 'DESCRIPCION BREVE DE LA ACTIVIDAD A DESARROLLAR: ' . $permiso->TrabajoEspecifico->tesp_nombre }}
                    </td>
                </tr>
            </table>
            <table class="table table-bordered tabla-contenido mb-0">
                <tr>
                    <td rowspan="2" class="text-center align-middle" style="width: 180px">
                        TIPO DE ACTIVIDAD
                    </td>
                    <td class="text-center align-middle p-1" style="width: 140px">
                        RUTINARIA
                    </td>
                    <td class="text-center align-middle p-1" style="width: 180px">
                        @if ($permiso->pmtt_tipo_actividad == 'RUTINARIA')
                            <b>X</b>
                        @endif
                    </td>
                    <td class="text-center align-middle p-1" style="width: 170px">
                        FECHAS DE VIGENCIA:
                    </td>
                    <td class="text-center align-middle p-0">
                        <table class="table mb-0">
                            <tr>
                                <td class="text-center align-middle p-1"
                                    style="width: 40px; border-top: 0 !important; border-bottom: 0 !important;">DEL</td>
                                <td class="text-center align-middle p-1"
                                    style="width: 180px; border-top: 0 !important; border-bottom: 0 !important;">
                                    <b>{{ Illuminate\Support\Carbon::parse($permiso->pmtt_vigencia_inicial)->format('d/m/Y') }}</b>
                                </td>
                                <td class="text-center align-middle p-1"
                                    style="width: 40px; border-top: 0 !important; border-bottom: 0 !important;">AL</td>
                                <td class="text-center align-middle p-1"
                                    style="border-top: 0 !important; border-bottom: 0 !important;">
                                    <b>{{ Illuminate\Support\Carbon::parse($permiso->pmtt_vigencia_final)->format('d/m/Y') }}</b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="text-center align-middle p-1">
                        NO RUTINARIA
                    </td>
                    <td class="text-center align-middle p-1">
                        @if ($permiso->pmtt_tipo_actividad == 'NO RUTINARIA')
                            <b>X</b>
                        @endif
                    </td>
                    <td class="text-center align-middle p-1">
                        HORARIO DE VIGENCIA:
                    </td>
                    <td class="text-center align-middle p-0">
                        <table class="table mb-0">
                            <tr>
                                <td class="text-center align-middle p-1"
                                    style="width: 60px; border-top: 0 !important; border-bottom: 0 !important;">DE LAS</td>
                                <td class="text-center align-middle p-1"
                                    style="width: 120px; border-top: 0 !important; border-bottom: 0 !important;">
                                    <b>{{ Illuminate\Support\Carbon::parse($permiso->pmtt_vigencia_inicial)->format('H:i') }}</b>
                                </td>
                                <td class="text-center align-middle p-1"
                                    style="width: 60px; border-top: 0 !important; border-bottom: 0 !important;">A LAS</td>
                                <td class="text-center align-middle p-1"
                                    style="width: 120px; border-top: 0 !important; border-bottom: 0 !important;">
                                    <b>{{ Illuminate\Support\Carbon::parse($permiso->pmtt_vigencia_final)->format('H:i') }}</b>
                                </td>
                                <td class="bg-dark p-1" style="border-top: 0 !important; border-bottom: 0 !important">

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table table-bordered mb-0">
                <tr>
                    <td class="text-center align-middle" style="width: 20%">
                        AREA ESPECIFICA DONDE SE REALIZARA LA ACTIVIDAD
                    </td>
                    <td class="text-center align-middle fw-bold" style="width: 20%">
                        {{ $permiso->Local->lcal_nombre_comercial }}
                    </td>
                    <td class="text-center align-middle p-0" style="width: 20%">
                        COMPAÑÍA CONTRATISTA (SOLO LLENAR ESTE PUNTO EN CASO DE SER PROVEEDOR EXTERNO)
                    </td>
                    <td class="text-center align-middle fw-bold" style="width: 20%">
                        {{ $permiso->pmtt_empresa }}
                    </td>
                    <td class="bg-dark" style="width: 20%"></td>
                </tr>
            </table>
            <table class="table table-bordered mb-0">
                <tr>
                    <td colspan="8" class="align-middle bg-blue-light p-0">
                        <b>ACTIVIDADES DE ALTO RIESGO INVOLUCRADAS</b>
                    </td>
                </tr>
                @foreach (array_chunk($actividadesAltoRiesgo, 4, true) as $grupo)
                    <tr>
                        @foreach ($grupo as $index => $actividad)
                            <td class="p-0 align-middle text-center" style="width: 5%">
                                @if ($permiso->ActividadesAltoRiesgo()->where('actividades_alto_riesgo.actar_id', $index)->exists())
                                    (&nbsp;&nbsp;<b>X</b>&nbsp;&nbsp;)
                                @else
                                    (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                @endif
                            </td>
                            <td class="p-0 align-middle" style="width: 20%">
                                {{ $actividad }}
                                @if ($index == 7 && $permiso->ActividadesAltoRiesgo()->where('actividades_alto_riesgo.actar_id', $index)->exists())
                                    :&nbsp;&nbsp;{{ $permiso->pmtt_otra_actividad_riesgo }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
            <table class="table table-bordered mb-0">
                <tr>
                    <td colspan="4" class="align-middle bg-blue-light p-0">
                        <b>EVALUACIÓN DE RIESGOS DE LA ACTIVIDAD</b>
                    </td>
                </tr>
                <tr>
                    <td class="text-center align-middle bg-blue-dark p-2 fw-bold" style="width: 30%">
                        Descripción de trabajo (Por pasos)
                    </td>
                    <td class="text-center align-middle bg-blue-dark p-2 fw-bold" style="width: 20%">
                        Riesgo(s) asodiado(s)
                    </td>
                    <td class="text-center align-middle bg-blue-dark p-2 fw-bold" style="width: 30%">
                        Medida de control de riesgo
                    </td>
                    <td class="text-center align-middle bg-blue-dark p-2 fw-bold" style="width: 20%">
                        Equipo o herramientas necesarias
                    </td>
                </tr>
                @foreach ($dataTable as $index => $data)
                    <tr>
                        @if ($index == 0)
                            <td class="text-center align-middle p-2" rowspan="{{ count($dataTable) }}">
                                {!! nl2br($permiso->pmtt_trabajo) !!}
                            </td>
                        @endif
                        <td class="p-0">
                            @if ($data['riesgosAsociados'])
                                {{ $data['riesgosAsociados']['nombre'] }}
                                @if ($permiso->RiesgosAsociados()->where('riesgos_asociados.rasoc_id', $data['riesgosAsociados']['id'])->exists())
                                    (&nbsp;<b>X</b>&nbsp;)
                                @else
                                    (&nbsp;&nbsp;&nbsp;)
                                @endif
                            @endif
                        </td>
                        <td class="p-0">
                            @if ($data['medidasControlRiesgo'])
                                {{ $data['medidasControlRiesgo']['nombre'] }}
                                @if ($permiso->MedidasControlRiesgo()->where('medidas_control_riesgo.medcr_id', $data['medidasControlRiesgo']['id'])->exists())
                                    @if ($data['medidasControlRiesgo']['id'] == 2)
                                        :&nbsp;&nbsp;{{ $permiso->pmtt_dispositivo_bloquear }}
                                    @else
                                        (&nbsp;<b>X</b>&nbsp;)
                                    @endif
                                @else
                                    (&nbsp;&nbsp;&nbsp;)
                                @endif
                            @endif
                        </td>
                        <td class="p-0">
                            @if ($data['equiposHerramientas'])
                                {{ $data['equiposHerramientas']['nombre'] }}
                                @if ($permiso->EquiposHerramientas()->where('equipos_herramientas.eqher_id', $data['equiposHerramientas']['id'])->exists())
                                    (&nbsp;<b>X</b>&nbsp;)
                                @else
                                    (&nbsp;&nbsp;&nbsp;)
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <table class="table table-bordered mb-0">
                <tr>
                    <td colspan="6" class="align-middle bg-blue-light p-0">
                        <b>LISTADO CON NOMBRE Y FIRMA DEL PERSONAL QUE REALIZARÁ LA ACTIVIDAD (INCLUYENDO AL SUPERVISOR
                            RESPONSABLE DE LA ACTIVIDAD)</b>
                    </td>
                </tr>
                <tr>
                    <td class="bg-blue-dark text-center p-1" style="width: 5%">No.</td>
                    <td class="bg-blue-dark text-center p-1" style="width: 45%">Nombre Trabajador</td>
                    <td class="bg-blue-dark text-center p-1" style="width: 5%">No.</td>
                    <td class="bg-blue-dark text-center p-1" style="width: 45%">Nombre Trabajador</td>
                </tr>
                <?php
                $pos = 1;
                ?>
                @foreach (array_chunk($trabajadores, 2) as $group)
                    <tr>
                        @foreach ($group as $trabajador)
                            <td class="text-center p-1">{{ $pos }}</td>
                            <td class="text-center p-1">{{ $trabajador['nombre'] }}</td>
                            <?php
                            $pos++;
                            ?>
                        @endforeach
                    </tr>
                @endforeach
            </table>
            <table class="table table-bordered mb-0">
                <tr>
                    <td colspan="3" class="align-middle bg-blue-light p-0">
                        <b>AUTORIZACIONES DEL PERMISO DE TRABAJO</b>
                    </td>
                </tr>
                <tr>
                    <td class="text-center align-middle p-1" style="width: 33.33%; padding-top: 4rem !important;">
                        <b>{{ $permiso->pmtt_representante }}</b>
                    </td>
                    <td class="text-center align-middle p-1" style="width: 33.33%; padding-top: 4rem !important;">
                        <b>{{ $permiso->MantenimientoAprobadoPor->name }}</b>
                    </td>
                    <td class="text-center align-middle p-1" style="width: 33.33%; padding-top: 4rem !important;">
                        <b>{{ $permiso->HessAprobadoPor->name }}</b>
                    </td>
                </tr>
                <tr>
                    <td class="text-center p-0" style="width: 33.33%;">
                        SUPERVISOR CONTRATISTA (SOLO SI EL TRABAJO LO EFECTUARA UN PROVEEDOR)
                    </td>
                    <td class="text-center p-0" style="width: 33.33%;">
                        SUPERVISOR RESPONSABLE DE LA ACTIVIDAD
                    </td>
                    <td class="text-center p-0" style="width: 33.34%;">
                        COORDINADOR HES
                    </td>
                </tr>
                <tr>
                    <td rowspan="2" class="text-center p-0">
                        <span class="text-blue-dark fw-bold">Evalúa actividad</span>
                    </td>
                    <td class="text-center p-0">
                        <span class="text-blue-dark fw-bold">Evalúa y autoriza la actividad</span>
                    </td>
                    <td class="text-center p-0">
                        <span class="text-blue-dark fw-bold">Autoriza actividad y Audita medidas establecidas</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center p-0">
                        <span class="text-blue-dark fw-bold">Se debe de contar con las firmas de todos los que autorizan
                            para hacer valido este formato</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="p-0 text-center" style="background: lightgrey">
                        <b>ESTE DOCUMENTO DEBE DE ESTAR VISIBLE EN EL LUGAR DE TRABAJO A MODO DE COPIA Y EL ORIGINAL DEBE
                            RESGUARDARSE EN LAS OFICINAS DE HES</b> <br>
                        <b>ADVERTENCIA: ESTE FORMATO AUTORIZA LA ACTIVIDAD PLANTEADA Y SE ANULA EN CASO DE REALIZARSE
                            CUALQUIER ACCION FUERA DE LO ESTIPULADO EN EL</b>
                    </td>
                </tr>
            </table>

        </div>
    </div>
@endsection
