<h4 class="text-uppercase">Gafetes desactivados</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light text-center">LOCAL</th>
        <th class="bg-primary text-light text-center">TIPO</th>
        <th class="bg-primary text-light text-center">REFERENCIA</th>
        <th class="bg-primary text-light text-center">TARJETA</th>
        <th class="bg-primary text-light text-center">FECHA DESACTIVACIÓN</th>

    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td>{{optional($r->Local)->lcal_nombre_comercial}}</td>
            <td class="text-uppercase text-center">{{$r->tipo}}</td>
            <td class="text-center">{{$r->ref_id}}</td>
            <td class="text-center">{{$r->numero_rfid}}</td>
            <td style="white-space: nowrap" class="text-center">{{$r->disabled_at->format('Y-m-d')}}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="4" class="text-center"> Total </th>
        <th class="text-center">{{ count($records) }}</th>
    </tr>
    </tfoot>

</table>
