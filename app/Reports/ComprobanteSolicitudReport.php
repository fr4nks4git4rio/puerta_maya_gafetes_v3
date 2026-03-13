<?php
namespace App\Reports;

use Illuminate\Http\Request;

use App\SolicitudGafete;
use App\Reports\BaseReport;

class ComprobanteSolicitudReport extends BaseReport
{

    private $solicitud = null;

    private $view = "pdf-reports.comprobante-solicitud";


    public function setSolicitud(SolicitudGafete $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "CP_SOL_".$this->solicitud->sgft_id;

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['solicitud'] = $this->solicitud;

        $view = View($this->view,$data);
        return $this -> output($view);
    }


}
