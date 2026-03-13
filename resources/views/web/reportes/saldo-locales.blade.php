<h4 class="text-uppercase">Saldo por Locales</h4>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">NOMBRE COMERCIAL</th>
        <th class="bg-primary text-light">RAZÓN SOCIAL</th>
        <th class="bg-primary text-light">SALDO ACTUAL</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    ?>
    @foreach($records as $r)
        <?php
        $total += (float)$r->getSaldos()['saldo_vigente'];
        ?>
        <tr style="page-break-inside:avoid">
            <td>{{$r->lcal_nombre_comercial}}</td>
            <td>{{$r->lcal_razon_social}}</td>
            <td style="text-align: center">{{$r->getSaldos()['saldo_vigente']}}</td>
        </tr>
    @endforeach
    <tr style="page-break-inside:avoid">
        <td colspan="2" style="text-align: right; font-weight: 700">Total:</td>
        <td style="text-align: center; font-weight: 700">{{$total}}</td>
    </tr>
    </tbody>

</table>
