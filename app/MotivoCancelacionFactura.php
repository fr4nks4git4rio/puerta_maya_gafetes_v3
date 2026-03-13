<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class MotivoCancelacionFactura extends Model
{
    use LogsActivity;


    protected $table = 'motivos_cancelacion_factura';

    protected $primaryKey = 'mcf_id';

    protected $guarded = ['mcf_id'];

    protected $appends = ['label'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at', 'updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Motivos Cancelación Factura';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un Motivo de Cancelación de Factura [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Motivo de Cancelación de Factura [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Motivo de Cancelación de Factura [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////

    public function getLabelAttribute()
    {
        return $this->mcf_codigo . ' ' . $this->mcf_descripcion;
    }

}
