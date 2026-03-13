<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Inventario extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'inventario_tarjetas';
    protected $primaryKey = 'invt_id';
    protected $prefix = 'invt_';

    protected $guarded = ['invt_id'];
    public $timestamps = true;

    const CREATED_AT = 'invt_created_at';
    const UPDATED_AT = 'invt_updated_at';
    const DELETED_AT = 'invt_deleted_at';

    protected $dates = ['invt_created_at','invt_updated_at','invt_deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Inventario';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['invt_created_at','invt_updated_at','invt_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó un registro de inventario ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de inventario ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de inventario ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////
}
