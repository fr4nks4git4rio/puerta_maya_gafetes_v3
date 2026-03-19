<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use App\Ciclo;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;

class Local extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'locales';
    protected $primaryKey = 'lcal_id';
    protected $prefix = 'lcal_';

    protected $guarded = ['lcal_id'];
    public $timestamps = true;

    const CREATED_AT = 'lcal_created_at';
    const UPDATED_AT = 'lcal_updated_at';
    const DELETED_AT = 'lcal_deleted_at';

    protected $dates = ['lcal_created_at', 'lcal_updated_at', 'lcal_deleted_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Locales';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['lcal_created_at', 'lcal_updated_at', 'lcal_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un nuevo <b>Local:</b> ' . $this->lcal_nombre_comercial . ' de tipo <b>' . $this->lcal_tipo . '</b> [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó un <b>Local:</b> ' . $this->lcal_nombre_comercial . ' de tipo <b>' . $this->lcal_tipo . '</b> [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó un <b>Local:</b> ' . $this->lcal_nombre_comercial . ' de tipo <b>' . $this->lcal_tipo . '</b> [' . $this->getKey() . ']';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }
    ////////////////////////////////////////////////////////////////

    public function gafetesGratuitosSolicitados()
    {
        $anio_impresion = $this->getAnioImpresion();

        //solicitados
        return $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->whereRaw("YEAR(sgft_fecha) = " . $anio_impresion)
            ->whereSgftPermisos('PEATONAL')
            ->whereSgftGratuito(1)
            ->count();
    }

    public function gafetesGratuitosAutoSolicitados()
    {
        $anio_impresion = $this->getAnioImpresion();
        //solicitados
        return $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", ['%AUTO%'])
            ->whereSgftGratuito(1)
            ->count();
    }

    public function gafetesGratuitosMotoSolicitados()
    {
        $anio_impresion = $this->getAnioImpresion();
        //solicitados
        return $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", ['%MOTO%'])
            ->whereSgftGratuito(1)
            ->count();
    }

    public function gafetesEstacionamientoAutoSolicitados()
    {
        $anio_impresion = $this->getAnioImpresion();

        //solicitados
        return $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", ['%AUTO%'])
            ->whereNull('sgft_disabled_at')
            ->count();
    }

    public function gafetesEstacionamientoMotoSolicitados()
    {
        $anio_impresion = $this->getAnioImpresion();
        //solicitados
        return $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", ['%MOTO%'])
            ->whereNull('sgft_disabled_at')
            ->count();
    }

    /**
     * Retorna el numero de gafetes de acceso gratuitos disponibles
     * para el local en el año corriente
     * @return mixed
     */
    public function gafetesGratuitosDisponibles()
    {
        return $this->lcal_gafetes_gratis - $this->gafetesGratuitosSolicitados();
    }


    /**
     * Retorna el numero de gafetes de de estacionamiento para AUTO
     * gratuitos disponibles para el local en el año corriente
     * @return mixed
     */
    public function gafetesGratuitosAutoDisponibles()
    {
        return $this->lcal_gafetes_gratis_auto - $this->gafetesGratuitosAutoSolicitados();
    }

    /**
     * Retorna el numero de gafetes de de estacionamiento para MOTO
     * gratuitos disponibles para el local en el año corriente
     * @return mixed
     */
    public function gafetesGratuitosMotoDisponibles()
    {
        return $this->lcal_gafetes_gratis_moto - $this->gafetesGratuitosMotoSolicitados();
    }

    /**
     * Retorna el numero de gafetes de de estacionamiento para AUTO
     * disponibles para el local en el año corriente segun el espacio maximo
     * @return mixed
     */
    public function gafetesEstacionamientoAutoDisponibles()
    {
        return $this->lcal_espacios_autos - $this->gafetesEstacionamientoAutoSolicitados();
    }

    /**
     * Retorna el numero de gafetes de de estacionamiento para MOTO
     * disponibles para el local en el año corriente segun el espacio maximo
     * @return mixed
     */
    public function gafetesEstacionamientoMotoDisponibles()
    {
        return $this->lcal_espacios_motos - $this->gafetesEstacionamientoMotoSolicitados();
    }

    /**
     * Retorna todos los gafetes de Auto
     * gratuitos disponibles para el local en el año corriente
     * @return mixed
     */
    public function gafetesAutoReasignadosAprobados($para_solicitar = false)
    {
        $anio_impresion = $this->getAnioImpresion();
        $query = DB::table('solicitudes_gafetes_reasignar')
            ->rightJoin('solicitudes_gafetes', 'sgft_id', '=', 'sgftre_sgft_id')
            ->where('sgftre_lcal_id', $this->lcal_id)
            ->where('sgftre_anio', $anio_impresion)
            ->whereRaw("sgftre_permisos like '%AUTO%'");
        if ($para_solicitar)
            $query->whereIn('sgftre_estado', ['PENDIENTE', 'ASIGNADO', 'AUTORIZADO']);
        else
            $query->whereIn('sgftre_estado', ['ASIGNADO', 'AUTORIZADO']);
        return $query;
    }

    /**
     * Retorna los gafetes de Moto
     * gratuitos disponibles para el local en el año corriente
     * @return mixed
     */
    public function gafetesMotoReasignadosAprobados($para_solicitar = false)
    {
        $anio_impresion = $this->getAnioImpresion();
        $query = DB::table('solicitudes_gafetes_reasignar')
            ->rightJoin('solicitudes_gafetes', 'sgft_id', '=', 'sgftre_sgft_id')
            ->where('sgftre_lcal_id', $this->lcal_id)
            ->where('sgftre_anio', $anio_impresion)
            ->whereRaw("sgftre_permisos like '%MOTO%'");
        if ($para_solicitar)
            $query->whereIn('sgftre_estado', ['PENDIENTE', 'ASIGNADO', 'AUTORIZADO']);
        else
            $query->whereIn('sgftre_estado', ['ASIGNADO', 'AUTORIZADO']);
        return $query;
    }

    /**
     * @param $tipo = [Auto, Moto]
     * @return mixed
     */
    public function numeroGafeteEstacionamiento($tipo)
    {
        $anio_impresion = $this->getAnioImpresion();

        if ($tipo === 'AUTO')
            $maximo = $this->lcal_espacios_autos;
        else
            $maximo = $this->lcal_espacios_motos;

        //solicitados
        $solicitados = $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->where('sgft_numero_estacionamiento', '>', 0)
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", [$tipo])
            ->orderBy('sgft_numero_estacionamiento', 'asc')
            ->where('sgft_disabled_at', null)
            ->get();

        $excluidos = [];
        $this->SolicitudesGafete()
            ->where('sgft_estado', '<>', 'CANCELADA')
            ->where('sgft_numero_estacionamiento', '>', 0)
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = ?", [$anio_impresion])
            ->whereRaw("sgft_permisos like ?", [$tipo])
            ->orderBy('sgft_numero_estacionamiento', 'asc')
            ->where('sgft_disabled_at', null)
            ->where('sgft_gafete_reposicion', '>', 0)
            ->get()->map->only('sgft_numero_estacionamiento')->map(function ($element) use (&$excluidos) {
                $excluidos[] = $element['sgft_numero_estacionamiento'];
            });

        if ($solicitados->count() > 0) {
            $numero = null;
            $i = 1;
            while ($i <= $maximo) {
                if (!in_array($i, $excluidos)) {
                    $gafete = $solicitados->where('sgft_numero_estacionamiento', '=', $i);
                    if ($gafete->count() === 0) {
                        $numero = $i;
                        break;
                    }
                }
                $i++;
            }

            return $numero;
        }

        return 1;
    }


    public function getSaldos()
    {

        //obtenemos los comprobantes no rechazados
        $anio = settings()->get('anio_impresion');

        $comprobantes = ComprobantePago::whereCpagLcalId($this->lcal_id)
            ->where('cpag_estado', '<>', 'RECHAZADO')
            ->get();

        $importeTransito = $comprobantes
            ->whereIn('cpag_estado', ['CAPTURADO', 'PREVALIDADO'])
            ->sum('cpag_importe_pagado');

        $importeValidado = $comprobantes
            ->whereIn('cpag_estado', ['VALIDADO'])
            ->sum('cpag_importe_pagado');

        $importeSolicitudesTransito = SolicitudGafete::whereSgftLcalId($this->lcal_id)
            ->whereIn('sgft_estado', ['PENDIENTE'])
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = '$anio'")
            ->sum('sgft_costo');

        $importeSolicitudesEfectivas = SolicitudGafete::whereSgftLcalId($this->lcal_id)
            ->whereIn('sgft_estado', ['VALIDADA', 'IMPRESA', 'ENTREGADA'])
            ->whereRaw("DATE_FORMAT(sgft_fecha, '%Y') = '$anio'")
            ->sum('sgft_costo');

        /// /////////////////////

        $importePermisos = PermisoTemporal::wherePtmpLcalId($this->lcal_id)
            ->where('ptmp_estado', 'CONCLUIDO')
            ->where('ptmp_estado_extemporaneo', 'PAGADO')
            ->whereRaw("DATE_FORMAT(ptmp_fecha, '%Y') = '$anio'")
            //  ->get();
            ->sum('ptmp_costo');

        $transferenciasRealizadas = TransferenciaSaldo::where('local_desde_id', $this->lcal_id)
            ->whereRaw("DATE_FORMAT(fecha, '%Y') = '$anio'")
            ->sum('saldo');

        $transferenciasRecibidas = TransferenciaSaldo::where('local_para_id', $this->lcal_id)
            ->whereRaw("DATE_FORMAT(fecha, '%Y') = '$anio'")
            ->sum('saldo');

        $saldo_virtual_old = $importeValidado + $importeTransito - $importeSolicitudesTransito - $importeSolicitudesEfectivas - $importePermisos + $transferenciasRecibidas;
        $saldo_vigente = $importeValidado - $importeSolicitudesEfectivas - $importePermisos - $transferenciasRealizadas + $transferenciasRecibidas;
        $saldo_virtual = $saldo_vigente + $importeTransito - $importeSolicitudesTransito;

        $return = [
            'importe_transito' => $importeTransito,
            'importe_validado' => $importeValidado,
            'solicitudes_transito' => $importeSolicitudesTransito,
            'solicitudes_cobradas' => $importeSolicitudesEfectivas,
            'permisos' => $importePermisos,
            'saldo_vigente' => $saldo_vigente,
            'saldo_virtual' => $saldo_virtual
        ];

        //        dd($return);
        return $return;
    }

    public function personasDentroDeLaPlaza()
    {
        return DB::select("SELECT empl.* from empleados_ubicacion as emplub
            left join empleados as empl on empl.empl_id = emplub.emplub_empl_id
            left join locales as lcal on lcal.lcal_id = empl.empl_lcal_id
            where emplub.emplub_ubicacion = 1
            and emplub.emplub_lcal_id = ?;", [$this->getKey()]);
    }

    public function autosDentroDeLaPlazaCantidad()
    {
        return DB::select("SELECT SUM(emplub_autos) as autos from empleados_ubicacion
            where emplub_lcal_id = ?;", [$this->getKey()])[0]->autos;
    }

    public function motosDentroDeLaPlazaCantidad()
    {
        return DB::select("SELECT SUM(emplub_motos) as motos from empleados_ubicacion
            where emplub_lcal_id = ?;", [$this->getKey()])[0]->motos;
    }

    /**
     * Retorna al año sobre el que se basarán las validaciones de gafetes gratuitos
     * depende de una configuración en settings para poder adelantar las impresiones
     * gratuitas al cierre de un año
     * @return int
     */
    private function getAnioImpresion()
    {
        $anio_impresion_config = settings()->get('anio_impresion', date('Y'));
        $anio_actual = date('Y');
        return $anio_actual < $anio_impresion_config ? $anio_impresion_config : $anio_actual;
    }


    ////////////////////////////////////////////////////////////////
    // R E L A T I O N S
    ////////////////////////////////////////////////////////////////
    public function RegimenFiscal()
    {
        return $this->belongsTo(CRegimenFiscal::class, 'lcal_regimen_fiscal_id');
    }

    public function Acceso()
    {
        return $this->belongsTo('App\CatAcceso', 'lcal_cacs_id');
    }

    public function SolicitudesGafete()
    {
        return $this->hasMany('App\SolicitudGafete', 'sgft_lcal_id');
    }

    public function GafetesEstacionamiento()
    {
        return $this->hasMany('App\GafeteEstacionamiento', 'gest_lcal_id');
    }
}
