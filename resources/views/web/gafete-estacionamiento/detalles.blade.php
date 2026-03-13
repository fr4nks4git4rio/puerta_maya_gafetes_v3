<div class="container">

    <dl class="row">

        <dt class="col-sm-3">Local</dt>
        <dd class="col-sm-9">{{$gafete->Local->lcal_nombre_comercial}}</dt>

{{--        <dt class="col-sm-3">Nombre</dt>--}}
{{--        <dd class="col-sm-9">{{$solicitud->sgft_nombre}}</dt>--}}

{{--        <dt class="col-sm-3">Cargo</dt>--}}
{{--        <dd class="col-sm-9">{{$solicitud->sgft_cargo}}</dt>--}}

{{--        <dt class="col-sm-3">Foto</dt>--}}
{{--        <dd class="col-sm-9"><img class="img-thumbnail" src="{{$solicitud->sgft_thumb_web}}" alt=""></dt>--}}

        <dt class="col-sm-3">Clase</dt>
        <dd class="col-sm-9">{{$gafete->gest_tipo}}</dt>

        <dt class="col-sm-3">Tipo</dt>
        <dd class="col-sm-9">{{$gafete->gest_tipo_solicitud}}</dt>

        <dt class="col-sm-3">Año</dt>
        <dd class="col-sm-9">{{$gafete->gest_anio}}</dt>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{$gafete->gest_estado}}</dt>

        <dt class="col-sm-3">Comentario</dt>
        <dd class="col-sm-9"><small><i>{{$gafete->gest_comentario}}</i></small></dt>

        @if($gafete->gest_gratuito == 1)
        <dt class="col-sm-3">Gafete gratuito</dt>
        <dd class="col-sm-9"><p class="alert alert-info">Con este,
                            <b>{{$gafete->Local->lcal_nombre_comercial}}</b> ha solicitado <br/><b>
                            @if($gafete->gest_tipo == 'AUTO')
                                {{ $gafete->Local->lcal_espacios_autos - $gafete->Local->gafetesEstacionamientoAutoDisponibles() }}</b>
                                gafete(s) gratuito(s) de <b>{{ $gafete->Local->lcal_espacios_autos }}</b> autorizados para AUTO</p>
                            @else
                                {{ $gafete->Local->lcal_espacios_motos - $gafete->Local->gafetesEstacionamientoMotoDisponibles()  }}</b>
                                gafete(s) gratuito(s) de <b>{{ $gafete->Local->lcal_espacios_motos }}</b> autorizados para MOTO</p>
                            @endif
        </dd>
        @endif

    </dl>


</div>
