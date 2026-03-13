{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
<style>
    .modal-body {
        padding-bottom: 0 !important;
    }
</style>
<div class="container">

    {!! Form::model(null,['id' => 'form-seleccionar','url' =>$url , 'class' => 'form-horizontal']) !!}

    {{--        {!! Form::text('id',null, ["class" => "form-control d-none", "placeholder"=>""]) !!}--}}
    {!! Form::text('dgpv_paquete_id',$paquete_disenno_gafete->dgp_id, ["class" => "form-control d-none", "placeholder"=>""]) !!}


    {{--<div class="row">--}}
    {{--<div class="col-sm-12 form-group">--}}
    {{--<p class="alert alert-info text-center">--}}
    {{--Cantidad de Diseños seleccionados: <strong--}}
    {{--id="cantidad_seleccionados"></strong> <br>--}}
    {{--Quedan por subir: <strong--}}
    {{--id="faltan"></strong> diseños--}}
    {{--</p>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                {!! Form::label('fecha_inicio', 'Fecha de Inicio', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::date('dgpv_fecha_inicio', null,["class"=>"form-control"]);!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                {!! Form::label('anno', 'Año', ['class' => 'col-sm-4 control-label']); !!}
                <div class="col-sm-8">
                    {!! Form::input('number','dgpv_anno', today()->year+1,["class"=>"form-control", 'min' => today()->year+1]);!!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7">
            <div class="form-group row">
                {!! Form::label('comentarios', 'Comentarios', ['class' => 'col-sm-2 control-label']); !!}
                <div class="col-sm-10">
                    {!! Form::text('dgpv_comentarios', null,["class"=>"form-control"]);!!}
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group row">
                <div class="col-sm-6 text-center">
                    <a href="javascript:void(0)" class="btn btn-primary" id="btn-texto-locales">Back Locales</a>
                </div>
                <div class="col-sm-6 text-center">
                    <a href="javascript:void(0)" class="btn btn-primary" id="btn-texto-admin">Back Administrativo</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3 style="margin-bottom: 0">Diseños:</h3>
        </div>
        <div class="col-sm-12">
            <div class="row">
                @foreach($disennos as $disenno)
                    <div class="col-sm-3 shadow-sm pt-2">
                        <img src="{{$disenno->src_imagen}}" alt="" style="max-height: 140px;border-radius: 5px;" class="border border-info p-1">
                        <p>Cat. Acceso:
                            <strong>{{$disenno->CatAcceso ? $disenno->CatAcceso->cacs_descripcion : ''}}</strong></p>
{{--                        <p>Nombre: <strong>{{$disenno->dgpi_nombre}}</strong></p>--}}
{{--                        <p>Imagen:</p>--}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    {!! Form::close() !!}

</div>

<script type="text/javascript">

    $(document).ready(function () {

        jModal = {
            modal: $('#modal-form'),
            form: '#form-seleccionar',
            modal_dom: 'modal-form-texto',
            texto_locales_url: '{{url('disenno_gafetes/texto_back_locales_form')}}',
            texto_admin_url: '{{url('disenno_gafetes/texto_back_admin_form')}}',

            init: function () {
                let $this = this;

                $('#modal-btn-ok', $this.modal).click(function () {
                    $this.handleSubmit();
                });

                $('#btn-texto-locales').click(function () {
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Texto Back Locales',
                        url: $this.texto_locales_url,
                        size: 'modal-lg',
                        method: 'GET'
                    });
                });

                $('#btn-texto-admin').click(function () {
                    APModal.open({
                        dom: $this.modal_dom,
                        title: 'Texto Back Administrativo',
                        url: $this.texto_admin_url,
                        size: 'modal-lg',
                        method: 'GET'
                    });
                });

//                $('#fecha_inicio', $this.modal).change(function (event) {
//                    if (event.target.value) {
//                        $('#anno', $this.modal).val(event.target.value.split('-')[0]);
//                    } else {
//                        $('#anno', $this.modal).val('');
//                    }
//                });
            },


            handleSubmit: function () {

                let $this = this;

                let url = $($this.form).attr('action');

//                    let form = $($this.form)[0];
//                    let formaData = new FormData(form);
//                    formaData.append('disennos', $this.disennos_seleccionados);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $($this.form).serialize(),
//                        contentType: false,
//                        cache: false,
//                        processData: false,
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
