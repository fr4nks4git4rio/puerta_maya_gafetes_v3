@extends('layouts.main_vertical')

@section('title')
<span class="zmdi zmdi-iridescent">&nbsp;</span>Gafetes Preimpresos
@endsection

@section('content')

<div class="row">

    <div class="col-sm-12">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="pill" href="#temporales" data-url="{{url('gafete-preimpreso/permisos-temporales' )}}" id="first-tab">
                    <i class="zmdi zmdi-calendar-check"></i> Permisos temporales
                </a>
            </li>

{{--            <li class="nav-item d-none">--}}
{{--                <a class="nav-link" data-toggle="pill" href="#otro" data-url="{{url('gafete-preimpreso/otro' )}}">--}}
{{--                  <i class="zmdi zmdi-badge-check"></i>  Otro--}}
{{--                </a>--}}
{{--            </li>--}}

        </ul>
    </div>

</div>




<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane container active" id="temporales">...</div>
    <div class="tab-pane container fade" id="otro"></div>
{{--    <div class="tab-pane container fade" id="menu2"></div>--}}
</div>


<script type="text/javascript">
    $(document).ready(function(){

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        jPage = {

            init: function(){

                let $this = this;

                $('.nav-link').click(function(){
                    let target = $(this).attr('href');
                    let url  = $(this).data('url');
                    $(target).html(ajaxLoader());
                    ajax_update(url,target,null,null,'get');
                });

                $("#first-tab").click();

            }

        };

        jPage.init();

    });
</script>
@endsection
