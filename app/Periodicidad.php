<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Periodicidad extends Model
{
    use LogsActivity;


    protected $table = 'periodicidades';

    protected $guarded = ['pdad_id'];

    protected $appends = ['nombre'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at', 'updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Periodicidades';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una Periodicidad [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Periodicidad [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Periodicidad [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////

    public function getNombreAttribute()
    {
        return $this->pdad_clave . ' | ' . $this->pdad_descripcion;
    }

}
