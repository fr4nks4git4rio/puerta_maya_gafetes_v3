<?php
namespace App\Exports;

use App\Factura;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FacturasExport implements FromCollection, WithHeadings, ShouldAutoSize
{

    private $collection;

    /**
     * FacturasExport constructor.
     * @param $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }


    public function headings(): array
    {
        return [
            'Fecha Emisión',
            'Fecha Certificación',
            'Serio',
            'Folio',
//            'RFC Receptor',
            'Nombre Receptor',
            'Subtotal',
            'IVA',
            'Total',
            'Tipo Cambio',
            'UUID',
            'Estado'
        ];
    }

    public function collection()
    {
        return $this->collection;
    }
}
