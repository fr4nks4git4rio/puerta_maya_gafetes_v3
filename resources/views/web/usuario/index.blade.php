@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-accounts">&nbsp;</span>Usuarios
@endsection

@section('content')


<div class="row">
    <div class="col-md-12">

        <div class="panel">
            {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group">
                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Eliminar</div>
                        </div>
                        <div class="btn btn-custom btn-trans waves-effect" id="btn-roles"> <i class="zmdi zmdi-assignment-account">&nbsp;</i>Roles</div>
                        <div class="btn btn-custom btn-trans waves-effect" id="btn-token"> <i class="zmdi zmdi-car">&nbsp;</i>Token abre-puertas</div>
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
    $(document).ready(function(){
        jUsuarios = {

                key_id:'id',
                modal_dom: 'modal-form',
                form_url: '{{url('usuario/form')}}',
                delete_url: '{{url('usuario/delete')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleDoubleClick();

            },

            handleButtons: function(){
                let $this = this;

                $('#btn-add').click(function(){
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Nuevo Usuario',
                        url: $this.form_url
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

                $('#btn-roles').click(function(){

                    if($this.isSelected()){
                        $this.showRolesForm($this.getSelectedRowData($this.key_id));
                    }

                });

                $('#btn-token').click(function(){

                    if($this.isSelected()){
                        $this.showTokenForm($this.getSelectedRowData($this.key_id));
                    }

                });


            },

            handleDoubleClick: function(){
                let $this = this;

                $('#oTable tbody').on('dblclick', 'tr', function () {
                    let data = dTables.oTable.row( this ).data();
                    dTables.oTable.rows(this).select()
                    $this.showEditForm( data[$this.key_id]  );
                } );
            },

            getSelectedRowData: function(key){
                // if(!key) key = 'id'
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
                let id = $this.getSelectedRowData('id');

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
                    title: 'Editar Usuario',
                    url: $this.form_url+'/'+id
                });

            },

            showRolesForm: function(id){
                let $this = this;
                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Seleccionar Roles',
                    url:'{{url('usuario/form-roles')}}/'+id
                });

            },

            showTokenForm: function(id){
                let $this = this;
                APModal.open({
                    dom: $this.modal_dom,
                    size: 'modal-lg',
                    title: 'Token abre-puertas',
                    bOk: false,
                    url:'{{url('usuario/form-token')}}/'+id
                });

            },


            doRemove: function(data){
                let $this = this;

                APAlerts.confirm({
                    message: '¿Eliminar a <b>'+data.name +'</b>?',
                    callback: function(){
                        let url = $this.delete_url+'/'+data[$this.key_id];

                        $.ajax({
                            url: url,
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

            }

        };

        jUsuarios.init();

    });
</script>
@endsection
