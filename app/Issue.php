<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Issue extends Model
{
    protected $fillable = ['name','description','observations','recommendations','evidence','classification',
    						'audit_test_id','audit_audit_plan_id','control_evaluation_id'];


   	//obtiene datos del origen de un control
    public static function getOrigin($kind,$id,$org_id)
    {
        if ($kind == 0) //obtenemos nombre de proceso
        {
            $origin = DB::table('processes')
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('risk_subprocess.id','=',$id)
                        ->select('processes.name')
                        ->first();
        }
        else if ($kind == 1) //obtenemos nombre de subproceso
        {
            $origin = DB::table('subprocesses')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('risk_subprocess.id','=',$id)
                        ->select('subprocesses.name')
                        ->first();
        }
        else if ($kind == 2) //organización
        {
            $origin = \Ermtool\Organization::find($org_id);
        }
        else if ($kind == 3) //control de proceso
        {
            $origin = DB::table('controls')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                        ->where('control_risk_subprocess.id','=',$id)
                        ->select('controls.name')
                        ->first();
        }

        else if ($kind == 4) //control de entidad
        {
            $origin = DB::table('controls')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                        ->where('control_objective_risk.id','=',$id)
                        ->select('controls.name')
                        ->first();
        }
        else if ($kind == 5) //programa de auditoría
        {
            $origin = DB::table('audit_programs')
                        ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
                        ->where('audit_audit_plan_audit_program.id','=',$id)
                        ->select('audit_programs.name')
                        ->first();
        }
        else if ($kind == 6) //auditoría
        {
            $origin = DB::table('audits')
                        ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->where('audit_audit_plan.id','=',$id)
                        ->select(DB::raw('CONCAT(audit_plans.name, " - ", audits.name) AS name'))
                        ->first();
        }

        return $origin->name;
    }

    //obtiene issues y planes de acción de una prueba de auditoría
    public static function getIssueByTestId($id)
    {
    	$issues = DB::table('issues')
                        ->where('issues.audit_test_id','=',$id)
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification')
                        //->first();
                        ->get();
            $i = 0;

            if ($issues == NULL)
            {
                $results = NULL;
            }
            else
            {
                foreach ($issues as $issue)
                {
                	//para cada issue obtenemos plan de acción (si es que hay)
                	$plan = DB::table('action_plans')
	                    ->where('issue_id','=',$issue->id)
	                    ->select('description','final_date','status')
	                    ->first();

                    //obtenemos evidencias de issue (si es que existen)
                    $evidences = getEvidences(2,$issue->id);

                    if ($plan != NULL)
                    {
                    	$results[$i] = [
	                        'id' => $issue->id,
	                        'name' => $issue->name,
	                        'description' => $issue->description,
	                        'recommendations' => $issue->recommendations,
	                        'classification' => $issue->classification,
	                        'evidences' => $evidences,
	                        'plan_description' => $plan->description,
	                        'plan_final_date' => $plan->final_date,
	                        'plan_status' => $plan->status,
	                    ];
                    }
                    else
                    {
                    	$results[$i] = [
	                        'id' => $issue->id,
	                        'name' => $issue->name,
	                        'description' => $issue->description,
	                        'recommendations' => $issue->recommendations,
	                        'classification' => $issue->classification,
	                        'evidences' => $evidences,
	                        'plan_description' => NULL,
	                        'plan_final_date' => NULL,
	                        'plan_status' => NULL,
	                    ];
                    }

                    $i += 1;
                }
            }

        return $results;
    }
}
