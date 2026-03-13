@extends('layouts.main_vertical')

@section('title')
    <span class="ti-bookmark-alt">&nbsp;</span>Solicitud de impresión de gafetes
    <br>
    <small>{{$local->lcal_nombre_comercial}}</small>
@endsection

@section('content')

    {{-- <style>

    table tbody td {
        padding: 2px;
    }
    </style> --}}
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-group">
                                <div class="btn btn-custom waves-effect" id="btn-add"><i
                                            class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva Solicitud
                                </div>
                                <div class="btn btn-custom btn-trans waves-effect" id="btn-reapply"><i
                                            class="zmdi zmdi-edit">&nbsp;</i>Volver a solicitar
                                </div>
                                {{-- <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div> --}}
                                {{-- <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Dar de baja</div> --}}
                                {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Dropdown
                                </button> --}}
                            </div>
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

    <script type="text/javascript">
        $(document).ready(function () {

            // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

            jSolicitud = {
                key_id: 'sgft_id',
                modal_dom: 'modal-form',
                modal_size: 'modal-lg',
                form_url: '{{url('solicitud-gafete/form')}}',
                delete_url: '{{url('solicitud-gafete/delete')}}',
                detalle_url: '{{url('solicitud-gafete/detalles')}}',
                form_reapply_url: '{{url('solicitud-gafete/form-reapply')}}',
                comprobante_pdf_url: '{{url('solicitud-gafete/comprobante-pdf')}}',
                url_new_comprobante: '{{ url('comprobante-pago/form') }}',


                init: function () {
                    let $this = this;

                    $this.handleButtons();

                    $this.handleDatatableButtons();

                    dTables.oTable.on('draw', function () {
                        $this.handleDatatableButtons();
                    });

                    // $this.handleDoubleClick();

                },

                handleButtons: function () {
                    let $this = this;

                    $('#btn-add').click(function () {
                        APModal.open({
                            dom: $this.modal_dom,
                            title: 'Nueva Solicitud',
                            url: $this.form_url,
                            size: $this.modal_size,
                        });
                    });

                    $('#btn-edit').click(function () {

                        if ($this.isSelected()) {
                            $this.showEditForm($this.getSelectedRowData($this.key_id));
                        }

                    });

                    $('#btn-remove').click(function () {

                        if ($this.isSelected()) {
                            $this.doRemove($this.getSelectedRowData());
                        }

                    });

                    $('#btn-reapply').click(function () {

                        if ($this.isSelected()) {

                            let statusString = $this.getSelectedRowData('sgft_estado');

                            if (statusString.indexOf('CANCELADA') == -1) {
                                APAlerts.warning('Solo se puede volver a solicitar registros CANCELADOS');
                                return true;
                            }

                            $this.showReapplyForm($this.getSelectedRowData($this.key_id));
                        }

                    });

                },

                handleDatatableButtons: function () {
                    let $this = this;

                    // $(".dataTable").off();

                    $('.btn-detalle').off().on('click', function () {
                        let id = $(this).data('id');
                        $this.showDetailsModal(id);
                    });


                    $('.btn-comprobante-pdf').off().on('click', function () {
                        let id = $(this).data('id');
                        $this.doComprobantePdf(id);
                    });

                    $('.btn-capturar-comprobante').off().on('click', function () {
                        let id = $(this).data('id');
                        $this.openComprobanteForm(id);
                    });

                },

                // handleDoubleClick: function(){
                //     let $this = this;

                //     $('#oTable tbody').on('dblclick', 'tr', function () {
                //         let data = dTables.oTable.row( this ).data();
                //         console.log(data,data[$this.key_id] );
                //         dTables.oTable.rows(this).select()
                //         $this.showEditForm( data[$this.key_id] );
                //     } );
                // },

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

                showReapplyForm: function (id) {
                    let $this = this;

                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Volver a Solicitar Gafete',
                        url: $this.form_reapply_url + '/' + id,
                        size: $this.modal_size
                    });

                },

                openComprobanteForm: function (id) {

                    let $this = this;

                    // if($ ('#sgft_tipo').val() == "" )
                    // {
                    //   APAlerts.warning('ELIJA UN TIPO DE SOLICITUD PRIMERO');
                    //   return;
                    // }

                    APModal.open({
                        dom: 'modal-comprobante',
                        title: 'Capturar comprobante de pago',
                        url: $this.url_new_comprobante + "?solicitud=" + id,
                        size: 'modal-md'
                    });


                },

                showEditForm: function (id) {
                    let $this = this;

                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Editar Registro',
                        url: $this.form_url + '/' + id,
                        size: $this.modal_size
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

                },

                showDetailsModal: function (id) {
                    let $this = this;
                    let url = $this.detalle_url + '/' + id;
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Detalles de la Solicitud',
                        url: url,
                        size: 'modal-md',
                        bOk: false
                    });
                },


                doComprobantePdf: function (id) {
                    let $this = this;
                    let url = $this.comprobante_pdf_url + '/' + id;
                    window.open(url, '_blank')
                }

            };

            jSolicitud.init();

        });
    </script>
@endsection
