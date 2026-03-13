{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud,['id' => 'form-validar-comprobante','url' =>"" , 'class' => 'form-horizontal']) !!}

        {!! Form::text('sgft_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
        {{-- {!! Form::text('sgft_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!} --}}

        @if($solicitud->sgft_cpag_id == 0 || $solicitud->sgft_cpag_id == "")
            <p class="alert alert-info">
                <b>Esta solicitud no tiene asignado ningún comprobante.</b>
            </p>
        @else

        <div class="row">
            <div class="col-md-6">

{{--                <h4>Datos de la solicitud</h4>--}}

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('sgft_local', 'Local', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::text('sgft_local', $solicitud->Local->lcal_nombre_comercial,["class"=>"form-control", "readonly" => true]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('sgft_nombre', 'Nombre', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::text('sgft_nombre', null,["class"=>"form-control", "readonly" => true]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('sgft_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::text('sgft_cargo', null,["class"=>"form-control", "readonly" => true]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="foto-wrap row">--}}
{{--                    <div class="col-sm-12 text-center">--}}
{{--                    <div id="empl-loader" class="text-center mt-2 d-none"> <i class="fa fa-gear fa-spin"></i> Cargando...</div>--}}
{{--                    <img id="img-empleado" src="{{$solicitud->sgft_foto_web}}" width="200px"/>--}}
{{--                    </div>--}}
{{--                </div>--}}



{{--                <div class="form-group row">--}}
{{--                    --}}{{-- {!! Form::label('sgft_comentario_admin', 'Comentario de Rechazo' , ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                    {!! Form::textarea('sgft_comentario_admin', null,["class"=>"form-control", "size"=>"50x2"]);!!}--}}
{{--                    </div> --}}
{{--                    <p class="alert alert-info text-center">--}}
{{--                        Esta acción marcará que el gafete ya ha sido impreso corectamente.--}}
{{--                    </p>--}}
{{--                </div>--}}


                <div class="form-group row">
                    {!! Form::label('cpag_fecha_pago', 'Fecha de pago', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_fecha_pago', $solicitud->ComprobantePago->cpag_fecha_pago,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('cpag_folio_bancario', 'Folio', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_folio_bancario', $solicitud->ComprobantePago->cpag_folio_bancario,["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('cpag_aut_bancario', 'Referencia', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::text('cpag_aut_bancario', $solicitud->ComprobantePago->cpag_aut_bancario,["class"=>"form-control", "readonly" => true]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="form-group row">
                    {!! Form::label('cpag_importe_pagado', 'Importe pagado', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('cpag_importe_pagado', number_format($solicitud->ComprobantePago->cpag_importe_pagado,2),["class"=>"form-control text-right", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    @if($solicitud->ComprobantePago->cpag_file != "")
                        <a href="{{asset('storage/comprobantes/'. $solicitud->ComprobantePago->cpag_file )}}" target="_blank"> Descargar comprobante original</a>
                    @else
                        <b class="text-danger">
                            No se cargo comprobante original
                        </b>
                    @endif
                </div>



            </div>


            <div class="col-md-6">

                    @if($solicitud->ComprobantePago->cpag_estado != 'CAPTURADO')

                        <p class="alert alert-success">
                            <b>El comprobante de esta solicitud ya ha sido prevalidado.</b>
                        </p>
                    @else

{{--                        <div class="form-group row">--}}
{{--                            {!! Form::label('cpag_saldo', 'Saldo actual', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                            <div class="col-sm-8">--}}
{{--                                {!! Form::text('cpag_saldo',number_format($solicitud->VComprobantePago->cpag_saldo,2),["class"=>"form-control text-right", "readonly" => true]);!!}--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row">--}}
{{--                            {!! Form::label('costo_gafete', 'Costo gafete', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                            <div class="col-sm-8">--}}
{{--                                {!! Form::text('costo_gafete', number_format($costo_tarifa,2),["class"=>"form-control text-right", "readonly" => true]);!!}--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row">--}}
{{--                            {!! Form::label('saldo_final', 'Saldo final', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                            <div class="col-sm-8">--}}
{{--                                {!! Form::text('saldo_final', number_format($solicitud->VComprobantePago->cpag_saldo - $costo_tarifa,2),["class"=>"form-control text-right", "readonly" => true]);!!}--}}
{{--                            </div>--}}
{{--                        </div>--}}



{{--                        <hr>--}}



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

                    <p class="alert alert-warning">
                        Rechazar el comprobante rechazará también todas las solicitud de gafete asociadas.
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
           sgft_id: {{$solicitud->sgft_id}},
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

                  let comentario = $('#sgft_comentario_admin').val();

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
                sgft_id: $this.sgft_id,
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
                   sgft_id: $this.sgft_id,
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
