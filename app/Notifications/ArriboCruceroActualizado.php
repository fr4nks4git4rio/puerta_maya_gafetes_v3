<?php

namespace App\Notifications;

use App\BarcoEvento;
use App\ComprobantePago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ArriboCruceroActualizado extends Notification
{
    use Queueable;


    private $evento = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(BarcoEvento $evento)
    {
        $this->evento = $evento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject('Itinerario actualizado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que se ha actualizado el itinerario de arribo de un crucero:')
            ->line('Crucero : ' . $this->evento->Barco->nombre)
            ->line('Llegada: ' . substr($this->evento->fecha_inicio, 0, 10) . ' ' . $this->evento->hora_llegada)
            ->line('Partida: ' . substr($this->evento->fecha_fin, 0, 10) . ' ' . $this->evento->hora_partida);


    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
//    public function toArray($notifiable)
//    {
//        return [
////            'ptmp_id' => $this->permisoTemporal->ptmp_id,
////            'nombre'  => $this->permisoTemporal->ptmp_nombre,
////            'inicio'  => $this->permisoTemporal->ptmp_vigencia_inicial,
////            'fin'     => $this->permisoTemporal->ptmp_vigencia_final,
//            'icono'   => "ti-check-box text-succes",
//            'titulo'  => 'Comprobante de Pago Validado',
//            'texto'   => '<b>Comprobante: </b><br/>'.
//                         'Folio Bancario: ' .  $this->comprobante->cpag_folio_bancario . '<br/>'.
//                         'Se han añadido: $'. number_format($this->comprobante->cpag_importe_pagado,2) . ' al saldo del local.',
////            'url'     => ""
//        ];
//    }
}
