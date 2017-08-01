<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Role extends Model
{
    
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
