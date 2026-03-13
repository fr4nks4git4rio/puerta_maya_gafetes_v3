{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($permiso,['id' => 'form-permiso','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('pmtt_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}

        <div class="row">
            <div class="col-md-6">

                <div class="form-group row">
                    {!! Form::label('local', 'Local' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-2">
                    {!! Form::text('local_numero', auth()->user()->Local->lcal_id ,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                    <div class="col-sm-5">
                    {!! Form::text('local', auth()->user()->Local->lcal_nombre_comercial ,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                 <div class="form-group row">
                    {!! Form::label('pmtt_fecha', 'Fecha Solcitud' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_fecha', date('Y-m-d') ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>


                <div class="form-group row">
                    {!! Form::label('pmtt_solicitante', 'Nombre Solicitante', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('pmtt_solicitante', null,["class"=>"form-control",'readonly'=>true]);!!}
                    </div>
                </div>



                <div class="form-group row">
                    {!! Form::label('pmtt_empresa', 'Empresa' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_empresa', null,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('pmtt_representante', 'Representante' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_representante', null,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('pmtt_listado_trabajadores', 'Listado de trabajadores' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('pmtt_listado_trabajadores', null,["class"=>"form-control", "size"=>"50x2",'readonly'=>true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('pmtt_trabajo', 'Trabajo a realizar', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('pmtt_trabajo', null,["class"=>"form-control","size"=>"50x2",'readonly'=>true]);!!}
                    </div>
                </div>


            </div>
            <div class="col-md-6">

                <div class="form-group row">
                    {!! Form::label('pmtt_vigencia_inicial', 'Fecha Inicio' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_vigencia_inicial', Carbon\Carbon::now()->addDays(1)->format('Y-m-d') ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('pmtt_dias', 'Dias Solicitados' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_dias', 15 ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>



                <div class="form-group row">
                    {!! Form::label('pmtt_observaciones', 'Observaciones' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::textarea('pmtt_observaciones', null,["class"=>"form-control", "size"=>"50x2",'readonly'=>true]);!!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('pmtt_estado', 'Estado' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('pmtt_estado', null ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('pmtt_comentario_admon', 'Comentario de validación', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('pmtt_comentario_admon', null,["class"=>"form-control","size"=>"50x2"]);!!}
                    </div>

                </div>

                <div class="row">
                    <p class="alert alert-danger">Esta acción marcará el permiso como <b>RECHAZADO</b></p>
                </div>


{{--                <p class="alert alert-info">Varifcacion de permiso</p>--}}

            </div>
        </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

        jModal = {
            modal:  $('#modal-form'),
            form:  '#form-permiso',

            init: function(){
                let $this = this;

                $('#modal-btn-ok',$this.modal).click(function(){
                    $this.handleSubmit();
                });

            },


            handleSubmit: function(){

                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    // data: new FormData(form),
                    data: $($this.form).serialize(),
                    // contentType: false,
                    // cache: false,
                    // processData:false,
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            $('#modal-btn-close').click();

                        }else{

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

        jModal.init();

    });

</script>
