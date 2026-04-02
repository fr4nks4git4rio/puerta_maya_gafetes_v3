<?php

namespace App\Reports;


use Illuminate\Http\Request;

//use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class AsignacionPermisosEstacionamientoReport extends BaseReport
{

    private $records = null;
    private $local = null;
    private $empleado = null;
    private $totales_asignados = null;
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

    public function setLocal($local)
    {
        $this->local = $local;
        return $this;
    }
    public function setEmpleado($empleado)
    {
        $this->empleado = $empleado;
        return $this;
    }
    public function setTotalesAsignados($totalesAsignados)
    {
        $this->totales_asignados = $totalesAsignados;
        return $this;
    }
    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "ASSIGN_PER_EST_" . date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;
        $data['local'] = $this->local;
        $data['empleado'] = $this->empleado;
        $data['totales_asignados'] = $this->totales_asignados;

        $view = View($this->view, $data);
        //return $view->render();
        return $this->output($view);
    }
}
