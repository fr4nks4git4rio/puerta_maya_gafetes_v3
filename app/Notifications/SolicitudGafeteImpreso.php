<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\SolicitudGafete;

class SolicitudGafeteImpreso extends Notification
{
    use Queueable;


    private $solicitudGafete = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( SolicitudGafete $solicitudGafete)
    {
        $this->solicitudGafete = $solicitudGafete;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
                    ->subject('Impresión de gafete')
//                    ->greeting('Puerta Maya - Sistema de credencialización')
                    ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
                    ->line('Por este medio tenemos la atención de informarle que el gafete de:')
                    ->line($this->solicitudGafete->Empleado->empl_nombre)
                    ->line('Ha sido impreso satisfactoriamente y esta en espera de su recolección');
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
            'icono'   => "zmdi zmdi-print text-success",
            'titulo'  => 'Gafete impreso',
            'texto'   => 'Se ha impreso el gafete de <b>'.$this->solicitudGafete->Empleado->empl_nombre .'</b> y esta disponible para su recolección.',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
