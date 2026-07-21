<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class MedidaControlRiesgo extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'medidas_control_riesgo';
    protected $primaryKey = 'medcr_id';
    protected $prefix = 'medcr_';

    protected $guarded = ['medcr_id'];
    public $timestamps = true;

    const CREATED_AT = 'medcr_created_at';
    const UPDATED_AT = 'medcr_updated_at';
    const DELETED_AT = 'medcr_deleted_at';

    protected $dates = ['medcr_created_at', 'medcr_updated_at', 'medcr_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Medidas Control de Riesgo';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['medcr_created_at', 'medcr_updated_at', 'medcr_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nueva Medida de Control de Riesgo [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Medida de Control de Riesgo [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Medida de Control de Riesgo [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
