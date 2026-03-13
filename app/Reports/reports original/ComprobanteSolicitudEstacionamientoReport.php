<?php
namespace App\Reports;

use App\GafeteEstacionamiento;
use Illuminate\Http\Request;

use App\SolicitudGafete;
use App\Reports\BaseReport;

class ComprobanteSolicitudEstacionamientoReport extends BaseReport
{

    private $solicitud = null;

    private $view = "pdf-reports.comprobante-solicitud-estacionamiento";


    public function setSolicitud(GafeteEstacionamiento $solicitud)
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
        $this->prefijo = "CP_SOLEST_".$this->solicitud->gest_id;

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
