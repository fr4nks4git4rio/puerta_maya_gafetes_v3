<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;

class SolicitudGafeteReasignar extends Model
{
    use LogsActivity;


    protected $table = 'solicitudes_gafetes_reasignar';
    protected $primaryKey = 'sgftre_id';
    protected $prefix = 'sgftre_';

    protected $guarded = ['sgftre_id'];
    public $timestamps = true;

    const CREATED_AT = 'sgftre_created_at';
    const UPDATED_AT = 'sgftre_updated_at';

    protected $dates = ['sgftre_created_at', 'sgftre_updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Solicitudes Gafete Reasignar';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['sgftre_created_at', 'sgftre_updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nueva Solicitud de Gafete a Reasignar de ' . $this->sgftre_permisos . ' [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Solicitud de Gafete a Reasignar de ' . $this->sgftre_permisos . ' [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Solicitud de Gafete a Reasignar de ' . $this->sgftre_permisos . ' [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function Empleado()
    {
        return $this->belongsTo('App\Empleado', 'sgftre_empl_id');
    }

    public function Local()
    {
        return $this->belongsTo('App\Local', 'sgftre_lcal_id');
    }

    public function Gafete()
    {
        return $this->belongsTo('App\SolicitudGafete', 'sgftre_sgft_id');
    }
}
