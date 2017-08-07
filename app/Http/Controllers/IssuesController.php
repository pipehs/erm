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
use Auth;
use Ermtool\Issue as Issue;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class IssuesController extends Controller
{
    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('riesgos_tipo');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/riesgos_tipo.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function setIssue1($processes,$subprocesses,$risks,$controls,$name,$classification,$recommendations,$plan,$status,$final_date)
    {
        try
        {
            if (Session::get('languaje') == 'en')
            {
                            $issue = [
                                'Processes' => $processes,
                                'Subprocesses' => $subprocesses,
                                'Risks' => $risks,
                                'Controls' => $controls,
                                'Name' => $name,
                                'Classification' => $classification,
                                'Recommendations' => $recommendations,
                                'Action Plan' => $plan,
                                'Status' => $status,
                                'Plan Deadline' => $final_date
                            ];
            }
            else
            {
                            $issue = [
                                'Procesos' => $processes,
                                'Subprocesos' => $subprocesses,
                                'Riesgos' => $risks,
                                'Controles' => $controls,
                                'Nombre' => $name,
                                'Clasificación' => $classification,
                                'Recomendaciones' => $recommendations,
                                'Plan de acción' => $plan,
                                'Estado' => $status,
                                'Fecha límite plan' => $final_date
                            ];
            }

            return $issue;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function setIssue2($objectives,$risks,$controls,$name,$classification,$recommendations,$plan,$status,$final_date)
    {
        try
        {
            if (Session::get('languaje') == 'en')
            {
                $issue = [
                                'Objectives' => $objectives,
                                'Risks' => $risks,
                                'Controls' => $controls,
                                'Name' => $name,
                                'Classification' => $classification,
                                'Recommendations' => $recommendations,
                                'Action Plan' => $plan,
                                'Status' => $status,
                                'Plan Deadline' => $final_date
                            ];
            }
            else
            {
                $issue = [
                                'Objetivos' => $objectives,
                                'Riesgos' => $risks,
                                'Controles' => $controls,
                                'Nombre' => $name,
                                'Clasificación' => $classification,
                                'Recomendaciones' => $recommendations,
                                'Plan de acción' => $plan,
                                'Estado' => $status,
                                'Fecha límite plan' => $final_date
                            ];
            }

            return $issue;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //set para reporte de excel de hallazgos de organización
    public function setIssue3($objectives,$name,$classification,$recommendations,$plan,$status,$final_date)
    {
        try
        {
            if (Session::get('languaje') == 'en')
            {
                $issue = [
                                'Objectives' => $objectives,
                                'Name' => $name,
                                'Classification' => $classification,
                                'Recommendations' => $recommendations,
                                'Action Plan' => $plan,
                                'Status' => $status,
                                'Plan Deadline' => $final_date
                            ];
            }
            else
            {
                $issue = [
                                'Objetivos' => $objectives,
                                'Nombre' => $name,
                                'Clasificación' => $classification,
                                'Recomendaciones' => $recommendations,
                                'Plan de acción' => $plan,
                                'Estado' => $status,
                                'Fecha límite plan' => $final_date
                            ];
            }

            return $issue;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //set para reporte de excel de auditoría y programas de auditoría
    public function setIssue4($audit_plans,$audits,$programs,$test,$name,$classification,$recommendations,$plan,$status,$final_date)
    {
        try
        {
            if (Session::get('languaje') == 'en')
            {
                $issue = [
                                'Audit_plans' => $audit_plans,
                                'Audits' => $audits,
                                'Programs' => $programs,
                                'Name' => $name,
                                'Classification' => $classification,
                                'Recommendations' => $recommendations,
                                'Action Plan' => $plan,
                                'Status' => $status,
                                'Plan Deadline' => $final_date
                            ];
            }
            else
            {
                $issue = [
                                'Plan(es) de auditoría' => $audit_plans,
                                'Auditoría(s)' => $audits,
                                'Programa(s)' => $programs,
                                'Nombre' => $name,
                                'Clasificación' => $classification,
                                'Recomendaciones' => $recommendations,
                                'Plan de acción' => $plan,
                                'Estado' => $status,
                                'Fecha límite plan' => $final_date
                            ];
            }

            return $issue;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    
    //obtiene hallazgos de tipo $kind para la org $org_id. Esto para mantenedor de hallazgos y reporte de hallazgos
    //ACTUALIZACIÓN 09-03-17: SE AGREGÓ 2DO TIPO PARA CUALQUIER HALLAZGO (EXCEPTO DE ORGANIZACIÓN)
    public function getIssues($kind,$kind3,$org_id,$kind2)
    {
        try
        {
            //$kind = tipo de hallazgo; $kind3 = id de elemento (en caso de haber); $org_id = org id; $kind2 = si es excel (2) o no (1)
            $issues = array();
            $datos = array(); //se usará sólo para reportes

            if ($kind == 0)
            {
                //hallazgos de proceso creados directamente
                if ($kind3 != NULL)
                {
                    $processes = \Ermtool\Process::getProcessFromIssues($org_id,$kind3);
                }
                else
                {
                    $processes = \Ermtool\Process::getProcessFromIssues($org_id,NULL);
                }

                $process_issues = array(); //se guardaran los procesos que tienen issues que además tienen documentos
                $i = 0;
                foreach ($processes as $process)
                {
                    //obtenemos issues del proceso
                    $issues2 = \Ermtool\Issue::getProcessIssues($process->id);

                    foreach ($issues2 as $issue)
                    {
                        //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                        $short_des = substr($issue->description,0,100);
                        $short_rec = substr($issue->recommendations,0,100);

                        $issues[$i] = [
                            'element_id' => $process->id,
                            'process' => $process->name,
                            'process_description' => $process->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }

            }

            else if ($kind == 1) 
            {
                //hallazgos de subproceso creados directamente
                if ($kind3 != NULL)
                {
                    $subprocesses = \Ermtool\Subprocess::getSubprocessFromIssues($org_id,$kind3);
                }
                else
                {
                    $subprocesses = \Ermtool\Subprocess::getSubprocessFromIssues($org_id,NULL);
                }
                
                $issues = array(); //se guardaran los subprocesos que tienen issues que además tienen documentos
                $issues2 = array();
                $i = 0;
                foreach ($subprocesses as $subprocess)
                {
                    //obtenemos issues del proceso
                    $issues2 = \Ermtool\Issue::getSubprocessIssuesBySubprocess($subprocess->id);

                    if (!empty($issues2))
                    {
                        foreach ($issues2 as $issue)
                        {
                            $short_des = substr($issue->description,0,100);
                            $short_rec = substr($issue->recommendations,0,100);

                            $issues[$i] = [
                                'element_id' => $subprocess->id,
                                'subprocess' => $subprocess->name,
                                'subprocess_description' => $subprocess->description,
                                'id' => $issue->id,
                                'name' => $issue->name,
                                'description' => $issue->description,
                                'classification' => $issue->classification,
                                'recommendations' => $issue->recommendations
                            ];

                            $i += 1; 
                        }
                    }
                    
                }
            }

            else if ($kind == 2) //Hallazgos de organización
            {
                //CORRECCIÓN 08-11-2016: SÓLO SE MOSTRARÁN LOS ISSUES PARA LAS ORGANIZACIONES DIRECTAMENTE => PARA EVITAR MAYORES CAMBIOS QUE PUEDAN CONLLEVAR ERRORERS,
                //LAS VARIABLES DE ISSUES SERÁN ENVIADAS PERO VACÍAS
                $issues = \Ermtool\Organization::find($org_id)->issues;
                $org_name = \Ermtool\Organization::name($org_id);
                $org_description = \Ermtool\Organization::description($org_id);
            }
            else if ($kind == 3) //Hallazgos de control de proceso
            {
                if ($kind3 != NULL)
                {
                    $controls = \Ermtool\Control::getProcessesControlsFromIssues($org_id,$kind3);
                }
                else
                {
                    $controls = \Ermtool\Control::getProcessesControlsFromIssues($org_id,NULL);
                }

                $issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                $i = 0;
                foreach ($controls as $control)
                {
                    //obtenemos issues del control
                    $issues2 = \Ermtool\Issue::getControlIssues($control->id);

                    foreach ($issues2 as $issue)
                    {
                        $issues[$i] = [
                            'element_id' => $control->id,
                            'control' => $control->name,
                            'control_description' => $control->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }
            }
            else if ($kind == 4) //hallazgos de control de negocio
            {
                if ($kind3 != NULL)
                {
                    $controls = \Ermtool\Control::getObjectivesControlsFromIssues($org_id,$kind3);
                }
                else
                {
                    $controls = \Ermtool\Control::getObjectivesControlsFromIssues($org_id,NULL);
                }

                $issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                $i = 0;
                foreach ($controls as $control)
                {
                    //obtenemos issues del control
                    $issues2 = \Ermtool\Issue::getControlIssues($control->id);

                    foreach ($issues2 as $issue)
                    {
                        $issues[$i] = [
                            'element_id' => $control->id,
                            'control' => $control->name,
                            'control_description' => $control->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }
            }
            else if ($kind == 5) //hallazgos de programa auditoría
            {
                if ($kind3 != NULL)
                {
                    $audit_programs = \Ermtool\Audit_program::getProgramsFromIssues($org_id,$kind3);
                }
                else
                {
                    $audit_programs = \Ermtool\Audit_program::getProgramsFromIssues($org_id,NULL);
                }

                $issues = array(); //se guardaran los programas que tienen issues que además tienen documentos
                $i = 0;
                foreach ($audit_programs as $audit_program)
                {
                    //obtenemos issues del programa
                    $issues2 = \Ermtool\Issue::getAuditProgramIssues($audit_program->id);

                    foreach ($issues2 as $issue)
                    {
                        $issues[$i] = [
                            'element_id' => $audit_program->id,
                            'audit_program' => $audit_program->name,
                            'audit_program_description' => $audit_program->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }

            }
            else if ($kind == 6) //hallazgos de auditoría
            {
                if ($kind3 != NULL)
                {
                    $audits = \Ermtool\Audit::getAuditsFromIssues($org_id,$kind3);
                }
                else
                {
                    $audits = \Ermtool\Audit::getAuditsFromIssues($org_id,NULL);
                }

                $issues = array(); //se guardaran los programas que tienen issues que además tienen documentos
                $i = 0;
                foreach ($audits as $audit)
                {
                    //obtenemos issues de la auditoría
                    $issues2 = \Ermtool\Issue::getAuditIssues($audit->id);

                    foreach ($issues2 as $issue)
                    {
                        $issues[$i] = [
                            'element_id' => $audit->id,
                            'audit' => $audit->name,
                            'audit_description' => $audit->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }
            }

            else if ($kind == 7) //ACTUALIZACIÓN 31-01-2017: Hallazgos de pruebas de auditoría
            {
                if ($kind3 != NULL)
                {
                    $tests = \Ermtool\Audit_test::getAuditTestsFromIssues($org_id,$kind3);
                }
                else
                {
                    $tests = \Ermtool\Audit_test::getAuditTestsFromIssues($org_id,NULL);    
                }

                $issues = array(); //se guardaran los programas que tienen issues que además tienen documentos
                $i = 0;
                foreach ($tests as $test)
                {
                    //obtenemos issues de la auditoría
                    $issues2 = \Ermtool\Issue::getTestIssues($test->id);

                    foreach ($issues2 as $issue)
                    {
                        $issues[$i] = [
                            'element_id' => $test->id,
                            'audit_test' => $test->name,
                            'audit_test_description' => $test->description,
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];

                        $i += 1; 
                    }
                }
            }

            $i = 0;
            //  dd($issues1);
            
            foreach ($issues as $issue)
            {
                $plan = NULL;
                //para cada issue obtenemos datos de plan de acción (si es que hay)
                $plan = DB::table('action_plans')
                        ->where('issue_id','=',$issue['id'])
                        ->select('description','final_date','status')
                        ->get();

                if ($plan != NULL)
                {
                    $temp = $this->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],$plan[0]->description,$plan[0]->status,$plan[0]->final_date);  
                }
                else
                {
                    $temp = $this->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],NULL,NULL,NULL);
                }      

                if ($kind2 == 2) //estamos formateando para reporte de hallazgos, por lo que se agregarán datos extras
                {
                    $datos = $this->datosReporte($issue['element_id'],$kind,$org_id);
                }

                if (strstr($_SERVER["REQUEST_URI"],'genexcelissues'))
                {
                    //DEBO ARREGLAR ESTO!!!!!
                    
                    if ($kind == 0) //Hallazgos de proceso
                    {
                        $issues[$i] = $this->setIssue1($datos['process'],$datos['subprocesses'],$datos['risks'],$datos['controls'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    if ($kind == 1) //Hallazgos de subproceso
                    {
                        $issues[$i] = $this->setIssue1($datos['process'],$datos['subprocess'],$datos['risks'],$datos['controls'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    if ($kind == 2) //Hallazgos de organización
                    {
                         $issues[$i] = $this->setIssue3($datos['objectives'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    else if ($kind == 3) //Hallazgos de control de proceso
                    {
                        $issues[$i] = $this->setIssue1($datos['processes'],$datos['subprocesses'],$datos['risks'],$datos['control'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    else if ($kind == 4) //Hallazgos de control de negocio
                    {
                        $issues[$i] = $this->setIssue2($datos['objectives'],$datos['risks'],$datos['control'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    else if ($kind == 5) //Hallazgos de programa de auditoría
                    {
                        $issues[$i] = $this->setIssue4($datos['audit_plans'],$datos['audits'],$datos['audit_program'],NULL,$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    else if ($kind == 6) //Hallazgos de auditoría
                    {
                        $issues[$i] = $this->setIssue4($datos['audit_plans'],$datos['audit'],$datos['audit_programs'],NULL,$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    else if ($kind == 7) //Hallazgos de pruebas de auditoría
                    {
                        $issues[$i] = $this->setIssue4($datos['audit_plans'],$datos['audit'],$datos['audit_programs'],$datos['audit_test'],$temp['name'],$temp['classification'],$temp['recommendations'],$temp['plan'],$temp['status'],$temp['final_date']);
                    }
                    
                }
                else
                {
                    //obtenemos posibles evidencias
                    //$evidence = getEvidences(2,$temp['id']);

                    $origin = Issue::getOrigin($kind,$issue['element_id'],$org_id);

                    $short_plan = substr($temp['plan'],0,100);
                    $short_des = substr($issue['description'],0,100);
                    $short_rec = substr($temp['recommendations'],0,100);

                    $issues[$i] = [
                        'id' => $temp['id'],
                        'origin' => $origin,
                        'name' => $temp['name'],
                        'classification' => $temp['classification'],
                        'recommendations' => $temp['recommendations'],
                        'plan' => $temp['plan'],
                        'status' => $temp['status'],
                        'status_origin' => $temp['status_origin'],
                        'final_date' => $temp['final_date'],
                        'datos' => $datos,
                        'description' => $issue['description'],
                        'short_des' => $short_des,
                        'short_rec' => $short_rec,
                        'short_plan' => $short_plan
                    ];
                }

                $i += 1;
            }
        
            return $issues;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //datos extras para reporte de auditoría
    public function datosReporte($element_id,$kind,$org)
    {
        try
        {
            $datos = array();
            $risks = "";
            $controls = "";
            $subprocesses = "";

            if ($kind == 0) //hallazgos de proceso
            {
                $process = \Ermtool\Process::find($element_id);
                $subprocesses1 = \Ermtool\Subprocess::getSubprocessesFromProcess($org,$element_id);

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

                $risks1 = \Ermtool\Risk::getRisksFromProcess($org,$element_id);
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
                    if (Session::get('languaje') == 'en')
                    {
                        $risks = "No risks were identified";
                    }
                    else
                    {
                       $risks = "No se han identificado riesgos"; 
                    }
                }

                $controls1 = \Ermtool\Control::getControlsFromProcess($org,$element_id);

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
                    if (Session::get('languaje') == 'en')
                    {
                        $controls = "No controls identified";
                    }
                    else
                    {
                       $controls = "No se han definido controles"; 
                    }
                }
                

                $datos = [
                    'risks' => $risks,
                    'process' => $process->name,
                    'description' => $process->description,
                    'subprocesses' => $subprocesses,
                    'controls' => $controls,
                    ];
            }
            else if ($kind == 1) //hallazgos de subproceso
            {
                $subprocess = \Ermtool\Subprocess::find($element_id);
                $process = \Ermtool\Subprocess::getProcess($subprocess->id);

                $risks1 = \Ermtool\Risk::getRisksFromSubprocess($org,$element_id);

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
                    if (Session::get('languaje') == 'en')
                    {
                        $risks = "No risks were identified";
                    }
                    else
                    {
                       $risks = "No se han identificado riesgos"; 
                    }
                }

                $controls1 = \Ermtool\Control::getControlsFromSubprocess($org,$element_id);

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
                    if (Session::get('languaje') == 'en')
                    {
                        $controls = "No controls identified";
                    }
                    else
                    {
                       $controls = "No se han definido controles"; 
                    }
                }
                

                $datos = [
                    'risks' => $risks,
                    'process' => $process->name,
                    'subprocess' => $subprocess->name,
                    'description' => $subprocess->description,
                    'controls' => $controls,
                    ];
            }
            else if ($kind == 2) //hallazgos de organización
            {
                $objectives = "";

                $objectives1 = \Ermtool\Organization::find($org)->objectives;      

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

                $datos = [
                    'objectives' => $objectives,
                    ];
            }
            else if ($kind == 3) //hallazgos de control de proceso
            {
                $processes = "";
                $subprocesses = "";

                $processes1 = \Ermtool\Process::getProcessesFromControl($org,$element_id);

                $last = end($processes1); //guardamos final para no agregarle coma
                foreach ($processes1 as $process)
                {
                    if ($process != $last)
                    {
                        $processes .= $process->name.', ';
                    }
                    else
                    {
                        $processes .= $process->name;
                    }
                
                }

                $subprocesses1 = \Ermtool\Subprocess::getSubprocessesFromControl($org,$element_id);

                $last = end($subprocesses1); //guardamos final para no agregarle coma
                foreach ($subprocesses1 as $subprocess)
                {
                    if ($subprocess != $last)
                    {
                        $subprocesses .= $subprocess->name.', ';
                    }
                    else
                    {
                        $subprocesses .= $subprocess->name;
                    }
                
                }

                $risks1 = \Ermtool\Risk::getRisksFromControl($org,$element_id);

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
                    if (Session::get('languaje') == 'en')
                    {
                        $risks = "No risks were identified";
                    }
                    else
                    {
                        $risks = "No se han identificado riesgos";
                    }
                }

                $control = \Ermtool\Control::find($element_id);     

                $datos = [
                    'processes' => $processes,
                    'subprocesses' => $subprocesses,
                    'risks' => $risks,
                    'control' => $control->name,
                    ];
            }
            else if ($kind == 4) //hallazgos de control de entidad
            {
                $objectives = "";
                $controls = "";
                $risks = "";

                $objectives1 = \Ermtool\Objective::getObjectivesFromControl($org,$element_id);

                $last = end($objectives1); //guardamos final para no agregarle coma
                foreach ($objectives1 as $obj)
                {
                    if ($obj != $last)
                    {
                        $objectives .= $obj->name.', ';
                    }
                    else
                    {
                        $objectives .= $obj->name;
                    }
                
                }

                $risks1 = \Ermtool\Risk::getRisksFromControl($org,$element_id);

                $last = end($risks1); //guardamos final para no agregarle coma
                foreach ($risks1 as $risk)
                {
                    if ($risk != $last)
                    {
                        $risks .= $risk->name.', ';
                    }
                    else
                    {
                        $risks .= $risk->name;
                    }
                
                }

                $control = \Ermtool\Control::find($element_id);

                $datos = [
                    'risks' => $risks,
                    'objectives' => $objectives,
                    'control' => $control->name,
                    ];
            }
            else if ($kind == 5) //programas de auditoría
            {
                $audits = "";
                $audit_plans = ""; 
                $audits1 = \Ermtool\Audit_program::getAudits($org,$element_id); 

                $audit_program = \Ermtool\Audit_program::name($element_id);

                if ($audits1)
                {
                    $last = end($audits1); //guardamos final para no agregarle coma
                    foreach ($audits1 as $audit)
                    {
                        if ($audit != $last)
                        {
                            $audits .= $audit->audit.', ';
                            $audit_plans .= $audit->audit_plan.', ';
                        }
                        else
                        {
                            $audits .= $audit->audit;
                            $audit_plans .= $audit->audit_plan;
                        }
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $audits = "No audits were identified";
                        $audit_plans = "No audit plans were identified";
                    }
                    else
                    {
                        $audits = "No se encontraron auditorías para el programa";
                        $audit_plans = "No se encotraron planes para el programa";
                    }
                }

                $datos = [
                    'audit_plans' => $audit_plans,
                    'audits' => $audits,
                    'audit_program' => $audit_program,
                ];
            }
            else if ($kind == 6) //auditorías
            {
                $audit_plans1 = \Ermtool\Audit_plan::getAuditPlansFromAudit($org,$element_id);
                $audit_plans = "";
                $audit_programs = "";
                if ($audit_plans1)
                {
                    $last = end($audit_plans1); //guardamos final para no agregarle coma
                    foreach ($audit_plans1 as $audit_plan)
                    {
                        if ($audit_plan != $last)
                        {
                            $audit_plans .= $audit_plan->name.', ';
                        }
                        else
                        {
                            $audit_plans .= $audit_plan->name;
                        }
                    }
                }

                $audit = \Ermtool\Audit::name($element_id);

                $audit_programs1 = \Ermtool\Audit_program::getAuditProgramFromAudit($org,$element_id);
                if ($audit_programs1)
                {
                    $last = end($audit_programs1); //guardamos final para no agregarle coma
                    foreach ($audit_programs1 as $program)
                    {
                        if ($program != $last)
                        {
                            $audit_programs .= $program->name.', ';
                        }
                        else
                        {
                            $audit_programs .= $program->name;
                        }
                    }
                }

                $datos = [
                    'audit_plans' => $audit_plans,
                    'audit' => $audit,
                    'audit_programs' => $audit_programs
                ];
            }

            else if ($kind == 7) //ACTUALIZACIÓN 31-01-2017: Pruebas de auditorías
            {
                $audit_plans1 = \Ermtool\Audit_plan::getAuditPlansFromAuditTest($org,$element_id);
                $audit_plans = "";
                $audit_programs = "";
                $audits = "";
                if ($audit_plans1)
                {
                    $last = end($audit_plans1); //guardamos final para no agregarle coma
                    foreach ($audit_plans1 as $audit_plan)
                    {
                        if ($audit_plan != $last)
                        {
                            $audit_plans .= $audit_plan->name.', ';
                        }
                        else
                        {
                            $audit_plans .= $audit_plan->name;
                        }
                    }
                }

                $audit1 = \Ermtool\Audit::getAuditFromAuditTest($org,$element_id);
                if ($audit1)
                {
                    $last = end($audit1); //guardamos final para no agregarle coma
                    foreach ($audit1 as $audit)
                    {
                        if ($audit != $last)
                        {
                            $audits .= $audit->name.', ';
                        }
                        else
                        {
                            $audits .= $audit->name;
                        }
                    }
                }

                $audit_programs1 = \Ermtool\Audit_program::getAuditProgramFromAuditTest($org,$element_id);
                if ($audit_programs1)
                {
                    $last = end($audit_programs1); //guardamos final para no agregarle coma
                    foreach ($audit_programs1 as $program)
                    {
                        if ($program != $last)
                        {
                            $audit_programs .= $program->name.', ';
                        }
                        else
                        {
                            $audit_programs .= $program->name;
                        }
                    }
                }

                $audit_test = \Ermtool\Audit_test::getTestNameById($element_id);

                $datos = [
                    'audit_plans' => $audit_plans,
                    'audit' => $audit,
                    'audit_programs' => $audit_programs,
                    'audit_test' => $audit_test,
                ];
            }

            return $datos;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function formatearIssue($id,$name,$classification,$recommendations,$plan,$status,$date)
    {
        try
        {
            if (Session::get('languaje') == 'en')
            {
                if ($classification === 0) //debe ser identico para no tomar en cuenta null
                    $class = 'Improvement Opportunity';
                else if ($classification == 1)
                    $class = 'Deficience';
                else if ($classification == 2)
                    $class = 'Significant weakness';
                else
                    $class = 'No classified yet';

                if ($recommendations == "" || $recommendations == NULL)
                {
                    $rec = 'No recommendations added';
                }
                else
                {
                    $rec = $recommendations;
                }

                if ($plan == "" || $plan == NULL)
                {
                    $desc = 'No description were added for the plan (or no created plan yet)';
                }
                else
                {
                    $desc = $plan;
                }

                if ($date == '0000-00-00' || $date == NULL)
                {
                    $final_date = 'Error storing plan deadline (or no created plan yet)';
                }
                else
                {
                    $temp = new DateTime($date);
                    $final_date = date_format($temp, 'd-m-Y');
                }

                if ($status === NULL)
                {
                    $status2 = 'Error storing the status for the plan';
                }
                else
                {
                    if ($status == 0)
                    {
                        $status2 = 'In progress';
                    }
                    else if ($status == 1)
                    {
                        $status2 = 'Closed';
                    }
                } 
            }
            else
            {
                if ($classification === 0) //debe ser identico para no tomar en cuenta null
                    $class = 'Oportunidad de mejora';
                else if ($classification == 1)
                    $class = 'Deficiencia';
                else if ($classification == 2)
                    $class = 'Debilidad significativa';
                else
                    $class = 'Aun no se ha clasificado';

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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //obtenemos lista de organizaciones
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.hallazgos.index',['organizations'=>$organizations]);
                }
                else
                {
                    return view('hallazgos.index',['organizations'=>$organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function index2()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //volvemos a obtener lista de organizaciones
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                //obtenemos nombre de organización
                $org = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');

                $org_id = $_GET['organization_id'];
                
                $issues = array();

                if (isset($_GET['second_select']) && $_GET['second_select'] != '') //si son hallazgos de organización no debería estar seteado
                {
                    $issues = $this->getIssues($_GET['kind'],$_GET['second_select'],$_GET['organization_id'],1);
                }
                else
                {
                    $issues = $this->getIssues($_GET['kind'],NULL,$_GET['organization_id'],1);
                }
                
                //print_r($_POST);
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.hallazgos.index',['issues'=>$issues,'kind'=>$_GET['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
                }
                else
                {
                    return view('hallazgos.index',['issues'=>$issues,'kind'=>$_GET['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función para ver issues a través de la ejecución de una prueba
    public function index3($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //obtenemos nombre de organización
                $audit_test = \Ermtool\Audit_test::getTestNameById($id);
                
                $issues = array();

                $issues1 = Issue::getIssueByTestId($id);

                //print_r($_POST);
                $i = 0;
                foreach ($issues1 as $issue)
                {
                    if ($issue['plan_description'] != NULL)
                    {
                        $temp = $this->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],$issue['plan_description'],$issue['plan_status'],$issue['plan_final_date']);  
                    }
                    else
                    {
                        $temp = $this->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],NULL,NULL,NULL);  
                    }

                    $issues[$i] = [
                        'id' => $temp['id'],
                        'name' => $temp['name'],
                        'classification' => $temp['classification'],
                        'recommendations' => $temp['recommendations'],
                        'plan' => $temp['plan'],
                        'status' => $temp['status'],
                        'status_origin' => $temp['status_origin'],
                        'final_date' => $temp['final_date'],
                        'evidence' => $issue['evidences']
                    ];

                    $i += 1; 
                }

                $org_id = \Ermtool\Organization::getOrgIdByTestId($id);
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.hallazgos.index2',['issues'=>$issues, 'audit_test' => $audit_test,'audit_test_id' => $id,'org_id' => $org_id]);
                }
                else
                {
                    return view('hallazgos.index2',['issues'=>$issues, 'audit_test' => $audit_test,'audit_test_id' => $id,'org_id' => $org_id]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                if (isset($_GET['org'])) //mantenedor de hallazgos
                {
                    $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');

                    //obtenemos stakeholders de la misma organización
                    $stakes = \Ermtool\Stakeholder::listStakeholders($_GET['org']);

                    if ($_GET['kind'] == 0) //obtenemos procesos
                    {
                        $processes = \Ermtool\Process::where('processes.status',0)
                                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                    ->lists('processes.name','processes.id');

                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.create',['org'=>$org, 'processes' => $processes,'kind' => $_GET['kind'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'processes' => $processes,'kind' => $_GET['kind'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        
                    }
                    else if ($_GET['kind'] == 1) //obtenemos subprocesos
                    {
                        $subprocesses = \Ermtool\Subprocess::where('subprocesses.status',0)
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                    ->lists('subprocesses.name','subprocesses.id');

                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.create',['org'=>$org, 'subprocesses' => $subprocesses,'kind' => $_GET['kind'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'subprocesses' => $subprocesses,'kind' => $_GET['kind'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                    else if ($_GET['kind'] == 2) //mandamos id de org
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.create',['org'=>$org, 'kind' => $_GET['kind'], 'org_id'=>$_GET['org'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'kind' => $_GET['kind'], 'org_id'=>$_GET['org'],'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                    else if ($_GET['kind'] == 3) //obtenemos controles de proceso
                    {
                        $controls = \Ermtool\Control::listControls($_GET['org'],0);

                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.create',['org'=>$org, 'controls' => $controls, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'controls' => $controls, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                    else if ($_GET['kind'] == 4) //obtenemos controles de entidad
                    {
                        $controls = \Ermtool\Control::listControls($_GET['org'],1);

                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.create',['org'=>$org, 'controls' => $controls, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'controls' => $controls, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                    else if ($_GET['kind'] == 5)
                    {
                        $audit_programs = DB::table('audit_programs')
                                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
                                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                                    ->where('audit_plans.organization_id','=',$_GET['org'])
                                    ->lists('audit_programs.name','audit_audit_plan_audit_program.id');
                        
                        if (Session::get('languaje') == 'en')
                        {         
                            return view('en.hallazgos.create',['org'=>$org, 'audit_programs' => $audit_programs, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'audit_programs' => $audit_programs, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                    else if ($_GET['kind'] == 6)
                    {
                        $audits = DB::table('audit_audit_plan')
                                ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                                ->where('audit_plans.organization_id','=',$_GET['org'])
                                ->select('audit_audit_plan.id',DB::raw("CONCAT(audit_plans.name, ' - ', audits.name) AS audit_name"))
                                ->lists('audit_name','audit_audit_plan.id');
                        
                        if (Session::get('languaje') == 'en')
                        {     
                            return view('en.hallazgos.create',['org'=>$org, 'audits' => $audits, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                        else
                        {
                            return view('hallazgos.create',['org'=>$org, 'audits' => $audits, 'kind' => $_GET['kind'], 'stakeholders'=>$stakes,'org_id' => $_GET['org']]);
                        }
                    }
                }

                else if (isset($_GET['test'])) //ejecución de auditorías
                {
                    $org = \Ermtool\Organization::getOrgIdByTestId($_GET['test']);

                    //obtenemos stakeholders de la misma organización
                    $stakes = \Ermtool\Stakeholder::listStakeholders($org);

                    $test = \Ermtool\Audit_test::getTestNameById($_GET['test']);

                    if (Session::get('languaje') == 'en')
                    {     
                        return view('en.hallazgos.create',['test'=>$test, 'test_id' => $_GET['test'],'stakeholders' => $stakes]);
                    }
                    else
                    {
                         return view('hallazgos.create',['test'=>$test, 'test_id' => $_GET['test'],'stakeholders' => $stakes]);
                    }
                }

                else if (isset($_GET['evaluation'])) //evaluación de controles
                {
                    $evaluation = \Ermtool\Control_evaluation::find($_GET['evaluation']);

                    $org = \Ermtool\Organization::getOrganizationIdFromControl($evaluation->control_id);
                    //obtenemos stakeholders de la misma organización
                    $stakes = \Ermtool\Stakeholder::listStakeholders($org->id);
                    
                    $control = \Ermtool\Control::name($evaluation->control_id);

                    if (Session::get('languaje') == 'en')
                    {     
                        return view('en.hallazgos.create',['control'=>$control, 'evaluation_id' => $_GET['evaluation'],'stakeholders' => $stakes]);
                    }
                    else
                    {
                        return view('hallazgos.create',['control'=>$control, 'evaluation_id' => $_GET['evaluation'],'stakeholders' => $stakes]);
                    }
                }       
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //print_r($_POST);
                global $req;
                $req = $request;
                global $evidence;
                $evidence = $request->file('evidence_doc2');
                DB::transaction(function() {
                    $logger = $this->logger;
                    //verificamos ingreso de datos
                    if (isset($_POST['description']))
                    {
                        $description = $_POST['description'];

                        $description = eliminarSaltos($description);
                    }
                    else
                    {
                        $description = NULL;
                    }

                    if (isset($_POST['recommendations']))
                    {
                        $recommendations = $_POST['recommendations'];
                        $recommendations = eliminarSaltos($recommendations);
                    }
                    else
                    {
                        $recommendations = NULL;
                    }
                    if (isset($_POST['classification']) && $_POST['classification'] != "")
                    {
                        $classification = $_POST['classification'];
                    }
                    else
                    {
                        $classification = NULL;
                    }
                    
                    if (isset($_POST['kind']))
                    {
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
                        else if ($_POST['kind'] == 1) //hallazgo de subproceso
                        {
                            $issue = DB::table('issues')
                                ->insertGetId([
                                        'name' => $_POST['name'],
                                        'description' => $description,
                                        'recommendations' => $recommendations,
                                        'classification' => $classification,
                                        'subprocess_id' => $_POST['subprocess_id'],
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
                        else if ($_POST['kind'] == 3 || $_POST['kind'] == 4) //hallazgo de controles
                        {
                            $issue = DB::table('issues')
                                ->insertGetId([
                                        'name' => $_POST['name'],
                                        'description' => $description,
                                        'recommendations' => $recommendations,
                                        'classification' => $classification,
                                        'control_id' => $_POST['control_id'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                        }
                        else if ($_POST['kind'] == 5) //hallazgo de programa de auditoría
                        {
                            $issue = DB::table('issues')
                                ->insertGetId([
                                        'name' => $_POST['name'],
                                        'description' => $description,
                                        'recommendations' => $recommendations,
                                        'classification' => $classification,
                                        'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                        }
                        else if ($_POST['kind'] == 6) //hallazgo de auditoría
                        {
                            $issue = DB::table('issues')
                                ->insertGetId([
                                        'name' => $_POST['name'],
                                        'description' => $description,
                                        'recommendations' => $recommendations,
                                        'classification' => $classification,
                                        'audit_audit_plan_id' => $_POST['audit_audit_plan_id'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                        }
                    }
                    else if (isset($_POST['test_id'])) //es un hallazgo de ejecución de prueba de auditoría
                    {
                        $issue = DB::table('issues')
                            ->insertGetId([
                                    'name' => $_POST['name'],
                                    'description' => $description,
                                    'recommendations' => $recommendations,
                                    'classification' => $classification,
                                    'audit_test_id' => $_POST['test_id'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                    }
                    else if (isset($_POST['evaluation_id'])) //es un hallazgo de control
                    {
                        $issue = DB::table('issues')
                            ->insertGetId([
                                    'name' => $_POST['name'],
                                    'description' => $description,
                                    'recommendations' => $recommendations,
                                    'classification' => $classification,
                                    'control_evaluation_id' => $_POST['evaluation_id'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                    }

                    //agregamos evidencia (si es que existe)
                    if ($GLOBALS['req']->file('evidence_doc') != NULL)
                    {
                        foreach ($GLOBALS['req']->file('evidence_doc') as $file)
                        {
                            if ($file != NULL)
                            {
                                upload_file($file,'evidencias_hallazgos',$issue);
                            } 
                        }
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

                        $_POST['description_plan'] = eliminarSaltos($_POST['description_plan']);

                        $newplan = $plan->store($issue,$_POST['description_plan'],$stakeholder,$final_date);

                        $id_action_plan = $newplan->id;

                        //agregamos evidencia del plan de acción (si es que existe)
                        if($GLOBALS['evidence'] != NULL)
                        {
                            foreach ($GLOBALS['evidence'] as $evidence)
                            {
                                if ($evidence != NULL)
                                {
                                    upload_file($evidence,'planes_accion',$id_action_plan);
                                }
                            }                    
                        }
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        if (isset($newplan))
                        {
                            Session::flash('message','Issue and action plan successfully created');
                        }
                        else
                        {   
                            Session::flash('message','Issue successfully created');
                        }
                    }
                    else
                    {
                        if (isset($newplan))
                        {
                            Session::flash('message','Hallazgo y plan de acción creado correctamente');
                        }
                        else
                        {   
                            Session::flash('message','Hallazgo creado correctamente');
                        }
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha generado el hallazgo con Id: '.$issue.' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                if (isset($_POST['kind']))
                {
                    return Redirect::to('hallazgos_lista?organization_id='.$_POST['organization_id'].'&kind='.$_POST['kind']);
                }
                else
                {
                    return Redirect::to('hallazgos');
                }        
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                if (isset($_GET['test_id']))
                {
                    $test_id = $_GET['test_id'];
                    $eval_id = NULL;
                }   
                else if (isset($_GET['evaluation']))
                {
                    $eval_id = $_GET['evaluation'];
                    $test_id = NULL;
                }
                else
                {
                    $test_id = NULL;
                    $eval_id = NULL;
                }

                $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');
                $org_id = \Ermtool\Organization::where('id',$_GET['org'])->value('id');

                //obtenemos stakeholders de la misma organización
                $stakes = \Ermtool\Stakeholder::listStakeholders(NULL);

                $issue = \Ermtool\Issue::find($_GET['id']);

                //vemos si es que tiene plan de accion
                $action_plan = NULL;

                $action_plan = DB::table('action_plans')
                                ->where('issue_id','=',$_GET['id'])
                                ->select('id','stakeholder_id','description','final_date','status')
                                ->first();


                //vemos si es hallazgo de proceso, organización, u otro
                if ($issue['process_id'] != NULL)
                {
                    $processes = \Ermtool\Process::where('processes.status',0)
                                ->join('subprocesses','subprocesses.process_id','=','processes.id')
                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                ->lists('processes.name','processes.id');

                    $process_selected = $issue->process_id;

                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'processes'=>$processes,'process_selected' => $process_selected,'action_plan'=>$action_plan,'test_id' => $test_id, 'kind' => $_GET['kind']]);
                    }
                    else
                    {
                        return view('hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'processes'=>$processes,'process_selected' => $process_selected,'action_plan'=>$action_plan,'test_id' => $test_id, 'kind' => $_GET['kind']]);
                    }
                }
                else if ($issue['organization_id'] != NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'org_id'=>$_GET['org'],'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => $_GET['kind']]);
                    }
                    else
                    {
                        return view('hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'org_id'=>$_GET['org'],'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => $_GET['kind']]);
                    }
                }
                else
                {
                    if (!isset($_GET['kind'])) //hallazgos de control de evaluación y de pruebas no poseen "kind"
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => NULL]);
                        }
                        else
                        {
                            return view('hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => NULL]);
                        } 
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => $_GET['kind']]);
                    }
                    else
                    {
                        return view('hallazgos.edit',['org'=>$org, 'org_id' => $org_id, 'issue' => $issue,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'test_id' => $test_id, 'eval_id' => $eval_id, 'kind' => $_GET['kind']]);
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //print_r($_POST);
                //actualizamos issue de id = $id
                global $id2;
                $id2 = $id;
                global $req;
                $req = $request;
                global $evidence;
                $evidence = $request->file('evidence_doc2');
                DB::transaction(function() {
                    $logger = $this->logger;
                    //vemos si el plan se mandó cerrado o abierto y damos formato a campos de plan de acción
                    if (isset($_POST['status']))
                    {
                        $status = 1;

                        if ($_POST['description_plan2'] != "")
                        {
                            $description_plan = eliminarSaltos($_POST['description_plan2']);
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
                            $description_plan = eliminarSaltos($_POST['description_plan']);
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
                        $description = eliminarSaltos($description);
                    }
                    else
                    {
                        $description = NULL;
                    }

                    if (isset($_POST['recommendations']) AND $_POST['recommendations'] != "")
                    {
                        $recommendations = $_POST['recommendations'];
                        $recommendations = eliminarSaltos($recommendations);
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
                    
                    $id_action_plan = \Ermtool\Action_plan::getActionPlanFromIssue($GLOBALS['id2']);
                    
                    if ($id_action_plan)
                    {
                        $id_action_plan = $id_action_plan->id;

                        //actualizamos action_plan de issue_id = $id
                        DB::table('action_plans')->where('id','=',$id_action_plan)
                            ->update([
                                'description' => $description_plan,
                                'stakeholder_id' => $stakeholder_id,
                                'final_date' => $final_date,
                                'status' => $status,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    }
                    else
                    {
                        $id_action_plan = DB::table('action_plans')
                                            ->insertGetId([
                                                'issue_id' => $GLOBALS['id2'],
                                                'description' => $description_plan,
                                                'stakeholder_id' => $stakeholder_id,
                                                'final_date' => $final_date,
                                                'status' => $status,
                                                'updated_at' => date('Y-m-d H:i:s')
                                            ]);
                    }
                    
                    
                    //agregamos evidencia (si es que existe)
                    if ($GLOBALS['req']->file('evidence_doc') != NULL)
                    {
                        foreach ($GLOBALS['req']->file('evidence_doc') as $file)
                        {
                            if ($file != NULL)
                            {
                                upload_file($file,'evidencias_hallazgos',$GLOBALS['id2']); 
                            }
                        }
                    }

                    //agregamos evidencia de plan de acción (si es que existe)
                    if($GLOBALS['evidence'] != NULL)
                    {
                        foreach ($GLOBALS['evidence'] as $evidence)
                        {
                            if ($evidence != NULL)
                            {
                                upload_file($evidence,'planes_accion',$id_action_plan);
                            }
                        }                    
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Issue and action plan successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Hallazgo y plan de acción actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el hallazgo con Id: '.$GLOBALS['id2'].' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                });
                
                if (isset($_POST['test_id']))
                {
                    return Redirect::to('hallazgos_test.'.$_POST['test_id']);
                }
                else if (isset($_POST['kind']))
                {
                    return Redirect::to('hallazgos_lista?organization_id='.$_POST['organization_id'].'&kind='.$_POST['kind']);
                }
                else
                {
                    return Redirect::to('hallazgos');
                }        
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                global $id1;
                $id1 = $id;
                global $res;
                $res = 1;
                DB::transaction(function() {
                    $logger = $this->logger;
                    $name = DB::table('issues')->where('id',$GLOBALS['id1'])->value('name');
                    //primero que todo, eliminamos plan de acción (si es que hay)
                    DB::table('action_plans')
                    ->where('issue_id','=',$GLOBALS['id1'])
                    ->delete();

                    //ahora eliminamos issue
                    DB::table('issues')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                    //eliminamos evidencia si es que existe (SE DEBE AGREGAR)
                    eliminarArchivo($GLOBALS['id1'],2,NULL);

                    $GLOBALS['res'] = 0;

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el hallazgo con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return $res;
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

     //función para reporte de hallazgos
    public function issuesReport()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.hallazgos',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.hallazgos',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //reporte de issues
    public function generarReporteIssues()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //volvemos a obtener lista de organizaciones
                $organizations = \Ermtool\Organization::lists('name','id');

                //obtenemos nombre de organización
                $org = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');

                $org_id = $_GET['organization_id'];
                
                $issues = array();

                //ACTUALIZAR 09-03-17: YA QUE SE DEBERÍA PODER FILTRAR POR "SECOND SELECT" COMO EN INDEX2
                $issues = $this->getIssues($_GET['kind'],NULL,$_GET['organization_id'],2);
                //print_r($_POST);
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.hallazgos',['issues'=>$issues,'kind'=>$_GET['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
                }
                else
                {
                    return view('reportes.hallazgos',['issues'=>$issues,'kind'=>$_GET['kind'],'organizations'=>$organizations,'org'=>$org,'org_id'=>$org_id]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarReporteIssuesExcel($kind,$org)
    {
        try
        {
            //ACTUALIZAR 09-03-17: YA QUE SE DEBERÍA PODER FILTRAR POR "SECOND SELECT" COMO EN INDEX2
            $issues = $this->getIssues($kind,NULL,$org,2);

            return $issues;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

}
