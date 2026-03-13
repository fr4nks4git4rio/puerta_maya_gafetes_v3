{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<div class="container">

    {!! Form::model($disenno_gafete,['id' => 'form-disenno-gafete','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    {{-- {!! Form::text('sgft_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!} --}}


    <div class="row">
        <div class="col-md-5">

            <div class="form-group row">
                {!! Form::label('nombre', 'Nombre', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('cat_acceso_id', 'Categoría Acceso', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::select('cat_acceso_id', $cat_accesos ,  null , ["class"=>"form-control select2-control filter-control",
                                            "placeholder" => "Categoría de Acceso", "style" => "width: 100%" ])!!}
                </div>
            </div>

            <div class="row form-group">
                {!! Form::label('imagen_front', 'Imagen Frontal (2mb máx.)' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    <a class="btn btn-primary file-btn w-100">
                        <input type="file" id="imagen_front" name="imagen_front" value="Selecciona una imagen" accept="image/*">
                    </a>
                </div>
            </div>
            <div class="row">
                {!! Form::label('imagen_back', 'Imagen Back (2mb máx.)' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    <a class="btn btn-primary file-btn w-100">
                        <input type="file" id="imagen_back" name="imagen_back" value="Selecciona una imagen" accept="image/*">
                    </a>
                </div>
            </div>

        </div>
        <div class="col-md-7">
            <div class="row">
                <div class="col-sm-6">
                    <label for="">Imagen Frontal:</label>
                    <div class="upload-demo-wrap">
                        @if($disenno_gafete != null)
                            <img id="fotografia-container-front" src="{{ 'disennos_gafetes/'.$disenno_gafete->imagen_front }}"
                                 width="300px"/>
                        @else
                            <img id="fotografia-container-front" width="300px"/>
                        @endif
                        {{-- <div id=""></div> --}}
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="">Imagen Back:</label>
                    <div class="upload-demo-wrap">
                        @if($disenno_gafete != null)
                            <img id="fotografia-container-back" src="{{ 'disennos_gafetes/'.$disenno_gafete->imagen_back }}"
                                 width="300px"/>
                        @else
                            <img id="fotografia-container-back" width="300px"/>
                        @endif
                        {{-- <div id=""></div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        jModal = {
            hasNewImage: false,
            modal: $('#modal-form'),
            form: '#form-disenno-gafete',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });

                // setTimeout(() => {
                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });
                // }, 1000);

                $('input#imagen_front').off().on('change', function () {
                    size = $(this)[0].files[0].size / 1024 / 1024;
                    if(size > 2){
                        document.getElementById('imagen_front').value = '';
                        APAlerts.error('Imagen muy grande!');
                    }else{
                        console.log('Se elijio un archivo delantero');
                        $this.readFileFront(this);
                    }
                });
                $('input#imagen_back').off().on('change', function () {
                    if(size > 2){
                        document.getElementById('imagen_back').value = '';
                        APAlerts.error('Imagen muy grande!');
                    }else{
                        console.log('Se elijio un archivo trasero');
                        $this.readFileBack(this);
                    }
                });

            },

            readFileFront: function (input) {
                let $this = this;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#fotografia-container-front').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);

                }
                else {
                    alert("El navegador no soporta esta funcionalidad.");
                }
            },
            readFileBack: function (input) {
                let $this = this;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#fotografia-container-back').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);

                }
                else {
                    alert("El navegador no soporta esta funcionalidad.");
                }
            },

            handleSubmit: function () {

                let $this = this;

                $this.doSendForm();


            },

            doSendForm: function () {

                let $this = this;
                let url = $($this.form).attr('action');
                let form = $($this.form)[0];

                $.ajax({
                    url: url,
                    method: 'POST',
//                    data: $($this.form).serialize(),
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
