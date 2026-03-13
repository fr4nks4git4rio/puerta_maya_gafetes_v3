@extends('layouts.main_vertical')

@section('title')
<span class="fa fa-home">&nbsp;</span>Tablero de inicio
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card card-color">
            <div class="card-heading bg-primary">
                <h3 class="card-title text-white">Bienvenido</h3>
            </div>
            <div class="card-body">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- <p>Sesión inciada con éxito!</p>

            <br> --}}

            <p>Usuario <b>{{ auth()->getUser()->name }}</b></p>

            <p>Local asignado <b> {{ auth()->getUser()->Local->lcal_nombre_comercial ?? 'Ninguno'}}</b></p>

{{--            <p>Tipo de cambio Banxico: {{$tipoCambio}}</p>--}}

            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->getUser()->hasRole('RECEPCIÓN'))
        @include('widget-permisos-vencer')
    @endif
</div>


{{-- <div class="row">
    <div class="col-md-4">
        <dib class="btn btn-mint" id="open-modal"> Open modal</dib>
    </div>
</div> --}}



<script>

$(document).ready(function(){

    // $('#open-modal').click(function(){
    //     APModal.open({dom:'modal',html:'html'});
    // });

});

</script>

@endsection
