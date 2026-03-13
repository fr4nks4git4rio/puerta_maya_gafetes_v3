<?php

namespace App;

//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class BarcoEvento extends Model
{
//    use SoftDeletes;
    use LogsActivity;

    protected $connection= 'shorex';
    protected $table  = 'barco_puerto_disponible';
    protected $primaryKey = 'id';
//    protected $prefix = '';

    protected $guarded = ['id'];
    public $timestamps = false;

//    const CREATED_AT = 'crgo_created_at';
//    const UPDATED_AT = 'crgo_updated_at';
//    const DELETED_AT = 'crgo_deleted_at';

//    protected $dates = ['crgo_created_at','crgo_updated_at','crgo_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Arribo de Crucero';
    protected static $logAttributes = ['*'];
//    protected static $logAttributesToIgnore = ['crgo_created_at','crgo_updated_at','crgo_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
            case 'created':
                $message = 'Creó un nuevo evento de arribo de crucero ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un evento de arribo de crucero ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un evento de arribo de crucero ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


    public function Barco(){
        return  $this->belongsTo('App\Barco','id_barco');
    }

}
