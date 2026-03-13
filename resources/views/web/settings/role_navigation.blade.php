

<div class="card">
    <div class="card-heading">
        <h3 class="header-title">
            Rol: <b>{{$role->name}}</b> > Navegación
            <span id="btn-save-navigation" class="btn btn-primary pull-right waves-effect"><i class="zmdi zmdi-check"></i> Guardar</span>
        </h3>

    </div>
    <div class="card-body">
        {{Form::open(['url'=>url('settings/role-set-navigation',$role->id),'id'=>'form-ui']) }}

        <!--Extra Large-->
        <!--===================================================-->
        <div class="list-group">

            <!--First Level-->
            @foreach($items as $item)
                @php
                     $checked = count($stored->where('navigation_id',$item->id)) >= 1;
                @endphp

                <label class="list-group-item list-navigation" href="#">
                    {{Form::checkbox('navigation['.$item->id.']',$item->title, $checked )}}
                    <i class="{{$item->icon_class}}">&nbsp;</i>
                    {{$item->title_es}}
                    <small class="text-info">{{$item->comment}}</small>
                </label>

                <!--Second Level-->
                @foreach( $item['children']->sortBy('weight') as $child)
                    @php
                        $checked = count($stored->where('navigation_id',$child->id)) >= 1;
                        // dd($child);
                    @endphp
                    <label class="list-group-item list-navigation" href="#">
                        <span class="pl-2">
                            {{Form::checkbox('navigation['.$child->id.']',$child->title, $checked )}}
                            <i class="{{$child->icon_class}}">&nbsp;</i>
                            {{$child->title_es}}
                        </span>
                        <small>{{$child->comment}}</small>
                    </label>

                    <!--Third Level-->
                    @foreach( $child['children']->sortBy('weight') as $grandson)
                        @php
                            $checked = count($stored->where('navigation_id',$grandson->id)) >= 1;
                        @endphp
                        <label class="list-group-item list-navigation" href="#">
                            <span class="pl-4">
                                {{Form::checkbox('navigation['.$grandson->id.']',$grandson->title, $checked )}}
                                <i class="{{$grandson->icon_class}}">&nbsp;</i>
                                {{$grandson->title_es}}
                                <small>{{$grandson->comment}}</small>
                            </span>
                        </label>

                    @endforeach<!--E3L -->

                @endforeach<!--E2L -->

            @endforeach<!--E1L -->
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

                // $('#form-ui').iCheck({
                //     checkboxClass: 'icheckbox_square',
                //     radioClass: 'iradio_square',
                // });

            },

            activateTooltips: function(){
                $("*[data-toggle='tooltip']").tooltip();
            },

            handleSubmit: function(){
                $('#btn-save-navigation').off().on('click',function(){
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

    })

</script>
