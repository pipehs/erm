<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Audit extends Model
{

    protected $fillable = ['name','description'];

    public static function name($id)
    {
        $res = DB::table('audits')->where('id', $id)->value('name');
        return $res;
    }

    //obtiene auditorías que contienen hallazgos
    public static function getAuditsFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            $audits = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->where('audits.id','=',$kind)
                    ->select('audits.id','audit_plans.name as audit_plan','audits.name','audits.description')
                    ->groupBy('audits.id','audit_plans.name','audits.name','audits.description')
                    ->get();
        }
        else
        {
            $audits = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->select('audits.id','audit_plans.name as audit_plan','audits.name','audits.description')
                    ->groupBy('audits.id','audit_plans.name','audits.name','audits.description')
                    ->get();
        }

        return $audits;
    }

    //obtenemos auditoría de una prueba
    public static function getAuditFromAuditTest($org,$test)
    {
        $audits = DB::table('audit_tests')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->where('audit_tests.id','=',$test)
                    ->select('audits.id','audits.name','audits.description')
                    ->groupBy('audits.id','audits.name','audits.description')
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
                ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                ->where('audit_audit_plan.audit_plan_id','=',$audit_plan)
                ->select('audits.id as id','audits.name','audits.description')
                ->get();
    }

    public static function getAudits2($org)
    {
        return DB::table('audits')
            ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
            ->where('audit_plans.organization_id','=',$org)
            ->where('audit_plans.status','=',0)
            ->select('audits.id','audits.name')
            ->get();
    }

    public static function getAuditByName($name)
    {
        return DB::table('audits')
                ->where('name','=',$name)
                ->select('id')
                ->first();
    }

    public static function getAuditAuditPlan($audit_plan_id,$audit_id)
    {
        return DB::table('audit_audit_plan')
                ->where('audit_plan_id','=',$audit_plan_id)
                ->where('audit_id','=',$audit_id)
                ->select('id')
                ->first();
    }
}
