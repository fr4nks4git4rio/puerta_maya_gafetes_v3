{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
@if($comprobante->cpag_estado != 'PREVALIDADO')

    <p class="alert alert-info">
        El comprobante ya ha sido validado.
    </p>

@else
<div class="container">
    {!! Form::model($comprobante,['id' => 'form-validar-comprobante','url' =>"" , 'class' => 'form-horizontal']) !!}

        {!! Form::text('cpag_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
        {{-- {!! Form::text('sgft_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!} --}}


        <div class="row">
            <div class="col-md-6">

                <div class="form-group row">
                    {!! Form::label('cpag_fecha_pago', 'Fecha de pago', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_fecha_pago', null,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('cpag_folio_bancario', 'Folio', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_folio_bancario',  null ,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('cpag_forma_pago', 'Forma de Pago', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::select('cpag_forma_pago',$formas_pago, $comprobante->cpag_forma_pago ,["class"=>"form-control"]);!!}
                    </div>
                </div>

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('cpag_uso_cfdi', 'Uso del CFDI', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::select('cpag_uso_cfdi',$usos_cfdi, $comprobante->cpag_uso_cfdi ,["class"=>"form-control"]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="form-group row">
                    {!! Form::label('cpag_importe_pagado', 'Importe pagado', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_importe_pagado', number_format($comprobante->cpag_importe_pagado,2),["class"=>"form-control text-right", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    @if($comprobante->cpag_file != "")
                        <a href="{{asset('storage/comprobantes/'. $comprobante->cpag_file )}}" target="_blank"> Descargar comprobante original</a>
                    @else
                        <b class="text-danger">
                            No se cargo comprobante original
                        </b>
                    @endif
                </div>

            </div>


            <div class="col-md-6">


                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="btn btn-success btn-trans" id="btn-aceptar-comprobante"> <i class="fa fa-check"></i> Aceptar comprobante</div>
                        </div>

                    </div>

                    <p class="alert alert-info">
                        Aceptar el comprobante sumará el importe al saldo del local.
                    </p>

                    <hr>

                    <div class="form-group row">
                        {!! Form::label('sgft_comentario_admin', 'Comentario Rechazo', ['class' => 'col-sm-4 control-label']); !!}
                        <div class="col-sm-8">
                            {!! Form::text('sgft_comentario_admin',"",["class"=>"form-control", "readonly" => false]);!!}
                        </div>
                    </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="btn btn-danger btn-trans" id="btn-rechazar-comprobante"> <i class="fa fa-times"></i> Rechazar comprobante</div>
                    </div>

                </div>

                <p class="alert alert-info">
                    Rechazar el comprobante rechazará también las solicitudes de gafete asociadas, si es que aún no han sido validadas por Recepción.
                </p>






            </div>
        </div>



    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

       jModal = {
          modal:  $('#modal-form'),
          form:  '#form-validar-comprobante',
           cpag_id: {{$comprobante->cpag_id}},
           url_aceptar: '{{$url_validar}}',
           url_rechazar: '{{$url_rechazar}}',

          init: function(){
              let $this = this;

              // $('#modal-btn-ok',$this.modal).click(function(){
              //     $this.handleSubmit();
              // });

              $('#btn-aceptar-comprobante').off().on('click', function(){
                  $this.aceptarComprobante();
              });

              $('#btn-rechazar-comprobante').off().on('click', function(){

                  let comentario = $('#sgft_comentario_admin').val();

                  if(comentario == ""){
                      APAlerts.error("Es necesario un comentario para rechazar el comprobante.")
                  }else{

                      APAlerts.confirm({
                          message: '¿Esta seguro de rechazar el comprobante?',
                          confirmText: 'Si, rechazar',
                          callback: function(){
                              $this.rechazarComprobante();
                          }
                        });
                  }

              });


          },


          aceptarComprobante: function(){

            let $this = this;
            let url = $this.url_aceptar;

            // let form = $($this.form)[0];
            let params = {
                cpag_id: $this.cpag_id,
                cpag_forma_pago: $('#cpag_forma_pago').val(),
                // cpag_uso_cfdi: $('#cpag_uso_cfdi').val(),
                sgft_comentario_admin: ''
            };

            $.ajax({
                url: url,
                method: 'POST',
                data: params,
                // contentType: false,
                // cache: false,
                // processData:true,
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

          },

          rechazarComprobante: function(){

               let $this = this;
               let url = $this.url_rechazar;

               // let form = $($this.form)[0];
               let params = {
                   cpag_id: $this.cpag_id,
                   sgft_comentario_admin: $('#sgft_comentario_admin').val()
               };

               $.ajax({
                   url: url,
                   method: 'POST',
                   data: params,
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
@endif
