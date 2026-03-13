<?php

namespace App\Clases;

use Carbon\Carbon;
use mysql_xdevapi\Exception;

class Banxico
{

    public static function getExRate()
    {
        date_default_timezone_set('America/Cancun');

        $dia = Carbon::today();

        //DOF no publica el indicador de tipo de cambio en fines de semana
        $diaSemana =$dia->dayOfWeekIso;
        if($diaSemana == 7) $dia->subDay(2);
        if($diaSemana == 6) $dia->subDay(1);
        //--------------------------------------------------------------------

        $dd = $dia->format('d');//date("d");
        $mm = $dia->format('m');//date("m");
        $yyyy = $dia->format('Y');//date("Y");

        $formated = $dia->format('d-m-Y');

        try{
            $url = "https://dof.gob.mx/indicadores_detalle.php?cod_tipo_indicador=158&dfecha=$dd%2F$mm%2F$yyyy&hfecha=$dd%2F$mm%2F$yyyy";

            $site = file_get_contents($url);
            $posIni = strpos($site,$formated);

            if($posIni === false ){
                throw new \Exception("No se encontró tipo de cambio para el día {$formated}");
            }
            $string = substr($site,$posIni,100);

            $existe = preg_match('/\d{2}[.]\d{6}/', $string, $coincidencias);
            if ($existe && count($coincidencias) == 1){
                $tipo_cambio = $coincidencias[0];
                if ($tipo_cambio && floatval($tipo_cambio)){
                   return $tipo_cambio;
                }
            }
        }catch (\Exception $e){
            \Log::log('warning', "Error en la consulta. " . $e->getMessage());
            throw $e;
        }

    }


}
