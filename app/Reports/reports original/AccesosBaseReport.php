<?php
namespace App\Reports;


use Illuminate\Http\Request;

//use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class AccesosBaseReport extends BaseReport
{

    private $records = null;
    private $inicio = null;
    private $fin = null;
    private $dia = null;

    private $view = "";

    public function setView($view){
        $this->view = $view;
        return $this;
    }

    public function setRecords($records)
    {
        $this->records = $records;
        return $this;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function setFin($fin)
    {
        $this->fin = $fin;
        return $this;
    }

    public function setDia($dia)
    {
        $this->dia = $dia;
        return $this;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "LOGACCSS_".date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;
        $data['inicio'] = $this->inicio;
        $data['fin'] = $this->fin;
        $data['dia'] = $this->dia;
        $data['view'] = $this->view;

        $view = View('pdf-reports.accesos-base-pdf',$data);
        //return $view->render();
        return $this -> output($view);
    }


}
