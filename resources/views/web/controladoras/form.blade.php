<div class="container">

    {!! Form::model($controladora,['id' => 'form-controladora','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('ctrl_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('ctrl_nombre', 'Nombre', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('ctrl_nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('ctrl_ip', 'IP', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('ctrl_ip', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('ctrl_usuario', 'Usuario', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('ctrl_usuario', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('ctrl_contrasenna', 'Contraseña', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('ctrl_contrasenna', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('ctrl_descripcion', 'Descripción', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::textarea('ctrl_descripcion', null,["class"=>"form-control", 'rows' => 2, "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-controladora',

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
