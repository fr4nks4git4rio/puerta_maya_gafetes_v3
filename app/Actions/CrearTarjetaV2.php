<?php
namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\VGafetesRfid;
use App\VGafetesRfidV2;


class CrearTarjetaV2
{
    private $force = false;
    private $gafete = null;
    private $controller = null;

    /**
     * ActualizarTipoCambio constructor.
     * @param VGafetesRfidV2 $gafete
     * @param Controladora $controller
     * @internal param bool $force Fuerza la consulta y actualización aunque no haya pasado el intervalo
     */
    public function __construct(VGafetesRfidV2 $gafete, Controladora $controller)
    {
        //date_default_timezone_set('America/Cancun');
        $this->gafete = $gafete;
        $this->controller = $controller;
    }

    public function execute() : bool
    {

        try{

            $cardNumber = $this->gafete->numero_rfid;
            $cardPin = $this->gafete->referencia;

            $dcg = new DoorCommandGeneratorV2($this->controller);
            $res = $dcg->setCards($cardPin,$cardNumber,0,0);

            if($res['success'] == false ) return false;

//            $this->gafete->getOriginalRecord()->setActivatedAt();

            return true;

        }catch(\Exception $e){
            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar activar tarjeta" .$cardPin. " : ". $e->getMessage());

                \Log::error('Catched Exeption: '.$e->getMessage().' On: '.$e->getFile().' @'.$e->getLine());

            return false;
        }
    }
}
