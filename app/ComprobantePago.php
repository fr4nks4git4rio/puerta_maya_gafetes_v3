<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;

class ComprobantePago extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'comprobantes_pago';
    protected $primaryKey = 'cpag_id';
    protected $prefix = 'cpag_';

    protected $guarded = ['cpag_id'];
    public $timestamps = true;

    const CREATED_AT = 'cpag_created_at';
    const UPDATED_AT = 'cpag_updated_at';
    const DELETED_AT = 'cpag_deleted_at';

    protected $dates = ['cpag_created_at', 'cpag_updated_at', 'cpag_deleted_at'];

    protected $appends = ['saldo_disponible'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Comprobantes de Pago';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['cpag_created_at', 'cpag_updated_at', 'cpag_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Capturó un nuevo Comprobante de Pago[' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un Comprobante de Pago [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó una Comprobante de Pago [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getSaldoDisponibleAttribute()
    {
        $saldo = str_replace('$', '', $this->cpag_importe_pagado);
        $saldo = (float)$saldo;
        $this->SolcitudesGafete->map(static function (SolicitudGafete $solicitud) use (&$saldo) {
            $saldo -= $solicitud->sgft_costo;
        });

        return $saldo;
    }


    public static function getCapturados(Local $local)
    {
        $comprobantes =ComprobantePago::whereCpagLcalId($local->lcal_id)
            ->where('cpag_estado','CAPTURADO')
            ->get();

//        dd($comprobantes);

        return $comprobantes;
    }


    /**
     * Obtiene los comprobante marcados que requieren factura y que no se
     * estan ligados a una factura todavia
     * @param Local $local
     * @return mixed
     */
    public static function getFacturablesDirectos(Local $local)
    {

        return ComprobantePago::whereCpagRequiereFactura(1)
            ->whereNull('cpag_fact_id')
            ->whereEstado('VALIDADO')
            ->whereCpagLcalId($local->lcal_id)
            ->get();

    }

    /**
     * Obtiene los comprobante que no están marcados para factura directa y que no
     * estan ligados a una factura todavía
     *
     * @return mixed
     */
    public static function getFacturablesGlobal()
    {

        return ComprobantePago::whereCpagRequiereFactura(0)
            ->whereNull('cpag_fact_id')
            ->whereCpagEstado('VALIDADO')
            ->get();

    }


    /**
     * Determina si existen comprobantes para adjuntar a una solicitud teniendo en cuenta que el comprobante no este
     * rechazado ni este adjunto en una solicitud vigente.
     * @param Local $local
     * @return bool
     */
    public static function checkComprobantesPendientesPrevalidar(Local $local)
    {

        $sql = "SELECT cp.cpag_id, cp.cpag_lcal_id, cp.cpag_fecha_pago, cp.cpag_importe_pagado, cp.cpag_estado, COUNT(sg.sgft_id) AS sg_count, COUNT(es.gest_id) AS ge_count
                FROM comprobantes_pago AS cp
                LEFT JOIN solicitudes_gafetes AS sg ON sg.sgft_cpag_id = cpag_id AND sg.sgft_estado <> 'CANCELADO' AND sg.sgft_deleted_at IS NULL
                LEFT JOIN gafetes_estacionamiento AS es ON es.gest_cpag_id = cpag_id AND es.gest_estado <> 'CANCELADO' AND es.gest_deleted_at IS NULL
                WHERE cp.cpag_estado = 'CAPTURADO'
                AND cpag_lcal_id = " . $local->lcal_id . "
                GROUP BY cp.cpag_id, cp.cpag_lcal_id, cp.cpag_fecha_pago, cp.cpag_importe_pagado, cp.cpag_estado
                HAVING sg_count = 0 AND ge_count = 0";

        $records = \DB::select($sql);

        if (count($records) > 0)
            return true;

//        $record = ComprobantePago::whereCpagLcalId($local->lcal_id)
//                ->whereCpagEstado('CAPTURADO')
//                ->first();
//
//        if($record != null)
//            return true;

        return false;

    }

    public static function getDisponiblesParaPermisoTemporal(Local $local)
    {

        $sql = "SELECT 
                    cpag_id,
                    CONCAT('FOLIO: ',cpag_folio_bancario, ' AUT: ', cpag_aut_bancario) AS descripcion,
                    c.cpag_cantidad_pv AS cantidad_pv,
                    c.cpag_cantidad_rp AS cantidad_rp,
                    -- SUM(IF(s.sgft_tipo='PRIMERA VEZ',1,0)) as usos_pv,
                    SUM(IF(s.sgft_tipo='REPOSICIÓN',1,0)) + SUM(IF(ptmp_estado_extemporaneo = 'PAGADO',1,0))  as usos_rp,
                    -- c.cpag_cantidad_pv - SUM(IF(s.sgft_tipo='PRIMERA VEZ',1,0)) as disponibles_pv,
                    c.cpag_cantidad_rp - SUM(IF(s.sgft_tipo='REPOSICIÓN',1,0)) as disponibles_rp
                    
                FROM comprobantes_pago AS c
                LEFT JOIN solicitudes_gafetes AS s ON s.sgft_cpag_id = c.cpag_id AND s.sgft_estado NOT IN ('CANCELADA')
                LEFT JOIN permisos_temporales as pt ON pt.ptmp_cpag_id = c.cpag_id AND ptmp_estado_extemporaneo = 'PAGADO'
                WHERE 
                    cpag_lcal_id = " . $local->lcal_id . "
                    AND cpag_deleted_at is null
                    AND sgft_deleted_at is null
                GROUP BY c.cpag_id
                HAVING (  usos_rp < cantidad_rp )";

        return \DB::select($sql);
    }

    public static function getComprobantesDisponiblesParaFactura(Local $local = null)
    {

        $where_local = $local != null ? " AND  cpag_lcal_id = " . $local->lcal_id : "";

        $sql = "SELECT 
                     cpag_id,
                     CONCAT('FOLIO: ',cpag_folio_bancario, ' AUT: ', cpag_aut_bancario) AS descripcion,
                     c.cpag_folio_bancario,
                     c.cpag_importe_pagado,
                     c.cpag_cantidad_pv AS cantidad_pv,
                     c.cpag_cantidad_rp AS cantidad_rp,
                     SUM( if(fd.fact_estado <> 'CANCELADA', ifnull(fd.fcdt_importe,0),0) ) AS importe_facturado,
                     group_concat(distinct fd.fact_estado) as fact_estado
                     
                 FROM comprobantes_pago AS c
                    LEFT JOIN v_factura_detalle AS fd ON fd.fcdt_cpag_id = c.cpag_id
                
                WHERE
                    cpag_deleted_at IS NULL
                    " . $where_local . "
                    
                 GROUP BY c.cpag_id
                 
                 HAVING importe_facturado < cpag_importe_pagado";

        return \DB::select($sql);
    }

    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////

    public function Local()
    {
        return $this->belongsTo('App\Local', 'cpag_lcal_id');
    }

    public function UsoCfdi()
    {
        return $this->belongsTo('App\CUsoCfdi', 'cpag_uso_cfdi');
    }

    public function FormaPago()
    {
        return $this->belongsTo('App\CFormaPago', 'cpag_forma_pago');
    }

    public function Factura()
    {
        return $this->hasOne('App\Factura', 'fact_id', 'cpag_fact_id');
    }

    public function SolcitudesGafete()
    {
        return $this->hasMany(SolicitudGafete::class, 'sgft_cpag_id', 'cpag_id');
    }

    public function GafetesEstacionamiento()
    {
        return $this->hasMany(GafeteEstacionamiento::class, 'gest_cpag_id', 'cpag_id');
    }



    ////////////////////////////////////////////////////////////////
    // DEPRECATED
    ////////////////////////////////////////////////////////////////

    /**
     * Obtiene comprobantes dispinibles dado la cantidad de usos de  primera vez
     * y reposición, se cambió la lógica de comprobantes el 04 de Noviembre 2019
     * y ahora se hace directamente por control de importe y costos de gafetes.
     * @param Local $local
     * @return mixed
     */
    public static function _getComprobantesDisponiblesParaGafete(Local $local)
    {

        $sql = "SELECT 
                    cpag_id,
                    CONCAT('FOLIO: ',cpag_folio_bancario, ' AUT: ', cpag_aut_bancario) AS descripcion,
                    c.cpag_cantidad_pv AS cantidad_pv,
                    c.cpag_cantidad_rp AS cantidad_rp,
                    SUM(IF(s.sgft_tipo='PRIMERA VEZ',1,0)) as usos_pv,
                    SUM(IF(s.sgft_tipo='REPOSICIÓN',1,0)) + SUM(IF(ptmp_estado_extemporaneo = 'PAGADO',1,0))  as usos_rp,
                    c.cpag_cantidad_pv - SUM(IF(s.sgft_tipo='PRIMERA VEZ',1,0)) as disponibles_pv,
                    c.cpag_cantidad_rp - SUM(IF(s.sgft_tipo='REPOSICIÓN',1,0)) as disponibles_rp
                    
                FROM comprobantes_pago AS c
                LEFT JOIN solicitudes_gafetes AS s ON s.sgft_cpag_id = c.cpag_id AND s.sgft_estado NOT IN ('CANCELADA')
                LEFT JOIN permisos_temporales as pt ON pt.ptmp_cpag_id = c.cpag_id AND ptmp_estado_extemporaneo = 'PAGADO'
                WHERE 
                    cpag_lcal_id = " . $local->lcal_id . "
                    AND cpag_deleted_at is null
                    AND sgft_deleted_at is null
                GROUP BY c.cpag_id
                HAVING ( (usos_pv < cantidad_pv) or usos_rp < cantidad_rp )";

        return $records = \DB::select($sql);
    }


    /**
     * Obtiene cantidad de usos PRIMERA VEZ asociados a un comprobante
     * Ya no se debe usar desde enrega 14 (nov 2019) pues ahora se descuenta
     * de un saldo, y no todas las solicitudes van ligadas a un comprobante
     * @return mixed
     */
    public function _getCantidadUsosPV()
    {

        $records = SolicitudGafete::whereSgftCpagId($this->cpag_id)
            ->whereSgftTipo('PRIMERA VEZ')
            ->whereRaw(" sgft_estado NOT IN ('CANCELADA') ")
            ->get()->count();

        return $records;
    }

    /**
     * Obtiene cantidad de usos REPOSICION vez asociados a un comprobante
     * Ya no se debe usar desde enrega 14 (nov 2019) pues ahora se descuenta
     * de un saldo, y no todas las solicitudes van ligadas a un comprobante
     * @return mixed
     */
    public function _getCantidadUsosRP()
    {

        $solicitudesCount = SolicitudGafete::whereSgftCpagId($this->cpag_id)
            ->whereSgftTipo('REPOSICIÓN')
            ->whereRaw(" sgft_estado NOT IN ('CANCELADA') ")
            ->get()->count();

        $permisosCount = PermisoTemporal::wherePtmpCpagId($this->cpag_id)
            ->wherePtmpEstadoExtemporaneo('PAGADO')
            ->get()->count();

        return $solicitudesCount + $permisosCount;
    }

    /**
     * Obtiene los comprobantes con saldo disponible de un local
     * Ya no se debe usar (nov 2019) pues ahora el saldo es por local
     * @param Local $local
     * @return mixed
     */
    public static function _getDisponibles(Local $local)
    {

//        return $comprobantes = VComprobantePago::where('cpag_saldo','>',0)
//            ->whereCpagLcalId($local->lcal_id)
//            ->get();

    }
}
