@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-truck">&nbsp;</span>Solcitud de gafetes de estacionamiento
@endsection

@section('content')

    {{-- F I L T R O S --}}
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">



                    <div class="row">
                        <div class="col-md-4">
                            <label style="cursor:pointer" id="filter-toggle" > <i class="fa fa-filter"></i> Filtros</label>
                            <div class="form-group">
                                <label for="filter-locales">Local</label>
                                {!! Form::select('filter-local', $locales ,  null , ["class"=>"form-control select2-control filter-control", "placeholder" => "Empresa  ", "style" => "width: 100%" ])!!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            &nbsp;
                        </div>

                        <div class="col-md-4 ">
                            <label> <i class="fa fa-user-plus"></i> Asignación de gafetes</label>
                            <table class="table table-condensed table-bordered">
                                <thead class="bg-primary">
                                    <tr class="text-center ">
                                        <th class="text-white">Clase</th>
                                        <th class="text-white">Espacios Totales</th>
                                        <th class="text-white">Gafetes asignados</th>
                                    </tr>

                                </thead>

                                <tr class="text-center">
                                    <td>AUTO</td>
                                    <td>{{$estadistica['espacios_totales_auto']}}</td>
                                    <td>{{$estadistica['gafetes_asignados_auto']}}</td>
                                </tr>
                                <tr class="text-center">
                                    <td>MOTO</td>
                                    <td>{{$estadistica['espacios_totales_moto']}}</td>
                                    <td>{{$estadistica['gafetes_asignados_moto']}}</td>
                                </tr>

                            </table>
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group">
{{--                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-plus-circle-o">&nbsp;</i>Nueva Solicitud</div>--}}
{{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-reapply"> <i class="zmdi zmdi-edit">&nbsp;</i>Volver a solicitar</div>--}}

                            {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Dropdown
                            </button> --}}
                        </div>

{{--                        <div class="btn btn-custom btn-trans waves-effect" id="btn-accesos"> <i class="zmdi zmdi-shield-security">&nbsp;</i>Accesos</div>--}}

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

        jGestR = {
            key_id:'gest_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',
            {{--form_url: '{{url('gafete-estacionamiento/form')}}',--}}
            {{--delete_url: '{{url('gafete-estacionamiento/delete')}}',--}}

            pdf_url:'{{url('gafete-estacionamiento/pdf')}}',
            detalle_url:'{{url('gafete-estacionamiento/detalles')}}',
            {{--form_reapply_url: '{{url('gafete-estacionamiento/form-reapply')}}',--}}
            comprobante_pdf_url:'{{url('gafete-estacionamiento/comprobante-pdf')}}',

            imprimir_url:'{{url('gafete-estacionamiento/marcar-impreso')}}',

            prevalidar_comprobante_url:'{{url('gafete-estacionamiento/prevalidar-comprobante')}}',
            rechazar_url:'{{url('gafete-estacionamiento/rechazar')}}',
            validar_url:'{{url('gafete-estacionamiento/validar')}}',
            entregar_url:'{{url('gafete-estacionamiento/entregar')}}',
            pendiente_cobro_url:'{{url('gafete-estacionamiento/do-marcar-pendiente-cobro')}}',
            cobrado_url:'{{url('gafete-estacionamiento/do-marcar-cobrada')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.handleDatatableButtons();

                dTables.oTable.on( 'draw', function () {
                    $this.handleDatatableButtons();
                });

                // $this.handleDoubleClick();

            },

            handleButtons: function(){
                let $this = this;

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

                $(".dataTable").off();

                // $(".dataTable").on('click','.btn-pdf', function () {
                //     let id = $(this).data('id');
                //     $this.doPdf(id);
                // });

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

                $(".dataTable").on('click','.btn-pendiente-cobro', function () {
                    let id = $(this).data('id');
                    $this.showPendienteCobroModal(id);
                });

                $(".dataTable").on('click','.btn-cobrado', function () {
                    let id = $(this).data('id');
                    $this.showCobradoModal(id);
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

            showPendienteCobroModal: function(id){
                let $this = this;
                APAlerts.confirm({
                    message: '¿Esta seguro de marcar como Pendiente de Cobro el Gafete?',
                    confirmText: 'Si, confirmar',
                    callback: function(){
                        $this.marcarPendienteCobro(id);
                    }
                });
            },

            showCobradoModal: function(id){
                let $this = this;
                APAlerts.confirm({
                    message: '¿Esta seguro de marcar como Cobrado el Gafete?',
                    confirmText: 'Si, confirmar',
                    callback: function(){
                        $this.marcarCobrado(id);
                    }
                });
            },

            marcarPendienteCobro: function (id) {
                let $this = this;
                let url = $this.pendiente_cobro_url + '/' + id;

                $.ajax({
                    url: url,
                    method: 'POST',
//                    data: formData,
//                    processData: true,
                    // contentType:false,
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            $('#btn-back').click();

                        }else{

                            if(typeof res.message !== "undefined"){
                                APAlerts.error(res.message);
                            }else{
                                APAlerts.error(res);
                            }

                        }
                    }
                });
            },

            marcarCobrado: function (id) {
                let $this = this;
                let url = $this.cobrado_url + '/' + id;

                $.ajax({
                    url: url,
                    method: 'POST',
//                    data: formData,
//                    processData: true,
                    // contentType:false,
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            $('#btn-back').click();

                        }else{

                            if(typeof res.message !== "undefined"){
                                APAlerts.error(res.message);
                            }else{
                                APAlerts.error(res);
                            }

                        }
                    }
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

        jGestR.init();

    });
</script>
@endsection
