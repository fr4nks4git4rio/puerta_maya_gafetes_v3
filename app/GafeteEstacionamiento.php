<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class GafeteEstacionamiento extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'gafetes_estacionamiento';
    protected $primaryKey = 'gest_id';
    protected $prefix = 'gest_';

    protected $guarded = ['gest_id'];
    public $timestamps = true;

    const CREATED_AT = 'gest_created_at';
    const UPDATED_AT = 'gest_updated_at';
    const DELETED_AT = 'gest_deleted_at';

    protected $dates = ['gest_created_at', 'gest_updated_at', 'gest_deleted_at'];

    protected $appends = ['pin_value'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Gafetes de Estacionamiento';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['gest_created_at', 'gest_updated_at', 'gest_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Gafete de Estacionamiento [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Gafete de Estacionamiento [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Gafete de Estacionamiento [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }


    public function getGestCostoTarifaAttribute()
    {

        if ($this->gest_gratuito == 1)
            return 0;

        else
            if($this->gest_tipo == 'AUTO'){
                return ($this->gest_tipo_solicitud == 'PRIMERA VEZ') ?
                    settings()->get('gft_est_auto_pvez', '0') :
                    settings()->get('gft_est_auto_reposicion', '0');
            }else{
                return ($this->gest_tipo_solicitud == 'PRIMERA VEZ') ?
                    settings()->get('gft_est_moto_pvez', '0') :
                    settings()->get('gft_est_moto_reposicion', '0');
            }

    }

    public function getLocalDescriptionAttribute()
    {
        return $this->gest_anio . ' ' . $this->gest_tipo . ' No. ' . $this->gest_numero;
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
        $this->gest_activated_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function setDisabledAt($datetime = false)
    {
        $this->gest_disabled_at = $datetime ? $datetime : date('Y-m-d H:i:s');
        $this->save();
    }

    public function getVGafeteRfid()
    {
        return VGafetesRfid::whereTipo('estacionamiento')->whereRefId($this->gest_id)->first();
    }

    public function getVGafeteRfidV2()
    {
        return VGafetesRfidV2::whereIn('tipo', ['AUTO', 'MOTO'])->whereRefId($this->gest_id)->first();
    }
    public function getVGafeteRfidV3()
    {
        return VGafetesRfidV3::whereIn('tipo', ['AUTO', 'MOTO'])->whereRefId($this->gest_id)->first();
    }

    /**
     * Devuelve una colleccion de gafetes de estacionamiento que todavia no se han desactivado
     * @param Local $local
     * @return mixed
     */
    public static function getGafetesActivos(Local $local)
    {

        return GafeteEstacionamiento::whereGestLcalId($local->lcal_id)
            ->where('gest_numero_nfc', '>', 0)
            ->whereNull('gest_disabled_at')
            ->whereIn('gest_estado',['ENTREGADA','CANCELADA'])
            ->get();

    }

    public function toStringQr()
    {
//        $local = $this->Local->lcal_nombre_comercial;
//        return Request::root()."/gafete-estacionamiento/contraparte-gafete-estacionamiento/$this->gest_id";
        return "GE-$this->gest_id";
    }


    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Local()
    {
        return $this->belongsTo('App\Local', 'gest_lcal_id');
    }

    public function ComprobantePago()
    {
        return $this->hasOne('App\ComprobantePago', 'cpag_id', 'gest_cpag_id');
    }

    public function Puertas()
    {
        return $this->belongsToMany(Puerta::class, 'gafetes_estacionamiento_puertas', 'gafete_estacionamiento_id', 'puerta_id');
    }


    // public function Cargo(){
    //     return $this->belongsTo('App\CatCargo','gfpi_crgo_id');
    // }


}
