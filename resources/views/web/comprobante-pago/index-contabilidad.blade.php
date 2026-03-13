@extends('layouts.main_vertical')

@section('title')
<span class="ti-receipt">&nbsp;</span>Validación  de comprobantes
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
                            {{-- <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nuevo Gafete</div> --}}
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

        jComprobantes = {
            key_id:'sgft_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',

            url_form_validar:'{{ $url_form_validar }}',


            init: function(){
                let $this = this;

                // $this.handleButtons();
                $this.handleDatatableButtons();

                dTables.oTable.on('draw',function(){
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


            },

            handleDatatableButtons: function(){
                let $this = this;

                // console.log('init botones');
                $(".dataTable").off();

                //forma de ligar evento con plugin responsive activado

                $(".dataTable").on('click','.btn-validar-comprobante', function () {
                    let id = $(this).data('id');
                    $this.showValidarComprobanteModal(id);
                });

            },



            showValidarComprobanteModal: function(id){
                let $this = this;
                let url = $this.url_form_validar + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Validar comprobante',
                    url: url,
                    size: 'modal-lg',
                    bOk: false
                });
            }

        };

        jComprobantes.init();

    });
</script>
@endsection
