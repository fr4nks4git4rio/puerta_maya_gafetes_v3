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
        $annio_impresion = settings()->get('anio_impresion');
        if($annio_impresion == 2022)
            $this->view = "gafetes.estacionamiento";
        else if(in_array($annio_impresion, [2025, 2026]))
            $this->view = "gafetes.dinamico.estacionamiento-$annio_impresion";
        else
            $this->view = "gafetes.dinamico.estacionamiento";
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
