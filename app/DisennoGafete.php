<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class DisennoGafete extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'disennos_gafetes';

    protected $guarded = ['id'];
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['seleccionado'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Diseño Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at', 'updated_at', 'deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un registro de Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un registro de Diseño Gafete [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un registro de Diseño Gafete [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getSeleccionadoAttribute()
    {
        $vigencia = DisennoGafeteVigencia::where('activo', 1)->get();
        if ($vigencia->count() > 0) {
            $vigencia = $vigencia->first();
            $seleccionado = false;
            $id = $this->id;
            $vigencia->disennos_gafetes->map(static function ($disenno) use ($id, &$seleccionado) {
                if ($disenno->id === $id)
                    $seleccionado = true;

            });
            return $seleccionado;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////

    public function vigencias()
    {
        return $this->belongsToMany(DisennoGafeteVigencia::class, 'disennos_gafetes_disennos_gafetes_vigencias', 'disenno_gafete_id', 'disenno_gafete_vigencia_id');
    }

    public function cat_acceso()
    {
        return $this->belongsTo(CatAcceso::class, 'cat_acceso_id');
    }
}
