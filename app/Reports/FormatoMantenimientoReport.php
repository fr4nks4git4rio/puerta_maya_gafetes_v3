<?php
namespace App\Reports;


use Illuminate\Http\Request;

use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class FormatoMantenimientoReport extends BaseReport
{

    private $permiso = null;

    private $view = "pdf-reports.formato-mantenimiento";


    public function setPermiso(PermisoMantenimiento $permiso)
    {
        $this->permiso = $permiso;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "PMANTO_".$this->permiso->pmtt_id;

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'portrait';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['permiso'] = $this->permiso;

        $view = View($this->view,$data);
        //return $view->render();
        return $this -> output($view);
    }


}
