<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class DisennoGafetePaquete extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $primaryKey = 'dgp_id';
//    protected $prefix = 'dgp_';

    protected $table = 'disennos_gafetes_paquetes';

    protected $guarded = ['dgp_id'];
    protected $appends = ['src_directory'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Paquete Diseño Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un registro de Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getSrcDirectoryAttribute()
    {
        return "/disennos_gafetes/paquete-" . $this->getKey();
    }

    public static function PaqueteSeleccionado()
    {
        return DisennoGafetePaquete::where('dgp_seleccionado', 1)->first();
    }

    ////////////////////////////////////////////////////////////////

    public function Imagenes()
    {
        return $this->hasMany(DisennoGafetePaqueteImagen::class, 'dgpi_paquete_id');
    }

    public function ImagenesPocas()
    {
        return $this->hasMany(DisennoGafetePaqueteImagen::class, 'dgpi_paquete_id')->take(2);
    }

    public function ImagenesFront()
    {
        return $this->hasMany(DisennoGafetePaqueteImagen::class, 'dgpi_paquete_id')
            ->where('dgpi_is_front', 1);
    }

    public function ImagenBack()
    {
        return $this->hasOne(DisennoGafetePaqueteImagen::class, 'dgpi_paquete_id')
            ->where('dgpi_is_front', 0)
            ->where(function ($q){
                $q->where('dgpi_is_admin', 0)
                    ->orWhere('dgpi_is_admin', null);
            });
    }

    public function ImagenBackAdmin()
    {
        return $this->hasOne(DisennoGafetePaqueteImagen::class, 'dgpi_paquete_id')
            ->where('dgpi_is_front', 0)
            ->where('dgpi_is_admin', 1);
    }

    public function Vigencia()
    {
        return $this->hasOne(DisennoGafetePaqueteVigencia::class, 'dgpv_paquete_id')->latest();
    }
}
