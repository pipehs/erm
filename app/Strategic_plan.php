<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Strategic_plan extends Model
{
    protected $fillable = ['name','comments','initial_date','status'];

	//eliminamos created_at y updated_at
    //public $timestamps = false;
    
    public function objectives()
    {
    	return $this->hasMany('Ermtool\Objective');
    }

    public static function name($id)
    {
    	$res = DB::table('strategic_plans')->where('id', $id)->value('name');
    	return $res;
    }

}
