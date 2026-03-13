@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-face">&nbsp;</span>Empleados
    <br><small>{{ $local->lcal_nombre_comercial }}</small>
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
                                <div class="btn btn-custom waves-effect" id="btn-add"> <i
                                        class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo</div>
                                <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i
                                        class="zmdi zmdi-edit">&nbsp;</i>Editar</div>
                                <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i
                                        class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Dar de baja</div>
                                <div class="btn btn-custom btn-trans waves-effect" id="btn-asignar-permisos"> <i
                                        class="zmdi zmdi-car">&nbsp;</i>Asignar P. Est.</div>
                                <div class="btn btn-custom btn-trans waves-effect" id="btn-remove-permisos"> <i
                                        class="zmdi zmdi-car">&nbsp;</i>Quitar P. Est.
                                </div>
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
        $(document).ready(function() {

            // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

            jEmpleado = {
                key_id: 'empl_id',
                modal_dom: 'modal-form',
                modal_size: 'modal-lg',
                sgft_proceso_count: 0,
                form_url: '{{ url('empleado/form') }}',
                asignar_permisos_estacionamiento_url: '{{ url('solicitud-gafete-reasignar/form') }}',
                quitar_permisos_estacionamiento_url: '{{ url('solicitud-gafete-reasignar/delete') }}',
                delete_url: '{{ url('empleado/delete') }}',
                get_count_sgft_proceso_url: '{{ url('empleado/get-solicitudes-proceso') }}',

                init: function() {
                    let $this = this;

                    $this.handleButtons();

                    $this.handleDoubleClick();

                },

                handleButtons: function() {
                    let $this = this;

                    $('#btn-add').click(function() {
                        APModal.open({
                            dom: $this.modal_dom,
                            title: 'Nuevo Empleado',
                            url: $this.form_url,
                            size: $this.modal_size,
                        });
                    });

                    $('#btn-edit').click(function() {

                        if ($this.isSelected()) {
                            $this.showEditForm($this.getSelectedRowData($this.key_id));
                        }

                    });

                    $('#btn-asignar-permisos').click(function() {

                        if ($this.isSelected()) {
                            $this.showSetPermisosEstacionamiento($this.getSelectedRowData($this
                                .key_id));
                        }

                    });

                    $('#btn-remove').click(function() {

                        if ($this.isSelected()) {

                            $.getJSON($this.get_count_sgft_proceso_url + '/' + $this
                                .getSelectedRowData()[$this.key_id],
                                function(json) {
                                    $this.sgft_proceso_count = json.solicitudes_en_proceso;

                                    $this.doRemove($this.getSelectedRowData());

                                });

                        }

                    });

                    $('#btn-remove-permisos').click(function() {

                        if ($this.isSelected()) {
                            $this.doRemovePermisos($this.getSelectedRowData());
                        }

                    });

                },

                handleDoubleClick: function() {
                    let $this = this;

                    $('#oTable tbody').on('dblclick', 'tr', function() {
                        let data = dTables.oTable.row(this).data();
                        console.log(data, data[$this.key_id]);
                        dTables.oTable.rows(this).select()
                        $this.showEditForm(data[$this.key_id]);
                    });
                },

                getSelectedRowData: function(key) {
                    let $this = this;

                    let selectedRows = dTables.oTable.rows({
                        selected: true
                    }).data();

                    if (selectedRows.length != 1) {
                        return false;
                    }
                    if (key) {
                        return selectedRows[0][key];
                    } else {
                        return selectedRows[0];
                    }

                },

                isSelected: function() {
                    let $this = this;
                    let id = $this.getSelectedRowData($this.key_id);

                    if (id) {
                        return true;
                    } else {
                        APAlerts.warning("Selecciona un registro primero.");
                        return false;
                    }
                },

                showEditForm: function(id) {
                    let $this = this;

                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Editar Registro',
                        url: $this.form_url + '/' + id,
                        size: $this.modal_size
                    });

                },

                showSetPermisosEstacionamiento: function(id) {
                    let $this = this;

                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Asignar permisos de estacionamiento',
                        url: $this.asignar_permisos_estacionamiento_url + '/' + id,
                        size: $this.modal_size
                    });

                },

                doRemove: function(data) {
                    let $this = this;

                    let mensaje = '¿Dar de baja al empleado <b>' + data.empl_nombre + '</b>?';

                    if ($this.sgft_proceso_count > 0) {
                        mensaje +=
                            '<br> El empleado tiene solicitudes de gafete en proceso, estas se terminarán y de haber sido cobradas no se reembolsarán.';
                    }

                    APAlerts.confirm({
                        message: mensaje,
                        confirmText: 'Proceder',
                        callback: function() {

                            $.ajax({
                                url: $this.delete_url + '/' + data[$this.key_id],
                                method: 'POST',
                                success: function(res) {
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

                doRemovePermisos: function(data) {
                    let $this = this;

                    if (!data.sgftre_id) {
                        APAlerts.error("El empleado no cuenta con permisos asignados!");
                        return;
                    }

                    let mensaje = '¿Quitar permisos de estacionamiento al empleado <b>' + data.empl_nombre +
                        '</b>?';

                    APAlerts.confirm({
                        message: mensaje,
                        confirmText: 'Proceder',
                        callback: function() {

                            $.ajax({
                                url: $this.quitar_permisos_estacionamiento_url + '/' + data.sgftre_id,
                                method: 'DELETE',
                                success: function(res) {
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

            jEmpleado.init();

        });
    </script>
@endsection
