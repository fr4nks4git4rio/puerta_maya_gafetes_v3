<?php
namespace App\Actions;

use Illuminate\Http\Request;

use App\PermisoTemporal;
use App\User;
use App\Notifications\PermisoTemporalVencido;
use Illuminate\Support\Facades\Notification;


class NotificarPermisosTemporalesVencidos
{


    public function __construct()
    {

    }

    public function execute() : bool
    {

        set_time_limit(60);

        try{


            //buscamos los permisos temporales que vencen hoy
            $permisosVencidos = PermisoTemporal::proximosVencer();

            if(count($permisosVencidos) > 0):

                //obtenemos todos los usuarios con rol de RECEPCION
                $recepcionistas = User::role('RECEPCIÓN')->get();

                foreach($permisosVencidos as $permiso):

                    Notification::send($recepcionistas, new PermisoTemporalVencido($permiso));

                endforeach;

            endif;


            activity()
                ->inLog('Tarea Programada')
                ->log("Se corrió el proceso de notificar los permisos temporales que vencen hoy");

            return true;

        }catch(\Exception $e){

            // \DB::rollBack();

            \Log::error('Catched Exeption: '.$e->getMessage().' On: '.$e->getFile().' @'.$e->getLine());

            throw($e);

            // return $e->getMessage().' '.$e->getFile().':'.$e -> getLine();

            return false;
        }


    }

}
