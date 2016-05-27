<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;

use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Redirect;
use Session;
use dateTime;

class IssuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //reporte de issues
    public function generarReporteIssues($tipo)
    {
        $results = array();
        $i = 0;
        
        if ($tipo == 1)
        {

            //obtenemos hallazgos de subproceso

            $issues = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('subprocesses','subprocesses.id','=','audit_tests.subprocess_id')
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification',
                            'audit_plans.name as audit_plan_name',
                            'audits.name as audit_name',
                            'audit_programs.name as audit_program_name',
                            'audit_tests.name as audit_test_name',
                            'processes.name as process_name',
                            'subprocesses.name as subprocess_name',
                            'subprocesses.id as subprocess_id')
                        ->get();

            $type = 'Hallazgos de procesos';

        }
        else if ($tipo == 1)
        {
            //obtenemos issues para controles
            $issues = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('controls','controls.id','=','audit_tests.control_id')
                        ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification',
                            'audit_plans.name as audit_plan_name',
                            'audits.name as audit_name',
                            'audit_programs.name as audit_program_name',
                            'audit_tests.name as audit_test_name',
                            'controls.type2 as type',
                            'controls.id as control_id',
                            'controls.name as control_name')
                        ->get();
            

            $type = 'Hallazgos de controles';
        }

        foreach ($issues as $issue1) //para cada issue obtenemos datos de planes de acción, riesgos y controles
        {
                $riesgos = NULL;
                $controles = NULL;
                //recomendaciones
                if ($issue1->recommendations == "")
                {
                    $recommendation = 'No se agregaron recomendaciones';
                }
                else
                    $recommendation = $issue1->recommendations;

                //clasificacion
                switch ($issue1->classification) {
                    case 0:
                        $classification = 'Oportunidad de mejora';
                        break;
                    case 1:
                        $classification = 'Deficiencia';
                        break;
                    case 2:
                        $classification = 'Debilidad significativa';
                        break;
                    default:
                        $classification = 'No se ha clasificado';
                        break;
                }

                $action_plans = NULL;
                $action_plans = DB::table('action_plans')
                        ->join('issues','issues.id','=','action_plans.issue_id')
                        ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id') 
                        ->select('action_plans.description as action_plan',
                                 'action_plans.final_date',
                                 'stakeholders.name as stakeholder_name',
                                 'stakeholders.surnames as stakeholder_surnames')
                        ->where('action_plans.issue_id','=',$issue1->id)
                        ->get();

                if ($action_plans != NULL) //si es que hay plan de acción asociado
                {
                    foreach ($issue2 as $issue)
                    {
                            //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
                            $fecha_final = date('d-m-Y',strtotime($issue->final_date));

                            $plan_accion = $issue->action_plan;
                            $responsable_plan_accion = $issue->stakeholder_name.' '.$issue->stakeholder_surnames;
                            $fecha_final_plan_accion = $fecha_final;
                     }
                }
                else //no hay plan de accion
                {
                    $plan_accion = "No tiene plan de acción";
                    $responsable_plan_accion = "No tiene plan de acción";
                    $fecha_final_plan_accion = "No tiene plan de acción";
                }

                $j = 0; //contador para riesgos
                //obtenemos riesgos asociados al subproceso del issue
                $risks = NULL;

                if ($tipo == 0) //los riesgos obtenidos se obtendrán de distinta forma para hallazgos de proceso o de control
                {
                    $risks = DB::table('risks')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                            ->where('risk_subprocess.subprocess_id','=',$issue1->subprocess_id)
                            ->select('risks.name','risk_subprocess.id')
                            ->get();  
                }
                else if ($tipo == 1)
                {
                    if ($issue1->type == 0) //riesgo de proceso
                    {
                        $risks = DB::table('control_risk_subprocess')
                                ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('control_risk_subprocess.control_id','=',$issue1->control_id)
                                ->select('risks.name')
                                ->get();
                    }
                    else if ($issue1->type == 1) //riesgo de negocio
                    {
                        $risks = DB::table('control_objective_risk')
                                ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('control_objective_risk.control_id','=',$issue1->control_id)
                                ->select('risks.name')
                                ->get();
                    }
                }

                if ($risks != NULL) //si es que hay riesgos asociados
                {       
                    foreach ($risks as $risk)
                    {
                        
                        if ($tipo == 0) //si son hallazgos de proceso obtenemos controles
                        {
                            $k = 0; //contador de controles
                            $controls = NULL;
                            //obtenemos controles asociados al riesgo
                            $controls = DB::table('controls')
                                            ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                                            ->where('control_risk_subprocess.risk_subprocess_id','=',$risk->id)
                                            ->select('controls.name')
                                            ->get();

                            if ($controls != NULL) //si es que hay controles
                            {
                                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                                {
                                    foreach ($controls as $control)
                                    {
                                        if ($k == 0)
                                        {
                                            $controles[$j] = $control->name;
                                        }
                                        else
                                        {
                                            $controles[$j] .= '; '.$control->name;
                                        }

                                        $k += 1;
                                    }
                                }
                                else
                                {
                                    foreach ($controls as $control)
                                    {
                                        $controles[$k] = $control->name;
                                        $k += 1;
                                    }
                                }
                            }
                            else
                            {
                                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                                {
                                    $controles[$j] = "No existen controles asignados";
                                }
                                else
                                {
                                    $controles = "No existen controles asignados";
                                }
                            }

                            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                            {
                                if ($j == 0)
                                {
                                    $riesgos = $risk->name;
                                }
                                else
                                {
                                    $riesgos .= '|'.$risk->name;
                                }
                                
                            }
                            else
                            {
                                $riesgos[$j] = [
                                    'name' => $risk->name,
                                    'controls' => $controles,
                                ];
                            }
                            
                        }
                        else if ($tipo == 1)
                        {
                            $riesgos[$j] = $risk->name;
                        }

                        $j += 1;
                    }
                }
                else
                {
                    $riesgos = "No hay riesgos asociados";
                }

                //se envian distintos campos para hallazgos de proceso o de control
                if ($tipo == 0) 
                {
                    if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                    {   
                        $m = 0;
                        $controlcontrol = '';
                        while (isset($controles[$m]))
                        {
                            $controlcontrol .= $controles[$m].'| ';
                            $m += 1; 
                        }
                        $results[$i] = [
                                        'Proceso' => $issue1->process_name,
                                        'Subproceso' => $issue1->subprocess_name,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Riesgos' => $riesgos,
                                        'Controles' => $controlcontrol,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                    }
                    else
                    {
                        $results[$i] = [
                                        'Proceso' => $issue1->process_name,
                                        'Subproceso' => $issue1->subprocess_name,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Riesgos' => $riesgos,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                    }
                }
                else if ($tipo == 1) 
                {
                    $results[$i] = [
                                        'Control' => $issue1->control_name,
                                        'Riesgos' => $riesgos,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                }
                $i += 1;
                    
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $results;
        }
        else
            return json_encode($results);
    }

    //reporte de issues
    public function generarReporteIssues_old($tipo)
    {
        $results = array();
        $i = 0;
        
        if ($tipo == 0)
        {

            //obtenemos hallazgos para pruebas de auditoría (para subprocesos)

            $issues = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('subprocesses','subprocesses.id','=','audit_tests.subprocess_id')
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification',
                            'audit_plans.name as audit_plan_name',
                            'audits.name as audit_name',
                            'audit_programs.name as audit_program_name',
                            'audit_tests.name as audit_test_name',
                            'processes.name as process_name',
                            'subprocesses.name as subprocess_name',
                            'subprocesses.id as subprocess_id')
                        ->get();

            $type = 'Hallazgos de procesos';

        }
        else if ($tipo == 1)
        {
            //obtenemos issues para controles
            $issues = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('controls','controls.id','=','audit_tests.control_id')
                        ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification',
                            'audit_plans.name as audit_plan_name',
                            'audits.name as audit_name',
                            'audit_programs.name as audit_program_name',
                            'audit_tests.name as audit_test_name',
                            'controls.type2 as type',
                            'controls.id as control_id',
                            'controls.name as control_name')
                        ->get();
            

            $type = 'Hallazgos de controles';
        }

        foreach ($issues as $issue1) //para cada issue obtenemos datos de planes de acción, riesgos y controles
        {
                $riesgos = NULL;
                $controles = NULL;
                //recomendaciones
                if ($issue1->recommendations == "")
                {
                    $recommendation = 'No se agregaron recomendaciones';
                }
                else
                    $recommendation = $issue1->recommendations;

                //clasificacion
                switch ($issue1->classification) {
                    case 0:
                        $classification = 'Oportunidad de mejora';
                        break;
                    case 1:
                        $classification = 'Deficiencia';
                        break;
                    case 2:
                        $classification = 'Debilidad significativa';
                        break;
                    default:
                        $classification = 'No se ha clasificado';
                        break;
                }

                $action_plans = NULL;
                $action_plans = DB::table('action_plans')
                        ->join('issues','issues.id','=','action_plans.issue_id')
                        ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id') 
                        ->select('action_plans.description as action_plan',
                                 'action_plans.final_date',
                                 'stakeholders.name as stakeholder_name',
                                 'stakeholders.surnames as stakeholder_surnames')
                        ->where('action_plans.issue_id','=',$issue1->id)
                        ->get();

                if ($action_plans != NULL) //si es que hay plan de acción asociado
                {
                    foreach ($issue2 as $issue)
                    {
                            //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
                            $fecha_final = date('d-m-Y',strtotime($issue->final_date));

                            $plan_accion = $issue->action_plan;
                            $responsable_plan_accion = $issue->stakeholder_name.' '.$issue->stakeholder_surnames;
                            $fecha_final_plan_accion = $fecha_final;
                     }
                }
                else //no hay plan de accion
                {
                    $plan_accion = "No tiene plan de acción";
                    $responsable_plan_accion = "No tiene plan de acción";
                    $fecha_final_plan_accion = "No tiene plan de acción";
                }

                $j = 0; //contador para riesgos
                //obtenemos riesgos asociados al subproceso del issue
                $risks = NULL;

                if ($tipo == 0) //los riesgos obtenidos se obtendrán de distinta forma para hallazgos de proceso o de control
                {
                    $risks = DB::table('risks')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                            ->where('risk_subprocess.subprocess_id','=',$issue1->subprocess_id)
                            ->select('risks.name','risk_subprocess.id')
                            ->get();  
                }
                else if ($tipo == 1)
                {
                    if ($issue1->type == 0) //riesgo de proceso
                    {
                        $risks = DB::table('control_risk_subprocess')
                                ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('control_risk_subprocess.control_id','=',$issue1->control_id)
                                ->select('risks.name')
                                ->get();
                    }
                    else if ($issue1->type == 1) //riesgo de negocio
                    {
                        $risks = DB::table('control_objective_risk')
                                ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('control_objective_risk.control_id','=',$issue1->control_id)
                                ->select('risks.name')
                                ->get();
                    }
                }

                if ($risks != NULL) //si es que hay riesgos asociados
                {       
                    foreach ($risks as $risk)
                    {
                        
                        if ($tipo == 0) //si son hallazgos de proceso obtenemos controles
                        {
                            $k = 0; //contador de controles
                            $controls = NULL;
                            //obtenemos controles asociados al riesgo
                            $controls = DB::table('controls')
                                            ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                                            ->where('control_risk_subprocess.risk_subprocess_id','=',$risk->id)
                                            ->select('controls.name')
                                            ->get();

                            if ($controls != NULL) //si es que hay controles
                            {
                                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                                {
                                    foreach ($controls as $control)
                                    {
                                        if ($k == 0)
                                        {
                                            $controles[$j] = $control->name;
                                        }
                                        else
                                        {
                                            $controles[$j] .= '; '.$control->name;
                                        }

                                        $k += 1;
                                    }
                                }
                                else
                                {
                                    foreach ($controls as $control)
                                    {
                                        $controles[$k] = $control->name;
                                        $k += 1;
                                    }
                                }
                            }
                            else
                            {
                                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                                {
                                    $controles[$j] = "No existen controles asignados";
                                }
                                else
                                {
                                    $controles = "No existen controles asignados";
                                }
                            }

                            if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                            {
                                if ($j == 0)
                                {
                                    $riesgos = $risk->name;
                                }
                                else
                                {
                                    $riesgos .= '|'.$risk->name;
                                }
                                
                            }
                            else
                            {
                                $riesgos[$j] = [
                                    'name' => $risk->name,
                                    'controls' => $controles,
                                ];
                            }
                            
                        }
                        else if ($tipo == 1)
                        {
                            $riesgos[$j] = $risk->name;
                        }

                        $j += 1;
                    }
                }
                else
                {
                    $riesgos = "No hay riesgos asociados";
                }

                //se envian distintos campos para hallazgos de proceso o de control
                if ($tipo == 0) 
                {
                    if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se guardaran de distinta forma si es que son para el excel
                    {   
                        $m = 0;
                        $controlcontrol = '';
                        while (isset($controles[$m]))
                        {
                            $controlcontrol .= $controles[$m].'| ';
                            $m += 1; 
                        }
                        $results[$i] = [
                                        'Proceso' => $issue1->process_name,
                                        'Subproceso' => $issue1->subprocess_name,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Riesgos' => $riesgos,
                                        'Controles' => $controlcontrol,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                    }
                    else
                    {
                        $results[$i] = [
                                        'Proceso' => $issue1->process_name,
                                        'Subproceso' => $issue1->subprocess_name,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Riesgos' => $riesgos,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                    }
                }
                else if ($tipo == 1) 
                {
                    $results[$i] = [
                                        'Control' => $issue1->control_name,
                                        'Riesgos' => $riesgos,
                                        'Hallazgo' => $issue1->name,
                                        'Clasificación' => $classification,
                                        'Descripción' => $issue1->description,
                                        'Recomendación' => $recommendation,
                                        'Plan_de_acción' => $plan_accion,
                                        'Responsable_plan' => $responsable_plan_accion,
                                        'Fecha_final_plan' => $fecha_final_plan_accion,
                                        'Plan_de_auditoría' => $issue1->audit_plan_name,
                                        'Auditoría' => $issue1->audit_name,
                                        'Programa_de_auditoría' => $issue1->audit_program_name,
                                        'Prueba_de_auditoría' => $issue1->audit_test_name
                                        
                                       ];
                }
                $i += 1;
                    
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcelissues')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $results;
        }
        else
            return json_encode($results);
    }
}
