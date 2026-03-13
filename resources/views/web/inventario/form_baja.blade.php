{{--<link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/>--}}

@if($compra == null)

    <p class="alert alert-danger">
        Para capturar baja de inventario, primero capture una compra.
    </p>

    <script type="text/javascript">

        $(document).ready(function(){

            $('#modal-btn-ok').addClass('d-none')

        });

    </script>

@else

@php
    $merma = $compra->icmp_cantidad - $currentUsedCards['acceso'] - $currentUsedCards['estacionamiento'];
@endphp
<div class="container">

    {!! Form::open(['id' => 'form-inventario','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::hidden('ibaj_icmp_id',$compra->icmp_id, ["class" => "form-control", "placeholder"=>""]) !!}

        <div class="row">
            <div class="col-sm-12">

                <div class="form-group row">
                    {!! Form::label('ultima_compra', 'Última compra', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ultima_compra', $compra->icmp_cantidad,["class"=>"form-control", "readonly"=>true, "placeholder" => ""]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('ibaj_acceso', 'Accesos Impresos', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ibaj_acceso', $currentUsedCards['acceso'],["class"=>"form-control", "placeholder" => ""]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('ibaj_estacionamiento', 'Estacionamiento Impresos', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ibaj_estacionamiento', $currentUsedCards['estacionamiento'],["class"=>"form-control", "placeholder" => ""]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('ibaj_merma', 'Merma', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('ibaj_merma',($merma > 0)? $merma : 0,["class"=>"form-control", "placeholder" => ""]);!!}
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
            form:  '#form-inventario',

          init: function(){
              let $this = this;

              $('#modal-btn-ok',$this.modal).click(function(){
                  $this.handleSubmit();
              });

            // setTimeout(() => {
            // $('.select2-control').select2({
            //     'allowClear': true,
            //     placeholder: "Seleccione",
            //     width: '100%'
            // });
            // }, 1000);



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
                           jInventario.getStockData();

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
