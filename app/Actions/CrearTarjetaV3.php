<?php

namespace App\Actions;

use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\Empleado;
use App\Http\Controllers\Controller;
use App\Puerta;
use App\Services\ControladoraAccesoService;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use App\VGafetesRfidV3;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CrearTarjetaV3
{
    private $force = false;
    private $gafete = null;

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
            $dataCard = [
                'empleado' => $empleado,
                'card' => $this->gafete->numero_rfid
            ];
            $resCard = $controllerService->addCard($dataCard);

            if ($resCard['success'] == false) {
                if ($resCard['error'] == 'EMPLOYEE_NOT_EXISTS') {
                    $dataEmpleado = [
                        'empleado' => $empleado,
                        'puertas_numeros' => $this->gafete->puertas_numeros,
                        'inicio' => $this->gafete->inicio,
                        'fin' => $this->gafete->fin
                    ];
                    $resEmpl = $controllerService->addPerson($dataEmpleado);
                    if ($resEmpl['success']) {
                        $resCard = $controllerService->addCard($dataCard);
                        if ($resCard['success'] == false) return false;
                        return true;
                    }
                }
                return false;
            };

            $dataEmpleado = [
                'empleado' => $empleado,
                'puertas_numeros' => $this->gafete->puertas_numeros,
                'Valid' => [
                    'enable' => true,
                    'beginTime' => $this->gafete->inicio->format('Y-m-d') . 'T' . $this->gafete->inicio->format('H:i:s'),
                    'endTime' => $this->gafete->fin->format('Y-m-d') . 'T' . $this->gafete->fin->format('H:i:s')
                ]
            ];
            $res = $controllerService->updatePerson($dataEmpleado);
            if ($res['success'] == false) return false;

            $this->gafete->getOriginalRecord()->setActivatedAt();

            return true;
        } catch (\Exception $e) {
            activity()
                ->inLog('Door Controller')
                ->log("Error al intentar crear la tarjeta" . $this->gafete->numero_rfid . " : " . $e->getMessage());

            Log::error('Catched Exeption: ' . $e->getMessage() . ' On: ' . $e->getFile() . ' @' . $e->getLine());

            return false;
        }
    }
}
