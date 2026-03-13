<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Team IMBA">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}">

    <title>{{ env('APP_NAME','APPLICATION NAME') }}</title>


    <!--STYLESHEET-->
    <!--=================================================-->

    <link href="{{ asset('css/bootstrap.min.css') }} " rel="stylesheet">
    <link href="{{ asset('css/icons.css') }} " rel="stylesheet">
    <link href="{{ asset('css/style.css') }} " rel="stylesheet">

    <script src="{{ asset('js/modernizr.min.js') }}"></script>

    <style>
        html{
            background-image: url('images/splash/splash002.jpg');
            background-attachment: fixed;
        }

        body{
            background-color: transparent;
            /* background-blend-mode: lighten; */
        }


    </style>

</head>

<body>

    <div class="text-center logo-alt-box">
        {{-- <a href="#" class="logo">PUERTA MAYA</a>

        <br> --}}
        <img src="{{asset('images/pm_logo_2.png')}}" alt="">
        <h5 class="logo">Gestión de Gafetes</h5>
    </div>


    {{-- <div id="container" class="cls-container"> --}}

        {{-- <div id="bg-overlay" class="bg-img" style="background-image: url('img/bg/bg001.jpg');"></div> --}}

        <div class="wrapper-page">

        	<div class="m-t-30 card card-body">
                <div class="form-group m-t-30 m-b-0">
                    <div class="col-sm-12 text-center"><h4>Inicio de sesión estándar</h4></div>
                </div>



                <div class="p-2">
                    <form method="POST" class="form-horizontaol mt-10" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf

						<div class="form-group ">
                            <div class="col-12">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   placeholder="email@address.com"  name="email" value="{{ old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    placeholder="Contraseña" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group ">
                            <div class="col-12">
                                <div class="checkbox checkbox-custom">
                                    <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
		                            <label for="remember">Recuérdame</label>
                                </div>
                            </div>
                        </div>

						<div class="form-group text-center m-t-30">
                            <div class="col-12">
                                <button class="btn btn-danger btn-lg btn-block" type="submit">Ingresar</button>
                            </div>
                        </div>

                        {{-- <div class="form-group m-t-30 m-b-0">
                            <div class="col-sm-12">
                                <a href="{{ route('password.request') }}" class="btn-link mar-rgt">Olvidaste la contraseña ?</a>
                            </div>
                        </div> --}}

                        <div class="form-group m-t-30 m-b-0">
                            <div class="col-sm-12 text-center"><h4>Inicio de sesión con cuenta de Google</h4></div>
                        </div>

                        <div class="form-group m-b-0 text-center">
                            <div class="col-sm-12">
{{--                                <button type="button" class="btn btn-facebook waves-effect waves-light m-t-20 d-none"><i class="fa fa-facebook m-r-5"></i> Facebook--}}
{{--                                </button>--}}
{{--                                <button type="button" class="btn btn-twitter waves-effect waves-light m-t-20 d-none"><i class="fa fa-twitter m-r-5"></i> Twitter--}}
{{--                                </button>--}}
                                    <a href="{{url("login-google")}}"  type="button" class="btn btn-googleplus waves-effect waves-light m-t-20"><i class="fa fa-google-plus m-r-5"></i> Google+
                                </a>
                            </div>
                        </div>



					</form>

                </div>
            </div>
        </div>



    {{-- </div> --}}
    <!--===================================================-->
    <!-- END OF CONTAINER -->


    </body>
</html>
