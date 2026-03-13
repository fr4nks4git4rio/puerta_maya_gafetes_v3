

<h4 class="text-uppercase">Permisos temporales</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">FOTO</th>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">NOMBRE</th>
        <th class="bg-primary text-light">CARGO</th>

        <th class="bg-primary text-light">INICIO</th>
        <th class="bg-primary text-light">FIN</th>

        <th class="bg-primary text-light">ESTADO</th>
        <th class="bg-primary text-light">COMENTARIO</th>
        <th class="bg-primary text-light">GAFETE</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td><img src="{{$r->ptmp_thumb_web}}" alt="" class="img-thumbnail" width="40px"></td>
            <td>{{optional($r->Local)->lcal_nombre_comercial}}</td>
            <td>{{$r->ptmp_nombre}}</td>
            <td>{{$r->Cargo->crgo_descripcion}}</td>
            <td style="white-space: nowrap">{{$r->ptmp_vigencia_inicial}}</td>
            <td style="white-space: nowrap">{{$r->ptmp_vigencia_final}}</td>
            <td>{{$r->ptmp_estado}}</td>
            <td>{{$r->ptmp_comentario}}</td>
            <td>{{  $r->GafetePreimpreso ? $r->GafetePreimpreso->gfpi_numero : ""   }}</td>
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th colspan="8" class="text-center"> Total </th>
        <th class="text-center">{{ count($records) }}</th>
    </tr>

</table>
