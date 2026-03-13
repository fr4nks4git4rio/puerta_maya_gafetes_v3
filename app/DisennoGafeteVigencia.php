<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity;

class DisennoGafeteVigencia extends Model
{
    use LogsActivity;


    protected $table = 'disennos_gafetes_vigencias';

    protected $guarded = ['id'];
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at', 'updated_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Vigencias de Diseño Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un registro de Vigencia de Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de Vigencia de Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de Vigencia de Diseño Gafete [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////

    public function disennos_gafetes()
    {
        return $this->belongsToMany(DisennoGafete::class, 'disennos_gafetes_disennos_gafetes_vigencias', 'disenno_gafete_vigencia_id', 'disenno_gafete_id');
    }


    /**
     * @param $params
     * @param $extra_params
     * @return DisennoGafeteVigencia
     */
    public static function createOrUpdate($params, $extra_params)
    {
        $record = DisennoGafeteVigencia::where($params)->first();

        if ($record) {
            $record->update($extra_params);
            $record = DisennoGafeteVigencia::where($params)->first();
        } else {
            $data = array_merge($params, $extra_params);
            $record = DisennoGafeteVigencia::create($data);
        }
        return $record;
    }

    public static function checkFechaIncioImpresion()
    {
        $fecha_inicio_impresion = settings()->get('fecha_inicio_impresion', 0);
        if ($fecha_inicio_impresion != '0' && today()->format('Y-m-d') >= $fecha_inicio_impresion) {
            $vigencia_activa = DisennoGafeteVigencia::where('activo', 1)->get();
            if ($vigencia_activa->count() > 0) {
                $vigencia_activa = $vigencia_activa->first();
                settings()->set('anio_impresion', $vigencia_activa->anno);
                settings()->save();
                return true;
            }
            return false;
        }
        return false;
    }
}
