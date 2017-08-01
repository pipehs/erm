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
use Auth;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class GestionEstrategicaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger1;
    public $logger2;

    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger1 = new Logger('gestion_estrategica');
        $this->logger1->pushHandler(new StreamHandler($dir.'/storage/logs/gestion_estrategica.log', Logger::INFO));
        $this->logger1->pushHandler(new FirePHPHandler());

        $this->logger2 = new Logger('kpi');
        $this->logger2->pushHandler(new StreamHandler($dir.'/storage/logs/kpi.log', Logger::INFO));
        $this->logger2->pushHandler(new FirePHPHandler());
    }
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)

    //index de planes estratégicos
    public function indexPlanes()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            try {
                if (isset($_GET['organizacion']))
                {
                    $planes = \Ermtool\Strategic_plan::where('organization_id','=',$_GET['organizacion'])->get();
                    $organization = \Ermtool\Organization::name($_GET['organizacion']);

                    //valida si es que existe algún plan vigente
                    $validador = 0;
                    foreach ($planes as $plan)
                    {
                        if ($plan['status'] == 1)
                        {
                            $validador = 1;
                        }
                        //formateamos las fechas
                        $initial_date = new DateTime($plan['initial_date']);
                        $plan['initial_date'] = date_format($initial_date, 'd-m-Y');

                        $final_date = new DateTime($plan['final_date']);
                        $plan['final_date'] = date_format($final_date, 'd-m-Y');

                        if ($plan['expiration_date'] != NULL)
                        {
                            $expiration_date = new DateTime($plan['expiration_date']);
                            $plan['expiration_date'] = date_format($expiration_date, 'd-m-Y');
                        }
                        //guardamos en organization_id el nombre de la organización
                        //$plan['organization_id'] = \Ermtool\Organization::name($plan['organization_id']);
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.gestion_estrategica.plan_estrategico',['planes' => $planes,'organization' => $organization,'org_id'=>$_GET['organizacion'],'validador' => $validador]);
                    }
                    else
                    {
                        return view('gestion_estrategica.plan_estrategico',['planes' => $planes,'organization' => $organization,'org_id'=>$_GET['organizacion'],'validador' => $validador]);
                    }
                } 
            }
            catch (\Exception $e){
                 return response()->view('errors.query');
            }         
        }
    }

    public function createPlanEstrategico()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos organizaciones ---> Expiró 05-10-2016
            //$organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            //ACTUALIZACIÓN 19-07-17: Verificamos si la organización depende de otra organización; en este caso, se verá si es que la organización padre posee objetivos estratégicos vigentes y se dará la opción de que se eligan
            $father_objs = \Ermtool\Objective::getFatherObjectives($_GET['org_id']);

            //obtenemos nombre de organización padre
            $father_org_name = \Ermtool\Organization::getFatherOrgName($_GET['org_id']);

            $org_name = \Ermtool\Organization::name($_GET['org_id']);

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.create_plan',['org_id' => $_GET['org_id'],'org_name' => $org_name,'father_objectives' => $father_objs,'father_org_name' => $father_org_name]);
            }
            else
            {
                return view('gestion_estrategica.create_plan',['org_id' => $_GET['org_id'],'org_name' => $org_name,'father_objectives' => $father_objs,'father_org_name' => $father_org_name]);
            }
        }
    }

    public function storePlanEstrategico()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos planes para redirección a index
            $planes = \Ermtool\Strategic_plan::all();
            DB::transaction(function () {
                $logger = $this->logger1;
                //primero, debemos asegurarnos de cambiar el status a todos los planes estratégicos existentes para la misma organización
                DB::table('strategic_plans')
                    ->where('organization_id','=',$_POST['organization_id'])
                    ->update([
                        'status' => 0,
                        ]);

                if (isset($_POST['comments']) && $_POST['comments'] != '')
                {
                    $comments = $_POST['comments'];
                }
                else
                {
                    $comments = NULL;
                }

                //seteamos fecha de expiracion
                $initial_date = explode('-',$_POST['initial_date']);

                $final_date = $initial_date[0]+$_POST['duration'].'-'.$initial_date[1].'-'.$initial_date[2];

                $plan = DB::table('strategic_plans')
                    ->insertGetId([
                        'name' => $_POST['name'],
                        'comments' => $comments,
                        'initial_date' => $_POST['initial_date'],
                        'final_date' => $final_date,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'organization_id' => $_POST['organization_id']
                        ]);

                //ACTUALIZACIÓN 19-07-17: Si es que se agregaron objetivos heredados, se deben crear para esta organización
                if (isset($_POST['objectives_id']))
                {
                    //obtenemos todos los datos asociados a los objetivos
                    foreach ($_POST['objectives_id'] as $obj_id)
                    {
                        $objective = \Ermtool\Objective::find($obj_id);

                            \Ermtool\Objective::create([
                                'code' => $objective->code,
                                'name' => $objective->name,
                                'description' => $objective->description,
                                'organization_id' => $_POST['organization_id'],
                                'status' => 0,
                                'perspective' => $objective->perspective,
                                'perspective2' => $objective->perspective2,
                                'strategic_plan_id' => $plan
                            ]);
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Strategic plan was successfully created');
                }
                else
                {
                    Session::flash('message','Plan estratégico fue generado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el plan estratégico con Id: '.$plan.' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });

            return Redirect::to('plan_estrategico?organizacion='.$_POST['organization_id']);
        }
    }

    public function editPlanEstrategico($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $strategic_plan = \Ermtool\Strategic_plan::find($id);
            //calculamos duracion en años
            $segundos=strtotime($strategic_plan->final_date) - strtotime($strategic_plan->initial_date);

            $duration = (int)($segundos / 365 / 24 / 60 / 60); 
            
            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.edit_plan',['strategic_plan'=>$strategic_plan,'duration' => $duration]);
            }
            else
            {
                return view('gestion_estrategica.edit_plan',['strategic_plan'=>$strategic_plan,'duration' => $duration]);
            }
        }
    }

    public function updatePlanEstrategico(Request $request, $id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {           
                global $id1;
                $id1 = $id;
                DB::transaction(function() 
                {
                    $logger = $this->logger1;
                    $strategic_plan = \Ermtool\Strategic_plan::find($GLOBALS['id1']);

                    if ($_POST['comments'] != "")
                    {
                        $comments = $_POST['comments'];
                    }
                    else
                    {
                        $comments = NULL;
                    }

                    //seteamos fecha de expiracion
                    $initial_date = explode('-',$_POST['initial_date']);

                    $final_date = $initial_date[0]+$_POST['duration'].'-'.$initial_date[1].'-'.$initial_date[2];

                    $strategic_plan->name = $_POST['name'];
                    $strategic_plan->comments = $comments;
                    $strategic_plan->initial_date = $_POST['initial_date'];
                    $strategic_plan->final_date = $final_date;
                    $strategic_plan->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Objective was successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Plan estratégico actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el plan estratégico con Id: '.$GLOBALS['id1'].' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    
                });
            return Redirect::to('plan_estrategico?organizacion='.$_POST['org_id']);
        }
    }
    public function kpi()
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
                return view('en.gestion_estrategica.kpi',['organizations' => $organizations]);
            }
            else
            {
                return view('gestion_estrategica.kpi',['organizations' => $organizations]);
            }
        }
    }

    public function kpi2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            $org_selected = \Ermtool\Organization::name($_GET['organization_id']);
            $kpi = array();

            $financiera = 0;
            $procesos = 0;
            $clientes = 0;
            $aprendizaje = 0;

            $kpiquery = DB::table('kpi')
                        ->join('kpi_objective','kpi_objective.kpi_id','=','kpi.id')
                        ->join('objectives','objectives.id','=','kpi_objective.objective_id')
                        ->where('objectives.organization_id','=',$_GET['organization_id'])
                        ->select('kpi.id','kpi.name','kpi.description','kpi.stakeholder_id','kpi.goal','objectives.name as obj_name','objectives.perspective as perspective','kpi.initial_value')
                        ->get();

            $i = 0;
            foreach ($kpiquery as $k)
            {
                //hacemos ciclo para obtener última medición de cada kpi
                //para esto primero obtenemos la fecha de la última medición (si es que hay mediciones)
                //ACTUALIZACIÓN 20-01-17: Obtenemos último periódo evaluado
                $last_period = \Ermtool\kpi::getLastEvaluationPeriod($k->id);

                $max_updated = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$k->id)
                        ->where('status','=',1)
                        ->max('updated_at');

                //$max_updated = str_replace('-','',$max_updated);
                //ahora si es que hay fecha, obtenemos datos de última eval
                if ($max_updated)
                {
                    $last_eval = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$k->id)
                            ->where('updated_at','=',$max_updated)
                            ->select('value','status')
                            ->first();

                    $last_eval_value = $last_eval->value;
                    $last_eval_status = $last_eval->status;
                    $date_last_eval = $last_period;
                }
                else
                {   //Resulta más fácil configurar los mensajes aquí que en la vista (en este caso)
                    if (Session::get('languaje') == 'en')
                    {
                        $last_eval_value = "No valid assessments";
                        $date_last_eval = "No valid assessments";
                    }
                    else
                    {
                        $last_eval_value = "No hay evaluaciones validadas";
                        $date_last_eval = "No hay evaluaciones validadas";
                    }
                    $last_eval_status = NULL;
                }

                //vemos si existe eval para validar
                $id_eval = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$k->id)
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                if ($id_eval) //existe evaluación para validar
                {
                    $status = TRUE;
                }
                else
                {
                    $status = FALSE;
                }

                $stakeholder = DB::table('stakeholders')
                            ->where('id','=',$k->stakeholder_id)
                            ->select('name','surnames')
                            ->first();

                if ($stakeholder)
                {
                    $stake = $stakeholder->name.' '.$stakeholder->surnames;
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $stake = "No responsable assigned";
                    }
                    else
                    {
                        $stake = "No se ha asignado responsable";   
                    }
                }

                //realizamos un contador de perspectivas, para poder mostrar ordenadamente en gráfica

                switch ($k->perspective) {
                            case 1:
                                $financiera += 1;
                                break;
                            case 2:
                                $procesos += 1;
                                break;
                            case 3:
                                $clientes += 1;
                                break;
                            case 4:
                                $aprendizaje += 1;
                                break;
                            default:
                                break;
                        } 

                if ($k->goal == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $goal = "No defined goals";
                    }
                    else
                    {
                        $goal = "No se han definido metas";
                    }
                }
                else
                {
                    $goal = $k->goal;
                }
                switch ($k->initial_value) 
                {
                    case 1:
                        $init = 'Clp';
                        break;
                    case 2:
                        $init = 'US Dlls';
                        break;
                    case 3:
                        $init = 'Porcentaje';
                        break;
                    case 4:
                        $init = 'Cantidad';
                        break;
                    default:
                        $init = 'No se ha definido';
                        break;
                }
                $kpi[$i] = [
                    'id' => $k->id,
                    'name' => $k->name,
                    'description' => $k->description,
                    'stakeholder' => $stake,
                    'last_eval' => $last_eval_value,
                    'status' => $last_eval_status,
                    'date_last_eval' => $date_last_eval,
                    'goal' => $goal,
                    'perspective' => $k->perspective,
                    'objective' => $k->obj_name,
                    'status_validate' => $status,
                    'initial_value' => $init,
                ];

                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.kpi',['organizations' => $organizations, 'org_selected' => $org_selected, 'kpi' => $kpi,'org_id' => $_GET['organization_id'],'financiera' => $financiera,'procesos' => $procesos,'clientes' => $clientes,'aprendizaje' => $aprendizaje]);
            }
            else
            {
                return view('gestion_estrategica.kpi',['organizations' => $organizations, 'org_selected' => $org_selected, 'kpi' => $kpi,'org_id' => $_GET['organization_id'],'financiera' => $financiera,'procesos' => $procesos,'clientes' => $clientes,'aprendizaje' => $aprendizaje]);
            }
        }
    }

    public function objectiveKpi($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {

            $obj_selected = \Ermtool\Objective::name($id);
            $kpi = array();

            $kpiquery = DB::table('kpi')
                        ->join('kpi_objective','kpi_objective.kpi_id','=','kpi.id')
                        ->join('objectives','objectives.id','=','kpi_objective.objective_id')
                        ->where('objectives.id','=',$id)
                        ->select('kpi.id','kpi.name','kpi.description','kpi.stakeholder_id','kpi.goal','objectives.name as obj_name','objectives.perspective as perspective','kpi.measurement_unit','kpi.periodicity','kpi.calculation_method','kpi.initial_value')
                        ->get();

            $i = 0;
            foreach ($kpiquery as $k)
            {
                //hacemos ciclo para obtener última medición de cada kpi
                //para esto primero obtenemos la fecha de la última medición (si es que hay mediciones)
                //ACTUALIZACIÓN 20-01-17: Obtenemos último periódo evaluado
                $last_period = \Ermtool\kpi::getLastEvaluationPeriod($k->id);

                $max_updated = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$k->id)
                        ->where('status','=',1)
                        ->max('updated_at');

                //ahora si es que hay fecha, obtenemos datos de última eval
                if ($max_updated)
                {
                    $last_eval = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$k->id)
                            ->where('updated_at','=',$max_updated)
                            ->select('value','status')
                            ->first();

                    $last_eval_value = $last_eval->value;
                    $last_eval_status = $last_eval->status;
                    $date_last_eval = $last_period;
                }
                else
                {   //Resulta más fácil configurarlo los mensajes aquí que en la vista (en este caso)
                    if (Session::get('languaje') == 'en')
                    {
                        $last_eval_value = "No valid assessments";
                        $date_last_eval = "No valid assessments";
                    }
                    else
                    {
                        $last_eval_value = "No hay evaluaciones validadas";
                        $date_last_eval = "No hay evaluaciones validadas";
                    }
                    $last_eval_status = NULL;
                }

                //vemos si existe eval para validar
                $id_eval = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$k->id)
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                if ($id_eval) //existe evaluación para validar
                {
                    $status = TRUE;
                }
                else
                {
                    $status = FALSE;
                }

                $stakeholder = DB::table('stakeholders')
                            ->where('id','=',$k->stakeholder_id)
                            ->select('name','surnames')
                            ->first();

                if ($stakeholder)
                {
                    $stake = $stakeholder->name.' '.$stakeholder->surnames;
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $stake = "No responsable assigned";
                    }
                    else
                    {
                        $stake = "No se ha asignado responsable";   
                    }
                }

                if ($k->goal == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $goal = "No defined goals";
                    }
                    else
                    {
                        $goal = "No se han definido metas";
                    }
                }
                else
                {
                    $goal = $k->goal;
                }

                switch ($k->initial_value) 
                {
                    case 1:
                        $init = 'Clp';
                        break;
                    case 2:
                        $init = 'US Dlls';
                        break;
                    case 3:
                        $init = 'Porcentaje';
                        break;
                    case 4:
                        $init = 'Cantidad';
                        break;
                    default:
                        # code...
                        break;
                }
                $kpi[$i] = [
                    'id' => $k->id,
                    'name' => $k->name,
                    'description' => $k->description,
                    'stakeholder' => $stake,
                    'last_eval' => $last_eval_value,
                    'date_last_eval' => $date_last_eval,
                    'status' => $last_eval_status,
                    'goal' => $goal,
                    'objective' => $k->obj_name,
                    'periodicity' => $k->periodicity,
                    'calculation_method' => $k->calculation_method,
                    'measurement_unit' => $init,
                    'status_validate' => $status,
                ];

                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.kpi_objective',['obj_selected' => $obj_selected, 'kpi' => $kpi,'obj_id' => $id]);
            }
            else
            {
                return view('gestion_estrategica.kpi_objective',['obj_selected' => $obj_selected, 'kpi' => $kpi,'obj_id' => $id]);
            }
        }
    }

    public function mapas()
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
                return view('en.gestion_estrategica.mapas',['organizations' => $organizations]);
            }
            else
            {
                return view('gestion_estrategica.mapas',['organizations' => $organizations]);
            }
        }
    }

    public function mapas2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            $vision = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('vision');
            $org_selected = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');
            $objectives = array();

            $objectives1 = DB::table('objectives')
                        ->join('strategic_plans','strategic_plans.id','=','objectives.strategic_plan_id')
                        ->where('objectives.organization_id','=',$_GET['organization_id'])
                        ->where('objectives.status','=',0)
                        ->where('strategic_plans.status','=',1)
                        ->select('objectives.name','objectives.id','objectives.perspective','objectives.description','objectives.code','objectives.perspective2')
                        ->get();

            //Obtendremos los objetivos impactados por cada objetivo, y los almacenaremos en un array con su correspondiente objetivo
            $objectives = array();
            $j = 0;
            foreach ($objectives1 as $obj)
            {
                $obj_imp = DB::table('objectives_impact')
                            ->join('objectives','objectives.id','=','objectives_impact.objective_father_id')
                            ->where('objective_father_id','=',$obj->id)
                            ->select('objectives_impact.objective_impacted_id as id')
                            ->get();

                $i = 0;
                $obj_temp = array();
                foreach ($obj_imp as $impacted)
                {
                    //obtenemos código
                    $code = \Ermtool\Objective::where('id',$impacted->id)->value('code');
                    $description_impacted = \Ermtool\Objective::where('id',$impacted->id)->value('description');
                    $obj_temp[$i] = ['code' => $code, 'description' => $description_impacted];
                    $i += 1;
                }

                $objectives[$j] = [
                    'id' => $obj->id,
                    'code' => $obj->code,
                    'name' => $obj->name,
                    'perspective' => $obj->perspective,
                    'perspective2' => $obj->perspective2,
                    'description' => $obj->description,
                    'impacted' => $obj_temp,
                ];

                $j += 1;

            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.mapas',['organizations' => $organizations, 'vision' => $vision, 'objectives' => $objectives, 'org_selected' => $org_selected]);
            }
            else
            {
                return view('gestion_estrategica.mapas',['organizations' => $organizations, 'vision' => $vision, 'objectives' => $objectives, 'org_selected' => $org_selected]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Nuevo KPI para la organización de id = $id
    public function kpiCreate($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos todos los objetivos de la organización
            $objectives = \Ermtool\Objective::where('objectives.organization_id','=',$id)
                                ->join('strategic_plans','strategic_plans.id','=','objectives.strategic_plan_id')
                                ->where('strategic_plans.status',1)
                                ->where('objectives.status',0)
                                ->select('objectives.id', DB::raw("CONCAT (objectives.code, ' - ', objectives.name) AS code_name"))
                                ->orderBy('code')
                                ->lists('code_name','objectives.id');

            $org_selected = \Ermtool\Organization::where('id',$id)->value('name');

            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.createkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $id,'stakeholders'=>$stakeholders]);
            }
            else
            {
                return view('gestion_estrategica.createkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $id,'stakeholders'=>$stakeholders]);
            }
        }
    }

    //Nuevo KPI para el objetivo de id = $id
    public function kpiCreateFromObjective($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
           
            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.createkpi2',['obj_id' => $id,'stakeholders'=>$stakeholders]);
            }
            else
            {
                return view('gestion_estrategica.createkpi2',['obj_id' => $id,'stakeholders'=>$stakeholders]);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function kpiStore(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            DB::transaction(function() {
                $logger = $this->logger2;

                if ($_POST['calculation_method'] == "")
                {
                    $calc_method = NULL;
                }
                else
                {
                    $calc_method = $_POST['calculation_method'];
                }

                if ($_POST['periodicity'] == "")
                {
                    $periodicity = NULL;
                }
                else
                {
                    $periodicity = $_POST['periodicity'];
                }

                if ($_POST['stakeholder_id'] == "")
                {
                    $stake = NULL;
                }
                else
                {
                    $stake = $_POST['stakeholder_id'];
                }

                if ($_POST['initial_value'] == "")
                {
                    $initial_value = NULL;
                }
                else
                {
                    $initial_value = $_POST['initial_value'];
                }

                if ($_POST['initial_date'] == "")
                {
                    $initial_date = NULL;
                }
                else
                {
                    $initial_date = $_POST['initial_date'];
                }

                if ($_POST['final_date'] == "")
                {
                    $final_date = NULL;
                }
                else
                {
                    $final_date = $_POST['final_date'];
                }

                if ($_POST['goal'] == "")
                {
                    $goal = NULL;
                }
                else
                {
                    $goal = $_POST['goal'];
                }

                //luego de seteados todos los posibles datos nulos, guardamos primero KPI y obtenemos id
                $kpi = \Ermtool\kpi::create([
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'calculation_method' => $calc_method,
                    'periodicity' => $periodicity,
                    'stakeholder_id' => $stake,
                    'initial_date' => $initial_date,
                    'final_date' => $final_date,
                    'initial_value' => $initial_value,
                    'goal' => $goal
                    ]);

                //ahora guardamos en kpi_objective cada objetivo

                foreach ($_POST['objective_id'] as $obj)
                {
                    DB::table('kpi_objective')
                        ->insert([
                            'kpi_id' => $kpi->id,
                            'objective_id' => $obj,
                            'created_at' => date('Y-m-d H:i:s')
                            ]);
                }

                //ahora almacenamos primera evaluación
                if ($_POST['first_evaluation'] != "")
                {
                    //ingresamos según periodicidad
                    if ($_POST['periodicity'] == 3) //Trimestral
                    {
                        //calculamos trimestre
                        if (date('m') >= 1 || date('m') <= 3)
                        {
                            $trim = 1;
                        }
                        else if (date('m') >= 4 || date('m') <= 6)
                        {
                            $trim = 2;
                        }
                        else if (date('m') >= 7 || date('m') <= 9)
                        {
                            $trim = 3;
                        }
                        else if (date('m') >= 10 || date('m') <= 12)
                        {
                            $trim = 4;
                        }

                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'trimester' => $trim,
                                        'year' => date('Y')
                                        ]);
                           
                    }
                    else if ($_POST['periodicity'] == 2)
                    {
                        //calculamos semestre
                        if (date('m') >= 1 || date('m') <= 6)
                        {
                            $sem = 1;
                        }
                        else if (date('m') >= 7 || date('m') <= 12)
                        {
                            $sem = 2;
                        }
                    
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'semester' => $sem,
                                        'year' => date('Y')
                                        ]);

                    }
                    else if ($_POST['periodicity'] == 1) //mensual 
                    {
 
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'month' => date('m'),
                                        'year' => date('Y')
                                        ]);

                    }

                    else //es anual
                    {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'year' => date('Y')
                                        ]);

                    }
                }

                if (isset($kpi))
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','KPI successfully created');
                    }
                    else
                    {
                        Session::flash('message','KPI generado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el KPI con Id: '.$kpi->id.' llamado: '.$kpi->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Error at storing KPI');
                    }
                    else
                    {
                        Session::flash('error','Error al grabar KPI');
                    }
                }
            });

            return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
        }
    }

    public function kpiStoreFromObjective(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            DB::transaction(function() {
                
                $logger = $this->logger2;

                if ($_POST['calculation_method'] == "")
                {
                    $calc_method = NULL;
                }
                else
                {
                    $calc_method = $_POST['calculation_method'];
                }

                if ($_POST['periodicity'] == "")
                {
                    $periodicity = NULL;
                }
                else
                {
                    $periodicity = $_POST['periodicity'];
                }

                if ($_POST['stakeholder_id'] == "")
                {
                    $stake = NULL;
                }
                else
                {
                    $stake = $_POST['stakeholder_id'];
                }

                if ($_POST['measurement_unit'] == "")
                {
                    $measurement_unit = NULL;
                }
                else
                {
                    $measurement_unit = $_POST['measurement_unit'];
                }

                if ($_POST['initial_date'] == "")
                {
                    $initial_date = NULL;
                }
                else
                {
                    $initial_date = $_POST['initial_date'];
                }

                if ($_POST['final_date'] == "")
                {
                    $final_date = NULL;
                }
                else
                {
                    $final_date = $_POST['final_date'];
                }

                if ($_POST['goal'] == "")
                {
                    $goal = NULL;
                }
                else
                {
                    $goal = $_POST['goal'];
                }

                //luego de seteados todos los posibles datos nulos, guardamos primero KPI y obtenemos id
                $kpi = \Ermtool\kpi::create([
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'calculation_method' => $calc_method,
                    'periodicity' => $periodicity,
                    'stakeholder_id' => $stake,
                    'initial_date' => $initial_date,
                    'final_date' => $final_date,
                    'measurement_unit' => $measurement_unit,
                    'calculation_method' => $calc_method,
                    'goal' => $goal
                ]);

                //ahora guardamos en kpi_objective la relación con obj_id

                DB::table('kpi_objective')
                    ->insert([
                        'kpi_id' => $kpi->id,
                        'objective_id' => $_POST['obj_id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                //ahora almacenamos primera evaluación
                if ($_POST['first_evaluation'] == "")
                {
                    //ingresamos según periodicidad
                    if ($_POST['periodicity'] == 3) //Trimestral
                    {
                        //calculamos trimestre
                        if (date('m') >= 1 || date('m') <= 3)
                        {
                            $trim = 1;
                        }
                        else if (date('m') >= 4 || date('m') <= 6)
                        {
                            $trim = 2;
                        }
                        else if (date('m') >= 7 || date('m') <= 9)
                        {
                            $trim = 3;
                        }
                        else if (date('m') >= 10 || date('m') <= 12)
                        {
                            $trim = 4;
                        }

                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'trimester' => $trim,
                                        'year' => date('Y')
                                        ]);
                           
                    }
                    else if ($_POST['periodicity'] == 2)
                    {
                        //calculamos semestre
                        if (date('m') >= 1 || date('m') <= 6)
                        {
                            $sem = 1;
                        }
                        else if (date('m') >= 7 || date('m') <= 12)
                        {
                            $sem = 2;
                        }
                    
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'semester' => $sem,
                                        'year' => date('Y')
                                        ]);

                    }
                    else if ($_POST['periodicity'] == 1) //mensual 
                    {
 
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'month' => date('m'),
                                        'year' => date('Y')
                                        ]);

                    }

                    else //es anual
                    {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $kpi->id,
                                        'value' => $_POST['first_evaluation'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'year' => date('Y')
                                        ]);

                    }
                }

                if (isset($kpi))
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','KPI successfully created');
                    }
                    else
                    {
                        Session::flash('message','KPI generado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el KPI con Id: '.$kpi->id.' llamado: '.$kpi->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Error at storing KPI');
                    }
                    else
                    {
                        Session::flash('error','Error al grabar KPI');
                    }
                }
            });

            return Redirect::to('objective_kpi.'.$_POST['obj_id']);
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
    public function kpiEdit($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $obj_selected = array();
            $kpi = \Ermtool\kpi::find($id);
            //obtenemos todos los objetivos de la organización
            $objectives = \Ermtool\Objective::where('organization_id','=',$_GET['org_id'])->where('status',0)->lists('name','id');

            $org_selected = \Ermtool\Organization::name($_GET['org_id']);

            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

            //obtenemos los objetivos relacionados al kpi
            $objs = DB::table('kpi_objective')
                        ->where('kpi_id','=',$kpi->id)
                        ->select('objective_id')
                        ->get();
            $i = 0;
            foreach ($objs as $obj)
            {
                $obj_selected[$i] = $obj->objective_id;
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.editkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $_GET['org_id'],'stakeholders'=>$stakeholders,'obj_selected' => $obj_selected,'kpi' => $kpi]);
            }
            else
            {
                return view('gestion_estrategica.editkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $_GET['org_id'],'stakeholders'=>$stakeholders,'obj_selected' => $obj_selected,'kpi' => $kpi]);
            }
        }
    }

    //Editar KPI al cual se accedió a través de objetivos (por lo que no existirá la opción de editar los objetivos)
    public function kpiEditFromObjective($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $kpi = \Ermtool\kpi::find($id);

            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.editkpi2',['stakeholders'=>$stakeholders,'kpi' => $kpi,'obj_id' => $_GET['obj_id']]);
            }
            else
            {
                return view('gestion_estrategica.editkpi2',['stakeholders'=>$stakeholders,'kpi' => $kpi,'obj_id' => $_GET['obj_id']]);
            }
        }
    }

    public function kpiEvaluate($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $kpi = \Ermtool\kpi::find($id);

            //asignamos unidad de medida
            switch ($kpi->initial_value) {
                case 1:
                    $kpi->initial_value = 'Clp';
                    break;
                case 2:
                    $kpi->initial_value = 'Us Dlls';
                    break;
                case 3:
                    $kpi->initial_value = '%';
                    break;
                case 4:
                    $kpi->initial_value = '';
                    break;
                    
                default:
                    $kpi->initial_value = '';
                    break;
            }
            
            //obtenemos fecha de término del KPI
            $final_date = $kpi->getFinalDate($kpi->id);

            if ($final_date->final_date != NULL)
            {
                if (Session::get('languaje') == 'en')
                {
                    $meses = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                }
                else
                {
                    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                }
                
                $final_date = explode('-',$final_date->final_date);
                $final_date = $final_date[2].' de '.$meses[$final_date[1]-1].' de '.$final_date[0];
            }
            else
            {
                $final_date = 'No se ha definido';
            }
            

            //obtenemos AÑO de última evaluación
            $year = DB::table('kpi_measurements')
                                ->where('kpi_id','=',$id)
                                ->where('status','=',1)
                                ->max('year');

            $min_year = date('Y')-1; //Se puede medir KPI como mínimo desde el año anterior
            $measurements = 0;

            //ACTUALIZACIÓN 15-03-17: Measurements 
            $measurements2 = 0;

            if ($year) //si es que hay fecha de evaluación
            {
                // Obtenemos el formato para mostrar la fecha de la última evaluación (dependiendo de la periodicidad del KPI)

                if ($kpi->periodicity == 1) //Mensual
                {
                    $month = DB::table('kpi_measurements')
                                ->where('kpi_id','=',$id)
                                ->where('year','=',$year)
                                ->where('status','=',1)
                                ->max('month');

                    $last_eval = $month;

                    //las mediciones irán dentro de los if para poder ordenar según corresponda (por mes, trimestre, semestre o año)
                    $measurements = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','month','year')
                                    ->orderBy('year','desc')
                                    ->orderBy('month','desc')
                                    ->get();
                    
                    $measurements2 = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','month','year')
                                    ->orderBy('year','asc')
                                    ->orderBy('month','asc')
                                    ->get();
            
                }
                else if ($kpi->periodicity == 2) //Semestral
                {
                    $semester = DB::table('kpi_measurements')
                                ->where('kpi_id','=',$id)
                                ->where('year','=',$year)
                                ->where('status','=',1)
                                ->max('semester');
                    
                    $last_eval = $semester;

                    $measurements = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','semester','year')
                                    ->orderBy('year','desc')
                                    ->orderBy('semester','desc')
                                    ->get();

                    $measurements2 = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','semester','year')
                                    ->orderBy('year','asc')
                                    ->orderBy('semester','asc')
                                    ->get();

                }
                else if ($kpi->periodicity == 3) //Trimestral
                {
                    $trimester = DB::table('kpi_measurements')
                                ->where('kpi_id','=',$id)
                                ->where('year','=',$year)
                                ->where('status','=',1)
                                ->max('trimester');

                    $last_eval = $trimester;

                    $measurements = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','trimester','year')
                                    ->orderBy('year','desc')
                                    ->orderBy('trimester','desc')
                                    ->get();

                    $measurements2 = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','trimester','year')
                                    ->orderBy('year','asc')
                                    ->orderBy('trimester','asc')
                                    ->get();
                }
                else if ($kpi->periodicity == 4) //Anual
                {
                    $last_eval = $year;

                    $measurements = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','year')
                                    ->orderBy('year','desc')
                                    ->get();

                    $measurements2 = DB::table('kpi_measurements')
                                    ->where('kpi_id','=',$id)
                                    ->where('status','=',1)
                                    ->select('value','year')
                                    ->orderBy('year','asc')
                                    ->get();
                }
            }
            else
            {
                $last_eval = NULL;
                $year = NULL;
            }

            //ahora verificaremos que existan una evaluación no validada para actualizar
            $eval = DB::table('kpi_measurements')
                                ->where('kpi_id','=',$id)
                                ->where('status','=',0)
                                ->select('id','month','semester','trimester','year','value')
                                ->first();

            if (isset($_GET['org_id']))
            {
                if ($eval)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval, 'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                    else
                    {
                        return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval, 'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year, 'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                    else
                    {
                        return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year, 'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                }
            }
            else if (isset($_GET['obj_id']))
            {
                if ($eval)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'obj_id' => $_GET['obj_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval,  'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                    else
                    {
                        return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'obj_id' => $_GET['obj_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval,  'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'obj_id' => $_GET['obj_id'],'last_eval' => $last_eval,'year' => $year,  'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                    else
                    {
                        return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'obj_id' => $_GET['obj_id'],'last_eval' => $last_eval,'year' => $year,  'measurements' => $measurements,'measurements2' => $measurements2,'final_date' => $final_date]);
                    }
                }
            }
            
        }
    }

    public function kpiStoreEvaluate()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            //verificamos que la eval no exista
            if (isset($_POST['trimestre']))
            {
                $eval = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$_POST['kpi_id'])
                    ->where('trimester','=',$_POST['trimestre'])
                    ->where('year','=',$_POST['ano'])
                    ->where('status','=',1)
                    ->select('id')
                    ->first();
            }
            else if (isset($_POST['mes']))
            {
                $eval = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$_POST['kpi_id'])
                    ->where('month','=',$_POST['mes'])
                    ->where('year','=',$_POST['ano'])
                    ->where('status','=',1)
                    ->select('id')
                    ->first();
            }
            else if (isset($_POST['semestre']))
            {
                $eval = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$_POST['kpi_id'])
                    ->where('semester','=',$_POST['semestre'])
                    ->where('year','=',$_POST['ano'])
                    ->where('status','=',1)
                    ->select('id')
                    ->first();
            }
            else //es anual
            {
                $eval = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$_POST['kpi_id'])
                    ->where('year','=',$_POST['ano'])
                    ->where('status','=',1)
                    ->select('id')
                    ->first();
            }
            if ($eval)
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('error','The evaluation period already exists. Please evaluate on a new period.');
                }
                else
                {
                    Session::flash('error','El periodo de evaluación ya existe. Debe evaluar en un periodo nuevo');
                }
                return Redirect::to('kpi.evaluate.'.$_POST['kpi_id'].'?org_id='.$_POST['org_id'])->withInput();
            }
            else
            {
                DB::transaction(function() {
                    $logger = $this->logger2;
                    if (isset($_POST['trimestre']))
                    {
                            //ahora vemos si se está actualizando o creando una nueva evaluación
                            $eval2 = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$_POST['kpi_id'])
                            ->where('trimester','=',$_POST['trimestre'])
                            ->where('year','=',$_POST['ano'])
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                            if ($eval2) //se está actualizando evaluación
                            {
                                DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                    ->update([
                                        'value' => $_POST['value'],
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                            }
                            else //es evaluación nueva
                            {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $_POST['kpi_id'],
                                        'value' => $_POST['value'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'trimester' => $_POST['trimestre'],
                                        'year' => $_POST['ano']
                                        ]);
                            }

                            Session::forget('error');
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Measurement successfully saved');
                            }
                            else
                            {
                                Session::flash('message','Medición guardada con éxito');
                            }
                    }
                    else if (isset($_POST['semester']))
                    {
                            //ahora vemos si se está actualizando o creando una nueva evaluación
                            $eval2 = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$_POST['kpi_id'])
                            ->where('semester','=',$_POST['semester'])
                            ->where('year','=',$_POST['ano'])
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                            if ($eval2) //se está actualizando evaluación
                            {
                                DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                    ->update([
                                        'value' => $_POST['value'],
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);   
                            }
                            else //es evaluación nueva
                            {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $_POST['kpi_id'],
                                        'value' => $_POST['value'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'semester' => $_POST['semester'],
                                        'year' => $_POST['ano']
                                        ]);
                            }

                            Session::forget('error');
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Measurement successfully saved');
                            }
                            else
                            {
                                Session::flash('message','Medición guardada con éxito');
                            }

                    }
                    else if (isset($_POST['mes']))
                    {
                            //ahora vemos si se está actualizando o creando una nueva evaluación
                            $eval2 = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$_POST['kpi_id'])
                            ->where('month','=',$_POST['mes'])
                            ->where('year','=',$_POST['ano'])
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                            if ($eval2) //se está actualizando evaluación
                            {
                                DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                    ->update([
                                        'value' => $_POST['value'],
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                            }
                            else //es evaluación nueva
                            {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $_POST['kpi_id'],
                                        'value' => $_POST['value'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'month' => $_POST['mes'],
                                        'year' => $_POST['ano']
                                        ]);
                            }

                            Session::forget('error');
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Measurement successfully saved');
                            }
                            else
                            {
                                Session::flash('message','Medición guardada con éxito');
                            }
                    }

                    else //es anual
                    {
                        //ahora vemos si se está actualizando o creando una nueva evaluación
                            $eval2 = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$_POST['kpi_id'])
                            ->where('year','=',$_POST['ano'])
                            ->where('status','=',0)
                            ->select('id')
                            ->first();

                            if ($eval2) //se está actualizando evaluación
                            {
                                DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                    ->update([
                                        'value' => $_POST['value'],
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                            }
                            else //es evaluación nueva
                            {
                                DB::table('kpi_measurements')
                                    ->insert([
                                        'kpi_id' => $_POST['kpi_id'],
                                        'value' => $_POST['value'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'status' => 0,
                                        'year' => $_POST['ano']
                                        ]);
                            }

                            Session::forget('error');
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Measurement successfully saved');
                            }
                            else
                            {
                                Session::flash('message','Medición guardada con éxito');
                            }
                    }
                    //get name of kpi
                    $name = \Ermtool\kpi::name($_POST['kpi_id']);
                    //guardamos logger
                    if ($eval2)
                    {
                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado una medición para el KPI de Id: '.$_POST['kpi_id'].' llamado: '.$name.', con el valor de: '.$_POST['value'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
                    }
                    else
                    {
                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha agregado una medición para el KPI de Id: '.$_POST['kpi_id'].' llamado: '.$name.', con el valor de: '.$_POST['value'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
                    }
                });
                
                if (isset($_POST['org_id']))
                {
                    return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
                }
                else if (isset($_POST['obj_id']))
                {
                    return Redirect::to('objective_kpi.'.$_POST['obj_id']);
                }
            }
        }
    }

    public function kpiValidate($id1)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $id;
            $id = $id1;
            //cambiamos estado de kpi validado
            DB::transaction(function() {

                $logger = $this->logger2;

                $kpi = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$GLOBALS['id'])
                    ->where('status','=',0)
                    ->update([
                        'status' => 1,
                        ]);

                if ($kpi)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','KPI successfully validated');
                    }
                    else
                    {
                        Session::flash('message','KPI validado con éxito');
                    }
                }

                //get name of kpi
                $name = \Ermtool\kpi::name($GLOBALS['id']);

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha validado la última medición para el KPI de Id: '.$GLOBALS['id'].' llamado: '.$name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));  
            });
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function kpiUpdate(Request $request, $id1)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $id;
            $id = $id1;
            DB::transaction(function() {

                $logger = $this->logger2;

                $kpi = \Ermtool\kpi::find($GLOBALS['id']);

                if ($_POST['calculation_method'] == "")
                {
                    $calc_method = NULL;
                }
                else
                {
                    $calc_method = $_POST['calculation_method'];
                }

                if ($_POST['stakeholder_id'] == "")
                {
                    $stake = NULL;
                }
                else
                {
                    $stake = $_POST['stakeholder_id'];
                }

                if ($_POST['initial_date'] == "")
                {
                    $initial_date = NULL;
                }
                else
                {
                    $initial_date = $_POST['initial_date'];
                }

                if ($_POST['final_date'] == "")
                {
                    $final_date = NULL;
                }
                else
                {
                    $final_date = $_POST['final_date'];
                }

                if ($_POST['goal'] == "")
                {
                    $goal = NULL;
                }
                else
                {
                    $goal = $_POST['goal'];
                }

                $kpi->name = $_POST['name'];
                $kpi->description = $_POST['description'];
                $kpi->calculation_method = $calc_method;
                $kpi->stakeholder_id = $stake;
                $kpi->initial_date = $initial_date;
                $kpi->final_date = $final_date;
                $kpi->goal = $goal;

                //si es que estamos editando a través del monitor de KPI
                if (isset($_POST['objectives_id']))
                {  
                    //primero que todo, eliminaremos los objetivos anteriores del kpi para evitar repeticiones
                    DB::table('kpi_objective')->where('kpi_id',$GLOBALS['id'])->delete();

                    //ahora, agregamos posibles nuevas relaciones
                    foreach($_POST['objectives_id'] as $obj_id)
                    {
                        DB::table('kpi_objective')->insert([
                            'objective_id'=>$obj_id,
                            'kpi_id'=>$GLOBALS['id']
                            ]);
                    }
                }

                $kpi->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','KPI successfully generated');
                }
                else
                {
                    Session::flash('message','KPI generado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el KPI de Id: '.$GLOBALS['id'].' llamado: '.$kpi->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));  
            });

            if (isset($_POST['objectives_id']))
            {
                return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
            }
            else
            {
                return Redirect::to('objective_kpi.'.$_POST['obj_id']);
            }
        }
    }

