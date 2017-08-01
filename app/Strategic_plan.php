<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

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

    //obtiene plan estratégico vigente de organización
    public static function getActivePlan($org)
    {
        return DB::table('strategic_plans')
                ->where('organization_id','=',$org)
                ->where('status','=',1)
                ->select('id','name','comments','initial_date','final_date')
                ->first();
    }

}
