@extends('layouts.main_vertical')

@section('title')
    <span class="ti-blackboard">&nbsp;</span>Panel PAC
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
                        <div class="form-group col-sm-12">
                            <fieldset>
                                <legend>Modo de timbrado</legend>
                                <div class="radios text-center pt-3">
                                    <label class="radio-inline mr-4">
                                        <input type="radio" name="modo" id="prueba" value="prueba"
                                               @if(settings()->get('cfdi_test_mode') == 1) checked @endif> Prueba
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="modo" id="produccion" value="produccion"
                                               @if(settings()->get('cfdi_test_mode') != 1) checked @endif> Producción
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group col-sm-12 text-center">
                            <fieldset>
                                <legend>Revisar Facturas</legend>
                                <div class="form-group">
                                    <br>
                                    <a href="https://www.timbracfdi.mx/portalintegradores/ConsultaCFDI.aspx?I=i1%2fBALvaBGYRKKqlBnWq%2b%2fpf4XsI9CT8vTbw97mYyFw%3d&E=q%2foW55zeGm9ICz43jdbBTQ%3d%3d"
                                       target="_blank"
                                       class="btn btn-danger" type="button"
                                       style="border-radius:3px; color: red; font-weight:bold">Visitar
                                        portal del PAC</a>
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group col-sm-12">
                            <fieldset>
                                <legend>Timbres Disponibles</legend>
                                <div class="form-group col-sm-8 m-auto">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                        class="fa fa-cog fa-spin"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="container_timbres"
                                               value="Obteniendo Información">
                                    </div>
                                </div>
                            </fieldset>
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

                    $this.cargarTimbresDisponibles();

                    document.getElementById('prueba').addEventListener('click', function () {
                        $this.cambiarModo('1');
                    })
                    document.getElementById('produccion').addEventListener('click', function () {
                        $this.cambiarModo('0');
                    })

                },

                cargarTimbresDisponibles: function () {
                    $.ajax({
                        dataType: "json",
                        url: "/local/conta/obtener_timbres_disponibles",
                        success: function (data) {
                            $('.input-group-prepend').css('display', 'none');
                            if (data.success) {
                                $('#container_timbres').addClass('text-success');
                                $('#container_timbres').val(data.data);
                            } else {
                                $('#container_timbres').addClass('text-danger');
                                $('#container_timbres').val('Error Obteniendo la Información.');
                                APAlerts.error({title: 'Error', message: data.slice(0, data.indexOf('}') + 1)});
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            APAlerts.error({title: 'Error', message: errorThrown});
//                        crearMensaje(errorThrown, 'danger');
                        }
                    });
                },

                cambiarModo: function (modo) {
                    $.ajax({
                        method: 'post',
                        url: '/settings/set-setting',
                        data: {
                            'key': 'cfdi_test_mode',
                            'value': modo
                        },
                        success: function (res) {

                            if(res.success === true) {
                                APAlerts.success(res.message);
                            }else{
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
