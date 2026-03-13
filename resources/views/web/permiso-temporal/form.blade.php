<link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/>
<div class="container">

    {!! Form::model($permiso,['id' => 'form-permiso','url' =>$url , 'class' => 'form-horizontal']) !!}

    {!! Form::text('ptmp_id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}
    {!! Form::text('ptmp_lcal_id',auth()->getUser()->Local->lcal_id, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    <div class="row">
        <div class="col-md-6">

            <div class="form-group row">
                {!! Form::label('ptmp_fecha', 'Fecha' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('ptmp_fecha', date('Y-m-d') ,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>


            <div class="form-group row">
                {!! Form::label('ptmp_nombre', 'Nombre *', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('ptmp_nombre', null,["class"=>"form-control"]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_crgo_id', 'Cargo', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::select('ptmp_crgo_id', $cargos ,  null , ["class"=>"form-control select2-control",
                                            "placeholder" => "Cargo", "style" => "width: 100%" ])!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_correo', 'Correo' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('ptmp_correo', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_telefono', 'Teléfono' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::text('ptmp_telefono', null,["class"=>"form-control", "placeholder" => ""]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_vigencia_inicial', 'Inicio *' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::date('ptmp_vigencia_inicial', Carbon\Carbon::now()->addDays(1)->format('Y-m-d') ,["class"=>"form-control", 'id' => 'ptmp_vigencia_inicial', "placeholder" => "", "onkeydown"=>"return false"]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_vigencia_final', 'Fin *' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::date('ptmp_vigencia_final', Carbon\Carbon::now()->addDays(16)->format('Y-m-d') ,["class"=>"form-control", 'id' => 'ptmp_vigencia_final', "placeholder" => "", 'max' => Carbon\Carbon::now()->addDays(16)->format('Y-m-d'), "onkeydown"=>"return false"]);!!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('ptmp_vacunado', 'Vacunado' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::checkbox('ptmp_vacunado', null, null,["class"=>"form-control input-sm"]);!!}
                    <br>
                    <small class="text-danger"><span class="fa fa-question-circle"></span>&nbsp;Indique si está
                        completamente vacunado</small>
                </div>
            </div>

            <div class="row form-group">
                {!! Form::label('ptmp_cert_vacuna', 'Certificado (2mb máx.)' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    @if(isset($permiso) && $permiso->ptmp_cert_vacuna)
                        <img src="/certificados_vacunacion/{{$permiso->ptmp_cert_vacuna}}"
                             alt="" class="img-fluid" id="img_cert_vacuna">
                    @else
                        <a class="btn btn-primary file-btn w-100">
                            {!! Form::file('ptmp_cert_vacuna',["accept"=>"image/jpeg,image/png,application/pdf", 'disabled' => true]) !!}
                        </a>
                        <small class="text-danger"><span class="fa fa-question-circle"></span>&nbsp;Recuerde que si no
                            sube un certificado de vacunación será necesario que muestre una prueba de PCR con resultado
                            negativo al entrar a la plaza.</small>
                    @endif
                </div>
            </div>

            <div class="row">
                {!! Form::label('data_photo', 'Fotografía *' , ['class' => 'col-sm-4 control-label']); !!}
                {{Form::hidden('data_photo',null,['id' => 'data_photo'])}}

                <a class="btn btn-primary file-btn">
                    <input type="file" id="upload" value="Selecciona una imagen" accept="image/*">
                </a>
            </div>


        </div>
        <div class="col-md-6">

            <div class="row">
                <div class="upload-demo-wrap">


                    @if($permiso != null)
                        <img id="fotografia-container" src="{{ $permiso->ptmp_foto_web  }}" width="300px"/>
                    @else
                        <img id="fotografia-container" src="{{asset('plugins/croppie/banner_foto.jpg')}}"
                             width="300px"/>
                    @endif

                </div>

            </div>
            <br>

            <div class="form-group row">
                {!! Form::label('ptmp_comentario', 'Comentario' , ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::textarea('ptmp_comentario', null,["class"=>"form-control", "size"=>"50x2"]);!!}
                </div>
            </div>


            <div class="form-group row">
                {{-- <div class="col m-t-20">
                        <label for="ptmp_caracter" class="control-label">Caracter</label>

                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_1" name="ptmp_caracter" type="radio" value="CONTRATISTA">
                            <label for="ptmp_caracter_1">CONTRATISTA</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_2" name="ptmp_caracter" type="radio" value="PROVEEDOR">
                            <label for="ptmp_caracter_2">PROVEEDOR</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_3" name="ptmp_caracter" type="radio" value="COBRATARIO">
                            <label for="ptmp_caracter_3">COBRATARIO</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_4" name="ptmp_caracter" type="radio" value="TECNICO">
                            <label for="ptmp_caracter_4">TECNICO</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_5" name="ptmp_caracter" type="radio" value="VISITA">
                            <label for="ptmp_caracter_5">VISITA</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_caracter_6" name="ptmp_caracter" type="radio" value="OTRO">
                            <label for="ptmp_caracter_6">OTRO</label>
                        </div>

                </div> --}}
                {{-- <div class="col m-t-20">
                        <label for="ptmp_objeto" class="control-label">Objeto</label>

                        <div class="radio radio-primary">
                            <input id="ptmp_objeto_1" name="ptmp_objeto" type="radio" value="CARRETA">
                            <label for="ptmp_objeto_1">CARRETA</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_objeto_2" name="ptmp_objeto" type="radio" value="MAQUINARIA">
                            <label for="ptmp_objeto_2">MAQUINARIA</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_objeto_3" name="ptmp_objeto" type="radio" value="EQUIPO">
                            <label for="ptmp_objeto_3">EQUIPO</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_objeto_4" name="ptmp_objeto" type="radio" value="MATERIAL">
                            <label for="ptmp_objeto_4">MATERIAL</label>
                        </div>
                        <div class="radio radio-primary">
                            <input id="ptmp_objeto_5" name="ptmp_objeto" type="radio" value="OTRO">
                            <label for="ptmp_objeto_5">OTRO</label>
                        </div>
                </div> --}}
            </div>

        </div>
    </div>


    {!! Form::close() !!}

</div>

<script src="{{asset('/plugins/moment/moment.js')}}"></script>
<script type="text/javascript">

    $(document).ready(function () {

        jModal = {
            uploadCrop: null,
            hasNewImage: false,
            cropSelector: '#fotografia-container',
            modal: $('#modal-form'),
            form: '#form-permiso',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $('#ptmp_fecha').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{App::getLocale()}}'
                });

                $('#ptmp_vigencia_inicial').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{App::getLocale()}}'
                });

                $('#ptmp_vigencia_final').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{App::getLocale()}}'
                });

                $('#ptmp_vigencia_inicial').on('change', function () {

                    let inicio = moment($(this).val(), 'YYYY-MM-DD');

                    let fin = inicio.add(15, 'days').format('YYYY-MM-DD');

                    $('#ptmp_vigencia_final').attr('max', fin);
                    $('#ptmp_vigencia_final').val(fin);

                });

                document.getElementById('ptmp_vacunado').addEventListener('click', function (event) {
                    if (this.checked) {
                        document.getElementById('ptmp_cert_vacuna').removeAttribute('disabled');
                    } else {
                        document.getElementById('ptmp_cert_vacuna').setAttribute('disabled', true);
                    }
                });

                if (document.getElementById('ptmp_vacunado').checked) {
                    document.getElementById('ptmp_cert_vacuna').removeAttribute('disabled');
                }


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

                } else {
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
//                   data: $($this.form).serialize(),
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

        loadScript('{{ asset('plugins/croppie/croppie.min.js') }}',
                {{--loadScript('{{ asset('plugins/moment/min/moment.min.js') }}',--}}
                function () {
                    jModal.init();
                }
//            );
        );

    });

</script>
