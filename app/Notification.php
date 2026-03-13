<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

//use App\Ciclo;
use Spatie\Activitylog\Traits\LogsActivity;

class Notification extends Model
{
    protected $table  = 'notifications';

    protected $casts = [
        'id' => 'string'
    ];

    protected $guarded = ['id'];

}
