<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class PermisoMantenimientoTrabajador extends Model
{
    protected $table = 'permiso_mantenimiento_trabajadores';
    protected $primaryKey = 'pmtb_id';
    protected $prefix = 'pmtb_';

    protected $guarded = ['pmtb_id'];
    public $timestamps = true;

    const CREATED_AT = 'pmtb_created_at';
    const UPDATED_AT = 'pmtb_updated_at';

    protected $dates = ['pmtb_created_at', 'pmtb_updated_at'];

    public function PermisoMantenimiento()
    {
        return $this->belongsTo('App\PermisoMantenimiento', 'pmtb_pmtt_id');
    }
}
