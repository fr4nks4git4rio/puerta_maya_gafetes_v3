<h4 class="text-uppercase">Ajenos en Casa</h4>
<small>Corte al {{ $dia }}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
        <tr>
            <th class="bg-primary text-light">NUMERO DE TARJETA</th>
            <th class="bg-primary text-light">NOMBRE</th>
            <th class="bg-primary text-light">CLASE</th>
            <th class="bg-primary text-light">HORA REGISTRO</th>
            <th class="bg-primary text-light">PUERTA</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $r)
            <tr style="page-break-inside:avoid">
                <td>{{ $r->numero_rfid }}</td>
                <td>{{ $r->empleado }}</td>
                <td>{{ $r->clase }}</td>
                <td>{{ $r->fecha }}</td>
                <td>{{ $r->puerta }}</td>
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
