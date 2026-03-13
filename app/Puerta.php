<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Puerta extends Model
{
    use SoftDeletes;
//    use LogsActivity;

    protected $table = 'puertas';
    protected $primaryKey = 'door_id';
    protected $prefix = 'door_';

    protected $guarded = ['door_id'];
    public $timestamps = true;


    const CREATED_AT = 'door_created_at';
    const UPDATED_AT = 'door_updated_at';
    const DELETED_AT = 'door_deleted_at';

    protected $dates = ['door_created_at', 'door_updated_at', 'door_deleted_at'];

    // protected $appends = ['pin_value'];

    ////LOG CONFIG///////////////////////////////////////////////////

//    protected static $logName = 'C_UsoCfdi';
//    protected static $logAttributes = ['*'];
//    protected static $logAttributesToIgnore = ['created_at','updated_at','deleted_at'];
//    protected static $logOnlyDirty = true;
//
//    public function getDescriptionForEvent(string $eventName): string
//    {
//        switch($eventName){
//             case 'created':
//                $message = 'Creó una nuevo uso de CFDI ['.$this->getKey().']';
//                break;
//            case 'updated':
//                $message = 'Actualizó un uso de CFDI ['.$this->getKey().']';
//                break;
//            case 'deleted':
//                $message = 'Eliminó un uso de CFDI ['.$this->getKey().']';
//                break;
//            default:
//                $message = "This model has been {$eventName} [".$this->getKey()."]";
//                break;
//        }
//
//        return $message;
//    }


    // public function getPinValueAttribute()
    // {
    //     return optional($this->Pin)->pin_value;
    // }

    ////////////////////////////////////////////////////////////////


    public function Controladora()
    {
        return $this->belongsTo(Controladora::class, 'door_controladora_id', 'ctrl_id');
    }

    // public function Pin()
    // {
    //     return $this->belongsTo(Pin::class, 'door_pin_id', 'pin_id');
    // }

}
