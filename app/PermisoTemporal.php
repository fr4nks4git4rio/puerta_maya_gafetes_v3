<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class PermisoTemporal extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'permisos_temporales';
    protected $primaryKey = 'ptmp_id';
    protected $prefix = 'ptmp_';

    protected $guarded = ['ptmp_id'];
    public $timestamps = true;

    const CREATED_AT = 'ptmp_created_at';
    const UPDATED_AT = 'ptmp_updated_at';
    const DELETED_AT = 'ptmp_deleted_at';

    protected $dates = ['ptmp_created_at', 'ptmp_updated_at', 'ptmp_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Permisos Temporales';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['ptmp_created_at', 'ptmp_updated_at', 'ptmp_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Permiso Temporal [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Permiso Temporal [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Permiso Temporal [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }


    public function getPtmpThumbWebAttribute($thumb)
    {
        $thumb = $this->ptmp_thumb;
//        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $path = public_path('storage') . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $thumb;
        Log::error([$file]);
        if ($thumb != "" && file_exists($file)) {
            return asset('storage/empleados/' . $thumb);
        } else {
            return asset('storage/empleados/default_sm.jpg');
        }

    }


    public function getPtmpFotoWebAttribute($foto)
    {
        $foto = $this->ptmp_foto;
//        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $path = public_path('storage') . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $foto;

        if (file_exists($file)) {
            return asset('storage/empleados/' . $foto);
        } else {
            return 'No existe ' . $foto;
        }
    }


    public function getPtmpEstadoAttribute($ptmp_estado)
    {

        if ($ptmp_estado == 'APROBADO') {
            if (intval($this->ptmp_gfpi_id) > 0) {
                return 'ASIGNADO';
            }
        }

        if ($ptmp_estado == 'ENTREGADO') {
            $fin = \Carbon\Carbon::createFromFormat('Y-m-d', $this->ptmp_vigencia_final);
            $today = \Carbon\Carbon::today();

            if ($today->gt($fin)) {
                return 'VENCIDO';
            }
        }

        return $ptmp_estado;

    }


    /**
     * Devuelve una colleccion de BD de los permisos ENTREGADOS próximos a vencer
     *
     * @return mixed
     */
    public static function proximosVencer(int $enCuantosDias = 0)
    {


        return PermisoTemporal::wherePtmpEstado('ENTREGADO')
            ->whereRaw('( ptmp_vigencia_final BETWEEN CURDATE() AND CURDATE() + INTERVAL ' . $enCuantosDias . ' DAY  )')
            ->orderBy('ptmp_vigencia_final')
            ->get();

    }



    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Local()
    {
        return $this->belongsTo('App\Local', 'ptmp_lcal_id');
    }

    public function Cargo()
    {
        return $this->belongsTo('App\CatCargo', 'ptmp_crgo_id');
    }

    public function GafetePreimpreso()
    {
        return $this->belongsTo('App\GafetePreimpreso', 'ptmp_gfpi_id');
    }

    public function AprobadoPor()
    {
        return $this->hasOne('App\User', 'id', 'ptmp_approved_by');
    }

    public function toStringQr()
    {
//        $local = $this->Local->lcal_nombre_comercial;
//        $fecha_inicio = $this->ptmp_vigencia_inicial;
//        $fecha_fin = $this->ptmp_vigencia_final;
//        $vacunado = $this->ptmp_vacunado ? 'SI' : 'NO';
//        return Request::root()."/permiso-temporal/formato-oficial-pdf/$this->ptmp_id";
        return "PT-$this->ptmp_id";
    }

}
