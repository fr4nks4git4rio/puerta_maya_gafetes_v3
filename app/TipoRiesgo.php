<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoRiesgo extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'tipos_riesgo';
    protected $primaryKey = 'trgo_id';
    protected $prefix = 'trgo_';

    protected $guarded = ['trgo_id'];
    public $timestamps = true;

    const CREATED_AT = 'trgo_created_at';
    const UPDATED_AT = 'trgo_updated_at';
    const DELETED_AT = 'trgo_deleted_at';

    protected $dates = ['trgo_created_at', 'trgo_updated_at', 'trgo_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Tipo de Riesgo';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['trgo_created_at', 'trgo_updated_at', 'trgo_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Tipo de Riesgo [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Tipo de Riesgo [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Tipo de Riesgo [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
