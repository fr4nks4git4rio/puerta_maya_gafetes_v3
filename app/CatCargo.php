<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class CatCargo extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'cat_cargos';
    protected $primaryKey = 'crgo_id';
    protected $prefix = 'crgo_';

    protected $guarded = ['crgo_id'];
    public $timestamps = true;

    const CREATED_AT = 'crgo_created_at';
    const UPDATED_AT = 'crgo_updated_at';
    const DELETED_AT = 'crgo_deleted_at';

    protected $dates = ['crgo_created_at','crgo_updated_at','crgo_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Cargos';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['crgo_created_at','crgo_updated_at','crgo_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó un nuevo Cargo ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un Cargo ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un Cargo ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


    public function Accesos(){
      return  $this->belongsToMany('App\CatAcceso','cargo_accesos','cgac_crgo_id','cgac_cacs_id');
    }

}
