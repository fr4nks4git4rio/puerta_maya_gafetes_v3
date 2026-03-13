<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjeta;
use App\Actions\ActivarTarjetaV2;
use App\Actions\CrearTarjetaV2;
use App\Actions\DesactivarTarjeta;
use App\Actions\DesactivarTarjetaV2;
use App\Clases\DoorCommandGenerator;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\DoorControllerLog;
use App\Notifications\ComprobanteValidado;
use App\Puerta;
use App\Services\ControladoraAccesoService;
use App\VGafetesRfid;
use App\VGafetesRfidV2;
use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use App\Navigation;

class GateController extends Controller
{

    protected $rules = [
        'action' => [
            'name' => 'required|alpha',
            'guard_name' => 'required',
        ],

        'open-door' => [
            'door_pin' => 'required|numeric',
        ],

        'open-door-v3' => [
            'door_id' => 'required|exists:puertas,door_id',
        ],

        'authorize-card' => [
            'card_pin' => 'required|numeric',
            'card_number' => 'required|numeric',
        ],

        'create-card-lote' => [
            'pines' => 'required|array|min:1'
        ],

        'deauthorize-card' => [
            'card_pin' => 'required|numeric',
            'card_number' => 'required|numeric',
        ],


    ];

    protected $etiquetas = [
        'id' => 'ID',
    ];


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }


    public function index(Request $request)
    {

        $url = url('settings/set-setting');
        $filtros['search_tarjeta'] = $request->search_tarjeta ?? '';
        $filtros['tarjetasVigentes'] = $request->tarjetasVigentes ?? '';
        $filtros['perPageTarjeta'] = $request->perPageTarjeta ?? '';

        $tarjetas = DB::table('v_gafetes_rfid_v3');
        $path = "/gate-controller";
        if ($request->search_tarjeta) {
            $search_tarjeta = $request->search_tarjeta;
            $tarjetas->where(function ($q) use ($request) {
                $q->orWhere('numero_rfid', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('lcal_id', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('lcal_nombre_comercial', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('lcal_razon_social', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('nombre', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('tipo', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('referencia', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('puertas', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('activated_at', 'like', '%' . $request->search_tarjeta . '%')
                    ->orWhere('disabled_at', 'like', '%' . $request->search_tarjeta . '%');
            });
            $conector = Str::contains($path, "?") ? '&' : '?';
            $path .= $conector . "search_tarjeta=$search_tarjeta";
        }
        if ($request->tarjetasVigentes) {
            $tarjetas->where('disabled_at', null)
                ->whereNotNull('puertas');
            //->whereRaw("DATE_FORMAT(str_to_date(inicio, 'Y-m-d'), '%Y') = ?", settings()->get('anio_impresion'));
            $conector = Str::contains($path, "?") ? '&' : '?';
            $path .= $conector . "tarjetasVigentes=1";
        }
        $tarjetas = $tarjetas->orderBy('activated_at', 'desc')->select()->get();

        $total = $tarjetas->count();
        $page = isset($request->page) ? $request->page : 1;
        $perPageTarjeta = isset($request->perPageTarjeta) ? $request->perPageTarjeta : 10;
        $conector = Str::contains($path, "?") ? '&' : '?';
        $path .= $conector . "perPageTarjeta=$perPageTarjeta";

        $tarjetas = $tarjetas->forPage($page, $perPageTarjeta);
        $tarjetas = new LengthAwarePaginator($tarjetas, $total, $perPageTarjeta, $page, ['path' => $path]);

        $puertas = Puerta::all();

        $admin_alog_days = settings()->get('admin_alog_days', 3);
        $accesos = DB::table('v_log_accesos_v3')
            ->whereRaw("(date(lgac_created_at) >=  CURDATE() - INTERVAL $admin_alog_days DAY )")
            ->orderBy('lgac_created_at', 'desc')
            //->take(500)
            ->get();

        $interacciones = DoorControllerLog::orderBy('dclg_created_at', 'desc')
            ->take(500)
            ->get();

        return view('web.gate-controller.index', compact('url', 'tarjetas', 'accesos', 'interacciones', 'puertas', 'filtros'));
    }


    public function getAccessLog(Request $request)
    {
        $success = true;
        $output = '';
        $res = [];
        foreach (Controladora::all() as $controller) {
            if ($controller->ctrl_usuario && $controller->ctrl_contrasenna) {
                $controllerService = new ControladoraAccesoService($controller);
                $res = $controllerService->pollingSync();
                $success = $res['success'];
                if (!$success)
                    $output .= $res['message'];
            }
            // $dcg = new DoorCommandGeneratorV2($controller);
            // $res[$controller->ctrl_nombre] = $dcg->getAccessLog();
            // if (!$res[$controller->ctrl_nombre]['success'])
            //     $success = false;
            // $output .= $res[$controller->ctrl_nombre]['output'];
        }
        //        $res = $dcg->pingController();

        return response()->json($this->ajaxResponse($success, $output, $res));
    }

    public function openDoor(Request $request)
    {

        if (!$this->validateAction('open-door')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {

            $dcg = new DoorCommandGenerator();

            $res = $dcg->openDoor($this->data['door_pin']);

            return response()->json($this->ajaxResponse($res['success'], $res['output'], $res));
        }
    }

    public function openDoorV3(Request $request)
    {

        if (!$this->validateAction('open-door-v3')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {

            $puerta = Puerta::find($this->data['door_id']);
            $controllerService = new ControladoraAccesoService($puerta->Controladora);

            $data = ['door' => $puerta->door_numero];
            $res = $controllerService->openDoor($data);

            return response()->json($this->ajaxResponse($res['success'], isset($res['message']) ? $res['message'] : '', $res));
        }
    }


    public function createCard(Request $request)
    {

        if (!$this->validateAction('authorize-card')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {

            $gafete = VGafetesRfidV2::whereReferencia($this->data['card_pin'])->first();
            foreach ($gafete->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                $controller = Controladora::find($key);
                // creamos tarjeta v2
                $crear = new CrearTarjetaV2($gafete, $controller);
                $res = $crear->execute();

                if ($res == false) {
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta $gafete->numero_rfid en la controladora $controller->ctrl_nombre.", $res));
                }
                foreach ($doors as $door) {
                    $activar = new ActivarTarjetaV2($gafete, $door->Controladora, $door->pin_value);
                    $res = $activar->execute();

                    if ($res == false) {
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
                    }
                }
            }

            return response()->json($this->ajaxResponse($res, 'Se enviaron los comandos a la controladora.'));
        }
    }

    public function createCardLote(Request $request)
    {
        if (!$this->validateAction('create-card-lote')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {
            set_time_limit(1000);
            foreach ($this->data['pines'] as $pin) {
                $gafete = VGafetesRfidV2::whereReferencia($pin)->first();
                foreach ($gafete->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                    $controller = Controladora::find($key);
                    // creamos tarjeta v2
                    $crear = new CrearTarjetaV2($gafete, $controller);
                    $res = $crear->execute();

                    if ($res == false) {
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta $gafete->numero_rfid en la controladora $controller->ctrl_nombre.", $res));
                    }
                    foreach ($doors as $door) {
                        $activar = new ActivarTarjetaV2($gafete, $door->Controladora, $door->pin_value);
                        $res = $activar->execute();

                        if ($res == false) {
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
                        }
                    }
                    DB::commit();
                }
            }
            return response()->json($this->ajaxResponse($res, 'Se enviaron los comandos a la controladora.'));
        }
    }

    public function authorizeCard(Request $request)
    {

        if (!$this->validateAction('authorize-card')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {

            $gafete = VGafetesRfidV2::whereReferencia($this->data['card_pin'])->first();
            foreach ($gafete->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                foreach ($doors as $door) {
                    $activar = new ActivarTarjetaV2($gafete, $door->Controladora, $door->pin_value);
                    $res = $activar->execute();

                    if ($res == false) {
                        \DB::rollBack();
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
                    }
                }
            }

            return response()->json($this->ajaxResponse($res, 'Se enviaron los comandos a la controladora.'));


            //            $doorPin = 1;
            //            $cardNumber = $this->data['card_number'];
            //            $cardPin = $this->data['card_pin'];
            //
            //            $dcg = new DoorCommandGenerator();
            //            $res1 = $dcg->setCards($cardPin,$cardNumber,0,0);
            //
            //            sleep(1);
            //
            //            $dcg = new DoorCommandGenerator();
            //            $res2 = $dcg->authCards($cardPin, $doorPin);

            //            return response()->json($this -> ajaxResponse($res2['success'], $res2['output'], $res2 ));

        }
    }

    public function deauthorizeCard(Request $request)
    {

        if (!$this->validateAction('deauthorize-card')) {

            return response()->json($this->ajaxResponse(false, 'Errores en la petición!'));
        } else {

            $gafete = VGafetesRfidV2::whereReferencia($this->data['card_pin'])->first();
            foreach ($gafete->getOriginalRecord()->Puertas->groupBy('door_controladora_id') as $key => $doors) {
                foreach ($doors as $door) {
                    $activar = new DesactivarTarjetaV2($gafete, $door->Controladora, $door->pin_value);
                    $res = $activar->execute();

                    if ($res == false) {
                        \DB::rollBack();
                        return response()->json($this->ajaxResponse(false, "Ocurrió un error al desactivar la tarjeta en la controladora " . $door->Controladora->ctrl_nombre, $res));
                    }
                }
            }

            return response()->json($this->ajaxResponse($res, 'Se enviaron los comandos a la controladora.'));

            //            $cardPin = $this->data['card_pin'];
            //            $cardNumber = $this->data['card_number'];
            //
            //            $cardRecord = VGafetesRfid::whereReferencia($cardPin)->first();
            //
            //            $doorPin = 1;
            //            if($cardRecord->tipo == 'estacionamiento'){
            //                $doorPin = 2;
            //            }
            //
            //            $dcg = new DoorCommandGenerator();
            //            $res1 = $dcg->lockCards($cardPin, $doorPin);
            //
            //            sleep(1);
            //
            //            $dcg = new DoorCommandGenerator();
            //            $res2 = $dcg->delCards($cardPin,$cardNumber,0,0);


        }
    }
}
