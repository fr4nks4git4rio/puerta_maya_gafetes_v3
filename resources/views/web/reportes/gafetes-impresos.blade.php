

<h4 class="text-uppercase">Gafetes impresos</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">Número Tarjeta</th>
        <th class="bg-primary text-light">NOMBRE</th>
        <th class="bg-primary text-light">FOTO</th>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">PERMISOS ESTACIONAMIENTO</th>
        <th class="bg-primary text-light">FECHA</th>
        <th class="bg-primary text-light">ESTADO</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">

            <td>{{$r->sgft_numero}}</td>
            <td>{{$r->sgft_nombre}}</td>
            <td><img src="{{$r->sgft_thumb_web}}" alt="" class="img-thumbnail" width="40px"></td>
            <td>{{optional($r->Local)->lcal_nombre_comercial}}</td>
            <td>{{str_replace(["PEATONAL,","PEATONAL"], "", $r->sgft_permisos)}}</td>
            <td style="white-space: nowrap">{{$r->sgft_fecha}}</td>
            <td>{{$r->sgft_estado}}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="6" class="text-center"> Total </th>
        <th class="text-center">{{ count($records) }}</th>
    </tr>
    </tfoot>

</table>
