<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoComprobante extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'tipos_comprobante';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at','updated_at','deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Tipos de Comprobante';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at','deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó un Tipo de Comprobante ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un Tipo de Comprobante ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un Tipo de Comprobante ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


}
