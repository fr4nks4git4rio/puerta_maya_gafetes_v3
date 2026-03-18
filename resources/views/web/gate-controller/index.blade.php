@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-settings">&nbsp;</span>Administrador de controladora de puertas
@endsection

@section('content')
    <style>
        .waiting-cursor {
            cursor: wait;
        }

        .pagination {
            justify-content: center !important;
        }
    </style>

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">

                    <!--Nav tabs-->
                    <ul class="nav nav-tabs mt-2">
                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-tarjetas" aria-expanded="true"
                                class="nav-link show text-center @if(!$filtros['search_logs_acceso']) active @endif" id="nav-tarjetas">
                                <b>Tarjetas</b></a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-puertas" aria-expanded="false"
                                class="nav-link show text-center" id="nav-puertas">
                                <b>Puertas</b></a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-accesos" aria-expanded="false"
                                class="nav-link show text-center @if($filtros['search_logs_acceso']) active @endif" id="nav-accesos">
                                <b>Log de acceso</b></a>
                        </li>

                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-interacciones" aria-expanded="false"
                                class="nav-link show text-center" id="nav-interacciones">
                                <b>Log de interacciones</b></a>
                        </li>

                    </ul>

                    <!--Tabs Content-->
                    <div class="tab-content">

                        <!--Tab 1-->

                        <div id="lft-tab-tarjetas" class="tab-pane show @if(!$filtros['search_logs_acceso']) active @endif">
                            <div class="row">
                                <div class="col-sm-2 d-inline-flex mb-1">
                                    <label for="" class="mt-1">
                                        Por Página:
                                        <select name="perPageTarjeta" id="perPageTarjeta" class="form-control-sm">
                                            <option value="10" @if ($filtros['perPageTarjeta'] == 10) selected @endif>
                                                10
                                            </option>
                                            <option value="25" @if ($filtros['perPageTarjeta'] == 25) selected @endif>
                                                25
                                            </option>
                                            <option value="50" @if ($filtros['perPageTarjeta'] == 50) selected @endif>
                                                50
                                            </option>
                                            <option value="100" @if ($filtros['perPageTarjeta'] == 100) selected @endif>
                                                100
                                            </option>
                                            <option value="200" @if ($filtros['perPageTarjeta'] == 200) selected @endif>
                                                200
                                            </option>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-sm-2 d-inline-flex mb-1 offset-5">
                                    <label for="" class="mt-1">
                                        Tarjetas Vigentes:
                                        <input type="checkbox" @if ($filtros['tarjetasVigentes']) checked @endif
                                            id="checkbox-tarjetas-vigentes">
                                    </label>
                                </div>
                                <div class="col-sm-3 d-inline-flex mb-1">
                                    <label for="" class="mt-1">Filtrar:</label>
                                    <input type="text" class="form-control-sm w-100"
                                        value="{{ $filtros['search_tarjeta'] }}" id="input-search-tarjetas">
                                </div>
                                <div class="col-sm-4 text-right ml-auto mb-2" id="crear-autorizar-div">
                                    <span class="btn btn-primary disabled" id="btn-create-card-lote"> <i
                                            class="fa fa-chain"></i> Crear & Autorizar por Lotes</span>
                                </div>
                            </div>
                            <table class="table table-bordered" id="table-tarjetas">
                                <tr class="bg-light">
                                    <th>Sel. <input type="checkbox" id="select-all"></th>
                                    <th>Numero RFID</th>
                                    <th>Local ID</th>
                                    <th>Local Nombre Comercial</th>
                                    <th>Local Razón Social</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>PIN</th>
                                    <th>PUERTAS</th>
                                    <th>Activado</th>
                                    <th>Desactivado</th>
                                    <th><i class="fa fa-gear"></i></th>
                                </tr>

                                @foreach ($tarjetas as $t)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-card" data-pin="{{ $t->referencia }}">
                                        </td>
                                        <td>{{ $t->numero_rfid }}</td>
                                        <td>{{ $t->lcal_id }}</td>
                                        <td>{{ $t->lcal_razon_social }}</td>
                                        <td>{{ $t->lcal_nombre_comercial }}</td>
                                        <td>{{ $t->nombre }}</td>
                                        <td>{{ $t->tipo }}</td>
                                        <td>{{ $t->referencia }}</td>
                                        <td>{{ $t->puertas }}</td>
                                        <td>{{ $t->activated_at }}</td>
                                        <td>{{ $t->disabled_at }}</td>
                                        <td>
                                            <span class="btn-group">
                                                <span class="btn btn-sm btn-primary btn-create-card mr-2"
                                                    id="btn-create-card-{{ $t->numero_rfid }}" title="Crear & Autorizar"
                                                    data-number="{{ $t->numero_rfid }}" data-pin="{{ $t->referencia }}">
                                                    <i class="fa fa-check"></i></span>
                                                {{-- <span class="btn btn-sm btn-primary btn-authorize-card mr-2" --}}
                                                {{-- title="Activar & Autorizar" data-number="{{$t->numero_rfid}}" --}}
                                                {{-- data-pin="{{$t->referencia}}"> --}}
                                                {{-- <i class="fa fa-check"></i></span> --}}
                                                <span class="btn btn-sm btn-primary btn-deauthorize-card" title="Desactivar"
                                                    data-number="{{ $t->numero_rfid }}" data-pin="{{ $t->referencia }}">
                                                    <i class="fa fa-times"></i></span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                            {{ $tarjetas->links() }}
                        </div>
                        <!--EndTab 1-->

                        <!--Tab Puertas-->
                        <div id="lft-tab-puertas" class="tab-pane show">
                            {{-- <p class="text-main text-semibold">Roles y Permisos</p> --}}


                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-bordered" id="table-puertas">
                                        <tr class="bg-light">
                                            <th>ID</th>
                                            {{-- <th>PIN</th> --}}
                                            <th>NUMERO</th>
                                            {{-- <th>AUTHPIN</th> --}}
                                            <th>DIRECCION</th>
                                            <th>NOMBRE</th>
                                            <th>TIPO</th>
                                            <th>OBSERVACIONES</th>
                                            <th><i class="fa fa-gear"></i></th>
                                        </tr>

                                        @foreach ($puertas as $p)
                                            @if ($p->door_numero)
                                                <tr>
                                                    <td>{{ $p->door_id }}</td>
                                                    {{-- <td>{{$p->door_pin_id}}</td> --}}
                                                    <td>{{ $p->door_numero }}</td>
                                                    {{-- <td>{{$p->Pin->pin_value}}</td> --}}
                                                    <td>{{ $p->door_direccion }}</td>
                                                    <td>{{ $p->door_nombre }}</td>
                                                    <td>{{ $p->door_tipo }}</td>
                                                    <td>{{ $p->door_observaciones }}</td>
                                                    <td>
                                                        <span class="btn-group">
                                                            <span class="btn btn-sm btn-primary btn-door"
                                                                title="Abrir Puerta" data-id="{{ $p->door_id }}">
                                                                <i class="fa fa-unlock"></i></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                    </table>

                                </div>

                            </div>


                        </div>
                        <!--End Tab Puertas-->


                        <!--Tab Accesos-->
                        <div id="lft-tab-accesos" class="tab-pane show @if($filtros['search_logs_acceso']) active @endif">
                            {{-- <p class="text-main text-semibold">Roles y Permisos</p> --}}

                            <div class="row" style="justify-content: end">
                                <div class="col-sm-4 d-inline-flex mb-1">
                                    <label for="" class="mt-1">Filtrar:</label>
                                    <input type="text" class="form-control-sm w-100"
                                        value="{{ $filtros['search_logs_acceso'] }}" id="input-search-logs-accesos">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8">
                                    <b class="text-primary">LOG DE ACCESOS DE LOS
                                        ÚLTIMOS {{ settings()->get('admin_alog_days', 3) }} DIAS</b>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <span class="btn btn-primary" id="btn-get-log"> <i class="fa fa-bolt"></i> PULL DE
                                        ACCESOS</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">

                                <div class="col-sm-12">

                                    <table class="table table-bordered table-condensed" id="table-accesos">
                                        <tr class="bg-light">
                                            <th>ID</th>
                                            <th>NUMBER</th>
                                            <th>PUERTA</th>
                                            <th>CONTROLADORA</th>
                                            <th>TIPO</th>
                                            <th>FECHA - HORA</th>
                                            <th>NOMBRE</th>
                                            <th>CLASE</th>
                                        </tr>

                                        @foreach ($accesos as $a)
                                            <tr>
                                                <td>{{ $a->lgac_id }}</td>
                                                <td>{{ $a->lgac_card_number }}</td>

                                                <td>{{ $a->lgac_puerta }}</td>
                                                <td>{{ $a->lgac_controladora }}</td>
                                                <td>{{ $a->lgac_tipo }}</td>
                                                <td>{{ $a->lgac_created_at }}</td>
                                                <td>{{ $a->nombre }}</td>
                                                <td>{{ strtoupper($a->tipo) }}</td>
                                            </tr>
                                        @endforeach

                                    </table>

                                </div>

                            </div>


                        </div>
                        <!--End Tab Accesos-->


                        <!--Tab Interacciones-->
                        <div id="lft-tab-interacciones" class="tab-pane show">
                            {{-- <p class="text-main text-semibold">Roles y Permisos</p> --}}

                            <div class="row">
                                <div class="col-sm-8">
                                    <b class="text-primary">ÚLTIMAS 500 INTERACCIONES CON CONTROLADORA</b>
                                </div>
                                {{--                            <div class="col-sm-4 text-right"> --}}
                                {{--                                <span class="btn btn-primary" id="btn-get-log"> <i class="fa fa-bolt"></i> PULL DE ACCESOS</span> --}}
                                {{--                            </div> --}}
                            </div>
                            <br>
                            <div class="row">

                                <div class="col-sm-12">

                                    <table class="table table-bordered table-condensed" id="table-interacciones">
                                        <tr class="bg-light">
                                            <th>ID</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>IP</th>
                                            <th>ENDPOINT</th>
                                            <th>ESTADO</th>
                                            <th>MENSAJE</th>
                                            <th>PETICIÓN</th>
                                            <th>RESPUESTA</th>
                                        </tr>

                                        @foreach ($interacciones as $i)
                                            <tr>
                                                <td>{{ $i->dclg_id }}</td>
                                                <td>{{ $i->dclg_description }}</td>
                                                <td>{{ $i->dclg_controller_ip }}</td>
                                                <td>{{ $i->dclg_end_point }}</td>
                                                <td>{{ $i->dclg_response_status_code }}</td>
                                                <td>
                                                    <small class="text-primary">{!! nl2br($i->dclg_response_message) !!}</small>
                                                </td>
                                                <td>{{ $i->dclg_created_at }}</td>
                                                <td>{{ $i->dclg_updated_at }}</td>
                                            </tr>
                                        @endforeach

                                    </table>

                                </div>

                            </div>


                        </div>
                        <!--End Tab Interacciones-->

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            jGateController = {

                modal_dom: 'modal-form',
                url_get_access_log: '{{ url('gate-controller/get-access-log') }}',
                url_open_door: '{{ url('gate-controller/open-door') }}',
                url_open_door_v3: '{{ url('gate-controller/open-door-v3') }}',
                url_create_card: '{{ url('gate-controller/create-card') }}',
                url_create_card_lote: '{{ url('gate-controller/create-card-lote') }}',
                url_authorize_card: '{{ url('gate-controller/authorize-card') }}',
                url_deauthorize_card: '{{ url('gate-controller/deauthorize-card') }}',
                url_filtrar_tarjetas: '{{ url('gate-controller') }}',
                cards_selected: [],

                init: function() {
                    let $this = this;

                    $('#btn-get-log').off().on('click', function() {
                        $this.getLogs();
                    });

                    $('.btn-door').off().on('click', function() {
                        let pin = $(this).data('pin');
                        let id = $(this).data('id');

                        // $this.openDoor(pin);
                        $this.openDoorV3(id);
                    });

                    $('.btn-authorize-card').off().on('click', function() {
                        let pin = $(this).data('pin');
                        let number = $(this).data('number');

                        $this.authorizeCard(pin, number);
                    });

                    $('.btn-create-card').off().on('click', function() {
                        let pin = $(this).data('pin');
                        let number = $(this).data('number');

                        $this.createCard(pin, number);
                    });

                    $('#btn-create-card-lote').off().on('click', function(event) {
                        if ($this.cards_selected.length > 0) {
                            let pines = [];
                            $('.checkbox-card').each(function(index, element) {
                                if (element.checked) {
                                    pines.push($(element).data('pin'));
                                }
                            })
                            $this.createCardLote(pines);
                        }
                    });

                    $('.btn-deauthorize-card').off().on('click', function() {
                        let pin = $(this).data('pin');
                        let number = $(this).data('number');

                        $this.deauthorizeCard(pin, number);
                    });

                    $('#input-search-tarjetas').off().on('keyup', function(event) {
                        if (event.key === 'Enter') {
                            let checked = document.getElementById('checkbox-tarjetas-vigentes')
                                .checked ? 1 : '';
                            let perPage = $('#perPageTarjeta').val();
                            window.location.href = "/gate-controller?search_tarjeta=" + $(this)
                                .val() + "&tarjetasVigentes=" + checked + "&perPageTarjeta=" +
                                perPage;
                        }
                    });

                    $('#input-search-logs-accesos').off().on('keyup', function(event) {
                        if (event.key === 'Enter') {
                            window.location.href = "/gate-controller?search_logs_acceso=" + $(this)
                                .val();
                        }
                    });

                    $('#checkbox-tarjetas-vigentes').off().on('click', function(event) {
                        let checked = event.target.checked ? 1 : '';
                        let perPage = $('#perPageTarjeta').val();
                        window.location.href = "/gate-controller?search_tarjeta=" + $(
                                '#input-search-tarjetas').val() + "&tarjetasVigentes=" + checked +
                            "&perPageTarjeta=" + perPage;
                    });

                    $('#perPageTarjeta').off().on('change', function(event) {
                        let checked = document.getElementById('checkbox-tarjetas-vigentes')
                            .checked ? 1 : '';
                        let perPage = event.target.value;
                        window.location.href = "/gate-controller?search_tarjeta=" + $(
                                '#input-search-tarjetas').val() + "&tarjetasVigentes=" + checked +
                            "&perPageTarjeta=" + perPage;
                    });

                    $('.checkbox-card').off().on('click', function(event) {
                        let pin = $(this).data('pin');
                        if (event.target.checked) {
                            $this.cards_selected.push(pin);
                        } else {
                            $this.cards_selected = $this.cards_selected.filter(function(element) {
                                return element != pin;
                            })
                        }
                        if ($this.cards_selected.length > 0) {
                            document.getElementById('btn-create-card-lote').className = document
                                .getElementById('btn-create-card-lote').className.replace(
                                    ' disabled', '');
                        } else {
                            document.getElementById('btn-create-card-lote').className = document
                                .getElementById('btn-create-card-lote').className + ' disabled';
                        }
                    });
                    $('#select-all').off().on('click', function(event) {
                        $('.checkbox-card').each(function(index, element) {
                            element.checked = event.target.checked;

                            let pin = $(element).data('pin');
                            if (element.checked) {
                                $this.cards_selected.push(pin);
                            } else {
                                $this.cards_selected = $this.cards_selected.filter(function(
                                    element) {
                                    return element != pin;
                                })
                            }

                        });
                        if ($this.cards_selected.length > 0) {
                            document.getElementById('btn-create-card-lote').className = document
                                .getElementById('btn-create-card-lote').className.replace(
                                    ' disabled', '');
                        } else {
                            document.getElementById('btn-create-card-lote').className = document
                                .getElementById('btn-create-card-lote').className + ' disabled';
                        }
                    });
                },

                getLogs: function() {

                    let $this = this;

                    // let PostData = {
                    //     key: key,
                    //     value: $('#'+key).val()
                    // };

                    $.ajax({
                        url: $this.url_get_access_log,
                        method: 'POST',
                        // data: PostData,
                        beforeSend: function() {
                            $('body').addClass('waiting-cursor');
                        },
                        success: function(res) {

                            console.log(res);
                            // Object.keys(res.data).forEach(function (element, index) {
                            //     if (res.data[element].success === true) {
                            //         APAlerts.success(res.data[element].output);
                            //     } else {
                            //         APAlerts.error(res.data[element].output);
                            //     }
                            // });

                            //                            for(let i = 0; i<Object.data()res.data.length; i++){
                            //                                console.log(i);
                            //                            }
                            if (res.success === true) {
                                APAlerts.success(res.message);
                                setTimeout(() => {
                                    window.location.href = '/gate-controller';
                                }, 1000);
                            } else {
                                APAlerts.error(res.message);
                            }
                        },
                        complete: function() {
                            $('body').removeClass('waiting-cursor');
                        }
                    });

                },

                openDoor: function(doorPin) {

                    let $this = this;

                    let PostData = {
                        door_pin: doorPin,
                    };

                    $.ajax({
                        url: $this.url_open_door,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }
                        }
                    });

                },

                openDoorV3: function(id) {

                    let $this = this;

                    let PostData = {
                        door_id: id,
                    };

                    $.ajax({
                        url: $this.url_open_door_v3,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }
                        }
                    });

                },

                createCard: function(cardPin, cardNumber) {

                    $('#btn-create-card-' + cardNumber).html('<i class="fa fa-spin fa-spinner"></i>');

                    let $this = this;

                    let PostData = {
                        card_pin: cardPin,
                        card_number: cardNumber,
                    };

                    $.ajax({
                        url: $this.url_create_card,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }
                            $('#btn-create-card-' + cardNumber).html(
                                '<i class="fa fa-check"></i>');
                        }
                    });

                },

                createCardLote: function(pines) {

                    $('#crear-autorizar-div').html(
                        '<span class="btn btn-primary disabled"> <i class="fa fa-spin fa-spinner"></i> Creando y Autorizando</span>'
                        )

                    let $this = this;

                    let PostData = {
                        'pines[]': pines
                    };

                    $.ajax({
                        url: $this.url_create_card_lote,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }

                            $('#crear-autorizar-div').html(
                                '<span class="btn btn-primary" id="btn-create-card-lote"> <i class="fa fa-chain"></i> Crear & Autorizar por Lotes</span>'
                                );
                        }
                    });

                },

                authorizeCard: function(cardPin, cardNumber) {

                    let $this = this;

                    let PostData = {
                        card_pin: cardPin,
                        card_number: cardNumber,
                    };

                    $.ajax({
                        url: $this.url_authorize_card,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }
                        }
                    });

                },

                deauthorizeCard: function(cardPin, cardNumber) {

                    let $this = this;

                    let PostData = {
                        card_pin: cardPin,
                        card_number: cardNumber,
                    };

                    $.ajax({
                        url: $this.url_deauthorize_card,
                        method: 'POST',
                        data: PostData,
                        success: function(res) {

                            console.log(res);

                            if (res.success === true) {
                                APAlerts.success(res.message);
                            } else {
                                APAlerts.error(res.message);
                            }
                        }
                    });

                },


                printRoles: function() {
                    let $this = this;
                    let url = '{{ url('settings/roles-view') }}';
                    $('#roles-container').html(ajaxLoader());
                    ajax_update(url, '#roles-container');

                },


                handleSetSetting: function() {
                    let $this = this;
                    {{--                let url = '{{$url}}'; --}}

                    $('.btn-save-setting').off().on('click', function() {
                        let key = $(this).data('setting_key');

                        let PostData = {
                            key: key,
                            value: $('#' + key).val()
                        };

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: PostData,
                            success: function(res) {
                                if (res.success === true) {
                                    APAlerts.success(res.message);
                                } else {
                                    APAlerts.error(res.message);
                                }
                            }
                        });


                    });

                }

            };

            jGateController.init();

        });
    </script>
@endsection
