<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\SolicitudGafete;
use Spatie\Activitylog\Traits\LogsActivity;

//YA NO EXISTE LA VISTA NO UTILIZAR ESTA CLASE

class VComprobantePago extends Model
{
//    use SoftDeletes;
//    use LogsActivity;


    protected $table  = 'v_comprobantes_pago';
    protected $primaryKey = 'cpag_id';
    protected $prefix = 'cpag_';

    protected $guarded = ['cpag_id'];
//    public $timestamps = true;

//    const CREATED_AT = 'cpag_created_at';
//    const UPDATED_AT = 'cpag_updated_at';
//    const DELETED_AT = 'cpag_deleted_at';

//    protected $dates = ['cpag_created_at','cpag_updated_at','cpag_deleted_at'];


}
