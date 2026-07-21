<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class EppBasico extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'epp_basicos';
    protected $primaryKey = 'eppb_id';
    protected $prefix = 'eppb_';

    protected $guarded = ['eppb_id'];
    public $timestamps = true;

    const CREATED_AT = 'eppb_created_at';
    const UPDATED_AT = 'eppb_updated_at';
    const DELETED_AT = 'eppb_deleted_at';

    protected $dates = ['eppb_created_at', 'eppb_updated_at', 'eppb_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'EPP Básico';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['eppb_created_at', 'eppb_updated_at', 'eppb_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo EPP Básico [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un EPP Básico [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un EPP Básico [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
