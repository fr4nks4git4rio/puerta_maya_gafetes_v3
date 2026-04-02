@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-chart">&nbsp;</span> Reportes
@endsection

@section('content')

    @php

        $reportes_select = \Arr::pluck($reportes, 'nombre','key');

    @endphp



    <div class="row">
        <div class="col-md-12">
            <div class="card card-color">

                <div class="card-body">

                    <label style="cursor:pointer" id="filter-toggle"> <i class="fa fa-check"></i> Selección de
                        reporte</label>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::select('reporte', $reportes_select ,  head(array_keys($reportes_select)) , ["class"=>"form-control select2-control", "placeholder" => "Seleccione una opción", 'id' => 'reporte', "style" => "width: 100%" ])!!}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <label style="cursor:pointer" id="filter-toggle"> <i class="fa fa-filter"></i> Filtros del
                        reporte</label>

                    <div class="row">

                        <div class="col-sm-1 ctrl-dia">
                            <label for="fecha">Fecha</label>
                        </div>
                        <div class="col-sm-2 ctrl-dia">
                            {!! Form::text('dia', date('Y-m-d') , ["class"=>"form-control",'id'=>'dia'])!!}
                        </div>


                        <div class="col-sm-1 ctrl-fechas">
                            <label for="inicio">Inicio</label>
                        </div>
                        <div class="col-sm-2 ctrl-fechas">
                            {!! Form::text('inicio', null , ["class"=>"form-control",'id'=>'inicio'])!!}
                        </div>


                        <div class="col-sm-1 ctrl-fechas">
                            <label for="fin">Fin</label>
                        </div>
                        <div class="col-sm-2 ctrl-fechas">
                            {!! Form::text('fin', null , ["class"=>"form-control",'id'=>'fin'])!!}
                        </div>


                        <div class="col-sm-1 ctrl-estados-ptmp">
                            <label for="estado">Estado</label>
                        </div>
                        <div class="col-sm-2 ctrl-estados-ptmp">
                            {!! Form::select('estado_ptmp', $estados_ptmp, null , ["class"=>"form-control",'id'=>'estado_ptmp'])!!}
                        </div>


                        <div class="col-sm-1 ctrl-estados-pmant">
                            <label for="estado">Estado</label>
                        </div>
                        <div class="col-sm-2 ctrl-estados-pmant">
                            {!! Form::select('estado_pmant', $estados_pmant, null , ["class"=>"form-control",'id'=>'estado_pmant'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-estados-gipa">
                            <label for="estado">Estado</label>
                        </div>
                        <div class="col-sm-2 ctrl-estados-gipa">
                            {!! Form::select('estado_gipa', $estados_gipa, null , ["class"=>"form-control",'id'=>'estado_gipa'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-estados-cpago">
                            <label for="estado">Estado</label>
                        </div>
                        <div class="col-sm-2 ctrl-estados-cpago">
                            {!! Form::select('estado_cpago', $estados_cpago, null , ["class"=>"form-control",'id'=>'estado_cpago'])!!}
                        </div>


                        <div class="col-sm-6 ctrl-locales">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="estado">Local</label>
                                </div>
                                <div class="col-sm-10">
                                    {!! Form::select('local', $locales, null , ["class"=>"form-control select2-control border",'id'=>'local'])!!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 ctrl-razonsociales">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="estado">Razón Social</label>
                                </div>
                                <div class="col-sm-10">
                                    {!! Form::select('razon_social', $razonsociales, null , ["class"=>"form-control select2-control border",'id'=>'razon_social'])!!}
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-1 ctrl-nombre-gafete">
                            <label for="estado">Nombre Empleado</label>
                        </div>
                        <div class="col-sm-2 ctrl-nombre-gafete">
                            {!! Form::text('nombre_gafete', null , ["class"=>"form-control",'id'=>'nombre_gafete'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-numero-rfid">
                            <label for="estado">Numero Tarjeta</label>
                        </div>
                        <div class="col-sm-2 ctrl-numero-rfid">
                            {!! Form::text('numero_rfid', null , ["class"=>"form-control",'id'=>'numero_rfid'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-puerta">
                            <label for="puerta">Puerta</label>
                        </div>
                        <div class="col-sm-2 ctrl-puerta">
                            {!! Form::select('puerta', $puertas, null , ["class"=>"form-control",'id'=>'puerta'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-hora">
                            <label for="estado">Hora</label>
                        </div>
                        <div class="col-sm-2 ctrl-hora">
                            {!! Form::select('horas', $horas, null , ["class"=>"form-control",'id'=>'hora'])!!}
                        </div>

                        <div class="col-sm-1 ctrl-tipo-gafete">
                            <label for="tipo_gafete">Tipo Gafete</label>
                        </div>
                        <div class="col-sm-2 ctrl-tipo-gafete">
                            {!! Form::select('tipos_gafete', $tipos_gafete, null , ["class"=>"form-control",'id'=>'tipo_gafete'])!!}
                        </div>


                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-sm-4">
                            <span class="btn btn-primary" id="btn-generar"> <i class="fa fa-bolt"></i> Generar</span>
                            <span class="btn btn-primary" id="btn-generar-pdf"> <i
                                        class="fa fa-file-pdf-o"></i> PDF</span>
                            <span class="btn btn-primary" id="btn-generar-excel"> <i class="fa fa-file-excel-o"></i> EXCEL</span>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">

            <div class="card card-color">

                <div class="card-body" id="report-container">


                    <p class="alert alert-info">Seleccione un reporte...</p>

                </div>
            </div>


        </div>
    </div>



    <script type="text/javascript">
        $(document).ready(function () {

            // $.fn.modal.Constructor.prototype.enforceFocus = function () {};
            $('div.datepicker').css('z-index', '9999 !important');

            jReportes = {

                usr_lcal_id: '{{ auth()->user()->usr_lcal_id  }}',

                config: {!! json_encode($reportes) !!},

                init: function () {
                    let $this = this;
                    console.log(this.config);

                    $('.select2-control').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });

                    $('#inicio').datepicker({
                        autoclose: true,
                        format: 'yyyy-mm-dd',
                        language: '{{App::getLocale()}}',
                        todayHighlight: true,
                        orientation: 'top'
                    });

                    $('#fin').datepicker({
                        autoclose: true,
                        format: 'yyyy-mm-dd',
                        language: '{{App::getLocale()}}',
                        todayHighlight: true,
                        orientation: 'top'
                    });

                    $('#dia').datepicker({
                        autoclose: true,
                        format: 'yyyy-mm-dd',
                        language: '{{App::getLocale()}}',
                        todayHighlight: true,
                        orientation: 'top'
                    });

                    $('#btn-generar').on('click', function () {
                        $this.generarReporte(false);
                    });

                    $('#btn-generar-pdf').on('click', function () {
                        $this.generarReporte(true);
                    });

                    $('#btn-generar-excel').on('click', function () {
                        $this.generarReporteExcel(true);
                    });

                    $this.showControls($('#reporte').val());

                    $('#reporte').change(function () {
                        $this.showControls($(this).val());
                    });

                },


                showControls: function (reportKey) {
                    let $this = this;
                    let config = $this.getConfigByKey(reportKey);
                    // console.log(reportKey,config);

                    $('.ctrl-dia').addClass('d-none');
                    $('.ctrl-fechas').addClass('d-none');
                    $('.ctrl-estados-ptmp').addClass('d-none');
                    $('.ctrl-estados-pmant').addClass('d-none');
                    $('.ctrl-estados-gipa').addClass('d-none');
                    $('.ctrl-estados-cpago').addClass('d-none');
                    $('.ctrl-locales').addClass('d-none');
                    $('.ctrl-razonsociales').addClass('d-none');
                    $('.ctrl-nombre-gafete').addClass('d-none');
                    $('.ctrl-numero-rfid').addClass('d-none');
                    $('.ctrl-tipo-gafete').addClass('d-none');
                    $('.ctrl-hora').addClass('d-none');
                    $('.ctrl-puerta').addClass('d-none');

                    $('#btn-generar').addClass('d-none');
                    $('#btn-generar-pdf').addClass('d-none');
                    $('#btn-generar-excel').addClass('d-none');

                    if (config.dia == true) {
                        $('.ctrl-dia').removeClass('d-none');
                    }

                    if (config.fechas == true) {
                        $('.ctrl-fechas').removeClass('d-none');
                    }

                    if (config.locales == true) {
                        $('.ctrl-locales').removeClass('d-none');
                    }

                    if (config.razonsociales == true) {
                        $('.ctrl-razonsociales').removeClass('d-none');
                    }

                    if (config.estados_ptmp == true) {
                        $('.ctrl-estados-ptmp').removeClass('d-none');
                    }

                    if (config.estados_gipa == true) {
                        $('.ctrl-estados-gipa').removeClass('d-none');
                    }

                    if (config.estados_pmant == true) {
                        $('.ctrl-estados-pmant').removeClass('d-none');
                    }

                    if (config.estados_cpago == true) {
                        $('.ctrl-estados-cpago').removeClass('d-none');
                    }

                    if (config.nombre_gafete == true) {
                        $('.ctrl-nombre-gafete').removeClass('d-none');
                    }

                    if (config.numero_rfid == true) {
                        $('.ctrl-numero-rfid').removeClass('d-none');
                    }

                    if (config.tipos_gafete == true) {
                        $('.ctrl-tipo-gafete').removeClass('d-none');
                    }

                    if (config.hora == true) {
                        $('.ctrl-hora').removeClass('d-none');
                    }

                    if (config.puertas == true) {
                        $('.ctrl-puerta').removeClass('d-none');
                    }

                    //---------------
                    if (config.do_html == true) {
                        $('#btn-generar').removeClass('d-none');
                    }

                    if (config.do_pdf == true) {
                        $('#btn-generar-pdf').removeClass('d-none');
                    }

                    if (config.do_xlsx == true) {
                        $('#btn-generar-excel').removeClass('d-none');
                    }

                },

                getConfigByKey: function (reportKey) {
                    let $this = this;

                    let record = $this.config.filter(
                        function (data) {
                            return data.key == reportKey
                        }
                    );

                    if (record.length > 0) {
                        return record[0];
                    }

                    return [];
                },

                generarReporte: function (pdf) {
                    let $this = this;
                    let config = $this.getConfigByKey($('#reporte').val());

                    let url = config.url;
                    let inicio = $('#inicio').val();
                    let fin = $('#fin').val();
                    let estado_ptmp = $('#estado_ptmp').val();
                    let estado_gipa = $('#estado_gipa').val();
                    let estado_pmant = $('#estado_pmant').val();
                    let local = $('#local').val();
                    let razon_social = $('#razon_social').val();
                    let dia = $('#dia').val();
                    let numero_rfid = $('#numero_rfid').val();
                    let nombre_gafete = $('#nombre_gafete').val();
                    let tipo_gafete = $('#tipo_gafete').val();
                    let estado_cpago = $('#estado_cpago').val();
                    let hora = $('#hora').val();
                    let puerta = $('#puerta').val();


                    if (url == "") {
                        APAlerts.error('Debe elegir un reporte');
                        return;
                    }

                    if (config.fechas == true) {
                        if (inicio == "" || fin == "") {
                            APAlerts.error('Faltan fechas');
                            return;
                        }
                    }

                    if (config.use_usr_lcal_id == true) {
                        local = $this.usr_lcal_id;
                    }


                    let FData = {
                        'inicio': inicio,
                        'fin': fin,
                        'dia': dia,
                        'estado_ptmp': estado_ptmp,
                        'estado_pmant': estado_pmant,
                        'estado_gipa': estado_gipa,
                        'estado_cpago': estado_cpago,
                        'local': local,
                        'razon_social': razon_social,
                        'nombre_gafete': nombre_gafete,
                        'numero_rfid': numero_rfid,
                        'tipo_gafete': tipo_gafete,
                        'hora': hora,
                        'puerta': puerta,
                        'pdf': pdf ? 1 : 0
                    };

                    if (pdf == true) {
                        url = url + '?' + $.param(FData);
                        window.open(url, '_blank');
                        return;
                    }

                    $.ajax({
                        url: url,
                        method: 'GET',
                        data: FData,
                        beforeSend: function () {
                            $('.input-error').remove();
                        },

                        beforeSend: function () {
                            $('#report-container').html(ajaxLoader());
                        },
                        success: function (res) {

                            if (res.success === true) {
                                APAlerts.success(res.message);

                                $('#report-container').html(res.data.report);

                            } else {
                                $('#report-container').html("");
                                if (typeof res.message !== "undefined") {
                                    APAlerts.error(res.message);
                                    handleFormErrors($this.form, res.errors);
                                } else {
                                    APAlerts.error(res);
                                }

                            }
                        }
                    });


                },

                generarReporteExcel: function (excel) {
                    let $this = this;
                    let config = $this.getConfigByKey($('#reporte').val());

                    let url = config.url;
                    let inicio = $('#inicio').val();
                    let fin = $('#fin').val();
                    let estado_ptmp = $('#estado_ptmp').val();
                    let estado_gipa = $('#estado_gipa').val();
                    let estado_pmant = $('#estado_pmant').val();
                    let local = $('#local').val();
                    let razon_social = $('#razon_social').val();
                    let dia = $('#dia').val();
                    let numero_rfid = $('#numero_rfid').val();
                    let tipo_gafete = $('#tipo_gafete').val();
                    let estado_cpago = $('#estado_cpago').val();
                    let hora = $('#hora').val();


                    if (url == "") {
                        APAlerts.error('Debe elegir un reporte');
                        return;
                    }

                    if (config.fechas == true) {
                        if (inicio == "" || fin == "") {
                            APAlerts.error('Faltan fechas');
                            return;
                        }
                    }

                    if (config.use_usr_lcal_id == true) {
                        local = $this.usr_lcal_id;
                    }


                    let FData = {
                        'inicio': inicio,
                        'fin': fin,
                        'dia': dia,
                        'estado_ptmp': estado_ptmp,
                        'estado_pmant': estado_pmant,
                        'estado_gipa': estado_gipa,
                        'estado_cpago': estado_cpago,
                        'local': local,
                        'razon_social': razon_social,
                        'numero_rfid': numero_rfid,
                        'tipo_gafete': tipo_gafete,
                        'hora': hora,
                        'excel': excel ? 1 : 0
                    };

                    if (excel == true) {
                        url = url + '?' + $.param(FData);
                        window.open(url, '_blank');
                        return;
                    }

                    $.ajax({
                        url: url,
                        method: 'GET',
                        data: FData,
                        beforeSend: function () {
                            $('.input-error').remove();
                        },

                        beforeSend: function () {
                            $('#report-container').html(ajaxLoader());
                        },
                        success: function (res) {

                            if (res.success === true) {
                                APAlerts.success(res.message);

                                $('#report-container').html(res.data.report);

                            } else {
                                $('#report-container').html("");
                                if (typeof res.message !== "undefined") {
                                    APAlerts.error(res.message);
                                    handleFormErrors($this.form, res.errors);
                                } else {
                                    APAlerts.error(res);
                                }

                            }
                        }
                    });


                }

            };

            jReportes.init();

        });
    </script>
@endsection
