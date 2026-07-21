<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoMantenimiento extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'tipos_mantenimiento';
    protected $primaryKey = 'tmtt_id';
    protected $prefix = 'tmtt_';

    protected $guarded = ['tmtt_id'];
    public $timestamps = true;

    const CREATED_AT = 'tmtt_created_at';
    const UPDATED_AT = 'tmtt_updated_at';
    const DELETED_AT = 'tmtt_deleted_at';

    protected $dates = ['tmtt_created_at', 'tmtt_updated_at', 'tmtt_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Tipos de Mantenimiento';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['tmtt_created_at', 'tmtt_updated_at', 'tmtt_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Tipo de Mantenimiento [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Tipo de Mantenimiento [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Tipo de Mantenimiento [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function TrabajosEspecificos()
    {
        return $this->hasMany('\App\TrabajoEspecifico', 'tesp_tmtt_id');
    }
}
