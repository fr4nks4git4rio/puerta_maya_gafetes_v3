{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model(null,['id' => 'form-send-factura','url' => $url, 'enctype' => 'multipart/form-data']) !!}

    <div class="row">
        {!! Form::hidden('fact_id', $factura->fact_id ,["class"=>"form-control", "placeholder" => ""]);!!}

        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('local', 'Local' , ['class' => 'control-label']); !!}
                {!! Form::text('local', $nombre_comercial ,["class"=>"form-control", "disabled" => true]);!!}
            </div>
        </div>
    </div>

    <hr>


    <div class="row">

        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('email_to', 'Para:' , ['class' => 'control-label']); !!}
                {!! Form::email('email_to', $receptor ,["class"=>"form-control", "placeholder" => ""]);!!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('email_to_others', 'Otros:' , ['class' => 'control-label']); !!}
                {!! Form::email('email_to_others', '' ,["class"=>"form-control", "placeholder" => "Escriba las direcciones separadas por (;)"]);!!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('subject', 'Asunto:' , ['class' => 'control-label']); !!}
                {!! Form::text('subject', $topic ,["class"=>"form-control"]);!!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('files', 'Adjuntos:' , ['class' => 'control-label']); !!}
                {!! Form::input('file', 'files', null ,["class"=>"form-control", 'multiple' => 'multiple']);!!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('body', 'Cuerpo:' , ['class' => 'control-label']); !!}
                {!! Form::textarea('body', $body ,["class"=>"form-control", 'rows' => 3, 'id' => 'body']);!!}
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <p class="alert alert-info">
                Se enviará un correo con el xml y pdf correspondientes a la factura.
            </p>
        </div>
    </div>


    {!! Form::close() !!}

</div>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function () {
        CKEDITOR.replace('body');
        jModal = {
            modal: $('#modal-form'),
            form: '#form-send-factura',


            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    console.log('enviando...');
                    $this.handleSubmit();
                });

            },


            handleSubmit: function () {

                let $this = this;
                let url = $($this.form).attr('action');
                $('#body').val(CKEDITOR.instances['body'].getData());
//                let data = $($this.form).serialize();
                let form = $($this.form)[0];

                console.log(form);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(form),
//                    data: $($this.form).serialize(),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            // dTables.oTable.draw();
                            $('#modal-btn-close').click();
                            // jNuevaFactura.addConcepto(res.data);

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
