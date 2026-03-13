<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helper;
use App\Http\Controllers\SOAP\BaseSoapController;
use App\Http\Controllers\SOAP\InstanceSoapClient;
use App\Models\Administracion\Traza;
use App\Models\Facturacion\Factura;
use App\Models\Facturacion\PanelPacSetting;
use App\Services\Timbrado\CfdiConstructor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;

class SoapController extends BaseSoapController
{
    private $service;

    /**
     * @param null $modo
     * @return $this
     */
    private function establecer_modo($modo = null)
    {
        if ($modo) {
            $modo_prueba = "$modo";
        } else {
            $modo_prueba = settings()->get('cfdi_test_mode');
        }

        if ($modo_prueba == '1') {
            self::modo_pruebas();
        } else {
            self::modo_productivo();
        }
        $this->service = InstanceSoapClient::init();
        return $this;
    }

    public function obtenerTimbresDisponibles()
    {
        $this->establecer_modo(0);
        if (is_array($this->service)) {
//            dd('asdasd');
            return response()->json($this->ajaxResponse(false, $this->service['message']));
        }
        try {
            $params = [
                'usuarioIntegrador' => self::getUsuarioIntegrador(),
                'rfcEmisor' => self::getRfcEmisor()
            ];
            $response = $this->service->ObtieneTimbresDisponibles($params);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        /*Obtenemos resultado del response*/
        $tipoExcepcion = $response->ObtieneTimbresDisponiblesResult->anyType[0];
        $numeroExcepcion = $response->ObtieneTimbresDisponiblesResult->anyType[1];
        $msg = $response->ObtieneTimbresDisponiblesResult->anyType[2];
        $asignados = $response->ObtieneTimbresDisponiblesResult->anyType[3];
        $utilizados = $response->ObtieneTimbresDisponiblesResult->anyType[4];
        $disponibles = $response->ObtieneTimbresDisponiblesResult->anyType[5];

        if ($numeroExcepcion == "0") {
            return response()->json($this->ajaxResponse(true, "Consulta exitosa", $disponibles));
        }

        return response()->json($this->ajaxResponse(false, $msg));
    }
}
