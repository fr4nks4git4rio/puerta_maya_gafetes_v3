<?php

namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Empleado;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\Services\ControladoraAccesoService;
use App\SolicitudGafete;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use App\VGafetesRfidV3;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActivarTarjetaV3
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
            if (!$controller) return false;
            $controllerService = new ControladoraAccesoService($controller);
            $empleado = Empleado::find($this->gafete->empl_id);

            $dataEmpleado = [
                'empleado' => $empleado,
                'puertas_numeros' => $this->gafete->puertas_numeros
            ];
            $resEmpl = $controllerService->updatePerson($dataEmpleado);
            if ($resEmpl['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setActivatedAt();

            return true;
        } catch (\Exception $e) {

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar activar las tarjetas de" . $empleado->empl_nombre . " : " . $e->getMessage());

            Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            return false;
        }
    }
}
