<div class="container">

    {!! Form::model($tipoRiesgo,['id' => 'form-tipo-riesgo','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('trgo_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('trgo_nombre', 'Nombre', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('trgo_nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('trgo_requiere_analisis', 'Requiere análisis', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::checkbox('trgo_requiere_analisis', null, null,["class"=>"form-checkbox", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('trgo_requiere_doble_aprob', 'Requiere doble aprobación', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::checkbox('trgo_requiere_doble_aprob', null, null,["class"=>"form-checkbox", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('trgo_comentarios', 'Comentarios', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('trgo_comentarios', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-tipo-riesgo',

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
