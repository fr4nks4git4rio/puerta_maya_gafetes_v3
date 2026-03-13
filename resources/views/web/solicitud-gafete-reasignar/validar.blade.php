{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud, ['id' => 'form-solicitud', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::text('sgftre_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}

    <div class="row">
        <div class="col-md-8">

            <div class="form-group row">
                {!! Form::label('sgftre_local', 'Local', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_local', $solicitud->Local->lcal_nombre_comercial, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftre_nombre', 'Nombre', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_nombre', $solicitud->Empleado->empl_nombre, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftrere_cargo', 'Cargo', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_cargo', $solicitud->Empleado->Cargo->crgo_descripcion, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgft_numero', 'Número Tarjeta', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgft_numero', $solicitud->Gafete->sgft_numero, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
            </div>


            <div class="form-group row">
                <p class="alert alert-info text-center">
                    Esta acción le otorgará los permisos de
                    <b>{{ str_replace_last(',', ' y ', $solicitud->sgftre_permisos) }}</b> al empleado.
                </p>
            </div>

        </div>
        <div class="col-md-4">

            <div class="foto-wrap">
                <div id="empl-loader" class="text-center mt-2 d-none"><i class="fa fa-gear fa-spin"></i> Cargando...
                </div>
                <img id="img-empleado" src="{{ $solicitud->Empleado->empl_foto_web }}" width="200px" />
            </div>


        </div>
    </div>
    <div class="row">
        <label for="">Puertas con Acceso:</label>
        <div class="col-sm-12">
            <div class="row">
                @foreach ($puertas as $puerta)
                    <div class="col-sm-4 form-group" {{-- @if ($solicitud->Local->Acceso->cacs_id !== 1 && $puerta->door_id != 5) hidden @endif --}}>
                        <input type="checkbox" name="puerta_{{ $puerta->door_id }}"
                            @if ($puerta->door_tipo === 'PEATONAL' || str_contains($solicitud->sgftre_permisos, $puerta->door_tipo)) checked @endif>
                        {{ $puerta->door_nombre }} <br>
                        <strong>Tipo:</strong> {{ $puerta->door_tipo }} <br>
                        <strong>Dirección:</strong> {{ $puerta->door_direccion }} <br>
                        <strong>Controladora:</strong> {{ $puerta->Controladora->ctrl_nombre }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">
    $(document).ready(function() {

        jModal = {
            modal: $('#modal-form'),
            form: '#form-solicitud',

            init: function() {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function() {
                    $this.handleSubmit();
                });


            },


            handleSubmit: function() {

                let $this = this;
                let url = $($this.form).attr('action');

                let form = $($this.form)[0];

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(form),
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
