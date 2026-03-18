<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;

class SolicitudGafete extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'solicitudes_gafetes';
    protected $primaryKey = 'sgft_id';
    protected $prefix = 'sgft_';
    protected $appends = ['is_active', 'is_disabled', 'pin_value'];

    protected $guarded = ['sgft_id'];
    public $timestamps = true;

    const CREATED_AT = 'sgft_created_at';
    const UPDATED_AT = 'sgft_updated_at';
    const DELETED_AT = 'sgft_deleted_at';

    protected $dates = ['sgft_created_at', 'sgft_updated_at', 'sgft_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Solicitudes Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['sgft_created_at', 'sgft_updated_at', 'sgft_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nueva Solicitud de Gafete de ' . $this->sgft_permisos . ' [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Solicitud de Gafete de ' . $this->sgft_permisos . ' [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Solicitud de Gafete de ' . $this->sgft_permisos . ' [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getIsActiveAttribute()
    {
        return $this->sgft_activated_at !== null && ($this->sgft_disabled_at === null && $this->sgft_deleted_at === null);
    }

    public function getIsDisabledAttribute()
    {
        return $this->sgft_disabled_at !== null || $this->sgft_deleted_at !== null;
    }

    public function getLocalDescriptionAttribute()
    {
        return $this->sgft_anio . ' ' . $this->sgft_permisos . ' No. ' . $this->sgft_numero_estacionamiento;
    }

    public function getPinValueAttribute()
    {
        $pin_value = 0;
        $this->Puertas->map(static function ($puerta) use (&$pin_value) {
            $pin_value += $puerta->pin_value;
        });

        return $pin_value;
    }

    public function setActivatedAt($datetime = false)
    {
        $this->sgft_activated_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function setDisabledAt($datetime = false)
    {
        $this->sgft_disabled_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function getVGafeteRfid()
    {
        return VGafetesRfid::whereTipo('planta')->whereRefId($this->sgft_id)->first();
    }

    public function getVGafeteRfidV2()
    {
        return VGafetesRfidV2::whereTipo('PEATONAL')->whereRefId($this->sgft_id)->first();
    }
    public function getVGafeteRfidV3()
    {
        return VGafetesRfidV3::whereRefId($this->sgft_id)->first();
    }


    public function getSgftThumbWebAttribute($thumb)
    {
        $thumb = $this->sgft_thumb;
        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $thumb;

        if ($thumb != "" && file_exists($file)) {
            return asset('storage/empleados/' . $thumb);
        } else {
            return asset('storage/empleados/default_sm.jpg');
        }
    }

    public function getSgftFotoWebAttribute($foto)
    {
        $foto = $this->sgft_foto;
        $path = storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $foto;
        Log::error($file);

        if (file_exists($file)) {
            return asset('storage/empleados/' . $foto);
        } else {
            return asset('storage/empleados/default_sm.jpg');
            //            return 'No existe ' . $foto;
        }
    }

    public function getSgftNumeroUsoComprobanteAttribute()
    {

        $comprobante = $this->ComprobantePago;
        if ($comprobante != null) {
            $solicitudes = SolicitudGafete::whereSgftCpagId($comprobante->cpag_id)
                ->whereSgftTipo($this->sgft_tipo)
                ->where("sgft_created_at", "<=", $this->sgft_created_at)
                ->whereRaw(" sgft_estado NOT IN ('CANCELADA') ")
                ->get()->count();

            $permisosTemporales = PermisoTemporal::wherePtmpCpagId($comprobante->cpag_id)
                ->wherePtmpEstadoExtemporaneo('PAGADO')
                ->where("ptmp_fecha_resolucion_extemporanea", "<=", $this->sgft_created_at)
                ->get()->count();

            return $solicitudes + $permisosTemporales;
        }


        return 0;
    }

    public function getSgftCostoTarifaAttribute()
    {

        if ($this->sgft_gratuito == 1)
            return 0;
        else {
            if (Str::contains($this->sgft_permisos, ['AUTO', 'MOTO'])) {
                return ($this->sgft_tipo == 'PRIMERA VEZ') ?
                    settings()->get('gft_est_auto_pvez', '0') :
                    settings()->get('gft_est_auto_reposicion', '0');
            } else {
                return ($this->sgft_tipo == 'PRIMERA VEZ') ?
                    settings()->get('gft_acceso_pvez', '0') :
                    settings()->get('gft_acceso_reposicion', '0');
            }
        }
    }

    /**
     * Devuelve una colleccion de gafetes de estacionamiento que todavia no se han desactivado
     * @param Local $local
     * @return mixed
     */
    public static function getGafetesEstacionaminetoActivos(Local $local)
    {

        return SolicitudGafete::whereSgftLcalId($local->lcal_id)
            ->where('sgft_numero', '>', 0)
            ->whereNull('sgft_disabled_at')
            ->whereRaw('sgft_permisos like ? or sgft_permisos like ?', ['AUTO', 'MOTO'])
            ->whereIn('sgft_estado', ['ENTREGADA', 'CANCELADA'])
            ->get();
    }

    public function toStringQr()
    {
        return "GA-$this->sgft_empl_id";
    }

    public function Controladora()
    {
        return optional($this->Puertas()->first())->Controladora;
    }



    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Empleado()
    {
        return $this->belongsTo('App\Empleado', 'sgft_empl_id');
    }

    public function Local()
    {
        return $this->belongsTo('App\Local', 'sgft_lcal_id');
    }

    public function ComprobantePago()
    {
        return $this->hasOne('App\ComprobantePago', 'cpag_id', 'sgft_cpag_id');
    }

    public function Puertas()
    {
        return $this->belongsToMany(Puerta::class, 'solicitudes_gafetes_puertas', 'solicitud_gafete_id', 'puerta_id');
    }
}
