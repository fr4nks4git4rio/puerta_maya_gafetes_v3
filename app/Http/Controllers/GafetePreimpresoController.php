<?php

namespace App\Http\Controllers;

use App\Actions\ActivarTarjeta;
use App\Actions\DesactivarTarjeta;
use App\Reports\GafetePermisoTemporalReport;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;


use App\GafetePreimpreso;

class GafetePreimpresoController extends Controller
{

    protected $rules = [
        'insert' => [
            'gfpi_numero' => 'required|digits_between:5,10',
            'gfpi_tipo' => 'required',
            'gfpi_comentario' => 'nullable',
        ],

        'edit' => [
            'gfpi_id' => 'required|exists:gafetes_preimpresos,gfpi_id',
            'gfpi_numero' => 'required|digits_between:5,10',
            'gfpi_tipo' => 'required',
            'gfpi_comentario' => 'nullable',
        ],


    ];

    protected $etiquetas = [
        'gfpi_id' => 'Id',
        'gfpi_numero' => 'Número',
        'gfpi_tipo' => 'Tipo',
        'gfpi_comentario' => 'Comentario',
    ];


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


    public function index(Request $request, Builder $htmlBuilder)
    {
        return view('web.gafete-preimpreso.index');
    }

    public function indexPermisosTemporales(Request $request, Builder $htmlBuilder)
    {

//        dd($request);
        if ($request->get('draw') >= 1) {

            $records = GafetePreimpreso::select(['gfpi_id', 'gfpi_numero', 'gfpi_tipo', 'gfpi_comentario', \DB::raw('YEAR(gfpi_created_at) as gfpi_anio')]);


            return Datatables::of($records)
                ->addColumn('actions',function(GafetePreimpreso $model){

                    $html = '<div class="btn-group">';
                    $html .= '<span class="btn btn-primary btn-sm btn-imprimir" 
                                    title="Imprimir" data-id=' . $model->gfpi_id . '><i class="zmdi zmdi-print"></i></span>';

                    $html .= '</div>';

                    return $html;

                })
                ->filterColumn('gfpi_anio', function($query, $keyword) {
                    $sql = " YEAR(gfpi_created_at)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        //Definición del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select'=>'single',
            'autoWidth'  => false,
            'language' => [
                'url'=> asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order'=>[[0,'desc']]
        ]);

        $htmlBuilder->ajax( [
            'url'=> url('gafete-preimpreso/permisos-temporales') ,
//            'data'=> 'function(d){d.filter_local = $(\'select[name=filter-local]\').val();}'
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'gfpi_id', 'name' => 'gfpi_id', 'title' => 'Id', 'visible'=>true])
            ->addColumn(['data' => 'gfpi_anio', 'name' => 'gfpi_anio', 'title' => 'Año', 'search'=>true])
            ->addColumn(['data' => 'gfpi_numero', 'name' => 'gfpi_numero', 'title' => 'Número', 'search'=>true])
//            ->addColumn(['data' => 'gfpi_tipo', 'name' => 'gfpi_tipo', 'title' => 'Tipo', 'search'=>true])
            ->addColumn(['data' => 'gfpi_comentario', 'name' => 'gfpi_comentario', 'title' => 'Comentario'])
            ->addColumn(['data' => 'actions', 'name' => 'actions', 'title' => 'Acciones']);


        return view('web.gafete-preimpreso.index-temporales', compact('dataTable'));

    }


    public function indexOtro(Request $request, Builder $htmlBuilder)
    {
        return 'En construcción';
    }


    public function formTemporal(GafetePreimpreso $gafete= null, Request $request){

        $url = ($gafete == null)? url('gafete-preimpreso/insert') : url('gafete-preimpreso/edit',$gafete->getKey() );

        return view('web.gafete-preimpreso.form-temporal', compact('gafete','url'));

    }

    public function insert(Request $request)
    {

        if(! $this->validateAction('insert')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {
                $gafete = GafetePreimpreso::create($this -> data);

                //activamos la tarjeta
                $gafeteRfid = $gafete->getVGafeteRfid();

                $activar = new ActivarTarjeta($gafeteRfid);
                $res = $activar->execute();

                if($res == false){
                    return response() -> json($this->ajaxResponse(false,'Ocurrió un error al activar la tarjeta en la controladora.', $res  ));
                }

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() ));
            }

            \DB::commit();
            return response()->json($this -> ajaxResponse(true,'Gafete <b>CREADO</b> correctamente.'));

        }
    }

    public function edit(GafetePreimpreso $gafete, Request $request)
    {
        if(! $this->validateAction('edit')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{

            \DB::beginTransaction();

            try
            {

                if($this->data['gfpi_numero'] != $gafete->gfpi_numero){

                    //desactivamos la tarjeta
                    $gafeteRfid = $gafete->getVGafeteRfid();

                    $desactivar = new DesactivarTarjeta($gafeteRfid);
                    $res = $desactivar->execute();

                    if($res == false){
                        return response() -> json($this->ajaxResponse(false,'Ocurrió un error al desactivar la tarjeta en la controladora.', $res  ));
                    }

                    //actualizamos el gafete
                    $gafete->update($this->data);

                    //activamos la tarjeta
                    $gafeteRfid = $gafete->getVGafeteRfid();

                    $activar = new ActivarTarjeta($gafeteRfid);
                    $res = $activar->execute();

                    if($res == false){
                        return response() -> json($this->ajaxResponse(false,'Ocurrió un error al activar la tarjeta en la controladora.', $res  ));
                    }

                }


            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
            }

            \DB::commit();
            return response() -> json($this->ajaxResponse(true,"Gafete <b>EDITADO</b> correctamente."));
        }

    }

    public function delete(GafetePreimpreso $gafete,Request $request)
    {

        \DB::beginTransaction();
        try
        {
            if($gafete->gfpi_permiso_actual > 0){
                return response() -> json($this->ajaxResponse(false, 'El gafete esta asignado a un permiso.'));
            }

            //desactivamos la tarjeta
            $gafeteRfid = $gafete->getVGafeteRfid();

            $desactivar = new DesactivarTarjeta($gafeteRfid);
            $res = $desactivar->execute();

            if($res == false){
                return response() -> json($this->ajaxResponse(false,'Ocurrió un error al desactivar la tarjeta en la controladora.', $res  ));
            }

            $gafete->delete();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response() -> json($this->ajaxResponse(false,$e -> getMessage()));
        }

        \DB::commit();
        return response() -> json($this->ajaxResponse(true,"Gafete <b>ELIMIADO</b> correctamente."));


    }


    public function pdfPermisoTemporal(GafetePreimpreso $gafete)
    {

        $report = new GafetePermisoTemporalReport(null,true,false);
        $report->setGafete($gafete);

        return $report->exec();


//        return view('web.solicitud-gafete.imprimir', compact('solicitud', 'url'));
    }

    public function getJSON(GafetePreimpreso $gafete){
        return response()->json( $gafete );
    }

//    /*Obtiene la información para un campo select2*/
//    public function getSelectOptions(Request $request){
//      $q = $request -> q;
//
//      $records = Local::select(\DB::raw('lcal_id as id, lcal_nombre_comercial as text'))
//                              ->where('lcal_nombre_comercial','like',"%$q%")
//                              ->get() -> toArray();
//      $records[0]['selected'] = true;
//      return response()->json( $records);
//    }


}
