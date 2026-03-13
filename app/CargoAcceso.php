<?php
namespace App;

/**
 * ESTE MODELO NO SE USA PUES SE FAVORECIÓ LA RELACIÓN LOCAL - ACCESO DENTRO DE LA TABLA DE LOCALES
 */

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CargoAcceso extends Pivot
{
    protected $primaryKey = 'cgac_id';
    protected $table  = 'cargo_accesos';
    protected $prefix = 'cgac_';

    protected $guarded = ['cgac_id'];
    public $timestamps = false;


    public function Cargo(){
        return $this->belongsTo('App\CatCargo','cgac_crgo_id');
    }

    public function Acceso(){
        return $this->belongsTo('App\CatAcceso','cgac_cacs_id');
    }

}
