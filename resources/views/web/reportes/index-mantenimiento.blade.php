@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-chart">&nbsp;</span> Reportes
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card card-color">

            <div class="card-body">

                <label style="cursor:pointer" id="filter-toggle" > <i class="fa fa-check"></i> Selección de reporte</label>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::select('reporte', $reportes ,  head(array_keys($reportes)) , ["class"=>"form-control select2-control", "placeholder" => "Seleccione una opción", 'id' => 'reporte', "style" => "width: 100%" ])!!}
                        </div>
                    </div>
                </div>

                <hr>

                <label style="cursor:pointer" id="filter-toggle" > <i class="fa fa-filter"></i> Filtros del reporte</label>

                <div class="row">
                    <div class="col-sm-1">
                        <label for="inicio">Inicio</label>
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('inicio', null , ["class"=>"form-control",'id'=>'inicio'])!!}
                    </div>

                    <div class="col-sm-1">
                        <label for="fin">Fin</label>
                    </div>
                    <div class="col-sm-3">
                        {!! Form::text('fin', null , ["class"=>"form-control",'id'=>'fin'])!!}
                    </div>

                    <div class="col-sm-3">
                        <label for="inicio">&nbsp;</label>
                        <span class="btn btn-primary" id="btn-generar"> <i class="fa fa-bolt"></i> Generar</span>
                        <span class="btn btn-primary" id="btn-generar-pdf"> <i class="fa fa-file-pdf-o"></i> PDF</span>
                    </div>


                </div>


            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12" >

        <div class="card card-color">

            <div class="card-body" id="report-container">


                <p class="alert alert-info">Genere un reporte</p>

            </div>
        </div>



    </div>
</div>



<script type="text/javascript">
    $(document).ready(function(){

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        jReportes = {


            init: function(){
                let $this = this;


                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $('#inicio').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language:'{{App::getLocale()}}',
                    orientation: 'bottom'
                });

                $('#fin').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language:'{{App::getLocale()}}',
                    orientation: 'bottom'
                });

                $('#btn-generar').on('click',function(){
                    $this.generarReporte(false);
                });

                $('#btn-generar-pdf').on('click',function(){
                    $this.generarReporte(true);
                });

            },

            generarReporte: function(pdf){
                let $this = this;

                let url = $('#reporte').val();
                let inicio = $('#inicio').val();
                let fin    = $('#fin').val();

                if(url == ""){
                    APAlerts.error('Debe elegir un reporte');
                    return;
                }

                if(inicio == "" || fin == ""){
                    APAlerts.error('Faltan fechas');
                    return;
                }



                let FData = {
                    'inicio' : inicio,
                    'fin' : fin,
                    'pdf': pdf ? 1: 0
                };

                if(pdf == true){
                    url = url + '?' + $.param( FData );
                    window.open(url,'_blank');
                    return;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: FData,
                    beforeSend:function(){
                        $('.input-error').remove();
                    },

                    beforeSend: function(){
                        $('#report-container').html(ajaxLoader());
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);

                            $('#report-container').html(res.data.report);

                        }else{
                            $('#report-container').html("");
                            if(typeof res.message !== "undefined"){
                                APAlerts.error(res.message);
                                handleFormErrors($this.form,res.errors);
                            }else{
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
