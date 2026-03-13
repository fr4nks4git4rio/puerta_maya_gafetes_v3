<h4 class="text-uppercase">Solicitudes de mantenimiento</h4>
<small>Periodo del {{ $inicio->format('Y-m-d') }} al {{ $fin->format('Y-m-d') }}</small>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
        <tr>
            <th class="bg-primary text-light">LOCAL</th>
            <th class="bg-primary text-light">FOLIO</th>
            <th class="bg-primary text-light">EMPRESA</th>
            <th class="bg-primary text-light">SOLICITANTE</th>
            <th class="bg-primary text-light">INICIO</th>
            <th class="bg-primary text-light">FIN</th>
            <th class="bg-primary text-light" style="width: 25%">TRABAJO A REALIZAR</th>
            <th class="bg-primary text-light">REPRESENTANTE</th>
            <th class="bg-primary text-light">TRABAJADORES</th>
            <th class="bg-primary text-light">OBSERVACIONES</th>
            <th class="bg-primary text-light">ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $r)
            <tr>
                <td>{{ optional($r->Local)->lcal_nombre_comercial }}</td>
                <td>{{ $r->pmtt_id }}</td>
                <td>{{ $r->pmtt_empresa }}</td>
                <td>{{ $r->pmtt_solicitante }}</td>
                <td style="white-space: nowrap">{{ $r->pmtt_vigencia_inicial }}</td>
                <td style="white-space: nowrap">{{ $r->pmtt_vigencia_final }}</td>
                <td>{{ $r->pmtt_trabajo }}</td>
                <td>{{ $r->pmtt_representante }}</td>
                <td>{!! nl2br($r->pmtt_listado_trabajadores) !!}</td>
                <td>{{ $r->pmtt_observaciones }}</td>
                <td>{{ $r->pmtt_estado }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="10" class="text-center"> Total </th>
            <th class="text-center">{{ count($records) }}</th>
        </tr>
    </tfoot>

</table>
