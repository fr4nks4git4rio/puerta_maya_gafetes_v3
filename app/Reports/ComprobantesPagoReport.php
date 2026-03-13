<?php

namespace App\Reports;


use Illuminate\Http\Request;

//use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class ComprobantesPagoReport extends BaseReport
{

    private $records = null;
    private $inicio = null;
    private $fin = null;

    private $view = "pdf-reports.comprobantes-pago";


    public function setRecords($records)
    {
        $this->records = $records;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    public function setFin($fin)
    {
        $this->fin = $fin;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "CPAG_" . date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'landscape';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;
        $data['inicio'] = $this->inicio;
        $data['fin'] = $this->fin;

        $view = View($this->view, $data);
        //return $view->render();
        return $this->output($view);
    }


}
