<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles;

    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'usr_lcal_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['roles_array'];


    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Usuario';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['sgft_created_at', 'sgft_updated_at', 'sgft_deleted_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch ($eventName) {
            case 'created':
                $message = 'Creó un <b>Usuario:</b> ' . $this->name . ' [' . $this->getKey() . ']';
                break;
            case 'updated':
                $message = 'Actualizó al <b>Usuario:</b> ' . $this->name . ' [' . $this->getKey() . ']';
                break;
            case 'deleted':
                $message = 'Eliminó al <b>Usuario:</b> ' . $this->name . ' [' . $this->getKey() . '] ';
                break;
            default:
                $message = "This model has been {$eventName} [" . $this->getKey() . "]";
                break;
        }

        return $message;
    }

    public function getNavigation()
    {

        $roles = $this->roles->pluck('id');

        return \DB::table('role_has_navigation')->select('navigation_id')->whereIn('role_id', $roles)->distinct()->get();
    }

    ///////////////////////////////////////////////////////////////
    public function Local()
    {
        return $this->belongsTo('App\Local', 'usr_lcal_id', 'lcal_id');
    }

    public function getRolesArrayAttribute()
    {
        return $this->roles->sortBy('name')->pluck('name')->toArray();
    }
}
