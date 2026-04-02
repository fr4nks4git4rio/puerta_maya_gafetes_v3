<table class="table-bordered table-condensed mb-2 float-right">
    <tr>
        <td><b>Total Motos:</b></td>
        <td><b>{{ $totales_asignados->cantidad_motos }}</b></td>
    </tr>
    <tr>
        <td><b>Total Autos:</b></td>
        <td><b>{{ $totales_asignados->cantidad_autos }}</b></td>
    </tr>
</table>
<h4 class="text-uppercase">Asignación de permisos de estacionamiento</h4>
<small>
    @if ($local)
        Local: {{ $local }}
    @endif
    @if ($empleado)
        @if ($local)
            &nbsp;&nbsp;
        @endif
        Empleado: {{ $empleado }}
    @endif
</small>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
        <tr>
            <th class="bg-primary text-light">LOCAL</th>
            <th class="bg-primary text-light">CANT. Estacionamientos</th>
            <th class="bg-primary text-light">NOMBRE EMPLEADO</th>
            <th class="bg-primary text-light">PERMISO AUTO</th>
            <th class="bg-primary text-light">PERMISO MOTO</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        ?>
        @foreach ($records as $recordsLocal)
            @foreach ($recordsLocal as $index => $r)
                <?php
                $count++;
                ?>
                <tr style="page-break-inside:avoid">
                    @if ($index == 0)
                        <td rowspan="{{ count($recordsLocal) }}">{{ $r->local }}</td>
                        <td rowspan="{{ count($recordsLocal) }}">
                            <p style="margin-bottom: 0">Motos: {{ $r->lcal_espacios_motos }}</p>
                            <p>Autos: {{ $r->lcal_espacios_autos }}</p>
                        </td>
                    @endif
                    <td>{{ $r->empleado }}</td>
                    <td class="text-center">{{ $r->con_permisos_auto ? 'X' : '' }}</td>
                    <td class="text-center">{{ $r->con_permisos_auto ? 'X' : '' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4"><b>TOTAL</b></th>
            <th><b>{{ $count }}</b></th>
        </tr>
    </tfoot>

</table>
