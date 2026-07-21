<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class EppEspecifico extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'epp_especificos';
    protected $primaryKey = 'eppe_id';
    protected $prefix = 'eppe_';

    protected $guarded = ['eppe_id'];
    public $timestamps = true;

    const CREATED_AT = 'eppe_created_at';
    const UPDATED_AT = 'eppe_updated_at';
    const DELETED_AT = 'eppe_deleted_at';

    protected $dates = ['eppe_created_at', 'eppe_updated_at', 'eppe_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'EPP Específico';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['eppe_created_at', 'eppe_updated_at', 'eppe_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo EPP Específico [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un EPP Específico [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un EPP Específico [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
