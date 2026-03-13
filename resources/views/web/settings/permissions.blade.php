<style>
.btn-edit-permission, .btn-delete-permission{
    display: none;
}

.list-group-item:hover > .btn-edit-permission {
  display: block;
}

.list-group-item:hover > .btn-delete-permission {
  display: block;
}

</style>

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            <b>Permisos</b>
            <span id="btn-new-permission" class="btn btn-default pull-right"><i class="pli-add"></i> Nuevo Permiso</span>
        </h3>

    </div>
    <div class="panel-body">

        <!--Extra Large-->
        <!--===================================================-->
        <div class="list-group">
            @foreach($permissions as $p)
                <a class="list-group-item list-permission" href="#">
                    {{$p->name}}


                    <span class="btn btn-danger btn-xs btn-delete-permission mar-hor pull-right"
                        data-toggle="tooltip" title="Borrar Permiso" data-permission="{{$p->id}}" data-name="{{$p->name}}">
                        <i class="pli-trash"></i>
                    </span>

                    <span class="btn btn-warning btn-xs btn-edit-permission pull-right"
                       data-toggle="tooltip" title="Editar Permiso" data-permission="{{$p->id}}">
                       <i class="pli-pencil"></i>
                    </span>

                </a>

            @endforeach
        </div>
        <!--===================================================-->

    </div>
</div>

<script type="text/javascript">

    $(document).ready(function(){

        var jForm = {

            init: function(){
                let $this = this;

                $this.activateTooltips();
                $this.handleNewRecord();
                $this.handleEditRecord();
                $this.handleDeleteRecord();


            },

            activateTooltips: function(){
                $("*[data-toggle='tooltip']").tooltip();
            },

            handleNewRecord: function(){
                let $this = this;

                $('#btn-new-permission').off().on('click',function(ev){

                    APModal.open({
                        dom: jSettings.modal_dom,
                        title: 'Crear Permiso',
                        url: '{{url('settings/permission-form')}}'
                    });
                });

            },

            handleEditRecord: function(){
                let $this = this;

                $('.btn-edit-permission').off().on('click',function(ev){
                    let permission = $(this).data('permission');

                    APModal.open({
                        dom: jSettings.modal_dom,
                        title: 'Editar Permiso',
                        url: '{{url('settings/permission-form')}}/'+permission
                    });
                });

            },

            handleDeleteRecord: function(){

                $('.btn-delete-permission').off().on('click',function(ev){
                    let permission = $(this).data('permission');
                    let name = $(this).data('name');

                    APAlerts.confirm({
                        message: '¿Eliminar el permiso <b>'+name + '</b>?',
                        callback: function(){
                            let PostData = {id: permission};

                            $.ajax({
                                url: '{{url('settings/delete-permission')}}',
                                method: 'POST',
                                data: PostData,
                                success: function (res) {
                                    if(res.success === true) {
                                        APAlerts.success(res.message);
                                        jSettings.printPermissions();
                                    }else{
                                        APAlerts.error(res.message);
                                    }
                                }
                            });

                        }
                    });
                });

            }

        };

        jForm.init();







        // $('.list-role').off().on('click',function(ev){

        //     $('.active','.list-group').removeClass('active');
        //     $(this).addClass('active');

        // });

    })

</script>
