@extends('layouts.main_vertical')

@section('title')
<span class="ti-paint-roller">&nbsp;</span>Permisos de Mantenimiento
<br><small>{{$local->lcal_nombre_comercial}}</small>
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
                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva Solicitud</div>
                            <div class="btn btn-custom btn-trans waves-effect" id="btn-reapply"> <i class="zmdi zmdi-edit">&nbsp;</i>Volver a solicitar</div>
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
            form_url: '{{url('permiso-mantenimiento/form')}}',
            pdf_url: '{{url('permiso-mantenimiento/formato-pdf-firmante')}}',
            form_reapply_url: '{{url('permiso-mantenimiento/form-reapply')}}',
            // delete_url: '{{url('permiso-temporal/delete')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleTableButtons();

                dTables.oTable.on( 'draw', function () {
                    $this.handleTableButtons();//

                    // setTimeout($this.handleDatatableButtons,3000);
                });

            },

            handleButtons: function(){
                let $this = this;

                $('#btn-add').click(function(){
                    APModal.open({
                        dom:$this.modal_dom,
                        title: 'Solicitar Permiso de Mantenimiento',
                        url: $this.form_url,
                        size: $this.modal_size,
                    });
                });

                $('#btn-reapply').click(function(){

                    if($this.isSelected()){

                        let statusString = $this.getSelectedRowData('pmtt_estado');

                        if( statusString.indexOf('RECHAZADO') == -1){
                            APAlerts.warning('Solo se puede volver a solicitar permisos RECHAZADOS');
                            return true;
                        }

                        $this.showReapplyForm( $this.getSelectedRowData($this.key_id) );
                    }

                });

            },


            handleTableButtons: function(){
                let $this = this;

                $(".dataTable").off();

                //forma de ligar evento con plugin responsive activado
                $(".dataTable").on('click','.btn-pdf', function () {
                    let id = $(this).data('id');
                    $this.doFormatoPDF(id);
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

            showReapplyForm: function(id){
                let $this = this;

                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Volver a solicitar Permiso',
                    url: $this.form_reapply_url+'/'+id,
                    size: $this.modal_size
                });

            },

            doFormatoPDF: function(id){
                let $this = this;
                let url = $this.pdf_url + '/' + id;
                window.open(url,'_blank')
            }

        };

        jPermiso.init();

    });
</script>
@endsection
