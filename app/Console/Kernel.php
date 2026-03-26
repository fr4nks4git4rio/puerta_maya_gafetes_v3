<?php

namespace App\Console;

use App\Actions\ActivarTarjetaV3;
use App\Actions\NotificarPermisosTemporalesVencidos;
use App\Clases\DoorCommandGenerator;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Actions\ActualizarTipoCambio;
use App\Actions\CrearTarjetaV3;
use App\Clases\DoorCommandGeneratorV2;
use App\Controladora;
use App\SolicitudGafete;
use App\SolicitudGafeteReasignar;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // $schedule->call(function () {

        //     $tipoCambio = new ActualizarTipoCambio(true);
        //     $tipoCambio->execute();
        // })->everyMinute();


        // $schedule->call(function () {

        //     $Notificator = new NotificarPermisosTemporalesVencidos();
        //     $Notificator->execute();

        // })->dailyAt('03:00');


        // $schedule->call(function () {

        //     //Extraer logs de la controladora
        //     $controladoras = Controladora::all();
        //     foreach ($controladoras as $controladora) {
        //         $dcg = new DoorCommandGeneratorV2($controladora);
        //         $dcg->getAccessLog();
        //         sleep(1);
        //     }
        // })->everyFiveMinutes();

        Log::info("Schedule ejecutado");

        $schedule->call(function () {

            //Otorgar en la controladora los permisos autorizados
            $solicitudes = SolicitudGafeteReasignar::where('sgftre_estado', 'ASIGNADO')->get();
            foreach ($solicitudes as $solicitud) {

                $gafete = $solicitud->Gafete;
                $gafeteRfid = $gafete->getVGafeteRfidV3();
                $controladora = Controladora::find($gafeteRfid->controladora_id);

                $activar = new ActivarTarjetaV3($gafeteRfid);
                $res = $activar->execute();
                if ($res == false) {
                    Log::error("Ocurrió un error al activar la tarjeta $gafeteRfid->numero_rfid en la controladora $controladora->ctrl_nombre.");
                    continue;
                }

                $solicitud->sgftre_estado = 'AUTORIZADO';
                $solicitud->sgftre_fecha_autorizado = now();
                $solicitud->save();

                $solicitud->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'AUTORIZADO',
                    'sgftrece_fecha' => $solicitud->sgftre_fecha_autorizado,
                    'sgftrece_created_at' => now()
                ]);

                $gafete->sgft_permisos = $solicitud->sgftre_permisos;
                $gafete->save();

                usleep(300000);
            }
        })->between('00:00', '00:20');

        $schedule->call(function () {

            //Quitar en la controladora los permisos autorizados y eliminar las solicitud de permisos para las solicitudes en estado CANCELADO
            $solicitudes = SolicitudGafeteReasignar::where('sgftre_estado', 'CANCELADO')->get();
            foreach ($solicitudes as $solicitud) {
                DB::beginTransaction();
                try {

                    $gafete = $solicitud->Gafete;
                    $gafeteRfid = $solicitud->Gafete->getVGafeteRfidV3();


                    $gafete->Puertas()->detach($gafete->Puertas()->where('door_tipo', '!=', 'PEATONAL')->pluck('door_id'));

                    $gafete->sgft_permisos = 'PEATONAL';
                    $gafete->save();


                    $controladora = Controladora::find($gafeteRfid->controladora_id);

                    $activar = new ActivarTarjetaV3($gafeteRfid);
                    $res = $activar->execute();
                    if ($res == false) {
                        DB::rollBack();
                        Log::error("Ocurrió un error al cambiar los permisos de la tarjeta $gafeteRfid->numero_rfid en la controladora $controladora->ctrl_nombre.");
                    }
                    $solicitud->sgftre_estado = 'DESACTIVADO';
                    $solicitud->sgftre_fecha_desactivado = now();
                    $solicitud->save();

                    $solicitud->CicloVida()->create([
                        'sgftrecs_sgftre_estado' => 'DESACTIVADO',
                        'sgftrece_fecha' => $solicitud->sgftre_fecha_desactivado,
                        'sgftrece_created_at' => now()
                    ]);

                    $solicitud->delete();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error("Error en el servidor! Error: {$e->getMessage()}");
                }
                DB::commit();
            }
        })->between('00:00', '00:20');

        //->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
