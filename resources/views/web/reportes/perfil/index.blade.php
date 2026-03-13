@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-account-box-o">&nbsp;</span>Perfil
@endsection

@section('content')

    <div class="row">

        <div class="col-6">

            <div class="card card-color">
                <div class="card-heading bg-custom">
                    <h3 class="card-title m-0 text-white">Información del usuario</h3>
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        {!! Form::label('usr_name', 'Nombre', ['class' => 'col-4 control-label']); !!}
{{--                        <div class="col-2 d-none">--}}
{{--                            {!! Form::text('usr_tratamiento',$user->usr_tratamiento, ["class"=>"form-control", "disabled" => true]);!!}--}}
{{--                        </div>--}}
                        <div class="col-8">
                            {!! Form::text('usr_name',$user->name, ["class"=>"form-control", "disabled" => true]);!!}
                        </div>
                    </div>
                    <div class="form-group row">
                        {!! Form::label('usr_email', 'Email', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::text('usr_email',$user->email, ["class"=>"form-control", "disabled" => true]);!!}
                        </div>
{{--                        <div class="col-1">--}}
{{--                            @if($user->email_valid === 1)--}}
{{--                                <span class="text-success zmdi zmdi-check"></span>--}}
{{--                            @else--}}
{{--                                <span class="text-warning zmdi zmdi-alert-circle-o"></span>--}}
{{--                            @endif--}}
{{--                        </div>--}}
                    </div>

                    <div class="form-group row">
                        {!! Form::label('usr_telefono', 'Teléfono', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::text('usr_telefono',$user->telefono, ["class"=>"form-control", "disabled" => true]);!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('usr_local', 'Local Asignado', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::text('usr_local',$user->Local->lcal_nombre_comercial ?? 'Sin local asignado', ["class"=>"form-control", "disabled" => true]);!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('usr_roles', 'Roles Asignados', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">

{{--                            <ul>--}}
                                @foreach($user->Roles as $role)
{{--                                    <li>{{$role->name}}</li>--}}
                                    <span class="zmdi zmdi-check text-success"></span> {{$role->name}} <br>
                                @endforeach
{{--                            </ul>--}}

                        </div>
                    </div>
{{--                    <div class="form-group row">--}}
{{--                        {!! Form::label('usr_idioma', 'Prefered language', ['class' => 'col-4 control-label']); !!}--}}
{{--                        <div class="col-8">--}}
{{--                            {!! Form::select('usr_idioma', ['en'=>'English','es'=>'Español'],  $user->usr_idioma , ["class"=>"form-control", "disabled" => true]);!!}--}}
{{--                        </div>--}}
{{--                    </div>--}}



                </div>
            </div>


        </div>
        <div class="col-6">

            <div class="card card-color d-none">
                <div class="card-heading bg-info">
                    <h3 class="card-title m-0 text-white">Change password</h3>
                </div>
                <div class="card-body">


                    {!! Form::open(['id' => 'form-change-password','url' =>$url_change_password , 'class' => 'form-horizontal']) !!}

                    {!! Form::hidden('usr_id', $user->id, ["class" => "form-control", "placeholder"=>""]) !!}




                    <div class="form-group row">
                        {!! Form::label('current_password', 'Current password', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::password('current_password', ["class"=>"form-control", "placeholder" => ""]);!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('new_password', 'New password', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::password('new_password', ["class"=>"form-control", "placeholder" => ""]);!!}
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('repeat_password', 'Repeat password', ['class' => 'col-4 control-label']); !!}
                        <div class="col-8">
                            {!! Form::password('repeat_password', ["class"=>"form-control", "placeholder" => ""]);!!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col push-right">
                            <div class="btn btn-primary waves-custom" id="btn-change-password"> Update password </div>
                        </div>
                    </div>





                    {!! Form::close() !!}



                </div>
            </div>

        </div>
    </div>



    <script type="text/javascript">
        $(document).ready(function(){

            jPerfil = {

                user: {!!$user!!},

                init: function(){
                    let $this = this;

                    $('#btn-change-password',$this.modal).click(function(){
                        $this.submitChangePassword();
                    });

                },


                submitChangePassword: function(){
                    let form_selector = '#form-change-password';
                    let $this = this;
                    let url = $(form_selector).attr('action');

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: $(form_selector).serialize(),
                        beforeSend:function(){
                            $('.input-error').remove();
                        },
                        success: function (res) {

                            if(res.success === true) {
                                APAlerts.success(res.message);
                                location.reload();

                            }else{

                                if(typeof res.message !== "undefined"){
                                    APAlerts.error(res.message);
                                    handleFormErrors(form_selector,res.errors);
                                }else{
                                    APAlerts.error(res);
                                }

                            }
                        }
                    });

                }
            };

            jPerfil.init();
        });
    </script>

@endsection
