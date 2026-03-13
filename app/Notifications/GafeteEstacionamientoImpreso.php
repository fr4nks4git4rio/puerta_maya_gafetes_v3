<?php

namespace App\Notifications;

use App\GafeteEstacionamiento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class GafeteEstacionamientoImpreso extends Notification
{
    use Queueable;


    private $gafeteEstacionamiento = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( GafeteEstacionamiento $gafeteEstacionamiento)
    {
        $this->gafeteEstacionamiento = $gafeteEstacionamiento;
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
                    ->subject('Impresión de gafete')
//                    ->greeting('Puerta Maya - Sistema de credencialización')
                    ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
                    ->line('Por este medio tenemos la atención de informarle que el siguiente gafete de estacionamiento ha sido impreso y esta en espera de su recolección:')
                    ->line('Tipo: '.$this->gafeteEstacionamiento->gest_tipo)
                    ->line('Tipo solicitud: ' . $this->gafeteEstacionamiento->gest_tipo_solicitud)
                    ->line('Año - número: ' . $this->gafeteEstacionamiento->gest_anio . ' - ' . $this->gafeteEstacionamiento->gest_numero)
                    ->line('Comentario: ' . $this->gafeteEstacionamiento->gest_comentario);
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
            'texto'   => 'Se ha impreso el gafete de estacionamiento tipo <b>'.$this->gafeteEstacionamiento->gest_tipo.' </b>' .
                          'Año-número <b>' . $this->gafeteEstacionamiento->gest_anio. ' - ' . $this->gafeteEstacionamiento->gest_numero. '</b>',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
