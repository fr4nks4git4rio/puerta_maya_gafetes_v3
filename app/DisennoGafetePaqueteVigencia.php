<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class DisennoGafetePaqueteVigencia extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $primaryKey = 'dgpv_id';
    protected $prefix = 'dgpv_';

    protected $table = 'disennos_gafetes_paquetes_vigencias';

    protected $guarded = ['dgpv_id'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Vigencia Paquete Diseño Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un registro de Vigencia Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de Vigencia Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de Vigencia Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public static function checkFechaIncioImpresion()
    {
        $fecha_inicio_impresion = settings()->get('fecha_inicio_impresion', 0);
        if ($fecha_inicio_impresion != '0' && today()->format('Y-m-d') >= $fecha_inicio_impresion) {
            $paquete_activo = DisennoGafetePaquete::where('dgp_seleccionado', 1)->get();
            if ($paquete_activo->count() > 0) {
                $paquete_activo = $paquete_activo->first();
                settings()->set('anio_impresion', $paquete_activo->Vigencia->dgpv_anno);
                settings()->save();

                GafeteEstacionamiento::where('gest_anio', '!=', settings()->get('anio_impresion'))
                    ->update(['gest_disabled_at' => now()]);

                return true;
            }
            return false;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////

    public function Paquete()
    {
        return $this->belongsTo(DisennoGafetePaquete::class, 'dgpv_paquete_id', 'dgp_id');
    }

}
