<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class EquipoHerramienta extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'equipos_herramientas';
    protected $primaryKey = 'eqher_id';
    protected $prefix = 'eqher_';

    protected $guarded = ['eqher_id'];
    public $timestamps = true;

    const CREATED_AT = 'eqher_created_at';
    const UPDATED_AT = 'eqher_updated_at';
    const DELETED_AT = 'eqher_deleted_at';

    protected $dates = ['eqher_created_at', 'eqher_updated_at', 'eqher_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Equipos / Herramientas';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['eqher_created_at', 'eqher_updated_at', 'eqher_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un nuevo Equipo / Herramienta [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Equipo / Herramienta [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Equipo / Herramienta [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
}
