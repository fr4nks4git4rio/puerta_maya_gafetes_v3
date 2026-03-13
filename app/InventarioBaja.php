<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class InventarioBaja extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'inventario_bajas';
    protected $primaryKey = 'ibaj_id';
    protected $prefix = 'ibaj_';

    protected $guarded = ['ibaj_id'];
    public $timestamps = true;

    const CREATED_AT = 'ibaj_created_at';
    const UPDATED_AT = 'ibaj_updated_at';
    const DELETED_AT = 'ibaj_deleted_at';

    protected $dates = ['ibaj_created_at','ibaj_updated_at','ibaj_deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Baja de inventario';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['ibaj_created_at','ibaj_updated_at','ibaj_deleted_at'];
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
