<?php

namespace App\Http\Controllers;

use App\Actions\NotificarPermisosTemporalesVencidos;
use App\Actions\ActualizarTipoCambio;
use App\Clases\Banxico;

use App\DisennoGafetePaqueteVigencia;
use App\DisennoGafeteVigencia;
use Illuminate\Http\Request;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        DisennoGafetePaqueteVigencia::checkFechaIncioImpresion();

        //        $notificacionesHoy = \DB::table('notifications')->whereRaw(' DATE(created_at) = CURDATE() ')->first();
        //        if($notificacionesHoy == null){
        //            $Notificator = new NotificarPermisosTemporalesVencidos();
        //            $Notificator->execute();
        //        }
        //
        $user = auth()->getUser();
        ////        $misNotificaciones = $user->unreadNotifications;
        //
        //        $tipoCambio = new ActualizarTipoCambio(true);
        //        $tipoCambio->execute();

        //        $tipoCambio = Banxico::getExRate();
        //        dd($tipoCambio);

        //Si tiene el rol locatario mandamos a barcos
        //        if($user->hasRole('LOCATARIO')){
        if ($user->hasRole('GUARDIA SEGURIDAD')) {
            return view('web.personal-dentro');
        } else {
            return view('web.barco.calendario-readonly');
        }
        //        }

        //        return view('dashboard');
    }


    public function infoGeneral()
    {
        return view('info-general');
    }

    public function videoAyuda()
    {
        return view('tutorial-video');
    }

    public function modalVideoAyuda($seconds)
    {
        $url_video = "https://www.youtube.com/embed/X38IkBEXjqI?start=$seconds&version=3&autoplay=1";
        return view('modal-video-tutorial', compact('url_video'));
    }

    public function loadDataPersonalDentro()
    {
        return $this->ajaxResponse(true, '', [
            'cantidad_motos_dentro' => cantidad_motos_dentro(),
            'cantidad_autos_dentro' => cantidad_autos_dentro(),
            'personas_dentro_de_plaza' => count(personas_dentro_de_plaza()),
            'personas_dentro_de_plaza_puerta_maya' => count(personas_dentro_de_plaza_puerta_maya())
        ]);
    }
}
