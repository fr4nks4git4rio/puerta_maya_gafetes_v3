<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CProducto extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table  = 'c_claveprodserv';

    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = ['created_at','updated_at','deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'C_Producto';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at','deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nueva Producto/Servicio ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Producto/Servicio ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó una Producto/Servicio ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////



}
