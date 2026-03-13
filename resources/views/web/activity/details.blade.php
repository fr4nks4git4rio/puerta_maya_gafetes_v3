
<div class="container">
<dl class="row">
    <dt class="col-sm-3"> Clase</dt>
    <dd class="col-sm-9">{{$activity->log_name}}</dd>

{{--    <hr>--}}

    <dt class="col-sm-3">Usuario</dt>
    <dd class="col-sm-9">{!!$activity->name!!}</dd>

{{--    <hr>--}}

    <dt class="col-sm-3">Descripción</dt>
    <dd class="col-sm-9"><i>{!!$activity->description!!}</i></dd>

{{--    <hr>--}}

    <dt class="col-sm-3">Fecha - Hora</dt>
    <dd class="col-sm-9">{!! $activity->created_at !!}</dd>

</dl>

@if($activity->properties != "[]")
    @php
        $data = json_decode($activity->properties,true);
    @endphp

    <hr>
        @if(isset($data['attributes']))
    <div class="row">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header"><b>Datos del registro</b></div>
                <div class="card-body">
                    <dl class="row">
                    @foreach($data['attributes'] as $k=>$v)
                        <dt class="col-sm-3"><b>{{$k}}</b></dt>
                        <dd class="col-sm-9"><small>{{$v}}</small></dd>
                    @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </div>

        @endif

    @if(isset($data['old']))
    <hr>
    <div class="row">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header"><b>Datos anteriores</b></div>
                <div class="card-body">
                    <dl class="row">
                        @foreach($data['old'] as $k=>$v)
                            <dt class="col-sm-3"><b>{{$k}}</b></dt>
                            <dd class="col-sm-9"><small>{{$v}}</small></dd>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @endif

@endif

</div>

<script>

    $('document').ready(function(){
        $('#modal-btn-ok').addClass('hidden');
    });


</script>
