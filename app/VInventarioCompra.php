<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class VInventarioCompra extends Model
{
    use SoftDeletes;
    use LogsActivity;


    protected $table  = 'v_inventario_compras';
    protected $primaryKey = 'icmp_id';
    protected $prefix = 'icmp_';

    protected $guarded = ['icmp_id'];
    public $timestamps = true;

    const CREATED_AT = 'icmp_created_at';
    const UPDATED_AT = 'icmp_updated_at';
    const DELETED_AT = 'icmp_deleted_at';

    protected $dates = ['icmp_created_at','icmp_updated_at','icmp_deleted_at'];


}
