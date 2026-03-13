<div class="col-sm-12">

    {!! Form::open(['id' => 'form-asociar-comprobantes','url' =>$url ]) !!}

    <div class="row">

        <div class="col-sm-12">
            <div class="form-group">
                <label for="">Factura Manual</label>
                <select name="factura_manual_id" id="factura_manual_id" class="select2-control">
                    <option value="0" selected>Seleccione...</option>
                    @foreach($facturas_manuales as $factura_manual)
                        <option value="{{$factura_manual->fact_id}}">{{$factura_manual->label_combo}}</option>
                    @endforeach
                </select>
                {{--                {!! Form::select('factura_manual_id', $facturas_manuales, null, ['class' => 'select2-control', 'id' => 'factura_manual_id', 'placeholder' => 'Seleccione...']) !!}--}}
            </div>
        </div>
    </div>

    <h4>Comprobantes</h4>
    <hr>

    <div class="row">

        @if(count($comprobantes) > 0)
            <p class="text-right w-100"><b>Monto Restante: </b><span id="monto_referencia" class="text-danger">0</span></p>
            <table class="table table-condensed">

                {{--        <tr class="bg-light">--}}
                {{--            <td colspan="7" class="text-center">--}}
                {{--                <b>Comprobantes a facturar</b>--}}
                {{--            </td>--}}
                {{--        </tr>--}}
                <tr class="bg-light">
                    <td>&nbsp;</td>
                    <td>Local</td>
                    <td>Fecha</td>
                    <td>Folio</td>
                    <td>Referencia</td>
                    <td>Forma pago</td>
                    <td>Descargar</td>
                    <td>Importe</td>
                </tr>


                @foreach($comprobantes as $c)
                    <tr>
                        <td>
                            {{Form::checkbox('cpag_'.$c->cpag_id, $c->cpag_id , false ,['id'=>'cpag_'.$c->cpag_id , 'class'=>'checkbox-comprobante'] )}}
                        </td>
                        <td>{{$c->Local->lcal_identificador}} - <b>{{$c->Local->lcal_razon_social}}</b></td>
                        <td>{{$c->cpag_fecha_pago}}</td>
                        <td>{{$c->cpag_folio_bancario}}</td>
                        <td>{{$c->cpag_aut_bancario}}</td>
                        <td>{{$c->FormaPago->descripcion}}</td>
                        <td><a href="{{asset('storage/comprobantes/'.$c->cpag_file)}}" target="_blank">
                                Comprobante original</a>
                        </td>
                        {{--                <td>{{$c->UsoCfdi->descripcion}}</td>--}}
                        <td class="text-right">{{number_format($c->cpag_importe_pagado,2)}}</td>
                    </tr>
                @endforeach


            </table>

        @else

            <p class="info">No se encontraron comprobantes a facturar.</p>

        @endif

    </div>

    {!! Form::close() !!}

    <span class="btn btn-success pull-right" id="btn-asociar-comprobantes">Asociar Comprobantes</span>

</div>


<script type="text/javascript">

    $(document).ready(function () {

        jNuevaFactura = {
            form: '#form-asociar-comprobantes',
            modal_dom: 'modal-concepto',
            modal_size: 'modal-lg',
            comprobantes: {!!  $comprobantes !!},
            facturas_manuales: {!!  $facturas_manuales !!},
            comprobantes_factura: [],
            monto_referencia: 0,
            factura_seleccionada: null,

            init: function () {
                let $this = this;

                $('#btn-asociar-comprobantes').off().click(function () {
                    $this.handleSubmit();
                });

                $('#factura_manual_id').off().change(function ($event) {
                    $this.selectFactura($event.target.value);
                });

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $this.setCheckComprobantesListeners();
            },

            selectFactura: function (id) {
                let $this = this;
                $this.factura_seleccionada = $this.facturas_manuales.filter(function (element, index) {
                    return id == element.fact_id;
                });
                if ($this.factura_seleccionada.length > 0)
                    $this.factura_seleccionada = $this.factura_seleccionada[0];
                else
                    $this.factura_seleccionada = null;
                $this.calcularMontoReferencia();
            },

            calcularMontoReferencia: function () {
                let $this = this;
                let monto_factura = 0;
                if ($this.factura_seleccionada)
                    monto_factura = $this.factura_seleccionada.fact_total;

                $this.comprobantes_factura.forEach(function (element, index) {
                    monto_factura -= element.cpag_importe_pagado;
                })

                $this.monto_referencia = monto_factura;
                $('#monto_referencia')[0].innerText = $this.monto_referencia;
                if($this.monto_referencia == 0){
                    $('#monto_referencia')[0].className = 'text-success';
                }else {
                    $('#monto_referencia')[0].className = 'text-danger';
                }
            },

            setCheckComprobantesListeners: function () {
                let $this = this;

                $('.checkbox-comprobante').off().on('change', function () {
                    if (this.checked) {

                        let cpag_id = $(this).val();

                        let comprobante = $this.comprobantes.find(function (el) {
                            return el.cpag_id == cpag_id;
                        });

                        $this.comprobantes_factura.push(comprobante);

                        // console.log('pushing',comprobante,$this.comprobantes_factura)

                    } else {

                        let cpag_id = $(this).val();
                        let key = null;

                        key = $this.comprobantes_factura.findIndex(function (el) {
                            return el.cpag_id == cpag_id;
                        });

                        $this.comprobantes_factura.splice(key, 1);

                        // console.log('poping',cpag_id,key, $this.comprobantes_factura);

                    }
                    $this.calcularMontoReferencia();
                })


            },

            handleSubmit: function () {

                let $this = this;
                let url = $($this.form).attr('action');

                if ($this.comprobantes_factura.length == 0) {
                    APAlerts.error('Seleccione al menos un Comprobante!');
                    return false;
                }

                if ($this.monto_referencia != 0) {
                    APAlerts.error('El Monto Restante tiene que ser 0!');
                    return false;
                }

                let formData = $($this.form).serializeArray();
//                let paramComprobantes = $.param({"conceptos": $this.comprobantes_factura});
//
//                formData.push({name: "comprobantes", value: paramComprobantes});

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: true,
                    // contentType:false,
                    beforeSend: function () {
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            $('#modal-btn-close').click();
                            setTimeout(() => {
                                $('#btn-add-comprobante-global').click();
                            }, 1500)
                            {{--window.location.href = '{{url('factura/form-contabilidad-global')}}';--}}
                        } else {
                            if (typeof res.message !== "undefined") {
                                APAlerts.error(res.message);
                            } else {
                                APAlerts.error(res);
                            }

                        }
                    }
                });

            }
        };


        loadScript('{{ asset('js/numeroALetras.js') }}',
            loadScript('{{ asset('js/currency.js') }}',
                function () {
                    jNuevaFactura.init();
                }
            )
        );


    });

</script>
