<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Navigation extends Model
{
    use LogsActivity;

    protected $table  = 'navigation';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];
    public $timestamps = true;

    protected $dates = ['created_at','updated_at'];

    ////LOG CONFIG///////////////////////////////////////////////////

    protected static $logName = 'Navigation';
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['created_at','updated_at'];
    protected static $logOnlyDirty = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        switch($eventName){
            case 'created':
                $message = 'Creó un nuevo acceso de navegación';
                break;
            case 'updated':
                $message = 'Actualizó un acceso de navegación';
                break;
            case 'deleted':
                $message = 'Eliminó un acceso de navegación';
                break;
            default:
                $message = "This model has been {$eventName}";
                break;
        }

        return $message;
    }

    ////////////////////////////////////////////////////////////////

    public static function getNavigationByRole(int $role_id){

        return \DB::table('role_has_navigation')->select('navigation_id')
                    ->whereRoleId($role_id)
                    ->distinct()
                    ->get();

    }

    public function parent() {

        return $this->hasOne('navigation', 'id', 'parent_id');

    }

    public function children() {

        return $this->hasMany('navigation', 'parent_id', 'id');

    }

    public static function tree() {

        return static::with(implode('.', array_fill(0, 4, 'children')))->where('parent_id', '=', NULL)->orderBy('weight')->get();

    }



}
