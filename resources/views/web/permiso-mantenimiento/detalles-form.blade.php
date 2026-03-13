{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($permiso,['id' => 'form-permiso','url' =>'#' , 'class' => 'form-horizontal']) !!}

        {!! Form::text('pmtt_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
{{--        {!! Form::text('pmtt_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!}--}}


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
                    {!! Form::text('pmtt_fecha', null,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
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


            </div>
            <div class="col-md-6">




                <div class="form-group row">
                    {!! Form::label('pmtt_vigencia_inicial', 'Fecha Inicio' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_vigencia_inicial', null ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('pmtt_dias', 'Dias Solicitados' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('pmtt_dias', null ,["class"=>"form-control", "placeholder" => "",'readonly'=>true]);!!}
                    </div>
                </div>

                 <div class="form-group row">
                    {!! Form::label('pmtt_trabajo', 'Trabajo a realizar', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('pmtt_trabajo', null,["class"=>"form-control","size"=>"50x2",'readonly'=>true]);!!}
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


                @if($permiso->pmtt_estado == 'RECHAZADO')

                    <div class="form-group row">
                        {!! Form::label('pmtt_comentario_admon', 'Motivo de rechazo' , ['class' => 'col-sm-4 control-label text-danger']); !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('pmtt_comentario_admon', null,["class"=>"form-control", "size"=>"2x2",'readonly'=>true]);!!}
                        </div>
                    </div>

                @endif



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

              // $this.modal.btnOk.addClass('d-none');

          }

      };

       jModal.init();

    });

</script>
