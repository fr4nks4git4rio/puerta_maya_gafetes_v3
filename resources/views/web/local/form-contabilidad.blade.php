<div class="container">

    {!! Form::model($local,['id' => 'form-local','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::hidden('lcal_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}

    <div class="form-group row">
        {!! Form::label('lcal_nombre_comercial', 'Nombre Comercial', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_nombre_comercial', null,["class"=>"form-control", "readonly" => true]);!!}
        </div>
    </div>


    <div class="form-group row">
        {!! Form::label('lcal_razon_social', 'Razón Social', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_razon_social', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_rfc', 'RFC', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_rfc', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_regimen_fiscal_id', 'Régimen Fiscal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::select('lcal_regimen_fiscal_id', \App\CRegimenFiscal::all()->pluck('descripcion', 'id'), $local->lcal_regimen_fiscal_id,["class"=>"form-control", "placeholder" => "Seleccione..."]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_codigo_postal', 'Código Postal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_codigo_postal', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_direccion_fiscal', 'Dirección Fiscal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::textarea('lcal_direccion_fiscal', null,["class"=>"form-control", "placeholder" => "", "rows" => "3"]);!!}
        </div>
    </div>

    <div class="row">
        {!! Form::label('lcal_referencia_bancaria', 'Referencia Bancaria', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_referencia_bancaria', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-local',

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
