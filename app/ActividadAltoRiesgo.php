<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class ActividadAltoRiesgo extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'actividades_alto_riesgo';
    protected $primaryKey = 'actar_id';
    protected $prefix = 'actar_';

    protected $guarded = ['actar_id'];
    public $timestamps = true;

    const CREATED_AT = 'actar_created_at';
    const UPDATED_AT = 'actar_updated_at';
    const DELETED_AT = 'actar_deleted_at';

    protected $dates = ['actar_created_at', 'actar_updated_at', 'actar_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Actividades de Alto Riesgo';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['actar_created_at', 'actar_updated_at', 'actar_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nueva Actividad de Alto Riesgo [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Actividad de Alto Riesgo [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Actividad de Alto Riesgo [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
