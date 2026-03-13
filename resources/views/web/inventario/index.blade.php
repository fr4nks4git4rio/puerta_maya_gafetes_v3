@extends('layouts.main_vertical')

@section('title')
<span class="ti-package">&nbsp;</span>Inventario de tarjetas

@endsection

@section('content')

{{-- <style>

table tbody td {
    padding: 2px;
}
</style> --}}
<div class="row">

    <div class="col-md-2">

        <div class="card">
            <div class="card_body text-center">

                <h4>Tarjetas en stock</h4>
{{--                <br>--}}
                <h4 class="text-primary">
                    <b id="ph-stock"></b></h4>

                <small>Ultima compra: <b id="ph-last-buy"></b> </small>

            </div>
        </div>

    </div>

    <div class="col-md-4">

        <div class="card">
            <div class="card_body text-center">

                <h4>Limite para Advertencia</h4>
{{--                <br>--}}
                <h4 class="text-primary"> <b id="ph-inventory-low-limit"></b></h4>
                <small>Se alertará cuando el stock sea menor a este número</small>
            </div>
        </div>

    </div>

    <div class="col-md-6">

        <div class="card">
            <div class="card_body text-center">

                <h4>Gafetes impresos</h4>
                <div class="row">
                    <div class="col-md-6 text-center">
                        <h4 class="text-primary" id="ph-acceso"></h4>
                        <small><b>Acceso</b></small>
                    </div>
                    <div class="col-md-6 text-center">
                        <h4 class="text-primary" id="ph-estacionamiento"></h4>
                        <small><b>Estacionamiento</b></small>
                    </div>
                    {{--<div class="col-md-4 text-center">--}}
                        {{--<h4 class="text-primary" id="ph-permiso"></h4>--}}
                        {{--<small><b>Permiso</b></small>--}}
                    {{--</div>--}}
                </div>
                <small class="text-info">Desde la última baja de inventario capturada</small>

            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-12">

        <div class="card">
            {{-- <div class="panel-heading"> <div class="h3 panel-title">Cierto titulo/div> </div> --}}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
{{--                        <div class="btn-group">--}}
                            <div class="btn btn-custom waves-effect" id="btn-add"> <i class="zmdi zmdi-long-arrow-up">&nbsp;</i>Nueva compra</div>
                            <div class="btn btn-custom waves-effect" id="btn-baja"> <i class="zmdi zmdi-long-arrow-down">&nbsp;</i>Capturar baja</div>
{{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-edit"> <i class="zmdi zmdi-edit">&nbsp;</i>Editar</div>--}}
{{--                            <div class="btn btn-custom btn-trans waves-effect" id="btn-remove"> <i class="zmdi zmdi-minus-circle-outline">&nbsp;</i>Dar de baja</div>--}}
                            {{-- <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Dropdown
                            </button> --}}
{{--                        </div>--}}
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

        jInventario = {
            key_id:'invt_id',
            modal_dom: 'modal-form',
            modal_size: 'modal-md',
            form_url: '{{url('inventario/form')}}',
            form_url_baja: '{{url('inventario/form-baja')}}',
            delete_url: '{{url('inventario/delete')}}',
            get_stock_url: '{{url('inventario/get-stock-data')}}',

            init: function(){
                let $this = this;

                $this.handleButtons();

                $this.getStockData();

                // $this.handleDoubleClick();

            },

            handleButtons: function(){
                let $this = this;

                $('#btn-add').click(function(){
                    APModal.open({
                        dom:$this.modal_dom,
                        title: 'Nueva compra de tarjetas',
                        url: $this.form_url,
                        size: $this.modal_size,
                    });
                });

                $('#btn-baja').click(function(){
                    APModal.open({
                        dom:$this.modal_dom,
                        title: 'Captura de baja de inventario',
                        url: $this.form_url_baja,
                        size: $this.modal_size,
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

            },

            getStockData: function(){
                $this = this;
                let loader = "<span><i class='fa fa-gear fa-spin'></i></span>";
                $('#ph-inventory-low-limit').html(loader)
                $('#ph-stock').html(loader);

                $.getJSON($this.get_stock_url,function(data){
                    $('#empl-loader').addClass('d-none');

                    $('#ph-inventory-low-limit').html(data.inventory_low_limit + ' tarjetas')
                    $('#ph-stock').html(data.current_stock );
                    $('#ph-last-buy').html(data.last_buy );
                    $('#ph-acceso').html(data.current_used_cards.acceso );
                    $('#ph-estacionamiento').html(data.current_used_cards.estacionamiento );
//                    $('#ph-permiso').html(data.current_used_cards.permiso );



                });
            },

            handleDoubleClick: function(){
                let $this = this;

                $('#oTable tbody').on('dblclick', 'tr', function () {
                    let data = dTables.oTable.row( this ).data();
                    // console.log(data,data[$this.key_id] );
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

            showEditForm: function(id){
                let $this = this;

                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Editar Registro',
                    url: $this.form_url+'/'+id,
                    size: $this.modal_size
                });

            },

            doRemove: function(data){
                let $this = this;

                APAlerts.confirm({
                    message: '¿Eliminar el registro<b>'+data.invt_id+'</b>?',
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

        jInventario.init();

    });
</script>
@endsection
