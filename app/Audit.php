<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit extends Model
{
    protected $fillable = ['name','description'];

    public static function name($audit_id)
    {
    	$res = DB::table('audits')
    			->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
    			->where('audit_audit_plan.id','=',$audit_id)
    			->select('audits.name')
    			->first();

    	return $res->name;
    }

    //obtiene auditorías que contienen hallazgos
    public static function getAuditsFromIssues($org)
    {
        $audits = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->select('audit_audit_plan.id','audit_plans.name as audit_plan','audits.name','audits.description')
                    ->groupBy('audit_audit_plan.id')
                    ->get();

        return $audits;
    }
}
