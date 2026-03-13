<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Empleado extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'empleados';
    protected $primaryKey = 'empl_id';
    protected $prefix = 'empl_';

    protected $guarded = ['empl_id'];
    public $timestamps = true;

    const CREATED_AT = 'empl_created_at';
    const UPDATED_AT = 'empl_updated_at';
    const DELETED_AT = 'empl_deleted_at';

    protected $dates = ['empl_created_at', 'empl_updated_at', 'empl_deleted_at'];

    protected $appends = ['empl_foto_web', 'empl_thumb_web'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Empleados';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['empl_created_at', 'empl_updated_at', 'empl_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un Empleado [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Empleado [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Empleado [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////


    public function getEmplThumbWebAttribute()
    {
        $thumb = $this->empl_thumb;
//        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $path = public_path('storage') . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $thumb;

        if ($thumb != "" && file_exists($file)) {
            return asset('storage/empleados/' . $thumb);
        } else {
            return asset('storage/empleados/default_sm.jpg');
        }

    }


    public function getEmplFotoWebAttribute()
    {
        $foto = $this->empl_foto;
//        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $path = public_path('storage') . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $foto;

        if (file_exists($file)) {
            return asset('storage/empleados/' . $foto);
        } else {
            return 'No existe ' . $foto;
        }
    }

    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Cargo()
    {
        return $this->belongsTo('App\CatCargo', 'empl_crgo_id');
    }

    public function Local()
    {
        return $this->belongsTo('App\Local', 'empl_lcal_id');
    }

    public function Ubicacion()
    {
        return $this->hasOne('App\EmpleadoUbicacion', 'emplub_empl_id')->withDefault([
            'emplub_ubicacion' => 0
        ]);
    }

    public function GafeteReasignado()
    {
        return $this->hasOne('App\SolicitudGafeteReasignar', 'sgftre_empl_id');
    }

    public function GafetesAcceso()
    {
        return $this->hasMany(SolicitudGafete::class, 'sgft_empl_id');
    }

    public function GafeteAcceso()
    {
        return SolicitudGafete::where('sgft_empl_id', $this->empl_id)
            ->where('sgft_activated_at', '!=', null)
            ->where('sgft_disabled_at', null)
            ->where('sgft_deleted_at', null)
            ->orderBy('sgft_created_at', 'desc')
            ->first();
    }

}
