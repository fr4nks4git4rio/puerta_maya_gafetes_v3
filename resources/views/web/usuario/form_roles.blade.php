<div class="container">
{{ Form::model($usuario,['id'=>'form-usuario', 'url'=>$url, 'class'=>'form form-horizontal'])}}

    {{Form::hidden('id',null,['class'=>'form-control'])}}

    @foreach($roles as $r)
        <div class="checkbox checkbox-custom">

            {{Form::checkbox('roles['.$r->name.']',$r->name, $usuario->hasRole($r->name),['id'=>'roles['.$r->name.']'] )}}

            <label data-role="{{$r->name}}" for="{{'roles['.$r->name.']'}}">
                {{$r->name}}
            </label>
        </div>
    @endforeach



{{ Form::close()}}
</div>
<script type="text/javascript">

    $(document).ready(function(){

        var jModal = {

            modal:  $('#modal-form'),
            form:  '#form-usuario',

            init: function(){

                let $this = this;

                $('#modal-btn-ok',$this.modal).click(function(){
                    $this.handleSubmit();
                });

                $('#myModalLabel',$this.modal).html('Seleccionar roles de <span class="text-custom">{{$usuario->name}}</span>');

                // $('#form-usuario').iCheck({
                //     checkboxClass: 'icheckbox_square',
                //     radioClass: 'iradio_square',
                // });

            },

            handleSubmit: function(){
                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $('#form-usuario').serialize(),
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);
                            $('#modal-btn-close').click();
                            dTables.oTable.draw();

                        }else{
                            APAlerts.error(res.message);
                            handleFormErrors($this.form,res.errors);
                        }
                    },
                });

            }
        };

        jModal.init();
    });

</script>
