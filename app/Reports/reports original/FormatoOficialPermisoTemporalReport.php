<?php
namespace App\Reports;


use App\PermisoTemporal;;
use App\Reports\BaseReport;

class FormatoOficialPermisoTemporalReport extends BaseReport
{

    private $permiso = null;

    private $view = "pdf-reports.formato-oficial-permiso-temporal";


    public function setPermiso(PermisoTemporal $permiso)
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
        $this->prefijo = "POFTMP_".$this->permiso->ptmp_id;

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
