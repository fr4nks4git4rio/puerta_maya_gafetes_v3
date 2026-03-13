@extends('layouts.main_vertical')

@section('title')
    <span class="zmdi zmdi-boat">&nbsp;</span> Calendario de Barcos
@endsection

@section('content')

    <link href='{{asset('plugins/fullcalendar/packages/core/main.css')}}' rel='stylesheet' />
    <link href='{{asset('plugins/fullcalendar/packages/daygrid/main.css')}}' rel='stylesheet' />

    <script src='{{asset('plugins/fullcalendar/packages/core/main.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar/packages/daygrid/main.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar/packages/interaction/main.js')}}'></script>
    <script src='{{asset('plugins/fullcalendar/packages/core/locales/es.js')}}'></script>


    <style>
        .fc-title{
            font-weight: bold;
        }

        .fc-left > h2{
            font-size: 16px;
            text-transform: uppercase;
        }
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>

    </div>

<script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function() {

        window.jCalendario = {

            calendarEl : document.getElementById('calendar'),
            calendario : null,
            dateDoubleClick: false,
            modal_dom: 'modal-evento',
            modal_size: 'modal-md',
            events_url : '{{url('barco/obtener-eventos')}}',
            new_event_url: '{{url('barco/nuevo-evento-form')}}',
            edit_event_url: '{{url('barco/editar-evento-form')}}',

            init: function(){
                let $this = this;

                $this.initCalendar();

            },

            initCalendar: function(){
                let $this = this;

                $this.calendario = new FullCalendar.Calendar($this.calendarEl,{
                    plugins: ['dayGrid','interaction'],
                    locale: 'es',
                    events: $this.events_url,
                    editable: true,
                    eventLimit: true,
                    selectable: false,
                    selectHelper: true,

                    eventRender: function(event){

                        $(event.el).on('dblclick', function (){
                            $this.editEventModal($(this).data('id'));
                        });

                            $(event.el).find(".fc-title").prepend('<span class="zmdi zmdi-boat">&nbsp;</span>');
                            $(event.el).attr("title", event.event.extendedProps.description);
                            $(event.el).attr("data-id", event.event.extendedProps.event_id);
                            $(event.el).attr("data-toggle", 'tooltip');
                            $(event.el).attr("data-placement", 'bottom');
                            $(event.el).tooltip();

                    },

                    dateClick: function (dateClickInfo){

                        if(!$this.dateDoubleClick) {
                            $this.dateDoubleClick = true;
                            setTimeout(function() { $this.dateDoubleClick = false; }, 500); //this waits for a second click for the next 500ms
                        }
                        else {
                            $this.dateDoubleClick = false;
                            $this.newEventModal( dateClickInfo.dateStr);
                        }
                    },

                });

                $this.calendario.render();

            },

            newEventModal: function(dateStr){
                let $this = this;

                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Nuevo arribo de crucero',
                    url: $this.new_event_url + '/' + dateStr ,
                    size: $this.modal_size,
                });
            },

            editEventModal: function(eventId){
                let $this = this;

                APModal.open({
                    dom:$this.modal_dom,
                    title: 'Editar arribo de crucero',
                    url: $this.edit_event_url + '/' + eventId ,
                    size: $this.modal_size,
                });
            }


        };

        jCalendario.init();
    });

</script>
@endsection