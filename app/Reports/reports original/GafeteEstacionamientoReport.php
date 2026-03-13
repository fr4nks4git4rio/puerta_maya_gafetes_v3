<?php
namespace App\Reports;

use App\GafeteEstacionamiento;
use Illuminate\Http\Request;
use App\Reports\BaseReport;

class GafeteEstacionamientoReport extends BaseReport
{

    private $gafete = null;

    private $view = "gafetes.estacionamiento";


    public function setGafete(GafeteEstacionamiento $gafete)
    {
        $this->gafete = $gafete;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "GEST_".$this->gafete->gest_id;
        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');

        // dd($request->data['Ciclo']);

        $data = [];
        $data['gafete'] = $this->gafete;

        $view = View($this->view,$data);
        return $this -> output($view);
    }


}
