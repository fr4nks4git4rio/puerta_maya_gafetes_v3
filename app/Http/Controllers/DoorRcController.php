<?php

namespace App\Http\Controllers;
use App\Clases\DoorCommandGenerator;
use App\Puerta;
use App\User;

use Illuminate\Http\Request;

class DoorRcController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->data = request()->all();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $token)
    {

        $usuario = User::whereDoorToken($token)->first();

        if($usuario == null){
            abort(401,'Token no válido.');
        }

        $puertas = Puerta::all();

        return view('web.door-rc.index',compact('usuario', 'puertas'));
    }

    public function openDoor(string $token, Puerta $door){

        $usuario = User::whereDoorToken($token)->first();

        if($usuario == null){
            abort(401,'Token no válido.');
        }

        $dcg = new DoorCommandGenerator();
        $res = $dcg->openDoor($door->door_pin);



        if($res['success']){

            activity()
                ->causedBy($usuario)
                ->inLog('Control de Accesso')
                ->withProperties(
                    ['attributes'=> [
                        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                        'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                    ]]
                )
                ->log("Abrió la puerta <b>".$door->door_nombre."</b> con el uso de su token remoto");

            return response()->json($this -> ajaxResponse( true, 'El comando se envió correctamente' ));
        }else{

            activity()
                ->causedBy($usuario)
                ->inLog('Control de Accesso')
                ->withProperties(
                    ['attributes'=> [
                        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                        'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                        'DC_ERROR' => $res['output']
                    ]]
                )
                ->log("Intentó abrir la puerta <b>".$door->door_nombre."</b> con el uso de su token remoto");


            return response()->json($this -> ajaxResponse( true, 'Ocurrió un error al generar la petición' ));
        }

    }

}
