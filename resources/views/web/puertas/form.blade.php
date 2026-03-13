<div class="container">

    {!! Form::model($puerta,['id' => 'form-puerta','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('door_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('door_nombre', 'Nombre', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('door_nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_tipo', 'Tipo Acceso', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('door_tipo', ['PEATONAL' => 'PEATONAL', 'AUTO' => 'AUTO', 'MOTO' => 'MOTO'], null,["class"=>"form-control select2-control filter-control",
                                    "placeholder" => "", "style" => "width: 100%" ]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_numero', 'Número Puerta', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::number('door_numero', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_direccion', 'Dirección', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('door_direccion', ['ENTRADA' => 'ENTRADA', 'SALIDA' => 'SALIDA'], null,["class"=>"form-control select2-control filter-control",
                                    "placeholder" => "", "style" => "width: 100%" ]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_modo', 'Modalidad', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('door_modo', ['FISICA' => 'FISICA', 'VIRTUAL' => 'VIRTUAL'], null,["class"=>"form-control select2-control filter-control",
                                    "placeholder" => "", "style" => "width: 100%" ]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_controladora_id', 'Controladora', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('door_controladora_id', $controladoras, null,["class"=>"form-control select2-control filter-control",
                                    "placeholder" => "", "style" => "width: 100%" ]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('door_observaciones', 'Observaciones', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::textarea('door_observaciones', null,["class"=>"form-control", 'rows' => 2, "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-puerta',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });

                setTimeout(() => {
                    $('.select2-control').select2({
                        'allowClear': true,
                        placeholder: "Seleccione",
                        width: '100%'
                    });
                }, 1000);
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
