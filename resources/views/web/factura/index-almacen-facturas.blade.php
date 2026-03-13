@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-receipt">&nbsp;</span>Almacén de Facturas
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

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_fecha_inicio">Fecha Inicio</label>
                            {!! Form::text('filter_fecha_inicio', null, ["class"=>'form-control filter-control date-control']) !!}
{{--                            {!! Form::select('filter-local', $locales ,  null , ["class"=>"form-control select2-control filter-control", "placeholder" => "Empresa  ", "style" => "width: 100%" ])!!}--}}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_fecha_fin">Fecha Fin</label>
                            {!! Form::text('filter_fecha_fin', null, ["class"=>'form-control filter-control date-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_folio">Folio</label>
                            {!! Form::text('filter_folio', null, ["class"=>'form-control filter-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            {!! Form::select('filter_estado', [ ''=>'TODOS', 'TIMBRADA'=>'TIMBRADA','CANCELADA'=>'CANCELADA'] ,null, ["class"=>'form-control filter-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_receptor">Receptor</label>
                            {!! Form::text('filter_receptor', null, ["class"=>'form-control filter-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">

                            <div class="btn btn-primary m-t-30" id="btn-excel"> <i class="fa fa-file-excel-o"></i> Exportar</div>
                        </div>
                    </div>



                </div>

            </div>

        </div>
    </div>

</div>
{{-- E N D  - O F - F I L T R O S --}}


<div class="row" id="datatable-container">
    <div class="col-md-12">

        <div class="card">

            <div class="card-body">

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


<div class="row" id="form-container">


</div>


<script type="text/javascript">
    $(document).ready(function(){

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        jAlmacenFacturas = {
            key_id:'fact_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',
            form_url: '{{url('factura/form')}}',
            timbrar_url: '{{url('factura/do-timbrar')}}',
            cancelar_url: '{{url('factura/form-cancelar-factura')}}',
            download_xml_url: '{{url('factura/do-download-xml')}}',
            download_pdf_url: '{{url('factura/do-download-pdf')}}',
            download_comprobantes_pdf_url: '{{url('factura/do-download-listado-comprobantes')}}',
            export_excel_url: '{{url('factura/do-export-excel')}}',

            send_mail_form_url: '{{url('factura/send-mail-form')}}',


            init: function(){
                let $this = this;

                $this.handleButtons();

                // $this.handleDoubleClick();

                dTables.oTable.on( 'draw', function () {
                    $this.handleDatatableButtons();
                });

                //controles de fecha
                $('.date-control').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language:'{{App::getLocale()}}'
                });


                //refrescar tabla al cambiar filtros
                $('.filter-control').on('change', function(e) {
                    dTables.oTable.draw();
                });

            },

            handleButtons: function(){
                let $this = this;

                // $('#btn-add').click(function(){
                //     $('#datatable-container').addClass('d-none');
                //     $('#form-container').removeClass('d-none');
                //     $('#form-container').html( ajaxLoader('panel') );
                //     ajax_update($this.form_url,'#form-container');
                //
                // });

                $('#btn-excel').click(function(){

                    let data = {};
                    let getData = function(d){
                            d.filter_estado = $('select[name=filter_estado]').val();
                            d.filter_fecha_inicio = $('input[name=filter_fecha_inicio]').val();
                            d.filter_fecha_fin = $('input[name=filter_fecha_fin]').val();
                            d.filter_folio = $('input[name=filter_folio]').val();
                            d.filter_receptor = $('input[name=filter_receptor]').val();
                        };

                    getData(data);

                    window.open($this.export_excel_url + '?' + $.param(data),'blank');


                });


            },

            handleDatatableButtons: function(){
                let $this = this;

                // $(".dataTable").off();

                $(".dataTable").on('click','.btn-cancelar', function () {
                    let id = $(this).data('id');
                    $this.showCancelarModal(id)
//                    $this.doCancelar(id, $(this));
                });


                $(".dataTable").on('click','.btn-xml', function () {
                    let id = $(this).data('id');
                    $this.downloadXml(id);
                });


                $(".dataTable").on('click','.btn-pdf', function () {
                    let id = $(this).data('id');
                    $this.downloadPdf(id);
                });

                $(".dataTable").on('click','.btn-comprobantes', function () {
                    let id = $(this).data('id');
                    $this.downloadListadoComprobantesPdf(id);
                });

                $(".dataTable").on('click','.btn-send-mail', function () {
                    let id = $(this).data('id');
                    $this.showSendMailModal(id);
                });
                //
                // $('.btn-rechazar').off().on('click',function(){
                //     let id = $(this).data('id');
                //     $this.showRejectModal(id);
                // });
                //
                // $('.btn-pdf').off().on('click',function(){
                //     let id = $(this).data('id');
                //     $this.doFormatoPDF(id);
                // });


            },

            doTimbrar: function(id,btn){

                let $this = this;

                APAlerts.confirm({
                    message: '¿Timbrar factura con folio: <b>'+id+'</b>?',
                    confirmText: 'Timbrar',
                    callback: function(){

                        btn.addClass('d-none');
                        $("body").css("cursor", "progress");

                        $.ajax({
                            url: $this.timbrar_url+'/'+id,
                            method: 'POST',
                            success: function (res) {
                                if(res.success === true) {
                                    APAlerts.success(res.message);
                                    dTables.oTable.draw();
                                }else{
                                    APAlerts.error(res.message);
                                }
                            },
                            complete: function(){
                                btn.removeClass('d-none');
                                $("body").css("cursor", "default");
                            }
                        });

                    }
                });
            },

            doCancelar: function(id,btn){

                let $this = this;

                APAlerts.confirm({
                    message: '¿Cancelar factura con folio: <b>'+id+'</b>?',
                    confirmText: 'Cancelar CFDI',
                    callback: function(){

                        btn.addClass('d-none');
                        $("body").css("cursor", "progress");

                        $.ajax({
                            url: $this.cancelar_url+'/'+id,
                            method: 'POST',
                            success: function (res) {
                                if(res.success === true) {
                                    APAlerts.success(res.message);
                                    dTables.oTable.draw();
                                }else{
                                    APAlerts.error(res.message);
                                }
                            },
                            complete: function(){
                                btn.removeClass('d-none');
                                $("body").css("cursor", "default");
                            }
                        });

                    }
                });
            },

            downloadXml: function(id){
                let $this = this;

                window.open($this.download_xml_url + '/' + id ,'blank');

            },

            downloadPdf: function(id){
                let $this = this;

                window.open($this.download_pdf_url + '/' + id ,'blank');

            },

            downloadListadoComprobantesPdf: function(id){
                let $this = this;

                window.open($this.download_comprobantes_pdf_url + '/' + id ,'blank');

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

            showCancelarModal: function(id){
                let $this = this;
                let url = $this.cancelar_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Cancelar Factura',
                    url: url,
                    size: 'modal-md',
                    // bOk: false
                });
            },

            showSendMailModal: function(id){
                let $this = this;
                let url = $this.send_mail_form_url + '/' + id;
                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Enviar factura por correo',
                    url: url,
                    size: 'modal-md',
                    // bOk: false
                });
            },


            doRemove: function(data){
                let $this = this;

                APAlerts.confirm({
                    message: '¿Elminar solicitud del empleado <b>'+data.sgft_nombre+'</b>?',
                    confirmText: 'Proceder',
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

            }

        };

        jAlmacenFacturas.init();

    });
</script>
@endsection
