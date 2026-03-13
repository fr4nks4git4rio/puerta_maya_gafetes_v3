<?php

namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\VGafetesRfid;
use App\VGafetesRfidV2;


class DesactivarTarjetaV2
{
    private $force = false;
    private $gafete = null;
    private $controller = null;
    private $auth_pin = null;

    /**
     * ActualizarTipoCambio constructor.
     * @param VGafetesRfidV2 $gafete
     * @param Controladora $controller
     * @param int $auth_pin
     * @internal param bool $force Fuerza la consulta y actualización aunque no haya pasado el intervalo
     */
    public function __construct(VGafetesRfidV2 $gafete, Controladora $controller, int $auth_pin)
    {
        //date_default_timezone_set('America/Cancun');
        $this->gafete = $gafete;
        $this->controller = $controller;
        $this->auth_pin = $auth_pin;
    }

    public function execute(): bool
    {

        try {
            $cardPin = $this->gafete->referencia;

            $dcg = new DoorCommandGeneratorV2($this->controller);
//            foreach ($this->gafete->getOriginalRecord()->Puertas as $puerta) {
//                sleep(1);
//            }
            $res = $dcg->lockCards($cardPin, $this->auth_pin);
            if ($res['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setDisabledAt();

//            $tipoPuerta = 'PERSONAL';
//            if($this->gafete->tipo == 'estacionamiento'){
//                $tipoPuerta = 'ESTACIONAMIENTO';
//            }
//
//            $puertas = Puerta::select('door_authpin')->whereDoorTipo($tipoPuerta)->distinct()->get();
//
//            foreach($puertas as $puerta){
//                $doorPin = $puerta->door_pin;
//                $dcg = new DoorCommandGenerator();
//                $res = $dcg->lockCards($cardPin, $doorPin);
//                if($res['success'] == false ) return false;
//                sleep(1);
//            }

            return true;

        } catch (\Exception $e) {

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar desactivar tarjeta" . $cardPin . " : " . $e->getMessage());

            \Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            //throw($e);

            return false;
        }


    }

}
