@extends('layouts.main_vertical')

@section('title')
<span class="ti-bookmark-alt">&nbsp;</span>Solicitud de impresión de Gafetes
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

        jSolicitud = {
            key_id:'sgft_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',

            detalle_url:'{{url('solicitud-gafete/detalles')}}',
            pdf_url:'{{url('solicitud-gafete/pdf')}}',
            comprobante_pdf_url:'{{url('solicitud-gafete/comprobante-pdf')}}',
            imprimir_url:'{{url('solicitud-gafete/marcar-impreso')}}',
            prevalidar_comprobante_url:'{{url('solicitud-gafete/prevalidar-comprobante')}}',
            rechazar_url:'{{url('solicitud-gafete/rechazar')}}',
            validar_url:'{{url('solicitud-gafete/validar')}}',
            entregar_url:'{{url('solicitud-gafete/entregar')}}',

            form_url: '{{url('solicitud-gafete/form')}}',
            delete_url: '{{url('solicitud-gafete/delete')}}',

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
                $(".dataTable").on('click','.btn-detalle', function () {
                    let id = $(this).data('id');
                    $this.showDetailsModal(id);
                });

                $(".dataTable").on('click','.btn-imprimir', function () {
                    let id = $(this).data('id');
                    $this.showPrintModal(id);
                });

                $(".dataTable").on('click','.btn-prevalidar-comprobante', function () {
                    let id = $(this).data('id');
                    $this.showPrevalidarComprobanteModal(id);
                });

                $(".dataTable").on('click','.btn-pdf', function () {
                    let id = $(this).data('id');
                    $this.doPdf(id);
                });

                $(".dataTable").on('click','.btn-comprobante-pdf', function () {
                    let id = $(this).data('id');
                    $this.doComprobantePdf(id);
                });

                $(".dataTable").on('click','.btn-rechazar', function () {
                    let id = $(this).data('id');
                    $this.showRejectModal(id);
                });

                $(".dataTable").on('click','.btn-validar', function () {
                    let id = $(this).data('id');
                    $this.showValidarModal(id);
                });

                $(".dataTable").on('click','.btn-entregar', function () {
                    let id = $(this).data('id');
                    $this.showDeliverModal(id);
                });

            },


            showDetailsModal: function(id){
                let $this = this;
                let url = $this.detalle_url + '/' + id;
                APModal.open({
                        dom:$this.modal_dom,
                        title: 'Detalles de la Solicitud',
                        url: url,
                        size: 'modal-md',
                        bOk: false
                    });
            },

            showRejectModal: function(id){
                let $this = this;
                let url = $this.rechazar_url + '/' + id;
                APModal.open({
                        dom:$this.modal_dom,
                        title: 'Rechazar la Solicitud',
                        url: url,
                        size: 'modal-lg'
                        // bOk: false
                    });
            },

            showValidarModal: function(id){
                let $this = this;
                let url = $this.validar_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Validar la Solicitud',
                    url: url,
                    size: 'modal-lg'
                    // bOk: false
                });
            },

            showDeliverModal: function(id){
                let $this = this;
                let url = $this.entregar_url + '/' + id;
                APModal.open({
                        dom:$this.modal_dom,
                        title: 'Entregar gafete',
                        url: url,
                        size: 'modal-lg'
                        // bOk: false
                    });
            },

            showPrintModal: function(id){
                let $this = this;
                let url = $this.imprimir_url + '/' + id;
                APModal.open({
                        dom:$this.modal_dom,
                        title: 'Marcar gafete como impreso',
                        url: url,
                        size: 'modal-lg'
                        // bOk: false
                    });
            },

            showPrevalidarComprobanteModal: function(id){
                let $this = this;
                let url = $this.prevalidar_comprobante_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Prevalidar comprobante',
                    url: url,
                    size: 'modal-lg',
                    bOk: false
                });
            },

            doPdf: function(id){
                let $this = this;
                let url = $this.pdf_url + '/' + id;
                window.open(url,'_blank')
            },

            doComprobantePdf: function(id){
                let $this = this;
                let url = $this.comprobante_pdf_url + '/' + id;
                window.open(url,'_blank')
            }

        };

        jSolicitud.init();

    });
</script>
@endsection
