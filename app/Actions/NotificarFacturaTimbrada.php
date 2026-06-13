<?php

namespace App\Actions;

use App\Factura;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

use App\PermisoTemporal;
use App\User;
use App\Notifications\PermisoTemporalVencido;
use App\Reports\FormatoFacturaPDF;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;


class NotificarFacturaTimbrada
{
    private $destinatarios;
    private $factura = null;
    private $subject = '';
    private $others_emails = [];
    private $body = '';
    private $files = [];

    public function __construct($destinatarios, Factura $factura, $subject = '', $others_emails = [], $body = '', $files = [])
    {
        $this->destinatarios = $destinatarios;
        $this->factura = $factura;
        $this->subject = $subject ? $subject : 'CFDI - Factura timbrada';
        $this->others_emails = $others_emails;
        $this->body = $body;
        $this->files = $files;
    }

    public function execute(): bool
    {
        try {

            $destinatarios = Arr::wrap($this->destinatarios);

            if (!is_dir(public_path("/tmp"))) {
                if (!mkdir($concurrentDirectory = public_path("/tmp")) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }
            File::cleanDirectory(public_path('/tmp'));

            $report = new FormatoFacturaPDF(null, true, false);
            $report->setFactura($this->factura);
            $pdfData = $report->exec();
            $nombre = "FACT_" . $this->factura->fact_id;
            if ($this->factura->fact_uuid != "") {
                $nombre = "FACT_" . $this->factura->fact_uuid;
            }
            file_put_contents("tmp/$nombre.pdf", $pdfData);

            $attachments = [];
            $attachments[] = [
                'name' => "$nombre.pdf",
                'src' => public_path("tmp/$nombre.pdf"),
                'mime' => 'application/pdf'
            ];

            if ($this->factura->fact_xml_path) {
                $attachments[] = [
                    'name' => $this->factura->fact_uuid . '.xml',
                    'src' => $this->factura->fact_xml_path,
                    'mime' => 'text/xml'
                ];
            }
            if (count($this->files) > 0) {
                foreach ($this->files as $file) {
                    $file->move(public_path("/tmp"), $file->getClientOriginalName());
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'src' => public_path("/tmp/{$file->getClientOriginalName()}"),
                        'mime' => $file->getClientMimeType()
                    ];
                }
            }

            foreach ($destinatarios as $destinatario) {
                SendEmailJob::dispatch(
                    $destinatario->email,
                    '',
                    "Facturas Puerta Maya",
                    $this->subject,
                    'mail.notificacion-factura-timbrada',
                    ['body' => $this->body],
                    $this->others_emails,
                    $attachments,
                    false
                );
                sleep(1);
            }

            return true;
        } catch (\Exception $e) {

            // \DB::rollBack();

            Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            throw ($e);

            // return $e->getMessage().' '.$e->getFile().':'.$e -> getLine();

            return false;
        }
    }
}
