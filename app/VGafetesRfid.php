<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VGafetesRfid extends Model
{

    protected $table  = 'v_gafetes_rfid';
    protected $primaryKey = 'referencia';
    // protected $prefix = 'embq_';

    protected $guarded = ['referencia'];
    public $timestamps = false;

//    const CREATED_AT = 'created_at';
//    const UPDATED_AT = 'updated_at';

    protected $dates = ['inicio','fin','activated_at','disabled_at'];

    public function Local(){
        return $this->belongsTo('App\Local','lcal_id');
    }


    public function getOriginalRecord(){

        if($this->tipo == 'planta'){
            return SolicitudGafete::find($this->ref_id);
        }

        if($this->tipo == 'estacionamiento'){
            return GafeteEstacionamiento::find($this->ref_id);
        }

        if($this->tipo == 'permiso'){
            return GafetePreimpreso::find($this->ref_id);
        }

    }


}
