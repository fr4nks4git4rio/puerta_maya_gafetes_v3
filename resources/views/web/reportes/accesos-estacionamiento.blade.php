<h4 class="text-uppercase">Accesos al estacionamiento de vehículos</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">NUMERO DE TARJETA</th>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">CLASE</th>
        <th class="bg-primary text-light">HORA REGISTRO</th>
        <th class="bg-primary text-light">TIPO</th>
        <th class="bg-primary text-light">PUERTA</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td>{{$r->lgac_card_number }}</td>
            <td>{{optional($r->Local)->lcal_nombre_comercial }}</td>
            <td>{{$r->lgac_puerta_tipo }}</td>
            <td>{{$r->lgac_created_at }}</td>
            <td>{{$r->lgac_tipo}}</td>
            <td>{{$r->lgac_puerta}}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5"><b>TOTAL</b></th>
            <th><b>{{ count($records) }}</b></th>
        </tr>
    </tfoot>

</table>
