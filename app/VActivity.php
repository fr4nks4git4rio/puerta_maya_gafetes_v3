<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VActivity extends Model
{

    protected $table  = 'v_activity_log';
    protected $primaryKey = 'id';
    // protected $prefix = 'embq_';

    protected $guarded = ['id'];
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at','updated_at'];

}
