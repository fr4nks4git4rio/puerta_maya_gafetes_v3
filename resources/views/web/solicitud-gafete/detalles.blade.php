<div class="container">

    <dl class="row">

        <dt class="col-sm-3">Local</dt>
        <dd class="col-sm-9">{{$solicitud->Local->lcal_nombre_comercial}}</dt>

        <dt class="col-sm-3">Nombre</dt>
        <dd class="col-sm-9">{{$solicitud->sgft_nombre}}</dt>

        <dt class="col-sm-3">Cargo</dt>
        <dd class="col-sm-9">{{$solicitud->sgft_cargo}}</dt>

        <dt class="col-sm-3">Foto</dt>
        <dd class="col-sm-9"><img class="img-thumbnail" src="{{$solicitud->sgft_thumb_web}}" alt=""></dt>


        <dt class="col-sm-3">Tipo</dt>
        <dd class="col-sm-9">{{$solicitud->sgft_tipo}}</dt>

        <dt class="col-sm-3">Fecha</dt>
        <dd class="col-sm-9">{{$solicitud->sgft_fecha}}</dt>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{$solicitud->sgft_estado}}</dt>

        <dt class="col-sm-3">Comentario</dt>
        <dd class="col-sm-9"><small><i>{{$solicitud->sgft_comentario}}</i></small></dt>

        @if($solicitud->sgft_gratuito == 1)
        <dt class="col-sm-3">Gafete gratuito</dt>
        <dd class="col-sm-9"><p class="alert alert-info">Con este,
                            <b>{{$solicitud->Local->lcal_nombre_comercial}}</b> ha solicitado <br/><b>{{ $solicitud->Local->lcal_gafetes_gratis - $solicitud->Local->gafetesGratuitosDisponibles() }}</b>
                             gafete(s) gratuito(s) de <b>{{ $solicitud->Local->lcal_gafetes_gratis }}</b> autorizados</p></dt>
        @endif
{{--        <dt class="col-sm-3">Comprobante</dt>--}}
{{--        @if($solicitud->sgft_cpag_id != "")--}}
{{--        <dd class="col-sm-9">--}}
{{--            Folio: {{ $solicitud->ComprobantePago->cpag_folio_bancario }} Referencia: {{ $solicitud->ComprobantePago->cpag_aut_bancario }}--}}
{{--            <br>Importe Pagado: $ {{ number_format($solicitud->ComprobantePago->cpag_importe_pagado,2) }}--}}
{{--            @if($solicitud->sgft_file_comprobante != "")--}}
{{--                <br>--}}
{{--             <a href="{{asset('storage/comprobantes/'. $solicitud->sgft_file_comprobante)}}" target="_blank"> Descargar comprobante original</a>--}}
{{--            @else--}}
{{--                <p class="alert alert-warning">--}}
{{--                    No se cargo comprobante original--}}
{{--                </p>--}}
{{--            @endif--}}
{{--            <p class="alert alert-info">Con esta solicitud el comprobante se ha utilizado--}}
{{--                        @if($solicitud->sgft_tipo == "PRIMERA VEZ")--}}
{{--                        {{ $solicitud->ComprobantePago->getCantidadUsosPV() }}  de {{ $solicitud->ComprobantePago->cpag_cantidad_pv }} veces posibles --}}
{{--                        @else--}}
{{--                        {{ $solicitud->ComprobantePago->getCantidadUsosRP() }}  de {{ $solicitud->ComprobantePago->cpag_cantidad_rp }} veces posibles --}}
{{--                        @endif--}}
{{--            </p>--}}
{{--            </dd>--}}
{{--        @else--}}
{{--        <dd class="col-sm-9"><span class="text-info">SIN COMPROBANTE</span></dd>--}}
{{--        @endif--}}

    </dl>


</div>
