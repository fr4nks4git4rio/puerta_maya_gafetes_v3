{{ Form::model($role,['id'=>'form-record', 'url'=>$url, 'class'=>'form form-horizontal'])}}

    {{Form::hidden('id',null,['class'=>'form-control'])}}

    <div class="form-group">
        {{Form::label('name','Nombre del Rol',['class'=>'col-sm-3 control-label'])}}
        <div class="col-sm-5">
            {{Form::text('name',null,['class'=>'form-control'])}}
        </div>
    </div>
    <div class="form-group">
        {{Form::label('guard_name','Guard',['class'=>'col-sm-3 control-label'])}}
        <div class="col-sm-3">
            {{Form::text('guard_name','web',['class'=>'form-control','readonly'=>true])}}
        </div>
    </div>

{{ Form::close()}}
<script type="text/javascript">

    $(document).ready(function(){

        var jModal = {
            modal:  $('#modal-form'),
            form: '#form-record',

            init: function(){
                let $this = this;

                $('#modal-btn-ok',$this.modal).click(function(){
                    $this.handleSubmit();
                });

            },

            handleSubmit: function(){
                console.log('handleSubmit');
                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $($this.form).serialize(),
                    beforeSend:function(){
                        $('.input-error').remove();
                    },
                    success: function (res) {

                        if(res.success === true) {
                            APAlerts.success(res.message);
                            jSettings.printRoles();
                            $('#modal-btn-close').click();

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
