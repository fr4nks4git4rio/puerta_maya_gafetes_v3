<?php
namespace App\Actions;

use App\TipoCambioLog;
use Illuminate\Http\Request;

use App\PermisoTemporal;
use App\User;
use App\Notifications\PermisoTemporalVencido;

use App\Clases\Banxico;


class ActualizarTipoCambio
{
    private $force = false;
    private $timeGap = 60; //minutes

    /**
     * ActualizarTipoCambio constructor.
     * @param bool $force Fuerza la consulta y actualización aunque no haya pasado el intervalo
     */
    public function __construct(bool $force = false)
    {
//        date_default_timezone_set('America/Cancun');
        $this->force = $force;
    }

    public function execute() : bool
    {

        try{

            //buscamos la ultima actualización de tipo de cambio
            $record = \DB::table('tipo_cambio_log')
                            ->orderBy('tclg_hora_consulta','DESC')
                            ->first();

            if($record <> null && $this->force == false){

                $lastUpdate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $record->tclg_hora_consulta);
                $now = \Carbon\Carbon::now();

                if($now->lessThan($lastUpdate->addMinutes($this->timeGap))){
                    return true;
                }


            }

            //obtenemos y actualizamos el tipo de cambio del sistema
            $banxico    = new Banxico();
            $tipoCambio = $banxico->getExRate();

            if($tipoCambio && floatval($tipoCambio)){

                \DB::table('tipo_cambio_log')->insert(['tclg_tipo_cambio'=>$tipoCambio]);

                activity()
                    ->inLog('Tarea Programada')
                    ->log("TCLG - Se capturó el tipo de cambio actual en Banxico: ".$tipoCambio);

                //obtenemos el tipo de cambio del dia de ayer porque ese debe ser el que muestra la aplciación
                $ayer = \Carbon\Carbon::yesterday()->format('Y-m-d');
                $record = TipoCambioLog::whereRaw( "DATE(tclg_hora_consulta) = '$ayer' ")
                                        ->where('tclg_tipo_cambio','>',0)
                                        ->orderBy('tclg_hora_consulta','desc')
                                        ->first();

                if($record != null ){
                    $tipoCambio = $record->tclg_tipo_cambio;

                    if($tipoCambio <> settings()->get('tipo_cambio'))
                    {
                        settings()->set('tipo_cambio', $tipoCambio);
                        settings()->save();

                        activity()
                            ->inLog('Configuración')
                            ->log("Se actualizó el sistema con el Tipo de Cambio correspondiente al día de <b>$ayer</b>: <b>$tipoCambio</b>");
                    }

                }



            }




            return true;

        }catch(\Exception $e){

            activity()
                ->inLog('Tarea Programada')
                ->log("Error al intentar modificar el tipo de cambio: ". $e->getMessage());

                \Log::error('Catched Exeption: '.$e->getMessage().' On: '.$e->getFile().' @'.$e->getLine());

            throw($e);

            return false;
        }


    }

}
