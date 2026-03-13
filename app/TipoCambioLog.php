<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class TipoCambioLog extends Model
{
//    use SoftDeletes;
//    use LogsActivity;


    protected $table  = 'tipo_cambio_log';
    protected $primaryKey = 'tclg_id';
    protected $prefix = 'tclg_';

    protected $guarded = ['tclg_id'];
    public $timestamps = false;

//    const CREATED_AT = 'ptmp_created_at';
//    const UPDATED_AT = 'ptmp_updated_at';
//    const DELETED_AT = 'ptmp_deleted_at';
//
//    protected $dates = ['ptmp_created_at','ptmp_updated_at','ptmp_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

//    protected static $logName = 'Permisos Temporales';
//    protected static $logAttributes = ['*'];
//    protected static $logAttributesToIgnore = ['ptmp_created_at','ptmp_updated_at','ptmp_deleted_at'];
//    protected static $logOnlyDirty = true;
//
//    public function getDescriptionForEvent(string $eventName): string
//    {
//        switch($eventName){
//             case 'created':
//                $message = 'Creó una nuevo Permiso Temporal ['.$this->getKey().']';
//                break;
//            case 'updated':
//                $message = 'Actualizó un Permiso Temporal ['.$this->getKey().']';
//                break;
//            case 'deleted':
//                $message = 'Eliminó un Permiso Temporal ['.$this->getKey().']';
//                break;
//            default:
//                $message = "This model has been {$eventName} [".$this->getKey()."]";
//                break;
//        }
//
//        return $message;
//    }





}
