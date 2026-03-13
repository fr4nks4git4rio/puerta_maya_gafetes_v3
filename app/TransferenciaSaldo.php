<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class TransferenciaSaldo extends Model
{
    use LogsActivity;


    protected $table  = 'transferencias_saldo';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at','updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Transferencia de Saldo';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una Transferencia ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Transferencia ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó una Transferencia ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


}
