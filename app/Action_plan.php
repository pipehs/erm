<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Action_plan extends Model
{

    protected $fillable = ['issue_id','description','final_date','status','stakeholder_id'];


    public static function getActionPlanFromIssue($issue)
    {
    	return DB::table('action_plans')
    			->where('issue_id','=',$issue)
    			->select('id','description','status')
    			->first();
    }

    //ACT 29-01-18: Por ahora haremos 2 funciones, pero luego se debe actualizar y dejar sólo una, ya que posteriormente se debe actualizar y un hallazgo puede tener más de un plan de acción
    public static function getActionPlanFromIssue2($issue)
    {
        return DB::table('action_plans')
                ->where('issue_id','=',$issue)
                ->select('id','description','status','stakeholder_id','final_date')
                ->get();
    }

    public static function getOpenedActionPlans()
    {
    	return DB::table('action_plans')
    			->where('status','=',0)
    			->select('action_plans.id','action_plans.description','action_plans.final_date','action_plans.stakeholder_id','action_plans.issue_id','action_plans.economic_value','action_plans.currency')
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
        else if ($kind == 10) //info de riesgos
        {
            return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->join('issue_organization_risk','issue_organization_risk.organization_risk_id','=','organization_risk.id')
                ->join('issues','issues.id','=','issue_organization_risk.issue_id')
                ->join('action_plans','action_plans.issue_id','=','issues.id')
                ->where('action_plans.id','=',$id)
                ->select('organization_risk.id','risks.name','risks.description')
                ->groupBy('organization_risk.id','risks.name','risks.description')
                ->get();
        }

    }

    public static function getActionPlanByDescription($description)
    {
        return DB::table('action_plans')
                ->where('description','=',$description)
                ->select('*')
                ->first();
    }

    public static function getProgressPercentage($id)
    {
        $max_date = DB::table('progress_percentage')
                ->where('action_plan_id','=',$id)
                ->max('updated_at');

        //obtenemos porcentaje y comentarios
        return DB::table('progress_percentage')
                ->where('action_plan_id','=',$id)
                ->where('updated_at','=',$max_date)
                ->select('percentage','comments','updated_at')
                ->first();
    }

    public static function getActionPlanByIssueAndDescription($issue_id,$description)
    {
        return DB::table('action_plans')
                ->where('issue_id','=',$issue_id)
                ->where('description','=',$description)
                ->select('*')
                ->first();
    }
}
