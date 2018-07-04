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

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class AuditoriasController extends Controller
{    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger;
    public $logger2;
    public $logger3;
    public $logger4;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('auditorias');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/auditorias.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());

        $this->logger2 = new Logger('programas_auditoria');
        $this->logger2->pushHandler(new StreamHandler($dir.'/storage/logs/programas_auditoria.log', Logger::INFO));
        $this->logger2->pushHandler(new FirePHPHandler());

        $this->logger3 = new Logger('pruebas_auditoria');
        $this->logger3->pushHandler(new StreamHandler($dir.'/storage/logs/pruebas_auditoria.log', Logger::INFO));
        $this->logger3->pushHandler(new FirePHPHandler());

        $this->logger4 = new Logger('notas');
        $this->logger4->pushHandler(new StreamHandler($dir.'/storage/logs/notas.log', Logger::INFO));
        $this->logger4->pushHandler(new FirePHPHandler());
    }

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
                $planes = array();
                $plans = \Ermtool\Audit_plan::orderBy('created_at','DESC')->get();
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
                        $lala = new DateTime($plan['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                        //$fecha_creacion = date_format($plan['created_at'],"d-m-Y");
                    }
                    //damos formato a fecha de actualización 
                    if ($plan['updated_at'] != NULL)
                    {
                        $lala = new DateTime($plan['udpdated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
                        //$fecha_act = date_format($plan['updated_at'],"d-m-Y");
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    
    public function indexAuditorias()
    {
        try 
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
                        $lala = new DateTime($audit['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                        //$fecha_creacion = date_format($audit['created_at'],"d-m-Y");
                    }
                    //damos formato a fecha de actualización 
                    if ($audit['updated_at'] != NULL)
                    {
                        $lala = new DateTime($audit['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
                        //$fecha_act = date_format($audit['updated_at'],"d-m-Y");
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //Creación de plan de auditoría
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //Función con la que primero se seleccionará: Organización
    public function auditPrograms1()
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
                    return view('en.auditorias.programas',['organizations' => $organizations]);
                }
                else
                {
                    return view('auditorias.programas',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función para abrir la vista de gestión de programas de auditoría
    public function auditPrograms()
    {
        try
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
                        $lala = new DateTime($program->created_at);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                        //$fecha_creacion = date('d-m-Y',strtotime($program->created_at));
                    }
                    //damos formato a fecha de actualización 
                    if ($program->updated_at != NULL)
                    {
                        //damos formato a fecha final
                        $lala = new DateTime($program->updated_at);
                        $fecha_act = date_format($lala,"d-m-Y");
                        //$fecha_act = date('d-m-Y',strtotime($program->updated_at));
                    }
                    else
                        $fecha_act = NULL;
                    //formato a fecha expiración
                    if ($program->expiration_date)
                    {
                        $lala = new DateTime($program->expiration_date);
                        $fecha_exp = date_format($lala,"d-m-Y");
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función crea PROGRAMAS de auditoría
    public function createPruebas($audit_id)
    {
        try
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
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function createAuditoria()
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función que guarda programa de auditoría
    public function storePrueba(Request $request)
    {
        try
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
                    $logger = $this->logger2;
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el programa con Id: '.$audit_program_id.' llamado: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/programas_auditoria');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function storeAuditoria(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger;
                $audit = \Ermtool\Audit::create([
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

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la auditoría con Id: '.$audit->id.' llamada: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/auditorias');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function ejecutar()
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
                    return view('en.auditorias.ejecutar',['organizations' => $organizations]);
                }
                else
                {
                    return view('auditorias.ejecutar',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function storeEjecution(Request $request)
    {
        try
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
                    $logger = $this->logger3;
                    $c = new ControlesController;
                    //primero que todo, actualizamos las pruebas
                    //para esto, separamos primer string del array id_pruebas por sus comas
                    $id_pruebas = explode(',',$_POST['id_pruebas'][0]);
                    foreach ($id_pruebas as $id)
                    {
                        //actualizamos resultados (ACTUALIZACIÓN 28-10-2016) Solo actualizamos resultados ya que el issue no se tocará en esta sección) (si es que el estado de la prueba es cerrado (igual a 2))
                        if ($_POST['status_'.$id] == 2)
                        {
                            if (isset($_POST['comments_'.$id]) && $_POST['comments_'.$id] != '')
                            {
                                $comments = $_POST['comments_'.$id];
                            }
                            else
                            {
                                $comments = NULL;
                            }
                            //actualizamos resultado de prueba de identificador $id (status y results)
                            DB::table('audit_tests')
                                ->where('id','=',$id)
                                ->update([ 
                                    'status' => $_POST['status_'.$id],
                                    'results' => $_POST['test_result_'.$id],
                                    'updated_at' => date('Ymd H:i:s'),
                                    'hh_real' => $_POST['hh_real_'.$id],
                                    'comments' => $comments
                                ]);

                            //obtenemos id de control para la evaluación de riesgo controlado
                            //ACTUALIZACIÓN 27-01: Obtenemos id de controles asociados a la prueba
                            $controls = DB::table('audit_tests')
                                        ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                                        ->where('audit_tests.id','=',$id)
                                        ->select('audit_test_control.control_id')
                                        ->get();

                            if (isset($_POST['test_result_'.$id]) && isset($controls) && $controls != NULL && !empty($controls))
                            {
                                foreach ($controls as $control)
                                {
                                    /*Por ahora no se usa en Coca Cola */
                                    //ACT 30-03-18: Volvemos a incorporarla
                                    if ($control->control_id != NULL)
                                    {
                                        $result = $c->calcControlValue($control->control_id,$_POST['org_id']);

                                        $eval = $c->calcControlledRisk($control->control_id,$_POST['org_id']);

                                        $result = 0;
                                    }
                                    else
                                    {
                                        $result = 2;
                                    }
                                    
                                    $result = 1;
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

                        $test = DB::table('audit_tests')
                                ->where('id','=',$id)
                                ->select('name')
                                ->first();

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha ejecutado la prueba de auditoría con Id: '.$id.' llamado: '.$test->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                        //guardamos archivos de evidencias (si es que hay)
                        if($GLOBALS['req']->file('evidence_doc_'.$id) != NULL)
                        {
                            foreach ($GLOBALS['req']->file('evidence_doc_'.$id) as $evidencedoc)
                            {
                                if ($evidencedoc != NULL)
                                {
                                    upload_file($evidencedoc,'ejecucion_auditoria',$id);
                                }
                            }                    
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

                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                
                return Redirect::to('/ejecutar_pruebas')->with(['organizations' => $organizations,'org_id' => $_POST['org_id'],'audit_plan_id' => $_POST['audit_plans'],'audit_id' => $_POST['audit_id']]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function supervisar()
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
                    return view('en.auditorias.supervisar',['organizations' => $organizations]);
                }
                else
                {
                    return view('auditorias.supervisar',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function storeSupervision(Request $request)
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
                $evidence = $request->file('evidencia_'.$request['test_id']);
                //primero vemos si se está agregando una nota o una evidencia
                DB::transaction(function() {
                    $logger = $this->logger4;

                    if (isset($_POST['stakeholder_id']))
                    {
                        $stake = $_POST['stakeholder_id'];
                    }
                    else
                    {
                        $stake = NULL;
                    }
                    $res = DB::table('notes')
                            ->insertGetId([
                                'name' => $_POST['name_'.$_POST['test_id']],
                                'description' => $_POST['description_'.$_POST['test_id']],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'audit_test_id' => $_POST['test_id'],
                                'status' => 0,
                                'user_id' => Auth::user()->id,
                                'stakeholder_id' => $stake
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

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha agregado la nota con Id: '.$res.' llamada: '.$_POST['name_'.$_POST['test_id']].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //Antes de almacenar un plan de auditoría, se deben ingresar los datos necesarios
    //para crear un auditoría perteneciente a dicho plan (audit_audit_plan)
    //En esta función se envían los datos del plan para poder generarlo despues en conjunto a su auditoría
    public function datosAuditoria(Request $request)
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
                //Mantenemos atomicidad y consistencia
                DB::transaction(function()
                {
                    $logger = $this->logger;
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
                            
                        }
                    } //fin isset($_POST['audits'])
                    //ahora guardamos auditorías nuevas (si es que hay)
                    $i = 1; //contador para auditorías nuevas
                    if(isset($_POST['audit_new'.$i.'_name']))
                    {   
                        while (isset($_POST['audit_new'.$i.'_name']))
                        {
                            //ACT 29-06-18: Verificamos que se ingrese nombre de auditoría
                            if ($_POST['audit_new'.$i.'_name'] != '')
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
                            }

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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el plan de auditoría con Id: '.$audit_plan_id.' llamado: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/plan_auditoria'); 
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $auditorias = array();
                $plan_auditoria = array();
                $organizacion = NULL;

                $j = 0; //contador de auditorías

                //obtenemos plan de auditoría
                $audit_plan = \Ermtool\Audit_plan::where('id',(int)$id)->get();
                foreach ($audit_plan as $plan)
                {
                    //damos formato a estado
                    $estado = $plan['status'];
                    //damos formato a fecha inicial
                    $fecha_inicial = new DateTime($plan['initial_date']);
                    $fecha_inicial = date_format($fecha_inicial, 'd-m-Y');
                    //$fecha_inicial = date("d-m-Y",strtotime($plan['initial_date']));
                    //damos formato a fecha final
                    $fecha_final = new DateTime($plan['final_date']);
                    $fecha_final = date_format($fecha_final, 'd-m-Y');
                    //$fecha_final = date("d-m-Y",strtotime($plan['final_date']));
                    //obtenemos organizacion
                    $organizacion = \Ermtool\Organization::name($plan['organization_id']);
                    
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
                                            'audit_audit_plan_id' => $audit->id,
                                            'name' => $audit1['name'],
                                            'description' => $audit1['description']
                                            ];
                            $j += 1;
                        }
                    }

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
                                                    'organizacion' => $organizacion]);
                    }
                    else
                    {
                        return view('auditorias.show',['plan_auditoria' => $plan_auditoria,
                                                    'auditorias' => $auditorias,
                                                    'organizacion' => $organizacion]);
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
    public function showProgram($id)
    {
        try
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
                    $lala = new DateTime($audit_program->expiration_date);
                    $expiration_date = date_format($lala,"d-m-Y");
                }

                //obtenemos pruebas de auditoría del programa
                $audit_tests = DB::table('audit_tests')
                                ->where('audit_audit_plan_audit_program_id','=',$id)
                                ->select('id','name','description','evaluation_test_id','status','results','created_at','updated_at','hh_plan','hh_real','stakeholder_id')
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
                    $lala = new DateTime($audit_test->created_at);
                    $fecha_creacion = date_format($lala,"d-m-Y");
                    //damos formato a fecha de actualización
                    $lala = new DateTime($audit_test->updated_at);
                    $fecha_act = date_format($lala,"d-m-Y");
                    //ACT 30-03-18: Tipo desde evaluation_tests
                    $type = \Ermtool\Evaluation_test::name($audit_test->evaluation_test_id);
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function editProgram($id)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function editTest($test_id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $audit_test = \Ermtool\Audit_test::find($test_id);
                //obtenemos audit_plan para despues obtener controles, riesgos o subproceso de la prueba
                $audit_plan = DB::table('audit_audit_plan_audit_program')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                            ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                            ->where('audit_tests.audit_audit_plan_audit_program_id','=',$audit_test->audit_audit_plan_audit_program_id)
                            ->select('audit_plans.id')
                            ->first();
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                //seleccionamos tipo o categoría
                //ACT 30-03-18: Una prueba puede tener más de un control asociado
                $audit_test_control = \Ermtool\Audit_test::getControls($test_id);

                if ($audit_test->process_id != NULL) //es prueba de proceso
                {
                    //Vemos si se especificaron subprocessos
                   $audit_test_subprocess = \Ermtool\Audit_test::getSubprocesses($test_id); 
                }
                
                /*
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
                }*/

                //obtenemos evidencias de prueba (si es que existen)
                //$evidence = getEvidences(5,$audit_test->id);
                if (Session::get('languaje') == 'en')
                {
                    //ACT 30-03-18: Agregamos tipos de prueba dinámicos
                    $evaluation_tests = \Ermtool\Evaluation_test::lists('name_eng','id');
                    return view('en.auditorias.edit_test',['audit_test'=>$audit_test,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'evaluation_tests' => $evaluation_tests,'audit_test_control' => $audit_test_control]);
                }
                else
                {
                    //ACT 30-03-18: Agregamos tipos de prueba dinámicos
                    $evaluation_tests = \Ermtool\Evaluation_test::lists('name','id');
                    return view('auditorias.edit_test',['audit_test'=>$audit_test,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'evaluation_tests' => $evaluation_tests,'audit_test_control' => $audit_test_control]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function updateProgram(Request $request, $id)
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
                global $req;
                $req = $request;
                //echo "POST: ";
                //print_r($_POST);


                DB::transaction(function (){

                    $logger = $this->logger2;

                    $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')->find($GLOBALS['id1']);
                    $audit_program = \Ermtool\Audit_program::find($audit_audit_plan_audit_program->audit_program_id);
                    $audit_program->name = $_POST['name'];
                    $audit_program->description = $_POST['description'];

                    if ($_POST['expiration_date'] == NULL || $_POST['expiration_date'] == "")
                        $exp_date = NULL;
                    else
                        $exp_date = $_POST['expiration_date'];

                    DB::table('audit_audit_plan_audit_program')
                        ->where('id',$audit_audit_plan_audit_program->id)
                        ->update([
                                'expiration_date' => $exp_date,
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el programa de auditoría con Id: '.$audit_audit_plan_audit_program->id.' llamado: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                return Redirect::to('/programas_auditoria');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function updateTest(Request $request, $id)
    {
        try
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

                    $logger = $this->logger3;
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
                    //ACT 29-03-18: Tipo será dinámico
                    if (isset($_POST['type']))
                    {
                        $GLOBALS['audit_test']->evaluation_test_id = $_POST['type'];
                    }
                    else
                    {
                        $GLOBALS['audit_test']->evaluation_test_id = NULL;
                    }
                    //si es que se ingreso stakeholder
                    if (isset($_POST['stakeholder_id']) && $_POST['stakeholder_id'] != '')
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

                    //ACTUALIZACIÓN 30-12: NUEVO FORMATO DE PRUEBAS (PROCESO O ENTIDAD)
                    if (isset($_POST['process_id']))
                    {
                        $GLOBALS['audit_test']->process_id = $_POST['process_id'];
                    }
                    else
                    {
                        $GLOBALS['audit_test']->process_id = NULL;
                    }

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
                                    //primero eliminamos todas las relaciones
                                    DB::table('audit_test_control')
                                        ->where('audit_test_id','=',$GLOBALS['audit_test']->id)
                                        ->delete();

                                    //almacenamos controles en audit_test_control
                                    //ACT 31-05-18: Se debe ingresar control_organization
                                    //Primero obtenemos org
                                    $org = \Ermtool\Organization::getOrgIdByTestId($GLOBALS['audit_test']->id);
                                    //obtenemos control_organization
                                    $co = \Ermtool\ControlOrganization::getByCO($c,$org);

                                    DB::table('audit_test_control')
                                        ->insert([
                                            'audit_test_id' => $GLOBALS['audit_test']->id,
                                            'control_id' => $c,
                                            'control_organization_id' => $co->id
                                            ]);
                                }   
                            }
                        }
                        //sino se agregaron controles, vemos si se especificaron subprocesos
                        else if (isset($_POST['subprocess_id']))
                        {
                            foreach ($_POST['subprocess_id'] as $s)
                            {
                                DB::table('audit_test_subprocess')
                                    ->where('audit_test_id','=',$GLOBALS['audit_test']->id)
                                    ->delete();

                                DB::table('audit_test_subprocess')
                                    ->insert([
                                        'audit_test_id' => $GLOBALS['audit_test']->id,
                                        'subprocess_id' => $s
                                        ]);
                            }
                        }
                    }
                    else if ($_POST['type1'] == 2) //es prueba a nivel de entidad
                    {
                        //vemos si se especificaron los controles
                        if (isset($_POST['control_id']))
                        {
                            foreach ($_POST['control_id'] as $c)
                            {
                                //primero verificamos que no se encuentre vacío (ya que está enviando el último valor vacío)
                                if ($c != NULL && $c != '')
                                {

                                    DB::table('audit_test_control')
                                        ->where('audit_test_id','=',$GLOBALS['audit_test']->id)
                                        ->delete();
                                    //almacenamos controles en audit_test_control
                                    //ACT 31-05-18: Se debe ingresar control_organization
                                    //Primero obtenemos org
                                    $org = \Ermtool\Organization::getOrgIdByTestId($GLOBALS['audit_test']->id);
                                    //obtenemos control_organization
                                    $co = \Ermtool\ControlOrganization::getByCO($c,$org);

                                    DB::table('audit_test_control')
                                        ->insert([
                                            'audit_test_id' => $GLOBALS['audit_test']->id,
                                            'control_id' => $c,
                                            'control_organization_id' => $co->id
                                            ]);
                                }   
                            }
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado la prueba de auditoría con Id: '.$GLOBALS['audit_test']->id.' llamado: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                return Redirect::to('programas_auditoria.show.'.$GLOBALS['audit_test']->audit_audit_plan_audit_program_id);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function createTest($id_program)
    {
        try
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

                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                $type2 = NULL;
                $type_id = NULL;

                if (Session::get('languaje') == 'en')
                {
                    //ACT 30-03-18: Agregamos tipos de prueba dinámicos
                    $evaluation_tests = \Ermtool\Evaluation_test::lists('name_eng','id');

                    return view('en.auditorias.create_test2',['audit_program'=>$id_program,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'type2'=>$type2,'type_id'=>$type_id,'evaluation_tests' => $evaluation_tests]);
                }
                else
                {
                    //ACT 30-03-18: Agregamos tipos de prueba dinámicos
                    $evaluation_tests = \Ermtool\Evaluation_test::lists('name','id');

                    return view('auditorias.create_test2',['audit_program'=>$id_program,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'type2'=>$type2,'type_id'=>$type_id,'evaluation_tests' => $evaluation_tests]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function storeTest(Request $request)
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
                DB::transaction(function () {

                    $logger = $this->logger3;
                    $fecha = date('Y-m-d H:i:s');
                    if (isset($_POST['description']) && $_POST['description'] != '')
                    {
                        $description = $_POST['description'];
                    }
                     else
                    {
                        $description = NULL;
                    }
                    //si es que se ingreso tipo
                    if (isset($_POST['type']) && $_POST['type'] != '')
                    {
                        $type = $_POST['type'];
                    }
                    else
                    {
                        $type = NULL;
                    }
                    //si es que se ingreso stakeholder
                    if (isset($_POST['stakeholder_id']) && $_POST['stakeholder_id'] != '')
                    {
                        $stakeholder = $_POST['stakeholder_id'];
                    }
                    else
                    {
                        $stakeholder = NULL;
                    }
                    //si es que se ingreso HH
                    if (isset($_POST['hh']) && $_POST['hh'] != '')
                    {
                        $hh = $_POST['hh'];
                    }
                    else
                    {
                        $hh = NULL;
                    }

                    //ACTUALIZACIÓN 07-12-16: La prueba ya no puede ser de riesgo; estará orientada a proceso o entidad; si es de proceso se dejará abierto si se seleccionan los controles o los subprocesos o sólo el proceso. A nivel de entidad se seleccionará sólo la perspectiva o se especificarán los controles a nivel de entidad

                    if (isset($_POST['process_id']) && $_POST['process_id'] != '')
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
                                'evaluation_test_id' => $type,
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
                        else if (isset($_POST['subprocess_id']) && $_POST['subprocess_id'])
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la prueba de auditoría con Id: '.$test_id.' llamada: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/programas_auditoria.show.'.$_POST['audit_audit_plan_audit_program_id']);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function showAuditoria($id)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
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
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                //obtenemos lista de organizaciones
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                //ACTUALIZACIÓN 08-02-2017; OBTENEMOS UNIVERSO DE AUDITORÍAS DISTINTAS A LAS QUE YA EXISTEN
                $audits = \Ermtool\Audit::getAuditsNotSelected($id);

                //obtenemos universo de auditorías seleccionadas
                $audits_selected = DB::table('audit_audit_plan')
                            ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                            ->where('audit_plan_id','=',$id)
                            ->select('audits.id as id','audits.name as name')
                            ->get();
                //cada una de las auditorías pertenecientes al plan
                //obtenemos riesgos de procesos que ya fueron seleccionados
                //ACT 05-04-17: Ya no se usan
                /*$riesgos_selected = DB::table('audit_plan_risk')
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
                }*/
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
                                                'audits'=>$audits,'audits_selected'=>$audits_selected,
                                                'stakeholder' => $stakeholder1, 'stakeholder_team' => json_encode($stakeholder_team),
                                                'id'=>$idorg,'audit_plan'=>$audit_plan]);
                }
                else
                {
                    return view('auditorias.edit',['stakeholders'=>$stakeholders,'organizations'=>$organizations,
                                                'audits'=>$audits,'audits_selected'=>$audits_selected,
                                                'stakeholder' => $stakeholder1, 'stakeholder_team' => json_encode($stakeholder_team),
                                                'id'=>$idorg,'audit_plan'=>$audit_plan]);
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
    public function update(Request $request, $idtemp)
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
                global $id;
                $id = $idtemp;
                //creamos una transacción para cumplir con atomicidad
                DB::transaction(function()
                {
                    $logger = $this->logger;
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
                            'objectives'=>$objectives,
                            'scopes'=>$scopes,
                            'status'=>0,
                            'resources'=>$resources,
                            'methodology'=>$methodology,
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

                        //ahora guardamos auditorías que no son nuevas (si es que hay)
                        //insertamos cada auditoria (de universo de auditorias) en audit_audit_plan
                        //ACTUALIZACIÓN 09-02: GUARDAREMOS SOLO AUDITORÍAS QUE SE ESTÉN AGREGANDO, LAS ANTIGUAS SOLO LAS ACTUALIZAMOS

                        //primero obtenemos id de auditorías que ya existen para este plan
                        $audits_exist = \Ermtool\Audit::getAudits($GLOBALS['id']);

                        foreach ($audits_exist as $a) //actualizamos estos datos
                        {
                            if ($_POST['audit_'.$a->id.'_resources'] == "")
                            {
                                $resources = NULL;
                            }
                            else
                            {
                                $resources = $_POST['audit_'.$a->id.'_resources'];
                            }

                            //actualizamos
                            $audit_audit_plan_id = DB::table('audit_audit_plan')
                                    ->where('audit_plan_id','=',$GLOBALS['id'])
                                    ->where('audit_id','=',$a->id)
                                    ->update([
                                        'initial_date' => $_POST['audit_'.$a->id.'_initial_date'],
                                        'final_date' => $_POST['audit_'.$a->id.'_final_date'],
                                        'resources' => $resources,
                                    ]);
                        }
                        $i = 1;
                        if (isset($_POST['audits'])) //auditorías existentes que se están agregando
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
                                                'audit_plan_id' => $GLOBALS['id'],
                                                'audit_id' => $audit,
                                                'initial_date' => $_POST['audit_'.$audit.'_initial_date'],
                                                'final_date' => $_POST['audit_'.$audit.'_final_date'],
                                                'resources' => $resources,
                                                ]);
                                
                            }
                        }

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
                            
                            $i += 1;
                        }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Audit plan successfully updated');
                    }
                    else   
                    {
                        Session::flash('message','Plan de auditor&iacute;a actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el plan de auditoría con Id: '.$GLOBALS['id'].' llamado: '.$_POST['name'].' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/plan_auditoria');
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
    public function destroy($id) //eliminar plan de auditoría
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {

                $logger = $this->logger;
                //primero obtenemos audit_audit_plan para hacer las validaciones
                $audit_audit_plan = DB::table('audit_audit_plan')
                            ->where('audit_plan_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();
                $rev2 = 0; //variable local para ver 
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

                    $name = \Ermtool\Audit_plan::name($GLOBALS['id1']);
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el plan de auditoría con Id: '.$GLOBALS['id1'].' llamado: '.$name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                }
            });
            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function destroyProgram($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {

                $logger = $this->logger2;
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
                        //nombre de program para log
                        $name = DB::table('audit_programs')
                            ->where('id','=',$program->audit_program_id)
                            ->select('name')->first();

                        //nombre de audit y audit_plan para log
                        $a = DB::table('audits')
                                ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_audit_plan_id','=','audit_audit_plan.id')
                                ->where('audit_audit_plan_audit_program.id','=',$GLOBALS['id1'])
                                ->select('audit_plans.name as audit_plan','audits.name as audit')
                                ->first();

                        //obtenemos nombre de programa
                        DB::table('audit_programs')
                            ->where('id','=',$program->audit_program_id)
                            ->delete();
                    }
                    $GLOBALS['res'] = 0;

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el programa de auditoría con Id: '.$GLOBALS['id1'].' llamado: '.$name->name.', asociado a la auditoría: '.$a->audit.' perteneciente al plan: '.$a->audit_plan.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                }
                
            });
            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function destroyTest($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {
                $logger = $this->logger3;
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
                            //nombre de audit y audit_plan para log
                            $a = DB::table('audits')
                                    ->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
                                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_audit_plan_id','=','audit_audit_plan.id')
                                    ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                                    ->where('audit_tests.id','=',$GLOBALS['id1'])
                                    ->select('audit_plans.name as audit_plan','audits.name as audit','audit_programs.name as program')
                                    ->first();
                            //nombre
                            $name = DB::table('audit_tests')->where('id',$GLOBALS['id1'])->value('name');
                            //ahora se puede eliminar

                            //primero eliminamos de audit_test_control (si es que hay)
                            DB::table('audit_test_control')
                                ->where('audit_test_id','=',$GLOBALS['id1'])
                                ->delete();
                                
                            DB::table('audit_tests')
                                ->where('id','=',$GLOBALS['id1'])
                                ->delete();

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la prueba de auditoría con Id: '.$GLOBALS['id1'].' llamado: '.$name.', asociado al programa '.$a->program.' de la auditoría: '.$a->audit.' perteneciente al plan: '.$a->audit_plan.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                            $GLOBALS['res'] = 0;
                        } 
                    } 
                }
            });
            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACTUALIZACIÓN 09-02-2017: Eliminar auditoría
    public function destroyAudit($audit_audit_plan_id)
    {
        try
        {
            global $id;
            $id = $audit_audit_plan_id;
            global $res;
            $res = 1;
            DB::transaction(function() {
                //primero obtenemos audit_audit_plan para hacer las validaciones

                $rev = DB::table('audit_audit_plan_audit_program')
                        ->where('audit_audit_plan_id','=',$GLOBALS['id'])
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //vemos si tiene hallazgos
                    $rev = DB::table('issues')
                            ->where('audit_audit_plan_id','=',$GLOBALS['id'])
                            ->select('id')
                            ->get();

                    if (empty($rev)) //si no tiene ni programas ni hallazgos se puede eliminar
                    {
                        DB::table('audit_audit_plan')
                            ->where('id','=',$GLOBALS['id'])
                            ->delete();
                            
                        $GLOBALS['res'] = 0;
                    }
                }
            });
            
            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función para ver todas las pruebas
    public function pruebas()
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función para revisar las notas agregadas por el auditor jefe
    public function notas()
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
                    return view('en.auditorias.notas',['organizations' => $organizations]);
                }
                else
                {
                    return view('auditorias.notas',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función para responder una nota por parte de un auditori
    public function responderNota(Request $request)
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
                $id = $_POST['note_id'];
                global $evidence;
                $evidence = $request->file('evidencia_'.$id);
                DB::transaction(function() {
                    $logger = $this->logger4;
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

                        $name = DB::table('notes')->where('id',$id)->value('name');

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha respondido la nota con Id: '.$id.' llamada: '.$name.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function actionPlans()
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function storePlan(Request $request)
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
                DB::transaction(function() {
                    //id del issue que se está agregando
                    $id = $_POST['issue_id'];

                    //VERIFICAMOS INGRESO DE DATOS 30-01-2017
                    if (isset($_POST['description_'.$id]) AND $_POST['description_'.$id] != '')
                    {
                        $description = $_POST['description_'.$id];
                    }
                    else
                    {
                        $description = NULL;
                    }

                    if (isset($_POST['responsable_'.$id]) && $_POST['responsable_'.$id] != '')
                    {
                        $stakeholder_id = $_POST['responsable_'.$id];
                    }
                    else
                    {
                        $stakeholder_id = NULL;
                    }

                    if (isset($_POST['final_date_'.$id]) && $_POST['final_date_'.$id] != '')
                    {
                        $final_date = $_POST['final_date_'.$id];
                    }
                    else
                    {
                        $final_date = NULL;
                    }

                    $new_id = DB::table('action_plans')
                                    ->insertGetId([
                                            'issue_id' => $id,
                                            'stakeholder_id' => $stakeholder_id,
                                            'description' => $description,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                            'final_date' => $final_date,
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    
    //función para reporte de auditorías
    public function auditsReport()
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
                    return view('en.reportes.auditorias',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.auditorias',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //reporte de auditorías
    public function generarReporteAuditorias($org)
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

                        //ACT 23-01-18: Hacemos verificador para caso de muchas auditorías (aun no se implementa este verificador)
                        $ver = 0;
                        if (count($audits) > 4)
                        {
                            $ver = 1;
                        }

                        foreach ($audits as $audit)
                        {
                            $auditorias[$j] = ['id' => $audit->id, 'name' => $audit->name];
                            $j += 1;
                        }
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit')) //ACT 23-01-18: guardamos sólo los datos necesarios para el excel (no se envía nuevo dato verificador de cantidad de auditorias)
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
                                    'Final_date' => $final_date
                                    
                            ];
                        }
                        else
                        {
                            $results[$i] = [
                                    'id' => $plan->id,
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
                                    'verificador' => $ver                        
                            ];
                        }
                    }
                    else
                    {
                        if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit')) //ACT 23-01-18: guardamos sólo los datos necesarios para el excel (no se envía nuevo dato verificador de cantidad de auditorias)
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
                                    'Fecha_fin' => $final_date        
                            ];
                        }
                        else
                        {
                            $results[$i] = [
                                    'id' => $plan->id,
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
                                    'verificador' => $ver         
                            ];
                        }
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función que obtiene objetivos y riesgos de objetivos a través de JsON para la creación de un plan de pruebas
    public function getObjetivos($org)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    
    //Función que obtiene todos los stakeholders menos auditor responsable, al crear plan de auditoría
    public function getStakeholders($rut)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función que obtiene un programa de auditoría (al crear uno nuevo basado en uno antiguo)
    public function getAuditProgram($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $results = array();
                //Seleccionamos programa de auditoría y pruebas
                $program = DB::table('audit_programs')
                            ->where('id','=',$id)
                            ->select('audit_programs.name','audit_programs.id','audit_programs.description')
                            ->first();

                $results = [
                        'name' => $program->name,
                        'id' => $program->id,
                        'description' => $program->description,
                ];

                return json_encode($results);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    /*función que obtiene los datos y las pruebas de un programa de auditoría (al revisar un plan de auditoría)
    a través del identificador de audit_audit_plan (auditoría + plan de auditoría) */
    public function getAuditProgram2($id)
    {
        try
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
                                    ->select('audit_tests.name','audit_tests.description','audit_tests.results','audit_tests.id','audit_tests.status','audit_tests.stakeholder_id','audit_tests.hh_real','audit_tests.comments')
                                    ->get();
                    $audit_tests2 = array(); //seteamos en 0 variable de pruebas
                    $i = 0; //contador de pruebas
                    foreach ($audit_tests as $test)
                    {
                        $test_result = $test->results;
                        if ($test->stakeholder_id == NULL || $test->stakeholder_id == 0)
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
                                        ->select('issues.id','issues.name','issues.description','issues.classification_id','issues.recommendations')
                                        ->get();
                        $debilidades = array();
                        $j = 0;
                        foreach ($issues as $issue)
                        {
                            $debilidades[$j] = [
                                'id' => $issue->id,
                                'name' => $issue->name,
                                'description' => $issue->description,
                                'classification' => $issue->classification_id,
                                'recommendations' => $issue->recommendations
                            ];
                            $j += 1;
                        }

                        //obtenemos documentos asociados a la ejecución de la prueba
                        $files = Storage::files('ejecucion_auditoria/'.$test->id);

                        $audit_tests2[$i] = [
                                'name' => $test->name,
                                'description' => $test->description,
                                'result' => $test_result,
                                'id' => $test->id,
                                'status' => $test->status,
                                'results' => $test->results,
                                'hh_real' => $test->hh_real,
                                'stakeholder' => $stake,
                                'issues' => $debilidades,
                                'comments' => $test->comments,
                                'files' => $files
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene programas de auditoría de una organización
    public function getAuditPrograms($org)
    {
        try
        {
            $audit_programs = \Ermtool\Audit_program::getAuditPrograms($org);           
            return json_encode($audit_programs);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtiene auditorías de una organización
    public function getAudits2($org)
    {
        try
        {
            $audits = \Ermtool\Audit::getAudits2($org);
            return json_encode($audits);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtiene auditorías de un plan
    public function getAudits3($audit_plan_id)
    {
        try
        {
            $audits = \Ermtool\Audit::getAudits($audit_plan_id);
            return json_encode($audits);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtiene pruebas de auditoría de una organización
    public static function getAuditTests($org)
    {
        try
        {
            $audit_tests = \Ermtool\Audit_test::getAuditTests($org);
            return json_encode($audit_tests);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }


    //obtiene auditorías relacionadas a un plan de auditoría
    public function getAudits($id)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    // ESTÁ MALA, CORREGIR SI ES QUE ES NECESARIO 
    // ACTUALIZACIÓN 24-08: LA UTILIZAREMOS PARA GENERAR EL GRÁFICO DE PRUEBAS DE AUDITORÍA
    //obtiene pruebas asociadas a una plan auditoria
    public function getTests($kind,$id)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene datos asociados al último plan de auditoría generado para una organización
    public function getAuditPlan($org)
    {
        try
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
                    //ACT 11-04-17: Ya no hay riesgos asociados directamente al plan
                    /*
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
                    */
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

                        //ACT 11-04-17: Ya no hay riesgos asociados directamente a la auditoría
                        /*
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

                        */
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
                        /*
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
                        */
                        //guardamos datos de pruebas de auditoría de la auditoría seleccionada
                        $auditorias[$i] = ['name' => $audit->name,
                                            'description' => $description,
                                            'initial_date' => $initial_date,
                                            'final_date' => $final_date,
                                            'resources' => $resources,
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
                                'audits' => $auditorias
                        ];
                    return json_encode($results);
                } //if ($audit_plan_id)
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene todos los planes asociados a una organización
    public function getPlanes($org)
    {
        try
        {
            $audit_plans = \Ermtool\Audit_plan::getPlanes($org);
            return json_encode($audit_plans);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene notas asociadas a una prueba de auditoría
    public function getNotes($id)
    {
        try
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
                                     'notes.audit_test_id as test_id','stakeholder_id','user_id')
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

                        //obtenemos stakeholder y user
                        $stakeholder = \Ermtool\Stakeholder::getName($note->stakeholder_id);
                        $user = \Ermtool\User::getName($note->user_id);
                        
                        //obtenemos evidencias de la nota (si es que existe)
                        $evidences = getEvidences(0,$note->id);
                        $lala = new DateTime($note->created_at);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                        $results[$i] = [
                            'id' => $note->id,
                            'name' => $note->name,
                            'description' => $note->description,
                            'created_at' => $fecha_creacion,
                            'status' => $note->status,
                            'status_origin' => $note->status,
                            'test_id' => $note->test_id,
                            'answers' => $answers,
                            'evidences' => $evidences,
                            'stakeholder' => $stakeholder,
                            'user' => $user,
                            ];
                        $i += 1;
                    }
                }
                return json_encode($results);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function closeNote($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger4;

                $res = 0;
                DB::table('notes')
                    ->where('id','=',$id)
                    ->update([
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                $res = 1;

                $name = DB::table('notes')->where('id',$id)->value('name');

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha cerrado la nota con Id: '.$id.' llamada: '.$name.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return $res;
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtenemos id de organización perteneciente a un plan de auditoría
    public function getOrganization($audit_plan)
    {
        try
        {
            $organization = NULL;
            $organization = DB::table('organizations')
                        ->join('audit_plans','audit_plans.organization_id','=','organizations.id')
                        ->where('audit_plans.id','=',$audit_plan)
                        ->select('organizations.id')
                        ->first();
            return json_encode($organization);
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
                    return view('en.reportes.auditorias_graficos',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.auditorias_graficos',['organizations' => $organizations]);
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
                    else if ($audit_plan->status == 0 || $abiertas > 0) //Obviamente abierto
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
                            if ($plan['abiertas'] >= 0 && $plan['ejecucion'] == 0 && $plan['status'] == 0)
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }    
    }
    //obtiene datos de una auditoría
    public function getAudit($id)
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función para cerrar plan de auditoría
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function open($id)
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
                DB::transaction(function() 
                {
                    $logger = $this->logger;

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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha re abierto el plan de auditoría con Id: '.$audit_plan->id.' llamada: '.$audit_plan->name.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('plan_auditoria');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getAuditInfo($audit_plan,$audit)
    {
        try
        {
            //obtenemos información de auditoría
            $audit_info = \Ermtool\Audit::getAuditInfo($audit_plan,$audit);
            return json_encode($audit_info);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 17-04-17: Elimina nota 
    public function destroyNote($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {

                $logger = $this->logger4;
                //revisaremos sólo si tiene respuestas
                $rev = DB::table('notes_answers')
                    ->where('note_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

                if (empty($rev))
                {
                    $name = DB::table('notes')->where('id',$GLOBALS['id1'])->value('name');
                    //si no tiene respuesta se puede eliminar
                    $rev = DB::table('notes')
                            ->where('id','=',$GLOBALS['id1'])
                            ->delete();

                    $GLOBALS['res'] = 0;

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la nota con Id: '.$GLOBALS['id1'].' llamado: '.$name.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
                }
            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function destroyNoteAnswer($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {

                $logger = $this->logger4;
                $answer = DB::table('notes_answers')->where('id',$GLOBALS['id1'])->value('answer');

                $note = DB::table('notes')
                        ->join('notes_answers','notes_answers.note_id','=','notes.id')
                        ->where('notes_answers.id','=',$GLOBALS['id1'])
                        ->select('notes.id','notes.name')
                        ->first();

                //ACT 17-04-17: Simplemente eliminamos respuesta
                DB::table('notes_answers')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                $GLOBALS['res'] = 0;

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la respuesta definida como '.$answer.' asociada a la nota con Id: '.$note->id.' llamada: '.$note->name.',  con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function docxGraficos()
    {
        try
        {
            //$pdf = \App::make('dompdf.wrapper');
            //$nombre = "Reporte de auditorías ".date("Y-m-d H:i:s").".pdf";
            //$pdf->loadHTML($_POST['cuerpo']);
            //return $pdf->stream();

            $word = new \PhpOffice\PhpWord\PhpWord();
            $section = $word->createSection();

            $word->setDefaultFontName('Verdana');
            $word->setDefaultFontSize(10);

            //estilos
            $titleStyle = array('size' => 18, 'color' => '045FB4');
            $subTitle = array('size' => 16);
            $subsubTitle = array('bold' => true);

            //estilos de tablas
            $tableStyleName = 'Audit Plans';
            $tableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
            $tableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true);
            $tableFirstRowStyle2 = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true,'size' => 7);
            $tableCellStyle = array('valign' => 'center');
            $tableCellBtlrStyle = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
            $tableFontStyle = array('bold' => false);
            $tableFontStyle2 = array('bold' => false, 'size' => 7);
            $word->addTableStyle($tableStyleName, $tableStyle, $tableFirstRowStyle);

            $section->addText(
                'Reporte de Gráficos de Auditoría',$titleStyle
            );

            $section->addText(
                'Estado de planes de auditoría', $subTitle   
            );

            //decodificamos los gráficos y los guardamos temporalmente
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['grafico1']));
            file_put_contents('image.png', $data);

            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['grafico2']));
            file_put_contents('image2.png', $data);

            $imageStyle = array('width'=>500, 'height'=>300, 'align'=>'center');
            $section->addImage('image.png', $imageStyle);

            $section->addTextBreak(1);

            //ACTUALIZACIÓN 08-06-17: CREAREMOS TABLAS MANUALMENTE AQUÍ

            $plans = $this->getReportePlanes($_POST['org']);

            $section->addText('Planes de auditoría abiertos',$subsubTitle);
            $table1 = $section->addTable($tableStyleName);
            $table1->addRow();
            $table1->addCell(1750)->addText('Nombre',$tableFirstRowStyle);
            $table1->addCell(1750)->addText('Descripción',$tableFirstRowStyle);
            $table1->addCell(1750)->addText('Auditorías',$tableFirstRowStyle);
            $table1->addCell(1750)->addText('Programas',$tableFirstRowStyle);
            $table1->addCell(1750)->addText('Pruebas',$tableFirstRowStyle);

            $section->addTextBreak(1);

            $section->addText('Planes de auditoría en ejecución',$subsubTitle);
            $table2 = $section->addTable($tableStyleName);
            $table2->addRow();
            $table2->addCell(1750)->addText('Nombre',$tableFirstRowStyle);
            $table2->addCell(1750)->addText('Descripción',$tableFirstRowStyle);
            $table2->addCell(1750)->addText('Auditorías',$tableFirstRowStyle);
            $table2->addCell(1750)->addText('Programas',$tableFirstRowStyle);
            $table2->addCell(1750)->addText('Pruebas',$tableFirstRowStyle);

            $section->addTextBreak(1);

            $section->addText('Planes de auditoría cerrados',$subsubTitle);
            $table3 = $section->addTable($tableStyleName);
            $table3->addRow();
            $table3->addCell(1750)->addText('Nombre',$tableFirstRowStyle);
            $table3->addCell(1750)->addText('Descripción',$tableFirstRowStyle);
            $table3->addCell(1750)->addText('Auditorías',$tableFirstRowStyle);
            $table3->addCell(1750)->addText('Programas',$tableFirstRowStyle);
            $table3->addCell(1750)->addText('Pruebas',$tableFirstRowStyle);

            
            foreach ($plans['audit_plans'] as $plan)
            {
                if ($plan['abiertas'] >= 0 && $plan['ejecucion'] == 0 && $plan['status'] == 0) //planes abiertos
                {
                    $table1->addRow();
                    $table1->addCell(1750)->addText($plan['name'],$tableFontStyle);
                    $table1->addCell(1750)->addText($plan['description'],$tableFontStyle);

                    $audit_string = '';
                    foreach ($plan['audits'] as $audit)
                    {
                        if ($audit == end($plan['audits']))
                        {
                            $audit_string .= $audit;
                        }
                        else
                        {
                           $audit_string .= $audit.', '; 
                        }          
                    }
                    $table1->addCell(1750)->addText($audit_string,$tableFontStyle);

                    $program_string = '';
                    foreach ($plan['programs'] as $program)
                    {
                        if ($program == end($plan['programs']))
                        {
                            $program_string .= $program;
                        }
                        else
                        {
                           $program_string .= $program.', '; 
                        }
                    }

                    $table1->addCell(1750)->addText($program_string,$tableFontStyle);

                    $test_string = '';
                    foreach ($plan['tests'] as $test)
                    {
                        if ($test == end($plan['tests']))
                        {
                            $test_string .= $test;
                        }
                        else
                        {
                           $test_string .= $test.', '; 
                        }
                    }

                    $table1->addCell(1750)->addText($test_string,$tableFontStyle);                     
                }
                else if ($plan['ejecucion'] > 0 && $plan['status'] == 0) //planes en ejecución
                {
                    $table2->addRow();
                    $table2->addCell(1750)->addText($plan['name']);
                    $table2->addCell(1750)->addText($plan['description']);

                    $audit_string = '';
                    foreach ($plan['audits'] as $audit)
                    {
                        if ($audit == end($plan['audits']))
                        {
                            $audit_string .= $audit;
                        }
                        else
                        {
                           $audit_string .= $audit.', '; 
                        }          
                    }
                    $table2->addCell(1750)->addText($audit_string);

                    $program_string = '';
                    foreach ($plan['programs'] as $program)
                    {
                        if ($program == end($plan['programs']))
                        {
                            $program_string .= $program;
                        }
                        else
                        {
                           $program_string .= $program.', '; 
                        }
                    }

                    $table2->addCell(1750)->addText($program_string);

                    $test_string = '';
                    foreach ($plan['tests'] as $test)
                    {
                        if ($test == end($plan['tests']))
                        {
                            $test_string .= $test;
                        }
                        else
                        {
                           $test_string .= $test.', '; 
                        }
                    }

                    $table2->addCell(1750)->addText($test_string); 
                }
                else if ($plan['status'] == 1) //planes cerrados
                {
                    $table3->addRow();
                    $table3->addCell(1750)->addText($plan['name']);
                    $table3->addCell(1750)->addText($plan['description']);

                    $audit_string = '';
                    foreach ($plan['audits'] as $audit)
                    {
                        if ($audit == end($plan['audits']))
                        {
                            $audit_string .= $audit;
                        }
                        else
                        {
                           $audit_string .= $audit.', '; 
                        }          
                    }
                    $table3->addCell(1750)->addText($audit_string);

                    $program_string = '';
                    foreach ($plan['programs'] as $program)
                    {
                        if ($program == end($plan['programs']))
                        {
                            $program_string .= $program;
                        }
                        else
                        {
                           $program_string .= $program.', '; 
                        }
                    }

                    $table3->addCell(1750)->addText($program_string);

                    $test_string = '';
                    foreach ($plan['tests'] as $test)
                    {
                        if ($test == end($plan['tests']))
                        {
                            $test_string .= $test;
                        }
                        else
                        {
                           $test_string .= $test.', '; 
                        }
                    }

                    $table3->addCell(1750)->addText($test_string); 
                }
            }

            $section->addTextBreak(1);

            //nombre de plan de auditoría seleccionado
            $plan_name = \Ermtool\Audit_plan::name($_POST['audit_plan']);


            //obtenemos todas las pruebas (0 es para pruebas abiertas en caso de ser excel, pero aquí da lo mismo)
            $tests = json_decode($this->getTests(0,$_POST['audit_plan']));

            //si es que hay pruebas continuamos (OBS! veremos sólo pruebas de auditoría abiertas o cerradas)
            if ($tests->pruebas_abiertas > 0 || $tests->pruebas_cerradas > 0)
            {
                $section->addText(
                    'Pruebas de Plan de Auditoría: '.$plan_name, $subTitle   
                );
                
                $section->addImage('image2.png', $imageStyle);

                $section->addText('Pruebas de auditoría abiertas',$subsubTitle);
                $table1 = $section->addTable($tableStyleName);
                $table1->addRow();
                $table1->addCell(1750)->addText('Auditoría',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Programa',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Prueba',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Descripción',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Tipo',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Resultado',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('HH Planificadas',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('HH Reales',$tableFirstRowStyle2);
                $table1->addCell(1750)->addText('Responsable',$tableFirstRowStyle2);

                $section->addTextBreak(1);

                $section->addText('Pruebas de auditoría cerradas',$subsubTitle);
                $table2 = $section->addTable($tableStyleName);
                $table2->addRow();
                $table2->addCell(1750)->addText('Auditoría',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Programa',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Prueba',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Descripción',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Tipo',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Resultado',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('HH Planificadas',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('HH Reales',$tableFirstRowStyle2);
                $table2->addCell(1750)->addText('Responsable',$tableFirstRowStyle2);

                foreach ($tests->audit_tests as $test)
                {
                    if ($test->status == 0) //tabla de pruebas abiertas
                    {
                        $table1->addRow();
                        $table1->addCell(1750)->addText($test->audit_name,$tableFontStyle2);
                        $table1->addCell(1750)->addText($test->audit_program_name,$tableFontStyle2);
                        $table1->addCell(1750)->addText($test->name,$tableFontStyle2);
                        $table1->addCell(1750)->addText($test->description,$tableFontStyle2);

                        if ($test->type == 0)
                        {
                            $table1->addCell(1750)->addText('Prueba de diseño',$tableFontStyle2);
                        }
                        else if ($test->type == 1)
                        {
                            $table1->addCell(1750)->addText('Prueba de efectividad operativa',$tableFontStyle2);
                        }
                        else if ($test->type == 2)
                        {
                            $table1->addCell(1750)->addText('Prueba sustantiva',$tableFontStyle2);
                        }
                        else if ($test->type == 3)
                        {
                            $table1->addCell(1750)->addText('Prueba de cumplimiento',$tableFontStyle2);
                        }
                        else
                        {
                            $table1->addCell(1750)->addText('Tipo no definido',$tableFontStyle2);
                        }

                        if ($test->results == 0)
                        {
                            $table1->addCell(1750)->addText('Inefectiva',$tableFontStyle2);
                        }
                        else if ($test->results == 1)
                        {
                            $table1->addCell(1750)->addText('Efectiva',$tableFontStyle2);
                        }
                        else if ($test->results == 2)
                        {
                            $table1->addCell(1750)->addText('En proceso',$tableFontStyle2);
                        }

                        if (!$test->hh_plan)
                        {
                            $table1->addCell(1750)->addText('No se han planificado horas hombre',$tableFontStyle2);
                        }   
                        else
                        {
                            $table1->addCell(1750)->addText($test->hh_plan,$tableFontStyle2);
                        } 

                        if (!$test->hh_real)
                        {
                            $table1->addCell(1750)->addText('No se han agregado horas hombre',$tableFontStyle2);
                        }   
                        else
                        {
                            $table1->addCell(1750)->addText($test->hh_real,$tableFontStyle2);
                        }

                        $table1->addCell(1750)->addText($test->stakeholder,$tableFontStyle2);                
                    }

                    else if ($test->status == 2) //tabla de pruebas cerradas
                    {
                        $table2->addRow();
                        $table2->addCell(1750)->addText($test->audit_name,$tableFontStyle2);
                        $table2->addCell(1750)->addText($test->audit_program_name,$tableFontStyle2);
                        $table2->addCell(1750)->addText($test->name,$tableFontStyle2);
                        $table2->addCell(1750)->addText($test->description,$tableFontStyle2);

                        if ($test->type == 0)
                        {
                            $table2->addCell(1750)->addText('Prueba de diseño',$tableFontStyle2);
                        }
                        else if ($test->type == 1)
                        {
                            $table2->addCell(1750)->addText('Prueba de efectividad operativa',$tableFontStyle2);
                        }
                        else if ($test->type == 2)
                        {
                            $table2->addCell(1750)->addText('Prueba sustantiva',$tableFontStyle2);
                        }
                        else if ($test->type == 3)
                        {
                            $table2->addCell(1750)->addText('Prueba de cumplimiento',$tableFontStyle2);
                        }
                        else
                        {
                            $table2->addCell(1750)->addText('Tipo no definido',$tableFontStyle2);
                        }

                        if ($test->results == 0)
                        {
                            $table2->addCell(1750)->addText('Inefectiva',$tableFontStyle2);
                        }
                        else if ($test->results == 1)
                        {
                            $table2->addCell(1750)->addText('Efectiva',$tableFontStyle2);
                        }
                        else if ($test->results == 2)
                        {
                            $table2->addCell(1750)->addText('En proceso',$tableFontStyle2);
                        }

                        if (!$test->hh_plan)
                        {
                            $table2->addCell(1750)->addText('No se han planificado horas hombre',$tableFontStyle2);
                        }   
                        else
                        {
                            $table2->addCell(1750)->addText($test->hh_plan,$tableFontStyle2);
                        } 

                        if (!$test->hh_real)
                        {
                            $table2->addCell(1750)->addText('No se han agregado horas hombre',$tableFontStyle2);
                        }   
                        else
                        {
                            $table2->addCell(1750)->addText($test->hh_real,$tableFontStyle2);
                        }

                        $table2->addCell(1750)->addText($test->stakeholder,$tableFontStyle2);   
                    }
                }
            }
            else
            {
                $section->addText(
                    'No se han agregado pruebas de auditoría para el plan seleccionado', $subTitle   
                );
            }
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
            $objWriter->save('auditorias_graficos.docx');
            
            //generamos doc para guardar
            $file_url = 'auditorias_graficos.docx';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url); // do the double-download-dance (dirty but worky)

            //ahora borramos archivos temporales
            unlink('auditorias_graficos.docx');
            unlink('image.png');
            unlink('image2.png');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}