<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CUsoCfdi extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table  = 'c_usocfdi';

    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = ['created_at','updated_at','deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'C_UsoCfdi';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at','deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nuevo uso de CFDI ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un uso de CFDI ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un uso de CFDI ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////



}
