<div class="container">

    {!! Form::model($gafete,['id' => 'form-gafete','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('gfpi_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


        <div class="form-group row">
            {!! Form::label('gfpi_numero', 'Número', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
            {!! Form::text('gfpi_numero', null,["class"=>"form-control", "placeholder" => ""]);!!}
            </div>
        </div>

        <div class="form-group row">
{{--            {!! Form::label('gfpi_tipo', 'Tipo', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--            <div class="col-sm-5">--}}
{{--            {!! Form::select('gfpi_tipo',['PERMISO'=>'PERMISO','ESTACIONAMIENTO'=>'ESTACIONAMIENTO'], null,["class"=>"form-control", "placeholder" => "Seleccione"]);!!}--}}
{{--            </div>--}}
            {{ Form::hidden('gfpi_tipo','PERMISO') }}
        </div>

        <div class="form-group row">
            {!! Form::label('gfpi_comentario', 'Comentario', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
                {!! Form::text('gfpi_comentario', null,["class"=>"form-control", "placeholder" => ""]);!!}
            </div>
        </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

      var jModal = {
          modal:  $('#modal-form'),
          form:  '#form-gafete',

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
                  data: $($this.form).serialize(),
                  beforeSend:function(){
                      $('.input-error').remove();
                  },
                  success: function (res) {

                      if(res.success === true) {
                          APAlerts.success(res.message);
                          dTables.oTable.draw();
                          //$('body').trigger('vivienda:added');
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
