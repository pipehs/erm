<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Redirect;
use Session;
use dateTime;
use Storage;
use stdClass;
use Ermtool\Http\Controllers\PlanesAccionController as PlanesAccion;

class IssuesController extends Controller
{
    //obtiene hallazgos de tipo $kind (proceso u organización) para la org $org_id. Esto para mantenedor de hallazgos y reporte de hallazgos
    public function getIssues($kind,$org_id,$kind2)
    {
        $issues = array();
        $datos = array(); //se usará sólo para reportes

        if ($kind == 0) //Hallazgo de proceso
        {
            //primero seleccionamos los hallazgos obtenidos a través de la evaluación de controles
            $issues1 = DB::table('control_evaluation')
                        ->join('controls','controls.id','=','control_evaluation.control_id')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->join('issues','issues.id','=','control_evaluation.issue_id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->where('organization_subprocess.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                 'issues.recommendations','risk_subprocess.id as subobj_id')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //ahora los hallazgos generados a través de auditoría orientada a procesos
            $issues2 = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        //->join('issues','issues.audit_test_id','=','audit_tests.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->join('subprocesses','subprocesses.id','=','audit_tests.subprocess_id')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org_id)
                        ->distinct()
                        ->groupBy('issues.id')
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations','risk_subprocess.id as subobj_id')
                        ->get();

            //hallazgos de auditoría orientados a riesgos (de proceso)
            $issues3 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->join('risk_subprocess','risk_subprocess.risk_id','=','audit_tests.risk_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations','risk_subprocess.id as subobj_id')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //hallazgos de auditoría con pruebas de controles (controles orientados a subproceso)
            $issues4 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','audit_tests.control_id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations','risk_subprocess.id as subobj_id')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //hallazgos de proceso creados directamente
            $issues5 = DB::table('issues')
                        ->join('processes','processes.id','=','issues.process_id')
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->where('organization_subprocess.organization_id','=',$org_id)
                        ->whereNotNull('issues.process_id')
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations','risk_subprocess.id as subobj_id')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();
        }

        else if ($kind == 2) //Hallazgos de organización
        {
            //seleccionamos los hallazgos obtenidos a través de la evaluación de controles (que sean para controles de riesgos de negocio)
            $issues1 = DB::table('control_evaluation')
                        ->join('controls','controls.id','=','control_evaluation.control_id')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->join('issues','issues.id','=','control_evaluation.issue_id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->where('objectives.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //hallazgos de auditoría orientados a riesgos (de negocio)
            $issues2 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->join('objective_risk','objective_risk.risk_id','=','audit_tests.risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //hallazgos de auditoría con pruebas de controles (controles orientados a objetivos)
            $issues3 = DB::table('audit_tests')
                        ->join('issues','issues.audit_test_id','=','audit_tests.id')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        //->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','audit_tests.control_id')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org_id)
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();

            //hallazgos de organización creados directamente
            $issues4 = DB::table('issues')
                        //->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->where('issues.organization_id','=',$org_id)
                        ->whereNotNull('issues.organization_id')
                        ->select('issues.id','issues.name as issue_name','issues.classification',
                                     'issues.recommendations')
                        ->distinct()
                        ->groupBy('issues.id')
                        ->get();
        }

        $i = 0;
        //  dd($issues1);
        foreach ($issues1 as $issue)
        {
            //para cada issue obtenemos datos de plan de acción (si es que hay)
            $plan = DB::table('action_plans')
                    ->where('issue_id','=',$issue->id)
                    ->select('description','final_date','status')
                    ->first();

            if ($plan != NULL)
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,$plan->description,$plan->status,$plan->final_date);  
            }
            else
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,NULL,NULL,NULL);
            }       

