<div class="container">
{{ Form::model($cargo,['id'=>'form-cargo', 'url'=>$url, 'class'=>'form form-horizontal'])}}

    {{Form::hidden('id',null,['class'=>'form-control'])}}

    @foreach($accesos as $a)
        <div class="checkbox checkbox-custom">
            {{-- {{dd($selected)}} --}}
            {{Form::checkbox('accesos['.$a->cacs_id.']',$a->cacs_id, in_array($a->cacs_id,$selected),['id'=>'accesos['.$a->cacs_id.']'] )}}

            <label data-role="{{$a->cacs_id}}" for="{{'accesos['.$a->cacs_id.']'}}">
                {{$a->cacs_descripcion}}
            </label>
        </div>
    @endforeach



{{ Form::close()}}
</div>
<script type="text/javascript">

    $(document).ready(function(){

        var jModal = {

            modal:  $('#modal-form'),
            form:  '#form-cargo',

            init: function(){

                let $this = this;

                $('#modal-btn-ok',$this.modal).click(function(){
                    $this.handleSubmit();
                });

                $('#myModalLabel',$this.modal).html('Accesos de <span class="text-custom">{{$cargo->crgo_descripcion}}</span>');

            },

            handleSubmit: function(){
                let $this = this;
                let url = $($this.form).attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $('#form-cargo').serialize(),
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
