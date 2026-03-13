<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class FacturaDetalle extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'factura_detalle';
    protected $primaryKey = 'fcdt_id';
    protected $prefix = 'fcdt_';

    protected $guarded = ['fcdt_id'];
    public $timestamps = true;

    const CREATED_AT = 'fcdt_created_at';
    const UPDATED_AT = 'fcdt_updated_at';
    const DELETED_AT = 'fcdt_deleted_at';

    protected $dates = ['fcdt_created_at','fcdt_updated_at','fcdt_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Factura Concepto';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['fcdt_created_at','fcdt_updated_at','fcdt_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nueva Concepto de Factura ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Concepto Factura ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó un concepto de factura ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////


    public function Factura(){
      return  $this->belongsTo('App\Factura','fcdt_fact_id');
    }

    public function Comprobante(){
        return  $this->belongsTo('App\ComprobantePago','fcdt_cpag_id');
    }

    public function Unidad(){
        return  $this->belongsTo('App\CUnidad','fcdt_claveunidad_id');
    }

    public function Producto(){
        return  $this->belongsTo('App\CProducto','fcdt_claveproducto_id');
    }

    public function ObjetoImpuesto(){
        return  $this->belongsTo('App\ObjetoImpuesto','fcdt_objeto_impuesto_id', 'oimp_id');
    }

}
