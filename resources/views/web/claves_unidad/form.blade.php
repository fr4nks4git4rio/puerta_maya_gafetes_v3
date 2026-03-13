<div class="container">

    {!! Form::model($clave_unidad,['id' => 'form-clave-unidad','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('codigo', 'Código', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('codigo', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('nombre', 'Nombre', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('descripcion', 'Descripción', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('descripcion', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('nota', 'Nota', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('nota', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('fecha_inicio', 'Fecha Inicio', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::date('fecha_inicio', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('fecha_fin', 'Fecha Fin', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::date('fecha_fin', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-clave-unidad',

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
