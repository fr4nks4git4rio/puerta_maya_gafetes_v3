@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-airplay">&nbsp;</span>Controladoras
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
                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Eliminar</div>
                            {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Dropdown
                            </button> --}}
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-8">
                        {!! $dataTable->table() !!}
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

{!! $dataTable->scripts() !!}

<script type="text/javascript">
    $(document).ready(function(){

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        JControladora = {
            key_id:'ctrl_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-md',
            form_url: '{{url('controladoras/form')}}',
            delete_url: '{{url('controladoras/delete')}}',
            conectar_url: '{{url('controladoras/probar_conexion')}}',
            syncronizar_url: '{{url('controladoras/syncronizar_registros')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleDoubleClick();

                $this.handleDatatableButtons();

                dTables.oTable.on('draw', function () {
                    $this.handleDatatableButtons();
                });

            },

            handleButtons: function(){
                let $this = this;

                $('#btn-add').click(function(){
                    APModal.open({
                        dom:$this.modal_dom,
                        title: 'Nuevo Registro',
                        url: $this.form_url,
                        size: $this.modal_size,
                    });
                });

                $('#btn-edit').click(function(){

                    if($this.isSelected()){
                        $this.showEditForm( $this.getSelectedRowData($this.key_id) );
                    }

                });

                $('#btn-remove').click(function(){

                    if($this.isSelected()){
                        $this.doRemove($this.getSelectedRowData());
                    }

                });

            },

            handleDoubleClick: function(){
                let $this = this;

                $('#oTable tbody').on('dblclick', 'tr', function () {
                    let data = dTables.oTable.row( this ).data();
                    // console.log(data,data[$this.key_id] );
                    dTables.oTable.rows(this).select()
                    $this.showEditForm( data[$this.key_id] );
                } );
            },

            handleDatatableButtons: function () {
                let $this = this;

                // $(".dataTable").off();

                $('.btn-conectar').off().on('click', function () {
                    let id = $(this).data('id');
                    $this.probarConexionControladora(id);
                });

                $('.btn-syncronizar').off().on('click', function () {
                    let id = $(this).data('id');
                    $this.sincronizarRegistrosControladora(id);
                });

            },

            getSelectedRowData: function(key){
                let $this = this;

                let selectedRows = dTables.oTable.rows( {selected:true} ).data();

                if(selectedRows.length != 1){
                    return false;
                }
                if(key){
                    return selectedRows[0][key];
                }else{
                    return selectedRows[0];
                }

            },

            isSelected: function(){
                let $this = this;
                let id = $this.getSelectedRowData($this.key_id);

                if(id){
                    return true;
                }else{
                    APAlerts.warning("Selecciona un registro primero.");
                    return false;
                }
            },

            showEditForm: function(id){
                let $this = this;

                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Editar Registro',
                    url: $this.form_url+'/'+id,
                    size: $this.modal_size
                });

            },

            doRemove: function(data){
                let $this = this;

                APAlerts.confirm({
                    message: '¿Eliminar Controladora <b>'+data.ctrl_nombre+'</b>?',
                    confirmText: 'Eliminar',
                    callback: function(){

                        $.ajax({
                            url: $this.delete_url+'/'+data[$this.key_id],
                            method: 'POST',
                            success: function (res) {
                                if(res.success === true) {
                                    APAlerts.success(res.message);
                                    dTables.oTable.draw();
                                }else{
                                    APAlerts.error(res.message);
                                }
                            }
                        });

                    }
                    });

            },

            probarConexionControladora(id){
                let $this = this;
                $('#wifi_'+id).css({'display': 'none'});
                $('#cargando_'+id).css({'display': 'block'});
                $.ajax({
                    url: $this.conectar_url+'/'+id,
                    method: 'GET',
                    success: function (res) {
                        if(res.success === true) {
                            APAlerts.success(res.message);
//                            dTables.oTable.draw();
                        }else{
                            APAlerts.error(res.message);
                        }
                        $('#wifi_'+id).css({'display': 'block'});
                        $('#cargando_'+id).css({'display': 'none'});
                    },
                    error: function (res) {
                        $('#wifi_'+id).css({'display': 'block'});
                        $('#cargando_'+id).css({'display': 'none'});
                    }
                });
            },

            sincronizarRegistrosControladora(id){
                let $this = this;
                $('#sync_'+id).css({'display': 'none'});
                $('#loading_'+id).css({'display': 'block'});
                $.ajax({
                    url: $this.syncronizar_url+'/'+id,
                    method: 'GET',
                    success: function (res) {
                        if(res.success === true) {
                            APAlerts.success(res.message);
//                            dTables.oTable.draw();
                        }else{
                            APAlerts.error(res.message);
                        }
                        $('#sync_'+id).css({'display': 'block'});
                        $('#loading_'+id).css({'display': 'none'});
                    },
                    error: function (res) {
                        $('#sync_'+id).css({'display': 'block'});
                        $('#loading_'+id).css({'display': 'none'});
                    }
                });
            }

        };

        JControladora.init();

    });
</script>
@endsection
