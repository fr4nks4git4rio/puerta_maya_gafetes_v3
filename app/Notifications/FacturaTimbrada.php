<?php

namespace App\Notifications;

use App\ComprobantePago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Factura as Factura;
use Illuminate\Support\Facades\Auth;

class FacturaTimbrada extends Notification
{
    use Queueable;


    private $factura = null;
    private $pdfData = null;
    private $subject = '';
    private $others_emails = [];
    private $body = '';
    private $files = [];

    /**
     * Create a new notification instance.
     *
     * @param Factura $factura
     * @param $pdfData
     * @param string $subject
     * @param array $others_emails
     * @param string $body
     */
    public function __construct(Factura $factura, $pdfData, $subject = '', $others_emails = [], $body = '', $files = [])
    {
        $this->factura = $factura;
        $this->pdfData = $pdfData;
        $this->subject = $subject ? $subject : 'CFDI - Factura timbrada';
        $this->others_emails = $others_emails;
        $this->body = $body;
        $this->files = $files;
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
        $email = new MailMessage;
        if (count($this->others_emails) > 0 && trim($this->others_emails[0]) !== "") {
            $email->cc($this->others_emails);
        }

        $email->from(config('mail.from.address'), "Facturas Puerta Maya")
            ->subject($this->subject)
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
            ->line($this->body)
//            ->line('Por este medio tenemos la atención de informarle que su comprobante ha sido timbrado.')
            ->line('Se adjunta XML y PDF correspondientes');
        if ($this->factura->fact_xml_path) {
            $email->attach($this->factura->fact_xml_path, [
                'as' => $this->factura->fact_uuid . '.xml',
                'mime' => 'text/xml'
            ]);
        }
        if ($this->pdfData) {
            $email->attachData($this->pdfData, $this->factura->fact_uuid . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        if (!is_dir(public_path("/tmp"))) {
            if (!mkdir($concurrentDirectory = public_path("/tmp")) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $file->move(public_path("/tmp"), $file->getClientOriginalName());
                $email->attach(public_path() . "/tmp/" . $file->getClientOriginalName(), [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType()
                ]);
            }
        }

        return $email;
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
