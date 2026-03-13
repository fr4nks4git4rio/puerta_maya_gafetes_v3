<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\PermisoTemporal;

class PermisoTemporalAprobado extends Notification
{
    use Queueable;


    private $permisoTemporal = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( PermisoTemporal $permisoTemporal)
    {
        $this->permisoTemporal = $permisoTemporal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Permiso temporal aprobado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que el permiso temporal de:')
            ->line($this->permisoTemporal->ptmp_nombre)
            ->line('con vigencia del '.$this->permisoTemporal->ptmp_vigencia_inicial .' al '.$this->permisoTemporal->ptmp_vigencia_final)
            ->line('Ha sido aprobado.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
//            'ptmp_id' => $this->permisoTemporal->ptmp_id,
//            'nombre'  => $this->permisoTemporal->ptmp_nombre,
//            'inicio'  => $this->permisoTemporal->ptmp_vigencia_inicial,
//            'fin'     => $this->permisoTemporal->ptmp_vigencia_final,
            'icono'   => "zmdi zmdi-calendar-check text-success",
            'titulo'  => 'Permisos Temporales',
            'texto'   => 'El <b>permiso temporal</b> de <b>' .
                            $this->permisoTemporal->ptmp_nombre. '</b> ha sido aprobado.',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
