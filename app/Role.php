<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Role extends Model
{
	public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }
    
    protected $fillable = ['name','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function stakeholders()
    {
    	return $this->belongsToMany('Ermtool\Stakeholder');
    }

    public static function name($id)
    {
        return DB::table('roles')->where('id',$id)->value('name');
    }
}
