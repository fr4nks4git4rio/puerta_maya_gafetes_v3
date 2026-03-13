<?php
namespace App\Reports;


use App\Factura;
use Illuminate\Http\Request;

use App\Reports\BaseReport;

class FacturaListadoComprobantesPDF extends BaseReport
{

    private $factura = null;

    private $view = "pdf-reports.factura-listado-comprobantes";


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

        $this->prefijo = "FACT_".$this->factura->fact_id.'_LC';
//        if($this->factura->fact_uuid != ""){
//            $this->prefijo = "FACT_".$this->factura->fact_uuid;
//        }


        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';

        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['factura'] = $this->factura;

        $view = View($this->view,$data);

        return $this -> output($view);
    }


}
