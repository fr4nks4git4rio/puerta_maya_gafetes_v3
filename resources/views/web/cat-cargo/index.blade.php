@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-shield-check">&nbsp;</span>Categorías de Cargos
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
                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Eliminar</div>

                            {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Dropdown
                            </button> --}}
                        </div>

{{--                        <div class="btn btn-custom btn-trans waves-effect" id="btn-accesos"> <i class="zmdi zmdi-shield-security">&nbsp;</i>Accesos</div>--}}

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
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

        jCCargo = {
            key_id:'crgo_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-md',
            form_url: '{{url('cat-cargo/form')}}',
            delete_url: '{{url('cat-cargo/delete')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleDoubleClick();

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

                $('#btn-accesos').click(function(){

                    if($this.isSelected()){
                        $this.showAccesosForm($this.getSelectedRowData($this.key_id));
                    }

                });

            },

            handleDoubleClick: function(){
                let $this = this;

                $('#oTable tbody').on('dblclick', 'tr', function () {
                    let data = dTables.oTable.row( this ).data();
                    console.log(data,data[$this.key_id] );
                    dTables.oTable.rows(this).select()
                    $this.showEditForm( data[$this.key_id] );
                } );
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
                    message: '¿Eliminar cargo <b>'+data.crgo_descripcion+'</b>?',
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

            showAccesosForm: function(id){
                let $this = this;
                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Seleccionar Accesos',
                    url:'{{url('cat-cargo/form-accesos')}}/'+id
                });

            }

        };

        jCCargo.init();

    });
</script>
@endsection
