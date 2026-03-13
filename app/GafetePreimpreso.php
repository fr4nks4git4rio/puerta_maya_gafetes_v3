<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class GafetePreimpreso extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'gafetes_preimpresos';
    protected $primaryKey = 'gfpi_id';
    protected $prefix = 'gfpi_';

    protected $guarded = ['gfpi_id'];
    public $timestamps = true;

    const CREATED_AT = 'gfpi_created_at';
    const UPDATED_AT = 'gfpi_updated_at';
    const DELETED_AT = 'gfpi_deleted_at';

    protected $dates = ['gfpi_created_at','gfpi_updated_at','gfpi_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Gafetes Preimpresos';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['gfpi_created_at','gfpi_updated_at','gfpi_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nuevo Gafete Preimpreso ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó un Gafete Preimpreso ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un Gafete Preimpreso ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }

    public function setActivatedAt($datetime = false){
        $this->gfpi_activated_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function setDisabledAt($datetime = false){
        $this->gfpi_disabled_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function getVGafeteRfid(){
        return VGafetesRfid::whereTipo('permiso')->whereRefId($this->gfpi_id)->first();
    }


    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    // public function Local(){
    //     return $this->belongsTo('App\Local','gfpi_lcal_id');
    // }

    // public function Cargo(){
    //     return $this->belongsTo('App\CatCargo','gfpi_crgo_id');
    // }



}
