<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Controladora
 * @package App
 *
 * @property string $ctrl_nombre
 * @property string $ctrl_ip
 * @property string $ctrl_usuario
 * @property string $ctrl_contrasenna
 * @property string $ctrl_descripcion
 */
class Controladora extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'tb_controladoras';

    protected $primaryKey = 'ctrl_id';

    protected $guarded = ['ctrl_id'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Controladoras';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una Controladora [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Controladora [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Controladora [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////


    //    public static function controladoraAccesoPeatonal()
    //    {
    //        return Controladora::find(1);
    //    }
    //
    //    public static function controladoraAccesoAutosMotos()
    //    {
    //        return Controladora::find(2);
    //    }

    public function Puertas()
    {
        return $this->hasMany('App\Puerta', 'door_controladora_id');
    }
}
