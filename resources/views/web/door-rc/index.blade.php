@extends('layouts.main_vertical_guest')

@section('title')
<span class="ti-unlock">&nbsp;</span> Control Remoto de Puertas
@endsection

@section('content')

<div class="row">
    <div class="col-md-8 col-md-push-2">
        <div class="card card-color">
            <div class="card-heading bg-primary">
                <h3 class="card-title text-white">Seleccione la puerta a abrir</h3>
            </div>
            <div class="card-body">

                <table class="table table-bordered">
                    <tr class="text-center">
                        <th>Puerta</th>
                        <th>Tipo</th>
                        <th>Abrir</th>
                    </tr>


                @foreach($puertas as $puerta)

                        <tr>
                            <td>{{$puerta->door_nombre}}</td>
                            <td class="text-center"> <span class="h4">@if($puerta->door_tipo == 'PERSONAL') <i class="ti-user"></i>  @else <i class="ti-car"></i> @endif</span>  </td>
                            <td><div class="btn btn-primary btn-block btn-open-door" data-id="{{$puerta->door_id}}"> <i class="ti-unlock"></i> </div></td>
                        </tr>

                @endforeach

                </table>



            </div>
        </div>
    </div>
</div>



<script>

$(document).ready(function(){

    $('.btn-open-door').click(function(){
        let btn = this;
        let door_id = $(this).data('id');

        let url = '{{ url('door-rc/open-door') }}/{{$usuario->door_token}}/' +  door_id;

        $.ajax({
            url: url,
            method: 'POST',
            beforeSend:function(){
                $('.ti-unlock',btn).removeClass('ti-unlock').addClass('fa fa-gear fa-spin');
            },
            success: function (res) {

                if(res.success === true) {
                    APAlerts.success(res.message);
                }else{

                    if(typeof res.message !== "undefined"){
                        APAlerts.error(res.message);
                    }else{
                        APAlerts.error(res);
                    }

                }
            },

            complete: function(){
                $('.fa-gear',btn).removeClass('fa fa-gear fa-spin').addClass('ti-unlock');
            }
        });


    });

    // $('#open-modal').click(function(){
    //     APModal.open({dom:'modal',html:'html'});
    // });

});

</script>

@endsection
