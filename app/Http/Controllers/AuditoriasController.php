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
use Auth;
class AuditoriasController extends Controller
{
    //compara scores para ordenar de mayor a menor
    function cmp($a, $b)
    {
        if ($a['score'] == $b['score'])
        {
            return 0;
        }
        return ($a['score'] > $b['score']) ? -1 : 1;
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
            $planes = array();
            $plans = \Ermtool\Audit_plan::all();
            $i = 0; //contador de planes
            foreach ($plans as $plan)
            {
                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($plan['created_at'] == NULL OR $plan['created_at'] == "0000-00-00" OR $plan['created_at'] == "")
                {
                    $fecha_creacion = NULL;
                }
                else
                {
                    $fecha_creacion = date_format($plan['created_at'],"d-m-Y");
                }
                //damos formato a fecha de actualización 
                if ($plan['updated_at'] != NULL)
                {
                    $fecha_act = date_format($plan['updated_at'],"d-m-Y");
                }
                else
                    $fecha_act = NULL;
                $planes[$i] = [
                                'id' => $plan['id'],
                                'name' => $plan['name'],
                                'description' => $plan['description'],
                                'created_at' => $fecha_creacion,
                                'updated_at' => $fecha_act,
                                'status' => $plan['status'],
                            ];
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.index',['planes' => $planes]);
            }
            else
            {
                return view('auditorias.index',['planes' => $planes]);
            }
        }
    }
    public function indexAuditorias()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $audits = array();
            $auditorias = \Ermtool\Audit::all();
            $i = 0; //contador de planes
            foreach ($auditorias as $audit)
            {
                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($audit['created_at'] == NULL OR $audit['created_at'] == "0000-00-00" OR $audit['created_at'] == "")
                {
                    $fecha_creacion = NULL;
                }
                else
                {
                    $fecha_creacion = date_format($audit['created_at'],"d-m-Y");
                }
                //damos formato a fecha de actualización 
                if ($audit['updated_at'] != NULL)
                {
                    $fecha_act = date_format($audit['updated_at'],"d-m-Y");
                }
                else
                    $fecha_act = NULL;
                $audits[$i] = [
                                'id' => $audit['id'],
                                'name' => $audit['name'],
                                'description' => $audit['description'],
                                'created_at' => $fecha_creacion,
                                'updated_at' => $fecha_act
                            ];
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.index_auditorias',['audits'=>$audits]);
            }
            else
            {
                return view('auditorias.index_auditorias',['audits'=>$audits]);
            }
        }
    }
    //Creación de plan de auditoría
    public function create()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos lista de stakeholders (LOS OBTENDREMOS SEGÚN ORGANIZACIÓN)
            //$stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            //->orderBy('name')
            //->lists('full_name', 'id');
            //obtenemos lista de organizaciones
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            //obtenemos universo de auditorias
            $audits = \Ermtool\Audit::lists('name','id');
            //obtenemos riesgos de proceso
            $risk_subprocess = DB::table('risk_subprocess')
                                    ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                    ->where('risks.type2','=',1)
                                    ->select('risk_subprocess.id AS riskid','risks.name')
                                    ->lists('risks.name','riskid');
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.create',[/*'stakeholders'=>$stakeholders,*/'organizations'=>$organizations,
                                            'audits'=>$audits,'risk_subprocess'=>$risk_subprocess]);
            }
            else
            {
                return view('auditorias.create',[/*'stakeholders'=>$stakeholders,*/'organizations'=>$organizations,
                                            'audits'=>$audits,'risk_subprocess'=>$risk_subprocess]);
            }
        }
    }
    //Función con la que primero se seleccionará: Organización
    public function auditPrograms1()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
        if (Session::get('languaje') == 'en')
        {
            return view('en.auditorias.programas',['organizations' => $organizations]);
        }
        else
        {
            return view('auditorias.programas',['organizations' => $organizations]);
        }
    }
    //función para abrir la vista de gestión de programas de auditoría
    public function auditPrograms()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $programas = array();
            $org_name = \Ermtool\Organization::getNameByAuditAuditPlan($_GET['audit_id']);
            $audit_plan_name = \Ermtool\Audit_plan::getNameByAuditAuditPlan($_GET['audit_id']);
            $audit_name = \Ermtool\Audit::name($_GET['audit_id']);
            $programs = \Ermtool\Audit_program::getProgramsByAudit($_GET['audit_id']);
                        
            $i = 0; //contador de planes
            foreach ($programs as $program)
            {
                //AGREGADO 17-08: Obtenemos plan de auditoría y auditoría asociados
                $audit_plan = DB::table('audit_plans')
                            ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                            ->where('audit_audit_plan.id','=',$program->audit_audit_plan_id)
                            ->select('audit_plans.name')
                            ->first();
                $audit = DB::table('audits')
                            ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                            ->where('audit_audit_plan.id','=',$program->audit_audit_plan_id)
                            ->select('audits.name')
                            ->first();
                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($program->created_at == NULL OR $program->created_at == "0000-00-00" OR $program->created_at == "")
                {
                    $fecha_creacion = NULL;
                }
                else
                {
                    //damos formato a fecha inicial
                    $fecha_creacion = date('d-m-Y',strtotime($program->created_at));
                }
                //damos formato a fecha de actualización 
                if ($program->updated_at != NULL)
                {
                    //damos formato a fecha final
                    $fecha_act = date('d-m-Y',strtotime($program->updated_at));
                }
                else
                    $fecha_act = NULL;
                //formato a fecha expiración
                if ($program->expiration_date)
                {
                    $fecha_exp = date('d-m-Y',strtotime($program->expiration_date));
                }
                else
                    $fecha_exp = NULL;
                $programas[$i] = [
                                'id' => $program->id,
                                'name' => $program->name,
                                'description' => $program->description,
                                'created_at' => $fecha_creacion,
                                'updated_at' => $fecha_act,
                                'expiration_date' => $fecha_exp,
                                'audit_plan' => $audit_plan->name,
                                'audit' => $audit->name,
                            ];
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.programas',['programs' => $programas,'org_name' => $org_name, 'audit_plan_name' => $audit_plan_name, 'audit_name' => $audit_name,'audit_id' => $_GET['audit_id']]);
            }
            else
            {
                return view('auditorias.programas',['programs' => $programas,'org_name' => $org_name, 'audit_plan_name' => $audit_plan_name, 'audit_name' => $audit_name,'audit_id' => $_GET['audit_id']]);
            }
        }
    }
    //función crea PROGRAMAS de auditoría
    public function createPruebas($audit_id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //plan de auditoría
            $audit_plans = \Ermtool\Audit_plan::where('status',0)->lists('name','id');
            $audit_programs = \Ermtool\Audit_program::lists('name','id');
             //obtenemos lista de stakeholders
            $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');
            //echo $audit_tests;
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.create_test',['audit_plans'=>$audit_plans,'audit_programs'=>$audit_programs,'stakeholders' => $stakeholders,'audit_id' => $audit_id]);
            }
            else
            {
                return view('auditorias.create_test',['audit_plans'=>$audit_plans,'audit_programs'=>$audit_programs,'stakeholders' => $stakeholders,'audit_id' => $audit_id]);
            }
        }
    }
    public function createAuditoria()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.create_auditoria');
            }
            else
            {
                return view('auditorias.create_auditoria');
            }
        }
    }
    //función que guarda programa de auditoría
    public function storePrueba(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $req;
            $req = $request;
            //creamos una transacción para cumplir con atomicidad
            DB::transaction(function()
            {
                $fecha = date('Y-m-d H:i:s');
                    if (!isset($_POST['description']) || $_POST['description'] == '')
                    {
                        $description = NULL;
                    }
                    else
                    {
                        $description = $_POST['description'];
                    }
                    $audit_program = \Ermtool\Audit_program::create([
                                'name' => $_POST['name'],
                                'description' => $description
                                ]);
                    $audit_program_id = $audit_program->id;
                //insertamos en audit_audit_plan_audit_program
                $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')
                    ->insertGetId([
                            'audit_program_id' => $audit_program_id,
                            'audit_audit_plan_id' => $_POST['audit_id'],
                            'created_at' => $fecha,
                            'updated_at' => $fecha,
                            //'stakeholder_id' => $_POST['stakeholder_id']
                        ]);
                //agregamos evidencia (si es que existe)
                if ($GLOBALS['req']->file('file_program') != NULL)
                {
                    foreach ($GLOBALS['req']->file('file_program') as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'programas_auditoria',$audit_audit_plan_audit_program);
                        }
                    }
                        
                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit program successfully created');
                }
                else
                {
                    Session::flash('message','Programa de auditor&iacute;a creado correctamente');
                }
            });
            return Redirect::to('/programas_auditoria');
        }
    }
    public function storeAuditoria(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            \Ermtool\Audit::create([
                'name' => $request['name'],
                'description' => $request['description']
                ]);
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Audit successfully created');
            }
            else
            {
                Session::flash('message','Auditor&iacute;a creada correctamente');
            }
            return Redirect::to('/auditorias');
        }
    }
    public function ejecutar()
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
                return view('en.auditorias.ejecutar',['organizations' => $organizations]);
            }
            else
            {
                return view('auditorias.ejecutar',['organizations' => $organizations]);
            }
        }
    }
    public function storeEjecution(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //haremos global de request
            global $req;
            $req = $request;
            //print_r($_POST);
            DB::transaction(function() {

                $c = new ControlesController;
                //primero que todo, actualizamos las pruebas
                //para esto, separamos primer string del array id_pruebas por sus comas
                $id_pruebas = explode(',',$_POST['id_pruebas'][0]);
                foreach ($id_pruebas as $id)
                {
                    //actualizamos resultados (ACTUALIZACIÓN 28-10-2016) Solo actualizamos resultados ya que el issue no se tocará en esta sección) (si es que el estado de la prueba es cerrado (igual a 2))
                    if ($_POST['status_'.$id] == 2)
                    {
                        //actualizamos resultado de prueba de identificador $id (status y results)
                        DB::table('audit_tests')
                            ->where('id','=',$id)
                            ->update([ 
                                'status' => $_POST['status_'.$id],
                                'results' => $_POST['test_result_'.$id],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'hh_real' => $_POST['hh_real_'.$id],
                                ]);

                        //obtenemos id de control para la evaluación de riesgo controlado
                        $control = DB::table('audit_tests')
                                    ->where('id','=',$id)
                                    ->select('control_id')
                                    ->first();

                        if (isset($_POST['test_result_'.$id]) && isset($control) && $control != NULL)
                        {
                        
                            if ($control->control_id != NULL)
                            {
                                $result = $c->calcControlValue($control->control_id);

                                $eval = $c->calcControlledRisk($control->control_id);

                                $result = 0;
                            }
                            else
                            {
                                $result = 2;
                            }
                        }
                        else 
                        {
                            $result = 2;
                        }
                        if (Session::get('languaje') == 'en')
                        {
                            if ($result == 0)
                            {
                                echo "Controlled risk successfully updated";
                            }
                            else if ($result == 1)
                            {
                                echo "Error updating value of controlled risk";
                            }
                        }
                        else
                        {
                            if ($result == 0)
                            {
                                echo "Riesgo controlado actualizado correctamente";
                            }
                            else if ($result == 1)
                            {
                                echo "Error al actualizar valor de riesgo controlado";
                            }
                        }
                    }
                    else
                    {
                        //sólo actualizamos resultado de prueba
                        DB::table('audit_tests')
                            ->where('id','=',$id)
                            ->update([ 
                                'status' => $_POST['status_'.$id],
                                'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                    }
                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit successfully executed');
                }
                else
                {
                    Session::flash('message','Auditor&iacute;a ejecutada correctamente');
                }
            });
            
            return Redirect::to('/ejecutar_pruebas');
        }
    }
    public function supervisar()
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
                return view('en.auditorias.supervisar',['organizations' => $organizations]);
            }
            else
            {
                return view('auditorias.supervisar',['organizations' => $organizations]);
            }
        }
    }
    public function storeSupervision(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST); 
            global $evidence;
            $evidence = $request->file('evidencia_'.$request['test_id']);
            //primero vemos si se está agregando una nota o una evidencia
            DB::transaction(function() {
                $res = DB::table('notes')
                        ->insertGetId([
                            'name' => $_POST['name_'.$_POST['test_id']],
                            'description' => $_POST['description_'.$_POST['test_id']],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'audit_test_id' => $_POST['test_id'],
                            'status' => 0
                            ]);
            
                //guardamos archivo de evidencia (si es que hay)
                if($GLOBALS['evidence'] != NULL)
                {
                    foreach ($GLOBALS['evidence'] as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'evidencias_notas',$res);
                        }
                    }
                }
                if ($res)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Note successfully added');
                    }
                    else
                    {
                        Session::flash('message','Nota agregada correctamente');
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Problem adding the note. Try again and if the problem persist, please contact with the admin of the system.');
                    }
                    else
                    {
                        Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
                    }
                }
            });
               
            return Redirect::to('/supervisar');    
        }
    }
    //Antes de almacenar un plan de auditoría, se deben ingresar los datos necesarios
    //para crear un auditoría perteneciente a dicho plan (audit_audit_plan)
    //En esta función se envían los datos del plan para poder generarlo despues en conjunto a su auditoría
    public function datosAuditoria(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            $audits = array();
            $plan = array();
            $objective_id = array();
            $objective_risk_id = array();
            $risk_subprocess_id = array();
            $stakeholder_team = array();
            //primero obtenemos datos de auditorias
            $i = 0;
            foreach ($request['audits'] as $audit)
            {
                $auditoria = DB::table('audits')
                            ->where('id','=',$audit)
                            ->select('name')
                            ->get();
                foreach ($auditoria as $auditoria2)
                {
                    $audits[$i] = [
                            'id' => $audit,
                            'name' => $auditoria2->name
                            ];
                    $i += 1;
                }
            }
            //ahora guardamos (para mandar) los datos del plan de auditoría que se creará
            $plan = [
                    'organization_id' => $request['organization_id'],
                    'name' => $request['name'],
                    'description' => $request['description'],
                    'objectives' => $request['objectives'],
                    'scopes' => $request['scopes'],
                    'resources' => $request['resources'],
                    'stakeholder_id' => $request['stakeholder_id'],
                    'methodology' => $request['methodology'],
                    'initial_date' => $request['initial_date'],
                    'final_date' => $request['final_date'],
                    'rules' => $request['rules']
            ];
            //mandamos equipo de stakeholders (auditores) si es que hay
            if (isset($request['stakeholder_team']))
            {
                foreach ($request['stakeholder_team'] as $stakeholder)
                {
                    $stakeholder_team[$i] = $stakeholder;
                    $i += 1;
                }
            }
            //mandamos objetivos
            $i = 0;
            foreach ($request['objective_id'] as $objective)
            {
                $objective_id[$i] = $objective;
                $i += 1;
            }
                //obtenemos lista de nombre e id de riesgos de objetivo
                $i = 0;
                if (isset($request['objective_risk_id']))
                {
                    foreach ($request['objective_risk_id'] as $objective_risk)
                    {
                        //obtenemos nombre del riesgo de objetivo
                        $risk_name = DB::table('objective_risk')
                                        ->where('objective_risk.id',$objective_risk)
                                        ->join('risks','objective_risk.risk_id','=','risks.id')
                                        ->select('risks.name')
                                        ->get();
                        //almacenamos id y nombre del riesgo
                        foreach ($risk_name as $name)
                        {
                            $objective_risk_id[$i] = [
                                        'id' => $objective_risk,
                                        'name' => $name->name
                                        ];
                            $i += 1;    
                        }
                    }
                }
                //obtenemos lista de nombre e id de riesgos de proceso
                $i = 0;
                if (isset($request['risk_subprocess_id']))
                {
                    foreach ($request['risk_subprocess_id'] as $risk_subprocess)
                    {
                        //obtenemos nombre del riesgo de de proceso
                        $risk_name = DB::table('risk_subprocess')
                                        ->where('risk_subprocess.id',$risk_subprocess)
                                        ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                        ->select('risks.name')
                                        ->get();
                        //almacenamos id y nombre del riesgo
                        foreach ($risk_name as $name)
                        {
                            $risk_subprocess_id[$i] = [
                                        'id' => $risk_subprocess,
                                        'name' => $name->name
                                        ];
                            $i += 1;    
                        }
                    }
                }
            $i = 0;
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.create2',['audits' => $audits,
                                              'plan' => $plan, 
                                              'objective_id' => $objective_id,
                                              'objective_risk_id' => $objective_risk_id,
                                              'risk_subprocess_id' => $risk_subprocess_id,
                                              'stakeholder_team' => $stakeholder_team]);
            }
            else
            {
                return view('auditorias.create2',['audits' => $audits,
                                              'plan' => $plan, 
                                              'objective_id' => $objective_id,
                                              'objective_risk_id' => $objective_risk_id,
                                              'risk_subprocess_id' => $risk_subprocess_id,
                                              'stakeholder_team' => $stakeholder_team]);
            }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            //Mantenemos atomicidad y consistencia
            DB::transaction(function()
            {
                //verificamos datos que no hayan sido ingresados
                if ($_POST['objectives'] == "")
                {
                    $objectives = NULL;
                }
                else
                {
                    $objectives = $_POST['objectives'];
                }
                if ($_POST['scopes'] == "")
                {
                    $scopes = NULL;
                }
                else
                {
                    $scopes = $_POST['scopes'];
                }
                if ($_POST['resources'] == "")
                {
                    $resources = NULL;
                }
                else
                {
                    $resources = $_POST['resources'];
                }
                if ($_POST['methodology'] == "")
                {
                    $methodology = NULL;
                }
                else
                {
                    $methodology = $_POST['methodology'];
                }
                if ($_POST['rules'] == "")
                {
                    $rules = NULL;
                }
                else
                {
                    $rules = $_POST['rules'];
                }

                //insertamos plan y obtenemos ID
                $audit_plan_id = DB::table('audit_plans')->insertGetId([
                        'name'=>$_POST['name'],
                        'description'=>$_POST['description'],
                        'objectives'=>$objectives,
                        'scopes'=>$scopes,
                        'status'=>0,
                        'resources'=>$resources,
                        'methodology'=>$methodology,
                        'initial_date'=>$_POST['initial_date'],
                        'final_date'=>$_POST['final_date'],
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'rules'=>$rules,
                        'organization_id'=>$_POST['organization_id']
                        ]);
                //insertamos en audit_plan_stakeholder primero al encargado del plan y luego al equipo
                DB::table('audit_plan_stakeholder')
                    ->insert([
                        'role' => 0,
                        'audit_plan_id' => $audit_plan_id,
                        'stakeholder_id' => $_POST['stakeholder_id']
                    ]);
                //ahora insertamos equipo de stakeholders (si es que hay)
                if (isset($_POST['stakeholder_team']))
                {
                    foreach ($_POST['stakeholder_team'] as $stakes)
                    {
                        DB::table('audit_plan_stakeholder')
                                ->insert([
                                    'role' => 1,
                                    'audit_plan_id' => $audit_plan_id,
                                    'stakeholder_id' => $stakes
                                    ]);
                    }
                }
                /*  ACTUALIZACIÓN 28-11: Eliminamos esta selección
                if ($_POST['type'] == 0) //se agrego auditoría de procesos
                {
                    //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                    if (isset($_POST['processes_id']))
                    {
                        foreach ($_POST['processes_id'] as $process_id)
                        {
                            //obtenemos subprocesses_risks
                            $risks = DB::table('subprocesses')
                                            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                            ->where('subprocesses.process_id','=',$process_id)
                                            ->select('risk_subprocess.id')
                                            ->get();
                            foreach ($risks as $risk)
                            {
                                //insertamos cada riesgo de subproceso
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $audit_plan_id,
                                        'risk_subprocess_id' => $risk->id
                                        ]);
                            }
                        }
                    }
                }
                else if ($_POST['type'] == 1) //se agregó auditoría de negocios
                {
                    //obtenemos riesgo asociado a cada objetivo (si es que hay)
                    if (isset($_POST['objectives_id']))
                    {
                        foreach ($_POST['objectives_id'] as $objective_id)
                        {
                            //obtenemos objective_risks
                            $risks = DB::table('objectives')
                                    ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                    ->select('objective_risk.id')
                                    ->get();
                            foreach ($risks as $risk)
                            {
                                //insertamos cada riesgo de subproceso
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $audit_plan_id,
                                        'objective_risk_id' => $risk->id
                                        ]);
                            }
                        }
                    }
                }
                else if ($_POST['type'] == 2)
                {
                    //insertamos riesgos del negocio (si es que existen)
                    if (isset($_POST['objective_risk_id']))
                    {
                        foreach ($_POST['objective_risk_id'] as $objective_risk)
                        {
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $audit_plan_id,
                                        'objective_risk_id' => $objective_risk
                                        ]);
                        }
                    }
                    //insertamos riesgos de proceso (si es que existen)
                    if (isset($_POST['risk_subprocess_id']))
                    {
                        foreach ($_POST['risk_subprocess_id'] as $risk_subprocess)
                        {
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $audit_plan_id,
                                        'risk_subprocess_id' => $risk_subprocess
                                        ]);
                        }
                    }
                } */
                //ahora guardamos auditorías que no son nuevas (si es que hay)
                //insertamos cada auditoria (de universo de auditorias) en audit_audit_plan
                $i = 1;
                if (isset($_POST['audits']))
                {
                    foreach ($_POST['audits'] as $audit)
                    {
                        if ($_POST['audit_'.$audit.'_resources'] == "")
                        {
                            $resources = NULL;
                        }
                        else
                        {
                            $resources = $_POST['audit_'.$audit.'_resources'];
                        }

                        //insertamos y obtenemos id para ingresarlo en audit_risk y otros
                        $audit_audit_plan_id = DB::table('audit_audit_plan')
                                    ->insertGetId([
                                        'audit_plan_id' => $audit_plan_id,
                                        'audit_id' => $audit,
                                        'initial_date' => $_POST['audit_'.$audit.'_initial_date'],
                                        'final_date' => $_POST['audit_'.$audit.'_final_date'],
                                        'resources' => $resources,
                                        ]);
                        
                        /* Eliminamos esta selección
                        if ($_POST['type'] == 0) //se agrego auditoría de procesos
                        {
                            //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                            if (isset($_POST['audit_'.$audit.'_processes']))
                            {
                                foreach ($_POST['audit_'.$audit.'_processes'] as $process_id)
                                {
                                    //obtenemos subprocesses_risks
                                    $risks = DB::table('subprocesses')
                                            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                            ->where('subprocesses.process_id','=',$process_id)
                                            ->select('risk_subprocess.id')
                                            ->get();
                                    foreach ($risks as $risk)
                                    {
                                        //insertamos cada riesgo de subproceso
                                        DB::table('audit_risk')
                                            ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $risk->id
                                                ]);
                                    }
                                }
                            }
                        }
                        else if ($_POST['type'] == 1) //se agregó auditoría de negocios
                        {
                            //obtenemos riesgo asociado a cada objetivo (si es que hay)
                            if (isset($_POST['audit_'.$audit.'_objectives']))
                            {
                                foreach ($_POST['audit_'.$audit.'_objectives'] as $objective_id)
                                {
                                    //obtenemos objective_risks
                                    $risks = DB::table('objectives')
                                            ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                            ->select('objective_risk.id')
                                            ->get();
                                    foreach ($risks as $risk)
                                    {
                                        //insertamos cada riesgo de subproceso
                                        DB::table('audit_risk')
                                            ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $risk->id
                                                ]);
                                    }
                                }
                            }
                        }
                        else if ($_POST['type'] == 2)
                        {
                            //insertamos riesgos de negocio de la auditoría
                            if (isset($_POST['audit_'.$audit.'_objective_risks']))
                            {
                                foreach ($_POST['audit_'.$audit.'_objective_risks'] as $audit_objective_risk)
                                {
                                    //insertamos audit risks
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $audit_objective_risk
                                            ]);
                                }
                            }
                            //insertamos riesgos de proceso de la auditoría
                            if (isset($_POST['audit_'.$audit.'_risk_subprocesses']))
                            {
                                foreach ($_POST['audit_'.$audit.'_risk_subprocesses'] as $audit_risk_subprocess)
                                {
                                    //insertamos audit risks
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $audit_risk_subprocess
                                            ]);
                                }
                            }
                        }*/
                    }
                } //fin isset($_POST['audits'])
                //ahora guardamos auditorías nuevas (si es que hay)
                $i = 1; //contador para auditorías nuevas
                if(isset($_POST['audit_new'.$i.'_name']))
                {   
                    while (isset($_POST['audit_new'.$i.'_name']))
                    {
                        //primero insertamos en tabla audits y obtenemos id
                        $audit_id = DB::table('audits')
                                    ->insertGetId([
                                        'name' => $_POST['audit_new'.$i.'_name'],
                                        'description' => $_POST['audit_new'.$i.'_description'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);
                        
                        //ahora insertamos en audit_audit_plan
                        $audit_audit_plan_id = DB::table('audit_audit_plan')
                                    ->insertGetId([
                                        'audit_plan_id' => $audit_plan_id,
                                        'audit_id' => $audit_id,
                                        'initial_date' => $_POST['audit_new'.$i.'_initial_date'],
                                        'final_date' => $_POST['audit_new'.$i.'_final_date'],
                                        'resources' => $_POST['audit_new'.$i.'_resources'],
                                        ]);
                        /* Actualización 28-11: Elminamos esta selección, ya que sólo se realizará en las pruebas de auditoría
                        if ($_POST['type'] == 0) //se agrego auditoría de procesos
                        {
                            //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                            if (isset($_POST['audit_new'.$i.'_processes']))
                            {
                                foreach ($_POST['audit_new'.$i.'_processes'] as $process_id)
                                {
                                    //obtenemos subprocesses_risks
                                    $risks = DB::table('subprocesses')
                                            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                            ->where('subprocesses.process_id','=',$process_id)
                                            ->select('risk_subprocess.id')
                                            ->get();
                                    foreach ($risks as $risk)
                                    {
                                        //insertamos cada riesgo de subproceso
                                        DB::table('audit_risk')
                                            ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $risk->id
                                                ]);
                                    }
                                }
                            }
                        }
                        else if ($_POST['type'] == 1) //se agregó auditoría de negocios
                        {
                            //obtenemos riesgo asociado a cada objetivo (si es que hay)
                            if (isset($_POST['audit_new'.$i.'_objectives']))
                            {
                                foreach ($_POST['audit_new'.$i.'_objectives'] as $objective_id)
                                {
                                    //obtenemos objective_risks
                                    $risks = DB::table('objectives')
                                            ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                            ->select('objective_risk.id')
                                            ->get();
                                    foreach ($risks as $risk)
                                    {
                                        //insertamos cada riesgo de subproceso
                                        DB::table('audit_audit_plan_risk')
                                            ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $risk->id
                                                ]);
                                    }
                                }
                            }
                        }
                        else if ($_POST['type'] == 2)
                        {
                             //insertamos riesgos de negocio (de haber)
                               if (isset($_POST['audit_new'.$i.'_objective_risks']))
                               {
                                    foreach ($_POST['audit_new'.$i.'_objective_risks'] as $objective_risk)
                                    {
                                        DB::table('audit_risk')
                                            ->insert([
                                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                                    'objective_risk_id' => $objective_risk
                                                ]);
                                    }
                               }
                               //insertamos nuevo riesgo de proceso (de haber)
                               if (isset($_POST['audit_new'.$i.'_risk_subprocesses']))
                               {
                                    foreach ($_POST['audit_new'.$i.'_risk_subprocesses'] as $risk_subprocess)
                                    {
                                        DB::table('audit_risk')
                                            ->insert([
                                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                                    'risk_subprocess_id' => $risk_subprocess
                                                ]);
                                    }
                               }
                        }*/           
                       $i += 1;
                    }
                } //fin isset($_POST['audit_new'.$i.'_name']))
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit plan successfully created');
                }
                else
                {
                    Session::flash('message','Plan de auditor&iacute;a generado correctamente');
                }
            });
            return Redirect::to('/plan_auditoria'); 
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $auditorias = array();
            $plan_auditoria = array();
            $objetivos = array();
            $organizacion = NULL;
            $riesgos_neg = array();
            $riesgos_proc = array();
            $j = 0; //contador de auditorías
            $k = 0; //contador de objetivos (nombre)
            $l = 0; //contador de riesgos de negocio
            $i = 0; //contador de riesgos de proceso
            //obtenemos plan de auditoría
            $audit_plan = \Ermtool\Audit_plan::where('id',(int)$id)->get();
            foreach ($audit_plan as $plan)
            {
                //damos formato a estado
                $estado = $plan['status'];
                //damos formato a fecha inicial
                $fecha_inicial = date("d-m-Y",strtotime($plan['initial_date']));
                //damos formato a fecha final
                $fecha_final = date("d-m-Y",strtotime($plan['final_date']));
                //obtenemos organizacion
                $organization = \Ermtool\Organization::where('id',$plan['organization_id'])->get();
                foreach ($organization as $org)
                {
                    //guardamos nombre de organizacion (siempre será el mismo)
                    $organizacion = $org['name'];
                }
                //obtenemos objetivos de negocios y riesgos, según los riesgos de negocio asociados al plan
                $objective = DB::table('audit_plan_risk')
                                    ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$plan['id'])
                                    ->whereNotNull('audit_plan_risk.objective_risk_id')
                                    ->select('objectives.name','objectives.id')
                                    ->distinct()
                                    ->get();
                foreach ($objective as $obj)
                {
                    $objetivos[$k] = $obj->name;
                    $k += 1;
                    //obtenemos riesgos de negocio asociados al objetivo
                    $objective_risk = DB::table('risks')
                                        ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                                        ->where('objective_risk.objective_id','=',$obj->id)
                                        ->select('risks.name')
                                        ->get();
                    foreach ($objective_risk as $risk)
                    {
                        $riesgos_neg[$l] = $risk->name;
                        $l += 1;
                    }
                }
                //obtenemos riesgos de procesos
                $risk_subprocess = DB::table('audit_plan_risk')
                                        ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                        ->where('audit_plan_risk.audit_plan_id','=',$plan['id'])
                                        ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                                        ->select('risks.name')
                                        ->get();
                foreach ($risk_subprocess as $risk)
                {
                    $riesgos_proc[$i] = $risk->name;
                    $i += 1;
                }
                //obtenemos universo de auditorías
                $audits = DB::table('audit_audit_plan')
                            ->where('audit_plan_id','=',$plan['id'])
                            ->get();
                //cada una de las auditorías pertenecientes al plan
                foreach ($audits as $audit)
                {
                    //obtenemos datos de auditoría
                    $audite = \Ermtool\Audit::where('id',$audit->audit_id)->get();
                    foreach ($audite as $audit1)
                    {
                        $auditorias[$j] = [
                                        'name' => $audit1['name'],
                                        'description' => $audit1['description']
                                        ];
                        $j += 1;
                    }
                }
                //obtenemos riesgos relacionados
                //$risks = DB::table('audit_plan_risk')
                $plan_auditoria = [
                                'id' => $plan['id'],
                                'name' => $plan['name'],
                                'description' => $plan['description'],
                                'objectives' => $plan['objectives'],
                                'scopes' => $plan['scopes'],
                                'status' => $estado,
                                'resources' => $plan['resources'],
                                'methodology' => $plan['methodology'],
                                'initial_date' => $fecha_inicial,
                                'final_date' => $fecha_final,
                                'rules' => $plan['rules']
                                ];
                if (Session::get('languaje') == 'en')
                {
                    return view('en.auditorias.show',['plan_auditoria' => $plan_auditoria,
                                                'auditorias' => $auditorias,
                                                'objetivos' => $objetivos,
                                                'organizacion' => $organizacion,
                                                'riesgos_proc' => $riesgos_proc,
                                                'riesgos_neg' => $riesgos_neg]);
                }
                else
                {
                    return view('auditorias.show',['plan_auditoria' => $plan_auditoria,
                                                'auditorias' => $auditorias,
                                                'objetivos' => $objetivos,
                                                'organizacion' => $organizacion,
                                                'riesgos_proc' => $riesgos_proc,
                                                'riesgos_neg' => $riesgos_neg]);
                }
            }
        }
    }
    public function showProgram($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $tests = array();
            //obtenemos datos de programa (sólo el primero ya que id es único)
            $audit_program = DB::table('audit_audit_plan_audit_program')
                                ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                                ->where('audit_audit_plan_audit_program.id','=',$id)
                                ->select('audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description',
                                    'audit_audit_plan_audit_program.created_at','audit_audit_plan_audit_program.expiration_date')
                                ->first();
            //damos formato a fecha creación
            $created_at = date('d-m-Y',strtotime($audit_program->created_at));
            $created_at.= ' a las '.date('H:i:s',strtotime($audit_program->created_at));
            //damos formato a fecha de expiración
            if ($audit_program->expiration_date == NULL)
            {
                $expiration_date = NULL;
            }
            else
            {
                $expiration_date = date('d-m-Y',strtotime($audit_program->expiration_date));
            }
            
            //obtenemos pruebas de auditoría del programa
            $audit_tests = DB::table('audit_tests')
                            ->where('audit_audit_plan_audit_program_id','=',$id)
                            ->select('id','name','description','type','status','results','created_at',
                                     'updated_at','hh_plan','hh_real','stakeholder_id')
                            ->get();
            $i = 0;
            foreach ($audit_tests as $audit_test)
            {
                //obtenemos stakeholder
                if ($audit_test->stakeholder_id == NULL)
                {
                    $stakeholder = NULL;
                    $stakeholder2 = NULL;
                }
                else
                {
                    $stakeholder = \Ermtool\Stakeholder::find($audit_test->stakeholder_id);
                    $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                }
                //damos formato a fecha creación
                $fecha_creacion = date('d-m-Y',strtotime($audit_test->created_at));
                //damos formato a fecha de actualización
                $fecha_act = date('d-m-Y',strtotime($audit_test->updated_at));
                $type = $audit_test->type;
                $status = $audit_test->status;
                $results = $audit_test->results;            
                $description = $audit_test->description;
                $hh = $audit_test->hh_plan;
                $hh_real = $audit_test->hh_real;
                //$evidence = getEvidences(5,$audit_test->id);
                $tests[$i] = [
                    'id' => $audit_test->id,
                    'name' => $audit_test->name,
                    'description' => $description,
                    'type' => $type,
                    'status' => $status,
                    'results' => $results,
                    'created_at' => $fecha_creacion,
                    'updated_at' => $fecha_act,
                    'stakeholder' => $stakeholder2,
                    'hh' => $hh,
                    'hh_real' => $hh_real,
                    //'evidence' => $evidence,
                ];
                $i += 1;
            }
            //$evidence = getEvidences(4,$audit_program->id);
            $programa = [
                'id' => $audit_program->id,
                'name' => $audit_program->name,
                'description' => $audit_program->description,
                'created_at' => $created_at,
                'expiration_date' => $expiration_date,
                'tests' => $tests,
                //'evidence' => $evidence,
            ];
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.show_program',['program'=>$programa]);
            }
            else
            {
                return view('auditorias.show_program',['program'=>$programa]);
            }
        }
    }
    public function editProgram($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')->find($id);
            $audit_program = \Ermtool\Audit_program::find($audit_audit_plan_audit_program->audit_program_id);
            //$evidence = getEvidences(4,$audit_audit_plan_audit_program->id);
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.edit_program',['program'=>$audit_program,
                    'audit_audit_plan_audit_program'=>$audit_audit_plan_audit_program]);
            }
            else
            {
                return view('auditorias.edit_program',['program'=>$audit_program,
                    'audit_audit_plan_audit_program'=>$audit_audit_plan_audit_program]);
            }
        }
    }
    public function editTest($program_id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $audit_test = \Ermtool\Audit_test::find($program_id);
            //obtenemos audit_plan para despues obtener controles, riesgos o subproceso de la prueba
            $audit_plan = DB::table('audit_audit_plan_audit_program')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                        ->where('audit_tests.audit_audit_plan_audit_program_id','=',$audit_test->audit_audit_plan_audit_program_id)
                        ->select('audit_plans.id')
                        ->first();
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');
            //seleccionamos tipo o categoría
            if ($audit_test->control_id != NULL)
            {
                $type2 = 1;
                $type_id = $audit_test->control_id;
            }
            else if ($audit_test->risk_id != NULL)
            {
                $type2 = 2;
                $type_id = $audit_test->risk_id;
            }
            else if ($audit_test->subprocess_id != NULL)
            {
                $type2 = 3;
                $type_id = $audit_test->subprocess_id;
            }
            else
            {
                $type2 = NULL;
                $type_id = NULL;
            }
            //obtenemos evidencias de prueba (si es que existen)
            //$evidence = getEvidences(5,$audit_test->id);
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.edit_test',['audit_test'=>$audit_test,'stakeholders'=>$stakeholders,'type2'=>$type2,'audit_plan' => $audit_plan->id,'type_id'=>$type_id]);
            }
            else
            {
                return view('auditorias.edit_test',['audit_test'=>$audit_test,'stakeholders'=>$stakeholders,'type2'=>$type2,'audit_plan' => $audit_plan->id,'type_id'=>$type_id]);
            }
        }
    }
    public function updateProgram(Request $request, $id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $id1;
            $id1 = $id;
            global $req;
            $req = $request;
            //echo "POST: ";
            //print_r($_POST);
            DB::transaction(function (){
                $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')->find($GLOBALS['id1']);
                $audit_program = \Ermtool\Audit_program::find($audit_audit_plan_audit_program->audit_program_id);
                $audit_program->name = $_POST['name'];
                $audit_program->description = $_POST['description'];
                DB::table('audit_audit_plan_audit_program')
                    ->where('id',$audit_audit_plan_audit_program->id)
                    ->update([
                            'expiration_date' => $_POST['expiration_date'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                //agregamos evidencias (si es que existe)
                if ($GLOBALS['req']->file('file_program') != NULL)
                {
                    foreach ($GLOBALS['req']->file('file_program') as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'programas_auditoria',$audit_audit_plan_audit_program->id);
                        } 
                    }
                }
            
                $audit_program->save();
                //$audit_audit_plan_audit_program->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Program successfully updated');
                }
                else
                {
                    Session::flash('message','Programa actualizado correctamente');
                }
            });
            
            return Redirect::to('/programas_auditoria');
        }
    }
    public function updateTest(Request $request, $id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos audit_tests
            global $audit_test;
            $audit_test = \Ermtool\Audit_test::find($id);
            //echo "POST: ";
            //print_r($_POST);
            global $req;
            $req = $request;
            DB::transaction(function (){
                $GLOBALS['audit_test']->name = $_POST['name'];
                //si es que se ingreso descripción
                if (isset($_POST['description']))
                {
                    $GLOBALS['audit_test']->description = $_POST['description'];
                }
                else
                {
                    $GLOBALS['audit_test']->description = NULL;
                }
                //si es que se ingreso tipo
                if (isset($_POST['type']))
                {
                    $GLOBALS['audit_test']->type = $_POST['type'];
                }
                else
                {
                    $GLOBALS['audit_test']->type = NULL;
                }
                //si es que se ingreso stakeholder
                if (isset($_POST['stakeholder_id']))
                {
                    $GLOBALS['audit_test']->stakeholder_id = $_POST['stakeholder_id'];
                }
                else
                {
                    $GLOBALS['audit_test']->stakeholder_id = NULL;
                }
                //si es que se ingreso HH
                if (isset($_POST['hh']))
                {
                    $GLOBALS['audit_test']->hh_plan = $_POST['hh'];
                }
                else
                {
                    $GLOBALS['audit_test']->hh_plan = NULL;
                }
                //vemos si el tipo de prueba es de control, de proceso o de riesgo
                if ($_POST['type2'] == 1) //es de control
                {
                    if (isset($_POST['control_id_test_1']))
                    {
                        $GLOBALS['audit_test']->control_id = $_POST['control_id_test_1'];
                    }
                }
                else if ($_POST['type2'] == 2) //es de riesgo
                {
                    if (isset($_POST['risk_id_test_1']))
                    {
                        $GLOBALS['audit_test']->risk_id = $_POST['risk_id_test_1'];
                    }
                }
                else if ($_POST['type2'] == 3) //es de proceso
                {
                    if (isset($_POST['subprocess_id_test_1']))
                    {
                        $GLOBALS['audit_test']->subprocess_id = $_POST['subprocess_id_test_1'];
                    }
                }
                if ($GLOBALS['req']->file('file_test') != NULL)
                {
                    foreach ($GLOBALS['req']->file('file_test') as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'pruebas_auditoria',$GLOBALS['audit_test']->id);
                        }
                    }   
                }
                $GLOBALS['audit_test']->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Test successfully updated');
                }
                else
                {
                    Session::flash('message','Prueba actualizada correctamente');
                }
            });
            
            return Redirect::to('programas_auditoria.show.'.$GLOBALS['audit_test']->audit_audit_plan_audit_program_id);
        }
    }
    public function createTest($id_program)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos audit_plan para despues obtener controles, riesgos o subproceso de la prueba
            $audit_plan = DB::table('audit_audit_plan_audit_program')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->where('audit_audit_plan_audit_program.id','=',$id_program)
                        ->select('audit_plans.id')
                        ->first();
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');
            $type2 = NULL;
            $type_id = NULL;
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.create_test2',['audit_program'=>$id_program,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'type2'=>$type2,'type_id'=>$type_id]);
            }
            else
            {
                return view('auditorias.create_test2',['audit_program'=>$id_program,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'type2'=>$type2,'type_id'=>$type_id]);
            }
        }
    }
    public function storeTest(Request $request)
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
            DB::transaction(function () {
                $fecha = date('Y-m-d H:i:s');
                if (isset($_POST['description']))
                {
                    $description = $_POST['description'];
                }
                 else
                {
                    $description = NULL;
                }
                //si es que se ingreso tipo
                if (isset($_POST['type']))
                {
                    $type = $_POST['type'];
                }
                else
                {
                    $type = NULL;
                }
                //si es que se ingreso stakeholder
                if (isset($_POST['stakeholder_id']))
                {
                    $stakeholder = $_POST['stakeholder_id'];
                }
                else
                {
                    $stakeholder = NULL;
                }
                //si es que se ingreso HH
                if (isset($_POST['hh']))
                {
                    $hh = $_POST['hh'];
                }
                else
                {
                    $hh = NULL;
                }

                //ACTUALIZACIÓN 07-12-16: La prueba ya no puede ser de riesgo; estará orientada a proceso o entidad; si es de proceso se podrá se dejará abierto si se seleccionan los controles o los subprocesos o sólo el proceso. A nivel de entidad se seleccionará sólo la perspectiva o se especificarán los controles a nivel de entidad

                if (isset($_POST['process_id']))
                {
                    $process_id = $_POST['process_id'];
                }
                else
                {
                    $process_id = NULL;
                }
                //primero insertamos prueba
                    $test_id = DB::table('audit_tests')
                            ->insertGetId([
                            'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                            'name' => $_POST['name'],
                            'description' => $description, 
                            'type' => $type,
                            'status' => 0,
                            'results' => 2,
                            'created_at' => $fecha,
                            'updated_at' => $fecha,
                            'hh_plan' => $hh,
                            'stakeholder_id' => $stakeholder,
                            'process_id' => $process_id
                    ]);

                if ($_POST['type1'] == 1) //prueba a nivel de proceso
                {
                    //vemos si se especificaron los controles
                    if (isset($_POST['control_id']))
                    {
                        foreach ($_POST['control_id'] as $c)
                        {
                            //primero verificamos que no se encuentre vacío (ya que está enviando el último valor vacío)
                            if ($c != NULL && $c != '')
                            {
                                //almacenamos controles en audit_test_control
                                DB::table('audit_test_control')
                                    ->insert([
                                        'audit_test_id' => $test_id,
                                        'control_id' => $c
                                        ]);
                            }   
                        }
                    }
                    //sino se agregaron controles, vemos si se especificaron subprocesos
                    else if ($_POST['subprocess_id'])
                    {
                        foreach ($_POST['subprocess_id'] as $s)
                        {
                            DB::table('audit_test_subprocess')
                                ->insert([
                                    'audit_test_id' => $test_id,
                                    'subprocess_id' => $s
                                    ]);
                        }
                    }
                }
                else if ($_POST['type1'] == 2) //es prueba a nivel de entidad
                {
                    //vemos si se especificaron los controles
                    $cont = 0;
                    if (isset($_POST['control_id']))
                    {
                        foreach ($_POST['control_id'] as $c)
                        {
                            //primero verificamos que no se encuentre vacío (ya que está enviando el último valor vacío)
                            if ($c != NULL && $c != '')
                            {
                                $cont += 1;
                                //almacenamos controles en audit_test_control
                                DB::table('audit_test_control')
                                    ->insert([
                                        'audit_test_id' => $test_id,
                                        'control_id' => $c
                                        ]);
                            }   
                        }
                    }

                    if ($cont == 0) //no se especificaron controles, por lo que se seleccionan todos los de la organización y perspectiva correspondiente
                    {
                        //obtenemos organización
                        $org = \Ermtool\Organization::getOrgByAuditPlan($_POST['audit_plans']);

                        //obtenemos todos los controles de los objetivos asociados a la org y la perspectiva
                        $controls = \Ermtool\Control::getControlsFromPerspective($org->id,$_POST['perspective']);

                        foreach ($controls as $c)
                        {
                            //almacenamos controles en audit_test_control
                            DB::table('audit_test_control')
                                ->insert([
                                    'audit_test_id' => $test_id,
                                    'control_id' => $c->id
                                ]);
                        }
                    }
                }
                /*
                //vemos si el tipo de prueba es de control, de proceso o de riesgo
                if ($_POST['type2'] == 1) //es de control
                {
                    if (isset($_POST['control_id_test_1']))
                    {
                        $test_id = DB::table('audit_tests')
                                ->insertGetId([
                                'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                'name' => $_POST['name'],
                                'description' => $description, 
                                'type' => $type,
                                'status' => 0,
                                'results' => 2,
                                'created_at' => $fecha,
                                'updated_at' => $fecha,
                                'hh' => $hh,
                                'stakeholder_id' => $stakeholder,
                                'control_id' => $_POST['control_id_test_1']
                        ]);
                    }
                }
                else if ($_POST['type2'] == 2) //es de riesgo
                {
                    if (isset($_POST['risk_id_test_1']))
                    {
                                $test_id = DB::table('audit_tests')
                                ->insertGetId([
                                        'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                        'name' => $_POST['name'],
                                        'description' => $description, 
                                        'type' => $type,
                                        'status' => 0,
                                        'results' => 2,
                                        'created_at' => $fecha,
                                        'updated_at' => $fecha,
                                        'hh' => $hh,
                                        'stakeholder_id' => $stakeholder,
                                        'risk_id' => $_POST['risk_id_test_1'],
                                ]);
                    }
                }
                else if ($_POST['type2'] == 3) //es de proceso
                {
                    if (isset($_POST['subprocess_id_test_1']))
                    {
                                $test_id = DB::table('audit_tests')
                                ->insertGetId([
                                        'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                        'name' => $_POST['name'],
                                        'description' => $description, 
                                        'type' => $type,
                                        'status' => 0,
                                        'results' => 2,
                                        'created_at' => $fecha,
                                        'updated_at' => $fecha,
                                        'hh' => $hh,
                                        'stakeholder_id' => $stakeholder,
                                        'subprocess_id' => $_POST['subprocess_id_test_1'],
                                ]);
                    }
                }
                */

                if ($GLOBALS['req']->file('file_test') != NULL)
                {
                    foreach ($GLOBALS['req']->file('file_test') as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'pruebas_auditoria',$test_id);
                        } 
                    }   
                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit test successfully created');
                }
                else
                {
                    Session::flash('message','Prueba de auditor&iacute;a creada correctamente');
                }
            });
            return Redirect::to('/programas_auditoria.show.'.$_POST['audit_audit_plan_audit_program_id']);
        }
    }
    public function showAuditoria($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $planes = array();
            $k = 0; //contador de planes
            //obtenemos auditoría
            $audite = \Ermtool\Audit::where('id',(int)$id)->get();
            foreach ($audite as $audit)
            {
                //obtenemos planes en los que se está aplicando
                $audit_plans = DB::table('audit_audit_plan')
                            ->where('audit_id','=',$audit['id'])
                            ->get();
                //cada una de los planes en los que se aplica la auditoria
                foreach ($audit_plans as $plan)
                {
                    //obtenemos nombre del plan
                    $plan_name = \Ermtool\Audit_plan::where('id',$plan->audit_plan_id)->value('name');
                    $planes[$k] = [
                            'id' => $plan->audit_plan_id,
                            'name' => $plan_name,
                    ];
                    $k += 1;
                }
                $auditoria = [
                            'id' => $audit['id'],
                            'name' => $audit['name'],
                            'description' => $audit['description']
                ];
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.show_audit',['auditoria' => $auditoria, 'planes' => $planes]);
            }
            else
            {
                return view('auditorias.show_audit',['auditoria' => $auditoria, 'planes' => $planes]);
            }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $riesgos_proc = array();
            $riesgos_neg = array();
            $audits_selected = array();
            $stakeholder_team = array();
             //obtenemos lista de stakeholders
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');
            //obtenemos lista de organizaciones
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            //obtenemos universo de auditorias
            $audits = \Ermtool\Audit::lists('name','id');
            //obtenemos universo de auditorías seleccionadas
            $auditorias = DB::table('audit_audit_plan')
                        ->where('audit_plan_id','=',$id)
                        ->select('audit_audit_plan.audit_id as id')
                        ->get();
            //cada una de las auditorías pertenecientes al plan
            $i = 0;
            foreach ($auditorias as $audit)
            {
                $audits_selected[$i] = $audit->id;
                $i += 1;
            }
            //obtenemos riesgos de procesos que ya fueron seleccionados
            $riesgos_selected = DB::table('audit_plan_risk')
                                    ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$id)
                                    ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                                    ->select('risk_subprocess.id')
                                    ->get();
            $i = 0;
            foreach ($riesgos_selected as $risk)
            {
                $riesgos_proc[$i] = $risk->id;
                $i += 1;
            }
            //obtenemos riesgos de negocio que ya fueron seleccionados
            $riesgos_selected = DB::table('audit_plan_risk')
                                    ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$id)
                                    ->whereNotNull('audit_plan_risk.objective_risk_id')
                                    ->select('objective_risk.id')
                                    ->get();
            $i = 0;
            foreach ($riesgos_selected as $risk)
            {
                $riesgos_neg[$i] = $risk->id;
                $i += 1;
            }
            //obtenemos stakeholder responsable
            $stakeholder1 = DB::table('audit_plan_stakeholder')
                            ->where('audit_plan_id','=',$id)
                            ->where('role','=',0)
                            ->select('stakeholder_id')
                            ->first();
            //obtenemos equipo de auditores
            $stakeholders2 = DB::table('audit_plan_stakeholder')
                                ->where('audit_plan_id','=',$id)
                                ->where('role','=',1)
                                ->select('stakeholder_id')
                                ->get();
            $i = 0;
            foreach ($stakeholders2 as $stakeholder)
            {
                $stakeholder_team[$i] = $stakeholder->stakeholder_id;
                $i += 1;
            }
            //enviamos id de org seleccionada
            $idorg = $id;
            $audit_plan = \Ermtool\Audit_plan::find($id);
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.edit',['stakeholders'=>$stakeholders,'organizations'=>$organizations,
                                            'audits'=>$audits,'riesgos_neg' => json_encode($riesgos_neg),
                                            'riesgos_proc'=>json_encode($riesgos_proc),'audits_selected'=>json_encode($audits_selected),
                                            'stakeholder' => $stakeholder1, 'stakeholder_team' => json_encode($stakeholder_team),
                                            'id'=>$idorg,'audit_plan'=>$audit_plan]);
            }
            else
            {
                return view('auditorias.edit',['stakeholders'=>$stakeholders,'organizations'=>$organizations,
                                            'audits'=>$audits,'riesgos_neg' => json_encode($riesgos_neg),
                                            'riesgos_proc'=>json_encode($riesgos_proc),'audits_selected'=>json_encode($audits_selected),
                                            'stakeholder' => $stakeholder1, 'stakeholder_team' => json_encode($stakeholder_team),
                                            'id'=>$idorg,'audit_plan'=>$audit_plan]);
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
    public function update(Request $request, $idtemp)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            global $id;
            $id = $idtemp;
            //creamos una transacción para cumplir con atomicidad
            DB::transaction(function()
            {
                    if (strpos($_POST['initial_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
                    {
                        //damos formato a fecha de inicio
                        $fecha = explode("/",$_POST['initial_date']);
                        $fecha_inicio = $fecha[2]."-".$fecha[0]."-".$fecha[1];
                    }
                    else
                        $fecha_inicio = $_POST['initial_date'];
                    if (strpos($_POST['final_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
                    {
                        //damos formato a fecha de término
                        $fecha = explode("/",$request['final_date']);
                        $fecha_termino = $fecha[2]."-".$fecha[0]."-".$fecha[1];
                    }
                    else
                        $fecha_termino = $_POST['final_date'];
                    //actualizamos plan
                    DB::table('audit_plans')
                            ->where('id','=',$GLOBALS['id'])
                            ->update([
                            'name'=>$_POST['name'],
                            'description'=>$_POST['description'],
                            'objectives'=>$_POST['objectives'],
                            'scopes'=>$_POST['scopes'],
                            'status'=>0,
                            'resources'=>$_POST['resources'],
                            'methodology'=>$_POST['methodology'],
                            'initial_date'=>$fecha_inicio,
                            'final_date'=>$fecha_termino,
                            'updated_at'=>date('Y-m-d H:i:s'),
                            'rules'=>$_POST['rules'],
                            'organization_id'=>$_POST['organization_id']
                            ]);
                    //primero actuakizamos stakeholder responsable
                    $resp = DB::table('audit_plan_stakeholder')
                            ->where('audit_plan_id','=',$GLOBALS['id'])
                            ->where('role','=',0)
                            ->update([
                                'stakeholder_id' => $_POST['stakeholder_id']
                                ]);
                    
                    //ahora actualizamos equipo de stakeholders (si es que hay)
                    if (isset($request['stakeholder_team']))
                    {   
                        //eliminamos antiguos stakeholders del plan (que su rol sea 1)
                        DB::table('audit_plan_stakeholder')
                        ->where('audit_plan_id',$GLOBALS['id'])
                        ->where('role','=',1)
                        ->delete();
                        //ahora agregamos cada uno
                        foreach ($_POST['stakeholder_team'] as $stakes)
                        {
                            DB::table('audit_plan_stakeholder')
                                    ->insert([
                                        'role' => 1,
                                        'audit_plan_id' => $GLOBALS['id'],
                                        'stakeholder_id' => $stakes
                                        ]);
                        }
                    }
                     //actualizamos riesgos del negocio (si es que existen)
                    if (isset($_POST['objective_risk_id']))
                    {
                        //eliminamos antiguos si es que existen
                        DB::table('audit_plan_risk')
                        ->where('audit_plan_id',$GLOBALS['id'])
                        ->whereNotNull('objective_risk_id')
                        ->delete();
                        foreach ($_POST['objective_risk_id'] as $objective_risk)
                        {
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $GLOBALS['id'],
                                        'objective_risk_id' => $objective_risk
                                        ]);
                        }
                    }
                    //actualizamos riesgos de proceso (si es que existen)
                    if (isset($_POST['risk_subprocess_id']))
                    {
                        //eliminamos antiguos si es que existen
                        DB::table('audit_plan_risk')
                        ->where('audit_plan_id',$GLOBALS['id'])
                        ->whereNotNull('risk_subprocess_id')
                        ->delete();
                        foreach ($_POST['risk_subprocess_id'] as $risk_subprocess)
                        {
                                DB::table('audit_plan_risk')
                                    ->insert([
                                        'audit_plan_id' => $GLOBALS['id'],
                                        'risk_subprocess_id' => $risk_subprocess
                                        ]);
                        }
                    }
        /* PRIMERO DEBO HACER MODIFICABLE LAS AUDITORÍAS DEL PLAN 
                    //ahora guardamos auditorías que no son nuevas
                    //insertamos cada auditoria (de universo de auditorias) en audit_audit_plan
                    $i = 1;
                    if (isset($request['audits']))
                    {
                        foreach ($request['audits'] as $audit)
                        {
                            //insertamos y obtenemos id para ingresarlo en audit_risk y otros
                            $audit_audit_plan_id = DB::table('audit_audit_plan')
                                        ->insertGetId([
                                            'audit_plan_id' => $audit_plan_id,
                                            'audit_id' => $audit,
                                            'initial_date' => $request['audit_'.$audit.'_initial_date'],
                                            'final_date' => $request['audit_'.$audit.'_final_date'],
                                            'resources' => $request['audit_'.$audit.'_resources']
                                            ]);
                            
                            //insertamos riesgos de negocio de la auditoría
                            if (isset($request['audit_'.$audit.'_objective_risks']))
                            {
                                foreach ($request['audit_'.$audit.'_objective_risks'] as $audit_objective_risk)
                                {
                                    //insertamos audit risks
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $audit_objective_risk
                                            ]);
                                }
                            }
                            //insertamos riesgos de proceso de la auditoría
                            if (isset($request['audit_'.$audit.'_risk_subprocess']))
                            {
                                foreach ($request['audit_'.$audit.'_risk_subprocess'] as $audit_risk_subprocess)
                                {
                                    //insertamos audit risks
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $audit_risk_subprocess
                                            ]);
                                }
                            }
                        }
    */
                        //ahora guardamos auditorías nuevas
                        $i = 1; //contador para auditorías nuevas
                        while (isset($_POST['audit_new'.$i.'_name']))
                        {
                            //primero insertamos en tabla audits y obtenemos id
                            $audit_id = DB::table('audits')
                                        ->insertGetId([
                                            'name' => $_POST['audit_new'.$i.'_name'],
                                            'description' => $_POST['audit_new'.$i.'_description'],
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                            ]);
                            //ahora insertamos en audit_audit_plan
                            $audit_audit_plan_id = DB::table('audit_audit_plan')
                                        ->insertGetId([
                                            'audit_plan_id' => $GLOBALS['id'],
                                            'audit_id' => $audit_id,
                                            'initial_date' => $_POST['audit_new'.$i.'_initial_date'],
                                            'final_date' => $_POST['audit_new'.$i.'_final_date'],
                                            'resources' => $_POST['audit_new'.$i.'_resources']
                                            ]);
                            //insertamos riesgos de negocio (de haber)
                           if (isset($_POST['audit_new'.$i.'_objective_risks']))
                           {
                                foreach ($_POST['audit_new'.$i.'_objective_risks'] as $objective_risk)
                                {
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $objective_risk
                                            ]);
                                }
                           }
                           //insertamos nuevo riesgo de proceso (de haber)
                           if (isset($_POST['audit_new'.$i.'_objective_risks']))
                           {
                                foreach ($_POST['audit_new'.$i.'_risk_subprocess'] as $risk_subprocess)
                                {
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $risk_subprocess
                                            ]);
                                }
                           }
                           $i += 1;
                        }
    // DEBO AGREGAR MODIFICACIÓN A AUDITORIAS ANTIGUAS                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit plan successfully updated');
                }
                else   
                {
                    Session::flash('message','Plan de auditor&iacute;a actualizado correctamente');
                }
            });
            return Redirect::to('/plan_auditoria');
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
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;
        DB::transaction(function() {
            //primero obtenemos audit_audit_plan para hacer las validaciones
            $audit_audit_plan = DB::table('audit_audit_plan')
                        ->where('audit_plan_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();
            $rev2 = 1; //variable local para ver 
            foreach ($audit_audit_plan as $audit)
            {
                //vemos si tiene issues
                $rev = DB::table('issues')
                    ->where('audit_audit_plan_id','=',$audit->id)
                    ->select('id')
                    ->get();
                if (empty($rev))
                {
                    //ahora vemos notas de supervisor
                    $rev = DB::table('supervisor_notes')
                        ->where('audit_audit_plan_id','=',$audit->id)
                        ->select('id')
                        ->get();
                    if (empty($rev))
                    {
                        //audit_audit_plan_audit_program
                        $rev = DB::table('audit_audit_plan_audit_program')
                            ->where('audit_audit_plan_id','=',$audit->id)
                            ->select('id')
                            ->get();
                        if (empty($rev))
                        {
                            //podemos eliminar
                            $rev2 = 0;
                        }
                        else
                        {
                            $rev2 = 1;
                        }
                    }
                    else
                    {
                        $rev2 = 1;
                    }
                }
                else
                {
                    $rev2 = 1;
                }
                if ($rev2 == 1)
                {
                    break;
                }
            }
            if ($rev2 == 0)
            {
                //se puede eliminar
                //primero eliminaremos todo lo que tiene que ver con audit_audit_plan, por lo que volvemos a recorrer todas las audit_audit_plan
                foreach ($audit_audit_plan as $audit)
                {
                    //eliminamos audit_risk
                    DB::table('audit_risk')
                        ->where('audit_audit_plan_id','=',$audit->id)
                        ->delete();
                    //ahora eliminamos audit_audit_plan
                    DB::table('audit_audit_plan')
                        ->where('id','=',$audit->id)
                        ->delete();
                }
                //eliminamos audit_plan_risk
                DB::table('audit_plan_risk')
                    ->where('audit_plan_id','=',$GLOBALS['id1'])
                    ->delete();
                //eliminamos audit_plan_stakeholder
                DB::table('audit_plan_stakeholder')
                    ->where('audit_plan_id','=',$GLOBALS['id1'])
                    ->delete();
                //ahora eliminamos audit_plan
                DB::table('audit_plans')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();
                $GLOBALS['res'] = 0;
            }
        });
        return $res;
    }
    public function destroyProgram($id)
    {
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;
        DB::transaction(function() {
            //revisaremos las pruebas de auditoría asociadas al programa
            $audit_tests = DB::table('audit_tests')
                    ->where('audit_audit_plan_audit_program_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();
            $rev2 = 0;
            foreach ($audit_tests as $audit_test)
            {
                //revisaremos los campos para audit_test
                $rev = DB::table('issues')
                    ->where('audit_test_id','=',$audit_test->id)
                    ->select('id')
                    ->get();
                if (empty($rev))
                {
                    //revisamos supervisor_notes
                    $rev = DB::table('supervisor_notes')
                        ->where('audit_test_id','=',$audit_test->id)
                        ->select('id')
                        ->get();
                    if (empty($rev))
                    {
                        //por último, notas
                        $rev = DB::table('notes')
                            ->where('audit_test_id','=',$audit_test->id)
                            ->select('id')
                            ->get();
                        if (empty($rev))
                        {
                            $rev2 = 0;
                        }
                        else
                        {
                            $rev2 = 1;
                        }
                    }
                    else
                    {
                        $rev2 = 1;
                    }
                }
                else
                {
                    $rev2 = 1;
                }
                if ($rev2 == 1)
                {
                    break;
                }
            }
            if ($rev2 == 1)
            {
               return $GLOBALS['res'];
            }
            else //ahora revisamos todos los campos del programa (que son los mismos de la prueba)
            {
                //revisaremos los campos para audit_test
                $rev = DB::table('issues')
                    ->where('audit_audit_plan_audit_program_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();
                if (empty($rev))
                {
                    //revisamos supervisor_notes
                    $rev = DB::table('supervisor_notes')
                        ->where('audit_audit_plan_audit_program_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();
                    if (empty($rev))
                    {
                        //por último, notas
                        $rev = DB::table('notes')
                            ->where('audit_audit_plan_audit_program_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();
                        if (empty($rev))
                        {
                            $rev2 = 0;
                        }
                        else
                        {
                            $rev2 = 1;
                        }
                    }
                    else
                    {
                        $rev2 = 1;
                    }
                }
                else
                {
                    $rev2 = 1;
                }
            }
            if ($rev2 == 0) //sólo si es igual a cero se podrán borrar todas las tablas
            {
                //eliminamos pruebas de auditoría
                DB::table('audit_tests')
                    ->where('audit_audit_plan_audit_program_id','=',$GLOBALS['id1'])
                    ->delete();
                //para ver si borramos el programa de auditoría en la tabla audit_programs, vemos si éste está presente en otros programas
                $program = DB::table('audit_audit_plan_audit_program')->where('id','=',$GLOBALS['id1'])->select('audit_program_id')->first();
                $rev3 = DB::table('audit_audit_plan_audit_program')
                    ->where('audit_program_id','=',$program->audit_program_id)
                    ->where('id','<>',$GLOBALS['id1'])
                    ->select('id')
                    ->get();
                //ahora eliminamos programa por completo
                DB::table('audit_audit_plan_audit_program')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();
                if (empty($rev3)) //si no hay otros planes, borramos
                {
                    DB::table('audit_programs')
                        ->where('id','=',$program->audit_program_id)
                        ->delete();
                }
                $GLOBALS['res'] = 0;
            }
            
        });
        return $res;
    }
    public function destroyTest($id)
    {
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;
        DB::transaction(function() {
            //revisaremos los campos para audit_test
            $rev = DB::table('issues')
                ->where('audit_test_id','=',$GLOBALS['id1'])
                ->select('id')
                ->get();
            if (empty($rev))
            {
                //revisamos supervisor_notes
                $rev = DB::table('supervisor_notes')
                        ->where('audit_test_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();
                if (empty($rev))
                {
                    //por último, notas
                    $rev = DB::table('notes')
                        ->where('audit_test_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();
                    if (empty($rev))
                    {
                        //ahora se puede eliminar
                        DB::table('audit_tests')
                            ->where('id','=',$GLOBALS['id1'])
                            ->delete();
                        $GLOBALS['res'] = 0;
                    } 
                } 
            }
        });
        return $res;
    }
    //función para ver todas las pruebas
    public function pruebas()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $audit_plans = \Ermtool\Audit_plan::lists('name','id');
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.pruebas',['audit_plans' => $audit_plans]);
            }
            else
            {
                return view('auditorias.pruebas',['audit_plans' => $audit_plans]);
            }
        }
    }
    //función para revisar las notas agregadas por el auditor jefe
    public function notas()
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
                return view('en.auditorias.notas',['organizations' => $organizations]);
            }
            else
            {
                return view('auditorias.notas',['organizations' => $organizations]);
            }
        }
    }
    //función para responder una nota por parte de un auditori
    public function responderNota(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            $id = $_POST['note_id'];
            global $evidence;
            $evidence = $request->file('evidencia_'.$id);
            DB::transaction(function() {
                $id = $_POST['note_id'];
                //insertamos y obtenemos id para verificar que se guarde
                $res = DB::table('notes_answers')
                        ->insertGetId([
                                'answer' => $_POST['answer_'.$id],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'note_id' => $id 
                            ]);
                //guardamos archivo de evidencia (si es que hay)
                if($GLOBALS['evidence'] != NULL)
                {
                    foreach ($GLOBALS['evidence'] as $file)
                    {
                        if ($file != NULL)
                        {
                            upload_file($file,'evidencias_resp_notas',$res);
                        }
                    }
                }
                if ($res)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Answer successfully added');
                    }
                    else
                    {
                        Session::flash('message','Respuesta agregada correctamente');
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Problem adding the note. Try again and if the problem persist, please contact with the admin of the system.');
                    }
                    else
                    {
                        Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
                    }
                } 
            });
            return Redirect::to('/notas');
        }
    }
    public function actionPlans()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            //$stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            //->orderBy('name')
            //->lists('full_name', 'id');
            $stakeholders = array();
            $stakes = DB::table('stakeholders')->select('id','name','surnames')->get();
            $i = 0;
            foreach ($stakes as $stake)
            {
                $stakeholders[$i] = [
                    'id' => $stake->id,
                    'name' => $stake->name.' '.$stake->surnames,
                ];
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.auditorias.planes_accion',['organizations' => $organizations,'stakeholders' => $stakeholders]);
            }
            else
            {
                return view('auditorias.planes_accion',['organizations' => $organizations,'stakeholders' => $stakeholders]);
            }
        }
    }
    public function storePlan(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            DB::transaction(function() {
                //id del issue que se está agregando
                $id = $_POST['issue_id'];
                $new_id = DB::table('action_plans')
                                ->insertGetId([
                                        'issue_id' => $id,
                                        'stakeholder_id' => $_POST['responsable_'.$id],
                                        'description' => $_POST['description_'.$id],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'final_date' => $_POST['final_date_'.$id],
                                        'status' => 0
                                    ]);
                if (Session::get('languaje') == 'en')
                {
                    if ($new_id)
                    {
                        Session::flash('message','Action plan successfully added');
                    }
                    else
                    {
                        Session::flash('error','Problems adding the action plan. Try again and if the problem persist, please contact with the system administrator.');
                    }
                }
                else
                {
                    if ($new_id)
                    {
                        Session::flash('message','Plan de acción agregado correctamente');
                    }
                    else
                    {
                        Session::flash('error','Problema al agregar plan de acción. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
                    }
                }
            });
            
            return Redirect::to('/planes_accion');
        }
    }
    //función para reporte de planes de acción
    public function actionPlansReport()
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
    //función para reporte de auditorías
    public function auditsReport()
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
                return view('en.reportes.auditorias',['organizations' => $organizations]);
            }
            else
            {
                return view('reportes.auditorias',['organizations' => $organizations]);
            }
        }
    }
    //reporte de auditorías
    public function generarReporteAuditorias($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit'))
            {
                $org_id = $org;
            }
            else
            {
                $org_id = $_GET['organization_id'];
            }
            $results = array();
            $i = 0;
            
            $audit_plans = DB::table('audit_plans')
                        ->where('audit_plans.organization_id','=',$org_id)
                        ->select('id','name','description','objectives','scopes','status','resources','methodology','initial_date','final_date','rules')
                        ->get();
            foreach ($audit_plans as $plan)
            {
                if ($plan->objectives == "" || $plan->objectives == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $objectives = 'No objectives have been added';
                    }
                    else
                    {
                        $objectives = 'No se agregaron objetivos';
                    }
                }
                else
                    $objectives = $plan->objectives;
                if ($plan->scopes == "" || $plan->scopes == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $scopes = 'No scopes have been added';
                    }
                    else
                    {
                        $scopes = 'No se agregaron alcances';
                    }
                }
                else
                    $scopes = $plan->scopes;
                if ($plan->resources == "" || $plan->resources == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $resources = 'No resources have been added';
                    }
                    else
                    {
                        $resources = 'No se agregaron recursos';
                    }
                }
                else
                    $resources = $plan->resources;
                if ($plan->methodology == "" || $plan->methodology == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $methodology = 'No methodology have been added';
                    }
                    else
                    {
                        $methodology = 'No se agregó metodología';
                    }
                }
                else
                    $methodology = $plan->methodology;
                if ($plan->rules == "" || $plan->rules == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $rules = 'No rules have been added';
                    }
                    else
                    {
                        $rules = 'No se agregaron reglas';
                    }
                }
                else
                    $rules = $plan->rules;

                /*
                if ($plan->hh == "" || $plan->hh == NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $hh = 'No hour-man have been added';
                    }
                    else
                    {
                        $hh = 'No se agregaron horas-hombre';
                    }
                }
                else
                    $hh = $plan->hh;
                */

                //obtenemos horas hombre a través de las pruebas de auditoría
                $hh = \Ermtool\Audit_plan::getHH($plan->id);
                $hh_plan = 0;
                $hh_real = 0;
                foreach ($hh as $h)
                {
                    if ($h->hh_plan != NULL)
                    {
                        $hh_plan = $hh_plan + $h->hh_plan;
                    }
                    
                    if ($h->hh_real != NULL)
                    {
                        $hh_real = $hh_real + $h->hh_real;
                    }
                }
                $initial_date = new DateTime($plan->initial_date);
                $initial_date = date_format($initial_date, 'd-m-Y');
                //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
                $fecha_final = date('d-m-Y',strtotime($plan->final_date));
                $final_date = new DateTime($plan->final_date);
                $final_date = date_format($final_date, 'd-m-Y');
                if ($plan->status == 0)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $estado_plan = 'Open';
                    }
                    else
                    {
                        $estado_plan = 'Abierto';
                    }
                }
                else if ($plan->status == 1)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $estado_plan = 'Closed';
                    }
                    else
                    {
                        $estado_plan = 'Cerrado';
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $estado_plan = 'Error obtaining status';
                    }
                    else
                    {
                        $estado_plan = 'Error al obtener estado';
                    }
                }
                //obtenemos auditorías
                $audits = DB::table('audits')
                            ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                            ->where('audit_audit_plan.audit_plan_id','=',$plan->id)
                            ->select('name','audit_audit_plan.id')
                            ->get();
                
                if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit'))
                {    
                    $auditorias = "";
                    $last = end($audits); //guardamos final para no agregarle coma
                    foreach ($audits as $audit)
                    {
                        if ($audit != $last)
                        {
                            $auditorias .= $audit->name.', ';
                        }
                        else
                            $auditorias .= $audit->name;
                    }
                }
                else
                {
                    $j = 0;
                    $auditorias = array();
                    foreach ($audits as $audit)
                    {
                        $auditorias[$j] = ['id' => $audit->id, 'name' => $audit->name];
                        $j += 1;
                    }
                }
                if (Session::get('languaje') == 'en')
                {
                    $results[$i] = [
                                'Audit_plan' => $plan->name,
                                'Description' => $plan->description,
                                'Audits' => $auditorias,
                                'Objectives' => $objectives,
                                'Scopes' => $scopes,
                                'Resources' => $resources,
                                'Methodology' => $methodology,
                                'Rules' => $rules,
                                'Hours_man_plan' => $hh_plan,
                                'Hours_man_real' => $hh_real,
                                'Initial_date' => $initial_date,
                                'Final_date' => $final_date,
                            
                    ];
                }
                else
                {
                    $results[$i] = [
                                'Plan_de_auditoría' => $plan->name,
                                'Descripción_plan' => $plan->description,
                                'Auditorías' => $auditorias,
                                'Objetivos' => $objectives,
                                'Alcances' => $scopes,
                                'Recursos' => $resources,
                                'Metodología' => $methodology,
                                'Normas' => $rules,
                                'Horas_hombre_plan' => $hh_plan,
                                'Horas_hombre_real' => $hh_real,
                                'Fecha_inicio' => $initial_date,
                                'Fecha_fin' => $final_date,
                            
                    ];
                }
                $i += 1;
            }
            if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
            {
                return $results;
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.auditorias',['audit_plans' => $results,'organizations' => $organizations,'org_selected' => $_GET['organization_id']]);
                }
                else
                {
                    return view('reportes.auditorias',['audit_plans' => $results,'organizations' => $organizations,'org_selected' => $_GET['organization_id']]);
                }
            }
        }
    }
    //función que obtiene objetivos y riesgos de objetivos a través de JsON para la creación de un plan de pruebas
    public function getObjetivos($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $objectives = \Ermtool\Objective::where('organization_id',(int)$org)->get();
            $i = 0; //contador de objetivos
            foreach ($objectives as $objective)
            {
                $results[$i] = [
                                'name' => $objective['name'],
                                'id' => $objective['id']
                                ];
                $i += 1;
            }
            return json_encode($results);
        }
    }
    //Función obtiene riesgos de negocio a través de JSON al crear plan de pruebas (también utilizado para crear encuesta de evaluación)
    public function getRiesgosObjetivos($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $i = 0; //contador de riesgos de negocio
                //obtenemos riesgos de negocio para la organización seleccionada
                $objective_risk = DB::table('objective_risk')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->where('objectives.organization_id','=',(int)$org)
                                    ->groupBy('risks.id')
                                    ->orderBy('risks.name')
                                    ->select('risks.name','objective_risk.id as riskid')
                                    ->get();
            foreach ($objective_risk as $risk)
            {
                //obtengo maxima fecha para obtener última evaluación
                $fecha = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.objective_risk_id','=',$risk->riskid)
                                ->max('updated_at');
                //obtenemos evaluación de riesgo de negocio (si es que hay)---> Ultima (mayor fecha updated_at)
                $evaluations = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.objective_risk_id','=',$risk->riskid)
                                ->where('evaluations.consolidation','=',1)
                                ->whereNotNull('evaluation_risk.avg_probability')
                                ->whereNotNull('evaluation_risk.avg_impact')
                                ->where('evaluations.updated_at','=',$fecha)
                                ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->get();
                //seteamos por si no hay evaluación
                $avg_probability = "Falta Eval.";
                $avg_impact = "Falta Eval.";
                $score = "Falta Eval.";
                $proba_def = "";
                $impact_def = "";
                foreach ($evaluations as $evaluation)
                {
                    //seteamos nombres de probabilidad e impacto
                    if (Session::get('languaje') == 'en')
                    {
                        switch ($evaluation->avg_probability)
                        {
                            case 1:
                                $proba = 'Very improbable';
                                break;
                            case 2:
                                $proba = 'unlikely';
                                break;
                            case 3:
                                $proba = 'Possible';
                                break;
                            case 4:
                                $proba = 'Likely';
                                break;
                            case 5:
                                $proba = 'Very likely';
                                break;
                        }
                        switch ($evaluation->avg_impact)
                        {
                            case 1:
                                $impact = 'Despicable';
                                break;
                            case 2:
                                $impact = 'Less';
                                break;
                            case 3:
                                $impact = 'Moderate';
                                break;
                            case 4:
                                $impact = 'Severe';
                                break;
                            case 5:
                                $impact = 'Catastrophic';
                                break;
                        }
                    }
                    else
                    {
                        switch ($evaluation->avg_probability)
                        {
                            case 1:
                                $proba = 'Muy poco probable';
                                break;
                            case 2:
                                $proba = 'Poco probable';
                                break;
                            case 3:
                                $proba = 'Intermedio';
                                break;
                            case 4:
                                $proba = 'Probable';
                                break;
                            case 5:
                                $proba = 'Muy probable';
                                break;
                        }
                        switch ($evaluation->avg_impact)
                        {
                            case 1:
                                $impact = 'Muy poco impacto';
                                break;
                            case 2:
                                $impact = 'Poco impacto';
                                break;
                            case 3:
                                $impact = 'Intermedio';
                                break;
                            case 4:
                                $impact = 'Alto impacto';
                                break;
                            case 5:
                                $impact = 'Muy alto impacto';
                                break;
                        }
                    }
                    $avg_probability = $evaluation->avg_probability;
                    $avg_impact = $evaluation->avg_impact;
                    $impact_def = $impact;
                    $proba_def = $proba;
                    $score = $evaluation->avg_probability * $evaluation->avg_impact;
                }
                $results[$i] = [
                            'name' => $risk->name,
                             'id' => $risk->riskid,
                             'avg_probability' => $avg_probability,
                             'avg_impact' => $avg_impact,
                             'proba_def' => $proba_def,
                             'impact_def' => $impact_def,
                             'score' => $score
                            ];
                $i += 1;
            }
            usort($results, array($this,"cmp"));
            return json_encode($results);
        }   
    }
    //Función obtiene riesgos de proceso a través de JSON al crear plan de pruebas
    public function getRiesgosProcesos($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $i = 0; //contador de riesgos de proceso
                //obtenemos riesgos de proceso para la organización seleccionada
                $risk_subprocess = DB::table('risk_subprocess')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->join('organizations','organizations.id','=','organization_subprocess.organization_id')
                                    ->where('organizations.id','=',(int)$org)
                                    ->groupBy('risks.id')
                                    ->select('risks.name','risk_subprocess.id as riskid')
                                    ->get();
            foreach ($risk_subprocess as $risk)
            {
                //obtengo maxima fecha para obtener última evaluación
                $fecha = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.risk_subprocess_id','=',$risk->riskid)
                                ->max('updated_at');
                //obtenemos evaluación de riesgo de negocio (si es que hay)---> Ultima (mayor fecha updated_at)
                $evaluations = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.risk_subprocess_id','=',$risk->riskid)
                                ->where('evaluations.consolidation','=',1)
                                ->whereNotNull('evaluation_risk.avg_probability')
                                ->whereNotNull('evaluation_risk.avg_impact')
                                ->where('evaluations.updated_at','=',$fecha)
                                ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->get();
                //seteamos por si no hay evaluación
                $avg_probability = "Falta Eval.";
                $avg_impact = "Falta Eval.";
                $proba_def = "";
                $impact_def = "";
                foreach ($evaluations as $evaluation)
                {
                //seteamos nombres de probabilidad e impacto
                    if (Session::get('languaje') == 'en')
                    {
                        switch ($evaluation->avg_probability)
                        {
                            case 1:
                                $proba = 'Very improbable';
                                break;
                            case 2:
                                $proba = 'unlikely';
                                break;
                            case 3:
                                $proba = 'Possible';
                                break;
                            case 4:
                                $proba = 'Likely';
                                break;
                            case 5:
                                $proba = 'Very likely';
                                break;
                        }
                        switch ($evaluation->avg_impact)
                        {
                            case 1:
                                $impact = 'Despicable';
                                break;
                            case 2:
                                $impact = 'Less';
                                break;
                            case 3:
                                $impact = 'Moderate';
                                break;
                            case 4:
                                $impact = 'Severe';
                                break;
                            case 5:
                                $impact = 'Catastrophic';
                                break;
                        }
                    }
                    else
                    {
                        switch ($evaluation->avg_probability)
                        {
                            case 1:
                                $proba = 'Muy poco probable';
                                break;
                            case 2:
                                $proba = 'Poco probable';
                                break;
                            case 3:
                                $proba = 'Intermedio';
                                break;
                            case 4:
                                $proba = 'Probable';
                                break;
                            case 5:
                                $proba = 'Muy probable';
                                break;
                        }
                        switch ($evaluation->avg_impact)
                        {
                            case 1:
                                $impact = 'Muy poco impacto';
                                break;
                            case 2:
                                $impact = 'Poco impacto';
                                break;
                            case 3:
                                $impact = 'Intermedio';
                                break;
                            case 4:
                                $impact = 'Alto impacto';
                                break;
                            case 5:
                                $impact = 'Muy alto impacto';
                                break;
                        }
                    }
                    $avg_probability = $evaluation->avg_probability;
                    $avg_impact = $evaluation->avg_impact;
                    $impact_def = $impact;
                    $proba_def = $proba;
                    $score = $evaluation->avg_probability * $evaluation->avg_impact;
                }
                $results[$i] = [
                            'name' => $risk->name,
                             'id' => $risk->riskid,
                             'avg_probability' => $avg_probability,
                             'avg_impact' => $avg_impact,
                             'proba_def' => $proba_def,
                             'impact_def' => $impact_def,
                             'score' => $score,
                            ];
                $i += 1;
            }
            usort($results, array($this,"cmp"));
            return json_encode($results);
        }   
    }
    //Función que obtiene todos los stakeholders menos auditor responsable, al crear plan de auditoría
    public function getStakeholders($rut)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $i = 0; //contador de usuarios
            $users = DB::table('stakeholders')
                        ->where('id','<>',(int)$rut)
                        ->select('stakeholders.name','stakeholders.surnames','stakeholders.id')
                        ->get();
            foreach ($users as $user)
            {
                $results[$i] = [
                        'name' => $user->name.' '.$user->surnames,
                        'id' => $user->id
                ];
                $i += 1;
            }
            return json_encode($results);
        }
    }
    //función que obtiene un programa de auditoría (al crear uno nuevo basado en uno antiguo)
    public function getAuditProgram($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $tests = array();
            //Seleccionamos programa de auditoría y pruebas
            $audit_program = DB::table('audit_programs')
                        ->where('id','=',$id)
                        ->select('audit_programs.name','audit_programs.id','audit_programs.description')
                        ->get();
            foreach ($audit_program as $program)
            {
                $i = 0; //contador de pruebas
                //obtenemos pruebas
                $audit_tests = DB::table('audit_tests')
                                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                                ->where('audit_audit_plan_audit_program.audit_program_id','=',$program->id)
                                ->select('audit_tests.name','audit_tests.description','audit_tests.type','audit_tests.hh_plan',
                                        'audit_tests.control_id','audit_tests.subprocess_id','audit_tests.risk_id')
                                ->get();
                $results = [
                        'name' => $program->name,
                        'id' => $program->id,
                        'description' => $program->description,
                ];
            }
            return json_encode($results);
        }
    }
    /*función que obtiene los datos y las pruebas de un programa de auditoría (al revisar un plan de auditoría)
    a través del identificador de audit_audit_plan (auditoría + plan de auditoría) */
    public function getAuditProgram2($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $audit_programs = array();
            
            $j = 0; //contador de pruebas de auditoría
            //Seleccionamos programas de auditoria
            $audit_programs = DB::table('audit_audit_plan_audit_program')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$id)
                        ->select('audit_programs.name','audit_audit_plan_audit_program.expiration_date','audit_audit_plan_audit_program.id','audit_programs.description')
                        ->get();
            $k = 0; //contador de programas
            foreach ($audit_programs as $program)
            {
                $stakeholder = new stdClass();
                //obtenemos pruebas
                $audit_tests = DB::table('audit_tests')
                                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                                ->where('audit_audit_plan_audit_program.id','=',$program->id)
                                ->select('audit_tests.name','audit_tests.description','audit_tests.results','audit_tests.id','audit_tests.status','audit_tests.stakeholder_id','audit_tests.hh_real')
                                ->get();
                $audit_tests2 = array(); //seteamos en 0 variable de pruebas
                $i = 0; //contador de pruebas
                foreach ($audit_tests as $test)
                {
                    $test_result = $test->results;
                    if ($test->stakeholder_id == NULL)
                    {
                        $stake = NULL;
                    }
                    else
                    {
                        //Obtenemos stakeholder
                        $stakeholder = \Ermtool\Stakeholder::find($test->stakeholder_id);
                        $stake = $stakeholder->name.' '.$stakeholder->surnames;
                    }
                    //obtenemos issues
                    $issues = DB::table('issues')
                                    ->where('audit_test_id','=',$test->id)
                                    ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                                    ->get();
                    $debilidades = array();
                    $j = 0;
                    foreach ($issues as $issue)
                    {
                        $debilidades[$j] = [
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'description' => $issue->description,
                            'classification' => $issue->classification,
                            'recommendations' => $issue->recommendations
                        ];
                        $j += 1;
                    }
                    $audit_tests2[$i] = [
                            'name' => $test->name,
                            'description' => $test->description,
                            'result' => $test_result,
                            'id' => $test->id,
                            'status' => $test->status,
                            'results' => $test->results,
                            'hh_real' => $test->hh_real,
                            'stakeholder' => $stake,
                            'issues' => $debilidades
                            ];
                    $i += 1;
                }
                $audit_programs[$k] = [
                        'name' => $program->name,
                        'id' => $program->id,
                        'description' => $program->description,
                        'audit_tests' => $audit_tests2
                ];
                $k += 1;
            }
            return json_encode($audit_programs);
        }
    }
    //obtiene los controles relacionados a un plan de auditoría (según la organización que esté involucrada
    //con el plan de auditoría)
    public function getObjectiveControls($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $objective_risks = array();
            $i = 0; //contador de pruebas
            //obtendremos riesgos de negocio
            $objective_risk = DB::table('audit_plan_risk')
                        ->join('control_objective_risk','control_objective_risk.objective_risk_id','=','audit_plan_risk.objective_risk_id')
                        ->join('controls','control_objective_risk.control_id','=','controls.id')
                        ->where('audit_plan_risk.audit_plan_id','=',$id)
                        ->where('audit_plan_risk.objective_risk_id','<>','NULL')
                        ->select('controls.name','controls.id')
                        ->groupBy('controls.id')
                        ->get();
            foreach ($objective_risk as $obj)
            {
                $results[$i] = [
                        'name' => $obj->name,
                        'id' => $obj->id,
                ];
                $i += 1;
            }
            return json_encode($results);
        }
    }
    //obtiene los controles relacionados a un plan de auditoría (según la organización que esté involucrada
    //con el plan de auditoría)
    public function getSubprocessControls($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $objective_risks = array();
            $i = 0; //contador de pruebas
            //obtendremos controles de procesos
            $risk_subprocess = DB::table('audit_plan_risk')
                        ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','audit_plan_risk.risk_subprocess_id')
                        ->join('controls','control_risk_subprocess.control_id','=','controls.id')
                        ->where('audit_plan_risk.audit_plan_id','=',$id)
                        ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                        ->select('controls.name','controls.id')
                        ->groupBy('controls.id')
                        ->get();
            foreach ($risk_subprocess as $sub)
            {
                $results[$i] = [
                        'name' => $sub->name,
                        'id' => $sub->id,
                ];
                $i += 1;
            }
            return json_encode($results);
        }
    }
    //obtiene auditorías relacionadas a un plan de auditoría
    public function getAudits($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $objective_risks = array();
            $i = 0; //contador de auditorias
            //obtendremos auditorias
            $audits = DB::table('audit_audit_plan')
                        ->where('audit_audit_plan.audit_plan_id','=',$id)
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->select('audits.name AS name','audit_audit_plan.id AS id')
                        ->get();
            foreach ($audits as $audit)
            {
                $results[$i] = [
                        'name' => $audit->name,
                        'id' => $audit->id,
                ];
                $i += 1;
            }
            return json_encode($results);
        }
    }
    // ESTÁ MALA, CORREGIR SI ES QUE ES NECESARIO 
    // ACTUALIZACIÓN 24-08: LA UTILIZAREMOS PARA GENERAR EL GRÁFICO DE PRUEBAS DE AUDITORÍA
    //obtiene pruebas asociadas a una plan auditoria
    public function getTests($kind,$id)
    {
        $i = 0; //contador de pruebas
        $audit_plan = \Ermtool\Audit_plan::where('id',$id)->value('name');
        $pruebas_ejec = 0; //pruebas en ejecución
        $pruebas_abiertas = 0; //pruebas abiertas
        $pruebas_cerradas = 0; //pruebas cerradas
        $type = NULL; //identifica si es una prueba asociada a un riesgo, subproceso o control (1=Riesgo, 2=Subproceso, 3=Control)
        $audit_tests = array();

        $org = \Ermtool\Organization::getOrgByAuditPlan($id);
        $tests = \Ermtool\Audit_test::getTests($org->id,$id);

        foreach ($tests as $test)
        {
            //sumamos a prueba ejec abierta o cerrada según el estado que posea
            if ($test->status == 0)
            {
                $pruebas_abiertas += 1;
            }
            else if ($test->status == 1)
            {
                $pruebas_ejec += 1;
            }
            else if ($test->status == 2)
            {
                $pruebas_cerradas += 1;
            }
            //obtenemos nombre de stakeholder
            $resp = \Ermtool\Stakeholder::find($test->stakeholder_id);
            $resp = $resp['name'].' '.$resp['surnames'];
            
            if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficosdinamicos')) //si es excel debemos setear los datos aquí
            {
                if (Session::get('languaje') == 'en')
                {
                    if ($kind == 1 && $test->status == 0) //reporte de pruebas abiertas
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Design test';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Operationa effectiveness test';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Compliance test';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Sustantive tests';
                        }
                        else
                        {
                            $test_type = 'Not defined';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Ineffective';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Effective';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'In process';
                        }
                        if ($type == 1)
                        {
                            $related = 'Risk: '.$relacionado;
                        }
                        else if ($type == 2)
                        {
                            $related = 'Subprocess: '.$relacionado;
                        }
                        else if ($type == 3)
                        {
                            $related = 'Control: '.$relacionado;
                        }
                        $audit_tests[$i] = [
                            'Audit plan' => $audit_plan,
                            'Audit' => $test->audit_name,
                            'Program' => $test->audit_program_name,
                            'Test' => $test->name,
                            'Description' => $test->description,
                            'Kind' => $test_type,
                            'Results' => $results,
                            'Hours-man Planned' => $test->hh_plan,
                            'Hours-man Real' => $test->hh_real,
                            'Responsable' => $resp,
                            'Related object' => $related,
                        ];  
                    }
                    else if ($kind == 2 && $test->status == 1) //pruebas en ejecución
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Design test';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Operationa effectiveness test';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Compliance test';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Sustantive tests';
                        }
                        else
                        {
                            $test_type = 'Not defined';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Ineffective';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Effective';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'In process';
                        }
                        if ($type == 1)
                        {
                            $related = 'Risk: '.$relacionado;
                        }
                        else if ($type == 2)
                        {
                            $related = 'Subprocess: '.$relacionado;
                        }
                        else if ($type == 3)
                        {
                            $related = 'Control: '.$relacionado;
                        }
                        $audit_tests[$i] = [
                            'Audit plan' => $audit_plan,
                            'Audit' => $test->audit_name,
                            'Program' => $test->audit_program_name,
                            'Test' => $test->name,
                            'Description' => $test->description,
                            'Kind' => $test_type,
                            'Results' => $results,
                            'Hours-man Planned' => $test->hh_plan,
                            'Hours-man Real' => $test->hh_real,
                            'Responsable' => $resp,
                            'Related object' => $related,
                        ];  
                    }
                    else if ($kind == 3 && $test->status == 2) //pruebas cerradas
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Design test';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Operationa effectiveness test';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Compliance test';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Sustantive tests';
                        }
                        else
                        {
                            $test_type = 'Not defined';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Ineffective';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Effective';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'In process';
                        }

                        $audit_tests[$i] = [
                            'Audit plan' => $audit_plan,
                            'Audit' => $test->audit_name,
                            'Program' => $test->audit_program_name,
                            'Test' => $test->name,
                            'Description' => $test->description,
                            'Kind' => $test_type,
                            'Results' => $results,
                            'Hours-man Planned' => $test->hh_plan,
                            'Hours-man Real' => $test->hh_real,
                            'Responsable' => $resp,
                        ];    
                    }
                }
                else
                {
                    if ($kind == 1 && $test->status == 0) //reporte de pruebas abiertas
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Prueba de diseño';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Prueba de efectividad operativa';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Prueba de cumplimiento';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Prueba sustantiva';
                        }
                        else
                        {
                            $test_type = 'No definido';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Inefectiva';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Efectiva';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'En proceso';
                        }

                        $audit_tests[$i] = [
                            'Plan de auditoría' => $audit_plan,
                            'Auditoría' => $test->audit_name,
                            'Programa' => $test->audit_program_name,
                            'Prueba' => $test->name,
                            'Descripción' => $test->description,
                            'Tipo' => $test_type,
                            'Resultado' => $results,
                            'Horas-hombre planificadas' => $test->hh_plan,
                            'Horas-hombre reales' => $test->hh_real,
                            'Responsable' => $resp,
                        ];  
                    }
                    else if ($kind == 2 && $test->status == 1) //pruebas en ejecución
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Prueba de diseño';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Prueba de efectividad operativa';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Prueba de cumplimiento';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Prueba sustantiva';
                        }
                        else
                        {
                            $test_type = 'No definido';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Inefectiva';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Efectiva';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'En proceso';
                        }

                        $audit_tests[$i] = [
                            'Plan de auditoría' => $audit_plan,
                            'Auditoría' => $test->audit_name,
                            'Programa' => $test->audit_program_name,
                            'Prueba' => $test->name,
                            'Descripción' => $test->description,
                            'Tipo' => $test_type,
                            'Resultado' => $results,
                            'Horas-hombre planificadas' => $test->hh_plan,
                            'Horas-hombre reales' => $test->hh_real,
                            'Responsable' => $resp,
                        ]; 
                    }
                    else if ($kind == 3 && $test->status == 2) //pruebas cerradas
                    {
                        //tipo
                        if ($test->type == 0)
                        {
                            $test_type = 'Prueba de diseño';
                        }
                        else if ($test->type == 1)
                        {
                            $test_type = 'Prueba de efectividad operativa';
                        }
                        else if ($test->type == 2)
                        {
                            $test_type = 'Prueba de cumplimiento';
                        }
                        else if ($test->type == 3)
                        {
                            $test_type = 'Prueba sustantiva';
                        }
                        else
                        {
                            $test_type = 'No definido';
                        }
                        //resultado
                        if ($test->results == 0)
                        {
                            $results = 'Inefectiva';
                        }
                        else if ($test->results == 1)
                        {
                            $results = 'Efectiva';
                        }
                        else if ($test->results == 2)
                        {
                            $results = 'En proceso';
                        }

                        $audit_tests[$i] = [
                            'Plan de auditoría' => $audit_plan,
                            'Auditoría' => $test->audit_name,
                            'Programa' => $test->audit_program_name,
                            'Prueba' => $test->name,
                            'Descripción' => $test->description,
                            'Tipo' => $test_type,
                            'Resultado' => $results,
                            'Horas-hombre planificadas' => $test->hh_plan,
                            'Horas-hombre reales' => $test->hh_real,
                            'Responsable' => $resp,
                        ];  
                    }
                }
            }
            else
            {
                $audit_tests[$i] = [
                    'audit_name' => $test->audit_name,
                    'audit_program_name' => $test->audit_program_name,
                    'name' => $test->name,
                    'description' => $test->description,
                    'type' => $test->type,
                    'status' => $test->status,
                    'results' => $test->results,
                    'hh_plan' => $test->hh_plan,
                    'hh_real' => $test->hh_real,
                    'stakeholder' => $resp,
                ];
            }
            
            $i += 1;
        }
        if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos')) //si es excel debemos solo enviamos audit_tests
        {
            return $audit_tests;
        }
        else
        {
            return json_encode(['audit_plan' => $audit_plan, 'audit_tests' => $audit_tests,'pruebas_abiertas' => $pruebas_abiertas, 'pruebas_ejec' => $pruebas_ejec, 'pruebas_cerradas' => $pruebas_cerradas]);
        }
        
    }
    //obtiene datos asociados al último plan de auditoría generado para una organización
    public function getAuditPlan($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $auditorias = array();
            $sub_plan_risks = array();
            $obj_plan_risks = array();
            $audit_programs = array();
            $i = 0; //contador de pruebas
            //primero obtenemos último id de plan de auditoría generado para una organización
            $audit_plan_id = DB::table('audit_plans')
                                    ->where('organization_id','=',$org)
                                    ->max('id');
            //verificamos que existe plan
            if ($audit_plan_id)
            {
                //primero obtenemos info del plan
                $audit_plan_info = \Ermtool\Audit_plan::find($audit_plan_id);
                //obtenemos stakeholders
                $stakeholders = DB::table('audit_plan_stakeholder')
                                    ->join('audit_plans','audit_plans.id','=','audit_plan_stakeholder.audit_plan_id')
                                    ->join('stakeholders','stakeholders.id','=','audit_plan_stakeholder.stakeholder_id')
                                    ->where('audit_plan_stakeholder.audit_plan_id','=',$audit_plan_id)
                                    ->select('stakeholders.name','stakeholders.surnames','audit_plan_stakeholder.role')
                                    ->get();
                $responsable = NULL;
                $users = NULL;
                $j = 0; //contador de stakeholders
                foreach ($stakeholders as $stakes)
                {
                    if ($stakes->role == 0)
                    {
                        $responsable = $stakes->name.' '.$stakes->surnames;
                    }
                    else
                    {
                        $users[$j] = $stakes->name.' '.$stakes->surnames;
                        $j += 1;    
                    }
                    
                }
                 //obtenemos riesgos de negocio del plan
                $objective_risks = DB::table('audit_plan_risk')
                                    ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$audit_plan_id)
                                    ->select('objective_risk.id as id','risks.name as risk_name','objectives.name as objective_name')
                                    ->get();
                //obtenemos riesgos de negocio del plan
                $subprocess_risks = DB::table('audit_plan_risk')
                                    ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->join('processes','processes.id','=','subprocesses.process_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$audit_plan_id)
                                    ->select('risk_subprocess.id as id','risks.name as risk_name','subprocesses.name as subprocess_name',
                                            'processes.name as process_name')
                                    ->get();
                $j = 0; //contador de riesgos en cero
                //seteamos en variables los riesgos de negocio
                foreach ($objective_risks as $objective_risk)
                {
                    $obj_plan_risks[$j] = ['name' => $objective_risk->risk_name,
                                           'objective_name' => $objective_risk->objective_name,
                                           ];
                    $j += 1;
                }
                $j = 0; //contador de riesgos en cero
                //seteamos en variables los riesgos de proceso
                foreach ($subprocess_risks as $subprocess_risk)
                {
                    $sub_plan_risks[$j] = ['name' => $subprocess_risk->risk_name,
                                           'subprocess_name' => $subprocess_risk->subprocess_name,
                                           'process_name' => $subprocess_risk->process_name];
                    $j += 1;
                }
                
                //ahora obtenemos los datos requeridos de auditoría para el plan
                $audits = DB::table('audit_audit_plan')
                                    ->join('audits','audits.id','=','audit_audit_plan.id')
                                    ->where('audit_audit_plan.audit_plan_id','=',$audit_plan_id)
                                    ->select('audit_audit_plan.id as id','audits.name as name',
                                            'audits.description as description','audit_audit_plan.initial_date as audit_initial_date',
                                            'audit_audit_plan.final_date as audit_final_date','audit_audit_plan.resources as resources')
                                    ->get();
                foreach ($audits as $audit)
                {
                    //formateamos datos
                    if ($audit->resources == NULL)
                    {
                        $resources = NULL;
                    }
                    else
                        $resources = $audit->resources;
                    if ($audit->audit_initial_date == NULL)
                    {
                        $initial_date = NULL;
                    }
                    else
                    {
                        //ordenamos fechas
                        $initial_date_tmp = new DateTime($audit->audit_initial_date);
                        $initial_date = date_format($initial_date_tmp, 'd-m-Y');
                    }
                     if ($audit->audit_final_date == NULL)
                    {
                        $final_date = NULL;
                    }
                    else
                    {
                        $final_date_tmp = new DateTime($audit->audit_final_date);
                        $final_date = date_format($final_date_tmp, 'd-m-Y');
                    }
                    if ($audit->description == NULL)
                    {
                        $description = NULL;
                    }
                    else
                        $description = $audit->description;
                    $obj_risks = array();
                    $sub_risks = array();
                    //obtenemos riesgos de negocio asociados a la auditoría
                    $objective_risks = DB::table('audit_risk')
                                    ->join('objective_risk','objective_risk.id','=','audit_risk.objective_risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->where('audit_risk.audit_audit_plan_id','=',$audit->id)
                                    ->select('risks.name as risk_name','objectives.name as objective_name')
                                    ->get();
                    //obtenemos riesgos de negocio asociados a la auditoría
                    $subprocess_risks = DB::table('audit_risk')
                                    ->join('risk_subprocess','risk_subprocess.id','=','audit_risk.risk_subprocess_id')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->join('processes','processes.id','=','subprocesses.process_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->where('audit_risk.audit_audit_plan_id','=',$audit->id)
                                    ->select('risks.name as risk_name','subprocesses.name as subprocess_name',
                                            'processes.name as process_name')
                                    ->get();
                    //obtenemos programas de auditoría realizadas en cada auditoría (si es que hay)
                    $audit_programs1 = DB::table('audit_audit_plan_audit_program')
                                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                                ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                                ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$audit->id)
                                ->select('audit_programs.name as audit_program_name')
                                ->get();
                    if ($audit_programs1)
                    {
                        $j = 0; //contador de programas en cero
                        foreach ($audit_programs1 as $program)
                        {
                            $audit_programs[$j] = ['audit_id' => $audit->id,
                                                'name' => $program->audit_program_name
                                            ];
                            $j += 1;
                        }
                    }
                    $j = 0; //contador de riesgos en cero
                    //seteamos en variables los riesgos de negocio (si es que hay)
                    foreach ($objective_risks as $objective_risk)
                    {
                        $obj_risks[$j] = ['audit_id' => $audit->id,
                                          'name' => $objective_risk->risk_name,
                                          'objective_name' => $objective_risk->objective_name];
                        $j += 1;
                    }
                    $j = 0; //contador de riesgos en cero
                    //seteamos en variables los riesgos de proceso
                    foreach ($subprocess_risks as $subprocess_risk)
                    {
                        $sub_risks[$j] = ['audit_id' => $audit->id,
                                          'name' => $subprocess_risk->risk_name,
                                          'subprocess_name' => $subprocess_risk->subprocess_name,
                                          'process_name' => $subprocess_risk->process_name];
                        $j += 1;
                    }
                    //guardamos datos de pruebas de auditoría de la auditoría seleccionada
                    $auditorias[$i] = ['name' => $audit->name,
                                        'description' => $description,
                                        'initial_date' => $initial_date,
                                        'final_date' => $final_date,
                                        'resources' => $resources,
                                        'obj_risks' => $obj_risks,
                                        'sub_risks' => $sub_risks,
                                        'audit_programs' => $audit_programs,
                                    ];
                    $i += 1;
                }
                //ordenamos fechas
                $initial_date_tmp = new DateTime($audit_plan_info['initial_date']);
                $initial_date = date_format($initial_date_tmp, 'd-m-Y');
                $final_date_tmp = new DateTime($audit_plan_info['final_date']);
                $final_date = date_format($final_date_tmp, 'd-m-Y');
                //guardamos datos finales que serán enviados
                $results = ['name' => $audit_plan_info['name'],
                            'description' => $audit_plan_info['description'],
                            'objectives' => $audit_plan_info['objectives'],
                            'scopes' => $audit_plan_info['scopes'],
                            'status' => $audit_plan_info['status'],
                            'resources' => $audit_plan_info['resources'],
                            'methodology' => $audit_plan_info['methodology'],
                            'initial_date' => $initial_date,
                            'final_date' => $final_date,
                            'rules' => $audit_plan_info['rules'],
                            'users' => $users,
                            'responsable' => $responsable,
                            'obj_plan_risks' => $obj_plan_risks,
                            'sub_plan_risks' => $sub_plan_risks,
                            'audits' => $auditorias
                    ];
                return json_encode($results);
            } //if ($audit_plan_id)
        }
    }
    //obtiene todos los planes asociados a una organización
    public function getPlanes($org)
    {
        return json_encode(\Ermtool\Organization::find($org)->audit_plans);
    }
    //obtiene notas asociadas a una prueba de auditoría
    public function getNotes($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $results = array();
            $i = 0;
            $notes = DB::table('notes')
                        ->where('audit_test_id','=',$id)
                        ->select('notes.id','notes.name','notes.description','notes.created_at','notes.status',
                                 'notes.audit_test_id as test_id')
                        ->get();
            if (empty($notes))
            {
                $results = NULL;
            }
            else
            {
                foreach ($notes as $note)
                {
                    //obtenemos respuestas a la nota (si es que existen)
                    $answers_notes = DB::table('notes_answers')
                                ->where('note_id',$note->id)
                                ->select('notes_answers.id','notes_answers.answer','notes_answers.created_at','notes_answers.updated_at')
                                ->get();
                    if (empty($answers_notes))
                    {
                        $answers = NULL;
                    }
                    else
                    {
                        $j = 0; //contador de respuestas para las notas
                        //seteamos cada respuesta de la nota
                        foreach ($answers_notes as $ans)
                        {
                            //obtenemos evidencias de la respuesta (si es que existen)
                            $evidences = getEvidences(1,$ans->id);
                            $answers[$j] = [
                                    'id' => $ans->id,
                                    'answer' => $ans->answer,
                                    'created_at' => $ans->created_at,
                                    'updated_at' => $ans->updated_at,
                                    'ans_evidences' => $evidences,
                            ];
                            $j += 1;
                        }
                        
                    }
                    //obtenemos evidencias de la nota (si es que existe)
                    $evidences = getEvidences(0,$note->id);
                    $fecha_creacion = date('d-m-Y',strtotime($note->created_at));
                    $fecha_creacion .= ' a las '.date('H:i:s',strtotime($note->created_at));
                    $results[$i] = [
                        'id' => $note->id,
                        'name' => $note->name,
                        'description' => $note->description,
                        'created_at' => $fecha_creacion,
                        'status' => $note->status,
                        'status_origin' => $note->status,
                        'test_id' => $note->test_id,
                        'answers' => $answers,
                        'evidences' => $evidences
                        ];
                    $i += 1;
                }
            }
            return json_encode($results);
        }
    }
    public function closeNote($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $res = 0;
            DB::table('notes')
                ->where('id','=',$id)
                ->update([
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            $res = 1;
            return $res;
        }
    }
    //obtenemos id de organización perteneciente a un plan de auditoría
    public function getOrganization($audit_plan)
    {
        $organization = NULL;
        $organization = DB::table('organizations')
                    ->join('audit_plans','audit_plans.organization_id','=','organizations.id')
                    ->where('audit_plans.id','=',$audit_plan)
                    ->select('organizations.id')
                    ->first();
        return json_encode($organization);
    }

    public function indexGraficos()
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
                return view('en.reportes.auditorias_graficos',['organizations' => $organizations]);
            }
            else
            {
                return view('reportes.auditorias_graficos',['organizations' => $organizations]);
            }
        }
    }

    public function indexGraficos2($value,$org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //ACTUALIZACIÓN 24-08: Nuevo gráfico de pruebas de auditorías, por lo tanto debemos seleccionar el plan que se desea ver
            if ($org == 0)
            {
                $planes_auditoria = \Ermtool\Audit_plan::where('status',0)->where('organization_id',$_GET['organization_id'])->lists('name','id');
            }
            else
            {
                $planes_auditoria = \Ermtool\Audit_plan::where('status',0)->where('organization_id',$org)->lists('name','id');
            }
            
            $planes_ejec = 0; //planes en ejecución
            $planes_abiertos = 0; //planes con al menos una prueba abierta
            $planes_cerrados = 0; //plan sin pruebas abiertas ni en ejecución, pero si cerradas 
            $audit_plans = array();
            //obtenemos todas las auditorías y pruebas de auditoría con su estado de ejecución
            if ($org == 0)
            {
                $planes = \Ermtool\Audit_plan::where('status','=',0)
                                            ->where('organization_id','=',$_GET['organization_id'])
                                            ->get(['id','name','status','description']);
            }
            else
            {
                $planes = \Ermtool\Audit_plan::where('status','=',0)
                                            ->where('organization_id','=',$org)
                                            ->get(['id','name','status','description']);
            }
            $i = 0; //contador de planes
            foreach ($planes as $audit_plan)
            {
                $audits = array();
                $audit_programs = array();
                $audit_tests = array();
                //obtenemos auditorías
                $auditorias = DB::table('audit_audit_plan')
                            ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                            ->where('audit_audit_plan.audit_plan_id','=',$audit_plan->id)
                            ->select('audit_audit_plan.id','audits.name')
                            ->get();
                $j = 0; //contador de auditorias por plan
                foreach ($auditorias as $audit)
                {
                    $ejecucion = 0;
                    $abiertas = 0;
                    $cerradas = 0;
                    $audits[$j] = $audit->name;
                    $j += 1;
                    //obtenemos programas
                    $programs = DB::table('audit_audit_plan_audit_program')
                                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                                    ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$audit->id)
                                    ->select('audit_audit_plan_audit_program.id','audit_programs.name')
                                    ->get();
                    $k = 0; //contador de programas
                    foreach ($programs as $program)
                    {
                        $audit_programs[$k] = $program->name;
                        $k += 1;
                        //obtenemos pruebas
                        $tests = DB::table('audit_tests')
                                    ->where('audit_audit_plan_audit_program_id','=',$program->id)
                                    ->select('name','status')
                                    ->get();
                        //vemos si hay alguna prueba en ejecución, si es así el plan estará en ejecución
                        $l = 0; //contador de pruebas
                        //estados de las pruebas
                        
                        foreach ($tests as $test)
                        {
                            $audit_tests[$l] = $test->name;
                            $l += 1;
                            if ($test->status == 0)
                            {
                                $abiertas += 1;
                            }
                            else if ($test->status == 1)
                            {
                                $ejecucion += 1;
                            }
                            else if ($test->status == 2)
                            {
                                $cerradas += 1;
                            }
                        }
                    }
                }
                $audit_plans[$i] = [
                    'name' => $audit_plan->name,
                    'description' => $audit_plan->description,
                    'audits' => $audits,
                    'status' => $audit_plan->status,
                    'programs' => $audit_programs,
                    'tests' => $audit_tests,
                    'ejecucion' => $ejecucion,
                    'abiertas' => $abiertas,
                    'cerradas' => $cerradas,
                ];
                $i += 1;
                if ($audit_plan->status == 1) //AGREGADO 01-08-2016 SI EL STATUS DEL PLAN ES 1 SE CALIFICA OBVIAMENTE COMO CERRADO)
                {
                    $planes_cerrados += 1;
                }
                //vemos si el plan está en ejecución o abierto
                else if ($ejecucion > 0) //tiene al menos una prueba en ejecución
                {
                    $planes_ejec += 1;
                }
                else if ($abiertas > 0) //no tiene pruebas en ejecución y tiene al menos una prueba abierta => plan abierto
                {
                    $planes_abiertos += 1;
                }
                else if ($cerradas > 0) //no tiene ni pruebas abiertas ni en ejecución pero si cerradas => plan cerrado
                {
                    $planes_cerrados += 1;
                } 
            }
            if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos'))
            {
                $i = 0;
                foreach ($audit_plans as $plan)
                {
                    //damos formato a auditorías, programas y pruebas
                    $audits = "";
                    $last = end($plan['audits']); //guardamos final para no agregarle coma
                    foreach ($plan['audits'] as $audit)
                    {
                            if ($audit != $last)
                            {
                                $audits .= $audit.', ';
                            }
                            else
                                $audits .= $audit;
                    }
                    $programs = "";
                    $last = end($plan['programs']); //guardamos final para no agregarle coma
                    foreach ($plan['programs'] as $program)
                    {
                            if ($program != $last)
                            {
                                $programs .= $program.', ';
                            }
                            else
                                $programs .= $program;
                    }
                    $tests = "";
                    $last = end($plan['tests']); //guardamos final para no agregarle coma
                    foreach ($plan['tests'] as $test)
                    {
                            if ($test != $last)
                            {
                                $tests .= $test.', ';
                            }
                            else
                                $tests .= $test;
                    }
                    if ($value == 5)
                    {
                        if ($plan['abiertas'] > 0 && $plan['ejecucion'] == 0 && $plan['status'] == 0)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res[$i] = [
                                    'Name' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Audits' => $audits,
                                    'Programs' => $programs,
                                    'Tests' => $tests
                                ];
                            }
                            else
                            {
                                $res[$i] = [
                                    'Nombre' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Auditorías' => $audits,
                                    'Programas' => $programs,
                                    'Pruebas' => $tests
                                ];
                            }
                            $i += 1;
                        }
                    }
                    else if ($value == 6)
                    {
                        if ($plan['ejecucion'] > 0 && $plan['status'] == 0)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res[$i] = [
                                    'Name' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Audits' => $audits,
                                    'Programs' => $programs,
                                    'Tests' => $tests
                                ];
                            }
                            else
                            {
                                $res[$i] = [
                                    'Nombre' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Auditorías' => $audits,
                                    'Programas' => $programs,
                                    'Pruebas' => $tests
                                ];
                            }
                            $i += 1;
                        }    
                    }
                    else if ($value == 7)
                    {
                        if ($plan['status'] == 1)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res[$i] = [
                                    'Name' => $plan['name'],
                                    'Description' => $plan['description'],
                                    'Audits' => $audits,
                                    'Programs' => $programs,
                                    'Tests' => $tests
                                ];
                            }
                            else
                            {
                                $res[$i] = [
                                    'Nombre' => $plan['name'],
                                    'Descripción' => $plan['description'],
                                    'Auditorías' => $audits,
                                    'Programas' => $programs,
                                    'Pruebas' => $tests
                                ];
                            }
                            $i += 1;
                        }       
                    }
                }
                return $res;
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.auditorias_graficos',['audit_plans'=>$audit_plans,'planes_ejec'=>$planes_ejec,'planes_abiertos'=>$planes_abiertos,'planes_cerrados'=>$planes_cerrados,'planes_auditoria' => $planes_auditoria,'org' => $_GET['organization_id']]);
                }
                else
                {
                    return view('reportes.auditorias_graficos',['audit_plans'=>$audit_plans,'planes_ejec'=>$planes_ejec,'planes_abiertos'=>$planes_abiertos,'planes_cerrados'=>$planes_cerrados,'planes_auditoria' => $planes_auditoria,'org' => $_GET['organization_id']]);
                }
            }
        }    
    }
    //obtiene datos de una auditoría
    public function getAudit($id)
    {
        $audit = DB::table('audits')
                ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                ->where('audit_audit_plan.id','=',$id)
                ->select('audits.id','audits.name','audits.description','audit_audit_plan.resources','audit_audit_plan.initial_date','audit_audit_plan.final_date')
                ->first();
        $initial_date = new DateTime($audit->initial_date);
        $audit->initial_date = date_format($initial_date, 'd-m-Y');
        $final_date = new DateTime($audit->final_date);
        $audit->final_date = date_format($final_date, 'd-m-Y');
        //obtenemos programas
        $audit_programs = DB::table('audit_programs')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
                    ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$id)
                    ->select('audit_programs.name','audit_audit_plan_audit_program.id')
                    ->get();
        $i = 0;
        $programs = array();
        foreach ($audit_programs as $program)
        {
            $programs[$i] = ['id' => $program->id, 'name' => $program->name];
            $i += 1;
        }
        if (empty($programs))
        {
            $programs = NULL;
        }
        $audits = ['audit' => $audit, 'programs' => $programs];
        return json_encode($audits);
    }
    //función para cerrar plan de auditoría
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
            DB::transaction(function() 
            {
                $audit_plan = \Ermtool\Audit_plan::find($GLOBALS['id1']);
                $audit_plan->status = 1;
                $audit_plan->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit Plan successfully closed');
                }
                else
                {
                    Session::flash('message','Plan de auditoría cerrado correctamente');
                }
            });
            return Redirect::to('plan_auditoria');
        }
    }
    public function open($id)
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
                $audit_plan = \Ermtool\Audit_plan::find($GLOBALS['id1']);
                $audit_plan->status = 0;
                $audit_plan->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Audit Plan successfully opened');
                }
                else
                {
                    Session::flash('message','Plan de auditoría ha sido re abierto');
                }
            });
            return Redirect::to('plan_auditoria');
        }
    }
}