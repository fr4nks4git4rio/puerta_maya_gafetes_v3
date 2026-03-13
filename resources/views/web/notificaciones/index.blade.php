@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-shield-security">&nbsp;</span>Notificaciones
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-group">
                                <div class="btn btn-custom waves-effect" id="btn-check-all"><i
                                            class="zmdi zmdi-check-all">&nbsp;</i>Marcar como Leidos
                                </div>
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

            JNotificaciones = {
                key_id: 'id',
                modal_dom: 'modal-form',
                modal_size: 'modal-md',
                check_all_url: '{{url('/check_all_notificaciones')}}',
                check_some_url: '{{url('/check_some_notificaciones')}}',

                init: function () {
                    let $this = this;

                    $this.handleButtons();

                    $this.handleDoubleClick();

                },

                handleButtons: function () {
                    let $this = this;

                    $('#btn-check-all').click(function () {
                        $this.doCheckAll();
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

                },

                handleDoubleClick: function () {
                    let $this = this;

                    $('#oTable tbody').on('dblclick', 'tr', function () {
                        let data = dTables.oTable.row(this).data();
                        // console.log(data,data[$this.key_id] );
                        dTables.oTable.rows(this).select()
                        $this.showEditForm(data[$this.key_id]);
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

                allSelectedRows: function () {
                    return dTables.oTable.rows({selected: true}).data();
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
                doCheckAll: function () {
                    let $this = this;
                    let selectedRows = $this.allSelectedRows();
                    let cantidad = selectedRows.length;
                    if (cantidad === 0) {
                        APAlerts.confirm({
                            message: '¿Desea Marcar todas las <b>Notificaciones</b> como <b>Leidas</b>?',
                            confirmText: 'Marcar',
                            callback: function () {

                                $.ajax({
                                    url: $this.check_all_url,
                                    method: 'POST',
                                    success: function (res) {
                                        if (res.success === true) {
                                            APAlerts.success(res.message);
                                            dTables.oTable.draw();
//                                            window.location.href = '/notificaciones';
                                        } else {
                                            APAlerts.error(res.message);
                                        }
                                    }
                                });
                            }
                        });
                    }else{
                        let notificaciones_ids = [];
                        for (let i = 0; i < $this.allSelectedRows().length; i++) {
                            console.log($this.allSelectedRows()[i]);
                            notificaciones_ids.push($this.allSelectedRows()[i].id);
                        }
                        let message = '';
                        if(notificaciones_ids.length > 1){
                            message = '¿Desea Marcar las </b>'+notificaciones_ids.length+'</b> Notificaciones seleccionadas como <b>Leidas</b>?'
                        }else{
                            message = '¿Desea Marcar la <b>Notificación</b> seleccionadas como <b>Leida</b>?'
                        }
                        APAlerts.confirm({
                            message: message,
                            confirmText: 'Marcar',
                            callback: function () {

                                $.ajax({
                                    url: $this.check_some_url,
                                    data: {
                                        'notificaciones_ids': notificaciones_ids
                                    },
                                    method: 'POST',
                                    success: function (res) {
                                        if (res.success === true) {
                                            APAlerts.success(res.message);
                                            dTables.oTable.draw();
//                                            window.location.href = '/notificaciones';
                                        } else {
                                            APAlerts.error(res.message);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }

                };

            JNotificaciones.init();

        });
    </script>
@endsection
