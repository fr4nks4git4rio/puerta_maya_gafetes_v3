@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-calendar-check">&nbsp;</span>Permisos temporales
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


<div class="row">
    <div class="col-md-12">

        <div class="card">
            {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
            <div class="card-body">
                <div class="row d-none">
                    <div class="col-sm-12">
                        <div class="btn-group">
                            {{-- <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo Permiso</div> --}}
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
            key_id:'ptmp_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',

            detalle_url:'{{url('permiso-temporal/detalles')}}',
            aprobar_url:'{{url('permiso-temporal/aprobar')}}',
            rechazar_url:'{{url('permiso-temporal/rechazar')}}',
            entregar_url:'{{url('permiso-temporal/entregar')}}',
            pdf_url:'{{url('permiso-temporal/formato-oficial-pdf')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                dTables.oTable.on( 'draw', function () {
                    $this.handleButtons();
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

            },

            handleButtons: function(){
                let $this = this;

                $(".dataTable").off();

                $(".dataTable").on('click','.btn-detalle', function () {
                    let id = $(this).data('id');
                    $this.showDetailsModal(id);
                });

                $(".dataTable").on('click','.btn-aprobar', function () {
                    let id = $(this).data('id');
                    $this.showApproveModal(id);
                });

                $(".dataTable").on('click','.btn-rechazar', function () {
                    let id = $(this).data('id');
                    $this.showRejectModal(id);
                });

                $(".dataTable").on('click','.btn-formato-oficial', function () {
                    let id = $(this).data('id');
                    $this.doFormatoOficialPDF(id);
                });


                // $('.btn-entregar').off().on('click',function(){
                //     let id = $(this).data('id');
                //     $this.showDeliverModal(id);
                // });


            },


            showDetailsModal: function(id){
                let $this = this;
                let url = $this.detalle_url + '/' + id;
                APModal.open({
                        dom:$this.modal_dom,
                        title: 'Detalles del Permiso',
                        url: url,
                        size: 'modal-md',
                        bOk: false
                    });
            },



            showApproveModal: function(id){
                let $this = this;
                let url = $this.aprobar_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Aprobar el permiso',
                    url: url,
                    size: 'modal-md'
                    // bOk: false
                });
            },

            showRejectModal: function(id){
                let $this = this;
                let url = $this.rechazar_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Rechazar el permiso',
                    url: url,
                    size: 'modal-md'
                    // bOk: false
                });
            },

            doFormatoOficialPDF: function(id){
                let $this = this;
                let url = $this.pdf_url + '/' + id;
                window.open(url,'_blank')
            }

        };

        jPermiso.init();

    });
</script>
@endsection
