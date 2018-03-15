<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Issue extends Model
{
    
    protected $fillable = ['name','description','recommendations','evidence','classification_id','audit_test_id','audit_audit_plan_id','control_evaluation_id','organization_id'];


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
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification')
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
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    //obtiene issues de subprocesos
    public static function getSubprocessIssuesBySubprocess($subprocess)
    {
        $issues1 = DB::table('issues')
                    ->where('subprocess_id','=',$subprocess)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->groupBy('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                    ->get();

        //hallazgos relacionados al subproceso a través de una evaluación de control
        $issues2 = DB::table('issues')
                    ->join('control_evaluation','control_evaluation.id','=','issues.control_evaluation_id')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                    ->where('risk_subprocess.subprocess_id','=',$subprocess)
                    ->groupBy('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        //hallazgos de auditoría basada en procesos
        $issues3 = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('processes','processes.id','=','audit_tests.process_id')
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->where('subprocesses.id','=',$subprocess)
                        ->groupBy('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                        ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
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
                        ->groupBy('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                        ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                        ->get();

        $issues = array_merge($issues1,$issues2,$issues3,$issues4);
        $issuesX = array_unique($issues,SORT_REGULAR);
        return $issuesX;
    }

    public static function getControlIssues($control,$org)
    {
        //hallazgos de controles creados directamente
        $issues1 = DB::table('issues')
                    ->where('issues.control_id','=',$control)
                    ->where('issues.organization_id','=',$org)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        //hallazgos de controles generados a través de evaluación de controles
        $issues2 = DB::table('control_evaluation')
                    ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                    ->where('control_evaluation.control_id','=',$control)
                    ->where('issues.organization_id','=',$org)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        $issues = array_merge($issues1,$issues2);
        $issues = array_unique($issues,SORT_REGULAR);

        return $issues;
    }

    public static function getAuditProgramIssues($audit_audit_plan_audit_program)
    {
        $issues = DB::table('issues')
                    ->where('audit_audit_plan_audit_program_id','=',$audit_audit_plan_audit_program)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    public static function getAuditIssues($audit_id)
    {
        $issues = DB::table('issues')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audits.id','=',$audit_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                    ->get();

        return $issues;
    }

    //obtiene issues y planes de acción de una evaluación de control
    public static function getIssueByControlEvaluation($id)
    {
        $results = array();

        $issues = DB::table('issues')
                    ->where('issues.control_evaluation_id','=',$id)
                    ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification')
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
        /*
        $issues1 = DB::table('issues')
                ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */

        //ACT 30-12-17: Hacemos consultas más simples, ya que ahora para todos los tipos se identificará organization_id
        //Hallazgos asociados a plan de auditoría
        $issues1 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('issues.audit_audit_plan_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);
        foreach ($issues1 as $i)
        {
            $i->kind = 1;
        }
        /*
        //issues de programa
        $issues2 = DB::table('issues')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','issues.audit_audit_plan_audit_program_id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        //Hallazgos asociados a programa de auditoría
        $issues2 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('issues.audit_audit_plan_audit_program_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues2 as $i)
        {
            $i->kind = 2;
        }
        /*
        //issues de ejecución de pruebas
        $issues3 = DB::table('issues')
                ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_plans.organization_id','=',$org)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        //Hallazgos asociados a prueba de auditoría
        $issues3 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('issues.audit_test_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues3 as $i)
        {
            $i->kind = 3;
        }

        //de organización directamente
        //ACT 30-12-17: Tienen que estar todos los demás atributos vacíos
        //Hallazgos asociados a organización
        $issues4 = DB::table('issues')
                    ->where('organization_id','=',$org)
                    ->whereNull('issues.audit_audit_plan_id')
                    ->whereNull('issues.audit_audit_plan_audit_program_id')
                    ->whereNull('issues.audit_test_id')
                    ->whereNull('issues.subprocess_id')
                    ->whereNull('issues.process_id')
                    ->whereNull('issues.control_id')
                    ->whereNull('issues.control_evaluation_id')
                    ->whereNull('issues.objective_id')
                    ->whereNull('issues.subprocess_id')
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues4 as $i)
        {
            $i->kind = 4;
        }
        /* 
        $issues5 = DB::table('issues')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','issues.subprocess_id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        //Hallazgos asociados a subproceso
        $issues5 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('subprocess_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues5 as $i)
        {
            $i->kind = 5;
        }
        /*
        //Hallazgos asociados a proceso
        $issues6 = DB::table('issues')
                    ->join('subprocesses','subprocesses.process_id','=','issues.process_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        $issues6 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('issues.process_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues6 as $i)
        {
            $i->kind = 6;
        }
        /*
        $issues7 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        //hallazgo de control de proceso
        $issues7 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                    ->where('issues.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues7 as $i)
        {
            $i->kind = 7;
        }
        /*
        $issues8 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification','issues.updated_at']);
        */
        //hallazgo de control de entidad
        $issues8 = DB::table('issues')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                    ->where('issues.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues8 as $i)
        {
            $i->kind = 8;
        }

        //Hallazgos de evaluación de control
        $issues9 = DB::table('issues')
                ->where('issues.organization_id','=',$org)
                ->whereNotNull('issues.control_evaluation_id')
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues9 as $i)
        {
            $i->kind = 9;
        }

        //hallazgos asociados a riesgos
        $issues10 = DB::table('issues')
                    ->whereIn('issues.id',function($query) {
                        $query->select('issue_id')->from('issue_organization_risk');
                    })
                    ->where('issues.kind','=',3)
                    ->where('issues.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues10 as $i)
        {
            $i->kind = 10;
        }

        //hallazgos de compliance
        $issues11 = DB::table('issues')
                    ->where('issues.kind','=',1)
                    ->where('issues.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues11 as $i)
        {
            $i->kind = 11;
        }

        //hallazgos de canal de denuncia
        $issues12 = DB::table('issues')
                    ->where('issues.kind','=',2)
                    ->where('issues.organization_id','=',$org)
                    ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification','issues.updated_at','issues.kind']);

        foreach ($issues12 as $i)
        {
            $i->kind = 12;
        }

        $issues = array_merge($issues1,$issues2,$issues3,$issues4,$issues5,$issues6,$issues7,$issues8,$issues9,$issues10,$issues11,$issues12);
        $issuesX = array_unique($issues,SORT_REGULAR);
        return $issuesX;
    }

    public static function getTestIssues($audit_test_id)
    {
        return DB::table('issues')
                ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                ->where('audit_tests.id','=',$audit_test_id)
                ->get(['issues.id','issues.name','issues.description','issues.recommendations','issues.classification_id as classification']);
    }

    public static function getRiskIssues($org_id)
    {
        //ACT 06-03-18: Ahora la relación entre riesgos e issues son muchos a muchos
        /*$issues = DB::table('issues')
                    ->join('organization_risk','organization_risk.id','=','issues.organization_risk_id')
                    ->where('issues.kind','=',3)
                    ->where('organization_risk.risk_id','=',$risk_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                    ->get();*/
        
        //ACT 09-03-18: Obtenemos los issues directamente de organización (para que no se repitan en caso de que un issue tenga muchos riesgos asociados)
        $issues = DB::table('issues')
                ->join('issue_organization_risk','issue_organization_risk.issue_id','=','issues.id')
                ->join('organization_risk','organization_risk.id','=','issue_organization_risk.organization_risk_id')
                ->where('issues.kind','=',3)
                //->where('issue_organization_risk.organization_risk_id','=',$org_risk_id)
                ->where('organization_risk.organization_id','=',(int)$org_id)
                ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
                ->groupBy('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                ->get();

        return $issues;
    }

    public static function getComplianceIssues($org_id)
    {
        $issues = DB::table('issues')
                    ->join('organizations','organizations.id','=','issues.organization_id')
                    ->where('issues.kind','=',1)
                    ->where('organizations.id','=',$org_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations','organizations.name as organization')
                    ->get();

        return $issues;
    }

    public static function getCompliantChanellIssues($org_id)
    {
        $issues = DB::table('issues')
                    ->join('organizations','organizations.id','=','issues.organization_id')
                    ->where('issues.kind','=',2)
                    ->where('organizations.id','=',$org_id)
                    ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations','organizations.name as organization')
                    ->get();

        return $issues;
    }

    public static function getIssuesFromControl($org,$ctrl)
    {
        return DB::table('issues')
            ->where('organization_id','=',$org)
            ->where('control_id','=',$ctrl)
            ->select('issues.id','issues.name','issues.description','issues.classification_id as classification','issues.recommendations')
            ->get();
    }
}
