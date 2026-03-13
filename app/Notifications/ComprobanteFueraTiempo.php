<?php

namespace App\Notifications;

use App\ComprobantePago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ComprobanteFueraTiempo extends Notification
{
    use Queueable;


    private $comprobante = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( ComprobantePago $comprobante)
    {
        $this->comprobante = $comprobante;
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
            'icono'   => "zmdi zmdi-time text-succes",
            'titulo'  => 'Comprobante de Pago Fuera de Tiempo',
            'texto'   => '<b>Comprobante: </b><br/>'.
                         'Folio Bancario: ' .  $this->comprobante->cpag_folio_bancario . '<br/>'.
                         'La factura asociada al comprobante se contabilizará en el próximo mes.',
//            'url'     => ""
        ];
    }
}
