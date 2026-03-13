<div class="container">

    {!! Form::model($gafete,['id' => 'form-gafete','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('gest_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}

        {!! Form::hidden('gest_anio', settings()->get('anio_impresion',2021) , ["class" => "form-control d-none", "placeholder"=>""]) !!}
        {!! Form::hidden('gest_lcal_id', $local->lcal_id , ["class" => "form-control d-none", "placeholder"=>""]) !!}

        <div class="row">
            <div class="col-md-8">


                <div class="form-group row">
                    {!! Form::label('gest_local', 'Local', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('gest_local', $local->lcal_nombre_comercial , ["class"=>"form-control select2-control", 'disabled' => true ])!!}
                    </div>
                </div>

        {{--        <div class="form-group row">--}}
        {{--            {!! Form::label('gest_numero', 'Número', ['class' => 'col-sm-4 control-label']); !!}--}}
        {{--            <div class="col-sm-8">--}}
        {{--            {!! Form::text('gest_numero', null,["class"=>"form-control", "placeholder" => ""]);!!}--}}
        {{--            </div>--}}
        {{--        </div>--}}

                <div class="form-group row">
                    {!! Form::label('gest_tipo', 'Clase', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-5">
                    {!! Form::select('gest_tipo',['MOTO'=>'MOTO','AUTO'=>'AUTO'], null,["class"=>"form-control", "placeholder" => "Seleccione"]);!!}
                    </div>

                </div>

                <div class="form-group row">
                    {!! Form::label('gest_tipo_solicitud', 'Tipo' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-4">
                        {!! Form::select('gest_tipo_solicitud', ['PRIMERA VEZ' => 'PRIMERA VEZ', 'REPOSICIÓN' => 'REPOSICIÓN'] ,null ,["class"=>"form-control", "placeholder" => "Seleccione..."]);!!}
                    </div>
                    <div class="col-sm-4 text-right">
                        <b class="text-info" id="ph-tarifa"></b>
                    </div>
                </div>

                <div class="controles-reposicion d-none">
                    <div class="form-group row">
                        {!! Form::label('gest_gafete_reposicion', 'Gefete a reponer' , ['class' => 'col-sm-4 control-label']); !!}
                        <div class="col-sm-8">
                            {!! Form::select('gest_gafete_reposicion', $gafetes_activos ,null ,["class"=>"form-control select2-control", "placeholder" => "Seleccione..."]);!!}
                        </div>
                    </div>
                </div>



                <div class="form-group row control-gratuito">
                    {!! Form::label('gest_gratuito', '&nbsp;' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-4">
                        <div class="checkbox checkbox-custom">
                            {{-- {{dd($selected)}} --}}
                            {{Form::checkbox('gest_gratuito',null, null ,['id'=>'gest_gratuito'] )}}

                            <label for="{{'gest_gratuito'}}">
                                Gafete gratuito
                            </label>
                        </div>

                    </div>
                    <div class="col-sm-4 text-right">
                        @if($gafetesGratisAuto >  0 || $gafetesGratisMoto >  0)
                            <b class="text-info"> Disponibles: <br></b> {{ $gafetesGratisAuto }}  Auto, {{ $gafetesGratisMoto }}   Moto  <br>
                        @else
                            <b class="text-danger"> No disponibles</b>
                        @endif
                    </div>
                </div>

                <span class="controles-comprobante {{ ($gafete->gest_gratuito ?? false == 1 )? "d-none" : "" }}">
                    <div class="row">
                        {!! Form::label('gest_cpag_id', 'Comprobante' , ['class' => 'col-sm-4 control-label']); !!}
                        <div class="col-sm-8">
                            {!! Form::select('gest_cpag_id', $comprobantes ,null ,["class"=>"form-control select2-control", "placeholder" => "Seleccione..."]);!!}
                        </div>

                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">&nbsp;</div>
                        <div class="col-sm-8">

                            <span class="btn btn-info" id="btn-test">Capturar Comprobante</span>
                            <i class="zmdi zmdi-help text-info" data-toggle="tooltip" title="Si no existe saldo vigente suficiente, debe capturar un nuevo comprobante de pago.">&nbsp;</i>
                        </div>
                    </div>
                    <br>

                </span>


                <div class="form-group row">
                    {!! Form::label('gest_comentario', 'Comentario', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('gest_comentario', null,["class"=>"form-control", "placeholder" => ""]);!!}
                    </div>
                </div>

            </div>

            <div class="col-md-4">

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-condensed">
                            <tr>
                                <td><b>Saldo</b></td>
                                <td class="text-right">$ {{number_format($saldos['saldo_vigente'],2)}}</td>
                            </tr>
                            {{--<tr>--}}
                                {{--<td><b>Saldo Virtual</b></td>--}}
                                {{--<td class="text-right">$ {{number_format($saldos['saldo_virtual'],2)}}</td>--}}
                            {{--</tr>--}}
                        </table>
                    </div>
                </div>


            </div>

        </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

      var jModal = {
          modal:  $('#modal-form'),
          form:  '#form-gafete',
          local_id: {{auth()->getUser()->Local->lcal_id}},
          est_auto_pvez: '{{ "$ ".number_format( settings()->get('gft_est_auto_pvez', '0' ),2) . " MXN" }}',
          est_auto_reposicion: '{{ "$ ".number_format( settings()->get('gft_est_auto_reposicion', '0' ),2) . " MXN" }}',
          est_moto_pvez: '{{ "$ ".number_format( settings()->get('gft_est_moto_pvez', '0' ),2) . " MXN" }}',
          est_moto_reposicion: '{{ "$ ".number_format( settings()->get('gft_est_moto_reposicion', '0' ),2) . " MXN" }}',
          url_new_comprobante: '{{ url('comprobante-pago/form') }}',

          init: function(){
              let $this = this;

              $('#modal-btn-ok',$this.modal).click(function(){
                  $this.handleSubmit();
              });

              $('[data-toggle="tooltip"]').tooltip();

              $('#gest_gafete_reposicion').select2();

              $this.handleSelectComprobantes();

              $('#btn-test').on('click',function(){
                  $this.openComprobanteForm();
              });

              $('#gest_tipo_solicitud').on('change',function(){
                  let tipo = $(this).val();

                  let t = $('#gest_tipo').val();
                  if(t){
                      if(t == 'AUTO'){
                          if(tipo == 'REPOSICIÓN'){
                              $('#ph-tarifa').html( $this.est_auto_reposicion);
                              $('.control-gratuito').addClass('d-none');
                              $('#sgft_gratuito').prop('checked',false).trigger('change');
                              $('.controles-reposicion').removeClass('d-none')
                          }
                          if(tipo == 'PRIMERA VEZ'){
                              $('#ph-tarifa').html($this.est_auto_pvez);
                              $('.control-gratuito').removeClass('d-none');
                              $('.controles-reposicion').addClass('d-none')
                          }
                      }else if(t == 'MOTO'){
                          if(tipo == 'REPOSICIÓN'){
                              $('#ph-tarifa').html( $this.est_moto_reposicion);
                              $('.control-gratuito').addClass('d-none');
                              $('#sgft_gratuito').prop('checked',false).trigger('change');
                              $('.controles-reposicion').removeClass('d-none')
                          }
                          if(tipo == 'PRIMERA VEZ'){
                              $('#ph-tarifa').html($this.est_moto_pvez);
                              $('.control-gratuito').removeClass('d-none');
                              $('.controles-reposicion').addClass('d-none')
                          }
                      }
                  }
                  if(tipo == ''){
                      $('#ph-tarifa').html('');
                  }

              });

              $('#gest_tipo').on('change',function(){
                  let t = $(this).val();

                  let tipo = $('#gest_tipo_solicitud').val();
                  if(t){
                      if(t == 'AUTO'){
                          if(tipo == 'REPOSICIÓN'){
                              $('#ph-tarifa').html( $this.est_auto_reposicion);
                              $('.control-gratuito').addClass('d-none');
                              $('#sgft_gratuito').prop('checked',false).trigger('change');
                              $('.controles-reposicion').removeClass('d-none')
                          }
                          if(tipo == 'PRIMERA VEZ'){
                              $('#ph-tarifa').html($this.est_auto_pvez);
                              $('.control-gratuito').removeClass('d-none');
                              $('.controles-reposicion').addClass('d-none')
                          }
                      }else if(t == 'MOTO'){
                          if(tipo == 'REPOSICIÓN'){
                              $('#ph-tarifa').html( $this.est_moto_reposicion);
                              $('.control-gratuito').addClass('d-none');
                              $('#sgft_gratuito').prop('checked',false).trigger('change');
                              $('.controles-reposicion').removeClass('d-none')
                          }
                          if(tipo == 'PRIMERA VEZ'){
                              $('#ph-tarifa').html($this.est_moto_pvez);
                              $('.control-gratuito').removeClass('d-none');
                              $('.controles-reposicion').addClass('d-none')
                          }
                      }
                  }
                  if(t == ''){
                      $('#ph-tarifa').html('');
                  }

              });

              $('#gest_gratuito').change(function(){
                  if($(this).prop('checked') == true){
                      $('.controles-comprobante').addClass('d-none');
                  }else{
                      $('.controles-comprobante').removeClass('d-none');
                  }

              });

              $( "body" ).off("comprobante:added").on( "comprobante:added", function( event ) {
                  $this.handleSelectComprobantes();
              });


          },

          handleSubmit: function(){

              let $this = this;
              let url = $($this.form).attr('action');
              let data = $($this.form).serialize();

              console.log(data);

              $.ajax({
                  url: url,
                  method: 'POST',
                  data: data,
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

          },

          handleSelectComprobantes: function(){
              let $this = this;

              //Pedimos el array completo de los comprobantes para inicializar el select2
              $.ajax({
                  url: "{!! url('comprobante-pago/get-select-options-gafete') !!}/" + $this.local_id,
                  type:'post',
                  data: {
                      _token: "{!! csrf_token() !!}",
                  },
                  dataType: 'json',
                  success: function(res){
                      // res.unshift({id:"",text:""});
                      console.log(res);
                      $("#gest_cpag_id").select2({
                          data:res,
                          width:'100%',
                          allowClear:true,
                          templateResult: function (d) { return $(d.result_template); },
                          // templateSelection: function (d) { return $(d.text); },
                          placeholder: 'Seleccione...' });

                      $("#gest_cpag_id").val("").trigger('select2:change');

                  }

              });

          },

          openComprobanteForm: function(){

              let $this = this;

              APModal.open({
                  dom: 'modal-comprobante',
                  title: 'Capturar comprobante de pago',
                  url: $this.url_new_comprobante,
                  size: 'modal-md'
              });


          }
      };

      jModal.init();
    });

</script>
