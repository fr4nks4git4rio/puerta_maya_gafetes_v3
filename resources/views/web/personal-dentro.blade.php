@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-accounts">&nbsp;</span> Estado de Personal
@endsection

@section('content')
    <div class="row p-t-10">
        <div class="col-md-6 col-xs-12 m-b-30">
            <div class="card">
                <div class="card-header bg-info align-content-center">
                    <h2 style="color: white">
                        Cant. motos en plaza
                        <span class="fa fa-motorcycle float-right fa-2x"></span>
                    </h2>
                </div>
                <div class="card-body text-center">
                    <span class="fa-5x">{{ cantidad_motos_dentro() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12 m-b-30">
            <div class="card">
                <div class="card-header bg-info align-content-center">
                    <h2 style="color: white">
                        Cant. autos en plaza
                        <span class="fa fa-automobile float-right fa-2x"></span>
                    </h2>
                </div>
                <div class="card-body text-center">
                    <span class="fa-5x">{{ cantidad_autos_dentro() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12 m-b-30">
            <div class="card">
                <div class="card-header bg-info align-content-center">
                    <h2 style="color: white">
                        Cant. total PAX en plaza
                        <span class="fa fa-users float-right fa-2x"></span>
                    </h2>
                </div>
                <div class="card-body text-center">
                    <span class="fa-5x">{{ count(personas_dentro_de_plaza()) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="card">
                <div class="card-header bg-info align-content-center">
                    <h2 style="color: white">
                        Cant. empleados en plaza
                        <span class="fa fa-user-plus float-right fa-2x"></span>
                    </h2>
                </div>
                <div class="card-body text-center">
                    <span class="fa-5x">{{ count(personas_dentro_de_plaza_puerta_maya()) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
