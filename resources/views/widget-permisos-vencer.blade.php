 <div class="col-sm-6">
        <div class="card card-color">
            <div class="card-heading bg-danger">
                <h3 class="card-title text-white">PERMISOS TEMPORALES POR VENCER</h3>
            </div>
            <div class="card-body">

                @php
                    $enCuantosDias = 3;
                    $records = \App\PermisoTemporal::proximosVencer($enCuantosDias);
                    // dd($records);
                @endphp

                @if(count($records) > 0)

                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Local</th>
                            <th>Nombre</th>
                            <th>Gafete</th>
                            <th>Termino</th>
                        </tr>

                        @foreach($records as $r)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$r->Local->lcal_nombre_comercial}}</td>
                            <td>{{$r->ptmp_nombre}}</td>
                            <td>{{$r->GafetePreimpreso->gfpi_numero }}</td>
                            <td>{{$r->ptmp_vigencia_final}}</td>
                        </tr>
                        @endforeach

                    </table>

                @else
                    <p> No existen permisos a vencer en los próximos 3 días.</p>
                @endif


            </div>
        </div>

    </div>
