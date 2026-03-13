@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-collection-text">&nbsp;</span>Trazas
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
                            <div class="btn btn-custom waves-effect" id="btn-detail"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Detalles</div>
{{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div>--}}
{{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Eliminar</div>--}}
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
        jActivity = {
            key_id:'id',
            modal_dom: 'modal-form',
            detail_url: '{{url('activity/detail')}}',
            // form_url: '{{url('cliente/form')}}',
            // delete_url: '{{url('cliente/delete')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleDoubleClick();

            },

            handleButtons: function(){
                let $this = this;


                $('#btn-detail').click(function(){

                    if($this.isSelected()){
                        $this.showDetails( $this.getSelectedRowData($this.key_id) );
                    }

                });

            },

            handleDoubleClick: function(){
                let $this = this;

                $('#oTable tbody').on('dblclick', 'tr', function () {
                    let data = dTables.oTable.row( this ).data();
                    dTables.oTable.rows(this).select()
                    $this.showDetails( data[$this.key_id] );
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

            showDetails: function(id){
                let $this = this;

                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Detalles del registro',
                    url: $this.detail_url+'/'+id,
                    size: 'modal-lg'
                });

            },


        };

        jActivity.init();

    });
</script>
@endsection
