<?php
namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Puerta;
use App\VGafetesRfid;


class DesactivarTarjeta
{
    private $force = false;
    private $gafete = null;

    /**
     * ActualizarTipoCambio constructor.
     * @param bool $force Fuerza la consulta y actualización aunque no haya pasado el intervalo
     */
    public function __construct(VGafetesRfid $gafete)
    {
        //date_default_timezone_set('America/Cancun');
        $this->gafete = $gafete;
    }

    public function execute() : bool
    {

        try{

            $cardNumber = $this->gafete->numero_rfid;
            $cardPin = $this->gafete->referencia;

            $tipoPuerta = 'PERSONAL';
            if($this->gafete->tipo == 'estacionamiento'){
                $tipoPuerta = 'ESTACIONAMIENTO';
            }

            $puertas = Puerta::select('door_authpin')->whereDoorTipo($tipoPuerta)->distinct()->get();

            foreach($puertas as $puerta){
                $doorPin = $puerta->door_pin;
                $dcg = new DoorCommandGenerator();
                $res = $dcg->lockCards($cardPin, $doorPin);
                if($res['success'] == false ) return false;
                sleep(1);
            }

            $dcg = new DoorCommandGenerator();
            $res = $dcg->delCards($cardPin, $cardNumber,0,0);

            if($res && settings()->get('door_controller_enabled',0) == 1){
                //marcamos como desactivado en bd
                $this->gafete->getOriginalRecord()->setDisabledAt();
            }

            return $res['success'];

        }catch(\Exception $e){

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar desactivar tarjeta" .$cardPin. " : ". $e->getMessage());

                \Log::error('Catched Exeption: '.$e->getMessage().' On: '.$e->getFile().' @'.$e->getLine());

            //throw($e);

            return false;
        }


    }

}
