<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit extends Model
{
    protected $fillable = ['name','description'];

    public static function name($id)
    {
        $res = DB::table('audits')->where('id', $id)->value('name');
        return $res;
    }
/*
    public static function name($audit_id)
    {
    	$res = DB::table('audits')
    			->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
    			->where('audit_audit_plan.id','=',$audit_id)
    			->select('audits.name')
    			->first();

    	return $res->name;
    }
*/
    //obtiene auditorías que contienen hallazgos
    public static function getAuditsFromIssues($org)
    {
        $audits = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->select('audits.id','audit_plans.name as audit_plan','audits.name','audits.description')
                    ->groupBy('audits.id')
                    ->get();

        return $audits;
    }
}
