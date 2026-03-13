{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud,['id' => 'form-solicitud','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('sgft_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
        {{-- {!! Form::text('sgft_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!} --}}


        <div class="row">
            <div class="col-md-8">

                <div class="form-group row">
                    {!! Form::label('sgft_local', 'Local', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('sgft_local', $solicitud->Local->lcal_nombre_comercial,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('sgft_nombre', 'Nombre', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('sgft_nombre', null,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('sgft_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('sgft_cargo', null,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('saldo_vigente', 'Saldo vigente', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('saldo_vigente', number_format($saldos['saldo_vigente'],2),["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('costo_gafete', 'Costo gafete', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('costo_gafete', number_format($solicitud->sgft_costo,2),["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="row">
                    <p class="alert alert-info">
                        Esta operación descontará el costo del gafete del saldo vigente del local, y permitirá su impresión.
                    </p>
                </div>




            </div>
            <div class="col-md-4">

                <div class="foto-wrap">
                    <div id="empl-loader" class="text-center mt-2 d-none"> <i class="fa fa-gear fa-spin"></i> Cargando...</div>
                    <img id="img-empleado" src="{{$solicitud->sgft_foto_web}}" width="200px"/>
                </div>





            </div>
        </div>



    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

       jModal = {
          modal:  $('#modal-form'),
          form:  '#form-solicitud',

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
