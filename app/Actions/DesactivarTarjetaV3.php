<?php

namespace App\Actions;

use App\Controladora;
use App\Empleado;
use App\Services\ControladoraAccesoService;
use App\VGafetesRfidV3;
use Illuminate\Support\Facades\Log;

class DesactivarTarjetaV3
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
                'puertas_numeros' => ''
            ];
            $resEmpl = $controllerService->updatePerson($dataEmpleado);
            if ($resEmpl['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setDisabledAt();

            return true;
        } catch (\Exception $e) {

            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar desactivar las tarjetas de" . $empleado->empl_nombre . " : " . $e->getMessage());

            Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            return false;
        }
    }
}
