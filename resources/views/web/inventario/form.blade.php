<link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/>
<div class="container">

    {!! Form::model($record,['id' => 'form-inventario','url' =>$url , 'class' => 'form-horizontal']) !!}

        {!! Form::text('icmp_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
        <div class="row">
            <div class="col-sm-12">

                <div class="form-group row">
                    {!! Form::label('icmp_fecha', 'Fecha', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('icmp_fecha', date('Y-m-d') , ["class"=>"form-control"])!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('icmp_cantidad', 'Cantidad', ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                        {!! Form::text('icmp_cantidad', null,["class"=>"form-control", "placeholder" => ""]);!!}
                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('inventory_low_limit', 'Advertir al llegar a ' , ['class' => 'col-sm-4 control-label']); !!}
                    <div class="col-sm-8">
                    {!! Form::text('inventory_low_limit', settings()->get('inventory_low_limit',50) ,["class"=>"form-control", "placeholder" => ""]);!!}
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

              $('#invt_fecha').datepicker({
                  autoclose: true,
                  format: 'yyyy-mm-dd',
                  language:'{{App::getLocale()}}',
                  orientation: 'bottom'
              });

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
