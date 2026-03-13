<h4 class="text-uppercase">Gafetes impresos de estacionamiento</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">PERMISOS</th>
        <th class="bg-primary text-light">TARJETA</th>
        <th class="bg-primary text-light">FECHA</th>

    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td>{{optional($r->Local)->lcal_nombre_comercial}}</td>
            <td>{{$r->sgft_permisos}}</td>
            <td>{{$r->sgft_numero}}</td>
            <td style="white-space: nowrap">{{$r->sgft_created_at->format('Y-m-d')}}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" class="text-center"> Total </th>
        <th class="text-center">{{ count($records) }}</th>
    </tr>
    </tfoot>

</table>
