<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('personas_dentro_de_plaza')) {
    function personas_dentro_de_plaza()
    {
        return DB::select("SELECT empl.* from empleados_ubicacion as emplub
            left join empleados as empl on empl.empl_id = emplub.emplub_empl_id
            where emplub.emplub_ubicacion = 1;");
    }
}

if (!function_exists('personas_dentro_de_plaza_puerta_maya')) {
    function personas_dentro_de_plaza_puerta_maya()
    {
        return DB::select("SELECT empl.* from empleados_ubicacion as emplub
            left join empleados as empl on empl.empl_id = emplub.emplub_empl_id
            left join locales as lcal on lcal.lcal_id = empl.empl_lcal_id
            where lcal.lcal_razon_social = 'Puerta Maya'
            and emplub.emplub_ubicacion = 1;");
    }
}

if (!function_exists('cantidad_autos_dentro')) {
    function cantidad_autos_dentro()
    {
        return DB::select("SELECT COALESCE(SUM(emplub_autos), 0) as autos from empleados_ubicacion;")[0]->autos;
    }
}

if (!function_exists('cantidad_motos_dentro')) {
    function cantidad_motos_dentro()
    {
        return DB::select("SELECT COALESCE(SUM(emplub_motos), 0) as motos from empleados_ubicacion;")[0]->motos;
    }
}
