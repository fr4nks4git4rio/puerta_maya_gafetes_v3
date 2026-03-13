@extends('layouts.main_vertical')

@section('title')
<span class="ti-info-alt">&nbsp;</span>Información General
@endsection

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card card-color">
            <div class="card-heading bg-primary">
                <h3 class="card-title text-white">Importante</h3>
            </div>
            <div class="card-body">
                {!! settings()->get('advertencia') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-color">
            <div class="card-heading bg-purple">
                <h3 class="card-title text-white">Responsabilidades del Locatario</h3>
            </div>
            <div class="card-body">

                {!! settings()->get('resp_locatario') !!}

            </div>
        </div>
    </div>
</div>

@endsection
