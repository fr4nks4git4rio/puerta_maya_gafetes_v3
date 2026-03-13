{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model(null,['id' => 'form-texto-back','url' =>$url , 'class' => 'form-horizontal']) !!}

    <div class="row">
        <div class="col-sm-12">
            <label for="">Texto:</label>
            <textarea name="texto_back_gafetes" id="texto_back_gafetes" cols="30" rows="10"
                      class="form-control ckeditor">{{$texto}}</textarea>
        </div>
    </div>

    {!! Form::close() !!}

</div>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace('texto_back_gafetes', {
            uiColor: '#f3f3f3',
            language: 'es'
        });
        {{--CKEDITOR.instances['texto_back_gafetes'].setData('{!! $texto !!}'.valueOf());--}}

            jModal = {
            modal: $('#modal-form-texto'),
            form: '#form-texto-back',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });
            },


            handleSubmit: function () {

                let $this = this;

                let url = $($this.form).attr('action');

                $('#texto_back_gafetes').val(CKEDITOR.instances['texto_back_gafetes'].getData());
                console.log(CKEDITOR.instances['texto_back_gafetes'].getData());
//                let data = $($this.form).serialize();
                let form = $($this.form)[0];

//                    let form = $($this.form)[0];
//                    let formaData = new FormData(form);
//                    formaData.append('disennos', $this.disennos_seleccionados);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(form),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            $('#modal-btn-close', $this.modal).click();

                            CKEDITOR.replace('texto_back_gafetes');

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
