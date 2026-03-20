<?php

namespace App\Console\Commands;

use App\GafeteEstacionamiento;
use App\Local;
use App\VGafetesRfidV3;
use Illuminate\Console\Command;

class GafetesInitPermisos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gafetes:init_permisos';

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

                foreach ($gafetes as $gafete) {

                    $solicitud = $gafete->getOriginalRecord();

                    $this->info("Visto: {$solicitud->sgft_id}");

                    if ($solicitud->SolicitudGafeteReasignar) {

                        $this->info("Procesando: {$solicitud->sgft_id}");

                        $solicitud->sgft_permisos = $solicitud->SolicitudGafeteReasignar->sgftre_permisos;
                    }
                    $solicitud->sgft_anio = 2026;
                    $solicitud->save();
                }
            });

        return 0;
    }
}
