<link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/>
<div class="container">

    {!! Form::model($empleado,['id' => 'form-empleado','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('empl_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    {!! Form::text('empl_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="row">
        <div class="col-md-6">

            <div class="form-group row">
                {!! Form::label('empl_nombre', 'Nombre', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('empl_nombre', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_crgo_id', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::select('empl_crgo_id', $cargos ,  null , ["class"=>"form-control select2-control filter-control",
                                            "placeholder" => "Cargo", "style" => "width: 100%" ])!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_email', 'Correo', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('empl_email', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_telefono', 'Teléfono' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('empl_telefono', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_nss', 'Número de Seguro Social' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('empl_nss', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_vacunado', 'Vacunado' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::checkbox('empl_vacunado', null, null,["class"=>"form-control input-sm"]);!!}
                    <br>
                    <small><span class="fa fa-question-circle"></span>&nbsp;Indique si está completamente vacunado
                    </small>
                </div>
            </div>

            <div class="row form-group">
                {!! Form::label('empl_cert_vacuna', 'Certificado (2mb máx.)' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8" id="contenedor_certificado">
                    @if(isset($empleado) && $empleado->empl_cert_vacuna)
                        <a href="javascript:void;" class="btn btn-primary btn-xs position-absolute"
                           style="left: 20px; top: 10px" id="zoom_frame"
                           onclick="document.getElementById('frame_certificado').requestFullscreen()"><i
                                    class="fa fa-search-plus"></i></a>
                        <iframe width="300" height="200" allow="fullscreen" id="frame_certificado"
                                src="/certificados_vacunacion/{{$empleado->empl_cert_vacuna}}?{{now()}}">
                        </iframe>
                        {{--<img src="/certificados_vacunacion/{{$empleado->empl_cert_vacuna}}"--}}
                        {{--alt="" class="img-fluid" id="img_cert_vacuna">--}}
                    @else
                        <a class="btn btn-primary file-btn w-100">
                            {!! Form::file('empl_cert_vacuna',["accept"=>"image/jpeg,image/png,application/pdf", 'disabled' => true]) !!}
                        </a>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('empl_comentario', 'Comentario' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('empl_comentario', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="row">
                {!! Form::label('upload', 'Foto de Empleado' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {{Form::hidden('data_photo',null,['id' => 'data_photo'])}}

                    <a class="btn btn-primary file-btn w-100">
                        <input type="file" id="upload" value="Selecciona una imagen" accept="image/*">
                    </a>
                </div>
            </div>

        </div>
        <div class="col-md-6">

            <div class="upload-demo-wrap">
                @if($empleado != null)
                    <img id="fotografia-container" src="{{ $empleado->empl_foto_web }}" width="300px"/>
                @else
                    <img id="fotografia-container" src="{{asset('plugins/croppie/banner_foto.jpg')}}" width="300px"/>
                @endif
                {{-- <div id=""></div> --}}
            </div>


        </div>
    </div>


    {!! Form::close() !!}

</div>

<script src="{{ asset('plugins/croppie/croppie.min.js') }}"></script>
<script src="{{ asset('plugins/moment/min/moment.min.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function () {

        jModal = {
            uploadCrop: null,
            hasNewImage: false,
            cropSelector: '',
            modal: $('#modal-form'),
            form: '#form-empleado',

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

                document.getElementById('empl_vacunado').addEventListener('click', function (event) {
                    if (this.checked) {
                        if (document.getElementById('empl_cert_vacuna'))
                            document.getElementById('empl_cert_vacuna').removeAttribute('disabled');
                    } else {
                        if (document.getElementById('empl_cert_vacuna')) {
                            document.getElementById('empl_cert_vacuna').setAttribute('disabled', true);
                            document.getElementById('empl_cert_vacuna').value = '';
                        }
                        if (document.getElementById('frame_certificado')) {
                            document.getElementById('frame_certificado').remove();
                            document.getElementById('zoom_frame').remove();
                            $('#contenedor_certificado').append('<a class="btn btn-primary file-btn w-100">' +
                                '<input type="file" name="empl_cert_vacuna" id="empl_cert_vacuna" accept="image/jpeg,image/png,application/pdf" disabled>' +
                                '</a>');
                        }
                    }
                });

                if (document.getElementById('empl_vacunado').checked) {
                    document.getElementById('empl_cert_vacuna').removeAttribute('disabled');
                }

                $this.cropSelector = '#fotografia-container';
                $this.initCroppie();

            },

            initCroppie: function () {
                let $this = this;

                $($this.cropSelector).croppie('destroy');
                // console.log($this.cropSelector);
                $this.uploadCrop = $($this.cropSelector).croppie({
                    viewport: {
                        width: 200,
                        height: 240,
                        // type: 'circle'
                    },
                    boundary: {
                        width: 300,
                        height: 300
                    }
                });

                $('input#upload').off().on('change', function () {
                    console.log('Se elijio un archivo');
                    $this.readFile(this);
                });
            },

            readFile: function (input) {
                let $this = this;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $($this.cropSelector).addClass('ready');
                        $this.uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function () {
                            $this.hasNewImage = true;
                            console.log('jQuery bind complete');
                        });

                    }

                    reader.readAsDataURL(input.files[0]);

                }
                else {
                    alert("El navegador no soporta esta funcionalidad.");
                }
            },

            handleSubmit: function () {

                let $this = this;

                if ($this.hasNewImage == true) {

                    $this.uploadCrop.croppie('result', {
                        type: 'base64',
                        format: 'jpeg',
                        size: {
                            width: 550,
                            height: 660
                        },
                        quality: .9
                    }).then(function (resp) {
                        $('#data_photo').val(resp);
                        console.log('Preparado para enviar form');
                        $this.doSendForm();

                    });

                } else {
                    $this.doSendForm();
                }


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


        {{--loadScript('{{ asset('plugins/croppie/croppie.min.js') }}',--}}
            {{--loadScript('{{ asset('plugins/moment/min/moment.min.js') }}',--}}
                {{--function(){--}}
                    jModal.init();
//                }
//            )
//        );


    });

</script>
