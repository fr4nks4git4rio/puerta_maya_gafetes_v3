<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class DisennoGafetePaqueteImagen extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $primaryKey = 'dgpi_id';
    protected $prefix = 'dgpi_';

    protected $table = 'disennos_gafetes_paquetes_imagenes';

    protected $guarded = ['dgpi_id'];

    protected $appends = ['src_imagen'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Imagen Paquete Diseño Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un registro de Imagen Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de Imagen Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de Imagen Paquete Diseño Gafete [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getSrcImagenAttribute()
    {
        return $this->Paquete->src_directory . $this->dgpi_imagen;
    }

    ////////////////////////////////////////////////////////////////

    public function Paquete()
    {
        return $this->belongsTo(DisennoGafetePaquete::class, 'dgpi_paquete_id');
    }

    public function CatAcceso()
    {
        return $this->belongsTo(CatAcceso::class, 'dgpi_acceso_id', 'cacs_id');
    }
}
