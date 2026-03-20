<?php

namespace App\Console\Commands;

use App\Actions\ActivarTarjetaV3;
use App\VGafetesRfidV3;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AsignarPermisos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asignar:permisos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        VGafetesRfidV3::whereNotNull('puertas_numeros')
            ->whereYear('inicio', 2026)
            ->orderBy('empl_id')
            ->chunk(50, function ($gafetes) {

                foreach ($gafetes as $gafeteRfid) {

                    $this->info("Procesando: {$gafeteRfid->numero_rfid}");

                    try {
                        $activar = new ActivarTarjetaV3($gafeteRfid);
                        $res = $activar->execute();

                        if ($res == false) {
                            Log::error("Error: {$gafeteRfid->numero_rfid}");
                        }
                    } catch (\Throwable $e) {
                        Log::error("Excepción: {$gafeteRfid->numero_rfid} - {$e->getMessage()}");
                    }

                    usleep(300000); // 0.3s → reduces de 33min a ~10min aprox
                }
            });

        return 0;
    }
}
