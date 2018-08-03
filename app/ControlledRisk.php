<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

class ControlledRisk extends Model
{
    protected $fillable = ['results','organization_risk_id'];

    public static function getMaxCreatedAt($org_risk_id,$ano,$mes,$dia)
    {
    	return DB::table('controlled_risk')
            ->where('controlled_risk.organization_risk_id','=',$org_risk_id)
            ->where('controlled_risk.created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
            ->max('controlled_risk.created_at');
    }

    public static function getResults($org_risk_id,$ano,$mes,$dia)
    {
    	return DB::table('controlled_risk')
            ->where('controlled_risk.organization_risk_id','=',$org_risk_id)
            ->where('controlled_risk.created_at','<=',date($ano.'-'.$mes.'-'.$dia.' 23:59:59'))
            ->select('results')
            ->orderBy('created_at','DESC')
            ->first();
    }
}
