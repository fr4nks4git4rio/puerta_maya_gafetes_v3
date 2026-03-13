@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-receipt">&nbsp;</span>Facturas (Hora: {{$hora}})
    {{--<br><small>{{$local->lcal_nombre_comercial}}</small>--}}
@endsection

@section('content')

    {{-- <style>

    table tbody td {
        padding: 2px;
    }
    </style> --}}
    <div class="row" id="datatable-container">
        <div class="col-md-12">

            <div class="card">
                {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            {{--                        <div class="btn-group">--}}
                            {{--                            <div class="btn btn-custom waves-effect" id="btn-add-factura"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva Factura</div>--}}
                            <div class="btn btn-custom waves-effect" id="btn-add-comprobante-global"><i
                                        class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo Comprobante Global
                            </div>
                            <div class="btn btn-custom waves-effect" id="btn-add-factura-manual"><i
                                        class="zmdi zmdi-plus-circle-o">&nbsp;</i>Factura Manual
                            </div>
                            {{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-reapply"> <i class="zmdi zmdi-edit">&nbsp;</i>Volver a solicitar</div>--}}
                            {{-- <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Dar de baja</div> --}}
                            {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Dropdown
                            </button> --}}
                            {{--                        </div>--}}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {!! $dataTable->scripts() !!}


    <div class="row d-none" id="form-container">

        <div class="col-sm-12">
            <p class="alert alert-info"> Formulario </p>
        </div>

    </div>


    <script type="text/javascript">
        $(document).ready(function () {

            // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

            jFacturasContabilidad = {
                key_id: 'fact_id',
                modal_dom: 'modal-form',
                modal_size: 'modal-lg',
                form_url: '{{url('factura/form-contabilidad')}}',
                form_url_global: '{{url('factura/form-contabilidad-global')}}',
                form_url_factura_manual: '{{url('factura/form-factura-manual')}}',
                timbrar_url: '{{url('factura/do-timbrar')}}',
                eliminar_url: '{{url('factura/do-eliminar')}}',
                capturar_url: '{{url('factura/do-capturar')}}',
                cancelar_url: '{{url('factura/do-cancelar')}}',
                download_xml_url: '{{url('factura/do-download-xml')}}',
                download_pdf_url: '{{url('factura/do-download-pdf')}}',
                download_comprobantes_pdf_url: '{{url('factura/do-download-listado-comprobantes')}}',
                url_form_validar: '{{ url('comprobante-pago/form-validar') }}',
                url_form_edit: '{{ url('factura/form-editar-sm') }}',

                send_mail_form_url: '{{url('factura/send-mail-form')}}',

                init: function () {
                    let $this = this;

                    $this.handleButtons();

                    // $this.handleDoubleClick();

                    dTables.oTable.on('draw', function () {
                        $this.handleDatatableButtons();
                    });

                },

                handleButtons: function () {
                    let $this = this;

                    $('#btn-add-factura').click(function () {
                        $('#datatable-container').addClass('d-none');
                        $('#form-container').removeClass('d-none');
                        $('#form-container').html(ajaxLoader('panel'));
                        ajax_update($this.form_url, '#form-container');
                    });

                    $('#btn-add-comprobante-global').click(function () {
                        $('#datatable-container').addClass('d-none');
                        $('#form-container').removeClass('d-none');
                        $('#form-container').html(ajaxLoader('panel'));
                        ajax_update($this.form_url_global, '#form-container');
                    });

                    $('#btn-add-factura-manual').click(function () {
                        $('#datatable-container').addClass('d-none');
                        $('#form-container').removeClass('d-none');
                        $('#form-container').html(ajaxLoader('panel'));
                        ajax_update($this.form_url_factura_manual, '#form-container');
                    });


                },

                handleDatatableButtons: function () {
                    let $this = this;

                    $(".dataTable").off();

                    $(".dataTable").on('click', '.btn-timbrar', function () {
                        let id = $(this).data('id');
                        $this.doTimbrar(id, $(this));
                    });


                    $(".dataTable").on('click', '.btn-eliminar', function () {
                        let id = $(this).data('id');
                        $this.doEliminar(id, $(this));
                    });


                    $(".dataTable").on('click', '.btn-cancelar', function () {
                        let id = $(this).data('id');
                        $this.doCancelar(id, $(this));
                    });


                    $(".dataTable").on('click', '.btn-xml', function () {
                        let id = $(this).data('id');
                        $this.downloadXml(id);
                    });


                    $(".dataTable").on('click', '.btn-pdf', function () {
                        let id = $(this).data('id');
                        $this.downloadPdf(id);
                    });


                    $(".dataTable").on('click', '.btn-comprobantes', function () {
                        let id = $(this).data('id');
                        $this.downloadListadoComprobantesPdf(id);
                    });


                    $(".dataTable").on('click', '.btn-validar', function () {
                        let id = $(this).data('id');
                        $this.showValidarComprobanteModal(id);
                        // $this.downloadPdf(id);
                    });

                    $(".dataTable").on('click', '.btn-editar', function () {
                        let id = $(this).data('id');
                        $this.showEditModal(id);
                        // $this.downloadPdf(id);
                    });


                    $(".dataTable").on('click', '.btn-capturar', function () {

                        let id = $(this).data('id');
                        $this.doCapturar(id, $(this));

                    });

                    $(".dataTable").on('click', '.btn-send-mail', function () {
                        let id = $(this).data('id');
                        $this.showSendMailModal(id);
                    });


                    //
                    // $('.btn-rechazar').off().on('click',function(){
                    //     let id = $(this).data('id');
                    //     $this.showRejectModal(id);
                    // });
                    //
                    // $('.btn-pdf').off().on('click',function(){
                    //     let id = $(this).data('id');
                    //     $this.doFormatoPDF(id);
                    // });


                },

                doTimbrar: function (id, btn) {

                    let $this = this;

                    APAlerts.confirm({
                        message: '¿Timbrar factura con folio: <b>' + id + '</b>?',
                        confirmText: 'Timbrar',
                        callback: function () {

                            btn.addClass('d-none');
                            $("body").css("cursor", "progress");

                            $.ajax({
                                url: $this.timbrar_url + '/' + id,
                                method: 'POST',
                                success: function (res) {
                                    if (res.success === true) {
                                        APAlerts.success(res.message);
                                        dTables.oTable.draw();
                                    } else {
                                        APAlerts.error(res.message);
                                    }
                                },
                                complete: function () {
                                    btn.removeClass('d-none');
                                    $("body").css("cursor", "default");
                                }
                            });

                        }
                    });
                },

                doCancelar: function (id, btn) {

                    let $this = this;

                    APAlerts.confirm({
                        message: '¿Cancelar factura con folio: <b>' + id + '</b>?',
                        confirmText: 'Cancelar CFDI',
                        callback: function () {

                            btn.addClass('d-none');
                            $("body").css("cursor", "progress");

                            $.ajax({
                                url: $this.cancelar_url + '/' + id,
                                method: 'POST',
                                success: function (res) {
                                    if (res.success === true) {
                                        APAlerts.success(res.message);
                                        dTables.oTable.draw();
                                    } else {
                                        APAlerts.error(res.message);
                                    }
                                },
                                complete: function () {
                                    btn.removeClass('d-none');
                                    $("body").css("cursor", "default");
                                }
                            });

                        }
                    });
                },

                doEliminar: function (id, btn) {

                    let $this = this;

                    APAlerts.confirm({
                        message: '¿Eliminar factura con folio: <b>' + id + '</b>?',
                        confirmText: 'Eliminar',
                        callback: function () {

                            btn.addClass('d-none');
                            $("body").css("cursor", "progress");

                            $.ajax({
                                url: $this.eliminar_url + '/' + id,
                                method: 'POST',
                                success: function (res) {
                                    if (res.success === true) {
                                        APAlerts.success(res.message);
                                        dTables.oTable.draw();
                                    } else {
                                        APAlerts.error(res.message);
                                    }
                                },
                                complete: function () {
                                    btn.removeClass('d-none');
                                    $("body").css("cursor", "default");
                                }
                            });

                        }
                    });
                },

                doCapturar: function (id, btn) {

                    let $this = this;

                    APAlerts.confirm({
                        message: '¿Pasar la factura con folio: <b>' + id + '</b> a estado <b>CAPTURADO<b/>?',
                        confirmText: 'Si.',
                        callback: function () {

                            btn.addClass('d-none');
                            $("body").css("cursor", "progress");

                            $.ajax({
                                url: $this.capturar_url + '/' + id,
                                method: 'POST',
                                success: function (res) {
                                    if (res.success === true) {
                                        APAlerts.success(res.message);
                                        dTables.oTable.draw();
                                    } else {
                                        APAlerts.error(res.message);
                                    }
                                },
                                complete: function () {
                                    btn.removeClass('d-none');
                                    $("body").css("cursor", "default");
                                }
                            });

                        }
                    });
                },

                downloadXml: function (id) {
                    let $this = this;

                    window.open($this.download_xml_url + '/' + id, 'blank');

                },

                downloadPdf: function (id) {

                    console.log('donwlad pdf', id);
                    let $this = this;

                    window.open($this.download_pdf_url + '/' + id, 'blank');

                },

                downloadListadoComprobantesPdf: function (id) {
                    let $this = this;

                    window.open($this.download_comprobantes_pdf_url + '/' + id, 'blank');

                },

                showValidarComprobanteModal: function (id) {
                    let $this = this;
                    let url = $this.url_form_validar + '/' + id;
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Validar comprobante',
                        url: url,
                        size: 'modal-lg',
                        bOk: false
                    });
                },

                showEditModal: function (id) {
                    let $this = this;
                    let url = $this.url_form_edit + '/' + id;
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Editar factura',
                        url: url,
                        size: 'modal-md',
                        // bOk: false
                    });
                },

                getSelectedRowData: function (key) {
                    let $this = this;

                    let selectedRows = dTables.oTable.rows({selected: true}).data();

                    if (selectedRows.length != 1) {
                        return false;
                    }
                    if (key) {
                        return selectedRows[0][key];
                    } else {
                        return selectedRows[0];
                    }

                },

                isSelected: function () {
                    let $this = this;
                    let id = $this.getSelectedRowData($this.key_id);

                    if (id) {
                        return true;
                    } else {
                        APAlerts.warning("Selecciona un registro primero.");
                        return false;
                    }
                },

                showSendMailModal: function (id) {
                    let $this = this;
                    let url = $this.send_mail_form_url + '/' + id;
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Enviar factura por correo',
                        url: url,
                        size: 'modal-md',
                        // bOk: false
                    });
                },


                doRemove: function (data) {
                    let $this = this;

                    APAlerts.confirm({
                        message: '¿Elminar solicitud del empleado <b>' + data.sgft_nombre + '</b>?',
                        confirmText: 'Proceder',
                        callback: function () {

                            $.ajax({
                                url: $this.delete_url + '/' + data[$this.key_id],
                                method: 'POST',
                                success: function (res) {
                                    if (res.success === true) {
                                        APAlerts.success(res.message);
                                        dTables.oTable.draw();
                                    } else {
                                        APAlerts.error(res.message);
                                    }
                                }
                            });

                        }
                    });

                }

            };

            jFacturasContabilidad.init();

        });
    </script>
@endsection
