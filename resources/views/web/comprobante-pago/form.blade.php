<div class="container">

    {!! Form::model(null,['id' => 'form-comprobante','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::hidden('cpag_lcal_id',$local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    {!! Form::hidden('solicitud_id',$solicitud, ["class" => "form-control d-none", "placeholder"=>""]) !!}

    <div class="form-group row">
        {!! Form::label('lcal_nombre_comercial', 'Local', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_nombre_comercial', $local->lcal_nombre_comercial,["class"=>"form-control", "placeholder" => "", 'readonly'=>true ]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('cpag_fecha_pago', 'Fecha pago', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('cpag_fecha_pago', date('Y-m-d'),["class"=>"form-control", "placeholder" => "" ]);!!}
        </div>
    </div>

    {{--        <div class="form-group row">--}}
    {{--            {!! Form::label('cpag_tipo', 'Tipo' , ['class' => 'col-sm-3 control-label']); !!}--}}
    {{--            <div class="col-sm-3">--}}
    {{--                {!! Form::text('cpag_tipo', '',["class"=>"form-control", "placeholder" => "", 'readonly'=>true ]);!!}--}}
    {{--            </div>--}}

    {{--            {!! Form::label('cpag_costo_unitario', 'Costo U.' , ['class' => 'col-sm-3 control-label']); !!}--}}
    {{--            <div class="col-sm-3">--}}
    {{--                {!! Form::text('cpag_costo_unitario', '',["class"=>"form-control", "placeholder" => "", 'readonly'=>true ]);!!}--}}
    {{--            </div>--}}
    {{--        </div>--}}

    {{--        <div class="form-group row">--}}

    {{--            {!! Form::label('cpag_cantidad_pv', 'Cant. Primera Vez' , ['class' => 'col-sm-4 control-label']); !!}--}}
    {{--            <div class="col-sm-2">--}}
    {{--                {!! Form::text('cpag_cantidad_pv', 0,["class"=>"form-control", "placeholder" => "" ]);!!}--}}
    {{--            </div>--}}

    {{--            {!! Form::label('cpag_cantidad_rp', 'Cant. Reposición' , ['class' => 'col-sm-4 control-label']); !!}--}}
    {{--            <div class="col-sm-2">--}}
    {{--                {!! Form::text('cpag_cantidad_rp', 0,["class"=>"form-control", "placeholder" => "" ]);!!}--}}
    {{--            </div>--}}
    {{--        </div>--}}


    <div class="form-group row">


        {!! Form::label('cpag_importe_pagado', 'Importe Pagado' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('cpag_importe_pagado', 0,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>


    </div>

    <div class="form-group row d-none">


        {!! Form::label('cpag_cantidad_letra', 'Importe con letras' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('cpag_cantidad_letra', null ,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>


    </div>


    <div class="form-group row">
        {!! Form::label('cpag_folio_bancario', 'Folio' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-4">
            {!! Form::text('cpag_folio_bancario', null,["class"=>"form-control", "placeholder" => "" ]);!!}
        </div>
        <a href="#" id="btn-info-comprobante">
            <i class="fa fa-question-circle text-info"></i>
        </a>
    </div>

    {{--        <div class="form-group row">--}}
    {{--            {!! Form::label('cpag_aut_bancario', 'Referencia' , ['class' => 'col-sm-4 control-label']); !!}--}}

    {{--            <div class="col-sm-4">--}}
    {{--                {!! Form::text('cpag_aut_bancario', null ,["class"=>"form-control", "placeholder" => "" ]);!!}--}}
    {{--            </div>--}}

    {{--            <div class="col-sm-1">--}}
    {{--                <a href="#" id="btn-show-info-image" data-toggle="tooltip"--}}
    {{--                    title="Una imagen. <img src='#' />">--}}
    {{--                    <i class="fa fa-question-circle text-info"></i> </a>--}}

    {{--                <a data-toggle="modal" data-target="#modal-info-comprobante"--}}
    {{--                        data-original-title="" title="" href="#">--}}
    {{--                    <i class="fa fa-question-circle text-info"></i>--}}

    {{--                </a>--}}
    {{--                <a href="#" id="btn-info-comprobante">--}}
    {{--                    <i class="fa fa-question-circle text-info"></i>--}}

    {{--                </a>--}}

    {{--            </div>--}}
    {{--        </div>--}}

    <div class="form-group row">
        {!! Form::label('cpag_forma_pago', 'Forma de Pago' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::select('cpag_forma_pago', $formas_pago, 1 ,["class"=>"form-control"]);!!}
        </div>
    </div>


    <div class="form-group row">
        {!! Form::label('label_requiere_factura', '&nbsp;' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {{--                <div class="checkbox checkbox-custom">--}}
            {{-- {{dd($selected)}} --}}
            {{--                    {{Form::checkbox('cpag_requiere_factura',1, false ,['id'=>'cpag_requiere_factura'] )}}--}}

            {{--                    {{ Form::radio('cpag_requiere_factura', '0') }}--}}

            {{--                    <div>--}}

            <label class="control-label">
                {{ Form::radio('cpag_requiere_factura', '1',["class"=>"form-control",'checked'=>false]) }}
                Requiere factura
            </label>
            {{--                    </div>--}}

            {{--                    <div>--}}
            <label class="control-label">
                {{ Form::radio('cpag_requiere_factura', '0',["class"=>"form-control",'checked'=>false]) }}
                No requiere factura
            </label>
            {{--                    </div>--}}


            {{--                </div>--}}

        </div>

    </div>


    <div class="form-group row control-factura d-none">
        {!! Form::label('cpag_uso_cfdi', 'Uso CFDI' , ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::select('cpag_uso_cfdi', $usos_cfdi, 1 ,["class"=>"form-control"]);!!}
        </div>
    </div>

    {{--        <div class="form-group row">--}}
    {{--            {!! Form::label('cpag_aut_bancario', 'Aut Bancaria' , ['class' => 'col-sm-3 control-label']); !!}--}}
    {{--            <div class="col-sm-3">--}}
    {{--                {!! Form::text('cpag_aut_bancario', null,["class"=>"form-control", "placeholder" => "" ]);!!}--}}
    {{--            </div>--}}
    {{--        </div>--}}


    {{--                    <div class="row">--}}
    {{--                        {!! Form::label('sgft_file_comprobante', 'Comprobante Original' , ['class' => 'col-sm-4 control-label']); !!}--}}
    {{--                        <div class="col-sm-8">--}}
    {{--                            <input type="file" name="sgft_file_comprobante" id="sgft_file_comprobante" value="Seleccione..." accept="application/pdf">--}}
    {{--                            {!! Form::select('sgft_file_comprobante', $comprobantes ,null ,["class"=>"form-control select2-control", "placeholder" => "Seleccione..."]);!!}--}}
    {{--                        </div>--}}

    {{--                    </div>--}}

    <div class="row">
        {!! Form::label('cpag_file', 'Comprobante Original (2mb máx.)' , ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-4">
            <a class="btn btn-primary file-btn">
                {!! Form::file('cpag_file',["accept"=>"image/jpeg,image/png,application/pdf"]) !!}
            </a>
        </div>
    </div>


    {!! Form::close() !!}


    <div class="modal fade" id="modal-info-comprobante">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Modal Heading</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <img class='img' style='width:300px' src='{!! asset('images/users/avatar-2.jpg') !!}'/>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

            </div>

        </div>

        <script type="text/javascript">

            $(document).ready(function () {

                jModalComprobante = {
                    modal: $('#modal-comprobante'),
                    form: '#form-comprobante',
                    {{--tarifa_1: '{{  settings()->get('gft_tarifa_1', '0' ) }}',--}}
                    {{--tarifa_2: '{{  settings()->get('gft_tarifa_2', '0' ) }}',--}}

                    formas_pago: [{!! json_encode($formas_pago) !!}],
                    usos_cfdi: [{!! json_encode($usos_cfdi) !!}],
                    {{--          usos_cfdi: {{ $usos_cfdi }},--}}

                    init: function () {
                        let $this = this;

                        $('#modal-btn-ok', $this.modal).click(function () {
                            $this.handleSubmit();
                        });

                        $('input[name=cpag_requiere_factura]').prop('checked', false);

                        $('#cpag_fecha_pago').datepicker({
                            autoclose: true,
                            format: 'yyyy-mm-dd',
                            language: '{{App::getLocale()}}'
                        });

                        $('input[name=cpag_requiere_factura]').change(function () {

                            if ($('input[name=cpag_requiere_factura]:checked').val() == '1') {
                                $('.control-factura').removeClass('d-none');
                            } else {
                                $('.control-factura').addClass('d-none');
                            }


                        });

                        $('#btn-info-comprobante').click(function () {
                            Swal.fire({
                                imageUrl: '{{asset('images/ticket_help001.jpg')}}',
                                imageWidth: 325,
                                imageAlt: 'Ayuda de comprobante',
                            })
                        });

                        $('#cpag_importe_pagado').change(function () {

                            $('#cpag_cantidad_letra').val("");
                            if ($(this).val() > 0) {

                                let cantidadLetras = numeroALetras($(this).val());
                                $('#cpag_cantidad_letra').val(cantidadLetras);


                            }
                        });


                    },

                    handleSubmit: function () {

                        let $this = this;
                        // $this.doSubmit();

                        let require_factura = $('input[name=cpag_requiere_factura]:checked').val();
                        let forma_pago = $('#cpag_forma_pago').val();
                        let uso_cfdi = $('#cpag_uso_cfdi').val();

                        if (require_factura == undefined) {
                            APAlerts.warning('Debe elegir si requiere o no factura');
                            return false;
                        }

                        if (require_factura == '1') {

                            let fp = $this.formas_pago[0][forma_pago];
                            let uso = $this.usos_cfdi[0][uso_cfdi];

                            console.log($this.formas_pago, fp);

                            APAlerts.confirm({
                                message: '¿Seguro que quiere solicitar la factura del comprobante con Forma de pago: ' +
                                '<b>' + fp + '</b> ' +
                                'y Uso del documento: ' + '<b>' + uso + '</b> ?',
                                confirmText: 'Si, estoy seguro',
                                callback: function () {
                                    $this.doSubmit();
                                }
                            });

                        } else {
                            APAlerts.confirm({
                                message: '¿Esta seguro que no requiere facturar su comprobante de pago?',
                                confirmText: 'Si, estoy seguro',
                                callback: function () {
                                    $this.doSubmit();
                                }
                            });
                        }


                    },

                    doSubmit: function () {

                        let $this = this;
                        let url = $($this.form).attr('action');
                        let form = $($this.form)[0];
                        console.log(new FormData(form));

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: new FormData(form),
                            // data: $($this.form).serialize(),
                            contentType: false,
                            cache: false,
                            processData: false,
                            beforeSend: function () {
                                $('.input-error').remove();
                            },
                            success: function (res) {

                                if (res.success === true) {

                                    swal({
                                        // title: options.title || '',
                                        html: res.message,
                                        type: 'warning',
                                        showCancelButton: false,
                                        confirmButtonClass: 'btn-success btn-trans waves-effect',
                                        // cancelButtonClass: 'btn-secondary btn-trans waves-effect',
                                        // confirmButtonText: options.confirmText || 'Realizar acción',
                                        // cancelButtonText: options.cancelText || 'Cancelar',
                                    });
                                    // APAlerts.success(res.message);

                                    //dTables.oTable.draw();
                                    $('body').trigger('comprobante:added');
                                    $('#modal-btn-close', $this.modal).click();

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

                loadScript('{{ asset('js/numeroALetras.js') }}',
                    //loadScript('{{ asset('js/currency.js') }}',
                    function () {
                        jModalComprobante.init();
                    }
                    //)
                );


            });

        </script>
