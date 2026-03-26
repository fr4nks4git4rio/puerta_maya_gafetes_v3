<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudGafeteReasignarCambioEstado extends Model
{
    protected $table = 'solicitudes_gr_cambios_estados';
    protected $primaryKey = 'sgftrece_id';
    protected $prefix = 'sgftrece_';

    protected $guarded = ['sgftrece_id'];
    public $timestamps = true;

    const CREATED_AT = 'sgftrece_created_at';
    const UPDATED_AT = 'sgftrece_updated_at';

    protected $dates = ['sgftrece_created_at', 'sgftrece_updated_at'];

    public function Solicitud()
    {
        return $this->belongsTo('App\SolicitudGafeteReasignar', 'sgftrece_sgftre_id');
    }

    public function RealizadoPor()
    {
        return $this->belongsTo('App\User', 'sgftrece_realizado_por_id');
    }
}
