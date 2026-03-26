<?php

namespace App\Console\Commands;

use App\SolicitudGafeteReasignar;
use Illuminate\Console\Command;

class GenerarCicloVidaSolicitudPermisosEstacionamiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sgr:generar_ciclo_vida';

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
        SolicitudGafeteReasignar::all()->map(function (SolicitudGafeteReasignar $sgr) {
            $this->info("Solicitud procesada: $sgr->sgftre_id");
            if ($sgr->sgftre_fecha_solicitado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'PENDIENTE',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_solicitado,
                    'sgftrece_created_at' => now()
                ]);

            if ($sgr->sgftre_fecha_denegado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'DENEGADO',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_denegado,
                    'sgftrece_comentarios' => $sgr->sgftre_comentarios_rechazo,
                    'sgftrece_created_at' => now()
                ]);

            if ($sgr->sgftre_fecha_asignado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'ASIGNADO',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_asignado,
                    'sgftrece_created_at' => now()
                ]);

            if ($sgr->sgftre_fecha_autorizado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'AUTORIZADO',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_autorizado,
                    'sgftrece_created_at' => now()
                ]);

            if ($sgr->sgftre_fecha_cancelado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'CANCELADO',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_cancelado,
                    'sgftrece_created_at' => now()
                ]);

            if ($sgr->sgftre_fecha_desactivado)
                $sgr->CicloVida()->create([
                    'sgftrecs_sgftre_estado' => 'DESACTIVADO',
                    'sgftrece_fecha' => $sgr->sgftre_fecha_desactivado,
                    'sgftrece_created_at' => now()
                ]);
        });
    }
}
