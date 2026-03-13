<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class SolicitudGafete extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'solicitudes_gafetes';
    protected $primaryKey = 'sgft_id';
    protected $prefix = 'sgft_';

    protected $guarded = ['sgft_id'];
    public $timestamps = true;

    const CREATED_AT = 'sgft_created_at';
    const UPDATED_AT = 'sgft_updated_at';
    const DELETED_AT = 'sgft_deleted_at';

    protected $dates = ['sgft_created_at','sgft_updated_at','sgft_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Solicitudes Gafete';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['sgft_created_at','sgft_updated_at','sgft_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
             case 'created':
                $message = 'Creó una nueva Solicitud de Gafete de Planta ['.$this->getKey().']';
                break;
            case 'updated':
                $message = 'Actualizó una Solicitud de Gafete de Planta ['.$this->getKey().']';
                break;
            case 'deleted':
                $message = 'Eliminó una Solicitud de Gafete de Planta ['.$this->getKey().']';
                break;
            default:
                $message = "This model has been {$eventName} [".$this->getKey()."]";
                break;
        }

        return $message;
    }




    public function getSgftThumbWebAttribute($thumb)
    {
        $thumb = $this->sgft_thumb;
        $path =  storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $thumb;

        if($thumb!= "" && file_exists($file)){
           return  asset('storage/empleados/'.$thumb);
        }
        else {
            return  asset('storage/empleados/default_sm.jpg');
        }

    }

    public function getSgftFotoWebAttribute($foto)
    {
        $foto = $this->sgft_foto;
        $path =  storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'empleados';
        $file = $path . DIRECTORY_SEPARATOR . $foto;

        if(file_exists($file)){
           return  asset('storage/empleados/'.$foto);
        }

        else {
            return 'No existe ' . $foto;
        }
    }

    public function getSgftNumeroUsoComprobanteAttribute(){

        $comprobante = $this->ComprobantePago;
        if($comprobante != null){
             $solicitudes = SolicitudGafete::whereSgftCpagId($comprobante->cpag_id)
                ->whereSgftTipo($this->sgft_tipo)
                ->where("sgft_created_at", "<=",$this->sgft_created_at)
                ->whereRaw(" sgft_estado NOT IN ('CANCELADA') ")
                ->get()->count();

            $permisosTemporales = PermisoTemporal::wherePtmpCpagId($comprobante->cpag_id)
                                    ->wherePtmpEstadoExtemporaneo('PAGADO')
                                    ->where("ptmp_fecha_resolucion_extemporanea", "<=",$this->sgft_created_at)
                                    ->get()->count();

            return $solicitudes + $permisosTemporales;

        }




        return 0;
    }

    public function getSgftCostoTarifaAttribute(){

        if($this->sgft_gratuito == 1)
            return 0;

        else
            return  ($this->sgft_tipo == 'PRIMERA VEZ')? settings()->get('gft_acceso_pvez', '0' ) : settings()->get('gft_acceso_reposicion', '0' );

    }




    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Empleado(){
        return $this->belongsTo('App\Empleado','sgft_empl_id');
    }

    public function Local(){
        return $this->belongsTo('App\Local','sgft_lcal_id');
    }

    public function ComprobantePago(){
        return $this->hasOne('App\ComprobantePago','cpag_id', 'sgft_cpag_id');
    }






}
