<?php

namespace App\Notifications;

use App\GafeteEstacionamiento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\SolicitudGafete;

class GafeteEstacionamientoRechazado extends Notification
{
    use Queueable;


    private $gafeteEstacionamiento = null;
    private $comentario = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(GafeteEstacionamiento $gafeteEstacionamiento, string $comentario)
    {
        $this->gafeteEstacionamiento = $gafeteEstacionamiento;
        $this->comentario = $comentario;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Solicitud de gafete rechazada')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
            ->line('Por este medio tenemos la atención de informarle que la siguiente solicitud de gafete de estacionamiento ha sido rechazada:')
//                    ->action('Notification Action', url('/'))
            ->line('Tipo: ' . $this->gafeteEstacionamiento->gest_tipo)
            ->line('Clase: ' . $this->gafeteEstacionamiento->gest_tipo_solicitud)
            ->line('Año: ' . $this->gafeteEstacionamiento->gest_anio)
            ->line('Fecha solicitud: ' . $this->gafeteEstacionamiento->gest_created_at)
            ->line('Comentario: ' . $this->gafeteEstacionamiento->gest_comentario)
            ->line('Motivo: ' . $this->comentario ?? 'Se desconoce, favor de acceder al aplicativo');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
//            'ptmp_id' => $this->permisoTemporal->ptmp_id,
//            'nombre'  => $this->permisoTemporal->ptmp_nombre,
//            'inicio'  => $this->permisoTemporal->ptmp_vigencia_inicial,
//            'fin'     => $this->permisoTemporal->ptmp_vigencia_final,
            'icono' => "ti-car text-danger",
            'titulo' => 'Gafete de estacionamiento',
            'texto' => 'Se rechazó la solicitud de gafete de estacionamiento: Tipo <b>' . $this->gafeteEstacionamiento->gest_tipo . '</b> creada en ' . $this->gafeteEstacionamiento->gest_created_at,
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
