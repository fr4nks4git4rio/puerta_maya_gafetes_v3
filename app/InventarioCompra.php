<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class InventarioCompra extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'inventario_compras';
    protected $primaryKey = 'icmp_id';
    protected $prefix = 'icmp_';

    protected $guarded = ['icmp_id'];
    public $timestamps = true;

    const CREATED_AT = 'icmp_created_at';
    const UPDATED_AT = 'icmp_updated_at';
    const DELETED_AT = 'icmp_deleted_at';

    protected $dates = ['icmp_created_at','icmp_updated_at','icmp_deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Compra de tarjetas';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['icmp_created_at','icmp_updated_at','icmp_deleted_at'];
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
