@if(count($comprobantes) > 0)

    <table class="table table-condensed">

        <tr class="bg-light">
            <td colspan="7" class="text-center">Comprobantes a facturar</td>
        </tr>
        <tr class="bg-light">
            <td>&nbsp;</td>
            {{--        <td>Fecha</td>--}}
            <td>Folio</td>
            <td>Referencia</td>
            <td>Forma pago</td>
            <td>Uso</td>
            <td>Importe</td>
        </tr>


        @foreach($comprobantes as $c)
            <tr>
                <td>
                    {{--                <div class="checkbox checkbox-custom">--}}
                    {{-- {{dd($selected)}} --}}
                    {{Form::checkbox('cpag_'.$c->cpag_id, $c->cpag_id , false ,['id'=>'cpag_'.$c->cpag_id , 'class'=>'checkbox-comprobante'] )}}

                    {{--                </div>--}}

                </td>
                {{--            <td>{{$c->cpag_fecha_pago}}</td>--}}
                <td>{{$c->cpag_folio_bancario}}</td>
                <td>{{$c->cpag_aut_bancario}}</td>
                <td>{{$c->FormaPago->descripcion}}</td>
                <td>{{$c->UsoCfdi->descripcion}}</td>
                <td class="text-right">{{number_format($c->cpag_importe_pagado,2)}}</td>
            </tr>
        @endforeach



    </table>

@else

    <p class="info">No se encontraron comprobantes a facturar.</p>

@endif