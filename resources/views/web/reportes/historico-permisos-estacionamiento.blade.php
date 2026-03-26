<h4 class="text-uppercase">Histórico de permisos de estacionamiento</h4>
<small>Inicio: {{ $inicio }} &nbsp;&nbsp;Fin: {{ $fin }}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
        <tr>
            <th class="bg-primary text-light">NOMBRE</th>
            <th class="bg-primary text-light">NUMERO DE TARJETA</th>
            <th class="bg-primary text-light">LOCAL</th>
            <th class="bg-primary text-light">OPERACIÓN</th>
            <th class="bg-primary text-light">HORA REGISTRO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $r)
            <tr style="page-break-inside:avoid">
                <td>{{ $r->empleado }}</td>
                <td>{{ $r->numero_tarjeta }}</td>
                <td>{{ $r->local }}</td>
                <td>{{ $operaciones[$r->estado] }}</td>
                <td>{{ $r->fecha }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4"><b>TOTAL</b></th>
            <th><b>{{ count($records) }}</b></th>
        </tr>
    </tfoot>

</table>