/* ACTUALIZACIÓN HECHA POR EUGENIO: (26-09): No se usa monitor KPI en su forma antigua, sino que ahora monitor KPI será la sección de Gestión de KPI.

    public function kpiMonitor()
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
                return view('en.gestion_estrategica.monitorkpi',['organizations' => $organizations]);
            }
            else
            {
                return view('gestion_estrategica.monitorkpi',['organizations' => $organizations]);
            }
        }
    }

    public function kpiMonitor2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            $org_selected = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');

            //obtenemos info del KPI
            $kpi = \Ermtool\kpi::find($_GET['kpi_id']);

            //obtenemos mediciones del KPI
            $measures = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$_GET['kpi_id'])
                        ->select('value','month','trimester','semester','year')
                        ->get();

            //obtenemos datos del responsable
            if ($kpi->stakeholder_id != NULL)
            {
                $stake = \Ermtool\Stakeholder::where('id',$kpi->stakeholder_id)->select('name','surnames')->first();
            }
            else
            {
                $stake = NULL;
            }

            if (Session::get('languaje') == 'en')
            {
                $meses = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                $trimestres = ['First quarter','Second quarter','Third quarter','Fourth quarter'];
                $semestres = ['First half','Second half'];

                return view('en.gestion_estrategica.monitorkpi',['organizations' => $organizations,'org_selected' => $org_selected,'kpi' => $kpi, 'measures' => $measures,'stake' => $stake,'meses' => $meses, 'trimestres' => $trimestres, 'semestres' => $semestres]);
            }
            else
            {
                $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                $trimestres = ['Primer trimestre','Segundo trimestre','Tercer trimestre','Cuarto trimestre'];
                $semestres = ['Primer semestre','Segundo semestre'];

                return view('gestion_estrategica.monitorkpi',['organizations' => $organizations,'org_selected' => $org_selected,'kpi' => $kpi, 'measures' => $measures,'stake' => $stake,'meses' => $meses, 'trimestres' => $trimestres, 'semestres' => $semestres]);
            }
        }
    }

    */

    public function getKpi($org)
    {
        //obtenemos KPI de determinada organización
        $kpi = DB::table('kpi')
                ->join('kpi_objective','kpi_objective.kpi_id','=','kpi.id')
                ->join('objectives','objectives.id','=','kpi_objective.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->select('kpi.name','kpi.id')
                ->orderBy('kpi.name')
                ->distinct()
                ->get();

        return json_encode($kpi);
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
}
