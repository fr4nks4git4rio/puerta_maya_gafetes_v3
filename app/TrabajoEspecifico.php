<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class TrabajoEspecifico extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'trabajos_especificos';
    protected $primaryKey = 'tesp_id';
    protected $prefix = 'tesp_';

    protected $guarded = ['tesp_id'];
    public $timestamps = true;

    const CREATED_AT = 'tesp_created_at';
    const UPDATED_AT = 'tesp_updated_at';
    const DELETED_AT = 'tesp_deleted_at';

    protected $dates = ['tesp_created_at', 'tesp_updated_at', 'tesp_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Trabajo Específico';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['tesp_created_at', 'tesp_updated_at', 'tesp_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Trabajo Específico [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Trabajo Específico [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Trabajo Específico [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function TipoMantenimiento()
    {
        return $this->belongsTo('App\TipoMantenimiento', 'tesp_tmtt_id');
    }
    public function TipoRiesgo()
    {
        return $this->belongsTo('App\TipoRiesgo', 'tesp_trgo_id');
    }
    public function EppBasicos()
    {
        return $this->belongsToMany('App\EppBasico', 'trab_esp_epp_basicos', 'teeb_tesp_id', 'teeb_eppb_id');
    }

    public function EppEspecificos()
    {
        return $this->belongsToMany('App\EppEspecifico', 'trab_esp_epp_especificos', 'teee_tesp_id', 'teee_eppe_id');
    }
}
