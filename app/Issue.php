<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Issue extends Model
{
    
    protected $fillable = ['name','description','recommendations','evidence','classification','audit_test_id','audit_audit_plan_id','control_evaluation_id','organization_id'];


   	//obtiene datos del origen de un control
    public static function getOrigin($kind,$id,$org_id)
    {
        if ($kind == 0) //obtenemos nombre de proceso
        {
            $origin = DB::table('processes')
                        ->where('id','=',$id)
                        ->select('processes.name')
                        ->first();
        }
        else if ($kind == 1) //obtenemos nombre de subproceso
        {
            $origin = DB::table('subprocesses')
                        ->where('id','=',$id)
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
                        ->where('id','=',$id)
                        ->select('controls.name')
                        ->first();
        }

        else if ($kind == 4) //control de entidad
        {
            $origin = DB::table('controls')
                        ->where('id','=',$id)
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
                        ->where('id','=',$id)
                        ->select('audits.name')
                        ->first();
        }

        else if ($kind == 7) //prueba auditoría
        {
            $origin = DB::table('audit_tests')
                        ->where('id','=',$id)
                        ->select('audit_tests.name')
                        ->first();
        }

        if (!empty($origin))
        {
            return $origin->name;
        }
        else
        {
            return NULL;
        }
    }

    //obtiene issues y planes de acción de una prueba de auditoría
    public static function getIssueByTestId($id)
    {
        $results = array();

    	$issues = DB::table('issues')
                        ->where('issues.audit_test_id','=',$id)
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification')
                        //->first();
                        ->get();
            $i = 0;

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

        return $results;
    }

    //obtiene issues de procesos
    public static function getProcessIssues($process)
    {
        $issues = DB::table('issues')
                    ->where('process_id','=',$process)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    //obtiene issues de subprocesos
    public static function getSubprocessIssuesBySubprocess($subprocess)
    {
        $issues1 = DB::table('issues')
                    ->where('subprocess_id','=',$subprocess)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->groupBy('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        //hallazgos relacionados al subproceso a través de una evaluación de control
        $issues2 = DB::table('issues')
                    ->join('control_evaluation','control_evaluation.id','=','issues.control_evaluation_id')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                    ->where('risk_subprocess.subprocess_id','=',$subprocess)
                    ->groupBy('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        //hallazgos de auditoría basada en procesos
        $issues3 = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('processes','processes.id','=','audit_tests.process_id')
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->where('subprocesses.id','=',$subprocess)
                        ->groupBy('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                        ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                        ->get();

        /* ACT. 14-12-16: YA NO EXISTEN LAS PRUEBAS DE RIESGOS
        //hallazgos de auditoría basada en riesgos
        $issues4 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        ->join('risk_subprocess','risk_subprocess.risk_id','=','audit_tests.risk_id')
                        ->where('risk_subprocess.subprocess_id','=',$subprocess)
                        ->groupBy('issues.id')
                        ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                        ->get();
        */
        //hallazgos de auditoría con pruebas de controles (controles orientados a subproceso)
        $issues4 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                        ->join('control_organization_risk','control_organization_risk.control_id','=','audit_test_control.control_id')
                        ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                        ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                        ->where('risk_subprocess.subprocess_id','=',$subprocess)
                        ->groupBy('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                        ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                        ->get();

        $issues = array_merge($issues1,$issues2,$issues3,$issues4);
        $issuesX = array_unique($issues,SORT_REGULAR);
        return $issuesX;
    }

    public static function getControlIssues($control)
    {
        //hallazgos de controles creados directamente
        $issues1 = DB::table('issues')
                    ->where('issues.control_id','=',$control)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        //hallazgos de controles generados a través de evaluación de controles
        $issues2 = DB::table('control_evaluation')
                    ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                    ->where('control_evaluation.control_id','=',$control)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        $issues = array_merge($issues1,$issues2);
        $issues = array_unique($issues,SORT_REGULAR);

        return $issues;
    }

    public static function getAuditProgramIssues($audit_audit_plan_audit_program)
    {
        $issues = DB::table('issues')
                    ->where('audit_audit_plan_audit_program_id','=',$audit_audit_plan_audit_program)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    public static function getAuditIssues($audit_id)
    {
        $issues = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audits.id','=',$audit_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    //obtiene issues y planes de acción de una evaluación de control
    public static function getIssueByControlEvaluation($id)
    {
        $results = array();

        $issues = DB::table('issues')
                    ->where('issues.control_evaluation_id','=',$id)
                    ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification')
                    ->get();
            $i = 0;

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

        return $results;
    }

    public static function getIssues($org)
    {
        //ACTUALIZACIÓN 03-02-2017: Distintos tipos serán enviados para reporte de gráficos
        //issues de organización a través de audit_audit_plan_id
        $issues1 = DB::table('issues')
                ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues1 as $i)
        {
            $i->kind = 1;
        }

        //issues de programa
        $issues2 = DB::table('issues')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','issues.audit_audit_plan_audit_program_id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues2 as $i)
        {
            $i->kind = 2;
        }

        //issues de ejecución de pruebas
        $issues3 = DB::table('issues')
                ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues3 as $i)
        {
            $i->kind = 3;
        }

        //de organización directamente
        $issues4 = DB::table('issues')
                    ->where('organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues4 as $i)
        {
            $i->kind = 4;
        }

        //de subproceso
        $issues5 = DB::table('issues')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','issues.subprocess_id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues5 as $i)
        {
            $i->kind = 5;
        }

        //de proceso
        $issues6 = DB::table('issues')
                    ->join('subprocesses','subprocesses.process_id','=','issues.process_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues6 as $i)
        {
            $i->kind = 6;
        }

        //hallazgo de control de proceso
        $issues7 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues7 as $i)
        {
            $i->kind = 7;
        }

        //hallazgo de control de entidad
        $issues8 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues8 as $i)
        {
            $i->kind = 8;
        }

        $issues9 = array(); //ya no es necesario ya que en issues8 están todas las issues de controles

        //de control_evaluation (de controles de negocio)
        $issues10 = DB::table('issues')
                    ->join('control_evaluation','control_evaluation.id','=','issues.control_evaluation_id')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);

        foreach ($issues10 as $i)
        {
            $i->kind = 9;
        }

        $issues = array_merge($issues1,$issues2,$issues3,$issues4,$issues5,$issues6,$issues7,$issues8,$issues9,$issues10);
        $issuesX = array_unique($issues,SORT_REGULAR);
        return $issuesX;
    }

    public static function getTestIssues($audit_test_id)
    {
        return DB::table('issues')
                ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                ->where('audit_tests.id','=',$audit_test_id)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification']);
    }

    public static function getRiskIssues($risk_id)
    {
        $issues = DB::table('issues')
                    ->join('organization_risk','organization_risk.id','=','issues.organization_risk_id')
                    ->where('issues.kind','=',3)
                    ->where('organization_risk.risk_id','=',$risk_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    public static function getComplianceIssues($org_id)
    {
        $issues = DB::table('issues')
                    ->join('organizations','organizations.id','=','issues.organization_id')
                    ->where('issues.kind','=',1)
                    ->where('organizations.organization_id','=',$org_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations','organizations.name as organization')
                    ->get();

        return $issues;
    }

    public static function getCompliantChanellIssues($org_id)
    {
        $issues = DB::table('issues')
                    ->join('organizations','organizations.id','=','issues.organization_id')
                    ->where('issues.kind','=',2)
                    ->where('organizations.organization_id','=',$org_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations','organizations.name as organization')
                    ->get();

        return $issues;
    }
}
