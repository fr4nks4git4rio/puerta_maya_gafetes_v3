<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 21/1/2022
 * Time: 19:04
 */

namespace App\Notifications;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NotificationHelper
{
    public static function getNotificationsByRole($roles = array())
    {
        $query = DB::table('notifications as n')
            ->selectRaw("
            n.id,
            n.data,
            n.created_at")
            ->where('read_at', null)
            ->orderBy('created_at', 'desc')
            ->where('notifiable_id', auth()->id());
//        $query->where(static function ($q) use ($roles) {
//            if (Arr::exists(array_flip($roles), 'RECEPCIÓN')) {
//                $q->orWhereIn('type', [ComprobanteFueraTiempo::class, SolicitudGafeteImpreso::class, ComprobanteValidado::class,
//                    PermisoTemporalRechazado::class]);
//            }
//            if (Arr::exists(array_flip($roles), 'LOCATARIO')) {
//                $q->orWhereIn('type', [ComprobanteValidado::class, FacturaTimbrada::class, GafeteEstacionamientoImpreso::class,
//                    GafeteEstacionamientoRechazado::class, PermisoMantenimientoAprobado::class, PermisoMantenimientoRechazado::class,
//                    PermisoTemporalAprobado::class, PermisoTemporalRechazado::class, PermisoTemporalVencido::class]);
//            }
//            if (Arr::exists(array_flip($roles), 'CONTABILIDAD')) {
//                $q->orWhereIn('type', [ComprobanteFueraTiempo::class]);
//            }
//            $q->orWhereIn('type', [ArriboCruceroActualizado::class]);
//        });

        return $query->get();

    }
}