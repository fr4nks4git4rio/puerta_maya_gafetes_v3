@php

    $user = auth()->getUser();
//    $misNotificaciones = $user->unreadNotifications;
    $misNotificaciones = \App\Notifications\NotificationHelper::getNotificationsByRole($user->roles_array);

@endphp

<li class="list-inline-item dropdown user-box">

    <!-- Notification -->
    <span class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown">

        <div class="notification-box">
            <ul class="list-inline m-b-0">
                <li>
                    <a href="javascript:void(0)" class="right-bar-toggle">
                        <i class="zmdi zmdi-notifications-none"></i>
                    </a>
                    @if(count($misNotificaciones) > 0)
                        <div class="noti-dot">
                        <span class="dot"></span>
                        <span class="pulse"></span>
                    </div>

                        <smal class="badge badge-pill badge-danger notification-count"
                              style="right: 0px;"> {{ count($misNotificaciones)  }} </smal>

                    @endif
                </li>
            </ul>
        </div>

    </span>

    <ul class="dropdown-menu dropdown-menu-lg" style="left: -200px; top:65px; max-height: 500px;overflow-y: auto;">

        @if(count($misNotificaciones) > 0)
            <li>
                <span class="dropdown-item" id="link_notifications" style="cursor: pointer">
                    <p class="text-info text-center m-b-0">Ver Todas</p>
                </span>
            </li>
        @endif
        @forelse($misNotificaciones as $noti)
            <li>
                    <span class="dropdown-item">
                    <span class="notification-icon"><i class="{{json_decode($noti->data)->icono}}"></i></span>

                       <b>{{json_decode($noti->data)->titulo}}</b> <br>
                       <p style="white-space:normal; margin-left: 16px; margin-bottom: 0px"> {!! json_decode($noti->data)->texto !!}</p>
                        <span class="float-left"><small class="text-muted">{{$noti->created_at}}</small></span>
                       <a href="#" style="display: block" class="text-right mark-as-read" data-id="{{$noti->id}}"><small
                                   class="text-primary">No volver a mostrar</small></a>
                    </span>
            </li>
        @empty
            <li>
                <span class="dropdown-item">
                    <p class="text-info text-center m-b-0">Sin notificaciones</p>
                </span>
            </li>

        @endforelse

    </ul>
    <!-- End Notification bar -->


</li>

<script type="text/javascript">

    $(document).ready(function () {

        if ($('#link_notifications').length > 0)
            document.getElementById('link_notifications').addEventListener('click', function (event) {
                window.location.href = '/notificaciones';
            })

        $('.mark-as-read').off().on('click', function () {
            let id = $(this).data('id');
            let url = '{{ url('notification/mark-as-read') }}/' + id;

            let noti_count = parseInt($('.notification-count').html());

            let element = $(this);


            $.ajax({
                url: url,
                method: 'POST',
                // data: $($this.form).serialize(),
                beforeSend: function () {
                    // console.log(url);
                    $('.notification-count').html("<i class='fa fa-spin fa-circle-o-notch'></i>");
                },
                success: function (res) {

                    if (res.success === true) {
                        // APAlerts.success(res.message);
                        element.closest('li').addClass('d-none');

                        noti_count--;
                        $('.notification-count').html(noti_count);

                        if (noti_count == 0) {
                            $('.notification-count').addClass('d-none');
                            $('.noti-dot').addClass('d-none');
                            element.closest('.dropdown-menu').addClass('d-none');
                        }

                    } else {
                        if (typeof res.message !== "undefined") {
                            APAlerts.error(res.message);
                        } else {
                            APAlerts.error(res);
                        }

                    }
                }
            });

        });


    });

</script>