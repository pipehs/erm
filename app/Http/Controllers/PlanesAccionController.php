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

class PlanesAccionController extends Controller
{
    //función que obtiene todos los planes de acción de una organización
    public function getActionPlans($id)
    {
        $i = 0;
        $action_plans = array();
            //primero obtenemos los planes de acción para los hallazgos que son directamente de la organización
            $planes = DB::table('issues')
                        ->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->where('issues.organization_id','=',$id)
                        ->groupBy('action_plans.id')
                        ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.organization_id as org_id')
                        ->get();

            //ahora los planes de acción para los planes de auditoría que corresponden a la organización
            $planes2 = DB::table('issues')
                        ->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','issues.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->where('audit_plans.organization_id','=',$id)
                        ->groupBy('action_plans.id')
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
                        ->groupBy('action_plans.id')
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
                        ->groupBy('action_plans.id')
                        ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.audit_test_id as test_id')
                        ->get();

            //planes de control de entidad asociados a la organización
            $planes5 = DB::table('issues')
                        ->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','issues.control_id')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk_id')
                        ->where('objectives.organization_id','=',$id)
                        ->groupBy('action_plans.id')
                        ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                'action_plans.status','action_plans.stakeholder_id','issues.name as issue','control_objective_risk.control_id as control_objective_risk_id')
                        ->get();

            //planes de control de proceso asociados a la organización
            $planes6 = DB::table('issues')
                        ->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','issues.control_id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$id)
                        ->groupBy('action_plans.id')
                        ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                'action_plans.status','action_plans.stakeholder_id','issues.name as issue','control_risk_subprocess.control_id as control_risk_subprocess_id')
                        ->get();

            //planes asociados a un subproceso perteneciente a la organización
            $planes7 = DB::table('issues')
                        ->join('action_plans','action_plans.issue_id','=','issues.id')
                        ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$id)
                        ->groupBy('action_plans.id')
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
                        ->groupBy('action_plans.id')
                        ->select('action_plans.id','action_plans.description','action_plans.stakeholder_id','action_plans.final_date',
                                'action_plans.status','action_plans.stakeholder_id','issues.name as issue','issues.process_id as process_id')
                        ->get(); 

            $plans = array_merge($planes,$planes2,$planes3,$planes4,$planes5,$planes6,$planes7,$planes8);

            foreach ($plans as $plan)
            {
                $final_date = new DateTime($plan->final_date);
                $final_date = date_format($final_date,"d-m-Y");

                
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
                        $resp_mail = '';
                    }
                    if ($plan->status === 0)
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
                    else if (isset($plan->control_objective_risk_id))
                    {
                        $origin = 'Entity control issue';
                    }
                    else if (isset($plan->control_risk_subprocess_id))
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
                        $resp_mail = '';
                    }
                    if ($plan->status === 0)
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
                    else if (isset($plan->control_objective_risk_id))
                    {
                        $origin = 'Hallazgo de control de entidad';
                    }
                    else if (isset($plan->control_risk_subprocess_id))
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
                    ];
                }

                $i += 1;
                //
            }

        return $action_plans;
    }

    //función que obtiene planes de acción de auditoría
    public function getActionPlanAudit($org)
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

    public function index2()
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
    public function store($issue_id,$description,$stakeholder,$final_date)
    {
        $new_plan = DB::table('action_plans')
                ->insertGetId([
                    'issue_id' => $issue_id,
                    'description' => $description,
                    'stakeholder_id' => $stakeholder,
                    'final_date' => $final_date,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

        return $new_plan;
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {   
            $org = \Ermtool\Organization::where('id',$_GET['org'])->value('name');
            $org_id = \Ermtool\Organization::where('id',$_GET['org'])->value('id');

            //obtenemos stakeholders de la misma organización
            $stakes = DB::table('stakeholders')
                        ->join('organization_stakeholder','organization_stakeholder.stakeholder_id','=','stakeholders.id')
                        ->where('organization_stakeholder.organization_id','=',$_GET['org'])
                        ->select('stakeholders.id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
                        ->orderBy('name')
                        ->lists('full_name', 'id');

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
            DB::transaction(function() {
                
                $status = 0;

                //verificamos ingreso de datos
                if (isset($_POST['description']) AND $_POST['description'] != "")
                {
                    $description = $_POST['description'];
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
                DB::table('action_plans')->where('id','=',$GLOBALS['id2'])
                    ->update([
                        'issue_id' => $_POST['issue_id'],
                        'description' => $description,
                        'stakeholder_id' => $stakeholder_id,
                        'final_date' => $final_date,
                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Action plan successfully updated');
                }
                else
                {
                    Session::flash('message','Plan de acción actualizado correctamente');
                }

            });

            return Redirect::to('action_plans2?organization_id='.$_POST['org_id']);
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

                //primero que todo, eliminamos plan de acción (si es que hay)
                DB::table('action_plans')
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

                $GLOBALS['res'] = 0;
            });

            return $res;
        }
    }

    //obtiene plan de acción para un issue dado
    public function getActionPlan($id)
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

    //ACTUALIZACIÓN 25-08: Se deben mostrar los planes de acción para todos los tipos de hallazgo del sistema
    public function generarReportePlanes($org)
    {
        $results = array();
        $i = 0;
        
        //$action_plans = $this->getActionPlanAudit($org);
            
        //ACTUALIZACIÓN 25-08-2016: Función extraida para que esté disponible en el mantenedor y en el reporte de planes de acción
        $results = $this->getActionPlans($org);

        if (strstr($_SERVER["REQUEST_URI"],'genexcelplan')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $results;
        }
        else
            return json_encode($results);
    }

    public function indexGraficos($value)
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

            $issues_all = \Ermtool\Issue::all(['id','name','description','recommendations','classification','updated_at']);

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
                        if ($action_plan->status == 0)
                        {
                            $status = "En progreso";
                        }
                        else if ($action_plan->status == 1)
                        {
                            $status = "Cerrado";
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

                    $act_plan = [
                        'id'=>$action_plan->id,
                        'description' => $action_plan->description,
                        'final_date' => $final_date,
                        'stakeholder' => $user->name.' '.$user->surnames,
                        'status' => $status
                    ];
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


            $planes_ejec = 0; //planes en ejecución
            $planes_cerrados = 0; //plan sin pruebas abiertas ni en ejecución, pero si cerradas 

            //para un gráfico separaremos 3 tipos de planes de acción: planes en planes de auditoria, planes en eval. de controles, y otros: cuando se agreguen genericamente (quizas)
            $action_plans_ctrl = array();
            $action_plans_audit = array();
            $action_plans_others = array();

            $action_plans_closed = array(); //planes de acción cerrados
            $action_plans_warning = array(); //planes de acción próximos a cerrar
            $action_plans_danger = array(); //planes de acción pasados en fecha y aun abiertos
            $action_plans_open = array(); //planes de acción en los que la fecha de cierre es mayor a 2 meses

            $cont_open = 0;
            $cont_danger = 0;
            $cont_warning = 0;
            $cont_closed = 0;

            $cont_ctrl = 0;
            $cont_audit = 0;
            $others = 0;

            //primero los controlados
            $action_plans = DB::table('action_plans')
                                ->join('issues','issues.id','=','action_plans.issue_id')
                                ->join('control_evaluation','control_evaluation.issue_id','=','issues.id')
                                ->join('controls','controls.id','=','control_evaluation.control_id')
                                ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                                ->whereNotNull('control_evaluation.issue_id')
                                ->select('action_plans.id','action_plans.description',
                                         'action_plans.status','action_plans.final_date',
                                         'action_plans.updated_at',
                                         'controls.name as control','stakeholders.name as user_name',
                                         'stakeholders.surnames as user_surnames',
                                         'issues.description as issue','issues.recommendations')
                                ->get();

            $i = 0;
            foreach ($action_plans as $plan)
            {
                $cont_ctrl += 1;

                if (Session::get('languaje') == 'en')
                {
                    if ($plan->status == 0)
                    {
                        $status = "In progress";
                    }
                    else if ($plan->status == 1)
                    {
                        $status = "Closed plan";
                    }
                    
                    if ($plan->final_date == '0000-00-00')
                    {
                        $final_date = "Error storing plan deadline";
                    }
                    else
                    {
                        //seteamos fecha final
                        $final_date_tmp = new DateTime($plan->final_date);
                        $final_date = date_format($final_date_tmp, 'd-m-Y');
                    }
                }
                else
                {
                    if ($plan->status == 0)
                    {
                        $status = "En progreso";
                    }
                    else if ($plan->status == 1)
                    {
                        $status = "Plan cerrado";
                    }
                    
                    if ($plan->final_date == '0000-00-00')
                    {
                        $final_date = "Error al registrar fecha final";
                    }
                    else
                    {
                        //seteamos fecha final
                        $final_date_tmp = new DateTime($plan->final_date);
                        $final_date = date_format($final_date_tmp, 'd-m-Y');
                    }
                }

                $updated_at_tmp = new DateTime($plan->updated_at);
                $updated_at = date_format($updated_at_tmp, 'd-m-Y');

                if (Session::get('languaje') == 'en')
                {
                    if ($plan->description == "")
                    {
                        $plan->description = "Without description";
                    }
                    if ($plan->recommendations == "")
                    {
                        $plan->recommendations = "Without recommendations";
                    }
                }
                else
                {
                    if ($plan->description == "")
                    {
                        $plan->description = "Sin descripción";
                    }
                    if ($plan->recommendations == "")
                    {
                        $plan->recommendations = "Sin recomendaciones";
                    }
                }

                $action_plans_ctrl[$i] = [
                    'id' => $plan->id,
                    'description' => $plan->description,
                    'status' => $status,
                    'final_date' => $final_date,
                    'updated_at' => $updated_at,
                    'control' => $plan->control,
                    'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                    'issue' => $plan->issue,
                    'recommendations' => $plan->recommendations,
                ];

                //verificamos para tercer gráfico el tipo de control (abierto, proximo a cerrar, cerrado, falta mucho para que cierre...)

                if ($plan->final_date != NULL)
                {
                    $fecha_temp = explode('-',$plan->final_date); //obtenemos solo mes y año
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
                    if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;
                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else
                    {
                        $cont_open += 1;
                        $action_plans_open[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    
                }
                else if ($fecha_ano == 0)
                {
                    if ($fecha >= 2 && $plan->status == 0)
                    {
                        $cont_open += 1;

                        $action_plans_open[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else if ($fecha < 2 && $fecha >= 0 && $plan->status == 0) //warning
                    {
                        //verificamos día
                        if ($fecha_dia <= 0)
                        {
                             $cont_danger += 1;

                            $action_plans_danger[$i] = [
                                'id' => $plan->id,
                                'description' => $plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                                'issue' => $plan->issue,
                                'recommendations' => $plan->recommendations,
                            ];
                        }
                        else
                        {
                            $cont_warning += 1;

                            $action_plans_warning[$i] = [
                                'id' => $plan->id,
                                'description' => $plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                                'issue' => $plan->issue,
                                'recommendations' => $plan->recommendations,
                            ];
                        }  
                    }
                    else if ($fecha < 0 && $plan->status == 0) //danger
                    {
                        $cont_danger += 1;

                        $action_plans_danger[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;

                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                }
                else //el año es menor, por lo que no se necesita hacer mas verificacion (excepto si es que esta cerrado)
                {
                    if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;
                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else
                    {
                        $cont_danger += 1;
                        $action_plans_danger[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                }

                $i += 1;
            }

            //ahora para action plans de auditoría
            $action_plans = $this->getActionPlanAudit(NULL);

            $i = 0;
            foreach ($action_plans as $plan)
            {
                $cont_audit += 1;

                if (Session::get('languaje') == 'en')
                {
                    if ($plan->status == 0)
                    {
                        $status = "In progress";
                    }
                    else if ($plan->status == 1)
                    {
                        $status = "Closed plan";
                    }
                    
                    if ($plan->final_date == '0000-00-00')
                    {
                        $final_date = "Error storing plan deadline";
                    }
                    else
                    {
                        //seteamos fecha final
                        $final_date_tmp = new DateTime($plan->final_date);
                        $final_date = date_format($final_date_tmp, 'd-m-Y');
                    }

                    if ($plan->description == "")
                    {
                        $plan->description = "Without description";
                    }
                    if ($plan->recommendations == "")
                    {
                        $plan->recommendations = "Without recommendations";
                    }
                }
                else
                {
                    if ($plan->status == 0)
                    {
                        $status = "En progreso";
                    }
                    else if ($plan->status == 1)
                    {
                        $status = "Plan cerrado";
                    }
                    
                    if ($plan->final_date == '0000-00-00')
                    {
                        $final_date = "Error al registrar fecha final";
                    }
                    else
                    {
                        //seteamos fecha final
                        $final_date_tmp = new DateTime($plan->final_date);
                        $final_date = date_format($final_date_tmp, 'd-m-Y');
                    }

                    if ($plan->description == "")
                    {
                        $plan->description = "Sin descripción";
                    }
                    if ($plan->recommendations == "")
                    {
                        $plan->recommendations = "Sin recomendaciones";
                    }
                }

                $updated_at_tmp = new DateTime($plan->updated_at);
                $updated_at = date_format($updated_at_tmp, 'd-m-Y');

                $action_plans_audit[$i] = [
                    'id' => $plan->id,
                    'description' => $plan->description,
                    'status' => $status,
                    'final_date' => $final_date,
                    'audit_plan' => $plan->audit_plan_name,
                    'audit' => $plan->audit_name,
                    'program' => $plan->program_name,
                    'test' => $plan->test_name,
                    'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                    'issue' => $plan->issue_name,
                    'recommendations' => $plan->recommendations,
                ];

                //verificamos para tercer gráfico el tipo de control (abierto, proximo a cerrar, cerrado, falta mucho para que cierre...)
                $fecha_temp = explode('-',$plan->final_date); //obtenemos solo mes y año
                $fecha_ano = (int)$fecha_temp[0] - (int)date('Y'); //obtenemos solo año
                $fecha = (int)$fecha_temp[1] - (int)date('m'); //solo mes
                $fecha_dia = (int)$fecha_temp[2] - (int)date('d'); //solo día

                if ($fecha_ano > 0)
                {
                    if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;
                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else
                    {
                        $cont_open += 1;
                        $action_plans_open[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    
                }
                else if ($fecha_ano == 0)
                {
                    if ($fecha >= 2 && $plan->status == 0)
                    {
                        $cont_open += 1;

                        $action_plans_open[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else if ($fecha < 2 && $fecha >= 0 && $plan->status == 0) //warning
                    {
                        //verificamos día
                        if ($fecha_dia <= 0)
                        {
                             $cont_danger += 1;

                            $action_plans_danger[$i] = [
                                'id' => $plan->id,
                                'description' => $plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                                'issue' => $plan->issue_name,
                                'recommendations' => $plan->recommendations,
                            ];
                        }
                        else
                        {
                            $cont_warning += 1;

                            $action_plans_warning[$i] = [
                                'id' => $plan->id,
                                'description' => $plan->description,
                                'status' => $status,
                                'final_date' => $final_date,
                                'updated_at' => $updated_at,
                                'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                                'issue' => $plan->issue_name,
                                'recommendations' => $plan->recommendations,
                            ];
                        }
                    }
                    else if ($fecha < 0 && $plan->status == 0) //danger
                    {
                        $cont_danger += 1;

                        $action_plans_danger[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;

                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                }
                else //el año es menor, por lo que no se necesita hacer mas verificacion (excepto si es que esta cerrado)
                {
                    if ($plan->status == 1) //closed
                    {
                        $cont_closed += 1;
                        $action_plans_closed[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                    else
                    {
                        $cont_danger += 1;
                        $action_plans_danger[$i] = [
                            'id' => $plan->id,
                            'description' => $plan->description,
                            'status' => $status,
                            'final_date' => $final_date,
                            'updated_at' => $updated_at,
                            'stakeholder' => $plan->user_name.' '.$plan->user_surnames,
                            'issue' => $plan->issue_name,
                            'recommendations' => $plan->recommendations,
                        ];
                    }
                }
                

                $i += 1;
            }

            if (Session::get('languaje') == 'en')
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
                }
                else
                {
                    return view('en.reportes.planes_accion_graficos',['issues_om'=>$issues_om,'issues_def'=>$issues_def,
                                                            'issues_deb'=>$issues_deb,'op_mejora'=>$op_mejora,
                                                            'deficiencia'=>$deficiencia,'deb_significativa'=>$deb_significativa,
                                                            'cont_ctrl' => $cont_ctrl,'cont_audit' => $cont_audit,'others' => $others,
                                                            'action_plans_ctrl' => $action_plans_ctrl,
                                                            'action_plans_audit' => $action_plans_audit,
                                                            'action_plans_open' => $action_plans_open,
                                                            'action_plans_warning' => $action_plans_warning,
                                                            'action_plans_danger' => $action_plans_danger,
                                                            'action_plans_closed' => $action_plans_closed,
                                                            'cont_open' => $cont_open,
                                                            'cont_warning' => $cont_warning,
                                                            'cont_danger' => $cont_danger,
                                                            'cont_closed' => $cont_closed]);
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
                }
                return view('reportes.planes_accion_graficos',['issues_om'=>$issues_om,'issues_def'=>$issues_def,
                                                            'issues_deb'=>$issues_deb,'op_mejora'=>$op_mejora,
                                                            'deficiencia'=>$deficiencia,'deb_significativa'=>$deb_significativa,
                                                            'cont_ctrl' => $cont_ctrl,'cont_audit' => $cont_audit,'others' => $others,
                                                            'action_plans_ctrl' => $action_plans_ctrl,
                                                            'action_plans_audit' => $action_plans_audit,
                                                            'action_plans_open' => $action_plans_open,
                                                            'action_plans_warning' => $action_plans_warning,
                                                            'action_plans_danger' => $action_plans_danger,
                                                            'action_plans_closed' => $action_plans_closed,
                                                            'cont_open' => $cont_open,
                                                            'cont_warning' => $cont_warning,
                                                            'cont_danger' => $cont_danger,
                                                            'cont_closed' => $cont_closed]);
            }
        }
    }

    //función para cerrar plan de acción
    public function close($id)
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

                $GLOBALS['res'] = 0;
            });

            return $res;
        }
    }
}