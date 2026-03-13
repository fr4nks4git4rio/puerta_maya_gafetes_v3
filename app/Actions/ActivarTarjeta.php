<?php
namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Puerta;
use App\VGafetesRfid;


class ActivarTarjeta
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

            $dcg = new DoorCommandGenerator();
            $res = $dcg->setCards($cardPin,$cardNumber,0,0);



            if($res['success'] == false ) return false;

//            dd($puertas);

            foreach($puertas as $puerta){

                sleep(1);
                $doorPin = $puerta->door_authpin;
                $dcg = new DoorCommandGenerator();
                $res = $dcg->authCards($cardPin, $doorPin);
                if($res['success'] == false ) return false;

            }

            //marcamos como activado en bd
            if(settings()->get('door_controller_enabled',0) == 1 ){
                $this->gafete->getOriginalRecord()->setActivatedAt();
            }



            return true;

        }catch(\Exception $e){

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar activar tarjeta" .$cardPin. " : ". $e->getMessage());

                \Log::error('Catched Exeption: '.$e->getMessage().' On: '.$e->getFile().' @'.$e->getLine());

            //throw($e);

            return false;
        }


    }

}
