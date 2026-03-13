<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ComprobantesPagoExport implements FromView
{
    private $data;
    private $inicio;
    private $fin;

    /**
     * ComprobantesPagoExport constructor.
     */
    public function __construct($data, $inicio, $fin)
    {
        $this->data = $data;
        $this->inicio = $inicio;
        $this->fin = $fin;
    }


    /**
     * @return View
     */
    public function view(): View
    {
        // TODO: Implement view() method.
        return \view('web.reportes.comprobantes-pago', [
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'records' => $this->data
        ]);
    }
}
