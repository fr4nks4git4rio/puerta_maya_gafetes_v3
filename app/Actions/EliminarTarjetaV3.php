<?php

namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\Services\ControladoraAccesoService;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use App\VGafetesRfidV3;
use Illuminate\Support\Facades\Log;

class EliminarTarjetaV3
{
    private $force = false;
    private $gafete = null;
    private $controller = null;

    /**
     * ActualizarTipoCambio constructor.
     * @param VGafetesRfidV3 $gafete
     * @internal param bool $force Fuerza la consulta y actualización aunque no haya pasado el intervalo
     */
    public function __construct(VGafetesRfidV3 $gafete)
    {
        //date_default_timezone_set('America/Cancun');
        $this->gafete = $gafete;
    }

    public function execute(): bool
    {

        try {
            $controller = Controladora::find($this->gafete->controladora_id);
            if(!$controller) return false;
            $controllerService = new ControladoraAccesoService($controller);
            $data = [
                'card' => $this->gafete->numero_rfid
            ];

            $res = $controllerService->deleteCard($data);
            if ($res['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setDisabledAt();

            return true;
        } catch (\Exception $e) {

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar eliminar la tarjeta" . $this->gafete->numero_rfid . " : " . $e->getMessage());

            Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            return false;
        }
    }
}
