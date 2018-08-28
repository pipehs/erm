<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

class ControlledRiskManual extends Model
{
    protected $fillable = ['probability','impact','organization_risk_id','impact2','probability2','user_id','comments'];

    public function getMaxCreatedAt($org_risk_id,$ano,$mes,$dia)
    {
    	return DB::table('controlled_risk_manual')
	        ->where('controlled_risk.organization_risk_id','=',$org_risk_id)
	        ->where('controlled_risk.created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
	        ->max('controlled_risk.created_at');
	}

	public static function getProbaImpact($org_risk_id,$ano,$mes,$dia)
    {
        return DB::table('controlled_risk_manual')
            ->where('organization_risk_id','=',$org_risk_id)
            ->where('created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
            ->select('probability','impact')
            ->orderBy('created_at','DESC')
            ->first();
    }
}
