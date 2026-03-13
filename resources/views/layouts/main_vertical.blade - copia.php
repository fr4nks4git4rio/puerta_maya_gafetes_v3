<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> --}}
    <meta name="author" content="WifiEmplresarial">

    {{-- <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}"> --}}

    <link rel="icon" type="image/png" sizes="32x32" href="{{asset("images/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset("images/favicon-16x16.png")}}">
    <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}" type="image/x-icon">
    <link rel="icon" href="{{asset("images/favicon.ico")}}" type="image/x-icon">

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
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=PT+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet">

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
            background-color: #deeffd;
            color: gray;
        }

        .menu-title{
            pointer-events: auto;
        }

        .notification-icon{
            font-size: 24px;
            display: inline-block;
            float: left;
            margin-left: -10px;
            margin-right: 8px;
        }

        .notification-count{
            position: fixed;
            float: left;
            margin-top: 22px;
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

    <div  id="wrapper">

        <!-- Top Bar Start -->
        <div class="topbar" style="max-height:60px">

            <!-- LOGO -->
            <div class="topbar-left" style="background-image: linear-gradient(to right, #ffad3b , #ffeed6 ,  #ffeed6, #ffad3b );">
                <div class="text-center">
                    <a href="{{url('/')}}" class="logo">
                        <i class="icon-c-logo">
                            <img src="{{asset("images/pm_logo_2.png")}}" alt="logo" style="height: 40px;">
                            {{-- <img src="{{asset("images/logo-test.png")}}" alt="logo" style="height: 40px;"> --}}
                        </i>
                        {{-- <i class="zmdi zmdi-toys icon-c-logo"></i><span>PUERTA<span>CREDENCIALES</span></span> --}}
                        <span><img src="{{asset("images/pm_logo_2.png")}}" alt="logo" style="height: 40px;">
                        <span>
                            {{-- ><img src="{{asset("images/logo-test.png")}}" alt="logo" style="height:55px;"> --}}
                            <small style="text-shadow: 1px 1px gray; color:#0085c8"><b>GAFETES</b></small>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Button mobile view to collapse sidebar menu -->
            <div class="navbar navbar-default" role="navigation">
                <div class="container-fluid">
                    <div class="">

                        <div class="float-left">

                            <button class="button-menu-mobile open-left waves-effect waves-light">
                                <i class="zmdi zmdi-menu" style="text-shadow: 1px 1px gray;"></i>
                            </button>





                        </div>

                        {{-- <form role="search" class="navbar-right float-right app-search">
                                <input type="text" placeholder="Search..." class="form-control">
                                <a href=""><i class="fa fa-search"></i></a>

                        </form> --}}
                        <div  class="float-left">
                            {{-- <div class="btn btn-sm btn-inverse">TC:1560</div> --}}
                        </div>

                        <ul class="nav navbar-right float-right">

                            @role('LOCATARIO')
                            <li class="list-inline-item">
                                <div class="btn-xs btn-primary btn-trans" style="margin-top: 18px;"><b>{{auth()->user()->Local->lcal_nombre_comercial}}</b></div>
                            </li>

                            <li class="list-inline-item">
                                <div class="btn-xs btn-primary btn-trans" style="margin-top: 18px;">REF BANC: <b>{{auth()->user()->Local->lcal_referencia_bancaria}}</b></div>
                            </li>
                            @endrole

                            <li class="list-inline-item">
                                <div class="btn-xs btn-primary btn-trans" style="margin-top: 18px;">TC: <b>{{settings()->get('tipo_cambio', '0')}}</b></div>
                            </li>

                            @include('notification-component')


                            <li class="dropdown user-box list-inline-item" >
                                <a href="" class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown" aria-expanded="true">
                                    <img src="{{auth()->getUser()->usr_avatar ?? asset('images/logo_user_default.jpg')}}" alt="user-img" class="rounded-circle user-img">
                                    {{-- <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div> --}}
                                    <span class="ic-user" style="color:white">
                                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                        <!--You can use an image instead of an icon.-->
                                        <!--<img class="img-circle img-user media-object" src="img/profile-photos/1.png" alt="Profile Picture">-->
                                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                        <i class="pli-male"></i>
                                        {{-- <b style="text-shadow: 1px 1px gray;">{{auth()->getUser()->name}}</b> --}}
                                        <b style="text-shadow: 1px 1px gray; color:white;" class="d-none d-sm-inline">{{auth()->getUser()->name}} <br>
                                        </b>
{{--                                        <small>{{auth()->getUser()->Local->lcal_nombre_comercial}}</small>--}}
                                     </span>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right" style="top: 65px">
                                    <li><a href="{{ route('perfil') }}" class="dropdown-item"><i class="ti-user m-r-5"></i> Perfil</a></li>
                                    <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti-help m-r-5"></i> Ayuda</a></li>
                                    {{-- <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti-lock m-r-5"></i> Lock screen</a></li> --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">

                                            <i class="ti-close m-r-5"></i>Salir </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                    </li>
                                </ul>
                            </li>

                        </ul>



                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <!-- Top Bar End -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <div class="sidebar-inner slimscrollleft">

                <div id="sidebar-menu">
                    <ul>

                        @include('layouts.nav.top-menu-db-by-roles')

                        {{-- <li class="text-muted menu-title">Navigation</li> --}}

                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>

            </div>
        </div>



        <!-- ============================================================== -->
			<!-- Start right Content here -->
			<!-- ============================================================== -->
			<div class="content-page">
				<!-- Start content -->
				<div class="content">
					<div class="container-fluid">

						<!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="page-title">@yield('title')</h4>
                            </div>
                        </div>

                        <div class="row"  id="page-content">
                            <div class="col-sm-12">

                            @section('content')

                                <p>Master Template</p>

                            @show
                            </div>

                        </div>



                    </div> <!-- container-fluid -->

                </div> <!-- content -->

                <footer class="footer">
                    {{date('Y')}} © {{env("APP_NAME")}}. <span class="d-none d-sm-inline-block">
                        {{-- Design by <a href="http://coderthemes.com/" target="_blank" class="text-muted">Coderthemes --}}
                        </a>
                    </span>
                </footer>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->

    </div>


    <script src="{{ asset('js/jquery.core.js') }}"></script>
    <script src="{{ asset('js/jquery.app.js') }}"></script>

    <script>

        $(document).ready(function(){
            var jMultiRolMenu = {

                items : $('li','#sidebar-menu'),
                roles : {},
                active_role : "",

                init: function(){
                    let $this = this;

                    $this.mapeaMenu();

                    $this.roleHandler();
                },

                mapeaMenu: function(){
                    let $this = this;
                    let current = "";
                    let stack = [];

                    $.each($this.items,function(k,i){

                        if( $(i).hasClass('role-title') ){

                            if(current != ""){
                                $this.roles[current] = stack;
                            }

                            current = $(i).html();
                            stack = [];

                        }else{

                            if( $(i).hasClass('active') ){
                                $this.active_role = current;
                            }
                            stack.push(i);
                        }

                        $this.roles[current] = stack;

                    });

                    $this.openRol($this.active_role);

                },

                openRol: function(role){
                    let $this = this;

                    for(let key in $this.roles ){

                        if (key != role){

                            $this.roles[key].forEach(function(element){
                                $(element).addClass('d-none');
                            });

                        }else{

                            $this.roles[key].forEach(function(element){
                                $(element).removeClass('d-none');
                            });
                        }
                    }

                },

                roleHandler: function(){
                    let $this = this;

                    $('li.role-title','#sidebar-menu').on('click',function(){
                        let role = $(this).html();
                        $this.openRol(role);
                    });

                }


            };

            jMultiRolMenu.init();

        });

    </script>

    </body>

</html>
