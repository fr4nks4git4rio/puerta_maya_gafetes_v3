@extends('layouts.main_vertical')

@section('title')
<span class="ti-help-alt">&nbsp;</span>Video Tutorial
@endsection

@section('content')

<br>

<div class="row">
    <div class="col-md-2">
        &nbsp;
    </div>
    <div class="col-md-8">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/X38IkBEXjqI"
                frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
    </div>
    <div class="col-md-12 pl-5">
        <fieldset>
            <legend>Secciones</legend>
            <ul class="list-unstyled">
                <li><a href="#" id="seccion-1">1- Nuevo Empleado min ...5:45</a></li>
                <li><a href="javascript:void(0)" id="seccion-2">2- Nueva Solicitud Gafete Acceso min ...24:10</a></li>
                <li><a href="javascript:void(0)" id="seccion-3">3- Nueva Solicitud Gafete Estacionamiento min ...31:56</a></li>
                <li><a href="javascript:void(0)" id="seccion-4">4- Nuevo Solicitud Permiso Temporal min ...11:45</a></li>
                <li><a href="javascript:void(0)" id="seccion-5">5- Nueva Solicitud Mantenimiento min ...17:25</a></li>
            </ul>
        </fieldset>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        // $.fn.modal.Constructor.prototype.enforceFocus = function () {};

        jTutorial = {
            modal_dom: 'modal-form',
            modal_size: 'modal-lg',
            form_url: '{{url('modal-video-ayuda')}}',

            init: function () {
                let $this = this;

                $this.handleButtons();

            },

            handleButtons: function () {
                let $this = this;

                $('#seccion-1').click(function () {
                    $this.showVideoModal(345);
                });

                $('#seccion-2').click(function () {
                    $this.showVideoModal(1450);
                });

                $('#seccion-3').click(function () {
                    $this.showVideoModal(1916);
                });

                $('#seccion-4').click(function () {
                    $this.showVideoModal(705);
                });

                $('#seccion-5').click(function () {
                    $this.showVideoModal(1045);
                });

            },

            showVideoModal: function (seconds) {
                let $this = this;
                let url = $this.form_url + '/' + seconds;
                APModal.open({
                    dom: $this.modal_dom,
                    title: 'Video Tutorial',
                    url: url,
                    size: $this.modal_size,
                    method: 'GET'
                    // bOk: false
                });
            }

        };

        jTutorial.init();

    });
</script>
@endsection
