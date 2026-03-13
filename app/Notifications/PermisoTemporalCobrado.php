<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\PermisoTemporal;

class PermisoTemporalCobrado extends Notification
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
        return ['database'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'icono'   => "zmdi zmdi-calendar-close text-danger",
            'titulo'  => 'Permisos Temporales',
            'texto'   => 'Se ha deducido $'.$this->permisoTemporal->ptmp_costo .' del saldo del local para cubrir la reposición del <b>permiso temporal</b> de <b>' .
                            $this->permisoTemporal->ptmp_nombre. '</b> con el gafete <b>'. $this->permisoTemporal->GafetePreimpreso->gfpi_numero .'</b>',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
