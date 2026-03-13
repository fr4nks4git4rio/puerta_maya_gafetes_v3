<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Factura extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'facturas';
    protected $primaryKey = 'fact_id';
    protected $prefix = 'fact_';

    protected $guarded = ['fact_id'];
    protected $appends = ['label_combo'];
    public $timestamps = true;

    const CREATED_AT = 'fact_created_at';
    const UPDATED_AT = 'fact_updated_at';
    const DELETED_AT = 'fact_deleted_at';

    protected $dates = ['fact_created_at', 'fact_updated_at', 'fact_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Facturas';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['fact_created_at', 'fact_updated_at', 'fact_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una Factura [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó una Facura [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Factura [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////

    public static function obtenerSiguienteFolio()
    {
        $record = Factura::whereIn('fact_estado', ['TIMBRADA', 'CANCELADA'])->orderBy('fact_folio', 'desc')->first();

        if ($record != null) {
            return $record->fact_folio + 1;
        }

        return 1;

    }

    public function getLabelComboAttribute()
    {
        return "Folio: ". ($this->fact_folio ? $this->fact_folio : "S/F") . " | Receptor: ".($this->Local ? $this->Local->lcal_razon_social : 'S/R') ." | Monto: ". $this->fact_total;
    }


    public function Local()
    {
        return $this->belongsTo('App\Local', 'fact_lcal_id');
    }

    public function Conceptos()
    {
        return $this->hasMany('App\FacturaDetalle', 'fcdt_fact_id');
    }

    public function Comprobante()
    {
        return $this->hasOne('App\ComprobantePago', 'cpag_id', 'fact_cpag_id');
    }

    public function Serie()
    {
        return $this->belongsTo('App\CSerie', 'fact_serie_id');
    }

    public function Moneda()
    {
        return $this->belongsTo('App\CMoneda', 'fact_moneda_id');
    }

    public function RegimenFiscal()
    {
        return $this->belongsTo('App\CRegimenFiscal', 'fact_regimenfiscal_id');
    }

    public function UsoCfdi()
    {
        return $this->belongsTo('App\CUsoCfdi', 'fact_usocfdi_id');
    }

    public function MetodoPago()
    {
        return $this->belongsTo('App\CMetodoPago', 'fact_metodopago_id');
    }

    public function FormaPago()
    {
        return $this->belongsTo('App\CFormaPago', 'fact_formapago_id');
    }

    public function Periodicidad()
    {
        return $this->belongsTo('App\Periodicidad', 'fact_periodicidad_id', 'pdad_id');
    }

    public function Mes()
    {
        return $this->belongsTo('App\Mes', 'fact_mes_id', 'mes_id');
    }

    public function MotivoCancelacion()
    {
        return $this->belongsTo(MotivoCancelacionFactura::class, 'fact_motivo_cancelacion_id', 'mcf_id');
    }

    public function Comprobantes()
    {
        return $this->hasMany(ComprobantePago::class, 'cpag_fact_id', 'fact_id');
    }


}
