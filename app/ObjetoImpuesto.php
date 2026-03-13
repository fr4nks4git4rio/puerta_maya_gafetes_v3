<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class ObjetoImpuesto extends Model
{
    use LogsActivity;


    protected $table = 'objetos_impuesto';

    protected $guarded = ['oimp_id'];

    protected $appends = ['nombre'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at', 'updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Objetos de Impuesto';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un Objeto de Impuesto [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Objeto de Impuesto [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Objeto de Impuesto [' . $this->getKey() . ']';
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
        return $this->oimp_clave . ' | ' . $this->oimp_descripcion;
    }

}
