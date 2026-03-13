

<h4 class="text-uppercase">Permisos temporales</h4>
<small>Periodo del {{$inicio->format('Y-m-d') }} al {{$fin->format('Y-m-d')}}</small>

<br>
<br>

<table class="table table-bordered table-condensed">

    <thead>
    <tr>
        <th class="bg-primary text-light">FOTO</th>
        <th class="bg-primary text-light">LOCAL</th>
        <th class="bg-primary text-light">NOMBRE</th>
        <th class="bg-primary text-light">CARGO</th>

        <th class="bg-primary text-light">INICIO</th>
        <th class="bg-primary text-light">FIN</th>

        <th class="bg-primary text-light">ESTADO</th>
        <th class="bg-primary text-light">COMENTARIO</th>
        <th class="bg-primary text-light">GAFETE</th>
        <th class="bg-primary text-light">FORMATO</th>
    </tr>
    </thead>
    <tbody>
        @foreach($records as $r)
        <tr style="page-break-inside:avoid">
            <td><img src="{{$r->ptmp_thumb_web}}" alt="" class="img-thumbnail" width="40px"></td>
            <td>{{optional($r->Local)->lcal_nombre_comercial}}</td>
            <td>{{$r->ptmp_nombre}}</td>
            <td>{{$r->Cargo->crgo_descripcion}}</td>
            <td style="white-space: nowrap">{{$r->ptmp_vigencia_inicial}}</td>
            <td style="white-space: nowrap">{{$r->ptmp_vigencia_final}}</td>
            <td>{{$r->ptmp_estado}}</td>
            <td>{{$r->ptmp_comentario}}</td>
            <td>{{  $r->GafetePreimpreso ? $r->GafetePreimpreso->gfpi_numero : ""   }}</td>
            <td><span class="btn btn-primary btn-formato" data-id="{{$r->ptmp_id}}"> <i class="fa fa-file-pdf-o"></i> Formato</span></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="9" class="text-center"> Total </th>
        <th class="text-center">{{ count($records) }}</th>
    </tr>
    </tfoot>

</table>


<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-formato').off().on('click',function(){
            let id = $(this).data('id');
            downloadFormatoPdf(id);
        });


        function downloadFormatoPdf(id){
            let $this = this;
            let url = '{{url('permiso-temporal/formato-oficial-pdf')}}/'+id;

            window.open(url,'blank');

        }

    });

</script>
