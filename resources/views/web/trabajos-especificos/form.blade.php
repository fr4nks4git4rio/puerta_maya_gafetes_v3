<div class="container">

    {!! Form::model($trabajoEspecifico, [
        'id' => 'form-trabajo-especifico',
        'url' => $url,
        'class' => 'form-horizontal',
    ]) !!}

    {!! Form::text('tesp_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}


    <div class="form-group row">
        {!! Form::label('tesp_nombre', 'Nombre', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::text('tesp_nombre', null, ['class' => 'form-control', 'placeholder' => '']) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('tesp_tmtt_id', 'Tipo de Mantenimiento', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::select('tesp_tmtt_id', $tiposMantenimiento, null, [
                'class' => 'form-control select2-control',
                'placeholder' => 'Seleccione...',
                'style' => 'width: 100%',
            ]) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('tesp_trgo_id', 'Tipo de Riesgo', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::select('tesp_trgo_id', $tiposRiesgo, null, [
                'class' => 'form-control select2-control',
                'placeholder' => 'Seleccione...',
                'style' => 'width: 100%',
            ]) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('tesp_epp_basicos', 'EPP Básicos', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::select('tesp_epp_basicos', $eppBasicos, $tesp_epp_basicos, [
                'class' => 'form-control select2-control',
                'style' => 'width: 100%',
                'name' => 'tesp_epp_basicos[]',
                'id' => 'tesp_epp_basicos',
                'multiple' => 'multiple'
            ]) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('tesp_epp_especificos', 'EPP Específicos', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::select('tesp_epp_especificos', $eppEspecificos, $tesp_epp_especificos, [
                'class' => 'form-control select2-control',
                'style' => 'width: 100%',
                'name' => 'tesp_epp_especificos[]',
                'id' => 'tesp_epp_especificos',
                'multiple' => 'multiple'
            ]) !!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('tesp_comentarios', 'Comentarios', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::text('tesp_comentarios', null, ['class' => 'form-control', 'placeholder' => '']) !!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">
    $(document).ready(function() {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-trabajo-especifico',

            init: function() {
                let $this = this;

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

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
                    data: $($this.form).serialize(),
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

        jModal.init();

    });
</script>
