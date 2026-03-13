<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> --}}
    <meta name="author" content="Team IMBA">

    {{-- <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}"> --}}

    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("images/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("images/favicon-16x16.png")}}">

    <title>{{ env('APP_NAME','Gafetes') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--STYLESHEET-->
    <!--=================================================-->

    <link href="{{ asset('css/bootstrap.min.css') }} " rel="stylesheet">
    <link href="{{ asset('css/icons.css') }} " rel="stylesheet">
    <link href="{{ asset('css/style.css') }} " rel="stylesheet">

    <script src="{{ asset('js/modernizr.min.js') }}"></script>

    <!--Open Sans Font [ OPTIONAL ] -->
    {{-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet"> --}}
    {{-- <link href="https://fonts.googleapis.com/css?family=PT+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet"> --}}

    <!--Premium Icons [ OPTIONAL ]-->
    {{-- <link href="{{asset('premium/icon-sets/icons/line-icons/premium-line-icons.min.css')}}" rel="stylesheet">
    <link href="{{asset('premium/icon-sets/icons/solid-icons/premium-solid-icons.min.css')}}" rel="stylesheet"> --}}


    <!--=================================================-->


    <!--Page Load Progress Bar [ OPTIONAL ]-->
    {{-- <link href="{{ asset('css/pace.min.css') }} " rel="stylesheet">
    <script src="{{ asset('js/pace.min.js') }}"></script> --}}


    <!--JAVASCRIPT-->
    <!--=================================================-->


    <script>
        var resizefunc = [];
    </script>


    <!-- jQuery  -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/detect.js') }}"></script>
    <script src="{{ asset('js/fastclick.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <script src="{{ asset('js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('js/jquery.scrollTo.min.js') }}"></script>

    <script src="{{ asset('js/jquery.core.js') }}"></script>
    <script src="{{ asset('js/jquery.app.js') }}"></script>

    <!-- Select2 -->

    <link href="{{ asset('plugins/select2/dist/css/select2.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>


    <!-- DataTables -->
    <link href="{{ asset('plugins/datatables/jquery.dataTables.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/fixedHeader.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" />


    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <link href="{{ asset('plugins/datatables/select.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/datatables/dataTables.select.min.js') }}"></script>


    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>

    <!-- Toastr js -->
    <link href="{{ asset('plugins/toastr/toastr.min.css') }} " rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

    <!-- Sweet Alert css -->
    <link href="{{ asset('plugins/sweetalert2/sweetalert2.css') }} " rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Datepicker -->
    <link href="{{ asset('plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }} " rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js') }}"></script>

    <!-- include blueimp/uploader -->
   {{-- <link href="{!! asset("plugins/uploader/css/jquery.fileupload.css") !!}" rel="stylesheet"> --}}

    <script src="{!! asset("plugins/uploader/js/vendor/jquery.ui.widget.js") !!}"></script>
    <script src="{!! asset("plugins/uploader/js/jquery.iframe-transport.js") !!}"></script>
    {{-- <script src="{!! asset("plugins/uploader/js/jquery.fileupload-process.js") !!}"></script> --}}
    {{-- <script src="{!! asset("plugins/uploader/js/jquery.fileupload-validate.js") !!}"></script> --}}
    <script src="{!! asset("plugins/uploader/js/jquery.fileupload.js") !!}"></script>



    <!-- include tagsinput css/js-->
    <link href="{!! asset("plugins/bootstrap-tagsinput/bootstrap-tagsinput.css") !!}" rel="stylesheet">
    <script src="{!! asset("plugins/bootstrap-tagsinput/bootstrap-tagsinput.js") !!}"></script>

    {{-- <link href="{{ asset('plugins/datatables/scroller.bootstrap4.min.css') }} " rel="stylesheet" type="text/css" /> --}}

    {{-- <link href="{{ asset('plugins/datatables/extensions/Responsive/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}" ></script> --}}

    {{-- <link href="{{ asset('plugins/datatables/extensions/Select/css/select.bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/datatables/extensions/Select/js/dataTables.select.min.js') }}" ></script> --}}

    {{-- <link href="{{ asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" ></script>
    <script src="{{ asset('plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}" ></script> --}}


    <script src="{{ asset('js/apinto.alerts.js')}}"  defer></script>
    <script src="{{ asset('js/apinto.modals.js')}}"  defer></script>
    <script src="{{ asset('js/apinto.helpers.js')}}" defer></script>


    <style type="text/css">
        /* table.dataTable{
            width: 100%;
        }
        .modal-backdrop{
            z-index: 10000;
        }
        .modal{
            z-index: 10001;
        }
        */
         tbody .selected td, body #container .table.dataTable tbody tr.selected {
            /* background-color: #17a2b8; */
            background-color: #fe6271;
            color: white;
        }

        /* table.dataTable tbody>tr.selected, table.dataTable tbody>tr>.selected {
            background-color: #17a2b8;
        } */
    </style>

</head>

<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->

<body class="fixed-left">
{{-- <body> --}}

        <!-- Navigation Bar-->
        <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">

                    <!-- LOGO -->
                    <div class="topbar-left">
                        <a href="{{url('/')}}" class="logo">
                            {{env("APP_NAME")}}
                            <!--<span><img src="assets/images/logo.png" alt="logo" style="height: 20px;"></span>-->
                        </a>
                    </div>
                    <!-- End Logo container-->

                    <div class="navbar-custom navbar-left">
                        <div id="navigation">
                            <!-- Navigation Menu-->

                            {{-- @include('layouts.nav.top-menu-static') --}}
                            @include('layouts.nav.top-menu-db')

                            <!-- End navigation menu  -->
                        </div>
                    </div>


                    <div class="menu-extras">

                        <ul class="nav navbar-right float-right list-inline">


                            <li class="dropdown user-box list-inline-item">
                                <a href="" class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown" aria-expanded="true">
                                    <b class=".d-none" >{{auth()->getUser()->name}}</b>
                                    {{-- <img src="{{asset("images/users/avatar-1.jpg")}}" alt="user-img" class="rounded-circle user-img">
                                    <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div> --}}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="{{ url('profile') }}" class="dropdown-item"><i class="ti-user m-r-5"></i> Perfil</a></li>
                                    {{-- <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti-settings m-r-5"></i> Configuraciones</a></li> --}}
                                    {{-- <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti-lock m-r-5"></i> Lock screen</a></li> --}}
                                    {{-- <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti-power-off m-r-5"></i> Logout</a></li> --}}
                                    <li><a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">

                                            <i class="ti-power-off m-r-5"></i> Cerrar sesión</a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="menu-item">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </div>
                    </div>

                </div>
            </div>

        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">

                        <h4 class="page-title">@yield('title')</h4>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid -->

            <div class="container-fluid"  id="page-content">

                @section('content')

                    <p>Master Template</p>

                @show

            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="pull-none mo-mb-10">
                                {{date('Y')}} © {{env("APP_NAME")}}.
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <ul class="float-right list-inline m-b-0 pull-none">
                                <li class="list-inline-item">
                                    <a href="#">About</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#">Help</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#">Contact</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- End Footer -->

        </div>

    </body>

</html>
