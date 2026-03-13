<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CatAcceso extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'cat_accesos';
    protected $primaryKey = 'cacs_id';
    protected $prefix = 'cacs_';

    protected $guarded = ['cacs_id'];
    public $timestamps = true;

    const CREATED_AT = 'cacs_created_at';
    const UPDATED_AT = 'cacs_updated_at';
    const DELETED_AT = 'cacs_deleted_at';

    protected $dates = ['cacs_created_at','cacs_updated_at','cacs_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Categoría de Acceso';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['cacs_created_at','cacs_updated_at','cacs_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una Categoría de Acceso ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Categoría de Acceso ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó una Categoría de Acceso ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


}
