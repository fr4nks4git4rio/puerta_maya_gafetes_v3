{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($gafete,['id' => 'form-validar-comprobante','url' =>"" , 'class' => 'form-horizontal']) !!}

        {!! Form::text('gest_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}

        @if($gafete->gest_cpag_id == 0 || $gafete->gest_cpag_id == "")
            <p class="alert alert-info">
                <b>Esta solicitud no tiene asignada ningún comprobante.</b>
            </p>
        @else

        <div class="row">
            <div class="col-md-6">



                <div class="form-group row">
                    {!! Form::label('cpag_fecha_pago', 'Fecha de pago', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_fecha_pago', $gafete->ComprobantePago->cpag_fecha_pago,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('cpag_folio_bancario', 'Folio', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_folio_bancario', $gafete->ComprobantePago->cpag_folio_bancario,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>


                <div class="form-group row">
                    {!! Form::label('cpag_importe_pagado', 'Importe pagado', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_importe_pagado', number_format($gafete->ComprobantePago->cpag_importe_pagado,2),["class"=>"form-control text-right", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    @if($gafete->ComprobantePago->cpag_file != "")
                        <a href="{{asset('storage/comprobantes/'. $gafete->ComprobantePago->cpag_file )}}" target="_blank"> Descargar comprobante original</a>
                    @else
                        <b class="text-danger">
                            No se cargo comprobante original
                        </b>
                    @endif
                </div>



            </div>


            <div class="col-md-6">

                    @if($gafete->ComprobantePago->cpag_estado != 'CAPTURADO')

                        <p class="alert alert-success">
                            <b>El comprobante de esta solicitud ya ha sido prevalidado.</b>
                        </p>
                    @else



                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="btn btn-success btn-trans" id="btn-aceptar-comprobante"> <i class="fa fa-check"></i> Aceptar comprobante</div>
                            </div>

                        </div>

                        <p class="alert alert-info">
                            Aceptar el comprobante permite a contabilidad validarlo y que el importe se sume al saldo del local.
                        </p>

                        <hr>

                        <div class="form-group row">
                            {!! Form::label('gest_comentario_admin', 'Comentario Rechazo', ['class' => 'col-sm-4 control-label']); !!}
                            <div class="col-sm-8">
                                {!! Form::text('gest_comentario_admin',"",["class"=>"form-control", "readonly" => false]);!!}
                            </div>
                        </div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="btn btn-danger btn-trans" id="btn-rechazar-comprobante"> <i class="fa fa-times"></i> Rechazar comprobante</div>
                        </div>

                    </div>

                    <p class="alert alert-warning">
                        Rechazar el comprobante rechazará también la solicitud de gafete.
                    </p>






                    @endif





            </div>
        </div>

        @endif



    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

       jModal = {
          modal:  $('#modal-form'),
          form:  '#form-validar-comprobante',
           gest_id: {{$gafete->gest_id}},
           url_aceptar: '{{$url_aceptar}}',
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

                  let comentario = $('#gest_comentario_admin').val();

                  if(comentario == ""){
                      APAlerts.error("Es necesario un comentario para rechazar el comprobante.")
                  }else{

                      APAlerts.confirm({
                          message: '¿Esta seguro de rechazar la solicitud?',
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
                gest_id: $this.gest_id,
                gest_comentario_admin: ''
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
                   gest_id: $this.gest_id,
                   gest_comentario_admin: $('#gest_comentario_admin').val()
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
