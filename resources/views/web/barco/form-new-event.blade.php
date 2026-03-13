<link href="{{asset('plugins/farbelous-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<link href="{{asset('plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
<div class="container">

    {!! Form::model(null,['id' => 'form-evento','url' =>$url , 'class' => 'form-horizontal']) !!}

        <div class="form-group row">
            {!! Form::label('fecha', 'Fecha', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
            {!! Form::text('fecha', $dateStr ,["class"=>"form-control", "placeholder" => "", 'readonly'=>true]);!!}
            </div>
        </div>

        <div class="form-group row">
            {!! Form::label('id_barco', 'Barco', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
                {!! Form::select('id_barco', $barcos ,  null , ["class"=>"form-control select2-control",
                                         "style" => "width: 100%" ])!!}
            </div>
        </div>


        <div class="form-group row">
            {!! Form::label('hora_llegada', 'Llegada', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
            {!! Form::text('hora_llegada', null,["class"=>"form-control time-picker", "placeholder" => ""]);!!}
            </div>
        </div>

        <div class="form-group row">
            {!! Form::label('hora_partida', 'Partida', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
            {!! Form::text('hora_partida', null,["class"=>"form-control time-picker", "placeholder" => ""]);!!}
            </div>
        </div>


    <div class="row">
        {!! Form::label('color', 'Color', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8 input-group" id="color-picker">
            {!! Form::text('color', '#000',["class"=>"form-control", "placeholder" => ""]);!!}
            <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('notificar', '&nbsp;', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            <div class="checkbox checkbox-custom">
                {{-- {{dd($selected)}} --}}
                {{Form::checkbox('notificar',1, null ,['id'=>'notificar'] )}}

                <label for="{{'notificar'}}">
                    Notificar por correo a todos los locatarios
                </label>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

      var jModal = {
          modal:  $('#modal-evento'),
          form:  '#form-evento',

          init: function(){
              let $this = this;

              $('#modal-btn-ok',$this.modal).click(function(){
                  $this.handleSubmit();
              });

              $('.select2-control').select2({
                  'allowClear': true,
                  placeholder: "Seleccione",
                  width: '100%'
              });

              $('#color-picker').colorpicker({
                  useAlpha: false,
                  fallbackColor: '#000',
                  popover: {
                      // title: 'Adjust the color',
                      placement: 'left'
                  }
              });

              $('#hora_llegada').timepicker({
                  minuteStep: 15,
                  defaultTime: '09:00',
                  explicitMode: true,
                  showMeridian: false
              });

              $('#hora_partida').timepicker({
                  minuteStep: 15,
                  defaultTime: '18:00',
                  explicitMode: true,
                  showMeridian: false
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
                          // dTables.oTable.draw();
                          //$('body').trigger('vivienda:added');
                          $('#modal-btn-close').click();
                          jCalendario.calendario.refetchEvents();



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

    loadScript('{{asset('plugins/farbelous-colorpicker/js/bootstrap-colorpicker.min.js')}}',function(){
        loadScript('{{asset('plugins/timepicker/bootstrap-timepicker.min.js')}}',function(){
            jModal.init();
        });
    });

    });

</script>
