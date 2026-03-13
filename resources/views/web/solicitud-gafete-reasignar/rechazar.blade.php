{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($solicitud, ['id' => 'form-rechazar', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::text('sgftre_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group row">
                {!! Form::label('sgftre_empleado', 'Empleado', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('sgftre_empleado', $solicitud->Empleado->empl_nombre, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('sgftre_comentarios_rechazo', 'Comentarios', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    <textarea name="sgftre_comentarios_rechazo" id="sgftre_comentarios_rechazo" class="form-control" rows="4"></textarea>
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
            form: '#form-rechazar',

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
