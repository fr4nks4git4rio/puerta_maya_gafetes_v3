<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class LogAcceso extends Model
{
//    use SoftDeletes;
//    use LogsActivity;


    protected $table  = 'log_accesos';
    protected $primaryKey = 'lgac_id';
    protected $prefix = 'lgac_';

    protected $guarded = ['lgac_id'];
    public $timestamps = false;

//    const CREATED_AT = 'lgac_created_at';
//    const UPDATED_AT = 'lgac_updated_at';
//    const DELETED_AT = 'lgac_deleted_at';

    protected $dates = ['lgac_created_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Locales';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['lgac_created_at','lgac_updated_at','lgac_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó un nuevo Log de Acceso ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un Log de Acceso ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un Log de Acceso ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////



}
