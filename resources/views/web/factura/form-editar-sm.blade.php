{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
@if($factura->fact_estado != 'CAPTURADA')

    <p class="alert alert-info">
        La factura debe estar en estado: CAPTURADA
    </p>

@else
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
    <div class="container">

        {!! Form::model($factura,['id' => 'form-edit-factura','url' => $url]) !!}

        {!! Form::hidden('fact_id', null ,["class"=>"form-control", "placeholder" => ""]);!!}

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('fact_nombre_receptor', 'Receptor' , ['class' => 'control-label']); !!}
                    {!! Form::text('fact_nombre_receptor', null,["class"=>"form-control", "disabled"=>true]);!!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('fact_total', 'Total' , ['class' => 'control-label']); !!}
                    {!! Form::text('fact_total', '$ '. number_format($factura->fact_total,2),["class"=>"form-control text-right", "disabled"=>true]);!!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('fact_formapago_id', 'Forma de pago' , ['class' => 'control-label']); !!}
                    {!! Form::select('fact_formapago_id', $formas_pago,null,["class"=>"form-control"]);!!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('fact_usocfdi_id', 'Uso de CFDI' , ['class' => 'control-label']); !!}
                    {!! Form::select('fact_usocfdi_id', $usos_cfdi,null,["class"=>"form-control"]);!!}
                </div>
            </div>
        </div>

        @if(!$factura->fact_lcal_id || $factura->fact_lcal_id == 129)
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Periodicidad</label>
                        {!! Form::select('fact_periodicidad_id', $periodicidades, null, ['class' => 'form-control', 'placeholder' => 'Seleccione...']) !!}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="">Mes</label>
                        {!! Form::select('fact_mes_id', $meses, null, ['class' => 'form-control', 'placeholder' => 'Seleccione...']) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Año</label>
                        {!! Form::select('fact_anio', $anios, null, ['class' => 'form-control', 'placeholder' => 'Seleccione...']) !!}
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">Objeto de Impuesto</label>
                    {!! Form::select('fcdt_objeto_impuesto_id', $objetos_impuesto, null, ['class' => 'form-control', 'placeholder' => 'Seleccione...']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">Concepto</label>
                    {!! Form::text('fcdt_concepto', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        {!! Form::close() !!}

    </div>

    <script type="text/javascript">

        $(document).ready(function () {


            jModal = {
                modal: $('#modal-form'),
                form: '#form-edit-factura',


                init: function () {
                    let $this = this;

                    $('#modal-btn-ok', $this.modal).click(function () {
                        // console.log('enviando...');
                        $this.handleSubmit();
                    });


                },


                handleSubmit: function () {

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
                        beforeSend: function () {
                            $('.input-error').remove();
                        },
                        success: function (res) {

                            if (res.success === true) {
                                APAlerts.success(res.message);
                                dTables.oTable.draw();
                                $('#modal-btn-close').click();
                                // jNuevaFactura.addConcepto(res.data);

                            } else {

                                if (typeof res.message !== "undefined") {
                                    APAlerts.error(res.message);
                                    handleFormErrors($this.form, res.errors);
                                } else {
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
