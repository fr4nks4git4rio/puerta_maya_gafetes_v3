<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class PermisoMantenimiento extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'permisos_mantenimiento';
    protected $primaryKey = 'pmtt_id';
    protected $prefix = 'pmtt_';

    protected $guarded = ['pmtt_id'];
    public $timestamps = true;

    const CREATED_AT = 'pmtt_created_at';
    const UPDATED_AT = 'pmtt_updated_at';
    const DELETED_AT = 'pmtt_deleted_at';

    protected $dates = ['pmtt_created_at', 'pmtt_updated_at', 'pmtt_deleted_at'];

    // protected $appends = ['pmtt_estado'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Permisos de Mantenimiento';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['pmtt_created_at', 'pmtt_updated_at', 'pmtt_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó una nuevo Permiso de Mantenimiento [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Permiso de Mantenimiento [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un Permiso de Mantenimiento [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }


    public function getPmttEstadoAttribute($value)
    {

        if ($value == 'APROBADO') {
            $hoy = \Carbon\Carbon::today();
            $final = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->pmtt_vigencia_final);

            if ($hoy->gt($final)) {
                return 'VENCIDO';
            }
        }


        return $value;
    }

    public function toStringQr()
    {
        //        $local = $this->Local->lcal_nombre_comercial;
        //        $fecha = Carbon::createFromFormat("Y-m-d H:i:s", $this->pmtt_fecha)->format('d/m/Y');
        //        $vigente_desde = Carbon::createFromFormat("Y-m-d", $this->pmtt_vigencia_inicial)->format('d/m/Y');
        //        $vigente_hasta = Carbon::createFromFormat("Y-m-d", $this->pmtt_vigencia_final)->format('d/m/Y');
        //        return Request::root()."/permiso-mantenimiento/formato-pdf-firmante/$this->pmtt_id";
        return "PM-$this->pmtt_id";
    }


    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Local()
    {
        return $this->belongsTo('App\Local', 'pmtt_lcal_id');
    }

    public function MantenimientoAprobadoPor()
    {
        return $this->belongsTo('App\User', 'pmtt_mtt_approved_by');
    }
    public function HessAprobadoPor()
    {
        return $this->belongsTo('App\User', 'pmtt_hess_approved_by');
    }

    public function TipoMantenimiento()
    {
        return $this->belongsTo('App\TipoMantenimiento', 'pmtt_tmtt_id');
    }
    public function TrabajoEspecifico()
    {
        return $this->belongsTo('App\TrabajoEspecifico', 'pmtt_tesp_id');
    }

    public function Trabajadores()
    {
        return $this->hasMany('App\PermisoMantenimientoTrabajador', 'pmtb_pmtt_id');
    }
    public function ActividadesAltoRiesgo()
    {
        return $this->belongsToMany('App\ActividadAltoRiesgo', 'permisos_mant_acts_alto_riesgo', 'pmtt_id', 'actar_id');
    }
    public function RiesgosAsociados()
    {
        return $this->belongsToMany('App\RiesgoAsociado', 'permisos_mant_riesgos_asociados', 'pmtt_id', 'rasoc_id');
    }
    public function MedidasControlRiesgo()
    {
        return $this->belongsToMany('App\MedidaControlRiesgo', 'permisos_mant_meds_cont_riesgo', 'pmtt_id', 'medcr_id');
    }
    public function EquiposHerramientas()
    {
        return $this->belongsToMany('App\EquipoHerramienta', 'permisos_mant_eqs_hers', 'pmtt_id', 'eqher_id');
    }
}
