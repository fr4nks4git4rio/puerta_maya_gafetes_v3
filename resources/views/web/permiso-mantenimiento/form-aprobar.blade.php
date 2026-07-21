{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($permiso, ['id' => 'form-permiso', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::text('pmtt_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}

    <div class="row">
        <div class="col-md-6">

            <div class="form-group row">
                {!! Form::label('local', 'Local', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('local_numero', auth()->user()->Local->lcal_id, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
                <div class="col-sm-5">
                    {!! Form::text('local', auth()->user()->Local->lcal_nombre_comercial, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_fecha', 'Fecha Solcitud', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_fecha', date('Y-m-d'), [
                        'class' => 'form-control',
                        'placeholder' => '',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>


            <div class="form-group row">
                {!! Form::label('pmtt_solicitante', 'Nombre Solicitante', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_solicitante', null, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
            </div>


            <div class="form-group row">
                {!! Form::label('pmtt_empresa', 'Empresa', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_empresa', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('pmtt_representante', 'Representante', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_representante', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_listado_trabajadores', 'Listado de trabajadores', ['class' => 'control-label']) !!}
                <table class="table table-striped table-condensed table-sm" id="listado_trabajadores">
                    <thead>
                        <tr>
                            <th>Trabajador</th>
                            {{-- <th>Número de seguro social</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permiso->Trabajadores as $trabajador)
                            <tr>
                                <td>{{ $trabajador->pmtb_nombre }}</td>
                                {{-- <td>{{ $trabajador->pmtb_nss }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_trabajo', 'Descripción de las actividades a realizar', [
                    'class' => 'col-sm-4 control-label',
                ]) !!}
                <div class="col-sm-8">
                    {!! Form::textarea('pmtt_trabajo', null, ['class' => 'form-control', 'size' => '50x2', 'readonly' => true]) !!}
                </div>
            </div>


        </div>
        <div class="col-md-6">

            <div class="form-group row">
                {!! Form::label('pmtt_vigencia_inicial', 'Fecha Inicio', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_vigencia_inicial', null, [
                        'class' => 'form-control',
                        'placeholder' => '',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_dias', 'Dias Solicitados', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_dias', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                </div>
            </div>


            <div class="form-group row">
                {!! Form::label('pmtt_vigencia_final', 'Fecha Fin', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_vigencia_final', null, [
                        'class' => 'form-control',
                        'disabled' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_tmtt_id', 'Tipo de Mantenimiento', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_tmtt_id', $permiso->TipoMantenimiento->tmtt_nombre, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('pmtt_tesp_id', 'Trabajo Específico', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_tmtt_id', $permiso->TrabajoEspecifico->tesp_nombre, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                    <div class="alert alert-info mb-1">
                        <b>EPP Básicos:</b>
                        {{ join(
                            ', ',
                            $permiso->TrabajoEspecifico->EppBasicos()->get()->map(function ($element) {
                                    return $element->eppb_nombre;
                                })->toArray(),
                        ) }}
                        <br>
                        @if ($permiso->TrabajoEspecifico->EppEspecificos()->exists())
                            <b>EPP Específicos:</b>
                            {{ join(
                                ', ',
                                $permiso->TrabajoEspecifico->EppEspecificos()->get()->map(function ($element) {
                                        return $element->eppe_nombre;
                                    })->toArray(),
                            ) }}
                            <br>
                        @endif
                    </div>
                    <div class="alert alert-danger mb-0">
                        <b>Tipo Riesgo:</b> {{ $permiso->TrabajoEspecifico->TipoRiesgo->trgo_nombre }}
                    </div>
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_estado', 'Estado', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_estado', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_comentario_admon', 'Comentario de validación', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::textarea('pmtt_comentario_admon', null, ['class' => 'form-control', 'size' => '50x2']) !!}
                </div>

            </div>

            <p class="alert alert-info">Esta acción marcará el permiso como <b>APROBADO</b></p>
            {{--                <p class="alert alert-info">Varifcacion de permiso</p> --}}

        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">
    $(document).ready(function() {

        jModal = {
            modal: $('#modal-form'),
            form: '#form-permiso',

            init: function() {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function() {
                    $this.handleSubmit();
                });

            },


            handleSubmit: function() {

                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    // data: new FormData(form),
                    data: $($this.form).serialize(),
                    // contentType: false,
                    // cache: false,
                    // processData:false,
                    beforeSend: function() {
                        $('.input-error').remove();
                    },
                    success: function(res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
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

        jModal.init();

    });
</script>
