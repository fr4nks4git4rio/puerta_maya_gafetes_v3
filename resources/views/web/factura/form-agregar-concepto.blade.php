{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model(null,['id' => 'form-concepto-factura','url' => $url]) !!}

    <div class="row">

        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('select_concepto', 'Concepto' , ['class' => 'control-label']); !!}
                {!! Form::select('select_concepto', $conceptos, head($conceptos) ,["class"=>"form-control"]);!!}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('fcdt_cpag_id', 'Comprobante de pago' , ['class' => 'control-label']); !!}
                {!! Form::select('fcdt_cpag_id', $comprobantes, null ,["class"=>"form-control select2-control", "placeholder" => "Seleccione..."]);!!}
            </div>
        </div>

    </div>

    <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('fcdt_cantidad', 'Cantidad' , ['class' => 'control-label']); !!}
                    {!! Form::text('fcdt_cantidad', 1 ,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="col-sm-8">
                <div class="form-group">
                    {!! Form::label('fcdt_claveunidad_id', 'Unidad' , ['class' => 'control-label']); !!}
                    {!! Form::select('fcdt_claveunidad_id', $unidades, null ,["class"=>"form-control"]);!!}
                </div>
            </div>
        </div>

    <div class="row">

        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('fcdt_claveproducto_id', 'Producto o Servicio' , ['class' => 'control-label']); !!}
                {!! Form::select('fcdt_claveproducto_id', $productos, null ,["class"=>"form-control"]);!!}
            </div>
        </div>

    </div>

    <hr>



    <div class="row">

        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('fcdt_concepto', 'Descripción concepto' , ['class' => 'control-label']); !!}
                {!! Form::text('fcdt_concepto', null ,["class"=>"form-control", "placeholder" => "", 'readonly' => true]);!!}
            </div>
        </div>

    </div>

    <hr>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('fcdt_importe', 'Total' , ['class' => 'control-label']); !!}
                {!! Form::text('fcdt_importe', 0 ,["class"=>"form-control", "placeholder" => ""]);!!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('fcdt_precio', 'Subtotal' , ['class' => 'control-label']); !!}
                {!! Form::text('fcdt_precio', null ,["class"=>"form-control", "placeholder" => "","readonly"=>true]);!!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('fcdt_iva', 'IVA' , ['class' => 'control-label']); !!}
                {!! Form::text('fcdt_iva', null,["class"=>"form-control", "placeholder" => "","readonly"=>true]);!!}
            </div>
        </div>

    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function(){

        jModal = {
            modal:  $('#modal-concepto'),
            form:  '#form-concepto-factura',
            local_id: '{{auth()->user()->Local->lcal_id }}',

            init: function(){
                let $this = this;

                $('#modal-btn-ok',$this.modal).click(function(){
                    $this.handleSubmit();
                });

                $this.handleSelectComprobantes();


                $('#fcdt_importe').off().on('change',function(){
                    $this.calcularIVA();
                });

                $this.calcularIVA();

                $("#fcdt_cpag_id").on('change',function(){$this.buildConceptDescription()});
                $("#select_concepto").on('change',function(){$this.buildConceptDescription()});

            },

            calcularIVA: function(){

                let total = $('#fcdt_importe').val();

                total = currency(total);

                subtotal = total.divide('1.16');
                iva = total.subtract(subtotal);

                $('#fcdt_importe').val(total);
                $('#fcdt_precio').val(subtotal);
                $('#fcdt_iva').val(iva);


            },


            handleSelectComprobantes: function(){
                let $this = this;
                let local_id = $('input[name="fact_lcal_id"]').val();
                let url_comprobantes = "{!! url('comprobante-pago/get-select-options-factura') !!}" + (local_id != "" ? "/" + local_id : "" );

                //Pedimos el array completo de los comprobantes para inicializar el select2
                $.ajax({
                    url: url_comprobantes,
                    type:'post',
                    dataType: 'json',
                    success: function(res){
                        // res.unshift({id:"",text:""});
                        // console.log(res);
                        $("#fcdt_cpag_id").select2({
                            data:res,
                            width:'100%',
                            allowClear:true,
                            templateResult: function (d) { return $(d.result_template); },
                            // templateSelection: function (d) { return $(d.text); },
                            placeholder: 'Seleccione...' });

                        $("#fcdt_cpag_id").val("").trigger('select2:change');


                    }

                });

            },

            buildConceptDescription: function(){

                let $this = this;

                let concepto = $('#select_concepto').val();
                let folio_comprobante = "";

                let cpag_data = $('#fcdt_cpag_id').select2('data')[0];

                if(cpag_data.selected == true){
                    folio_comprobante = cpag_data.folio;
                }

                $('#fcdt_concepto').val(concepto + ' según comprobante '+ folio_comprobante);


            },


            handleSubmit: function(){

                let $this = this;
                let url = $($this.form).attr('action');
                let data = $($this.form).serialize();

                console.log(data);

                $.ajax({
                    url: url,
                    method: 'POST',
                    // data: new FormData(form),
                    data: $($this.form).serialize(),
                    // contentType: false,
                    // cache: false,
                    // processData:false,
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            // APAlerts.success(res.message);
                            // dTables.oTable.draw();
                            $('#modal-btn-close').click();
                            jNuevaFactura.addConcepto(res.data);

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

        loadScript('{{ asset('js/currency.js') }}',
            function(){
                jModal.init();
            }
        );


    });

</script>
