@extends('layouts.main_vertical')

@section('title')
    <span class="ti-blackboard">&nbsp;</span>Cabecera de Factura
@endsection

@section('content')

    {{-- <style>

    table tbody td {
        padding: 2px;
    }
    </style> --}}
    <div class="row">
        <div class="col-sm-6 m-auto text-center">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">Nombre Emisor</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="cfdi_nombre_emisor"
                                           value="{{settings()->get('cfdi_nombre_emisor')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">RFC</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="cfdi_rfc_emisor"
                                           value="{{settings()->get('cfdi_rfc_emisor')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">Código Postal</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="cfdi_lugar_expedicion"
                                           value="{{settings()->get('cfdi_lugar_expedicion')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">Dirección Fiscal</label>
                                </div>
                                <div class="col-sm-9">
                                    <textarea name="cfdi_direccionfiscal_emisor" id="cfdi_direccionfiscal_emisor"
                                              rows="2" class="form-control">{{settings()->get('cfdi_direccionfiscal_emisor')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">Régimen Fiscal</label>
                                </div>
                                <div class="col-sm-9">
                                    {!! Form::select('cfdi_regimenfiscal_emisor', \App\CRegimenFiscal::all()->pluck('nombre', 'id'), settings()->get('cfdi_regimenfiscal_emisor'), ['class' => 'form-control', 'id' => 'cfdi_regimenfiscal_emisor']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="">Teléfono</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="cfdi_telefono_emisor"
                                           value="{{settings()->get('cfdi_telefono_emisor')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <a href="#" class="btn btn-primary" id="guardar">Guardar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

            panelPac = {
                init: function () {
                    let $this = this;

                    document.getElementById('guardar').addEventListener('click', function () {
                        $this.guardar();
                    })
                },

                guardar: function () {
                    $.ajax({
                        method: 'post',
                        url: '/settings/guardar-cabecera-factura',
                        data: {
                            'cfdi_nombre_emisor': $('#cfdi_nombre_emisor').val(),
                            'cfdi_rfc_emisor': $('#cfdi_rfc_emisor').val(),
                            'cfdi_lugar_expedicion': $('#cfdi_lugar_expedicion').val(),
                            'cfdi_telefono_emisor': $('#cfdi_telefono_emisor').val(),
                            'cfdi_direccionfiscal_emisor': $('#cfdi_direccionfiscal_emisor').val(),
                            'cfdi_regimenfiscal_emisor': $('#cfdi_regimenfiscal_emisor').val()
                        },
                        success: function (res) {
                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res);
                            }
                        }
                    })
                }

            };

            panelPac.init();

        });
    </script>
@endsection
