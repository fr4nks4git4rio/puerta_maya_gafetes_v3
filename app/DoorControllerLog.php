<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class DoorControllerLog extends Model
{
    use SoftDeletes;
    //    use LogsActivity;

    protected $table  = 'door_controller_log_v3';
    protected $primaryKey = 'dclg_id';
    protected $prefix = 'dclg_';

    protected $guarded = ['dclg_id'];
    protected $connection = 'mysql_logs';
    public $timestamps = true;


    const CREATED_AT = 'dclg_created_at';
    const UPDATED_AT = 'dclg_updated_at';
    const DELETED_AT = 'dclg_deleted_at';

    protected $dates = ['dclg_created_at', 'dclg_updated_at', 'dclg_deleted_at'];

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
    ////////////////////////////////////////////////////////////////



}
