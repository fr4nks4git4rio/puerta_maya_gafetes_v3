<div class="container">

    {!! Form::model($transferencia,['id' => 'form-transferencia-saldo','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="form-group row">
        {!! Form::label('razon_social', 'Razón Social', ['class' => 'col-sm-3 control-label']) !!}
        <div class="col-sm-9">
            {!! Form::select('razon_social',$razonsociales, null,["class"=>"form-control select2-control border"]);!!}
        </div>
    </div>
    <div class="form-group row">
        {!! Form::label('local_desde_id', 'Local Origen', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('local_desde_id',[], null,["class"=>"form-control select2-control border"]);!!}
        </div>
    </div>
    <div class="form-group row">
        {!! Form::label('local_para_id', 'Local Destino', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::select('local_para_id',[], null,["class"=>"form-control select2-control border"]);!!}
        </div>
    </div>
    <div class="form-group row">
        {!! Form::label('saldo', 'Saldo a Transferir', ['class' => 'col-sm-3 control-label']); !!}
        <div class="col-sm-9">
            {!! Form::text('saldo', null,["class"=>"form-control", "placeholder" => ""]);!!}
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        var jModal = {
            modal: $('#modal-form'),
            form: '#form-transferencia-saldo',

            init: function () {
                let $this = this;

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $('#razon_social', $this.modal).change(function ($event) {
                    $this.loadLocalsBySocialReason($event.target.value);
                });

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });
            },

            handleSubmit: function () {

                let $this = this;
                let url = $($this.form).attr('action');

                if($('#local_desde_id').val() === $('#local_para_id').val()){
                    APAlerts.error("El Local Origen y el Local Destino no pueden coincidir!");
                }else{
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
            },

            loadLocalsBySocialReason: function (social_reazon) {
                $.ajax({
                    url: '/local/locales-by-razon-social?q=' + social_reazon,
                    method: 'GET',
                    success: function (res) {
                        $("#local_desde_id").select2({
                            data: res,
                            width: '100%',
                            allowClear: true,
//                            templateResult: function (d) {
//                                return $(d.text);
//                            },
//                            templateSelection: function (d) {
//                                return $(d.text);
//                            },
                            placeholder: 'Seleccione...'
                        });

                        $("#local_desde_id").val(null).trigger('change');

                        $("#local_para_id").select2({
                            data: res,
                            width: '100%',
                            allowClear: true,
//                            templateResult: function (d) {
//                                return $(d.result_template);
//                            },
                            // templateSelection: function (d) { return $(d.text); },
                            placeholder: 'Seleccione...'
                        });

                        $("#local_para_id").val(null).trigger('change');
                    }
                });
            }
        };

        jModal.init();

    });

</script>
