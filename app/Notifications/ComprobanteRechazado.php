<?php

namespace App\Notifications;

use App\ComprobantePago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ComprobanteRechazado extends Notification
{
    use Queueable;


    private $comprobante = null;
    private $comentario = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( ComprobantePago $comprobante,$comentario = null)
    {
        $this->comprobante = $comprobante;
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
        $comprobante_string = $this->comprobante->cpag;

        return (new MailMessage)
            ->subject('Comprobante rechazado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que el siguiente comprobante ha sido rechazado:')
            ->line('Folio: ' . $this->comprobante->cpag_folio_bancario)
            ->line('Fecha de pago: ' . $this->comprobante->cpag_fecha_pago)
            ->line('Importe: $' . number_format($this->comprobante->cpag_importe_pagado,2))
            ->line('Motivo de rechazo: '. $this->comentario ?? 'Se desconoce, favor de acceder al aplicativo.');

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
            'icono'   => "ti-close text-danger",
            'titulo'  => 'Comprobante de Pago',
            'texto'   => '<b>Comprobante RECHAZADO: </b><br/>'.
                         'Folio Bancario: ' .  $this->comprobante->cpag_folio_bancario . '<br/>'.
                         'Motivo: ' .  $this->comentario
//            'url'     => ""
        ];
    }
}
