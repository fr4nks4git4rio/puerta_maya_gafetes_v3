<div class="container">

    {!! Form::model($local,['id' => 'form-local','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::hidden('lcal_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    {!! Form::hidden('lcal_tipo',$tipo, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    @if($id_acceso == 2)
        <div class="form-group row">
            {!! Form::label('lcal_cacs_id', 'Tipo', ['class' => 'col-sm-4 control-label']); !!}
            <div class="col-sm-8">
                {!! Form::select('lcal_cacs_id',['2'=>'Local','1'=> 'Administración'],$local->lcal_cacs_id ?? $id_acceso, ["class"=>"form-control", "placeholder" => "Seleccione"]);!!}
            </div>
        </div>
    @else
        {!! Form::hidden('lcal_cacs_id',$id_acceso, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    @endif

    <div class="form-group row">
        {!! Form::label('lcal_nombre_comercial', 'Nombre Comercial', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_nombre_comercial', null,["class"=>"form-control", "readonly" => false]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_identificador', 'Identificador', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_identificador', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_razon_social', 'Razón Social', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_razon_social', null,["class"=>"form-control", "readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_rfc', 'RFC', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_rfc', null,["class"=>"form-control", "readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_regimen_fiscal_id', 'Régimen Fiscal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_regimen_fiscal_id', isset($local, $local->RegimenFiscal) ? $local->RegimenFiscal->descripcion : '',["class"=>"form-control", "readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_codigo_postal', 'Código Postal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_codigo_postal', null,["class"=>"form-control", "placeholder" => "","readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_direccion_fiscal', 'Dirección Fiscal', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::textarea('lcal_direccion_fiscal', null,["class"=>"form-control", "placeholder" => "", "rows" => "3","readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_referencia_bancaria', 'Referencia Bancaria', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_referencia_bancaria', null,["class"=>"form-control", "readonly" => true]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_nombre_responsable', 'Responsable', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-8">
            {!! Form::text('lcal_nombre_responsable', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_espacios_autos', 'Espacios de estacionamiento para  autos', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_espacios_autos', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_espacios_motos', 'Espacios de estacionamiento para motos', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_espacios_motos', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_gafetes_gratis', 'Gafetes de acceso gratuitos', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_gafetes_gratis', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_gafetes_gratis_auto', 'Gafetes gratuitos para autos', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_gafetes_gratis_auto', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    <div class="form-group row">
        {!! Form::label('lcal_gafetes_gratis_moto', 'Gafetes gratuitos para motos', ['class' => 'col-sm-4 control-label']); !!}
        <div class="col-sm-5">
            {!! Form::text('lcal_gafetes_gratis_moto', null,["class"=>"form-control", "placeholder" => ""]);!!}
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
