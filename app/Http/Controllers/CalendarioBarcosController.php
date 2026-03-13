<?php
namespace App\Http\Controllers;

//Framework
use App\Notifications\ArriboCruceroActualizado;
use App\Notifications\PermisoMantenimientoRechazado;
use App\User;
use Illuminate\Http\Request;


//App Clases
use App\BarcoEvento;


class CalendarioBarcosController extends Controller
{

    protected $rules = [
        'insert-event' =>[
            'fecha'    => 'required:date',
            'id_barco'     => 'required|exists:shorex.barco,id',
            'hora_llegada'      => 'required|date_format:G:i',
            'hora_partida'      => 'required|date_format:G:i',
            'notificar'      => 'nullable',
            'color'    => 'required',
        ],

        'update-event' =>[
            'id'    => 'required:exists:shorex.barco_puerto_disponible,id',
            'fecha'    => 'required:date',
            'id_barco'     => 'required|exists:shorex.barco,id',
            'hora_llegada'      => 'required|date_format:G:i',
            'hora_partida'      => 'required|date_format:G:i',
            'notificar'      => 'nullable',
            'color'    => 'required',
        ],
    ];

    protected $etiquetas = [
        'id'    => 'Id',
        'fecha'    => 'Fecha',
        'id_barco'     => 'Barco',
        'hora_llegada'      => 'Hora de llegada',
        'hora_partida'      => 'Hora de partida',
        'color'    => 'Color',
        'notificar'      => 'Notificar a locatarios',
    ];


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }

    public function index(Request $request){
        return view('web.barco.calendario');
    }


    public function obtenerEventos(Request $request){
        $start = $request->start ?? date('Y-m-01');
        $end   = $request->end ?? date('Y-m-t');
        $result = \DB::connection('shorex')
            ->table('barco_puerto_disponible AS n')

            ->selectRaw('n.*, b.id as id_barco, b.nombre as nombreBarco')
            ->join('barco as b', 'n.id_barco', '=', 'b.id')
            ->where('n.activo', 1)
            ->whereRaw("n.fecha_inicio BETWEEN '$start' AND  '$end'")
            ->get();

        $eventos = collect();
        if (count($result) > 0){
            foreach ($result as $eventoItem){
                // $barco = $em->getRepository('AppBundle:Barco')->find($eventoItem['idBarco']);
                $f_inic = $eventoItem->fecha_inicio;
                $start_e = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $f_inic)->format('Y-m-d').' '.$eventoItem->hora_llegada;
                $end_e = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $f_inic)->format('Y-m-d').' '.$eventoItem->hora_partida;

                $id = $eventoItem->id;
                $title = $eventoItem->nombreBarco;
                $start = $start_e;
                $end = $end_e;
                $color = $eventoItem->color;
                $evento = [
                    "id" => $id,
                    "title" => utf8_decode($title),
                    "titleid" => $eventoItem->id_barco,
                    "start" => $start,
                    "end" => $end,
                    "color" => "#f5f4f4",
                    'barco' => 1,
                    "textColor" => $color,
                    "barco_id" => $eventoItem->id_barco,
                    "event_id" => $eventoItem->id,
                    "description"=> "DE ".$eventoItem->hora_llegada ." A ". $eventoItem->hora_partida,
                    ];
                $eventos->push($evento);
            }
        }

        return json_decode($eventos);

    }

    public function nuevoEventoForm($dateStr, Request $request){
        $url = url('barco/insert-event');

        $barcos =\DB::connection('shorex')
                ->table('barco')
                ->selectRaw('id, nombre')
                ->orderBy('nombre')
                ->get()
                ->pluck('nombre','id')
                ->put( '' ,'SELECCIONE UNA OPCIÓN');

        return view('web.barco.form-new-event',compact('url','barcos', 'dateStr'));
    }

    public function editarEventoForm($evento, Request $request){

        $evento = BarcoEvento::find($evento);


        $url = url('barco/update-event');

        $barcos =\DB::connection('shorex')
            ->table('barco')
            ->selectRaw('id, nombre')
            ->orderBy('nombre')
            ->get()
            ->pluck('nombre','id')
            ->put( '' ,'SELECCIONE UNA OPCIÓN');

        return view('web.barco.form-edit-event',compact('url','barcos', 'evento'));
    }

    public function insertEvent(Request $request){

        if(! $this->validateAction('insert-event')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {


                $evento= new BarcoEvento();
                $evento->id_barco = $this->data['id_barco'];
                $evento->fecha_inicio = $this->data['fecha'];
                $evento->fecha_fin = $this->data['fecha'];
                $evento->hora_llegada= $this->data['hora_llegada'];
                $evento->hora_partida = $this->data['hora_partida'];
                $evento->activo= 1;
                $evento->color = $this->data['color'];

                $evento->save();

                $response_message = 'Evento <b>CREADO</b> correctamente.';
                $response_data = [];

                if($request->has('notificar') && $request->get('notificar') == 1 ){

                    try{
                        // N o t i f i c a ci o n -------------------------------------------------------------
                        $locatarios = User::role('LOCATARIO')
//                        ->inRandomOrder()
//                        ->limit(2)
                            ->get();

                        \Notification::send($locatarios, new ArriboCruceroActualizado($evento));
                        //-------------------------------------------------------------------------------------

                    }catch (\Exception $e){
                        $response_message.= ' Error al notificar';
                        $response_data['notification_error'] = $e->getMessage();
                    }

                }

                \DB::commit();
                return response()->json($this -> ajaxResponse(true,$response_message, $response_data));

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() . $e -> getFile() . $e -> getLine()  ));
            }



        }



    }

    public function updateEvent(Request $request){

        if(! $this->validateAction('update-event')){

            return response() -> json($this->ajaxResponse(false,'Errores en el formulario!'));

        }else{


            \DB::beginTransaction();
            try
            {
                $evento = BarcoEvento::find($this->data['id']);

                $evento->id_barco = $this->data['id_barco'];
                $evento->fecha_inicio = $this->data['fecha'];
                $evento->fecha_fin = $this->data['fecha'];
                $evento->hora_llegada= $this->data['hora_llegada'];
                $evento->hora_partida = $this->data['hora_partida'];
                $evento->activo= 1;
                $evento->color = $this->data['color'];

                $evento->save();

                $response_message = 'Evento <b>ACTUALIZADO</b> correctamente.';
                $response_data = [];

                if($request->has('notificar') && $request->get('notificar') == 1 ){

                    try{

                        // N o t i f i c a ci o n -------------------------------------------------------------
                        $locatarios = User::role('LOCATARIO')
//                        ->inRandomOrder()
//                        ->limit(2)
                            ->get();

                        \Notification::send($locatarios, new ArriboCruceroActualizado($evento));
                        //-------------------------------------------------------------------------------------
                    }catch (\Exception $e){
                        $response_message.= ' Error al notificar';
                        $response_data['notification_error'] = $e->getMessage();
                    }


                }

                \DB::commit();
                return response()->json($this -> ajaxResponse(true,$response_message));

            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                return response()->json($this -> ajaxResponse(false,"Error en el servidor!", $e -> getMessage() . $e -> getFile() . $e -> getLine()  ));
            }



        }



    }

}
