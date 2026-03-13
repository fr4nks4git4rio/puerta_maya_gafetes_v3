<style>
.btn-group-role{
    display: none;
}

.list-group-item:hover > .btn-group-role {
  display: block;
}


</style>

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-heading">
                <h3 class="header-title">
                    <b>Roles del sistema</b>
                    <span id="btn-new-role" class="btn btn-custom waves-effect pull-right">
                        <i class="zmdi zmdi-plus"></i> Crear Rol</span>
                </h3>

            </div>
            <div class="card-body">

                <!--Extra Large-->
                <!--===================================================-->
                <div class="list-group">
                    @foreach($roles as $r)
                        <a class="list-group-item list-role" href="#" data-role="{{$r->name}}">{{$r->name}}

                            <span class="btn-group btn-group-sm pull-right btn-group-role">
                                <span class="btn btn-custom btn-delete-role waves-effect"
                                    data-toggle="tooltip" title="Borrar Rol" data-role="{{$r->id}}" data-name="{{$r->name}}">
                                    <i class="zmdi zmdi-close"></i>
                                </span>

                                <span class="btn btn-custom btn-edit-role waves-effect"
                                    data-toggle="tooltip" title="Editar Rol" data-role="{{$r->id}}">
                                    <i class="zmdi zmdi-edit"></i>
                                </span>

                                <span class="btn btn-custom btn-edit-permissions waves-effect"
                                    data-toggle="tooltip" title="Configurar Permisos" data-role="{{$r->id}}">
                                    <i class="zmdi zmdi-lock"></i>
                                </span>

                                <span class="btn btn-custom btn-edit-navigation waves-effect"
                                    data-toggle="tooltip" title="Configurar Navegación" data-role="{{$r->id}}">
                                    <i class="zmdi zmdi-menu"></i>
                                </span>

                            </span>


                        </a>
                    @endforeach
                </div>
                <!--===================================================-->

            </div>
        </div>
    </div>

    <div class="col-md-6">

        <div id="ui-config-container">


        </div>

    </div>

</div>



<script type="text/javascript">

    $(document).ready(function(){

        $('#btn-new-role').off().on('click',function(ev){
            console.log('hola');
             APModal.open({
                dom: jSettings.modal_dom,
                title: 'Crear Rol',
                url: '{{url('settings/role-form')}}'
            });
        });

        $('.btn-edit-permissions').off().on('click',function(){
            let role = $(this).data('role');
            let url = '{{url('settings/role-permissions-view')}}/'+role;
            $('#ui-config-container').html(ajaxLoader());
            ajax_update(url,'#ui-config-container');
        });

        $('.btn-edit-navigation').off().on('click',function(){
            let role = $(this).data('role');
            let url = '{{url('settings/role-navigation-view')}}/'+role;
            $('#ui-config-container').html(ajaxLoader());
            ajax_update(url,'#ui-config-container');
        });

        $("*[data-toggle='tooltip']").tooltip();

    })

</script>
