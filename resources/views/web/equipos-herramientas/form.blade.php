<div class="container">

    {!! Form::model($equipoHerramienta,['id' => 'form-equipo-herramienta','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('eqher_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('eqher_nombre', 'Nombre', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('eqher_nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('eqher_comentarios', 'Comentarios', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('eqher_comentarios', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-equipo-herramienta',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });
            },

            handleSubmit: function () {

                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $($this.form).serialize(),
                    beforeSend: function () {
                        $('.input-error').remove();
                    },
                    success: function (res) {

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

        jModal.init();

    });

</script>
