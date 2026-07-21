<?php

namespace App\Jobs;

use App\Controladora;
use App\Empleado;
use App\LogAcceso;
use App\Services\ControladoraAccesoService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessHikvisionEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $event = $this->eventData['AccessControllerEvent'];
            $empleado = Empleado::find($event['employeeNoString']);
            if ($empleado) {
                $controladora = Controladora::where('ctrl_id', $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id)->first();
                $door = DB::table('puertas')
                    ->where('door_controladora_id', $controladora->ctrl_id)
                    ->where('door_numero', $event['doorNo'])
                    ->first();
                $logAcceso = LogAcceso::firstOrCreate([
                    'lgac_serial_no' => $event['serialNo']
                ], [
                    'lgac_mac_address' => $this->eventData['macAddress'],
                    'lgac_ip' => $this->eventData['ipAddress'],
                    'lgac_major' => $event['majorEventType'],
                    'lgac_minor' => $event['subEventType'],
                    'lgac_card_number' => $event['cardNo'],
                    'lgac_lector' => $event['cardReaderNo'],
                    'lgac_door_number' => $event['doorNo'],
                    'lgac_door_id' => $door->door_id,
                    'lgac_ctrl_id' => $door->door_controladora_id,
                    'lgac_empl_id' => $event['employeeNoString'],
                    'lgac_tipo' => $door->door_direccion,
                    'lgac_in_out_state' => $door->door_direccion === 'ENTRADA',
                    'lgac_source' => 'event_notification_stream',
                    'lgac_time' => $this->eventData['dateTime'],
                    'lgac_payload' => json_encode($this->eventData),
                    'lgac_created_at' => str_replace(['T', 'Z'], [' ', ''], $this->eventData['dateTime'])
                ]);
                $controllerService = new ControladoraAccesoService($controladora);

                if ($door->door_direccion == 'SALIDA') {
                    $numeros = implode(
                        ',',
                        $empleado->GafeteAcceso()
                            ->Puertas()
                            ->where('door_direccion', 'ENTRADA')
                            ->where('door_modo', 'FISICA')
                            ->pluck('door_numero')->toArray()
                    );
                } else {
                    $numeros = implode(
                        ',',
                        $empleado->GafeteAcceso()
                            ->Puertas()
                            ->where('door_direccion', 'SALIDA')
                            ->where('door_modo', 'FISICA')
                            ->pluck('door_numero')->toArray()
                    );
                }
                $data = [
                    'empleado' => $empleado,
                    'puertas_numeros' => $numeros
                ];
                $res = $controllerService->updatePerson($data);

                if (!$res['success']) {
                    Log::error("Ha ocurrido un error recibiendo el evento desde la conrtroladora. Error: " . $res['message']);
                    DB::rollBack();
                }

                $ubicacion = DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->first();
                $autos = $ubicacion ? max($ubicacion->emplub_autos, 0) : 0;
                $motos = $ubicacion ? max($ubicacion->emplub_motos, 0) : 0;

                $autos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'AUTO' ? ($autos + 1) : $autos) : ($door->door_tipo == 'AUTO' ? ($autos - 1) : $autos);
                $motos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'MOTO' ? ($motos + 1) : $motos) : ($door->door_tipo == 'MOTO' ? ($motos - 1) : $motos);

                if (!$ubicacion) {
                    if ($door->door_direccion == 'ENTRADA') {
                        DB::table('empleados_ubicacion')->insert([
                            'emplub_empl_id' => $empleado->empl_id,
                            'emplub_lcal_id' => $empleado->empl_lcal_id,
                            'emplub_door_in_id' => $door->door_id,
                            'emplub_door_out_id' => null,
                            'emplub_ubicacion' => 1,
                            'emplub_fecha' => $logAcceso->lgac_created_at,
                            'emplub_autos' => $autos,
                            'emplub_motos' => $motos
                        ]);
                    } else {
                        DB::table('empleados_ubicacion')->insert([
                            'emplub_empl_id' => $empleado->empl_id,
                            'emplub_lcal_id' => $empleado->empl_lcal_id,
                            'emplub_door_in_id' => null,
                            'emplub_door_out_id' => $door->door_id,
                            'emplub_ubicacion' => 0,
                            'emplub_fecha' => $logAcceso->lgac_created_at,
                            'emplub_autos' => $autos,
                            'emplub_motos' => $motos
                        ]);
                    }
                } else {
                    if ($door->door_direccion == 'ENTRADA') {
                        DB::table('empleados_ubicacion')
                            ->where('emplub_empl_id', $empleado->empl_id)
                            ->update([
                                'emplub_door_in_id' => $door->door_id,
                                'emplub_door_out_id' => null,
                                'emplub_ubicacion' => 1,
                                'emplub_fecha' => $logAcceso->lgac_created_at,
                                'emplub_autos' => $autos,
                                'emplub_motos' => $motos
                            ]);
                    } else {
                        DB::table('empleados_ubicacion')
                            ->where('emplub_empl_id', $empleado->empl_id)
                            ->update([
                                'emplub_door_out_id' => $door->door_id,
                                'emplub_ubicacion' => 0,
                                'emplub_fecha' => $logAcceso->lgac_created_at,
                                'emplub_autos' => $autos,
                                'emplub_motos' => $motos
                            ]);
                    }
                }

                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Ha ocurrido un error intentando procesar el evento recibido desde el streamer. Error: {$e->getMessage()}");
            throw $e;
        }
    }
}
