{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud, ['id' => 'form-solicitud', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::hidden('sgftre_anio', settings()->get('anio_impresion', date('Y')), [
        'class' => 'form-control d-none',
        'placeholder' => '',
    ]) !!}
    {!! Form::hidden('sgftre_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}
    {!! Form::hidden('sgftre_lcal_id', $local->lcal_id, ['class' => 'form-control d-none', 'placeholder' => '']) !!}
    {!! Form::hidden('sgftre_empl_id', $empleado->empl_id, ['class' => 'form-control d-none', 'placeholder' => '']) !!}
    {!! Form::hidden('sgftre_sgft_id', $empleado->GafeteAcceso()->sgft_id, [
        'class' => 'form-control d-none',
        'placeholder' => '',
    ]) !!}


    <div class="row">
        <div class="col-md-8">

            <div class="form-group row">
                {!! Form::label('sgftre_local', 'Local', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_local', $local->lcal_nombre_comercial, [
                        'class' => 'form-control select2-control',
                        'disabled' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftre_empleado', 'Empleado', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_empleado', $empleado->empl_nombre, [
                        'class' => 'form-control select2-control',
                        'disabled' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftre_gafete', 'Gafete de Acceso', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_gafete', $empleado->GafeteAcceso()->sgft_numero, [
                        'class' => 'form-control select2-control',
                        'disabled' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftre_permisos', 'Clase', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <select class="form-control select2" id="sgftre_permisos" data-placeholder="Seleccione"
                        name="sgftre_permisos[]" multiple="multiple" required>
                        <option value="PEATONAL" loked selected>PEATONAL</option>
                        @if ($solicitud && in_array('AUTO', $solicitud->sgftre_permisos))
                            <option value="AUTO" selected>AUTO</option>
                        @else
                            <option value="AUTO">AUTO</option>
                        @endif
                        @if ($solicitud && in_array('MOTO', $solicitud->sgftre_permisos))
                            <option value="MOTO" selected>MOTO</option>
                        @else
                            <option value="MOTO">MOTO</option>
                        @endif
                    </select>

                </div>
            </div>
            @if ($solicitud->sgftre_estado == 'DENEGADO')
                <div class="form-group row">
                    {!! Form::label('sgftre_estado', 'Estado', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                       {!! Form::text('sgftre_estado', $solicitud->sgftre_estado, [
                        'class' => 'form-control text-danger',
                        'readonly' => true,
                    ]) !!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('sgftre_comentarios_rechazo', 'Comentarios de rechazo', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        <textarea name="sgftre_comentarios_rechazo" id="sgftre_comentarios_rechazo" class="form-control" rows="4"
                            readonly="true">{{ $solicitud->sgftre_comentarios_rechazo }}</textarea>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-4">

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-condensed table-active">
                        <tr>
                            <td><b>Gafetes AUTO</b></td>
                            <td class="text-right">
                                {{ $gafetesAuto > 0 ? $gafetesAuto : 'No' }} disponibles</td>
                        </tr>
                        <tr>
                            <td><b>Gafetes MOTO</b></td>
                            <td class="text-right">
                                {{ $gafetesMoto > 0 ? $gafetesMoto : 'No' }} disponibles</td>
                        </tr>
                    </table>
                </div>
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

            init: function() {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function() {
                    $this.handleSubmit();
                });

                $('[data-toggle="tooltip"]').tooltip();

                setTimeout(() => {
                    $('#sgftre_permisos').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });
                    $('#sgftre_permisos').on('select2:unselecting', function(e) {
                        if (e.params.args.data.id == 'PEATONAL') {
                            e.preventDefault()
                        }
                    })
                }, 300);

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
