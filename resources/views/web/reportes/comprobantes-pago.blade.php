<h4 class="text-uppercase">Comprobantes de Pago</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>


<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">FOLIO</th>
        <th class="bg-primary text-light">FECHA PAGO</th>
        <th class="bg-primary text-light">IMPORTE</th>
        <th class="bg-primary text-light">ESTADO</th>
        <th class="bg-primary text-light">COMENTARIO</th>
        <th class="bg-primary text-light">ACCIONES</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td>{{$r->Local ? $r->Local->lcal_nombre_comercial : ''}}</td>
            <td class="text-uppercase">{{$r->cpag_folio_bancario}}</td>
            <td class="text-center">{{\Carbon\Carbon::createFromFormat('Y-m-d',$r->cpag_fecha_pago)->format('d/m/Y')}}</td>
            <td class="text-center">{{$r->cpag_importe_pagado}}</td>
            <td class="text-center">{{$r->cpag_estado}}</td>
            <td class="text-center">{{$r->cpag_comentario_admin}}</td>
            <td class="text-center">
                <a href="#" class="btn btn-success btn-xs" title="Detalles"><span class="fa fa-eye"></span></a>
            </td>
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