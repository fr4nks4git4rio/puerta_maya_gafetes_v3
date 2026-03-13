{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud, ['id' => 'form-solicitud', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::hidden('sgft_anio', settings()->get('anio_impresion', 2021), [
        'class' => 'form-control d-none',
        'placeholder' => '',
    ]) !!}
    {!! Form::hidden('sgft_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}
    {!! Form::hidden('sgft_lcal_id', $local->lcal_id, ['class' => 'form-control d-none', 'placeholder' => '']) !!}


    <div class="row">
        <div class="col-md-8">

            <div class="form-group row">
                {!! Form::label('sgft_local', 'Local', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgft_local', $local->lcal_nombre_comercial, [
                        'class' => 'form-control select2-control',
                        'disabled' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgft_empl_id', 'Empleado', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('sgft_empl_id', $empleados, null, [
                        'class' => 'form-control select2-control filter-control',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgft_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgft_cargo', null, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgft_permisos', 'Clase', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <select class="form-control select2" id="sgft_permisos" data-placeholder="Seleccione"
                        name="sgft_permisos[]" multiple="multiple" required>
                        <option value="PEATONAL" loked selected>PEATONAL</option>
                        @if ($solicitud && in_array('AUTO', $solicitud->sgft_permisos))
                            <option value="AUTO" selected>AUTO</option>
                        @else
                            <option value="AUTO">AUTO</option>
                        @endif
                        @if ($solicitud && in_array('MOTO', $solicitud->sgft_permisos))
                            <option value="MOTO" selected>MOTO</option>
                        @else
                            <option value="MOTO">MOTO</option>
                        @endif
                    </select>

                </div>
            </div>

            <div class="form-group row align-content-center">
                {!! Form::label('sgft_tipo', 'Tipo', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::select('sgft_tipo', ['PRIMERA VEZ' => 'PRIMERA VEZ', 'REPOSICIÓN' => 'REPOSICIÓN'], null, [
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione...',
                    ]) !!}
                </div>
                <div class="col-sm-4 text-right align-content-center">
                    <b class="text-info" id="ph-tarifa"></b>
                </div>
            </div>

            <div class="controles-reposicion d-none">
                <div class="form-group row">
                    {!! Form::label('sgft_gafete_reposicion', 'Gefete a reponer', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::select('sgft_gafete_reposicion', $gafetes_activos, null, [
                            'class' => 'form-control select2-control',
                            'placeholder' => 'Seleccione...',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="form-group row control-gratuito d-none">
                {!! Form::label('sgft_gratuito', '&nbsp;', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-4">
                    <div class="checkbox checkbox-custom">
                        {{-- {{dd($selected)}} --}}
                        {{ Form::checkbox('sgft_gratuito', 1, null, ['id' => 'sgft_gratuito']) }}

                        <label for="{{ 'sgft_gratuito' }}">
                            Gafete gratuito
                        </label>
                    </div>

                </div>
                <div class="col-sm-4 text-right align-content-center">
                </div>
            </div>

            <span class="controles-comprobante {{ $solicitud->sgft_gratuito ?? false == 1 ? 'd-none' : '' }}">
                <div class="row">
                    {!! Form::label('sgft_cpag_id', 'Comprobante', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::select('sgft_cpag_id', $comprobantes, null, [
                            'class' => 'form-control select2-control',
                            'placeholder' => 'Seleccione...',
                        ]) !!}
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="col-sm-4">&nbsp;</div>
                    <div class="col-sm-8">

                        <span class="btn btn-info" id="btn-test">Capturar Comprobante</span>
                        <i class="zmdi zmdi-help text-info" data-toggle="tooltip"
                            title="Si no existe saldo vigente suficiente, debe capturar un nuevo comprobante de pago.">&nbsp;</i>
                    </div>
                </div>
                <br>
            </span>


            <div class="form-group row">
                {!! Form::label('sgft_comentario', 'Comentario', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgft_comentario', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>


        </div>
        <div class="col-md-4">

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-condensed table-active">
                        <tr>
                            <td><b>Saldo</b></td>
                            <td class="text-right">$ {{ number_format($saldos['saldo_vigente'], 2) }}</td>
                        </tr>
                        {{-- <tr> --}}
                        {{-- <td><b>Saldo Virtual</b></td> --}}
                        {{-- <td class="text-right">$ {{number_format($saldos['saldo_virtual'],2)}}</td> --}}
                        {{-- </tr> --}}
                    </table>
                </div>
                <div class="col-sm-12">
                    {{-- <div class="cant_grat_peatonal d-none"> --}}
                    <table class="table table-condensed table-active">
                        <tr>
                            <td colspan="2" class="text-center"><b class="text-info">Gafetes PEATONAL</b></td>
                        </tr>
                        <tr>
                            <td><b>Gratis</b></td>
                            <td class="text-right">
                                {{ $gafetesGratis > 0 ? $gafetesGratis : 'No' }} disponibles</td>
                        </tr>
                    </table>
                    {{-- </div> --}}
                    {{-- <div class="cant_grat_estacionamiento d-none"> --}}
                    <table class="table table-condensed table-active">
                        <tr class="text-center">
                            <td class="text-left"><b class="text-info">Gafetes AUTO</b>
                            </td>
                            <td class="text-right"><b class="text-info">{{ $gafetesAuto > 0 ? $gafetesAuto : 'No' }}
                                    disponibles</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">
                                <b class="text-info">Gafetes MOTO</b>
                            </td>
                            <td class="text-right"><b class="text-info">{{ $gafetesMoto > 0 ? $gafetesMoto : 'No' }}
                                    disponibles</b>
                            </td>
                        </tr>
                    </table>
                    {{-- </div> --}}
                </div>
            </div>

            <div class="foto-wrap">
                <div id="empl-loader" class="text-center mt-2 d-none"><i class="fa fa-gear fa-spin"></i> Cargando...
                </div>
                <img id="img-empleado" src="" width="200px" />
            </div>


        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">
    $(document).ready(function() {

        jModalSolicitud = {
            modal: $('#modal-form'),
            form: '#form-solicitud',
            local_id: {{ auth()->getUser()->Local->lcal_id }},
            acceso_pvez: '{{ "$ " . number_format(settings()->get('gft_acceso_pvez', '0'), 2) . ' MXN' }}',
            acceso_reposicion: '{{ "$ " . number_format(settings()->get('gft_acceso_reposicion', '0'), 2) . ' MXN' }}',
            est_auto_pvez: '{{ "$ " . number_format(settings()->get('gft_est_auto_pvez', '0'), 2) . ' MXN' }}',
            est_auto_reposicion: '{{ "$ " . number_format(settings()->get('gft_est_auto_reposicion', '0'), 2) . ' MXN' }}',
            // est_moto_pvez: '{{ "$ " . number_format(settings()->get('gft_est_moto_pvez', '0'), 2) . ' MXN' }}',
            // est_moto_reposicion: '{{ "$ " . number_format(settings()->get('gft_est_moto_reposicion', '0'), 2) . ' MXN' }}',
            url_new_comprobante: '{{ url('comprobante-pago/form') }}',

            init: function() {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function() {
                    $this.handleSubmit();
                });

                $('[data-toggle="tooltip"]').tooltip();

                setTimeout(() => {
                    $('#sgft_empl_id').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });
                    $('#sgft_permisos').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });
                    $('#sgft_permisos').on('select2:unselecting', function(e) {
                        if (e.params.args.data.id == 'PEATONAL') {
                            e.preventDefault()
                        }
                    })
                }, 300);

                $('#sgft_empl_id').on('change', function() {
                    $this.changeEmpleado();
                });

                if ($('#sgft_empl_id').val() > 0) {
                    $this.changeEmpleado();
                }

                $this.handleSelectComprobantes();

                $('#sgft_tipo, #sgft_permisos').on('change', function() {
                    let tipo = $('#sgft_tipo').val();

                    let permisos = $('#sgft_permisos').val();
                    if (permisos) {
                        if (permisos.includes('AUTO') || permisos.includes('MOTO')) {
                            $('.control-gratuito').addClass('d-none');
                            $('#sgft_gratuito').prop('checked', false).trigger('change');
                            if (tipo == 'REPOSICIÓN') {
                                $('#ph-tarifa').html($this.est_auto_reposicion);
                                $('.controles-reposicion').removeClass('d-none');
                            }
                            if (tipo == 'PRIMERA VEZ') {
                                $('#ph-tarifa').html($this.est_auto_pvez);
                                // $('.control-gratuito').removeClass('d-none');
                                $('.controles-reposicion').addClass('d-none');

                                // $('.cant_grat_peatonal').addClass('d-none');
                                // $('.cant_grat_estacionamiento').removeClass('d-none');

                                $('#sgft_cpag_id').val('');
                            }
                        } else {
                            $('.controles-reposicion').addClass('d-none');
                            if (tipo == 'REPOSICIÓN') {
                                $('#ph-tarifa').html($this.acceso_reposicion);
                                $('.control-gratuito').addClass('d-none');
                                $('#sgft_gratuito').prop('checked', false).trigger('change');
                            }
                            if (tipo == 'PRIMERA VEZ') {
                                $('#ph-tarifa').html($this.acceso_pvez);
                                $('.control-gratuito').removeClass('d-none');

                                // $('.cant_grat_peatonal').removeClass('d-none');
                                // $('.cant_grat_estacionamiento').addClass('d-none');

                                $('#sgft_cpag_id').val('');
                            }
                        }
                    }


                    if (tipo == '' || permisos == '') {
                        $('#ph-tarifa').html('');
                        $('.controles-reposicion').addClass('d-none');
                        $('.control-gratuito').addClass('d-none');
                        $('#sgft_gratuito').prop('checked', false).trigger('change');
                        $('#sgft_cpag_id').val('');
                    }

                });

                $('#btn-test').on('click', function() {
                    $this.openComprobanteForm();
                });

                $('#sgft_gratuito').change(function() {
                    if ($(this).prop('checked') == true) {
                        $('.controles-comprobante').addClass('d-none');
                    } else {
                        $('.controles-comprobante').removeClass('d-none');
                    }

                });

                $("body").off("comprobante:added").on("comprobante:added", function(event) {
                    $this.handleSelectComprobantes();
                });

            },

            handleSelectComprobantes: function() {
                let $this = this;

                //Pedimos el array completo de los comprobantes para inicializar el select2
                $.ajax({
                    url: "{!! url('comprobante-pago/get-select-options-gafete') !!}/" + $this.local_id,
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        // res.unshift({id:"",text:""});
                        console.log(res);
                        $("#sgft_cpag_id").select2({
                            data: res,
                            width: '100%',
                            allowClear: true,
                            templateResult: function(d) {
                                return $(d.result_template);
                            },
                            // templateSelection: function (d) { return $(d.text); },
                            placeholder: 'Seleccione...'
                        });

                        $("#sgft_cpag_id").val("").trigger('select2:change');

                    }

                });

            },


            openComprobanteForm: function() {

                let $this = this;

                // if($ ('#sgft_tipo').val() == "" )
                // {
                //   APAlerts.warning('ELIJA UN TIPO DE SOLICITUD PRIMERO');
                //   return;
                // }

                APModal.open({
                    dom: 'modal-comprobante',
                    title: 'Capturar comprobante de pago',
                    url: $this.url_new_comprobante,
                    size: 'modal-md'
                });


            },

            changeEmpleado: function() {
                let $this = this;
                let empl_id = $('#sgft_empl_id').val();

                $('#sgft_cargo').val('');
                $('#img-empleado').attr('src', '');

                if (empl_id != "") {
                    let url = '{{ url('empleado/get-json') }}/' + empl_id;

                    $('#empl-loader').removeClass('d-none');
                    $('#img-empleado').attr('src', '');

                    $.getJSON(url, function(data) {
                        $('#empl-loader').addClass('d-none');

                        $('#sgft_cargo').val(data.cargo.crgo_descripcion);
                        $('#img-empleado').attr('src', data.empl_foto_web);

                    });
                }


            },


            handleSubmit: function() {

                let $this = this;
                let url = $($this.form).attr('action');

                let form = $($this.form)[0];

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(form),
                    // data: $($this.form).serialize(),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('.input-error').remove();
                    },
                    success: function(res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            //$('body').trigger('vivienda:added');
                            $('#modal-btn-close').click();

                        } else {

                            if (typeof res.message !== "undefined") {
                                APAlerts.error(res.message);
                                handleFormErrors($this.form, res.errors);
                            } else {
                                APAlerts.error(res);
                            }

                        }
                    }
                });

            }
        };

        jModalSolicitud.init();

    });
</script>
