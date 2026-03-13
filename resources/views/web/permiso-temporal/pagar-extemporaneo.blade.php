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

{{--                <div class="form-group row">--}}
{{--                    {!! Form::label('ptmp_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                    <div class="col-sm-8">--}}
{{--                        {!! Form::text('ptmp_cargo', $permiso->Cargo->crgo_descripcion,["class"=>"form-control", "readonly" => true]);!!}--}}
{{--                    </div>--}}
{{--                </div>--}}
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
                    {!! Form::label('ptmp_gafete', 'Gafete', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ptmp_gafete',$permiso->GafetePreimpreso->gfpi_numero ?? 'SIN GAFETE ASIGNADO',["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('saldo_actual', 'Saldo Vigente', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('saldo_actual',number_format($saldos['saldo_vigente'],2),["class"=>"form-control", "readonly" => true]);!!}
                    </div>
                </div>


{{--                <span class="controles-comprobante">--}}
{{--                    <div class="row">--}}
{{--                        {!! Form::label('ptmp_cpag_id', 'Comprobante' , ['class' => 'col-sm-4 control-label']); !!}--}}
{{--                        <div class="col-sm-8">--}}
{{--                            {!! Form::select('ptmp_cpag_id', $comprobantes ,null ,["class"=>"form-control select2-control", "placeholder" => "Seleccione..."]);!!}--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                    <br>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-sm-4">&nbsp;</div>--}}
{{--                        <div class="col-sm-8">--}}

{{--                            <span class="btn btn-info" id="btn-new-comprobante">Capturar Comprobante</span>--}}
{{--                            <i class="zmdi zmdi-help text-info" data-toggle="tooltip" title="Seleccione un comprobante de pago previamente capturado o bien capture uno nuevo si no existe alguno.">&nbsp;</i>--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <br>--}}
{{--                </span>--}}



                {{-- <div class="form-group row">
                    {!! Form::label('ptmp_comentario_admin', 'Comentario de Rechazo' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::textarea('ptmp_comentario_admin', null,["class"=>"form-control", "size"=>"50x2"]);!!}
                    </div>
                </div> --}}
                <p class="alert alert-info text-justify"> Esta acción deducirá ${{settings()->get('gft_tarifa_2')}} al saldo del local, pues es la tarifa de una reposición de gafete.
                    <br>Se concluirá el ciclo del permiso y el gafete no podrá ser asignado a otro permiso.
                </p>

            </div>

        </div>



    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

       jModal = {
          modal:  $('#modal-form'),
          local_id: {{ $permiso->Local->lcal_id }},
          form:  '#form-permiso',
          url_new_comprobante: '{{ url('comprobante-pago/form') }}',

          init: function(){
              let $this = this;


              $('#modal-btn-ok',$this.modal).click(function(){
                  $this.handleSubmit();
              });

              $('[data-toggle="tooltip"]').tooltip();

              // $('#btn-new-comprobante').on('click',function(){
              //     $this.openComprobanteForm();
              // });

              // $this.handleSelectComprobantes();

              // $( "body" ).off("comprobante:added").on( "comprobante:added", function( event ) {
              //     $this.handleSelectComprobantes();
              // });

          },

           {{--handleSelectComprobantes: function(){--}}
           {{--    let $this = this;--}}

           {{--    //Pedimos el array completo de los comprobantes para inicializar el select2--}}
           {{--    $.ajax({--}}
           {{--        url: "{!! url('comprobante-pago/get-select-options-gafete') !!}/" + $this.local_id,--}}
           {{--        type:'post',--}}
           {{--        dataType: 'json',--}}
           {{--        success: function(res){--}}
           {{--            // res.unshift({id:"",text:""});--}}
           {{--            console.log(res);--}}
           {{--            $("#ptmp_cpag_id").select2({--}}
           {{--                data:res,--}}
           {{--                width:'100%',--}}
           {{--                allowClear:true,--}}
           {{--                templateResult: function (d) { return $(d.result_template); },--}}
           {{--                // templateSelection: function (d) { return $(d.text); },--}}
           {{--                placeholder: 'Seleccione...' });--}}

           {{--            $("#ptmp_cpag_id").val("").trigger('select2:change');--}}

           {{--        }--}}

           {{--    });--}}

           {{--},--}}

           {{--openComprobanteForm: function(){--}}

           {{--    let $this = this;--}}

           {{--    // if($ ('#sgft_tipo').val() == "" )--}}
           {{--    // {--}}
           {{--    //   APAlerts.warning('ELIJA UN TIPO DE SOLICITUD PRIMERO');--}}
           {{--    //   return;--}}
           {{--    // }--}}

           {{--    APModal.open({--}}
           {{--        dom: 'modal-comprobante',--}}
           {{--        title: 'Capturar comprobante de pago',--}}
           {{--        url: $this.url_new_comprobante,--}}
           {{--        size: 'modal-md'--}}
           {{--    });--}}


           {{--},--}}

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
