<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class EmpleadoUbicacion extends Model
{
    protected $table  = 'empleados_ubicacion';
    protected $primaryKey = 'emplub_empl_id';
    protected $guarded = ['emplub_empl_id'];

    public $timestamps = false;

    protected $dates = ['emplub_fecha'];

    ////LOG CONFIG///////////////////////////////////////////////////

    public function PuertaEntrada(){
        return $this->belongsTo('App\Puerta', 'emplub_door_in_id');
    }

    public function PuertaSalida(){
        return $this->belongsTo('App\Puerta', 'emplub_door_out_id');
    }
}
