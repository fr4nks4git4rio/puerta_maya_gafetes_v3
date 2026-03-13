<div class="container">
{{ Form::model($usuario,['id'=>'form-usuario', 'url'=>$url, 'class'=>'form form-horizontal'])}}

    {{Form::hidden('id',null,['class'=>'form-control'])}}

    <div class="form-group row">

        {{Form::label('name','Nombre',['class'=>'col-sm-4 control-label'])}}

        <div class="col-sm-8">
            {{Form::text('name',null,['class'=>'form-control'])}}
        </div>

    </div>

    <div class="form-group row">

        {{Form::label('email','Email',['class'=>'col-sm-4 control-label'])}}

        <div class="col-sm-8">
            {{Form::text('email',null,['class'=>'form-control'])}}
        </div>

    </div>

    <div class="form-group row">

        {{Form::label('telefono','Teléfono',['class'=>'col-sm-4 control-label'])}}

        <div class="col-sm-8">
            {{Form::text('telefono',null,['class'=>'form-control'])}}
        </div>

    </div>

    <div class="form-group row">

        {{Form::label('usr_lcal_id','Local Asignado',['class'=>'col-sm-4 control-label'])}}
        <div class="col-sm-8">
        {!! Form::select('usr_lcal_id', $locales ,  null , ["class"=>"form-control select2-control filter-control",
                                    "placeholder" => "Local", "style" => "width: 100%" ])!!}
        </div>
    </div>

    <div class="form-group row">

        {{Form::label('password','Contaseña',['class'=>'col-sm-4 control-label'])}}

        <div class="col-sm-8">
            {{Form::password('password',['class'=>'form-control'])}}
        </div>

    </div>

    <div class="form-group row">

        {{Form::label('repeat','Repetir Contraseña',['class'=>'col-sm-4 control-label'])}}

        <div class="col-sm-8">
            {{Form::password('repeat',['class'=>'form-control'])}}
        </div>

    </div>


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

                setTimeout(() => {
                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });
                }, 1000);

            },

            handleSubmit: function(){
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
                          dTables.oTable.draw();
                         // $('body').trigger('usuario:added');
                          $('#modal-btn-close').click();

                        }else{

                            if(typeof res.message !== "undefined"){
                                APAlerts.error(res.message);
                                handleFormErrors($this.form,res.errors);
                            }else{
                                APAlerts.error(res);
                            }

                        }
                    },
                });

            }
        };

        jModal.init();
    });

</script>
