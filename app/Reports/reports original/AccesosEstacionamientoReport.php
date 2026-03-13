<?php
namespace App\Reports;


use Illuminate\Http\Request;

use App\Reports\BaseReport;

class AccesosEstacionamientoReport extends BaseReport
{

    private $records = null;
    private $inicio = null;
    private $fin = null;

    private $local = null;

    private $view = "pdf-reports.gafetes-impresos-estacionamiento";


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
        $this->prefijo = "EST_ACCESS_".date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;
        $data['inicio'] = $this->inicio;
        $data['fin'] = $this->fin;

        $view = View($this->view,$data);
        //return $view->render();
        return $this -> output($view);
    }


}
