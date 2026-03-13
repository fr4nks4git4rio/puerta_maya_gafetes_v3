{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
@if($factura->fact_estado != 'TIMBRADA')

    <p class="alert alert-info">
        La factura debe estar en estado: TIMBRADA
    </p>

@else
    <div class="container">

        {!! Form::model($factura,['id' => 'form-cancelar-factura','url' => $url]) !!}

        {!! Form::hidden('fact_id', null ,["class"=>"form-control", "placeholder" => ""]);!!}

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('motivo_cancelacion', 'Motivo Cancelación' , ['class' => 'control-label']); !!}
                    {!! Form::select('fact_motivo_cancelacion_id', $motivos_cancelacion, null,["class"=>"form-control", 'placeholder' => 'Seleccione...', 'id' => 'fact_motivo_cancelacion_id']);!!}
                </div>
            </div>
        </div>

        <div class="row" id="div_folio_sustituto" style="display: none">
            <div class="col-sm-12">
                <div class="form-group">
                    {!! Form::label('folio_sustituto', 'Folio Sustituto' , ['class' => 'control-label']); !!}
                    {!! Form::select('folio_sustituto', $facturas_timbradas, null,["class"=>"form-control", 'placeholder' => 'Seleccione...']);!!}
                </div>
            </div>
        </div>

        {!! Form::close() !!}

    </div>

    <script type="text/javascript">

        $(document).ready(function () {


            jModal = {
                modal: $('#modal-form'),
                form: '#form-cancelar-factura',


                init: function () {
                    let $this = this;

                    $('#modal-btn-ok', $this.modal).click(function () {
                        // console.log('enviando...');
                        $this.handleSubmit();
                    });

                    $('#fact_motivo_cancelacion_id', $this.modal).change(function ($event) {
                        if($event.target.value == 1){
                            $('#div_folio_sustituto').css('display', 'inline-block')
                        }else{
                            $('#div_folio_sustituto').css('display', 'none')
                        }
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
