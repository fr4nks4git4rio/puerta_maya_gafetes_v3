<div class="container">

    <dl class="row">

        <dt class="col-sm-3">Local</dt>
        <dd class="col-sm-9">{{$permiso->Local->lcal_nombre_comercial}}</dt>

        <dt class="col-sm-3">Empresa</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_empresa}}</dt>

        <dt class="col-sm-3">Representante</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_representante}}</dt>

        <dt class="col-sm-3">Solicitante</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_solicitante}}</dt>

        <dt class="col-sm-3">Trabajo</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_trabajo}}</dt>

        <dt class="col-sm-3">Fecha Solicitud</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_fecha}}</dt>

        <dt class="col-sm-3">Vigencia</dt>
        <dd class="col-sm-9">{{$permiso->pmtt_dias}} días, del {{$permiso->pmtt_vigencia_inicial}} al {{$permiso->pmtt_vigencia_final}}</dt>

        <dt class="col-sm-3">Observaciones</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_observaciones}}</dt>

        <dt class="col-sm-3">Trabajadores</dt>
        <dd class="col-sm-9">{!! nl2br($permiso->pmtt_listado_trabajadores)  !!}</dt>

        @php

            $color = 'badge-primary';
            if($permiso->pmtt_estado == 'PENDIENTE') $color = 'badge-warning';
            if($permiso->pmtt_estado == 'APROBADO') $color = 'badge-success';
            if($permiso->pmtt_estado == 'RECHAZADO') $color = 'badge-danger';
        @endphp
        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9"> <span class="badge {{$color}}"> {{$permiso->pmtt_estado}} </span> </dt>

{{--        @if($permiso->pmtt_estado == 'RECHAZADO')--}}
{{--            <dt class="col-sm-3">Comentario Rechazo</dt>--}}
{{--            <dd class="col-sm-9"> {{$permiso->pmtt_comentario_admon}} </dt>--}}
{{--        @endif--}}


    </dl>


</div>
