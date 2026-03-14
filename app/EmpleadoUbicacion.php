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
    // protected $guarded = ['emplub_empl_id'];

    protected $fillable = [
        'emplub_empl_id',
        'emplub_lcal_id',
        'emplub_door_in_id',
        'emplub_door_out_id',
        'emplub_ubicacion',
        'emplub_fecha',
        'emplub_autos',
        'emplub_motos'
    ];

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
