<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Action_plan extends Model
{
    public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }

    protected $fillable = ['issue_id','description','final_date','status','stakeholder_id'];


    public static function getActionPlanFromIssue($issue)
    {
    	return DB::table('action_plans')
    			->where('issue_id','=',$issue)
    			->select('id','description','status')
    			->first();
    }

    public static function getOpenedActionPlans()
    {
    	return DB::table('action_plans')
    			->where('status','=',0)
    			->select('action_plans.id','action_plans.description','action_plans.final_date','action_plans.stakeholder_id')
    			->get();
    }

    //obtiene información asociada al plan de acción
    public static function getInfo($id,$kind)
    {
        if ($kind == 1) //info de plan de auditoría
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('action_plans.id','=',$id)
                    ->select('audit_plans.name as audit_plan','audits.name as audit')
                    ->first();
        }
        else if ($kind == 2) //info de programa de auditoría
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','issues.audit_audit_plan_audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('action_plans.id','=',$id)
                    ->select('audit_plans.name as audit_plan','audits.name as audit','audit_programs.name as audit_program')
                    ->first();
        }
        else if ($kind == 3) //info de ejecución de auditorías
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('action_plans.id','=',$id)
                    ->select('audit_plans.name as audit_plan','audits.name as audit','audit_programs.name as audit_program','audit_tests.name as audit_test')
                    ->first();
        }
        else if ($kind == 4) //info de organización
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('organizations','organizations.id','=','issues.organization_id')
                    ->where('action_plans.id','=',$id)
                    ->select('organizations.name as organization')
                    ->first();
        }
        else if ($kind == 5) //info de subproceso
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                    ->join('processes','processes.id','=','subprocesses.process_id')
                    ->where('action_plans.id','=',$id)
                    ->select('processes.name as process','subprocesses.name as subprocess')
                    ->first();
        }
        else if ($kind == 6) //info de proceso
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('processes','processes.id','=','issues.process_id')
                    ->where('action_plans.id','=',$id)
                    ->select('processes.name as process')
                    ->first();
        }
        else if ($kind == 7 || $kind == 8) //info de control de proceso o de negocio
        {
            $control = DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('controls','controls.id','=','issues.control_id')
                    ->where('action_plans.id','=',$id)
                    ->select('controls.name as control')
                    ->first();

            if (empty($control)) //si está vacío entonces es control de negocio
            {
                $control = DB::table('action_plans')
                        ->join('issues','issues.id','=','action_plans.issue_id')
                        ->join('controls','controls.id','=','issues.control_id')
                        ->where('action_plans.id','=',$id)
                        ->select('controls.name as control')
                        ->first();
            }

            return $control;
        }
        else if ($kind == 9) //info de control (asociado a evaluacion)
        {
            return DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('control_evaluation','control_evaluation.id','=','issues.control_evaluation_id')
                    ->join('controls','controls.id','=','control_evaluation.control_id')
                    ->where('action_plans.id','=',$id)
                    ->select('controls.name as control')
                    ->first();
        }

    }
}
