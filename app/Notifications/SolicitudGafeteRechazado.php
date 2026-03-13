<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\SolicitudGafete;

class SolicitudGafeteRechazado extends Notification
{
    use Queueable;


    private $solicitudGafete = null;
    private $comentario = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudGafete $solicitudGafete, string $comentario)
    {
        $this->solicitudGafete = $solicitudGafete;
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
            ->line('Por este medio tenemos la atención de informarle que la siguiente solicitud de gafete de acceso ha sido rechazada:')
//                    ->action('Notification Action', url('/'))
            ->line('Nombre: ' . $this->solicitudGafete->sgft_nombre)
            ->line('Cargo: ' . $this->solicitudGafete->sgft_cargo)
            ->line('Tipo: ' . $this->solicitudGafete->sgft_tipo)
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
            'icono' => "ti-user text-danger",
            'titulo' => 'Gafete de accesso',
            'texto' => 'Se rechazó la solicitud de gafete de <b>' . $this->solicitudGafete->sgft_nombre . '</b>.',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
