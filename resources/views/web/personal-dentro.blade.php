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
                    <span class="fa-5x" id="cantidad_motos_dentro"></span>
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
                    <span class="fa-5x" id="cantidad_autos_dentro"></span>
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
                    <span class="fa-5x" id="personas_dentro_de_plaza"></span>
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
                    <span class="fa-5x" id="personas_dentro_de_plaza_puerta_maya"></span>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            loadData();
        });

        function loadData() {
            $.ajax({
                url: '{{ url('/load-data-personal-dentro') }}',
                method: 'GET',
                success: function(res) {
                    if (res.success === true) {
                        $('#cantidad_motos_dentro').html(res.data.cantidad_motos_dentro);
                        $('#cantidad_autos_dentro').html(res.data.cantidad_autos_dentro);
                        $('#personas_dentro_de_plaza').html(res.data.personas_dentro_de_plaza);
                        $('#personas_dentro_de_plaza_puerta_maya').html(res.data
                            .personas_dentro_de_plaza_puerta_maya);
                    }
                    setTimeout(() => {
                        loadData();
                    }, 3000);
                },
                error: function(err) {
                    setTimeout(() => {
                        loadData();
                    }, 3000);
                }
            });
        }
    </script>
@endsection
