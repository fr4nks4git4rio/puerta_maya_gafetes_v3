{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($permiso,['id' => 'form-permiso','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('ptmp_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
        {{-- {!! Form::text('sgft_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!} --}}


        <div class="row">
            <div class="col-md-12">

                <div class="form-group row">
                    {!! Form::label('ptmp_local', 'Local', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_local', $permiso->Local->lcal_nombre_comercial,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('ptmp_nombre', 'Nombre', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_nombre', null,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('ptmp_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_cargo', $permiso->Cargo->crgo_descripcion,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('ptmp_fecha', 'Fecha', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_fecha', null,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('ptmp_periodo', 'Periodo', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_cargo', 'DEL '. $permiso->ptmp_vigencia_inicial . ' AL ' . $permiso->ptmp_vigencia_final,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>


                <div class="form-group row">
                    {!! Form::label('ptmp_comentario_admin', 'Comentario de Rechazo' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::textarea('ptmp_comentario_admin', null,["class"=>"form-control", "size"=>"50x2"]);!!}
                    </div>
                </div>

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

            let form = $($this.form)[0];

            $.ajax({
                url: url,
                method: 'POST',
                data: new FormData(form),
                contentType: false,
                cache: false,
                processData:false,
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
