<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CRegimenFiscal extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'c_regimenfiscal';

    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['nombre'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'C_RegimenFiscal';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un nuevo Regimen Fiscal [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Regimen Fiscal [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Regimen Fiscal [' . $this->getKey() . ']';
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
        return $this->codigo . ' | '. $this->descripcion;
    }

}
