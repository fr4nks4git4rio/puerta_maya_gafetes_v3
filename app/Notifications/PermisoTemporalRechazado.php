<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\PermisoTemporal;

class PermisoTemporalRechazado extends Notification
{
    use Queueable;


    private $permisoTemporal = null;
    private $comentario = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( PermisoTemporal $permisoTemporal,$comentario=null)
    {
        $this->permisoTemporal = $permisoTemporal;
        $this->comentario = $comentario;
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
            ->subject('Permiso temporal rechazado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que el siguiente permiso temporal ha sido rechazado:')
            ->line('Nombre: '. $this->permisoTemporal->ptmp_nombre)
            ->line('Vigencia: del '.$this->permisoTemporal->ptmp_vigencia_inicial .' al '.$this->permisoTemporal->ptmp_vigencia_final)
            ->line('Motivo: '.$this->comentario ?? 'Se desconoce, favor de acceder al aplicativo');
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
            'icono'   => "zmdi zmdi-calendar-check text-danger",
            'titulo'  => 'Permisos Temporales',
            'texto'   => 'El <b>permiso temporal</b> de <b>' .
                            $this->permisoTemporal->ptmp_nombre. '</b> ha sido rechazado.',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
