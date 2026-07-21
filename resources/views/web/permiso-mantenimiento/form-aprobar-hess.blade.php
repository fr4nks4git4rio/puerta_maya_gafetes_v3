{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container p-0">

    {!! Form::model($permiso, ['id' => 'form-permiso', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::text('pmtt_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#generales" role="tab">Generales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#analisis-riesgo" role="tab">Análisis de Riesgo</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="generales" role="tabpanel">
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

                    <div class="form-group row">
                        {!! Form::label('pmtt_tipo_actividad', 'Tipo de actividad', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::select(
                                'pmtt_tipo_actividad',
                                ['' => '', 'RUTINARIA' => 'RUTINARIA', 'NO RUTINARIA' => 'NO RUTINARIA'],
                                null,
                                ['class' => 'form-control']
                            ) !!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('pmtt_comentario_admon', 'Comentario de validación mantenimiento', [
                            'class' => 'col-sm-4 control-label',
                        ]) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('pmtt_comentario_admon', null, [
                                'class' => 'form-control',
                                'size' => '50x2',
                                'readonly' => true,
                            ]) !!}
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
                        {!! Form::label('pmtt_comentario_hess', 'Comentario de validación HESS', ['class' => 'col-sm-4 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::textarea('pmtt_comentario_hess', null, ['class' => 'form-control', 'size' => '50x2']) !!}
                        </div>

                    </div>

                    <p class="alert alert-info">Esta acción marcará el permiso como <b>APROBADO</b></p>
                    {{--                <p class="alert alert-info">Varifcacion de permiso</p> --}}

                </div>
            </div>
        </div>
        <div class="tab-pane" id="analisis-riesgo" role="tabpanel">
            <div class="form-group row">
                {!! Form::label('actividades_alto_riesgo', 'Actividades de Alto Riesgo', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('actividades_alto_riesgo', $actividadesAltoRiesgo, null, [
                        'class' => 'form-control select2-control',
                        'name' => 'actividades_alto_riesgo[]',
                        'multiple' => 'multiple',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>
            <div id="otra_actividad_div" class="form-group row d-none">
                {!! Form::label('pmtt_otra_actividad_riesgo', 'Otra actividad', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_otra_actividad_riesgo', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('riesgos_asociados', 'Riesgos Asociados', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('riesgos_asociados', $riesgosAsociados, null, [
                        'class' => 'form-control select2-control',
                        'name' => 'riesgos_asociados[]',
                        'multiple' => 'multiple',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('medidas_control_riesgo', 'Medidas de Control de Riesgo', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('medidas_control_riesgo', $medidasControlRiesgo, null, [
                        'class' => 'form-control select2-control',
                        'name' => 'medidas_control_riesgo[]',
                        'multiple' => 'multiple',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>
            <div id="dispositivo_bloquear_div" class="form-group row d-none">
                {!! Form::label('pmtt_dispositivo_bloquear', 'Dispositivo a bloquear', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_dispositivo_bloquear', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('equipos_herramientas', 'Equipos y Herramientas', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('equipos_herramientas', $equiposHerramientas, null, [
                        'class' => 'form-control select2-control',
                        'name' => 'equipos_herramientas[]',
                        'multiple' => 'multiple',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>
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

                setTimeout(() => {
                    $('.select2-control').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });
                    $('#actividades_alto_riesgo').on('select2:selecting', function(e) {
                        if (e.params.args.data.id == 7) {
                            $('#otra_actividad_div')[0].classList.remove('d-none')
                        }
                    });
                    $('#actividades_alto_riesgo').on('select2:unselecting', function(e) {
                        if (e.params.args.data.id == 7) {
                            $('#pmtt_otra_actividad_riesgo').val('');
                            $('#otra_actividad_div')[0].classList.add('d-none')
                        }
                    });
                    $('#medidas_control_riesgo').on('select2:selecting', function(e) {
                        if (e.params.args.data.id == 2) {
                            $('#dispositivo_bloquear_div')[0].classList.remove('d-none')
                        }
                    });
                    $('#actividades_alto_riesgo').on('select2:unselecting', function(e) {
                        if (e.params.args.data.id == 2) {
                            $('#pmtt_dispositivo_bloquear').val('');
                            $('#dispositivo_bloquear_div')[0].classList.add('d-none')
                        }
                    });
                }, 300);

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
