<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use DateTime;
use Auth;
use stdClass;
use Mail;
use Ermtool\Http\Controllers\IssuesController as Issues;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class PlanesAccionController extends Controller
{
    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('planes_accion');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/planes_accion.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    //función que obtiene todos los planes de acción de una organización
    public function getActionPlans($id)
    {
        try
        {
            $i = 0;
            $action_plans = array();
                //primero obtenemos los planes de acción para los hallazgos que son directamente de la organización
                $planes = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->where('issues.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.organization_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.organization_id as org_id')
                            ->get();

                //ahora los planes de acción para los planes de auditoría que corresponden a la organización
                $planes2 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                            ->where('audit_plans.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.audit_audit_plan_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.audit_audit_plan_id as audit_plan_id')
                            ->get();

                //planes de accion asociados a programa de auditoría (que corresponde a un plan de auditoría asociado a una organización)
                $planes3 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','issues.audit_audit_plan_audit_program_id')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                            ->where('audit_plans.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.audit_audit_plan_audit_program_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.audit_audit_plan_audit_program_id as program_id')
                            ->get();

                //asociados a una prueba de un plan de auditoría
                $planes4 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                            ->where('audit_plans.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.audit_test_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.audit_test_id as test_id')
                            ->get();

                //planes de control de entidad asociados a la organización
                $planes5 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','issues.control_id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('organization_risk.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','control_organization_risk.control_id','controls.type2')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','control_organization_risk.control_id as control_organization_risk_id','controls.type2')
                            ->get();

                $planes6 = array(); //ACT 05-04 ya no se necesita diferenciar entre controles de entidad o de proceso ya que están todos dentro de planes5

                //planes asociados a un subproceso perteneciente a la organización
                $planes7 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('organization_subprocess.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.subprocess_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.subprocess_id as subprocess_id')
                            ->get(); 
                //planes asociados a un proceso perteneciente a la organización
                $planes8 = DB::table('issues')
                            ->join('action_plans','action_plans.issue_id','=','issues.id')
                            ->join('processes','processes.id','=','issues.process_id')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('organization_subprocess.organization_id','=',$id)
                            ->groupBy('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name','issues.process_id')
                            ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                    'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.process_id as process_id')
                            ->get(); 

                $plans = array_merge($planes,$planes2,$planes3,$planes4,$planes5,$planes6,$planes7,$planes8);

                foreach ($plans as $plan)
                {
                    if ($plan->final_date != NULL)
                    {
                        $final_date = new DateTime($plan->final_date);
                        $final_date = date_format($final_date,"d-m-Y");
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $final_date = 'Final date is not defined';
                        }
                        else
                        {
                            $final_date = 'No se ha definido fecha final';
                        }
                    }

                    
                    if (Session::get('languaje') == 'en')
                    {
                        //obtenemos datos de responsable
                        if ($plan->stakeholder_id != NULL)
                        {
                            $resp = \Ermtool\Stakeholder::find($plan->stakeholder_id);
                            $resp_mail = $resp->mail;
                            $resp = $resp->name.' '.$resp->surnames;
                        }
                        else
                        {
                            $resp = 'Responsable is not defined';
                            $resp_mail = 'Responsable is not defined';
                        }
                        if ($plan->status == 0)
                        {

                            $status = 'In progress';
                        }
                        else if ($plan->status == 1)
                        {
                            $status = 'Closed';
                        }
                        else if ($plan->status === NULL)
                        {
                            $status = 'Status is not defined';
                        }
                        //seteamos origen
                        if (isset($plan->org_id))
                        {
                            $origin = 'Organization issue';
                        }
                        else if (isset($plan->audit_plan_id))
                        {
                            $origin = 'Audit plan issue';
                        }
                        else if (isset($plan->program_id))
                        {
                            $origin = 'Audit program issue';
                        }
                        else if (isset($plan->test_id))
                        {
                            $origin = 'Audit test issue';
                        }
                        else if (isset($plan->type2) && $plan->type2 == 1)
                        {
                            $origin = 'Entity control issue';
                        }
                        else if (isset($plan->type2) && $plan->type2 == 0)
                        {
                            $origin = 'Process control issue';
                        }
                        else if (isset($plan->subprocess_id))
                        {
                            $origin = 'Subprocess issue';
                        }
                        else if (isset($plan->process_id))
                        {
                            $origin = 'Process issue';
                        }
                    }
                    else
                    {
                        //obtenemos datos de responsable
                        if ($plan->stakeholder_id != NULL)
                        {
                            $resp = \Ermtool\Stakeholder::find($plan->stakeholder_id);
                            $resp_mail = $resp->mail;
                            $resp = $resp->name.' '.$resp->surnames;
                        }
                        else
                        {
                            $resp = 'No se ha definido responsable';
                            $resp_mail = 'No se ha definido responsable';
                        }
                        if ($plan->status == 0)
                        {
                            $status = 'En progreso';
                        }
                        else if ($plan->status == 1)
                        {
                            $status = 'Cerrado';
                        }
                        else if ($plan->status === NULL)
                        {
                            $status = 'Estado no definido';
                        }
                        //seteamos origen
                        if (isset($plan->org_id))
                        {
                            $origin = 'Hallazgo de organización';
                        }
                        else if (isset($plan->audit_plan_id))
                        {
                            $origin = 'Hallazgo de plan de auditoría';
                        }
                        else if (isset($plan->program_id))
                        {
                            $origin = 'Hallazgo de programa de auditoría';
                        }
                        else if (isset($plan->test_id))
                        {
                            $origin = 'Hallazgo de prueba de auditoría';
                        }
                        else if (isset($plan->type2) && $plan->type2 == 1)
                        {
                            $origin = 'Hallazgo de control de entidad';
                        }
                        else if (isset($plan->type2) && $plan->type2 == 0)
                        {
                            $origin = 'Hallazgo de control de proceso';
                        }
                        else if (isset($plan->subprocess_id))
                        {
                            $origin = 'Hallazgo asociado a subproceso';
                        }
                        else if (isset($plan->process_id))
                        {
                            $origin = 'Hallazgo asociado a proceso';
                        }
                    }
                    if (strstr($_SERVER["REQUEST_URI"],'genexcelplan')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
                    {
                        $action_plans[$i] = [
                            'Origen del hallazgo' => $origin,
                            'Hallazgo' => $plan->issue,
                            'Descripción' => $plan->description,
                            'Responsable' => $resp,
                            'Correo responsable' => $resp_mail,
                            'Estado' => $status,
                            'Fecha final' => $final_date,
                        ];
                    }
                    else
                    {
                        $short_des = substr($plan->description,0,100);

                        $action_plans[$i] = [
                            'origin' => $origin,
                            'id' => $plan->id,
                            'issue' => $plan->issue,
                            'description' => $plan->description,
                            'stakeholder' => $resp,
                            'stakeholder_mail' => $resp_mail,
                            'final_date' => $final_date,
                            'status' => $status,
                            'status_number' => $plan->status,
                            'short_des' => $short_des,
                        ];
                    }

                    $i += 1;
                    //
                }

            return $action_plans;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función que obtiene planes de acción de auditoría
    public function getActionPlanAudit($org)
    {
        try
        {
            if ($org != NULL)
            {
                //obtenemos datos de plan de auditoría, auditoría, issue y plan de acción
                $action_plans = DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->whereNotNull('issues.audit_test_id')
                    ->select('audit_plans.name as audit_plan_name',
                             'audits.name as audit_name',
                             'audit_programs.name as program_name',
                             'audit_tests.name as test_name',
                             'issues.name as issue_name',
                             'issues.recommendations',
                             'action_plans.id',
                             'action_plans.description',
                             'action_plans.final_date',
                             'action_plans.updated_at',
                             'action_plans.status',
                             'action_plans.created_at',
                             'stakeholders.name as user_name',
                             'stakeholders.surnames as user_surnames')
                    ->get();;
            }
            else
            {
                //obtenemos datos de plan de auditoría, auditoría, issue y plan de acción
                $action_plans = DB::table('action_plans')
                    ->join('issues','issues.id','=','action_plans.issue_id')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                    ->whereNotNull('issues.audit_test_id')
                    ->select('audit_plans.name as audit_plan_name',
                             'audits.name as audit_name',
                             'audit_programs.name as program_name',
                             'audit_tests.name as test_name',
                             'issues.name as issue_name',
                             'issues.recommendations',
                             'action_plans.id',
                             'action_plans.description',
                             'action_plans.final_date',
                             'action_plans.updated_at',
                             'action_plans.status',
                             'action_plans.created_at',
                             'stakeholders.name as user_name',
                             'stakeholders.surnames as user_surnames')
                    ->get();;
            }

            return $action_plans;
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
                    return view('en.planes_accion.index',['organizations'=>$organizations]);
                }
                else
                {
                    return view('planes_accion.index',['organizations'=>$organizations]);
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
                $id = $_GET['organization_id'];

                $org = \Ermtool\Organization::where('id',$id)->value('name');
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                
                //ACTUALIZACIÓN 25-08-2016: Función extraida para que esté disponible en el mantenedor y en el reporte de planes de acción
                $action_plans = $this->getActionPlans($id);
                //print_r($_GET);
                if (Session::get('languaje') == 'en')
                {
                    return view('en.planes_accion.index',['action_plans'=>$action_plans,'organizations' => $organizations, 'org' => $org, 'org_id' => $id]);
                }
                else
                {
                    return view('planes_accion.index',['action_plans'=>$action_plans,'organizations' => $organizations, 'org' => $org, 'org_id' => $id]);
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
    public function create($org)
    {
        try
        {
            $org_name = \Ermtool\Organization::where('id',$org)->value('name');

            //obtenemos stakeholders de la misma organización
            $stakes = \Ermtool\Stakeholder::listStakeholders($org);

            if (Session::get('languaje') == 'en')
            {
                return view('en.planes_accion.create',['org' => $org_name, 'org_id' => $org, 'stakeholders' => $stakes]);
            }
            else
            {
                return view('planes_accion.create',['org' => $org_name, 'org_id' => $org, 'stakeholders' => $stakes]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función para obtener issues a través de Json
    public function getIssues($kind,$org)
    {
        try
        {
            $issues = array();
            //encontramos hallazgos de organización
            $i = new Issues;

            $issues_temp = $i->getIssues($kind,NULL,$org,NULL);

            //dentro de estas issues, vemos cuales ya tienen planes de acción y las omitimos
            $i = 0;
            foreach ($issues_temp as $is)
            {
                //obtenemos posible plan de acción de issue
                $plan = \Ermtool\Action_plan::getActionPlanFromIssue($is['id']);

                if (empty($plan)) //significa que no tiene plan, por lo que se puede crear
                {
                    $issues[$i] = [
                        'id' => $is['id'],
                        'name' => $is['name'],
                    ];

                    $i += 1;
                }
            }

            return json_encode($issues);
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
    public function store($issue_id,$description,$stakeholder,$final_date)
    {
        try
        {
            $logger = $this->logger;

            $new_plan = \Ermtool\Action_plan::create([
                            'issue_id' => $issue_id,
                            'description' => $description,
                            'stakeholder_id' => $stakeholder,
                            'final_date' => $final_date,
                            'status' => 0,
                        ]);

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el plan de acción con Id: '.$new_plan->id.' definido como: '.$new_plan->description.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

            return $new_plan;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //guardado a través de mantenedor de planes de acción
    public function store2(Request $request)
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
                global $evidence;
                $evidence = $request->file('evidence_doc');
                DB::transaction(function() {
                    $logger = $this->logger;
                    $status = 0;

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

                    $action_plan = \Ermtool\Action_plan::create([
                            'issue_id' => $_POST['issue_id'],
                            'description' => $description,
                            'stakeholder_id' => $stakeholder_id,
                            'final_date' => $final_date,
                            'status' => $status,

                        ]);

                    if($GLOBALS['evidence'] != NULL)
                    {
                        foreach ($GLOBALS['evidence'] as $evidence)
                        {
                            if ($evidence != NULL)
                            {
                                upload_file($evidence,'planes_accion',$action_plan->id);
                            }
                        }                    
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Action plan successfully created');
                    }
                    else
                    {
                        Session::flash('message','Plan de acción creado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el plan de acción con Id: '.$action_plan->id.' definido como: '.$action_plan->description.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                });

                return Redirect::to('action_plans2?organization_id='.$_POST['org_id']);
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
    public function edit($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {   
                $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');
                $org_id = \Ermtool\Organization::where('id',$_GET['org'])->value('id');

                //obtenemos stakeholders de la misma organización
                $stakes = \Ermtool\Stakeholder::listStakeholders($_GET['org']);

                $action_plan = \Ermtool\Action_plan::find($id);

                //obtenemos todos los issues y el issue del plan de acción
                $issue = DB::table('issues')
                        ->where('id','=',$action_plan->issue_id)
                        ->select('id','name')
                        ->first();

                $issues = \Ermtool\Issue::lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.planes_accion.edit',['org'=>$org, 'org_id' => $org_id, 'action_plan' => $action_plan,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'issues' => $issues, 'issue' => $issue]);
                }
                else
                {
                    return view('planes_accion.edit',['org'=>$org, 'org_id' => $org_id, 'action_plan' => $action_plan,'stakeholders'=>$stakes,'action_plan'=>$action_plan,'issues' => $issues, 'issue' => $issue]);
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
                global $evidence;
                $evidence = $request->file('evidence_doc');
                DB::transaction(function() {
                    $logger = $this->logger;
                    $status = 0;

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

                    //actualizamos action_plan de issue_id = $id
                    $action_plan = \Ermtool\Action_plan::find($GLOBALS['id2']);
                    $action_plan->description = $description;
                    $action_plan->stakeholder_id = $stakeholder_id;
                    $action_plan->final_date = $final_date;
                    $action_plan->status = $status;
                    $action_plan->save();


                    if($GLOBALS['evidence'] != NULL)
                    {
                        foreach ($GLOBALS['evidence'] as $evidence)
                        {
                            if ($evidence != NULL)
                            {
                                upload_file($evidence,'planes_accion',$GLOBALS['id2']);
                            }
                        }                    
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Action plan successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Plan de acción actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el plan de acción con Id: '.$action_plan->id.' definido como: '.$action_plan->description.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                });

                return Redirect::to('action_plans2?organization_id='.$_POST['org_id']);
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
            //action plan está enlazado con stakeholders e issues, pero ninguno de ellos depende de éste, por lo que simplemente se borarrá
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

                    //obtenemos nombre (descripción)
                    $description = DB::table('action_plans')->where('id',$GLOBALS['id1'])->value('description');
                    //primero que todo, eliminamos plan de acción (si es que hay)
                    DB::table('action_plans')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                    $GLOBALS['res'] = 0;

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el plan de acción con Id: '.$GLOBALS['id1'].' definido como: '.$description.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    //eliminamos evidencia si es que existe (SE DEBE AGREGAR)
                    eliminarArchivo($GLOBALS['id1'],5,NULL);
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

    //obtiene plan de acción para un issue dado
    public function getActionPlan($id)
    {
        try
        {
            $results = array();
            //obtenemos action plan
            $action_plan = DB::table('action_plans')
                        ->where('action_plans.issue_id','=',$id)
                        ->select('id','description','final_date','stakeholder_id','created_at')
                        ->get();

            if ($action_plan == NULL)
            {
                $results = NULL;
            }

            else
            {
                foreach ($action_plan as $ap) //aunque solo existirá uno
                {
                    //obtenemos stakeholder
                    if ($ap->stakeholder_id == NULL)
                    {
                        $stakeholder = NULL;
                        $rut = NULL;
                    }
                    else
                    {
                        $stakeholder_temp = \Ermtool\stakeholder::find($ap->stakeholder_id);
                        $stakeholder = $stakeholder_temp->name.' '.$stakeholder_temp->surnames;
                        $rut = $stakeholder_temp->id;
                    }
                    $results = [
                        'id' => $ap->id,
                        'description' => $ap->description,
                        'final_date' => $ap->final_date,
                        'stakeholder' => $stakeholder,
                        'rut' => $rut,
                        'created_at' => $ap->created_at
                    ];
                }
            }
            
            return json_encode($results);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función para reporte de planes de acción
    public function actionPlansReport()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.planes_accion',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.planes_accion',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACTUALIZACIÓN 25-08: Se deben mostrar los planes de acción para todos los tipos de hallazgo del sistema
    public function generarReportePlanes($org)
    {
        try
        {
            $results = array();
            $i = 0;
            
            if (strstr($_SERVER["REQUEST_URI"],'genexcelplan'))
            {
                $org = $org;
            }
            else
            {
                $org = $_GET['organization'];
            }
            //$action_plans = $this->getActionPlanAudit($org);
                
            //ACTUALIZACIÓN 25-08-2016: Función extraida para que esté disponible en el mantenedor y en el reporte de planes de acción
            $results = $this->getActionPlans($org);

            if (strstr($_SERVER["REQUEST_URI"],'genexcelplan')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
            {
                return $results;
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.planes_accion',['action_plans' => $results,'org_id' => $org]);
                }
                else
                {
                    return view('reportes.planes_accion',['action_plans' => $results,'org_id' => $org]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function indexGraficos()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.planes_accion_graficos',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.planes_accion_graficos',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function indexGraficos2($value,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //datos para gráfico de hallazgos
                $issues_om = array();
                $issues_def = array();
                $issues_deb = array();
                $op_mejora = 0;
                $deficiencia = 0;
                $deb_significativa = 0;
                
                //ACT 12-12-16: Obtiene TODOS los issues de una organización (de los distintos tipos de issues)
                if ($org == 0)
                {
                    $issues_all = \Ermtool\Issue::getIssues($_GET['organization_id']);
                }
                else
                {
                    $issues_all = \Ermtool\Issue::getIssues($org);
                }

                //ACTUALIZACIÓN 03-02: DE AQUÍ MISMO PODEMOS SACAR PLANES PRÓXIMOS A CERRAR
                $action_plans_closed = array(); //planes de acción cerrados
                $action_plans_warning = array(); //planes de acción próximos a cerrar
                $action_plans_danger = array(); //planes de acción pasados en fecha y aun abiertos
                $action_plans_open = array(); //planes de acción en los que la fecha de cierre es mayor a 2 meses

                $cont_open = 0;
                $cont_danger = 0;
                $cont_warning = 0;
                $cont_closed = 0;

                //ACTUALIZACIÓN 03-02-2017: En gráfico mostraremos todos los tipos distintos
                $action_plans_ctrl = array();
                $action_plans_audit_plan = array();
                $action_plans_program = array();
                $action_plans_audit = array();
                $action_plans_org = array();
                $action_plans_subprocess = array();
                $action_plans_process = array();
                $action_plans_process_ctrl = array();
                $action_plans_bussiness_ctrl = array();

                $cont_ctrl = 0;
                $cont_audit_plan = 0;
                $cont_program = 0;
                $cont_audit = 0;
                $cont_org = 0;
                $cont_subprocess = 0;
                $cont_process = 0;
                $cont_process_ctrl = 0;
                $cont_bussiness_ctrl = 0;
                $i = 0;
                foreach ($issues_all as $issue)
                {
                    //debemos obtener datos de plan de acción y responsable de plan de acción (si es que hay)
                    $action_plan = NULL;

                    $action_plan = DB::table('action_plans')
                                        ->where('issue_id','=',$issue->id)
                                        ->first(['id','description','final_date','status','stakeholder_id','updated_at']);

                    if ($action_plan != NULL)
                    {
                        if ($action_plan->stakeholder_id == NULL)
                        {
                            $user = new stdClass;
                            $user->name = "No definido";
                            $user->surnames = "";
                        }
                        else
                        {
                            //obtenemos nombre de responsable
                            $user = DB::table('stakeholders')
                                    ->where('id','=',$action_plan->stakeholder_id)
                                    ->first(['name','surnames']);
                        } 

                        if (Session::get('languaje') == 'en')
                        {
                            //seteamos status
                            if ($action_plan->status == 0)
                            {
                                $status = "In progress";
                            }
                            else if ($action_plan->status == 1)
                            {
                                $status = "Closed";
                            }
                            else
                            {
                                $status = "Is not defined";
                            }
                            if ($action_plan->final_date == '0000-00-00')
                            {
                                $final_date = "Error storing plan deadline";
                            }
                            else
                            {
                                //seteamos fecha final
                                $final_date_tmp = new DateTime($action_plan->final_date);
                                $final_date = date_format($final_date_tmp, 'd-m-Y');
                            }
                        }
                        else
                        {
                            //seteamos status
                            if ($action_plan->status === 0)
                            {
                                $status = "En progreso";
                            }
                            else if ($action_plan->status == 1)
                            {
                                $status = "Cerrado";
                            }
                            else
                            {
                                $status = "No está definido";
                            }
                            if ($action_plan->final_date == '0000-00-00')
                            {
                                $final_date = "Error al registrar fecha final";
                            }
                            else
                            {
                                //seteamos fecha final
                                $final_date_tmp = new DateTime($action_plan->final_date);
                                $final_date = date_format($final_date_tmp, 'd-m-Y');
                            }
                        }

                        $updated_at_tmp = new DateTime($action_plan->updated_at);
                        $updated_at = date_format($updated_at_tmp, 'd-m-Y');

                        //verificamos para tercer gráfico el tipo de control (abierto, proximo a cerrar, cerrado, falta mucho para que cierre...)

                        if ($action_plan->final_date != NULL || $action_plan->final_date == '0000-00-00')
                        {
                            $fecha_temp = explode('-',$action_plan->final_date); //obtenemos solo mes y año
                            $fecha_ano = (int)$fecha_temp[0] - (int)date('Y'); //obtenemos solo año
                            $fecha = (int)$fecha_temp[1] - (int)date('m'); //solo mes
                            $fecha_dia = (int)$fecha_temp[2] - (int)date('d'); //solo día
                        }
                        else //no se ha registrado fecha de cierre, así que por defecto dejaremos 31-12-9999
                        {
                            $fecha_ano = 9999 - (int)date('Y'); //año
                            $fecha = 12 - (int)date('m'); //mes
                            $fecha_dia = 31 - (int)date('d'); //día
                        }

                        if ($fecha_ano > 0)
                        {
                            if ($action_plan->status == 1) //closed
                            {
                                $cont_closed += 1;
                                $action_plans_closed[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                            else
                            {
                                $cont_open += 1;
                                $action_plans_open[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                            
                        }
                        else if ($fecha_ano == 0)
                        {
                            if ($fecha >= 2 && $action_plan->status == 0)
                            {
                                $cont_open += 1;

                                $action_plans_open[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                            else if ($fecha < 2 && $fecha >= 0 && $action_plan->status == 0) //warning
                            {
                                //verificamos día
                                if ($fecha_dia <= 0)
                                {
                                     $cont_danger += 1;

                                    $action_plans_danger[$i] = [
                                        'id' => $action_plan->id,
                                        'description' => $action_plan->description,
                                        'status' => $status,
                                        'final_date' => $final_date,
                                        'updated_at' => $updated_at,
                                        'stakeholder' => $user->name.' '.$user->surnames,
                                        'issue' => $issue->description,
                                        'recommendations' => $issue->recommendations,
                                    ];
                                }
                                else
                                {
                                    $cont_warning += 1;

                                    $action_plans_warning[$i] = [
                                        'id' => $action_plan->id,
                                        'description' => $action_plan->description,
                                        'status' => $status,
                                        'final_date' => $final_date,
                                        'updated_at' => $updated_at,
                                        'stakeholder' => $user->name.' '.$user->surnames,
                                        'issue' => $issue->description,
                                        'recommendations' => $issue->recommendations,
                                    ];
                                }  
                            }
                            else if ($fecha < 0 && $action_plan->status == 0) //danger
                            {
                                $cont_danger += 1;

                                $action_plans_danger[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                            else if ($action_plan->status == 1) //closed
                            {
                                $cont_closed += 1;

                                $action_plans_closed[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                        }
                        else //el año es menor, por lo que no se necesita hacer mas verificacion (excepto si es que esta cerrado)
                        {
                            if ($action_plan->status == 1) //closed
                            {
                                $cont_closed += 1;
                                $action_plans_closed[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                            else
                            {
                                $cont_danger += 1;
                                $action_plans_danger[$i] = [
                                    'id' => $action_plan->id,
                                    'description' => $action_plan->description,
                                    'status' => $status,
                                    'final_date' => $final_date,
                                    'updated_at' => $updated_at,
                                    'stakeholder' => $user->name.' '.$user->surnames,
                                    'issue' => $issue->description,
                                    'recommendations' => $issue->recommendations,
                                ];
                            }
                        }

                        $act_plan = [
                            'id'=>$action_plan->id,
                            'description' => $action_plan->description,
                            'final_date' => $final_date,
                            'stakeholder' => $user->name.' '.$user->surnames,
                            'status' => $status
                        ];

                        //QUEDÉ AQUÍ!!!! DEBO VER COMO PONER DATOS DE CADA UNO DE LOS TIPOS (EN ESTE CASO DE PLANES)
                        $info = \Ermtool\Action_plan::getInfo($action_plan->id,$issue->kind);

                        //ACTUALIZACIÓN: Hacemos aquí categorización de tipos
                        if ($issue->kind == 1) //hallazgo de plan de auditoría
                        {
                            $cont_audit_plan += 1;
                            $action_plans_audit_plan[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'audit_plan' => $info->audit_plan,
                                'audit' => $info->audit
                            ];
                        }
                        else if ($issue->kind == 2) //hallazgo de programa
                        {
                            $cont_program += 1;
                            $action_plans_program[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'audit_plan' => $info->audit_plan,
                                'audit' => $info->audit,
                                'audit_program' => $info->audit_program  
                            ];
                        }
                        else if ($issue->kind == 3) //hallazgo de ejecución de pruebas de auditoría
                        {
                            $cont_audit += 1;
                            $action_plans_audit[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'audit_plan' => $info->audit_plan,
                                'audit' => $info->audit,
                                'audit_program' => $info->audit_program,
                                'audit_test' => $info->audit_test 
                            ];
                        }
                        else if ($issue->kind == 4) //hallazgo de organización
                        {
                            $cont_org += 1;
                            $action_plans_org[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'organization' => $info->organization
                            ];
                        }
                        else if ($issue->kind == 5) //hallazgo de subproceso
                        {
                            $cont_subprocess += 1;
                            $action_plans_subprocess[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'process' => $info->process,
                                'subprocess' => $info->subprocess
                            ];
                        }
                        else if ($issue->kind == 6) //hallazgo de proceso
                        {
                            $cont_process += 1;
                            $action_plans_process[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'process' => $info->process
                            ];
                        }
                        else if ($issue->kind == 7) //hallazgo de control de proceso
                        {
                            $cont_process_ctrl += 1;
                            $action_plans_process_ctrl[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'control' => $info->control
                            ];
                        }
                        else if ($issue->kind == 8) //hallazgo de control de negocio
                        {
                            $cont_bussiness_ctrl += 1;
                            $action_plans_bussiness_ctrl[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'control' => $info->control
                            ];
                        }
                        else if ($issue->kind == 9) //hallazgo de evaluación de controles
                        {
                            $cont_ctrl += 1;
                            $action_plans_ctrl[$i] = [
                                'id' => $action_plan->id,
                                'description' => $action_plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $user->name.' '.$user->surnames,
                                'issue' => $issue->description,
                                'recommendations' => $issue->recommendations,
                                'control' => $info->control
                            ];
                        }
                    }
                    else
                    {
                        $act_plan = NULL;
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        if ($issue->description == "")
                        {
                            $issue->description = "Without description";
                        }
                        if ($issue->recommendations == "")
                        {
                            $issue->recommendations = "Without recommendations";
                        }
                    }
                    else
                    {
                        if ($issue->description == "")
                        {
                            $issue->description = "Sin descripción";
                        }
                        if ($issue->recommendations == "")
                        {
                            $issue->recommendations = "Sin recomendaciones";
                        }
                    }

                    //determinamos clasificación
                    if ($issue->classification == 0)
                    {
                        $op_mejora += 1;
                        $issues_om[$i] = [
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'recommendations' => $issue->recommendations,
                            'classification' => $issue->classification,
                            'updated_at' => $updated_at,
                            'action_plan' => $act_plan,
                        ];
                    }
                    else if ($issue->classification == 1)
                    {
                        $deficiencia += 1;
                        $issues_def[$i] = [
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'recommendations' => $issue->recommendations,
                            'classification' => $issue->classification,
                            'updated_at' => $updated_at,
                            'action_plan' => $act_plan,
                        ];
                    }
                    else if ($issue->classification == 2)
                    {
                        $deb_significativa += 1;
                        $issues_deb[$i] = [
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'recommendations' => $issue->recommendations,
                            'classification' => $issue->classification,
                            'updated_at' => $updated_at,
                            'action_plan' => $act_plan,
                        ];
                    }

                    $i += 1;
                }


                if (Session::get('languaje') == 'en')
                {
                    if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos'))
                    {
                        if ($value == 8) //planes de acción eval. controles
                        {
                            //damos formato en inglés y orden
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action Plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder'],
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 9) //planes de acción por auditoría
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_audit as $plan)
                            {
                                $plans[$i] = [
                                    'Audit plan' => $plan['audit_plan'],
                                    'Audit' => $plan['audit'],
                                    'Program' => $plan['program'],
                                    'Test' => $plan['test'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 10) //planes de acción op.mejora
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_om as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Classification' => $plan['classification'],
                                    'Last updated' => $plan['updated_at'],
                                    'Action plan' => $plan['action_plan']['description'],
                                    'Plan final date' => $plan['action_plan']['final_date'],
                                    'Plan status' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 11) //planes de acción deficiencia
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_def as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Classification' => $plan['classification'],
                                    'Last updated' => $plan['updated_at'],
                                    'Action plan' => $plan['action_plan']['description'],
                                    'Plan final date' => $plan['action_plan']['final_date'],
                                    'Plan status' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 12) //planes de acción deb. significativa
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_deb as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Classification' => $plan['classification'],
                                    'Last updated' => $plan['updated_at'],
                                    'Action plan' => $plan['action_plan']['description'],
                                    'Plan final date' => $plan['action_plan']['final_date'],
                                    'Plan status' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 13) //planes de acción abiertos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_open as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Last updated' => $plan['updated_at'],
                                    'Plan final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 14) //planes de acción proximos a cerrar
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_warning as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Last updated' => $plan['updated_at'],
                                    'Plan final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 15) //planes de acción abiertos con fecha límite pasada
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_danger as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Last updated' => $plan['updated_at'],
                                    'Plan final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 16) //planes de acción cerrados
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_closed as $plan)
                            {
                                $plans[$i] = [
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Last updated' => $plan['updated_at'],
                                    'Plan final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }

                        //ACTUALIZACIÓN 08-02-2017: NUEVOS TIPOS DE PLANES DE ACCIÓN
                        else if ($value == 17) //planes de acción para auditoría (o audit_audit_plan)
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_audit_plan as $plan)
                            {
                                $plans[$i] = [
                                    'Audit plan' => $plan['audit_plan'],
                                    'Audit' => $plan['audit'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 18) //planes de acción para programa
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_program as $plan)
                            {
                                $plans[$i] = [
                                    'Audit plan' => $plan['audit_plan'],
                                    'Audit' => $plan['audit'],
                                    'Program' => $plan['audit_program'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 19) //planes de acción para organización
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_org as $plan)
                            {
                                $plans[$i] = [
                                    'Organization' => $plan['organization'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 20) //planes de acción para subprocesos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_subprocess as $plan)
                            {
                                $plans[$i] = [
                                    'Process' => $plan['process'],
                                    'Subprocess' => $plan['subprocess'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 21) //planes de acción para procesos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_process as $plan)
                            {
                                $plans[$i] = [
                                    'Process' => $plan['process'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 22) //planes de acción para controles de proceso
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_process_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 23) //planes de acción para controles de negocio
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_bussiness_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Issue' => $plan['issue'],
                                    'Recommendations' => $plan['recommendations'],
                                    'Action plan' => $plan['description'],
                                    'Status' => $plan['status'],
                                    'Final date' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        
                    }
                    else
                    {
                        return view('en.reportes.planes_accion_graficos',['issues_om'=>$issues_om,'issues_def'=>$issues_def,'issues_deb'=>$issues_deb,'op_mejora'=>$op_mejora,'deficiencia'=>$deficiencia,'deb_significativa'=>$deb_significativa,'cont_ctrl' => $cont_ctrl,'cont_audit' => $cont_audit,'action_plans_ctrl' => $action_plans_ctrl,'action_plans_audit' => $action_plans_audit,'action_plans_open' => $action_plans_open,'action_plans_warning' => $action_plans_warning,'action_plans_danger' => $action_plans_danger,'action_plans_closed' => $action_plans_closed,'cont_open' => $cont_open,'cont_warning' => $cont_warning,'cont_danger' => $cont_danger,'cont_closed' => $cont_closed,'cont_process' => $cont_process,'cont_subprocess' => $cont_subprocess,'cont_program' => $cont_program,'cont_audit_plan' => $cont_audit_plan,'cont_process_ctrl' => $cont_process_ctrl,'cont_bussiness_ctrl' => $cont_bussiness_ctrl,'cont_org' => $cont_org,'action_plans_process' => $action_plans_process,'action_plans_subprocess' => $action_plans_subprocess,'action_plans_audit_plan' => $action_plans_audit_plan,'action_plans_process_ctrl' => $action_plans_process_ctrl,'action_plans_bussiness_ctrl' => $action_plans_bussiness_ctrl,'action_plans_org' => $action_plans_org,'action_plans_program' => $action_plans_program,'org' => $_GET['organization_id']]);
                    }
                }
                else
                {
                    if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos'))
                    {
                        if ($value == 8) //planes de acción eval. controles
                        {
                            //damos formato en español y orden
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder'],
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 9) //planes de acción por auditoría
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_audit as $plan)
                            {
                                $plans[$i] = [
                                    'Plan de auditoría' => $plan['audit_plan'],
                                    'Auditoría' => $plan['audit'],
                                    'Programa' => $plan['program'],
                                    'Prueba' => $plan['test'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 10) //planes de acción op.mejora
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_om as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Clasificación' => $plan['classification'],
                                    'Última actualización' => $plan['updated_at'],
                                    'Plan de acción' => $plan['action_plan']['description'],
                                    'Fecha final plan' => $plan['action_plan']['final_date'],
                                    'Estado del plan' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 11) //planes de acción deficiencia
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_def as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Clasificación' => $plan['classification'],
                                    'Última actualización' => $plan['updated_at'],
                                    'Plan de acción' => $plan['action_plan']['description'],
                                    'Fecha final plan' => $plan['action_plan']['final_date'],
                                    'Estado del plan' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 12) //planes de acción deb. significativa
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($issues_deb as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Clasificación' => $plan['classification'],
                                    'Última actualización' => $plan['updated_at'],
                                    'Plan de acción' => $plan['action_plan']['description'],
                                    'Fecha final plan' => $plan['action_plan']['final_date'],
                                    'Estado del plan' => $plan['action_plan']['status'],
                                    'Responsable' => $plan['action_plan']['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 13) //planes de acción abiertos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_open as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha actualizado' => $plan['updated_at'],
                                    'Fecha final plan' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 14) //planes de acción proximos a cerrar
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_warning as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha actualizado' => $plan['updated_at'],
                                    'Fecha final plan' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 15) //planes de acción abiertos con fecha límite pasada
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_danger as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha actualizado' => $plan['updated_at'],
                                    'Fecha final plan' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }
                        else if ($value == 16) //planes de acción cerrados
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_closed as $plan)
                            {
                                $plans[$i] = [
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha actualizado' => $plan['updated_at'],
                                    'Fecha final plan' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }
                            return $plans;
                        }

                        //ACTUALIZACIÓN 08-02-2017: NUEVOS TIPOS DE PLANES DE ACCIÓN
                        else if ($value == 17) //planes de acción para auditoría (o audit_audit_plan)
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_audit_plan as $plan)
                            {
                                $plans[$i] = [
                                    'Plan de auditoría' => $plan['audit_plan'],
                                    'Auditoría' => $plan['audit'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 18) //planes de acción para programa
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_program as $plan)
                            {
                                $plans[$i] = [
                                    'Plan de auditoría' => $plan['audit_plan'],
                                    'Auditoría' => $plan['audit'],
                                    'Programa' => $plan['audit_program'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 19) //planes de acción para organización
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_org as $plan)
                            {
                                $plans[$i] = [
                                    'Organización' => $plan['organization'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 20) //planes de acción para subprocesos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_subprocess as $plan)
                            {
                                $plans[$i] = [
                                    'Proceso' => $plan['process'],
                                    'Subproceso' => $plan['subprocess'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 21) //planes de acción para procesos
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_process as $plan)
                            {
                                $plans[$i] = [
                                    'Proceso' => $plan['process'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 22) //planes de acción para controles de proceso
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_process_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                        else if ($value == 23) //planes de acción para controles de negocio
                        {
                            $i = 0;
                            $plans = array();
                            foreach ($action_plans_bussiness_ctrl as $plan)
                            {
                                $plans[$i] = [
                                    'Control' => $plan['control'],
                                    'Hallazgo' => $plan['issue'],
                                    'Recomendaciones' => $plan['recommendations'],
                                    'Plan de acción' => $plan['description'],
                                    'Estado' => $plan['status'],
                                    'Fecha final' => $plan['final_date'],
                                    'Responsable' => $plan['stakeholder']
                                ];
                                $i += 1;
                            }

                            return $plans;
                        }
                    }
                    return view('reportes.planes_accion_graficos',['issues_om'=>$issues_om,'issues_def'=>$issues_def,'issues_deb'=>$issues_deb,'op_mejora'=>$op_mejora,'deficiencia'=>$deficiencia,'deb_significativa'=>$deb_significativa,'cont_ctrl' => $cont_ctrl,'cont_audit' => $cont_audit,'action_plans_ctrl' => $action_plans_ctrl,'action_plans_audit' => $action_plans_audit,'action_plans_open' => $action_plans_open,'action_plans_warning' => $action_plans_warning,'action_plans_danger' => $action_plans_danger,'action_plans_closed' => $action_plans_closed,'cont_open' => $cont_open,'cont_warning' => $cont_warning,'cont_danger' => $cont_danger,'cont_closed' => $cont_closed,'cont_process' => $cont_process,'cont_subprocess' => $cont_subprocess,'cont_program' => $cont_program,'cont_audit_plan' => $cont_audit_plan,'cont_process_ctrl' => $cont_process_ctrl,'cont_bussiness_ctrl' => $cont_bussiness_ctrl,'cont_org' => $cont_org,'action_plans_process' => $action_plans_process,'action_plans_subprocess' => $action_plans_subprocess,'action_plans_audit_plan' => $action_plans_audit_plan,'action_plans_process_ctrl' => $action_plans_process_ctrl,'action_plans_bussiness_ctrl' => $action_plans_bussiness_ctrl,'action_plans_org' => $action_plans_org,'action_plans_program' => $action_plans_program,'org' => $_GET['organization_id']]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función para cerrar plan de acción
    public function close($id)
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
                DB::transaction(function() 
                {
                    $logger = $this->logger;
                    $action_plan = \Ermtool\Action_plan::find($GLOBALS['id1']);
                    $action_plan->status = 1;
                    $action_plan->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Action Plan successfully closed');
                    }
                    else
                    {
                        Session::flash('message','Plan de acción cerrado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el plan de acción con Id: '.$action_plan->id.' definido como: '.$action_plan->description.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    
                    $GLOBALS['res'] = 0;
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

    //Función para que verificará proximidad en fecha de término de planes de acción, para poder de esta manera enviar alertas a los usuarios
    public function verificarFechaPlanes()
    {
        try
        {
            $planes = \Ermtool\Action_plan::getOpenedActionPlans();
            $plans = array();
            $i = 0;
            foreach ($planes as $p)
            {
                //verificaremos en una variable diferencia en días entre fecha final y fecha actual
                $date = $p->final_date;
                $date = new DateTime($date);
                //$date = date_format($date,'Y-m-d');
                $date_actual = new DateTime(date('Y-m-d'));
                //$seconds= $date - $date_actual;
                //$dif = intval($seconds/60/60/24);

                $dif = $date_actual->diff($date);
                $dif = (int)$dif->format('%a');
                //usaremos como estándar 2 semanas (14 días) para enviar alerta, sin embargo es modificable AQUÍ
                if ($dif <= 14)
                {
                    if ($p->stakeholder_id != NULL && $p->stakeholder_id != '')
                    {
                        $plans[$i] = [
                            'id' => $p->id,
                            'description' => $p->description,
                            'final_date' => $p->final_date,
                            'dif' => $dif
                        ];

                        $i += 1;

                        //obtenemos stakeholder (responsable) para enviarle un correo informando la situación
                        $stakeholder_mail = \Ermtool\Stakeholder::where('id',$p->stakeholder_id)->value('mail');
                        $name = \Ermtool\Stakeholder::getName($p->stakeholder_id);

                        $mensaje1 = 'Estimado usuario.';
                        $mensaje2 = 'Usted ha sido identificado como el encargado del plan de acción descrito como "'.$p->description.'".';
                        $mensaje3 = 'Se le envía este correo para informarle que dicho plan de acción se encuentra próximo a su fecha límite, o bien esta fecha ya se encuentra expirada.';
                        $mensaje4 = 'Esperamos pueda solucionar dicha situación a la brevedad.';
                        $mensaje5 = 'Se despide atentamente,';
                        $mensaje6 = 'El equipo de B-GRC';

                        Mail::send('envio_mail2',['mensaje1' => $mensaje1, 'mensaje2' => $mensaje2, 'mensaje3' => $mensaje3, 'mensaje4' => $mensaje4, 'mensaje5' => $mensaje5, 'mensaje6' => $mensaje6], function ($msj) use ($stakeholder_mail,$name)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $msj->to($stakeholder_mail, $name)->subject('Action plan next to close!');
                            }
                            else
                            {
                                $msj->to($stakeholder_mail, $name)->subject('Plan de accion proximo a cerrar!');
                            }
                        });
                    }
                    
                }
            }

            return $plans;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}