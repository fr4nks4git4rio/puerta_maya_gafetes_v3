{{--<link href="{{asset('plugins/farbelous-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">--}}
<div class="container">

    {!! Form::model($cargo,['id' => 'form-cargo','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('crgo_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


        <div class="form-group row">
            {!! Form::label('crgo_descripcion', 'Nombre Cargo', ['class' => 'col-sm-2 control-label']); !!}
            <div class="col-sm-10">
            {!! Form::text('crgo_descripcion', null,["class"=>"form-control", "placeholder" => ""]);!!}
            </div>
        </div>

{{--        <div class="row">--}}
{{--            {!! Form::label('crgo_color', 'Color', ['class' => 'col-sm-2 control-label']); !!}--}}
{{--                <div class="col-sm-10 input-group" id="color-picker">--}}
{{--                    {!! Form::text('crgo_color', null,["class"=>"form-control", "placeholder" => ""]);!!}--}}
{{--                    <span class="input-group-append">--}}
{{--                        <span class="input-group-text colorpicker-input-addon"><i></i></span>--}}
{{--                    </span>--}}
{{--                </div>--}}
{{--        </div>--}}

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

      var jModal = {
          modal:  $('#modal-form'),
          form:  '#form-cargo',

          init: function(){
              let $this = this;

              $('#modal-btn-ok',$this.modal).click(function(){
                  $this.handleSubmit();
              });

              // $('#color-picker').colorpicker({
              //     useAlpha: false,
              //     fallbackColor: '#fff',
              //      popover: {
              //           // title: 'Adjust the color',
              //           placement: 'left'
              //       }
              // });

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




      {{--loadScript('{{asset('plugins/farbelous-colorpicker/js/bootstrap-colorpicker.min.js')}}',function(){--}}
        jModal.init();
      // });

    });

</script>
