<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Cause extends Model
{
    
    protected $fillable = ['name','description','status'];

    public function risks()
    {
    	return $this->belongsToMany('Ermtool\Risk');
    }

    public static function getCausesFromRisk($org_risk_id)
    {
        return DB::table('causes')
                ->join('cause_risk','cause_risk.cause_id','=','causes.id')
                ->join('organization_risk','organization_risk.risk_id','=','cause_risk.risk_id')
                ->where('organization_risk.id','=',$org_risk_id)
                ->select('causes.name','causes.description')
                ->get();
    }

    public static function name($id)
    {
        return DB::table('causes')->where('id',$id)->value('name');
    }

    public static function getCauseByName($name)
    {   
        return DB::table('causes')
                ->where('name','=',$name)
                ->select('id')
                ->first();
    }

    public static function getCauseByNameAndDescription($name,$desc)
    {   
        return DB::table('causes')
                ->where('name','=',$name)
                ->where('description','=',$desc)
                ->select('id')
                ->first();
    }
     
}
