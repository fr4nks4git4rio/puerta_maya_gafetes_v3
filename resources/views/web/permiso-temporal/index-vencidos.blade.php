@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-calendar-close">&nbsp;</span>Permisos temporales por entregar
<br><small>CON {{$dias_vencidos}} DIAS O MAS DE VENCIDOS</small>
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
            pagar_url:'{{url('permiso-temporal/pagar-extemporaneo')}}',
            recibir_url:'{{url('permiso-temporal/recibir-extemporaneo')}}',

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

                //forma de ligar evento con plugin responsive activado
                $(".dataTable").on('click','.btn-detalle', function () {
                    let id = $(this).data('id');
                    $this.showDetailsModal(id);
                });

                $(".dataTable").on('click','.btn-devolver', function () {
                    let id = $(this).data('id');
                    $this.showReceiveModal(id);
                });

                $(".dataTable").on('click','.btn-pagar', function () {
                    let id = $(this).data('id');
                    $this.showPagarModal(id);
                });


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

            showReceiveModal: function(id){
                let $this = this;
                let url = $this.recibir_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Devolver gafete - Concluir permiso',
                    url: url,
                    size: 'modal-md'
                    // bOk: false
                });
            },

            showPagarModal: function(id){
                let $this = this;
                let url = $this.pagar_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Pagar gafete - Concluir permiso',
                    url: url,
                    size: 'modal-md'
                    // bOk: false
                });
            }

        };

        jPermiso.init();

    });
</script>
@endsection
