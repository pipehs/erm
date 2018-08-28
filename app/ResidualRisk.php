<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

class ResidualRisk extends Model
{
    protected $fillable = ['probability','impact','organization_risk_id'];

    public function getMaxCreatedAt($org_risk_id,$ano,$mes,$dia)
    {
    	return DB::table('residual_risk')
            ->where('residual_risk.organization_risk_id','=',$org_risk_id)
            ->where('residual_risk.created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
            ->max('residual_risk.created_at');
    }

    public static function getProbaImpact($org_risk_id,$ano,$mes,$dia)
    {
        return DB::table('residual_risk')
            ->where('organization_risk_id','=',$org_risk_id)
            ->where('created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
            ->select('probability','impact')
            ->orderBy('created_at','DESC')
            ->first();
    }
}
