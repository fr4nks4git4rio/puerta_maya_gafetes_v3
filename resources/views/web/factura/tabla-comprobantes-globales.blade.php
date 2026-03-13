@if(count($comprobantes) > 0)

    <table class="table table-condensed">

        {{--        <tr class="bg-light">--}}
        {{--            <td colspan="7" class="text-center">--}}
        {{--                <b>Comprobantes a facturar</b>--}}
        {{--            </td>--}}
        {{--        </tr>--}}
        <tr class="bg-light">
            <td>&nbsp;</td>
            <td>Local</td>
            <td>Fecha</td>
            <td>Folio</td>
            <td>Referencia</td>
            <td>Forma pago</td>
            <td>Descargar</td>
            <td>Importe</td>
            <td>Acciones</td>
        </tr>


        @foreach($comprobantes as $c)
            <tr>
                <td>
                    {{Form::checkbox('cpag_'.$c->cpag_id, $c->cpag_id , false ,['id'=>'cpag_'.$c->cpag_id , 'class'=>'checkbox-comprobante'] )}}
                </td>
                <td>{{$c->Local->lcal_identificador}} - <b>{{$c->Local->lcal_razon_social}}</b></td>
                <td>{{$c->cpag_fecha_pago}}</td>
                <td>{{$c->cpag_folio_bancario}}</td>
                <td>{{$c->cpag_aut_bancario}}</td>
                <td>{{$c->FormaPago->descripcion}}</td>
                <td><a href="{{asset('storage/comprobantes/'.$c->cpag_file)}}" target="_blank"> Comprobante original</a>
                </td>
                {{--                <td>{{$c->UsoCfdi->descripcion}}</td>--}}
                <td class="text-right">{{number_format($c->cpag_importe_pagado,2)}}</td>
                <td class="text-center">
                    <a href="#{{--{{route('comprobantes.do_factura_manual', $c->cpag_id)}}--}}"
                       data-id="{{$c->cpag_id}}" data-folio="{{$c->cpag_folio_bancario}}"
                       class="btn btn-primary btn-xs comprobantes_pago"
                       title="Factura Manual">
                        <i class="zmdi zmdi-receipt"></i>
                    </a>
                </td>
            </tr>
        @endforeach


    </table>

@else

    <p class="info">No se encontraron comprobantes a facturar.</p>

@endif
