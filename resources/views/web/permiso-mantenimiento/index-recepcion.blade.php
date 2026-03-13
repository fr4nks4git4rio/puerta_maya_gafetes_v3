@extends('layouts.main_vertical')

@section('title')
<span class="ti-paint-roller">&nbsp;</span>Permisos de Mantenimiento
@endsection

@section('content')

{{-- <style>

table tbody td {
    padding: 2px;
}
</style> --}}

{{-- F I L T R O S --}}
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">

                <label style="cursor:pointer" id="filter-toggle" > <i class="fa fa-filter"></i> Filtros</label>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filter-locales">Local</label>
                            {!! Form::select('filter-local', $locales ,  null , ["class"=>"form-control select2-control filter-control", "placeholder" => "Empresa  ", "style" => "width: 100%" ])!!}
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
{{-- E N D  - O F - F I L T R O S --}}


<div class="row" >
    <div class="col-md-12">

        <div class="card">
            {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group">
{{--                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva Solicitud</div>--}}
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
    $(document).ready(function(){

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        jPermiso = {
            key_id:'pmtt_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',
            //form_url: '{{url('permiso-mantenimiento/form')}}',
            detalle_url: '{{url('permiso-mantenimiento/detalles')}}',
            // delete_url: '{{url('permiso-temporal/delete')}}',

            init: function(){
                let $this = this;

                // $this.handleButtons();
                $this.handleDatatableButtons();

                dTables.oTable.on( 'draw', function () {
                    $this.handleDatatableButtons();
                });

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                //refrescar tabla al cambiar filtros
                $('.filter-control').on('change', function(e) {
                    dTables.oTable.draw();
                });


                // $this.handleDoubleClick();

            },

            handleDatatableButtons: function(){
                let $this = this;

                $(".dataTable").off();

                //forma de ligar evento con plugin responsive activado
                $(".dataTable").on('click','.btn-detalle', function () {
                    let id = $(this).data('id');
                    $this.showDetailsModal(id);
                });


            },

            // handleDoubleClick: function(){
            //     let $this = this;
            //
            //     $('#oTable tbody').on('dblclick', 'tr', function () {
            //         let data = dTables.oTable.row( this ).data();
            //         console.log(data,data[$this.key_id] );
            //         dTables.oTable.rows(this).select()
            //         $this.showEditForm( data[$this.key_id] );
            //     } );
            // },

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


            showDetailsModal: function(id){
                let $this = this;
                let url = $this.detalle_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Detalles del Permiso de Mantenimiento',
                    url: url,
                    size: $this.modal_size,
                    bOk: false
                });
            }


        };

        jPermiso.init();

    });
</script>
@endsection
