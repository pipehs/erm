<?php

namespace Ermtool;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Auth;
use Carbon;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','dv','name','surnames','password','email','rest_id','cc_user'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function system_roles()
    {
        return $this->belongsToMany('Ermtool\System_role');
    }

    public function users()
    {
        return $this->belongsToMany('Ermtool\CcRole');
    }

    public static function getName($rut)
    {
        $user = \Ermtool\User::where('id',$rut)->first(['name','surnames']);
        
        return $user->name.' '.$user->surnames;
    }
}
