<div class="container">

    <dl class="row">

        <dt class="col-sm-3">Local</dt>
        <dd class="col-sm-9">{{$permiso->Local->lcal_nombre_comercial}}</dt>

        <dt class="col-sm-3">Nombre</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_nombre}}</dt>

        @if($permiso->ptmp_foto != "")
        <dt class="col-sm-3">Foto</dt>
        <dd class="col-sm-9"><img class="img-thumbnail" src="{{$permiso->ptmp_thumb_web}}" alt=""></dt>
        @endif

        <dt class="col-sm-3">Cargo</dt>
        <dd class="col-sm-9">{{$permiso->Cargo->crgo_descripcion}}</dt>

        <dt class="col-sm-3">Correo</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_correo}}</dt>

        <dt class="col-sm-3">Teléfono</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_telefono}}</dt>

        <dt class="col-sm-3">Fecha</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_fecha}}</dt>

        <dt class="col-sm-3">Periodo</dt>
        <dd class="col-sm-9">DEL {{$permiso->ptmp_vigencia_inicial}} AL {{$permiso->ptmp_vigencia_final}}</dt>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{$permiso->ptmp_estado}}</dt>

        @if($permiso->ptmp_estado == 'ASIGNADO' || $permiso->ptmp_estado == 'ENTREGADO' || $permiso->ptmp_estado == 'VENCIDO' || $permiso->ptmp_estado == 'CONCLUIDO'   )
            <dt class="col-sm-3">Gafete</dt>
            <dd class="col-sm-9">{{$permiso->GafetePreimpreso->gfpi_numero}}</dt>
        @endif

        <dt class="col-sm-4">Comentario Captura</dt>
        <dd class="col-sm-8"><small><i>{{$permiso->ptmp_comentario}}</i></small></dt>


        @if($permiso->ptmp_estado == 'RECHAZADO')
            <dt class="col-sm-4">Comentario Rechazo</dt>
            <dd class="col-sm-8"><small class="text-danger"><i>{{$permiso->ptmp_comentario_admin}}</i></small></dt>

        @endif
    </dl>


</div>
