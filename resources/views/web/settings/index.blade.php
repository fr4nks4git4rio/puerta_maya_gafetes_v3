@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-settings">&nbsp;</span>Configuraciones de la Aplicación
@endsection

@section('content')


    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">

                    <!--Nav tabs-->
                    <ul class="nav nav-tabs mt-2">
                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-settings" aria-expanded="true"
                               class="nav-link show text-center active">
                                <b>Variables</b></a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" href="#lft-tab-generalinfo" aria-expanded="true"
                               class="nav-link show text-center">
                                <b>Información general </b></a>
                        </li>
                        <li class="nav-item d-none">
                            <a data-toggle="tab" href="#lft-tab-permisos" aria-expanded="false"
                               class="nav-link show text-center" id="nav-permisos">
                                <b>Permisos</b></a>
                        </li>
                        @if( env('APP_DEBUG', false) == true)
                            <li class="nav-item">
                        @else
                            <li class="nav-item d-none">
                                @endif

                                <a data-toggle="tab" href="#lft-tab-roles" aria-expanded="false"
                                   class="nav-link show text-center" id="nav-roles">

                                    <b>Roles</b></a>
                            </li>

                    </ul>

                    <!--Tabs Content-->
                    <div class="tab-content">

                        <!--Settings-->
                        <div id="lft-tab-settings" class="tab-pane show active">

                            {{-- ////////////////////////////////////////////// --}}
                            {{--@php--}}
                                {{--$key = 'gft_tarifa_1';--}}
                            {{--@endphp--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-2">--}}
                                    {{--<div class="text-right mar-top">--}}
                                        {{--Tarifa de Gafete por <b>Primera Vez</b>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-3">--}}
                                    {{--<input type="text" class="form-control text-right" id="{{$key}}"--}}
                                           {{--value="{{settings()->get($key, '0')}}">--}}
                                {{--</div>--}}
                                {{--<div class="col-2">--}}
                                    {{--<div class="btn btn-custom waves-effect btn-save-setting"--}}
                                         {{--data-setting_key="{{$key}}">--}}
                                        {{--<i class="zmdi zmdi-check"></i> Guardar--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{-- <div class="col-4">--}}
                                    {{--<p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen.</p>--}}
                                {{--</div> --}}
                            {{--</div>--}}
                            {{--<hr>--}}

                            {{-- ////////////////////////////////////////////// --}}
                            {{--@php--}}
                                {{--$key = 'gft_tarifa_2';--}}
                            {{--@endphp--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-2">--}}
                                    {{--<div class="text-right mar-top">--}}
                                        {{--Tarifa de Gafete por <b>Reposición</b>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-3">--}}
                                    {{--<input type="text" class="form-control text-right" id="{{$key}}"--}}
                                           {{--value="{{settings()->get($key, '0')}}">--}}
                                {{--</div>--}}
                                {{--<div class="col-2">--}}
                                    {{--<div class="btn btn-custom waves-effect btn-save-setting"--}}
                                         {{--data-setting_key="{{$key}}">--}}
                                        {{--<i class="zmdi zmdi-check"></i> Guardar--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{-- <div class="col-4">--}}
                                    {{--<p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>--}}
                                {{--</div> --}}
                            {{--</div>--}}
                            {{--<hr>--}}

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_acceso_pvez';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Acceso por <b>Primera Vez</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_acceso_reposicion';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Acceso por <b>Reposición</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_est_auto_pvez';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Estacionamiento para Autos por <b>Primera Vez</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_est_auto_reposicion';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Estacionamiento para Autos por <b>Reposición</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_est_moto_pvez';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Estacionamiento para Motos por <b>Primera Vez</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'gft_est_moto_reposicion';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        Tarifa de Gafete de Estacionamiento para Motos por <b>Reposición</b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'tipo_cambio';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        <b>Tipo de cambio </b>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control text-right" id="{{$key}}"
                                           value="{{settings()->get($key, '0')}}">
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                {{-- <div class="col-4">
                                    <p class="text-muted alert alert-info">A esta dirección se enviarán copias de todos los correos que se generen. Para propósitos de soporte.</p>
                                </div> --}}
                            </div>
                            <hr>

                            {{-- ////////////////////////////////////////////// --}}
                            @php
                                $key = 'cfdi_test_mode';
                            @endphp
                            <div class="row">
                                <div class="col-2">
                                    <div class="text-right mar-top">
                                        <b>CFDI</b> Test Mode
                                    </div>
                                </div>
                                <div class="col-3">

                                    {{Form::select($key,['0'=>'Modo productivo','1'=>'Modo de pruebas'], settings()->get($key, '0'),['id'=>$key, 'class' => 'form-control' ] )}}

                                    {{--                                <input type="text" class="form-control text-right" id="{{$key}}"--}}
                                    {{--                                       value="{{settings()->get($key, '0')}}" >--}}
                                </div>
                                <div class="col-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="{{$key}}">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                                <div class="col-4">
                                    <p class="text-muted alert alert-info">Habilita el timbrado de CFDI</p>
                                </div>
                            </div>
                            <hr>


                        </div>

                        <!--Informacion General-->
                        <div id="lft-tab-generalinfo" class="tab-pane">

                            {{-- ////////////////////////////////////////////// --}}
                            <div class="row">
                                <div class="col-12 form-group">
                                    {!! Form::label('Advertencia:') !!}
                                    {!! Form::textarea('advertencia', settings()->get('advertencia') ,["class"=>"form-control ckeditor", 'rows' => 3]);!!}
                                </div>
                                <div class="mr-3 ml-auto mt-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="advertencia">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 form-group">
                                    {!! Form::label('Responsabilidades del Locatario:') !!}
                                    {!! Form::textarea('resp_locatario', settings()->get('resp_locatario') ,["class"=>"form-control ckeditor", 'rows' => 3]);!!}
                                </div>
                                <div class="mr-3 ml-auto mt-2">
                                    <div class="btn btn-custom waves-effect btn-save-setting"
                                         data-setting_key="resp_locatario">
                                        <i class="zmdi zmdi-check"></i> Guardar
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!--Roles y Permisos-->
                        <div id="lft-tab-permisos" class="tab-pane show">
                            {{-- <p class="text-main text-semibold">Roles y Permisos</p> --}}

                            <div class="row">
                                <div class="col-md-6">
                                    <div id="permissions-container">
                                    </div>
                                </div>
                            </div>


                        </div>

                        <!--Roles y Permisos-->
                        <div id="lft-tab-roles" class="tab-pane show">
                            {{-- <p class="text-main text-semibold">Roles y Permisos</p> --}}
                            <div id="roles-container">
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            jSettings = {

                modal_dom: 'modal-form',

                init: function () {
                    let $this = this;

                    $this.handleSetSetting();

                    $('#nav-roles').click(function (ev) {
                        $this.printRoles();
                    });

                    $('#nav-permisos').click(function (ev) {
                        $this.printPermissions();
                    });

                    // setTimeout(() => {
                    //      $('#nav-settings').click();
                    // }, 500);


                },

                printRoles: function () {
                    let $this = this;
                    let url = '{{url('settings/roles-view')}}';
                    $('#roles-container').html(ajaxLoader());
                    ajax_update(url, '#roles-container');

                },

                printPermissions: function () {
                    let $this = this;
                    let url = '{{url('settings/permissions-view')}}';
                    $('#permissions-container').html(ajaxLoader());
                    ajax_update(url, '#permissions-container');

                },

                handleSetSetting: function () {
                    let $this = this;
                    let url = '{{$url}}';

                    $('.btn-save-setting').off().on('click', function () {
                        let key = $(this).data('setting_key');

                        let value = '';
                        switch (key) {
                            case 'advertencia':
                            case 'resp_locatario':
                                value = CKEDITOR.instances[key].getData();
                                break;
                            default:
                                value = $('#' + key).val();
                                break;
                        }

                        let PostData = {
                            key: key,
                            value: value
                        };

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: PostData,
                            success: function (res) {
                                if (res.success === true) {
                                    APAlerts.success(res.message);
                                } else {
                                    APAlerts.error(res.message);
                                }
                            }
                        });


                    });

                }

            };

            jSettings.init();

        });
    </script>
@endsection

