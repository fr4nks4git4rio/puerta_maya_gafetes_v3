<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class RiesgoAsociado extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'riesgos_asociados';
    protected $primaryKey = 'rasoc_id';
    protected $prefix = 'rasoc_';

    protected $guarded = ['rasoc_id'];
    public $timestamps = true;

    const CREATED_AT = 'rasoc_created_at';
    const UPDATED_AT = 'rasoc_updated_at';
    const DELETED_AT = 'rasoc_deleted_at';

    protected $dates = ['rasoc_created_at', 'rasoc_updated_at', 'rasoc_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Riesgos Asociados';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['rasoc_created_at', 'rasoc_updated_at', 'rasoc_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un nuevo Riesgo Asociado [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Riesgo Asociado [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Riesgo Asociado [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
