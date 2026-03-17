<?php

namespace App\Http\Controllers;

use App\ComprobantePago;
use App\Exports\ComprobantesPagoExport;
use App\Exports\SaldoLocalesExport;
use App\Local;
use App\LogAcceso;
use App\PermisoMantenimiento;
use App\PermisoTemporal;
use App\Reports\AccesosBaseReport;
use App\Reports\ComprobantesPagoReport;
use App\Reports\GafetesImpresosAccesoReport;
use App\Reports\GafetesImpresosEstacionamientoReport;
use App\Reports\SaldoLocalesReport;
use App\SolicitudGafete;
use App\GafeteEstacionamiento;
use App\Reports\GafetesDesactivadosReport;
use App\Reports\PermisosTemporalesReport;
use App\Reports\SolicitudesMantenimientoVigentesReport;
use App\VGafetesRfid;
use App\VGafetesRfidV3;
use App\VLogAcceso;
use App\VLogAccesoV3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }

    public function indexRecepcion()
    {

        $reportes =
            [
                [
                    'key' => 'P_TEMP',
                    'url' => url('reportes/permisos-temporales'),
                    'nombre' => 'Permisos temporales',
                    'fechas' => true,
                    'estados_ptmp' => true,
                    'locales' => false,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],


                [
                    'key' => 'GFT_IMPR',
                    'url' => url('reportes/gafetes-impresos-acceso'),
                    'nombre' => 'Gafetes impresos por acceso',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'estados_gipa' => true,
                    'locales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                [
                    'key' => 'GFT_EST',
                    'url' => url('reportes/gafetes-impresos-estacionamiento'),
                    'nombre' => 'Gafetes impresos de estacionamiento',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'locales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                [
                    'key' => 'SOL_MANT_VIG',
                    'url' => url('reportes/solicitudes-mantenimiento-vigentes'),
                    'nombre' => 'Solicitudes de mantenimiento vigentes',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'estados_pmant' => true,
                    'locales' => false,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                [
                    'key' => 'GAFATES_DESACTIVADOS',
                    'url' => url('reportes/gafetes-desactivados'),
                    'nombre' => 'Gafetes desactivados',
                    'fechas' => true,
                    //'estados_ptmp' => false,
                    //'estados_pmant' => true,
                    'tipos_gafete' => true,
                    'locales' => false,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                [
                    'key' => 'COMPROBANTES_PAGO',
                    'url' => url('reportes/comprobantes-pago'),
                    'nombre' => 'Comprobantes de Pago',
                    'fechas' => true,
                    'estados_cpago' => true,
                    'locales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                [
                    'key' => 'SALDO_LOCALES',
                    'url' => url('reportes/saldo-locales'),
                    'nombre' => 'Saldo por Locales',
                    'fechas' => false,
                    'estados_ptmp' => false,
                    'locales' => true,
                    'razonsociales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

            ];

        return $this->renderIndex($reportes);
    }

    public function indexMantenimiento()
    {

        $reportes =
            [
                [
                    'key' => 'SOL_MANT_VIG',
                    'url' => url('reportes/solicitudes-mantenimiento-vigentes'),
                    'nombre' => 'Solicitudes de mantenimiento vigentes',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'estados_pmant' => true,
                    'locales' => false,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ]

            ];

        return $this->renderIndex($reportes);
    }

    public function indexSeguridad()
    {

        $reportes =
            [
                [
                    'key' => 'P_TEMP',
                    'url' => url('reportes/permisos-temporales'),
                    'nombre' => 'Permisos temporales',
                    'fechas' => true,
                    'estados_ptmp' => true,
                    'locales' => false,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],


                [
                    'key' => 'ACCESS_VEHIC',
                    'url' => url('reportes/accesos-vehicular'),
                    'nombre' => 'Acceso al estacionamiento de vehículos',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'locales' => false,
                    'numero_rfid' => true,
                    'hora' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                // [
                //     'key' => 'ACCESS_VEHIC_6PM',
                //     'url' => url('reportes/accesos-vehicular-6pm'),
                //     'nombre' => 'Acceso vehicular a las 6pm',
                //     'fechas' => false,
                //     'dia' => true,
                //     'estados_ptmp' => false,
                //     'numero_rfid' => false,
                //     'locales' => false,
                //     'do_html' => true,
                //     'do_pdf' => true,
                //     'do_xlsx' => false,
                // ],

                [
                    'key' => 'ACCESS_GAFETE',
                    'url' => url('reportes/accesos-gafete'),
                    'nombre' => 'Accesos por gafete',
                    'fechas' => false,
                    'dia' => true,
                    'estados_ptmp' => false,
                    'numero_rfid' => true,
                    'locales' => false,
                    'tipos_gafete' => true,
                    'hora' => true,

                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

                // [
                //     'key' => 'ACCESS_GAFETE_6PM',
                //     'url' => url('reportes/accesos-gafete-6pm'),
                //     'nombre' => 'Accesos por gafete a las 6pm',
                //     'fechas' => false,
                //     'dia' => true,
                //     'estados_ptmp' => false,
                //     'numero_rfid' => false,
                //     'locales' => false,

                //     'do_html' => true,
                //     'do_pdf' => true,
                //     'do_xlsx' => false,
                // ],
                [
                    'key' => 'AJENOS_CASA',
                    'url' => url('reportes/ajenos-en-casa'),
                    'nombre' => 'Ajenos en Casa',
                    'fechas' => false,
                    'dia' => false,
                    'estados_ptmp' => false,
                    'numero_rfid' => false,
                    'locales' => true,
                    'tipos_gafete' => false,
                    'hora' => false,

                    'do_html' => false,
                    'do_pdf' => false,
                    'do_xlsx' => false,
                ],
            ];

        return $this->renderIndex($reportes);
    }

    public function indexLocatario()
    {

        $reportes =
            [
                [
                    'key' => 'GFT_IMPR',
                    'url' => url('reportes/gafetes-impresos-acceso'),
                    'nombre' => 'Gafetes impresos por acceso',
                    'fechas' => true,
                    'estados_ptmp' => false,
                    'estados_gipa' => true,
                    'locales' => false,
                    'use_usr_lcal_id' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => false,
                ],

            ];

        return $this->renderIndex($reportes);
    }

    public function indexContabilidad()
    {

        $reportes =
            [
                [
                    'key' => 'COMPROBANTES_PAGO',
                    'url' => url('reportes/comprobantes-pago'),
                    'nombre' => 'Comprobantes de Pago',
                    'fechas' => true,
                    'estados_cpago' => true,
                    'locales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => true,
                ],

                [
                    'key' => 'SALDO_LOCALES',
                    'url' => url('reportes/saldo-locales'),
                    'nombre' => 'Saldo por Locales',
                    'fechas' => false,
                    'estados_ptmp' => false,
                    'locales' => true,
                    'do_html' => true,
                    'do_pdf' => true,
                    'do_xlsx' => true,
                ],

            ];

        return $this->renderIndex($reportes);
    }


    private function renderIndex($reportes)
    {

        $estados_ptmp = [
            '' => 'TODOS',
            'PENDIENTE' => "PENDIENTE",
            'APROBADO' => "APROBADO",
            'RECHAZADO' => "RECHAZADO",
            'ASIGNADO' => "ASIGNADO",
            'ENTREGADO' => "ENTREGADO",
            'CONCLUIDO' => "CONCLUIDO"
        ];

        $estados_gipa = [
            '' => 'TODOS',
            'CANCELADA' => "CANCELADA",
            'IMPRESA' => "IMPRESA",
            'ENTREGADA' => "ENTREGADA"
        ];

        $estados_pmant = [
            '' => 'TODOS',
            'VENCIDO' => "VENCIDO",
            'APROBADO' => "APROBADO",
            'RECHAZADO' => "RECHAZADO",
        ];

        $estados_cpago = [
            '' => 'TODOS',
            'CAPTURADO' => 'CAPTURADO',
            'PREVALIDADO' => 'PREVALIDADO',
            'VALIDADO' => 'VALIDADO',
            'RECHAZADO' => 'RECHAZADO'
        ];

        $horas = [
            '' => 'Toodas',
            '12 AM' => '12 AM',
            '1 AM' => '1 AM',
            '2 AM' => '2 AM',
            '3 AM' => '3 AM',
            '4 AM' => '4 AM',
            '5 AM' => '5 AM',
            '6 AM' => '6 AM',
            '7 AM' => '7 AM',
            '8 AM' => '8 AM',
            '9 AM' => '9 AM',
            '10 AM' => '10 AM',
            '11 AM' => '11 AM',
            '12 PM' => '12 PM',
            '1 PM' => '1 PM',
            '2 PM' => '2 PM',
            '3 PM' => '3 PM',
            '4 PM' => '4 PM',
            '5 PM' => '5 PM',
            '6 PM' => '6 PM',
            '7 PM' => '7 PM',
            '8 PM' => '8 PM',
            '9 PM' => '9 PM',
            '10 PM' => '10 PM',
            '11 PM' => '11 PM'
        ];

        $locales = Local::selectRaw('lcal_id , CONCAT(lcal_nombre_comercial, " / ", lcal_razon_social) as nombre')
            ->get()
            ->pluck('nombre', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $razonsociales = Local::selectRaw('lcal_razon_social as razon_social')
            ->distinct('razon_social')
            ->get()
            ->pluck('razon_social', 'razon_social')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        $tipos_gafete = [
            '' => 'TODOS',
            'PEATONAL' => 'PEATONAL',
            'ESTACIONAMIENTO' => 'ESTACIONAMIENTO',
            // 'planta' => 'PLANTA',
            // 'permiso' => 'PERMISO TEMPORAL',
            // 'estacionamiento' => 'ESTACIONAMIENTO',
        ];

        return view(
            'web.reportes.index',
            compact(
                'reportes',
                'estados_ptmp',
                'locales',
                'estados_pmant',
                'tipos_gafete',
                'estados_gipa',
                'estados_cpago',
                'razonsociales',
                'horas'
            )
        );
    }


    //---------------------------------------------------------------------------------------------

    public function solicitudesMantenimientoVigentes(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $estado = $request->get('estado_pmant');

        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }


        $records = PermisoMantenimiento::whereNotIn('pmtt_estado', ['PENDIENTE'])
            ->whereRaw("
                        ( pmtt_vigencia_inicial BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'
                                    OR
                        pmtt_vigencia_final BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}' )
                        ");

        $records->with('Local');

        $records = $records->orderBy('pmtt_vigencia_inicial', 'desc')->get();

        if ($estado != '') {
            $records = $records->where('pmtt_estado', $estado);
        }


        if ($request->get('pdf') == 1) {

            $report = new SolicitudesMantenimientoVigentesReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);


            return $report->exec();
        }

        $reporte = view(
            'web.reportes.solicitudes-mantenimiento-vigentes',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function permisosTemporales(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $estado = $request->get('estado_ptmp');

        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }


        $records = PermisoTemporal::whereRaw("
                        ( ptmp_vigencia_inicial BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'
                                    OR
                        ptmp_vigencia_final BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}' )
                        ")->with('Local', 'Cargo');

        $records = $records->orderBy('ptmp_vigencia_inicial', 'desc')->get();

        if ($estado != '') {
            $records = $records->where('ptmp_estado', $estado);
        }


        if ($request->get('pdf') == 1) {

            $report = new PermisosTemporalesReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);

            return $report->exec();
        }

        $reporte = view(
            'web.reportes.permisos-temporales_html',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function gafetesImpresosAcceso(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $local = $request->get('local');


        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }


        $records = SolicitudGafete::whereRaw(" DATE(sgft_fecha) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");

        $estado = $this->data['estado_gipa'];
        if ($estado != "") {
            $records->whereSgftEstado($estado);
            //            $records->whereIn('sgft_estado', ['IMPRESA', 'ENTREGADA']);
        }


        if ($local != '') {
            $records->whereSgftLcalId($local);
        }

        $records->with('Local');

        $records = $records->orderBy('sgft_fecha', 'desc')->get();


        if ($request->get('pdf') == 1) {

            $report = new GafetesImpresosAccesoReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);

            return $report->exec();
        }

        $reporte = view(
            'web.reportes.gafetes-impresos-acceso',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function gafetesImpresosEstacionamiento(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $local = $request->get('local');


        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }

        // $records = GafeteEstacionamiento::whereRaw(" DATE(gest_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");
        $records = SolicitudGafete::whereRaw("sgft_permisos like '%AUTO%' or sgft_permisos like '%MOTO%'")->whereRaw(" DATE(sgft_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");

        if ($local != '') {
            $records->whereSgftLcalId($local);
        }

        $records->with('Local');

        $records = $records->orderBy('sgft_created_at', 'desc')->get();


        if ($request->get('pdf') == 1) {

            $report = new GafetesImpresosEstacionamientoReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);

            return $report->exec();
        }

        $reporte = view(
            'web.reportes.gafetes-impresos-estacionamiento',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function accesosVehicular(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $numero = $request->get('numero_rfid');
        $hora = $request->get('hora');

        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }

        // $records = VLogAcceso::whereRaw('lgac_puerta like "%autos%"')
        //     ->whereRaw('tipo = "estacionamiento"')
        //     ->whereRaw(" DATE(lgac_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'")
        //     ->orderBy('lgac_created_at');
        $records = VLogAccesoV3::whereRaw("lgac_puerta_tipo IN ('AUTO','MOTO')")
            ->whereRaw(" DATE(lgac_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'")
            ->orderBy('lgac_created_at');

        if ($numero != '') {
            $records->whereLgacCardNumber($numero);
        }

        if ($hora) {
            $arr = explode(' ', $hora);
            if ($arr[1] == 'AM')
                $hora_inicio = $arr[0] == 12 ? '00' : ($arr[0] < 10 ? ("0" . $arr[0]) : $arr[0]); //($arr[0] == 12 ? '00' : ($arr[0] < 10 ? ("0" . $arr[0]) : $arr[0])) .":00";
            else
                $hora_inicio = $arr[0] != 12 ? ($arr[0] + 12) : ($arr[0]); //($arr[0] != 12 ? ($arr[0]+12) : ($arr[0])) .":00";
            // $hora_fin = ((int)$hora_inicio)+1;//str_replace(':00', ':59', $hora_inicio);
            // $hora_fin = $hora_fin < 10 ? ("0".$hora_fin):$hora_fin;
            $records->whereRaw("DATE_FORMAT(lgac_created_at, '%H') = ?", ["$hora_inicio"]);
        }

        $records = $records->orderBy('lgac_created_at', 'desc')->get();
        $view = 'web.reportes.accesos-estacionamiento';

        if ($request->get('pdf') == 1) {

            $report = new AccesosBaseReport(null, true, false);

            $report->setView($view)
                ->setRecords($records)
                ->setInicio($inicio)
                ->setFin($fin);

            return $report->exec();
        }


        $reporte = view($view, compact('records', 'inicio', 'fin'))
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function accesosVehicular6pm(Request $request)
    {

        $dia = $request->get('dia');
        if ($dia == "") {
            $dia = date('Y-m-d');
        }

        $dia = $dia . ' 18:00:00';

        $sql = " SELECT

                    l.lgac_card_number,
                    MAX(l.lgac_created_at) as ultima_entrada,
                    COUNT(l.lgac_id) AS n,
                    lcal_nombre_comercial AS local

                FROM v_log_accesos_v3 AS l
                INNER JOIN locales as loc ON l.lcal_id = loc.lcal_id
                WHERE
                    lgac_created_at <= '$dia'
                    and lgac_puerta_tipo IN ('AUTO','MOTO')
                ORDER BY lgac_created_at desc
                GROUP BY l.lgac_card_number, l.lcal_id
                HAVING n%2 <> 0

        ";

        $records = \DB::select($sql);
        $view = 'web.reportes.accesos-estacionamiento-6pm';

        if ($request->get('pdf') == 1) {

            $report = new AccesosBaseReport(null, true, false);

            $report->setView($view)
                ->setRecords($records)
                ->setDia($dia);

            return $report->exec();
        }

        $reporte = view(
            $view,
            compact('records', 'dia')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function accesosGafete(Request $request)
    {

        $numero = $request->get('numero_rfid');
        $dia = $request->get('dia');
        if ($dia == "") {
            $dia = date('Y-m-d');
        }
        $tipo = $request->get('tipo_gafete');
        $hora = $request->get('hora');


        // $where_tipo = ($tipo == "") ? 'tipo IN ("planta","permiso")' : 'tipo = "' . $tipo . '"';


        $records = VLogAccesoV3::whereRaw(" DATE(lgac_created_at) = '{$dia}'")
            ->orderBy('lgac_created_at', 'desc');

        if ($tipo) {
            // $where_tipo = "tipo = '$tipo'";
            $records->whereRaw("tipo = '$tipo'");
        }

        if ($numero != '') {
            $records->whereLgacCardNumber($numero);
        }

        if ($hora) {
            $arr = explode(' ', $hora);
            if ($arr[1] == 'AM')
                $hora_inicio = $arr[0] == 12 ? '00' : ($arr[0] < 10 ? ("0" . $arr[0]) : $arr[0]); //($arr[0] == 12 ? '00' : ($arr[0] < 10 ? ("0" . $arr[0]) : $arr[0])) .":00";
            else
                $hora_inicio = $arr[0] != 12 ? ($arr[0] + 12) : ($arr[0]); //($arr[0] != 12 ? ($arr[0]+12) : ($arr[0])) .":00";
            // $hora_fin = ((int)$hora_inicio)+1;//str_replace(':00', ':59', $hora_inicio);
            // $hora_fin = $hora_fin < 10 ? ("0".$hora_fin):$hora_fin;
            $records->whereRaw("DATE_FORMAT(lgac_created_at, '%H') = ?", ["$hora_inicio"]);
        }

        $records = $records->get();
        $view = 'web.reportes.accesos-gafete';

        if ($request->get('pdf') == 1) {

            $report = new AccesosBaseReport(null, true, false);

            $report->setView($view)
                ->setRecords($records)
                ->setDia($dia);

            return $report->exec();
        }


        $reporte = view($view, compact('records', 'dia'))
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function accesosGafete6pm(Request $request)
    {

        $dia = $request->get('dia');
        if ($dia == "") {
            $dia = date('Y-m-d');
        }

        $dia = $dia . ' 18:00:00';

        $sql = " SELECT

                    l.lgac_card_number,
                    MAX(l.lgac_created_at) as ultima_entrada,
                    COUNT(l.lgac_id) AS n,
                    lcal_nombre_comercial AS local,
                    nombre, tipo

                FROM v_log_accesos_v3 AS l
                INNER JOIN locales as loc ON l.lcal_id = loc.lcal_id
                WHERE
                    lgac_created_at <= '$dia'
                    and tipo = 'PEATONAL'
                GROUP BY l.lgac_card_number, l.lcal_id, nombre, tipo
                HAVING n%2 <> 0
                ORDER BY lgac_created_at desc

        ";

        $records = \DB::select($sql);
        $view = 'web.reportes.accesos-gafete-6pm';

        if ($request->get('pdf') == 1) {

            $report = new AccesosBaseReport(null, true, false);

            $report->setView($view)
                ->setRecords($records)
                ->setDia($dia);

            return $report->exec();
        }

        $reporte = view($view, compact('records', 'dia'))
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }


    public function gafetesDesactivados(Request $request)
    {

        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $local = $request->get('local');
        $tipo = $request->get('tipos_gafete');


        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }


        $records = VGafetesRfidV3::whereRaw(" DATE(disabled_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");
        //        $records = GafeteEstacionamiento::whereRaw(" DATE(gest_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");

        if ($local != '') {
            $records->whereLcalId($local);
        }

        if ($tipo != '') {
            $records->whereTipo($tipo);
        }

        $records = $records->orderBy('disabled_at', 'desc')->get();


        if ($request->get('pdf') == 1) {

            // return false;

            $report = new GafetesDesactivadosReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);

            return $report->exec();
        }

        $reporte = view(
            'web.reportes.gafetes-desactivados',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' =>
            $reporte]
        ));
    }

    public function comprobantesPago(Request $request)
    {
        $inicio = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('inicio'));
        $fin = \Carbon\Carbon::createFromFormat("Y-m-d", $request->get('fin'));
        $local = $request->get('local');
        $estado = $request->get('estado_cpago');
        $razon_social = $request->get('razon_social');

        if ($inicio->gt($fin)) {
            return response()->json($this->ajaxResponse(false, "Fechas erroneas"));
        }

        $records = ComprobantePago::whereRaw("cpag_fecha_pago BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'");

        if ($local != '') {
            $records->whereCpagLcalId($local);
        }

        if ($estado != '') {
            $records->whereCpagEstado($estado);
        }

        if ($razon_social != '') {
            $records->whereHas('Local', function ($q) use ($razon_social) {
                $q->where('lcal_razon_social', $razon_social);
            });
        }

        $records = $records->with('Local')->orderBy('cpag_fecha_pago', 'desc')->get();
        //        $records->map(static function (ComprobantePago $record) {
        //            $comentario = '';
        //            if ($record->SolcitudesGafete()->count() > 0) {
        //                $record->SolcitudesGafete->map(static function ($sol) use (&$comentario) {
        //                    if ($sol->sgft_comentario_admin)
        //                        $comentario .= $sol->sgft_comentario_admin . '. ';
        //                });
        //            }
        //            if ($record->GafetesEstacionamiento()->count() > 0) {
        //                $record->GafetesEstacionamiento->map(static function ($sol) use (&$comentario) {
        //                    if ($sol->gest_comentario_admin)
        //                        $comentario .= $sol->gest_comentario_admin . '. ';
        //                });
        //            }
        //
        //            $record->cpag_comentario_admin = $comentario;
        //        });
        //        Log::error($records);


        if ($request->get('pdf') == 1) {

            //            return false;

            $report = new ComprobantesPagoReport(null, true, false);
            $report->setRecords($records);
            $report->setInicio($inicio);
            $report->setFin($fin);

            return $report->exec();
        }

        if ($request->get('excel') == 1) {

            //            return false;
            return Excel::download(new ComprobantesPagoExport($records, $inicio, $fin), 'Comprobantes_Pago.xlsx');

            //            $report = new ComprobantesPagoReport(null, true, false);
            //            $report->setRecords($records);
            //            $report->setInicio($inicio);
            //            $report->setFin($fin);
            //
            //            return $report->exec();
        }

        $reporte = view(
            'web.reportes.comprobantes-pago',
            compact('records', 'inicio', 'fin')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' => $reporte]
        ));
    }

    public function saldoLocales(Request $request)
    {
        $local = $request->get('local');
        $razon_social = $request->get('razon_social');
        $query = Local::query();

        if ($local) {
            $query->where('lcal_id', $local);
        }

        if ($razon_social) {
            $query->where('lcal_razon_social', $razon_social);
        }

        $records = $query->get();


        if ($request->get('pdf') == 1) {

            //            return false;

            $report = new SaldoLocalesReport(null, true, false);
            $report->setRecords($records);

            return $report->exec();
        }

        if ($request->get('excel') == 1) {
            return Excel::download(new SaldoLocalesExport($records), 'Saldo_Locales.xlsx');
        }

        $reporte = view(
            'web.reportes.saldo-locales',
            compact('records')
        )
            ->render();

        return response()->json($this->ajaxResponse(
            true,
            "Reporte generado exitosamente",
            ['report' => $reporte]
        ));
    }
}
