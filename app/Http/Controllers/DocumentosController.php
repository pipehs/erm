<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;

use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Storage;
use Auth;
use DateTime;

class DocumentosController extends Controller
{
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
                return Redirect::route('/');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                return view('documentos.index',['organizations' => $organizations]);
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
        try
        {
            //print_r($_GET);
            switch ($_GET['kind']) 
            {
                case 1: //archivos de controles
                    //if ($_GET['control_type'] == 0)
                    //{
                        //$controls = \Ermtool\Control::getProcessesControls($_GET['organization_id']);
                        //ACTUALIZACIÓN 14-12-16: SE MOSTRARÁN LOS ARCHIVOS DE UN SÓLO CONTROL
                    //}
                    //else if ($_GET['control_type'] == 1)
                    //{
                        //$controls = \Ermtool\Control::getBussinessControls($_GET['organization_id']);
                        //ACTUALIZACIÓN 14-12-16: SE MOSTRARÁN LOS ARCHIVOS DE UN SÓLO CONTROL
                    //}

                    $control = \Ermtool\Control::find($_GET['control_id']);
                    
                    //$i = 0;

                    $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                    //recorremos los controles para ver cuales tienen archivos
                    //foreach ($controls as $control)
                    //{
                    //ACT 24-04-18: Ahora se buscará control_org_id
                    $co = \Ermtool\ControlOrganization::getByCO($_GET['control_id'],$_GET['organization_id']);
                    $files = Storage::files('controles_org/'.$co->id);
                    //$files = Storage::files('controles/'.$control->id);
                        //vemos si existe la carpeta (si existe es porque tiene archivos)
                    $risks = \Ermtool\Risk::getRisksFromControl($_GET['organization_id'],$control->id);

                    //}
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.documentos.show',['control' => $control,'kind' => $_GET['kind'], 'control_type' => $_GET['control_type'],'org_name' => $org_name,'risks' => $risks, 'files' => $files,'co' => $co]);
                    }
                    else
                    {
                        return view('documentos.show',['control' => $control,'kind' => $_GET['kind'], 'control_type' => $_GET['control_type'],'org_name' => $org_name,'risks' => $risks,'files' => $files,'co' => $co]);
                    }
                    break;
                case 2: //hallazgos
                    //$files = Storage::files('evidencias_hallazgos');

                    //obtenemos id de los issues que son del tipo "kind_issue"
                    switch ($_GET['kind_issue'] ) 
                    {
                        case 0: //obtenemos issues de proceso
                            //ACTUALIZACIÓN 09-03-17: SE AGREGA UN NULL POR SI SE SELECCIONA UN TIPO
                            $processes = \Ermtool\Process::getProcessFromIssues($_GET['organization_id'],NULL);
                            $process_issues = array(); //se guardaran los procesos que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($processes as $process)
                            {
                                //obtenemos issues del proceso
                                $issues = \Ermtool\Issue::getProcessIssues($process->id);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos

                                $action_plans = array();
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos procesos que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $process_issues[$i] = [
                                        'name' => $process->name,
                                        'description' => $process->description,
                                        'issues' => $issues2,
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $process_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $process_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        case 1: //issues de subprocesos
                            $subprocesses = \Ermtool\Subprocess::getSubprocessFromIssues($_GET['organization_id'],NULL);
                            $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                            $subprocess_issues = array(); //se guardaran los subprocesos que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($subprocesses as $subprocess)
                            {
                                //obtenemos issues del proceso
                                $issues = \Ermtool\Issue::getSubprocessIssuesBySubprocess($subprocess->id);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos subprocesos que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $subprocess_issues[$i] = [
                                        'name' => $subprocess->name,
                                        'description' => $subprocess->description,
                                        'issues' => $issues2,
                                        'process' => $subprocess->process_name
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $subprocess_issues,'kind2' => $_GET['kind_issue'],'org_name' => $org_name]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $subprocess_issues,'kind2' => $_GET['kind_issue'],'org_name' => $org_name]);
                            }
                            break;
                        case 2: //issues de organización
                            //ACT 31-05-18: Todos los issues tienen organization_id. Se debe buscar que sea todo lo demás NULL
                            $issues = \Ermtool\Issue::getOrganizationIssues($_GET['organization_id']);

                            $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                            $org_description = \Ermtool\Organization::description($_GET['organization_id']);

                            $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                            $j = 0;
                            foreach ($issues as $issue)
                            {
                                    //obtenemos plan de acción del issue
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                            }

                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['issues' => $issues2,'org_name'=>$org_name,'org_description' => $org_description,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['issues' => $issues2,'org_name'=>$org_name,'org_description' => $org_description,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        case 3: //issues de control de proceso
                            $controls = \Ermtool\Control::getProcessesControlsFromIssues($_GET['organization_id'],NULL);

                            $control_issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($controls as $control)
                            {
                                //obtenemos issues del control
                                $issues = \Ermtool\Issue::getControlIssues($control->id,$_GET['organization_id']);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    $action_plan = \Ermtool\Action_plan::getActionPlanFromIssue($issue->id);
                                    //obtenemos files del plan de acción
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos controles que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $control_issues[$i] = [
                                        'name' => $control->name,
                                        'description' => $control->description,
                                        'issues' => $issues2
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $control_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $control_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        case 4: //issues de control de entidad
                            $controls = \Ermtool\Control::getObjectivesControlsFromIssues($_GET['organization_id'],NULL);

                            $control_issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($controls as $control)
                            {
                                //obtenemos issues del control
                                $issues = \Ermtool\Issue::getControlIssues($control->id,$_GET['organization_id']);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    $action_plan = \Ermtool\Action_plan::getActionPlanFromIssue($issue->id);
                                    //obtenemos files del plan de acción
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos controles que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $control_issues[$i] = [
                                        'name' => $control->name,
                                        'description' => $control->description,
                                        'issues' => $issues2
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $control_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $control_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        case 5: //issues de programas de auditoría
                            //(audit_audit_plan_audit_program)
                            $audit_programs = \Ermtool\Audit_program::getProgramsFromIssues($_GET['organization_id'],NULL);

                            $audit_program_issues = array(); //se guardaran los programas que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($audit_programs as $audit_program)
                            {
                                //obtenemos issues del programa
                                $issues = \Ermtool\Issue::getAuditProgramIssues($audit_program->id);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    $action_plan = \Ermtool\Action_plan::getActionPlanFromIssue($issue->id);
                                    //obtenemos files del plan de acción
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos controles que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $audit_program_issues[$i] = [
                                        'name' => $audit_program->name,
                                        'description' => $audit_program->description,
                                        'issues' => $issues2
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $audit_program_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $audit_program_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        case 6: //issues de auditoría
                            //(audit_audit_plan)
                            $audits = \Ermtool\Audit::getAuditsFromIssues($_GET['organization_id'],NULL);

                            $audit_issues = array(); //se guardaran las auditorías que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($audits as $audit)
                            {
                                //obtenemos issues de la auditoría
                                $issues = \Ermtool\Issue::getAuditIssues($audit->id);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    $action_plan = \Ermtool\Action_plan::getActionPlanFromIssue($issue->id);
                                    //obtenemos files del plan de acción
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos controles que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $audit_issues[$i] = [
                                        'audit_plan' => $audit->audit_plan,
                                        'name' => $audit->name,
                                        'description' => $audit->description,
                                        'issues' => $issues2
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $audit_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $audit_issues,'kind2' => $_GET['kind_issue']]);
                            }
                            break;
                        //ACT 01-06-18: Agregado opción hallazgos de Riesgo
                        case 7: //issues de Riesgos
                            $risks = \Ermtool\Risk::getrisksFromIssues($_GET['organization_id']);

                            $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                            $risk_issues = array(); //se guardaran los subprocesos que tienen issues que además tienen documentos
                            $i = 0;
                            foreach ($risks as $risk)
                            {
                                //obtenemos issues del riesgo
                                $issues = \Ermtool\Issue::getRiskIssuesByRisk($risk->id);

                                $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                                //recorremos los issues para ver por cada uno si posee archivos
                                $j = 0;
                                foreach ($issues as $issue)
                                {
                                    //obtenemos plan de acción del issue
                                    //ACT 31-05-18: Obtenemos todos los planes de acción (puede haber más de uno)
                                    $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                    //obtenemos files del plan de acción
                                    if (!empty($action_plans))
                                    {
                                        foreach ($action_plans as $ap)
                                        {
                                            $files2 = Storage::files('planes_accion/'.$ap->id);

                                            $ap->files = $files2;
                                        }
                                    }

                                    $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                                    if ($files != NULL)
                                    {
                                        $issues2[$j] = [
                                            'id' => $issue->id,
                                            'name' => $issue->name,
                                            'description' => $issue->description,
                                            'classification' => $issue->classification,
                                            'recommendations' => $issue->recommendations,
                                            'files' => $files,
                                            'action_plans' => $action_plans
                                        ];

                                        $j += 1;
                                    }
                                }

                                //ahora guardamos solo aquellos subprocesos que tienen documentos asociados
                                if (!empty($issues2))
                                {
                                    $risk_issues[$i] = [
                                        'name' => $risk->name,
                                        'description' => $risk->description,
                                        'issues' => $issues2,
                                    ];

                                    $i += 1;
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                return view('en.documentos.show',['elements' => $risk_issues,'kind2' => $_GET['kind_issue'],'org_name' => $org_name]);
                            }
                            else
                            {
                                return view('documentos.show',['elements' => $risk_issues,'kind2' => $_GET['kind_issue'],'org_name' => $org_name]);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                case 3: //notas
                    $notes = \Ermtool\Note::getNotes($_GET['organization_id'],$_GET['audit_plan_id']);

                    $i = 0;
                    //recorremos las notas para ver cuales tienen archivos
                    $notes2 = array();
                    foreach ($notes as $note)
                    {
                        //obtenemos posibles respuestas
                        $answers = \Ermtool\Note::find($note->id)->notes_answers;
                        $j = 0;
                        $answers2 = array();
                        foreach ($answers as $ans)
                        {
                            $files1 = Storage::files('evidencias_resp_notas/'.$ans->id);

                            if ($files1 != NULL)
                            {
                                //seteamos fecha
                                //$created_at = date_format($ans['created_at'], 'd-m-Y');
                                $lala = new DateTime($ans['created_at']);
                                $created_at = date_format($lala,"d-m-Y");
                                
                                $answers2[$j] = [
                                    'id' => $ans['id'],
                                    'answer' => $ans['answer'],
                                    'created_at' => $created_at,
                                    'files' => $files1
                                ];

                                $j += 1;
                            }
                        }

                        $files = Storage::files('evidencias_notas/'.$note->id);
                        //vemos si existe la carpeta (si existe es porque tiene archivos)
                        if ($files != NULL)
                        {
                            $notes2[$i] = [
                                'id' => $note->id,
                                'name' => $note->name,
                                'description' => $note->description,
                                'files' => $files,
                                'answers' => $answers2,
                            ];

                            $i += 1;
                        }
                        //puede ser que la respuesta tenga archivos

                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.documentos.show',['elements' => $notes2,'kind' => $_GET['kind']]);
                    }
                    else
                    {
                        return view('documentos.show',['elements' => $notes2,'kind' => $_GET['kind']]);
                    }
                    break;
                case 4: //programas
                    $programs = \Ermtool\Audit_program::getPrograms($_GET['organization_id'],$_GET['audit_plan_id']);
                    $plan = \Ermtool\Audit_plan::name($_GET['audit_plan_id']);
                    $i = 0;
                    //recorremos los programas para ver cuales tienen archivos
                    $programs2 = array();
                    foreach ($programs as $program)
                    {
                        $files = Storage::files('programas_auditoria/'.$program->id);
                        //vemos si existe la carpeta (si existe es porque tiene archivos)
                        if ($files != NULL)
                        {
                            $programs2[$i] = [
                                'id' => $program->id,
                                'audit' => $program->audit,
                                'name' => $program->name,
                                'description' => $program->description,
                                'files' => $files,
                            ];

                            $i += 1;
                        }

                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.documentos.show',['elements' => $programs2,'kind' => $_GET['kind'], 'audit_plan' => $plan]);
                    }
                    else
                    {
                        return view('documentos.show',['elements' => $programs2,'kind' => $_GET['kind'], 'audit_plan' => $plan]);
                    }
                    break;
                case 5: //pruebas
                    $tests = \Ermtool\Audit_test::getTests($_GET['organization_id'],$_GET['audit_plan_id']);
                    $plan = \Ermtool\Audit_plan::name($_GET['audit_plan_id']);
                    $i = 0;
                    //recorremos los programas para ver cuales tienen archivos
                    $tests2 = array();
                    foreach ($tests as $test)
                    {
                        $files = Storage::files('pruebas_auditoria/'.$test->id);
                        //vemos si existe la carpeta (si existe es porque tiene archivos)
                        if ($files != NULL)
                        {
                            $tests2[$i] = [
                                'id' => $test->id,
                                'audit' => $test->audit_name,
                                'program' => $test->audit_program_name,
                                'name' => $test->name,
                                'description' => $test->description,
                                'files' => $files,
                            ];

                            $i += 1;
                        }

                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.documentos.show',['elements' => $tests2,'kind' => $_GET['kind'], 'audit_plan' => $plan]);
                    }
                    else
                    {
                        return view('documentos.show',['elements' => $tests2,'kind' => $_GET['kind'], 'audit_plan' => $plan]);
                    }
                    break;
                case 6: //riesgos

                    //ACT 01-06-18: Ahora se guardará organization_risk en evidencia (eso se envía en risk_id)
                    //Obtenemos datos de riesgo por organization_risk
                    $risk = \Ermtool\Risk::getRiskByOrgRisk($_GET['risk_id']);
                    //$i = 0;

                    $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                    //recorremos los controles para ver cuales tienen archivos
                    //foreach ($controls as $control)
                    //{
                    $files = Storage::files('riesgos/'.$_GET['risk_id']);
                        //vemos si existe la carpeta (si existe es porque tiene archivos)
                    $controls = \Ermtool\Control::getControlsFromRisk($_GET['organization_id'],$risk->id);

                    //}
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.documentos.show',['risk' => $risk,'kind' => $_GET['kind'], 'risk_type' => $_GET['risk_type'],'org_name' => $org_name,'controls' => $controls, 'files' => $files]);
                    }
                    else
                    {
                        return view('documentos.show',['risk' => $risk,'kind' => $_GET['kind'], 'risk_type' => $_GET['risk_type'],'org_name' => $org_name,'controls' => $controls, 'files' => $files]);
                    }
                    break;
                default:
                    break;
            }
        }
        catch (\Exception $e)
        {
            //enviarMailSoporte($e);
            //return view('errors.query',['e' => $e]);
            print_r($e);
        }     
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

    public function storeFile()
    {
        print_r($_POST);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //ACT 05-04-17: Al parecer no es necesario (existe función eliminarArchivos en helpers)
    public function deleteFiles($dir,$id,$file)
    {
        try
        {
            global $dir1;
            global $id1;
            global $file1;
            $dir1 = $dir;
            $id1 = $id;
            $file1 = $file;
            DB::transaction(function() {

            });
            //obtenemos todos los archivos de la carpeta $dir
            //ACTUALIZACIÓN 03-04-17: Además seleccionamos carpeta $id
            $files = Storage::files($GLOBALS['dir1'].'/'.$GLOBALS['id1']);

            //ahora buscamos los que terminen en $id
            if ($GLOBALS['file1'] == NULL) //eliminamos todos los archivos asociados
            {
                foreach ($files as $file)
                {
                    Storage::delete($file);
                }
            }
            else
            {
                foreach ($files as $file)
                {
                    
                    if ($file == $GLOBALS['dir'].'/'.$GLOBALS['id1'].'/'.$GLOBALS['file1'])
                    {
                        Storage::delete($file);
                    }
                    /*
                    //separamos id del archivo
                    $temp = explode('/',$file);

                    //ahora separamos id de la extensión del archivo (si es que hay extensión, sino sólo se elimina)
                    if (strpos($temp[1],'.'))
                    {
                        $temp = explode('.',$temp[1]);

                        //ahora vemos si el id corresponde
                        if ($temp[0] == $id)
                        {
                            Storage::delete($file);
                        }
                    }
                    else
                    {
                        //vemos si el id corresponde
                        if ($temp[1] == $id)
                        {
                            Storage::delete($file);
                        }
                    } */ 
                }
            }
            return 0;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
