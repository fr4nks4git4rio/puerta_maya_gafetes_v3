<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class SaldoLocalesExport implements FromView
{
    private $data;

    /**
     * SaldoLocalesExport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * @return View
     */
    public function view(): View
    {
        // TODO: Implement view() method.
        return \view('web.reportes.saldo-locales', [
            'records' => $this->data
        ]);
    }
}