            if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán los riesgos
            {
                $datos = $this->datosReporte($issue->subobj_id,$kind);
            }

            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
            {
                if ($kind == 0)
                {
                    $issues[$i] = [
                        'Procesos' => $datos['processes'],
                        'Subprocesos' => $datos['subprocesses'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
                else if ($kind == 2)
                {
                    $issues[$i] = [
                        'Objetivos' => $datos['objectives'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
            }
            else
            {
                //obtenemos posibles evidencias
                $evidence = getEvidences(2,$temp['id']);

                $issues[$i] = [
                    'id' => $temp['id'],
                    'name' => $temp['name'],
                    'classification' => $temp['classification'],
                    'recommendations' => $temp['recommendations'],
                    'plan' => $temp['plan'],
                    'status' => $temp['status'],
                    'status_origin' => $temp['status_origin'],
                    'final_date' => $temp['final_date'],
                    'datos' => $datos,
                    'evidence' => $evidence
                ];
            }
        
            $i += 1;
        }

        foreach ($issues2 as $issue)
        {
            $plan = NULL;
            //para cada issue obtenemos datos de plan de acción (si es que hay)
            $plan = DB::table('action_plans')
                    ->where('issue_id','=',$issue->id)
                    ->select('description','final_date','status')
                    ->get();

            if ($plan != NULL)
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,$plan[0]->description,$plan[0]->status,$plan[0]->final_date);  
            }
            else
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,NULL,NULL,NULL);
            }      

            if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán los riesgos
            {
                $datos = $this->datosReporte($issue->subobj_id,$kind);
            }

            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
            {
                if ($kind == 0)
                {
                    $issues[$i] = [
                        'Procesos' => $datos['processes'],
                        'Subprocesos' => $datos['subprocesses'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
                else if ($kind == 2)
                {
                    $issues[$i] = [
                        'Objetivos' => $datos['objectives'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
            }
            else
            {
                //obtenemos posibles evidencias
                $evidence = getEvidences(2,$temp['id']);
                $issues[$i] = [
                    'id' => $temp['id'],
                    'name' => $temp['name'],
                    'classification' => $temp['classification'],
                    'recommendations' => $temp['recommendations'],
                    'plan' => $temp['plan'],
                    'status' => $temp['status'],
                    'status_origin' => $temp['status_origin'],
                    'final_date' => $temp['final_date'],
                    'datos' => $datos,
                    'evidence' => $evidence
                ];
            }

            $i += 1;
        }

        foreach ($issues3 as $issue)
        {
            $plan = NULL;
            //para cada issue obtenemos datos de plan de acción (si es que hay)
            $plan = DB::table('action_plans')
                    ->where('issue_id','=',$issue->id)
                    ->select('description','final_date','status')
                    ->get();

            if ($plan != NULL)
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,$plan[0]->description,$plan[0]->status,$plan[0]->final_date);  
            }
            else
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,NULL,NULL,NULL);
            }     

            if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán los riesgos
            {
                $datos = $this->datosReporte($issue->subobj_id,$kind);
            }

            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
            {
                if ($kind == 0)
                {
                    $issues[$i] = [
                        'Procesos' => $datos['processes'],
                        'Subprocesos' => $datos['subprocesses'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
                else if ($kind == 2)
                {
                    $issues[$i] = [
                        'Objetivos' => $datos['objectives'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
            }
            else
            {
                //obtenemos posibles evidencias
                $evidence = getEvidences(2,$temp['id']);
                $issues[$i] = [
                    'id' => $temp['id'],
                    'name' => $temp['name'],
                    'classification' => $temp['classification'],
                    'recommendations' => $temp['recommendations'],
                    'plan' => $temp['plan'],
                    'status' => $temp['status'],
                    'status_origin' => $temp['status_origin'],
                    'final_date' => $temp['final_date'],
                    'datos' => $datos,
                    'evidence' => $evidence
                ];
            }

            $i += 1;
        }

        foreach ($issues4 as $issue)
        {
            $plan = NULL;
            //para cada issue obtenemos datos de plan de acción (si es que hay)
            $plan = DB::table('action_plans')
                    ->where('issue_id','=',$issue->id)
                    ->select('description','final_date','status')
                    ->get();

            if ($plan != NULL)
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,$plan[0]->description,$plan[0]->status,$plan[0]->final_date);  
            }
            else
            {
                $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,NULL,NULL,NULL);
            }     

            if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán los riesgos
            {
                $datos = $this->datosReporte($issue->subobj_id,$kind);
            }            

            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
            {
                if ($kind == 0)
                {
                    $issues[$i] = [
                        'Procesos' => $datos['processes'],
                        'Subprocesos' => $datos['subprocesses'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
                else if ($kind == 2)
                {
                    $issues[$i] = [
                        'Objetivos' => $datos['objectives'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
            }
            else
            {
                //obtenemos posibles evidencias
                $evidence = getEvidences(2,$temp['id']);
                $issues[$i] = [
                    'id' => $temp['id'],
                    'name' => $temp['name'],
                    'classification' => $temp['classification'],
                    'recommendations' => $temp['recommendations'],
                    'plan' => $temp['plan'],
                    'status' => $temp['status'],
                    'status_origin' => $temp['status_origin'],
                    'final_date' => $temp['final_date'],
                    'datos' => $datos,
                    'evidence' => $evidence
                ];
            }

            $i += 1;
        }

        if ($kind == 0)
        {
            foreach ($issues5 as $issue)
            {
                $plan = NULL;
                //para cada issue obtenemos datos de plan de acción (si es que hay)
                $plan = DB::table('action_plans')
                        ->where('issue_id','=',$issue->id)
                        ->select('description','final_date','status')
                        ->get();

                if ($plan != NULL)
                {
                    $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,$plan[0]->description,$plan[0]->status,$plan[0]->final_date);  
                }
                else
                {
                    $temp = $this->formatearIssue($issue->id,$issue->issue_name,$issue->classification,$issue->recommendations,NULL,NULL,NULL);
                }               

                if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán los riesgos
                {
                    $datos = $this->datosReporte($issue->subobj_id,$kind);
                }

                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
                {
                    $issues[$i] = [
                        'Procesos' => $datos['processes'],
                        'Subprocesos' => $datos['subprocesses'],
                        'Riesgos' => $datos['risks'],
                        'Controles' => $datos['controls'],
                        'Nombre' => $temp['name'],
                        'Clasificación' => $temp['classification'],
                        'Recomendaciones' => $temp['recommendations'],
                        'Plan de acción' => $temp['plan'],
                        'Estado' => $temp['status'],
                        'Fecha límite plan' => $temp['final_date']
                    ];
                }
                else
                {
                    //obtenemos posibles evidencias
                    $evidence = getEvidences(2,$temp['id']);
                    $issues[$i] = [
                        'id' => $temp['id'],
                        'name' => $temp['name'],
                        'classification' => $temp['classification'],
                        'recommendations' => $temp['recommendations'],
                        'plan' => $temp['plan'],
                        'status' => $temp['status'],
                        'status_origin' => $temp['status_origin'],
                        'final_date' => $temp['final_date'],
                        'datos' => $datos,
                        'evidence' => $evidence
                    ];
                }

                $i += 1;


            }
        }

        return $issues;
    }

    //datos extras para reporte de auditoría
    public function datosReporte($risk_subobj_id,$kind)
    {
        $datos = array();
        $risks = "";
        $controls = "";

        if ($kind == 0)
        {
            $subprocesses = "";
            $processes = "";

            $subprocesses1 = DB::table('subprocesses')
                    ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('risk_subprocess.id','=',$risk_subobj_id)
                    ->select('subprocesses.name')
                    ->get();

            $last = end($subprocesses1); //guardamos final para no agregarle coma
            foreach ($subprocesses1 as $sub)
            {
                if ($sub != $last)
                {
                    $subprocesses .= $sub->name.', ';
                }
                else
                    $subprocesses .= $sub->name;
            }

            $risks1 = DB::table('risks')
                        ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                        ->where('risk_subprocess.id','=',$risk_subobj_id)
                        ->select('risks.name')
                        ->get();

            if ($risks1)
            {
                $last = end($risks1); //guardamos final para no agregarle coma
                foreach ($risks1 as $risk)
                {
                    if ($risk != $last)
                    {
                        $risks .= $risk->name.', ';
                    }
                    else
                        $risks .= $risk->name;
                }
            }
            else
            {
                $risks = "No se han identificado riesgos";
            }

            $processes1 = DB::table('processes')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('risk_subprocess.id','=',$risk_subobj_id)
                            ->select('processes.name')
                            ->distinct('processes.id')
                            ->get();

            $last = end($processes1); //guardamos final para no agregarle coma
            foreach ($processes1 as $process)
            {
                if ($process != $last)
                {
                    $processes .= $process->name.', ';
                }
                else
                    $processes .= $process->name;
            }

            $controls1 = DB::table('controls')
                            ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                            ->where('control_risk_subprocess.risk_subprocess_id','=',$risk_subobj_id)
                            ->select('controls.name')
                            ->get();

            if ($controls1)
            {
                $last = end($controls1); //guardamos final para no agregarle coma
                foreach ($controls1 as $control)
                {
                    if ($control != $last)
                    {
                        $controls .= $control->name.', ';
                    }
                    else
                        $controls .= $control->name;
                }
            }
            else
            {
                $controls = "No se han definido controles";
            }
            

            $datos = [
                'risks' => $risks,
                'processes' => $processes,
                'subprocesses' => $subprocesses,
                'controls' => $controls,
                ];
        }
        else if ($kind == 2)
        {
            $objectives = "";

            $objectives1 = DB::table('objectives')
                            ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                            ->where('objective_risk.id','=',$risk_subobj_id)
                            ->select('objectives.name')
                            ->get();

            $last = end($objectives1); //guardamos final para no agregarle coma
            foreach ($objectives1 as $obj)
            {
                if ($obj != $last)
                {
                    $objectives .= $obj->name.', ';
                }
                else
                    $objectives .= $obj->name;
            }

            $risks1 = DB::table('risks')
                        ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                        ->where('objective_risk.id','=',$risk_subobj_id)
                        ->select('risks.name')
                        ->get();

            if ($risks1)
            {
                $last = end($risks1); //guardamos final para no agregarle coma
                foreach ($risks1 as $risk)
                {
                    if ($risk != $last)
                    {
                        $risks .= $risk->name.', ';
                    }
                    else
                        $risks .= $risk->name;
                }
            }
            else
            {
                $risks = "No se han identificado riesgos";
            }

            $controls1 = DB::table('controls')
                            ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                            ->where('control_objective_risk.objective_risk_id','=',$risk_subobj_id)
                            ->select('controls.name')
                            ->get();

            if ($controls1)
            {
                $last = end($controls1); //guardamos final para no agregarle coma
                foreach ($controls1 as $control)
                {
                    if ($control != $last)
                    {
                        $controls .= $control->name.', ';
                    }
                    else
                        $controls .= $control->name;
                }
            }
            else
            {
                $controls = "No se han definido controles";
            }
            

            $datos = [
                'risks' => $risks,
                'objectives' => $objectives,
                'controls' => $controls,
                ];
        }

        return $datos;
    }

    public function formatearIssue($id,$name,$classification,$recommendations,$plan,$status,$date)
    {
            switch ($classification) 
            {
                case 0:
                    $class = 'Oportunidad de mejora';
                    break;
                case 1:
                    $class = 'Deficiencia';
                    break;
                case 2:
                    $class = 'Debilidad significativa';
                    break;
                default:
                    $class = 'Aun no se ha clasificado';
                    break;
            }

            if ($recommendations == "" || $recommendations == NULL)
            {
                $rec = 'No se han agregado recomendaciones';
            }
            else
            {
                $rec = $recommendations;
            }

            if ($plan == "" || $plan == NULL)
            {
                $desc = 'No se ha agregado descripción para el plan de acción (o aun no se agrega plan)';
            }
            else
            {
                $desc = $plan;
            }

            if ($date == '0000-00-00' || $date == NULL)
            {
                $final_date = 'Error al registrar fecha final para el plan de acción (o aun no se ha agregado plan)';
            }
            else
            {
                $temp = new DateTime($date);
                $final_date = date_format($temp, 'd-m-Y');
            }

            if ($status === NULL)
            {
                $status2 = 'Error al registrar el estado o aun no se ha agregado plan';
            }
            else
            {
                if ($status == 0)
                {
                    $status2 = 'En progreso';
                }
                else if ($status == 1)
                {
                    $status2 = 'Cerrado';
                }
            } 

            $issue = [
                'id' => $id,
                'name' => $name,
                'classification' => $class,
                'recommendations' => $rec,
                'plan' => $desc,
                'status' => $status2,
                'status_origin' => $status,
                'final_date' => $final_date,
            ];

            return $issue;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //obtenemos lista de organizaciones
        $organizations = \Ermtool\Organization::lists('name','id');

        return view('hallazgos.index',['organizations'=>$organizations]);
    }

    public function index2()
    {
        //volvemos a obtener lista de organizaciones
        $organizations = \Ermtool\Organization::lists('name','id');

        //obtenemos nombre de organización
        $org = \Ermtool\Organization::where('id',$_POST['organization_id'])->value('name');

        $org_id = $_POST['organization_id'];
        
        $issues = array();

        $issues = $this->getIssues($_POST['kind'],$_POST['organization_id'],1);
        //print_r($_POST);
        
        return view('hallazgos.index',['issues'=>$issues,'kind'=>$_POST['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //echo $_GET['org'];
        //echo $_GET['kind'];
        $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');

        //obtenemos stakeholders de la misma organización
        $stakes = DB::table('stakeholders')
                    ->join('organization_stakeholder','organization_stakeholder.stakeholder_id','=','stakeholders.id')
                    ->where('organization_stakeholder.organization_id','=',$_GET['org'])
                    ->select('stakeholders.id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
                    ->orderBy('name')
                    ->lists('full_name', 'id');

        if ($_GET['kind'] == 0) //obtenemos procesos
        {
            $processes = \Ermtool\Process::where('processes.status',0)
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$_GET['org'])
                        ->lists('processes.name','processes.id');

            return view('hallazgos.create',['org'=>$org, 'processes' => $processes,'kind' => $_GET['kind'],'stakeholders'=>$stakes]);
        }
        else if ($_GET['kind'] == 2) //mandamos id de org
        {
            return view('hallazgos.create',['org'=>$org, 'kind' => $_GET['kind'], 'org_id'=>$_GET['org'],'stakeholders'=>$stakes]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //print_r($_POST);
        global $req;
        $req = $request;
        DB::transaction(function() {

            //verificamos ingreso de datos
            if (isset($_POST['description']))
            {
                $description = $_POST['description'];
            }
            else
            {
                $description = NULL;
            }

            if (isset($_POST['recommendations']))
            {
                $recommendations = $_POST['recommendations'];
            }
            else
            {
                $recommendations = NULL;
            }
            if (isset($_POST['classification']))
            {
                $classification = $_POST['classification'];
            }
            else
            {
                $classification = NULL;
            }
            
            if ($_POST['kind'] == 0) //es un hallazgo de proceso
            {
                $issue = DB::table('issues')
                    ->insertGetId([
                            'name' => $_POST['name'],
                            'description' => $description,
                            'recommendations' => $recommendations,
                            'classification' => $classification,
                            'process_id' => $_POST['process_id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
            }
            else if ($_POST['kind'] == 2) //hallazgo de organización
            {
                $issue = DB::table('issues')
                    ->insertGetId([
                            'name' => $_POST['name'],
                            'description' => $description,
                            'recommendations' => $recommendations,
                            'classification' => $classification,
                            'organization_id' => $_POST['org_id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
            }

            //agregamos evidencia (si es que existe)
            if ($GLOBALS['req']->file('evidence_doc') != NULL)
            {
                upload_file($GLOBALS['req']->file('evidence_doc'),'evidencias_hallazgos',$issue);    
            }


            //vemos si tiene al menos la descripción del plan de acción, si es así se agrega el plan
            if (isset($_POST['description_plan']) AND $_POST['description_plan'] != "")
            {
                if (isset($_POST['stakeholder_id']) AND $_POST['stakeholder_id'] != "")
                {
                    $stakeholder = $_POST['stakeholder_id'];
                }
                else
                {
                    $stakeholder = NULL;
                }

                if (isset($_POST['final_date']) AND $_POST['final_date'] != "")
                {
                    $final_date = $_POST['final_date'];
                }
                else
                {
                    $final_date = NULL;
                }

                $plan = new PlanesAccion;

                $newplan = $plan->store($issue,$_POST['description_plan'],$stakeholder,$final_date);

            }

            if (isset($newplan))
            {
                Session::flash('message','Hallazgo y plan de acción creado correctamente');
            }
            else
            {   
                Session::flash('message','Hallazgo creado correctamente');
            }
        });

        return Redirect::to('hallazgos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //echo $_GET['org'];
        //echo $_GET['id'];
        
        $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');

        //obtenemos stakeholders de la misma organización
        $stakes = DB::table('stakeholders')
                    ->join('organization_stakeholder','organization_stakeholder.stakeholder_id','=','stakeholders.id')
                    ->where('organization_stakeholder.organization_id','=',$_GET['org'])
                    ->select('stakeholders.id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
                    ->orderBy('name')
                    ->lists('full_name', 'id');

        $issue = \Ermtool\Issue::find($_GET['id']);

        //vemos si es que tiene plan de accion
        $action_plan = NULL;

        $action_plan = DB::table('action_plans')
                        ->where('issue_id','=',$_GET['id'])
                        ->select('id','stakeholder_id','description','final_date','status')
                        ->first();


        //vemos si es hallazgo de proceso, organización, u otro
        if ($issue->process_id != NULL)
        {
            $processes = \Ermtool\Process::where('processes.status',0)
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$_GET['org'])
                        ->lists('processes.name','processes.id');

            $process_selected = $issue->process_id;

            return view('hallazgos.edit',['org'=>$org, 'issue' => $issue,'stakeholders'=>$stakes,'processes'=>$processes,
                                          'process_selected' => $process_selected,'action_plan'=>$action_plan]);
        }
        else if ($issue->organization_id != NULL)
        {
            return view('hallazgos.edit',['org'=>$org, 'issue' => $issue,'stakeholders'=>$stakes,'org_id'=>$_GET['org'],
                                          'action_plan'=>$action_plan]);
        }
        else
        {
            return view('hallazgos.edit',['org'=>$org, 'issue' => $issue,'stakeholders'=>$stakes,'action_plan'=>$action_plan]);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //print_r($_POST);
        //actualizamos issue de id = $id
        global $id2;
        $id2 = $id;
        DB::transaction(function() {

            //vemos si el plan se mandó cerrado o abierto y damos formato a campos de plan de acción
            if (isset($_POST['status']))
            {
                $status = 1;

                if ($_POST['description_plan2'] != "")
                {
                    $description_plan = $_POST['description_plan2'];
                }
                else
                    $description_plan = NULL;

                if ($_POST['stakeholder_id2'] != "")
                {
                    $stakeholder_id = $_POST['stakeholder_id2'];
                }
                else
                    $stakeholder_id = NULL;

                if ($_POST['final_date2'] != "")
                {
                    $final_date = $_POST['final_date2'];
                }
                else
                {
                    $final_date = NULL;
                }
            }
            else
            {
                $status = 0;

                if ($_POST['description_plan'] != "")
                {
                    $description_plan = $_POST['description_plan'];
                }
                else
                    $description_plan = NULL;

                if ($_POST['stakeholder_id'] != "")
                {
                    $stakeholder_id = $_POST['stakeholder_id'];
                }
                else
                    $stakeholder_id = NULL;

                if ($_POST['final_date'] != "")
                {
                    $final_date = $_POST['final_date'];
                }
                else
                {
                    $final_date = NULL;
                }
            }

            //verificamos ingreso de datos
            if (isset($_POST['description']) AND $_POST['description'] != "")
            {
                $description = $_POST['description'];
            }
            else
            {
                $description = NULL;
            }

            if (isset($_POST['recommendations']) AND $_POST['recommendations'] != "")
            {
                $recommendations = $_POST['recommendations'];
            }
            else
            {
                $recommendations = NULL;
            }
            if (isset($_POST['classification']) AND $_POST['classification'] != "")
            {
                $classification = $_POST['classification'];
            }
            else
            {
                $classification = NULL;
            }

            DB::table('issues')->where('id','=',$GLOBALS['id2'])
                ->update([
                    'name' => $_POST['name'],
                    'description' => $description,
                    'recommendations' => $recommendations,
                    'classification' => $classification,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                

            //actualizamos action_plan de issue_id = $id
            DB::table('action_plans')->where('issue_id','=',$GLOBALS['id2'])
                ->update([
                    'description' => $description_plan,
                    'stakeholder_id' => $stakeholder_id,
                    'final_date' => $final_date,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            Session::flash('message','Hallazgo y plan de acción actualizado correctamente');    
        });

        return Redirect::to('hallazgos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        global $id1;
        $id1 = $id;
        
        DB::transaction(function() {

            //primero que todo, eliminamos plan de acción (si es que hay)
            DB::table('action_plans')
            ->where('issue_id','=',$GLOBALS['id1'])
            ->delete();

            //eliminamos si es que parte de una evaluación de control
            DB::table('control_evaluation')
            ->where('issue_id','=',$GLOBALS['id1'])
            ->delete();

            //ahora eliminamos issue
            $res = DB::table('issues')
            ->where('id','=',$GLOBALS['id1'])
            ->delete();

        });
    }

     //función para reporte de hallazgos
    public function issuesReport()
    {
        $organizations = \Ermtool\Organization::lists('name','id');

        return view('reportes.hallazgos',['organizations' => $organizations]);
    }

    //reporte de issues
    public function generarReporteIssues()
    {
        //volvemos a obtener lista de organizaciones
        $organizations = \Ermtool\Organization::lists('name','id');

        //obtenemos nombre de organización
        $org = \Ermtool\Organization::where('id',$_POST['organization_id'])->value('name');

        $org_id = $_POST['organization_id'];
        
        $issues = array();

        $issues = $this->getIssues($_POST['kind'],$_POST['organization_id'],2);
        //print_r($_POST);
        
        return view('reportes.hallazgos',['issues'=>$issues,'kind'=>$_POST['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
    }

    public function generarReporteIssuesExcel($kind,$org)
    {
        $issues = $this->getIssues($kind,$org,2);

        return $issues;
    }

    public function close($id)
    {

    }
}
