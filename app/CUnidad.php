<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CUnidad extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table  = 'c_claveunidad';

    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = ['created_at','updated_at','deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'C_Unidad';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at','deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nueva Unidad ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Unidad ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó una Unidad ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////



}
