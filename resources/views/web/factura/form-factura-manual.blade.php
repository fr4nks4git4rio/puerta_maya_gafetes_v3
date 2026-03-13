<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <span class="btn fa fa-close pull-right m-t-10" id="btn-back">&nbsp</span>
            <h4>Nueva Factura Manual</h4>
        </div>
        <div class="card-body">

            {!! Form::model($factura,['id' => 'form-factura','url' =>$url ]) !!}

            {!! Form::text('fact_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
            {!! Form::text('fact_lcal_id', null , ["class" => "form-control d-none", "placeholder"=>"", 'id' => 'fact_lcal_id']) !!}
            {!! Form::text('fact_rfc_emisor',settings()->get('cfdi_rfc_emisor'), ["class" => "form-control d-none", "placeholder"=>""]) !!}
            {!! Form::text('fact_nombre_emisor',settings()->get('cfdi_nombre_emisor'), ["class" => "form-control d-none", "placeholder"=>""]) !!}

            <h4>Encabezado de factura</h4>
            <hr>

            <div class="row">

                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('fact_fecha_creacion', 'Fecha' , ['class' => 'control-label']); !!}
                        {!! Form::text('fact_fecha_creacion', date('Y-m-d') ,["class"=>"form-control", "placeholder" => "", "readonly"=>true]);!!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('fact_nombre_receptor', 'Receptor' , ['class' => 'control-label']); !!}
                        <select name="fact_lcal" id="fact_lcal" class="form-control select2-control">
                            <option value="">Seleccione...</option>
                            @foreach($receptores as $receptor)
                                <option value="{{$receptor}}">{{$receptor->lcal_razon_social}}</option>
                            @endforeach
                        </select>
                        {{--                        {!! Form::select('fact_lcal_id', , $receptor->lcal_razon_social ,["class"=>"form-control", "placeholder" => "", "readonly"=>true]);!!}--}}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('fact_rfc_receptor', 'RFC' , ['class' => 'control-label']); !!}
                        {!! Form::text('fact_nombre_receptor', null,["class"=>"form-control d-none", "placeholder" => "", "readonly"=>true, 'id' => 'fact_nombre_receptor']);!!}
                        {!! Form::text('fact_rfc_receptor', null,["class"=>"form-control", "placeholder" => "", "readonly"=>true, 'id' => 'fact_rfc_receptor']);!!}
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('fact_lugar_expedicion', 'CP' , ['class' => 'control-label']); !!}
                        {!! Form::text('fact_lugar_expedicion', null ,["class"=>"form-control", "placeholder" => "", "readonly"=>true, 'id' => 'fact_lugar_expedicion']);!!}
                    </div>
                </div>

                <div class="col-sm-1">
                    <div class="form-group">
                        {!! Form::label('fact_serie_id', 'Serie' , ['class' => 'control-label']); !!}
                        {!! Form::select('fact_serie_id', $series, 7 ,["class"=>"form-control"]);!!}
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('regimen_fiscal', 'Régimen Fiscal' , ['class' => 'control-label']); !!}
                        <input type="text" value="1" name="fact_regimenfiscal_id" hidden>
                        {!! Form::text('regimen_fiscal', null ,["class"=>"form-control", "readonly" => true, 'id' => 'regimen_fiscal']);!!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('direccion_fiscal', 'Dirección Fiscal' , ['class' => 'control-label']); !!}
                        {!! Form::text('direccion_fiscal', null ,["class"=>"form-control", "readonly" => true, 'id' => 'direccion_fiscal']);!!}
                    </div>
                </div>


                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('fact_formapago_id', 'Forma Pago' , ['class' => 'control-label']); !!}
                        {!! Form::select('fact_formapago_id', $formaspago, null ,["class"=>"form-control"]);!!}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('fact_metodopago_id', 'Método de pago' , ['class' => 'control-label']); !!}
                        {!! Form::select('fact_metodopago_id', $metodospago, null ,["class"=>"form-control"]);!!}
                    </div>
                </div>

                <div class="col-sm-1">
                    <div class="form-group">
                        {!! Form::label('fact_moneda_id', 'Moneda' , ['class' => 'control-label']); !!}
                        {!! Form::select('fact_moneda_id', $monedas, null ,["class"=>"form-control"]);!!}
                    </div>
                </div>

            </div>

            <div class="row form-group">
                <div class="col-sm-3">
                    <label for="">Periodicidad</label>
                    {!! Form::select('fact_periodicidad_id', $periodicidades, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-3">
                    <label for="">Mes</label>
                    {!! Form::select('fact_mes_id', $meses, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-2">
                    <label for="">Año</label>
                    {!! Form::select('fact_anio', $anios, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('fact_usocfdi_id', 'Uso CFDI' , ['class' => 'control-label']); !!}
                        {!! Form::select('fact_usocfdi_id', $usocfdi, null ,["class"=>"form-control"]);!!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <label for="">Importe sin IVA</label>
                    {!! Form::number('importe_sin_iva', 0, ['class' => 'form-control', 'step' => 0.01, 'id' => 'importe_sin_iva']) !!}
                </div>
            </div>

            <h4>
                Conceptos de facturación
                {{--                <span class="btn btn-primary" id="btn-nuevo-concepto">Agregar</span>--}}
            </h4>
            <hr>

            <div class="row">

                <div class="col-sm-12" id="conceptos-container">

                    <p class="alert alert-info text-center"><b>Sin conceptos</b></p>

                </div>

            </div>

            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('fact_cantidad_letra', 'Importe con letra' , ['class' => 'control-label']); !!}
                        {!! Form::text('fact_cantidad_letra', null ,["class"=>"form-control", "placeholder" => "", "readonly"=>true]);!!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('fact_observaciones', 'Observaciones' , ['class' => 'control-label']); !!}
                        {!! Form::textarea('fact_observaciones', null ,["class"=>"form-control", "placeholder" => "", "size"=>"20x2"]);!!}
                    </div>
                </div>
            </div>


            {!! Form::close() !!}


        </div>

        <div class="card-footer">
            <span class="btn btn-success pull-right" id="btn-generar-factura">Capturar Factura</span>
        </div>
    </div>

</div>


<script type="text/javascript">

    $(document).ready(function () {

        jNuevaFactura = {
            form: '#form-factura',
            total_importe: 0,
            total_subtotal: 0,
            total_iva: 0,
            modal_dom: 'modal-concepto',
            new_concepto_url: '{{url('factura/form-agregar-concepto')}}',
            do_factura_manual: '{{url('comprobante-pago/do-factura-manual')}}',
            modal_size: 'modal-md',
            productos: {!!  $productos !!},
            unidades: {!!  $unidades !!} ,
            conceptos: [],
            objetos_impuesto: {!! $objetos_impuesto !!},

            init: function () {
                let $this = this;

                $('#btn-back').off().on('click', function () {
                    $('#form-container').addClass('d-none');
                    $('#datatable-container').removeClass('d-none');
                });

                $('#btn-generar-factura').off().click(function () {
                    $this.handleSubmit();
                });

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $('#ptmp_fecha').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{App::getLocale()}}'
                });

                $('#btn-nuevo-concepto').off().on('click', function () {
                    $this.newConceptForm();
                });

                $('#fact_moneda_id').off().on('change', function () {
                    $this.importeALetras();
                });

                $('#importe_sin_iva').off().on('change', function ($event) {
                    $this.createConcepto($event.target.value);
                });

                $('#fact_lcal').on('change', function ($event) {
                    $('#fact_nombre_receptor').val('');
                    $('#fact_rfc_receptor').val('');
                    $('#regimen_fiscal').val('');
                    $('#direccion_fiscal').val('');
                    $('#fact_lugar_expedicion').val('');

                    if ($event.target.value) {
                        let receptor = JSON.parse($event.target.value);
                        $('#fact_nombre_receptor').val(receptor.lcal_razon_social);
                        $('#fact_rfc_receptor').val(receptor.lcal_rfc);
                        if (receptor.regimen_fiscal)
                            $('#regimen_fiscal').val(receptor.regimen_fiscal.nombre);
                        $('#direccion_fiscal').val(receptor.lcal_direccion_fiscal);
                        $('#fact_lugar_expedicion').val(receptor.lcal_codigo_postal);
                    }

                });
            },

            doFacturaManual: function (id, folio) {
                let $this = this;

                APAlerts.confirm({
                    message: '¿Crear Factura manual al Comprobante <b>' + folio + '</b>?',
                    confirmText: 'Proceder',
                    callback: function () {

                        $.ajax({
                            url: $this.do_factura_manual + '/' + id,
                            method: 'POST',
                            success: function (res) {
                                if (res.success === true) {
                                    APAlerts.success(res.message);
                                    window.location.reload();
                                } else {
                                    APAlerts.error(res.message);
                                }
                            }
                        });

                    }
                });

            },

            importeALetras: function () {
                let $this = this;

                let cantidadConLetras = numeroALetras($this.total_importe);

                if ($('#fact_moneda_id').val() == 2) { //USD
                    cantidadConLetras = numeroALetras($this.total_importe, {
                        divisa: 'USD',
                        plural: 'DOLARES',
                        singular: 'DOLAR'
                    })
                }

                $('#fact_cantidad_letra').val(cantidadConLetras);

            },

            newConceptForm: function () {
                let $this = this;

                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Agregar concepto a la factura',
                    url: $this.new_concepto_url,
                    //size: $this.modal_size
                });

            },

            createConcepto: function (importe_sin_iva) {
                let $this = this;

                // console.log( $this.comprobantes_factura);

                let folios = '';
                let importe = 0;
                let precio = 0;
                let iva = 0;

                $this.conceptos = [];


                precio = currency(parseFloat(importe_sin_iva))
                iva = precio.multiply(0.16);
                importe = precio.add(iva);

                let data_concepto = {
                    fcdt_cantidad: 1,
                    fcdt_claveunidad_id: 2, // pieza
                    fcdt_claveproducto_id: 3, //gafetes / portagafetes
                    fcdt_objeto_impuesto_id: 2, // Si objeto de impuesto
                    fcdt_concepto: '',
                    fcdt_importe: importe.value,
                    fcdt_precio: precio.value,
                    fcdt_iva: iva.value,
//                    fcdt_cpag_id: null

                };

                $this.addConcepto(data_concepto);


            },

            addConcepto: function (data) {

                let $this = this;

                $this.conceptos.push(data);

                $this.renderConceptos();

            },

            renderConceptos: function () {
                let $this = this;
                let html = "";
                let conceptos = $this.conceptos;
                let objetos_impuesto = $this.objetos_impuesto;

                let importe = currency(0);
                let subtotal = currency(0);
                let iva = currency(0);

                if (conceptos.length == 0) {

                    html = "<p class=\"alert alert-info text-center\"><b>Sin conceptos</b></p>";

                } else {

                    html = "<table class='table table-bordered table-condensed' id='table-conceptos'>";
                    html += "<thead>";
                    html += "<tr>";
                    html += "<th>Cantidad</th>";
                    html += "<th>Producto/Servicio</th>";
                    html += "<th>Unidad</th>";
                    html += "<th>Objeto de Impuesto</th>";
                    html += "<th>Concepto/Descripción</th>";
                    html += "<th>Subtotal</th>";
                    html += "<th>IVA</th>";
                    html += "<th>Importe</th>";
                    // html += "<th>&nbsp;</th>";
                    html += "</tr>";
                    html += "</thead>";

                    $.each(conceptos, function (k, v) {
                        // console.log('imprime',k,v);
                        // console.log($this.productos);

                        importe = importe.add(v.fcdt_importe);
                        iva = iva.add(v.fcdt_iva);
                        subtotal = subtotal.add(v.fcdt_precio);

                        // console.log('check de sumas',importe,iva , v.fcdt_importe, v['fcdt_importe']);

                        let producto = $this.productos[v['fcdt_claveproducto_id']];
                        let unidad = $this.unidades[v['fcdt_claveunidad_id']];

                        let options_objetos_impuesto = "";
                        $.each(objetos_impuesto, function (r, e) {
                            if (r == 2) {
                                options_objetos_impuesto += "<option value='" + r + "' selected>" + e + "</option>";
                            } else {
                                options_objetos_impuesto += "<option value='" + r + "'>" + e + "</option>";
                            }
                        })

                        let select_objeto_impuesto = "<select class='select_objetosimpuestos form-control' data-concepto='" + v['fcdt_concepto'] + "'>" + options_objetos_impuesto + "</select>";
                        let textarea_concepto = "<textarea class='textarea_concepto form-control' placeholder='Concepto/Descripción' rows='2'></textarea>"

                        html += "<tr>";
                        html += "<td class='text-center'>" + v['fcdt_cantidad'] + "</td>";
                        html += "<td>" + producto + "</td>";
                        html += "<td>" + unidad + "</td>";
                        html += "<td>" + select_objeto_impuesto + "</td>";
                        html += "<td>" + textarea_concepto + "</td>";
                        html += "<td class='text-right'>" + v['fcdt_precio'] + "</td>";
                        html += "<td class='text-right'>" + v['fcdt_iva'] + "</td>";
                        html += "<td class='text-right'>" + v['fcdt_importe'] + "</td>";
                        // html += "<td class='text-center'><span class='btn btn-sm btn-danger btn-eliminar-concepto' title='Eliminar' data-index='"+k+"' ><i class='fa fa-times'></i></span></td>";
                        html += "</tr>";

                    });

                    html += "<tr>";
                    html += "<td colspan='5' >&nbsp;</td>";
                    html += "<td class='text-right'><b>" + subtotal + "</b></td>";
                    html += "<td class='text-right'><b>" + iva + "</b></td>";
                    html += "<td class='text-right'><b>" + importe + "</b></td>";
                    // html += "<td class='text-right'>&nbsp;</td>";
                    // html += "<td class='text-center'></td>";
                    html += "</tr>";


                    html += "</table>";


                }

                $('#conceptos-container').html(html);
                if ($('.select_objetosimpuestos').length > 0) {
                    let valor = '';
                    $('.select_objetosimpuestos')[0].addEventListener('change', function ($event) {
                        valor = $event.target.value;
                        $this.conceptos[0].fcdt_objeto_impuesto_id = valor;
                    })
                }
                if ($('.textarea_concepto').length > 0) {
                    let valor = '';
                    $('.textarea_concepto')[0].addEventListener('change', function ($event) {
                        valor = $event.target.value;
                        $this.conceptos[0].fcdt_concepto = valor;
                    })
                }

                $this.total_importe = importe.value;
                $this.total_iva = iva.value;
                $this.total_subtotal = subtotal.value;

                $this.importeALetras();

                $this.setTableConceptosObservers();

            },

            setTableConceptosObservers: function () {

                let $this = this;

                // $('.btn-eliminar-concepto','#table-conceptos').off().on('click',function(){
                //     let key = $(this).data('key');
                //
                //     $this.conceptos.splice(key+1,1);
                //
                //     $this.renderConceptos();
                // });

            },

            handleSubmit: function () {

                let $this = this;
                let url = $($this.form).attr('action');

                if ($this.conceptos.length == 0) {
                    APAlerts.error('Agregue un concepto');
                    return false;
                }

                let lcal_id = JSON.parse($('#fact_lcal').val()).lcal_id;
                $('#fact_lcal_id').val(lcal_id);
                let formData = $($this.form).serializeArray();
                let paramConceptos = $.param({"conceptos": $this.conceptos});

                formData.push({name: "conceptos", value: paramConceptos});
                formData.push({name: "total_subtotal", value: $this.total_subtotal});
                formData.push({name: "total_iva", value: $this.total_iva});
                formData.push({name: "total_importe", value: $this.total_importe});

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
                            dTables.oTable.draw();
                            $('#btn-back').click();

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
            loadScript('{{ asset('js/currency.js') }}',
                function () {
                    jNuevaFactura.init();
                }
            )
        );


    });

</script>
