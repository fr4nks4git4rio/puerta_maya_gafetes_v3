<?php
namespace App\Reports;


use App\Factura;
use Illuminate\Http\Request;

use App\Reports\BaseReport;

class FormatoFacturaPDF extends BaseReport
{

    private $factura = null;

    private $view = "pdf-reports.formato-factura";


    public function setFactura(Factura $factura)
    {
        $this->factura = $factura;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {

        $this->prefijo = "FACT_".$this->factura->fact_id;
        if($this->factura->fact_uuid != ""){
            $this->prefijo = "FACT_".$this->factura->fact_uuid;
        }


        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';

        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['factura'] = $this->factura;

        $view = View($this->view,$data);

        return $this -> output($view);
    }


}
