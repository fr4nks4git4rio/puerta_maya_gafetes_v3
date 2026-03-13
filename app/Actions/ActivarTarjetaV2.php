<?php

namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\SolicitudGafete;
use App\VGafetesRfid;
use App\VGafetesRfidV2;


class ActivarTarjetaV2
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
            $res = $dcg->authCards($cardPin, $this->auth_pin);
            if ($res['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setActivatedAt();

            return true;

//            foreach($puertas as $puerta){
//
//                sleep(1);
//                $doorPin = $puerta->door_authpin;
//                $dcg = new DoorCommandGenerator();
//                $res = $dcg->authCards($cardPin, $doorPin);
//                if($res['success'] == false ) return false;
//
//            }
        } catch (\Exception $e) {

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar activar tarjeta" . $cardPin . " : " . $e->getMessage());

            \Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            //throw($e);

            return false;
        }


    }

}
