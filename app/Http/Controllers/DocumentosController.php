<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;

use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Storage;
use Auth;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            return view('documentos.index',['organizations' => $organizations]);
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
        //print_r($_GET);


        switch ($_GET['kind']) 
        {
            case 1: //archivos de controles
                if ($_GET['control_type'] == 0)
                {
                    $controls = \Ermtool\Control::getProcessesControls($_GET['organization_id']);
                }
                else if ($_GET['control_type'] == 1)
                {
                    $controls = \Ermtool\Control::getBussinessControls($_GET['organization_id']);
                }
                
                $i = 0;

                $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                //recorremos los controles para ver cuales tienen archivos
                $controls2 = array();
                foreach ($controls as $control)
                {
                    $files = Storage::files('controles/'.$control->id);
                    //vemos si existe la carpeta (si existe es porque tiene archivos)
                    if ($files != NULL)
                    {
                        //obtenemos los riesgos asociados a este control
                        $risks = \Ermtool\Risk::getRisksFromControl($control->id,$_GET['control_type']);

                        $controls2[$i] = [
                            'name' => $control->name,
                            'description' => $control->description,
                            'risks' => $risks,
                            'files' => $files,
                        ];

                        $i += 1;
                    }

                }
                if (Session::get('languaje') == 'en')
                {
                    return view('en.documentos.show',['elements' => $controls2,'kind' => $_GET['kind'], 'control_type' => $_GET['control_type'],'org_name' => $org_name]);
                }
                else
                {
                    return view('documentos.show',['elements' => $controls2,'kind' => $_GET['kind'], 'control_type' => $_GET['control_type'],'org_name' => $org_name]);
                }
                break;
            case 2: //hallazgos
                //$files = Storage::files('evidencias_hallazgos');

                //obtenemos id de los issues que son del tipo "kind_issue"
                switch ($_GET['kind_issue'] ) 
                {
                    case 0: //obtenemos issues de proceso
                        $processes = \Ermtool\Process::getProcessFromIssues($_GET['organization_id']);
                        $process_issues = array(); //se guardaran los procesos que tienen issues que además tienen documentos
                        $i = 0;
                        foreach ($processes as $process)
                        {
                            //obtenemos issues del proceso
                            $issues = \Ermtool\Issue::getProcessIssues($process->id);

                            $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                            //recorremos los issues para ver por cada uno si posee archivos
                            $j = 0;
                            foreach ($issues as $issue)
                            {
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
                                    ];

                                    $j += 1;
                                }
                                //else
                                //{
                                //    echo 'el issue ' . $issue->id . ' no tiene evidencia<br>';
                                //}
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
                        $subprocesses = \Ermtool\Subprocess::getSubprocessFromIssues($_GET['organization_id']);

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
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
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
                            return view('en.documentos.show',['elements' => $subprocess_issues,'kind2' => $_GET['kind_issue']]);
                        }
                        else
                        {
                            return view('documentos.show',['elements' => $subprocess_issues,'kind2' => $_GET['kind_issue']]);
                        }
                        break;
                    case 2: //issues de organización
                        $issues = \Ermtool\Organization::find($_GET['organization_id'])->issues;
                        $org_name = \Ermtool\Organization::name($_GET['organization_id']);
                        $org_description = \Ermtool\Organization::description($_GET['organization_id']);

                        $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                            //recorremos los issues para ver por cada uno si posee archivos
                        $j = 0;
                        foreach ($issues as $issue)
                        {
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue['name'],
                                        'description' => $issue['description'],
                                        'classification' => $issue['classification'],
                                        'recommendations' => $issue['recommendations'],
                                        'files' => $files,
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
                        $controls = \Ermtool\Control::getProcessesControlsFromIssues($_GET['organization_id']);

                        $controls_issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            //obtenemos issues del control
                            $issues = \Ermtool\Issue::getControlIssues($control->id);

                            $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                            //recorremos los issues para ver por cada uno si posee archivos
                            $j = 0;
                            foreach ($issues as $issue)
                            {
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
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
                        $controls = \Ermtool\Control::getObjectivesControlsFromIssues($_GET['organization_id']);

                        $control_issues = array(); //se guardaran los controles que tienen issues que además tienen documentos
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            //obtenemos issues del control
                            $issues = \Ermtool\Issue::getControlIssues($control->id);

                            $issues2 = array(); //array donde se guardaran los issues que tienen documentos
                            //recorremos los issues para ver por cada uno si posee archivos
                            $j = 0;
                            foreach ($issues as $issue)
                            {
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
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
                        $audit_programs = \Ermtool\Audit_program::getProgramsFromIssues($_GET['organization_id']);

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
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
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
                        $audits = \Ermtool\Audit::getAuditsFromIssues($_GET['organization_id']);

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
                                $files = Storage::files('evidencias_hallazgos/'.$issue->id);
                                //vemos si existe la carpeta (si existe es porque tiene archivos)
                                if ($files != NULL)
                                {
                                    $issues2[$j] = [
                                        'name' => $issue->name,
                                        'description' => $issue->description,
                                        'classification' => $issue->classification,
                                        'recommendations' => $issue->recommendations,
                                        'files' => $files,
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
                            $created_at = date_format($ans['created_at'], 'd-m-Y');
                            $answers2[$j] = [
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
                        $notes2[$j] = [
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
                            'audit' => $test->audit,
                            'program' => $test->program,
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
            default:
                break;
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
    public function deleteFiles($dir,$id)
    {
        //obtenemos todos los archivos de la carpeta $dir
        $files = Storage::files($dir);

        //ahora buscamos los que terminen en $id
        foreach ($files as $file)
        {
            //separamos id del archivo
            $temp = explode('___',$file);

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
            }   
        }

        return 0;
    }
}
