<?php

namespace App\Reports;


use Illuminate\Http\Request;

use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class FormatoMantenimientoAnalisisRiesgoReport extends BaseReport
{

    private $permiso = null;
    private $tiposMantenimiento = [];
    private $actividadesAltoRiesgo = [];
    private $riesgosAsociados = [];
    private $medidasControlRiesgo = [];
    private $equiposHerramientas = [];
    private $trabajadores = [];

    private $view = "pdf-reports.formato-mantenimiento-analisis-riesgo";


    public function setPermiso(PermisoMantenimiento $permiso)
    {
        $this->permiso = $permiso;
    }
    public function setTiposMantenimiento(array $tiposmantenimiento)
    {
        $this->tiposMantenimiento = $tiposmantenimiento;
    }
    public function setActividadesAltoRiesgo(array $actividadesAltoRiesgo)
    {
        $this->actividadesAltoRiesgo = $actividadesAltoRiesgo;
    }
    public function setRiesgosAsociados(array $riesgosAsociados)
    {
        $this->riesgosAsociados = $riesgosAsociados;
    }
    public function setMedidasControlRiesgo(array $medidasControlRiesgo)
    {
        $this->medidasControlRiesgo = $medidasControlRiesgo;
    }
    public function setEquiposHerramientas(array $equiposHerramientas)
    {
        $this->equiposHerramientas = $equiposHerramientas;
    }
    public function setTrabajadores(array $trabajadores)
    {
        $this->trabajadores = $trabajadores;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "PMANRIES_" . $this->permiso->pmtt_id;

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'landscape';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['permiso'] = $this->permiso;
        $data['tiposMantenimiento'] = $this->tiposMantenimiento;
        $actividadesAltoRiesgo = $this->actividadesAltoRiesgo;
        $cantidad = count($actividadesAltoRiesgo);
        if ($cantidad % 4 != 0) {
            $faltantes = 4 - ($cantidad % 4);
            while ($faltantes > 0) {
                $actividadesAltoRiesgo[] = '';
                $faltantes--;
            }
        }
        $data['actividadesAltoRiesgo'] = $actividadesAltoRiesgo;
        $trabajadores = $this->trabajadores;
        if (count($trabajadores) % 2 != 0) {
            $trabajadores[] = ['id' => '', 'nombre' => '', 'nss' => ''];
        }
        $data['trabajadores'] = $trabajadores;
        $dataTable = [];
        $count = max(count($this->riesgosAsociados), count($this->medidasControlRiesgo), count($this->equiposHerramientas));

        $pos = 0;
        while ($count > 0) {
            $dataTable[] = [
                'riesgosAsociados' => $this->riesgosAsociados[$pos] ?? null,
                'medidasControlRiesgo' => $this->medidasControlRiesgo[$pos] ?? null,
                'equiposHerramientas' => $this->equiposHerramientas[$pos] ?? null
            ];
            $count--;
            $pos++;
        }

        $data['dataTable'] = $dataTable;

        $view = View($this->view, $data);
        //return $view->render();
        return $this->output($view);
    }
}
