<?php

namespace App\Mail;

use App\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FacturaTimbrada extends Mailable
{
    use Queueable, SerializesModels;

    private $factura = null;
    private $pdfData = null;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Factura $factura, $pdfData)
    {
        $this->factura = $factura;
        $this->pdfData = $pdfData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.factura-timbrada')
            ->attach($this->factura->fact_xml_path)
            ->attachData($this->pdfData, $this->factura->fact_uuid . '.pdf', [
                'mime' => 'application/pdf',
            ]);;
    }
}
