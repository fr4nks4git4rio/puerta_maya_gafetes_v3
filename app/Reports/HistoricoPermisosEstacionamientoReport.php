<?php

namespace App\Reports;


use Illuminate\Http\Request;

//use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class HistoricoPermisosEstacionamientoReport extends BaseReport
{

    private $records = null;
    private $inicio = null;
    private $fin = null;
    private $operaciones = [];

    private $view = "";

    public function setView($view)
    {
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
    public function setOperaciones($operaciones)
    {
        $this->operaciones = $operaciones;
        return $this;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "HIST_PER_EST_" . date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;
        $data['inicio'] = $this->inicio;
        $data['fin'] = $this->fin;
        $data['operaciones'] = $this->operaciones;

        $view = View($this->view, $data);
        //return $view->render();
        return $this->output($view);
    }
}
