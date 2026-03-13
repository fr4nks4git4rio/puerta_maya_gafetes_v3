<?php
namespace App\Reports;

use Illuminate\Http\Request;

use App\GafetePreimpreso;
use App\Reports\BaseReport;

class GafetePermisoTemporalReport extends BaseReport
{

    private $gafete = null;

    private $view = "gafetes.preimpreso-permiso-temporal";


    public function setGafete(GafetePreimpreso $gafete)
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
        $this->prefijo = "GPI_".$this->gafete->gfpi_id;
        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');

        $this->setOrientation('portrait');

        // dd($request->data['Ciclo']);

        $data = [];
        $data['gafete'] = $this->gafete;

        $view = View($this->view,$data);
        return $this -> output($view);
    }


}
