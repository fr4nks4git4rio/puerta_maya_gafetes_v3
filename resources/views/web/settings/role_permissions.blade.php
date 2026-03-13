

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">
            Permisos de <b>{{$role->name}}</b>
            <span id="btn-save-permissions" class="btn btn-mint pull-right"><i class="pli-yes"></i> Guardar</span>
        </h3>

    </div>
    <div class="panel-body">
        {{Form::open(['url'=>url('settings/role-set-permissions',$role->id),'id'=>'form-ui']) }}

        <!--Extra Large-->
        <!--===================================================-->
        <div class="list-group">
            @foreach($permissions as $p)

                <label class="list-group-item list-permission" href="#">
                    {{Form::checkbox('permission['.$p->name.']',$p->name, $role->hasPermissionTo($p->name) )}}
                    {{$p->name}}
                </label>

            @endforeach
        </div>
        <!--===================================================-->

    </div>
</div>

<script type="text/javascript">

    $(document).ready(function(){

        var jUi = {

            init: function(){
                let $this = this;

                $this.activateTooltips();
                $this.handleSubmit();

                $('#form-ui').iCheck({
                    checkboxClass: 'icheckbox_square',
                    radioClass: 'iradio_square',
                });


            },

            activateTooltips: function(){
                $("*[data-toggle='tooltip']").tooltip();
            },

            handleSubmit: function(){
                $('#btn-save-permissions').off().on('click',function(){
                    let url = $('#form-ui').attr('action');

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: $('#form-ui').serialize(),
                            success: function (res) {
                                if(res.success === true) {
                                    APAlerts.success(res.message);
                                }else{
                                    APAlerts.error(res.message);
                                }
                            },
                        });
                });

            }
        };

        jUi.init();







        // $('.list-role').off().on('click',function(ev){

        //     $('.active','.list-group').removeClass('active');
        //     $(this).addClass('active');

        // });

    })

</script>
