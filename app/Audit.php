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

    //obtenemos auditoría de una prueba
    public static function getAuditFromAuditTest($org,$test)
    {
        $audits = DB::table('issues')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->whereNotNull('issues.audit_test_id')
                    ->select('audits.id','audits.name','audits.description')
                    ->groupBy('audits.id')
                    ->get();

        return $audits;
    }

    //obtiene información de audit_audit_plan (recursos, hh, etc)
    public static function getAuditInfo($audit_plan,$audit)
    {
        return DB::table('audit_audit_plan')
            ->join('audits','audits.id','=','audit_audit_plan.audit_id')
            ->where('audit_plan_id','=',$audit_plan)
            ->where('audit_id','=',$audit)
            ->select('audits.id as audit_id','audits.name as audit_name','initial_date','final_date','resources')
            ->first();
    }

    //obtenemos auditorías no seleccionadas de un plan
    public static function getAuditsNotSelected($audit_plan)
    {
        global $id;
        $id = $audit_plan;

        return DB::table('audits')
                ->whereNotIn('audits.id', function($q){
                        $q->select('audit_id')
                            ->from('audit_audit_plan')
                            ->where('audit_plan_id','=',$GLOBALS['id']);
                    })
                ->lists('audits.name','audits.id');
    }

    //obtenemos auditorías seleccionadas en un plan
    public static function getAudits($audit_plan)
    {
        return DB::table('audit_audit_plan')
                ->where('audit_plan_id','=',$audit_plan)
                ->select('audit_id as id')
                ->get();
    }
}
