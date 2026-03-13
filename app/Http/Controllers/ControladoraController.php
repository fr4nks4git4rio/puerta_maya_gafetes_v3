<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjetaV2;
use App\Actions\CrearTarjetaV2;
use App\Actions\CrearTarjetaV3;
use App\Banco;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\GafeteEstacionamiento;
use App\Services\ControladoraAccesoService;
use App\SolicitudGafete;
use Illuminate\Http\Request;

//Vendors
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\CatAcceso;
use Illuminate\Support\Facades\Crypt;

class ControladoraController extends Controller
{

    protected $rules = [
        'insert' => [
            'ctrl_nombre' => 'required',
            'ctrl_ip' => 'required',
            'ctrl_usuario' => 'required',
            'ctrl_contrasenna' => 'required',
            'ctrl_descripcion' => 'nullable',
        ],

        'edit' => [
            'ctrl_id' => 'required|exists:tb_controladoras,ctrl_id',
            'ctrl_nombre' => 'required',
            'ctrl_ip' => 'required',
            'ctrl_usuario' => 'required',
            'ctrl_contrasenna' => 'required',
            'ctrl_descripcion' => 'nullable',
        ],
    ];


    protected $etiquetas = [
        'ctrl_id' => 'Id',
        'ctrl_nombre' => 'Nombre Controladora',
        'ctrl_ip' => 'IP Controladora',
        'ctrl_usuario' => 'Usuario Controladora',
        'ctrl_contrasenna' => 'Contraseña Controladora',
        'ctrl_descripcion' => 'Descripción Controladora',
    ];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }


    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            return Datatables::of(
                Controladora::select(['ctrl_id', 'ctrl_nombre', 'ctrl_ip', 'ctrl_descripcion'])
            )->addColumn('actions', function (Controladora $controladora) {
                $html = '<div class="btn-group">';
                $html .= '<span class="btn btn-primary btn-sm btn-conectar mr-2" title="Probar Conexión" data-id=' . $controladora->ctrl_id . '>
                               <i class="zmdi zmdi-network-wifi-alt" id="wifi_' . $controladora->ctrl_id . '"></i>
                               <i class="fa fa-spinner fa-spin" style="display: none" id="cargando_' . $controladora->ctrl_id . '"></i>
                          </span>';
                $html .= '<span class="btn btn-primary btn-sm btn-syncronizar" title="Sincronizar Registros" data-id=' . $controladora->ctrl_id . '>
                               <i class="fa fa-chain" id="sync_' . $controladora->ctrl_id . '"></i>
                               <i class="fa fa-spinner fa-spin" style="display: none" id="loading_' . $controladora->ctrl_id . '"></i>
                          </span>';
                $html .= '</div>';

                return $html;
            })
                ->rawColumns(['actions'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'ctrl_id', 'name' => 'ctrl_id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'ctrl_nombre', 'name' => 'ctrl_nombre', 'title' => 'Nombre', 'search' => true])
            ->addColumn(['data' => 'ctrl_ip', 'name' => 'ctrl_ip', 'title' => 'IP', 'search' => true])
            ->addColumn(['data' => 'ctrl_descripcion', 'name' => 'ctrl_descripcion', 'title' => 'Descripción', 'search' => true])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);


        return view('web.controladoras.index', compact('dataTable'));
    }

    public function form(Controladora $controladora = null, Request $request)
    {

        $url = ($controladora == null) ? url('controladoras/insert') : url('controladoras/edit', $controladora->getKey());

        if ($controladora != null && $controladora->ctrl_contrasenna)
            $controladora->ctrl_contrasenna = Crypt::decrypt($controladora->ctrl_contrasenna);

        return view('web.controladoras.form', compact('controladora', 'url'));
    }

    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {


            \DB::beginTransaction();
            try {
                $this->data['ctrl_contrasenna'] = Crypt::encrypt($this->data['ctrl_contrasenna']);
                Controladora::create($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Controladora <b>CREADA</b> correctamente.'));
        }
    }

    public function edit(Controladora $controladora, Request $request)
    {
        if (!$this->validateAction('edit')) {

            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();

            try {
                $this->data['ctrl_contrasenna'] = Crypt::encrypt($this->data['ctrl_contrasenna']);
                $controladora->update($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, "Controladora <b>EDITADO</b> correctamente."));
        }
    }

    public function delete(Controladora $controladora, Request $request)
    {

        \DB::beginTransaction();
        try {
            $controladora->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->ajaxResponse(false, $e->getMessage()));
        }

        \DB::commit();
        return response()->json($this->ajaxResponse(true, "Controladora <b>ELIMIADA</b> correctamente."));
    }

    public function getJSON(Controladora $controladora)
    {
        return response()->json($controladora);
    }

    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = Controladora::select(\DB::raw('ctrl_id as id, ctrl_nombre as text'))
            ->where('ctrl_nombre', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }

    public function probarConexionControladora(Controladora $controladora)
    {
        $manejador = new DoorCommandGeneratorV2($controladora);

        $res = $manejador->pingController();

        $message = $res['success'] ? "Conexión exitosa!" : "Error en conexión!";
        return response()->json($this->ajaxResponse($res['success'], $message));
    }
    public function probarConexionControladoraV3(Controladora $controladora)
    {
        $controladoraService = new ControladoraAccesoService($controladora)
;       $res = $controladoraService->testConnection();

        $message = $res['online'] ? "Conexión exitosa!" : $res['message'];
        return response()->json($this->ajaxResponse($res['online'], $message));
    }

    public function syncronizarRegistrosControladora(Controladora $controladora)
    {
        set_time_limit(900);
        $controller = Controladora::findOrFail($controladora->ctrl_id);

        $group_solicitudes_gafetes = SolicitudGafete::whereNotNull('sgft_activated_at')
            ->whereNull('sgft_disabled_at')
            ->whereNull('sgft_deleted_at')
            ->whereHas('Puertas')
            ->get()->chunk(50);

        foreach ($group_solicitudes_gafetes as $solicitudes_gafetes) {
            set_time_limit(50 * 3);
            foreach ($solicitudes_gafetes as $solicitud) {
                $gafeteRfid = $solicitud->getVGafeteRfidV2();

                // creamos tarjeta v2
                $crear = new CrearTarjetaV2($gafeteRfid, $controller);
                $res = $crear->execute();

                if ($res == false) {
                    \DB::rollBack();
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                }

                if ($solicitud->Empleado->empl_vacuna_validada) {
                    foreach ($solicitud->Puertas->where('door_controladora_id', $controladora->ctrl_id)->groupBy('door_controladora_id') as $doors) {
                        // activamos tarjeta v2
                        foreach ($doors as $door) {
                            $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $door->pin_value);
                            $res = $activar->execute();

                            if ($res == false) {
                                \DB::rollBack();
                                return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                            }
                        }
                    }
                }
            }
            sleep(1);
        }


        $group_gafetes_estacionamiento = GafeteEstacionamiento::whereNotNull('gest_activated_at')
            ->whereNull('gest_disabled_at')
            ->whereNull('gest_deleted_at')
            ->whereHas('Puertas')
            ->get()->chunk(50);

        foreach ($group_gafetes_estacionamiento as $gafetes_estacionamiento) {
            foreach ($gafetes_estacionamiento as $gafete) {
                $gafeteRfid = $gafete->getVGafeteRfidV2();

                // creamos tarjeta v2
                $crear = new CrearTarjetaV2($gafeteRfid, $controller);
                $res = $crear->execute();

                if ($res == false) {
                    \DB::rollBack();
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                }

                foreach ($gafete->Puertas->where('door_controladora_id', $controladora->ctrl_id)->groupBy('door_controladora_id') as $key => $doors) {
                    // activamos tarjeta v2
                    foreach ($doors as $door) {
                        $activar = new ActivarTarjetaV2($gafeteRfid, $controller, $door->pin_value);
                        $res = $activar->execute();

                        if ($res == false) {
                            \DB::rollBack();
                            return response()->json($this->ajaxResponse(false, "Ocurrió un error al activar la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                        }
                    }
                }
            }
            sleep(1);
        }


        return response()->json($this->ajaxResponse(true, "Sincronización exitosa!"));
    }
    public function syncronizarRegistrosControladoraV3(Controladora $controladora)
    {
        set_time_limit(900);
        $controller = Controladora::findOrFail($controladora->ctrl_id);

        $group_solicitudes_gafetes = SolicitudGafete::whereNotNull('sgft_activated_at')
            ->whereNull('sgft_disabled_at')
            ->whereNull('sgft_deleted_at')
            ->whereHas('Puertas')
            ->get()->chunk(50);

        foreach ($group_solicitudes_gafetes as $solicitudes_gafetes) {
            set_time_limit(20);
            foreach ($solicitudes_gafetes as $solicitud) {
                $gafeteRfid = $solicitud->getVGafeteRfidV3();

                // creamos tarjeta v3
                if(!$gafeteRfid->controladora_id)
                    continue;
                $crear = new CrearTarjetaV3($gafeteRfid);
                $res = $crear->execute();

                if ($res == false) {
                    return response()->json($this->ajaxResponse(false, "Ocurrió un error al crear la tarjeta en la controladora $controller->ctrl_nombre.", $res));
                }
            }
            sleep(1);
        }

        return response()->json($this->ajaxResponse(true, "Sincronización exitosa!"));
    }
}
