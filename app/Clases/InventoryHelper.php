<?php

namespace App\Clases;

use App\InventarioBaja;
use App\InventarioCompra;

use App\SolicitudGafete;
use App\GafetePreimpreso;
use App\GafeteEstacionamiento;

class InventoryHelper
{


    public static function getCurrentUsedCards()
    {
        $inicio = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2000-01-01 00:00:01');
        $fin = \Carbon\Carbon::now();

//        dd($inicio,$fin);
        //ultima baja reportada
        $ultima_baja = InventarioBaja::latest()->first();
        if ($ultima_baja != null) {
            $inicio = $ultima_baja->ibaj_created_at;
        }


        //acceso
        $acceso = SolicitudGafete::whereRaw(" sgft_fecha BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'")
            ->whereIn('sgft_estado', ['IMPRESA', 'ENTREGADA'])
            ->count();

        //estacionamiento
        $estacionamiento = GafeteEstacionamiento::whereRaw(" DATE(gest_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'")
            ->count();

        //permisos temporales
//        $permisos = GafetePreimpreso::whereRaw(" DATE(gfpi_created_at) BETWEEN '{$inicio->format('Y-m-d')}' AND '{$fin->format('Y-m-d')}'")
//                                            ->whereGfpiTipo('PERMISO')
//                                            ->count();


        $return = [
            'acceso' => $acceso,
            'estacionamiento' => $estacionamiento,
//            'permiso' => $permisos
        ];

        return $return;

    }


}
