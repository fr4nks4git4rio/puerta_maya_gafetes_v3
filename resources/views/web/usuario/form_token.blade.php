<div class="container">


    <div class="row">

        <div class="col-sm-4">
            <b>URL de acceso</b>
        </div>

        <div class="col-sm-8">
            <p class="alert alert-info">
            {{ url('door-rc') }}/<span class="token-holder text-primary">{{$usuario->door_token}}</span>
            </p>
        </div>

    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-sm-4">
            <div class="btn btn-primary" id="btn-refresh-token"> Generar Nuevo Token</div>
        </div>
        <div class="col-sm-4">
            <div class="btn btn-primary" id="btn-unset-token"> Eliminar Token</div>
        </div>
    </div>


</div>
<script type="text/javascript">

    $(document).ready(function(){

        var jModal = {

            modal:  $('#modal-form'),
            form:  '#form-usuario',

            init: function(){

                let $this = this;


                $('#btn-refresh-token',$this.modal).click(function(){
                    $('.token-holder').html("&nbsp;&nbsp;<i class='fa fa-spinner fa-spin'></i>");
                    $.getJSON('{{$url_refresh_token}}',function(json){
                        $('.token-holder').html(json.door_token);
                    })
                });

                $('#btn-unset-token',$this.modal).click(function(){
                    $('.token-holder').html("&nbsp;&nbsp;<i class='fa fa-spinner fa-spin'></i>");
                    $.getJSON('{{$url_unset_token}}',function(json){
                        $('.token-holder').html(json.door_token);
                    })
                });
                {{--$('#myModalLabel',$this.modal).html('Seleccionar roles de <span class="text-custom">{{$usuario->name}}</span>');--}}

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
