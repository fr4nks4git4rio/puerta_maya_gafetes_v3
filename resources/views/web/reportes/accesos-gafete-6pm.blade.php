<h4 class="text-uppercase">Reporte de acceso por gafete a las 6pm</h4>
<small>Corte del {{$dia }}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">NUMERO DE TARJETA</th>
        <th class="bg-primary text-light">NOMBRE</th>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">TIPO</th>
        <th class="bg-primary text-light">ULTIMA ENTRADA</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td>{{$r->lgac_card_number }}</td>
            <td>{{$r->nombre }}</td>
            <td>{{$r->local }}</td>
            <td>{{$r->tipo }}</td>
            <td>{{$r->ultima_entrada }}</td>
{{--            <td>{{$r->lgac_tipo}}</td>--}}
        </tr>
        @endforeach
    </tbody>

</table>