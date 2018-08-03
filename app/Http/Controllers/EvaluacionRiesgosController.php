<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Mail;
use Session;
use Redirect;
use DB;
use DateTime;
use ArrayObject;
use Auth;
use Input;
use Ermtool\Http\Controllers\ControlesController as Controles;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class EvaluacionRiesgosController extends Controller
{
    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('evaluacion_riesgos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/evaluacion_riesgos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function getEvalRisks($eval_id,$rut)
    {
        try
        {
            //obtenemos datos
            $evaluation_risk = DB::table('evaluation_risk')->where('evaluation_id','=',$eval_id)->get();

            $user_answers = array(); //posibles respuestas ingresadas anteriormente
            $j = 0; //contador de posibles respuestas previas
            $i = 0;

            foreach ($evaluation_risk as $risk)
            {
                //obtenemos posibles respuestas (si es que ya se ha ingresado)
                //ACTUALIZACIÓN 22-08-17: Seleccionamos también los comentarios
                $respuestas = DB::table('evaluation_risk_stakeholder')
                                    ->where('evaluation_risk_id','=',$risk->id)
                                    ->where('stakeholder_id','=',$rut)
                                    ->select('evaluation_risk_id as id','probability','impact','comments')
                                    ->get();

                foreach ($respuestas as $respuesta)
                {
                    $user_answers[$j] = array(
                                'impact'=>$respuesta->impact,
                                'probability' => $respuesta->probability,
                                'comments' => $respuesta->comments,
                                'id'=>$respuesta->id,
                            );
                    $j += 1;
                }
                    //-- vemos si es de proceso o de negocio --//
                    //ya no es necesario ver si es de proceso o de negocio

                        $r = \Ermtool\Risk::getRisksFromOrgRisk($risk->organization_risk_id);

                        //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                        foreach ($r as $r)
                        {
                            //obtenemos subprocesos u objetivos
                            $subobj = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($r->id,$r->org_id);

                            if (empty($subobj)) //entonces es de negocio
                            {
                                $subobj = \Ermtool\Objective::getObjectivesFromOrgRisk($r->id,$r->org_id);
                                $type = 'objective';
                            }
                            else
                            {
                                $type = 'subprocess';
                            }

                            $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                                'risk_name' => $r->risk_name,
                                                'description' => $r->description,
                                                'subobj' => $subobj,
                                                'org' => $r->org,
                                                'type' => $type);
                        }
                    $i += 1;
            }

            return ['riesgos'=>$riesgos,'user_answers'=>$user_answers];
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function mensaje($id)
    {
        //ACT 06-04-18: Obtenemos URL desde base de datos
        $conf = \Ermtool\Configuration::where('option_name','=','system_url')->first();

        if (!empty($conf))
        {
            if (Session::get('languaje') == 'en')
            {
                //Mensaje predeterminado al enviar encuestas (inglés)
                $mensaje = "Dear User.

                            We send to you the following poll for risk assessments. You must assign a value on probability and impact for each one of the risks associated to the survey. To answer this poll you have to access to the following link:

                            http://".$conf->option_value."/evaluacion_encuesta.{$id}

                            Best Regards,
                            Administration.";
                        
            }
            else
            {
                //Mensaje predeterminado al enviar encuestas
                $mensaje = "Estimado Usuario.

                            Le enviamos la siguiente encuesta para la evaluación de riesgos. Ud deberá asignar un valor de probabilidad e impacto para cada uno de los riesgos asociados a la encuesta. Para responderla deberá acceder al siguiente link.

                            http://".$conf->option_value."/evaluacion_encuesta.{$id}

                            Saludos cordiales,
                            Administrador.";
            }
            return $mensaje;
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.configuration.create');
            }
            else
            {
                return view('configuration.create');
            }
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
                return Redirect::route('/');
            }
            else
            {
                //obtenemos riesgos generales
                //$riesgos_gral = DB::table('risks')->where('type2',1)->distinct()->lists('name','name');


                //ACTUALIZACIÓN 25-07: En vez de obtener y enviar riesgos, enviamos organización para poder seleccionar
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');
                //juntamos riesgos
                //$riesgos = $riesgos_sub+$riesgos_obj; no se pueden juntar ya que se puede repetir id

                if (Session::get('languaje') == 'en')
                { 
                    return view('en.evaluacion.crear_evaluacion',['organizations'=>$organizations,'categories' => $categories]);
                }
                else
                {
                    return view('evaluacion.crear_evaluacion',['organizations'=>$organizations,'categories' => $categories]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $riesgos_objetivos = array();
                $riesgos_subprocesos = array();

                global $verificador;
                $verificador = 0;
                if (isset($_POST['manual'])) //Se está evaluando manualmente
                {

                    $tipo = 0; //evaluación manual

                    $i = 0;
                    if (isset($_POST['objective_risk_id'])) //insertamos primero riesgos de negocio -> si es que se agregaron
                    {
                        foreach ($_POST['objective_risk_id'] as $objective_risk_id)
                        {
                            $riesgos_objetivos[$i] = $objective_risk_id;
                            $i += 1;
                        }
                    }

                    $i = 0;
                    if (isset($_POST['risk_subprocess_id'])) //ahora insertamos riesgos de subproceso (si es que se agregaron)
                    {
                        foreach ($_POST['risk_subprocess_id'] as $subprocess_risk_id)
                        {
                            $riesgos_subprocesos[$i] = $subprocess_risk_id;
                            $i += 1;
                        }
                    }

                    return $this->generarEvaluacionManual($riesgos_subprocesos,$riesgos_objetivos);

                }

                else //se está creando encuesta de evaluación
                {
                    DB::transaction(function () {
                        $logger = $this->logger;
                        if (!isset($_POST['objective_risk_id']) && !isset($_POST['risk_subprocess_id'])) //no se ingresó ningún riesgo
                        {
                            Session::flash('error','Debe ingresar a lo menos un riesgo');
                            $GLOBALS['verificador'] = 1;
                        }
                        else
                        {
                            if ($_POST['expiration_date'] == "" || $_POST['expiration_date'] == NULL)
                            {
                                $exp_date = NULL;
                            }
                            else
                            {
                                $exp_date = $_POST['expiration_date'];
                            }
                            //agregamos evaluación y obtenemos id
                            $eval_id = DB::table('evaluations')->insertGetId([
                                'name' => $_POST['name'],
                                'consolidation' => 0,
                                'description' => $_POST['description'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'expiration_date' => $exp_date,
                                ]);

                        
                            if (isset($_POST['objective_risk_id'])) //insertamos primero riesgos de negocio -> si es que se agregaron
                            {
                                foreach ($_POST['objective_risk_id'] as $objective_risk_id)
                                {                   
                                    //insertamos riesgo de negocio en evaluation_risk
                                    //ACT 30-03-17: objective_risk_id => organizat...
                                    DB::table('evaluation_risk')->insert([
                                        'evaluation_id' => $eval_id,
                                        'organization_risk_id' => $objective_risk_id,
                                        ]); 
                                }
                            }

                            $i = 0;
                            if (isset($_POST['risk_subprocess_id'])) //ahora insertamos riesgos de subproceso (si es que se agregaron)
                            {
                                foreach ($_POST['risk_subprocess_id'] as $subprocess_risk_id)
                                {
                                    //inseratmos riesgo de subproceso en evaluation_risk
                                    //ACT 30-03-17: risk_subprocess_id => organizat...
                                    DB::table('evaluation_risk')->insert([
                                        'evaluation_id' => $eval_id,
                                        'organization_risk_id' => $subprocess_risk_id,
                                        ]); 
                                }
                            }
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Assessment poll successfully created');
                            }
                            else
                            {
                                Session::flash('message','Encuesta de evaluacion agregada correctamente');
                            }

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la encuesta de evaluación de Id: '.$eval_id.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                        }
                        
                    });

                    if ($verificador == 1)
                    {
                        return Redirect::to('evaluacion')->withInput();
                    }
                    else
                    {
                        return Redirect::to('evaluacion_agregadas');
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

    //Función que mostrará lista de encuestas agregadas
    public function encuestas()
    {
        //try
        //{
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //ACT 30-12-17: Sacamos la consolidación para ver todas
                //$encuestas = \Ermtool\Evaluation::where('consolidation',0)->get(); //se muestran las encuestas NO consolidadas
                $encuestas = \Ermtool\Evaluation::all();
                $i = 0;
                $fecha = array();
                foreach($encuestas as $encuesta)
                {
                    if ($encuesta->expiration_date != NULL)
                    {
                        $expiration_date = new DateTime($encuesta->expiration_date);
                        $encuesta->expiration_date = date_format($expiration_date, 'd-m-Y');         
                    }
                    else
                    {
                        $encuesta->expiration_date = "Ninguna";
                    }

                    if ($encuesta->created_at != NULL)
                    {
                        $created_at = new DateTime($encuesta->created_at);
                        $encuesta->created_at = date_format($created_at, 'Y-m-d');
                    }
                    

                    //$fecha[$i] = ['evaluation_id' => $encuesta->id,
                    //              'expiration_date' => $fecha_exp];

                    $i += 1;
                }
                
                if (Session::get('languaje') == 'en')
                { 
                    return view('en.evaluacion.encuestas',['encuestas'=>$encuestas,'fecha'=>$fecha]);
                }
                else
                {
                    return view('evaluacion.encuestas',['encuestas'=>$encuestas,'fecha'=>$fecha]);
                }
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    public function show($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $poll = \Ermtool\Evaluation::find($id);

                $encuesta = array();
                $stakeholders = array();

                $encuesta['id'] = $poll['id'];
                $encuesta['name'] = $poll['name'];
                $encuesta['description'] = $poll['description'];

                $created_at = new DateTime($poll['created_at']);
                $encuesta['created_at'] = date_format($created_at, 'd-m-Y');
                $encuesta['created_at'] .= " a las ".date_format($created_at, 'H:i:s');

                if ($poll['expiration_date'] != NULL)
                {
                    $expiration_date = new DateTime($poll['expiration_date']);
                    $encuesta['expiration_date'] = date_format($expiration_date, 'd-m-Y');
                }
                else
                {
                    $encuesta['expiration_date'] = "";
                }

                //seleccionamos riesgos de evaluation_risk, a través de id de encuesta
                $risks = DB::table('evaluation_risk')->where('evaluation_id','=',$poll['id'])->get();

                $riesgos = array(); //almacenaremos nombre de riesgos para mostrar
                $i = 0; //contador de riesgos

                foreach ($risks as $risk)
                {
                    //obtenemos nombre de riesgo y de subproceso
                    $r = \Ermtool\Risk::getRisksFromOrgRisk($risk->organization_risk_id);

                    foreach ($r as $r)
                    {
                        $riesgos[$i] = array('risk_name' => $r->risk_name,
                                             'description' => $r->description,
                                             'org' => $r->org);
                    }

                    $i += 1;   

                }

                //obtenemos stakeholders (si es que hay) a los que se ha enviado esta encuesta
                $users = DB::table('stakeholders')
                            ->join('evaluation_stakeholder','evaluation_stakeholder.stakeholder_id','=','stakeholders.id')
                            ->where('evaluation_stakeholder.evaluation_id','=',$id)
                            ->select('stakeholders.id','stakeholders.name','stakeholders.surnames')
                            ->get();
                $i = 0;
                foreach ($users as $user)
                {
                    //vemos si cada uno de estos usuarios ha enviado respuestas (solo nos basta con que haya uno por lo que seleccionamos "first")
                    $answers = DB::table('evaluation_risk_stakeholder')
                                ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                ->where('evaluation_risk.evaluation_id','=',$id)
                                ->where('evaluation_risk_stakeholder.stakeholder_id','=',$user->id)
                                ->select('evaluation_risk_stakeholder.id')
                                ->first();
                    if ($answers)
                    {
                        $res = 0;
                    }
                    else
                    {
                        $res = 1;
                    }
                    $stakeholders[$i] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surnames' => $user->surnames,
                        'answers' => $res,
                    ];

                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.evaluacion.show',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'stakeholders'=>$stakeholders]);
                }
                else
                {
                    return view('evaluacion.show',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'stakeholders'=>$stakeholders]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function enviar($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                if (Session::get('languaje') == 'en')
                {
                    return view('en.evaluacion.enviar',['encuesta_id'=>$id,'stakeholders'=>$stakeholders,'mensaje'=>$this->mensaje($id)]);
                }
                else
                {
                    return view('evaluacion.enviar',['encuesta_id'=>$id,'stakeholders'=>$stakeholders,'mensaje'=>$this->mensaje($id)]);
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

    //función que generará la encuesta manual para proceder a guardar evaluación y encuesta
    public function generarEvaluacionManual($subprocess_risk,$objective_risk)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $tipo = 0; //identifica evaluación manual
                if (Session::get('languaje') == 'en')
                {
                    $encuesta = "Manual evaluation";
                }
                else
                {
                    $encuesta = "Evaluación Manual";
                }
                $riesgos = array();
                $i = 0;
                $id = 0;
                
                //cada uno de los riesgos de subproceso
                foreach ($subprocess_risk as $risk)
                {

                        $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                        //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                        foreach ($risk1 as $r)
                        {
                            $org_id = $r->org_id;
                            //obtenemos subprocesos relacionados
                            $subobj = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($r->id,$r->org_id);

                            $riesgos[$i] = array('type' => 'subprocess',
                                                'org_risk_id' => $risk,
                                                'risk_name' => $r->risk_name,
                                                'description' => $r->description,
                                                'subobj' => $subobj);
                            $i += 1;
                        }
                }

                foreach ($objective_risk as $risk)
                {
                        //obtenemos nombre de riesgo y organizacion
                        $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                        foreach ($risk1 as $r)
                        {
                            $org_id = $r->org_id;
                            //obtenemos objetivos relacionados
                            $subobj = \Ermtool\Objective::getObjectivesFromOrgRisk($r->id,$r->org_id);

                            $riesgos[$i] = array('type' => 'objective',
                                                'org_risk_id' => $risk,
                                                'risk_name' => $r->risk_name,
                                                'description' => $r->description,
                                                'subobj' => $subobj);
                            $i += 1;
                        }
                    
                }
                $user_answers = array();
                if (Session::get('languaje') == 'en')
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);
                    $org_name = \Ermtool\Organization::name($org_id);
                    return view('en.evaluacion.encuesta',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'tipo'=>$tipo,
                            'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$id,'user_answers' => $user_answers,'org_name' => $org_name]);
                }
                else
                {
                    if (isset($org_id))
                    {
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1);
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);
                        $org_name = \Ermtool\Organization::name($org_id);
                        return view('evaluacion.encuesta',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'tipo'=>$tipo,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$id,'user_answers' => $user_answers,'org_name' => $org_name]);
                    }
                    else
                    {
                        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                        $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');
                        Session::flash('error','Debe seleccionar a lo menos un Riesgo');
                        return view('evaluacion.evaluacion_manual',['organizations' => $organizations,'categories' => $categories]);
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
    //función que generará la encuesta para que el usuario pueda responderla
    public function generarEncuesta()
    {
        try
        {
            $tipo = 1; //identifica que es encuesta y no evaluación manual
            $encuesta = \Ermtool\Evaluation::find($_POST['encuesta_id']);

            //vemos si la encuesta se encuentra consolidada o no (ACTUALIZACIÖN: 24-01-17)
            if ($encuesta->consolidation == 1)
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('error',"The poll si consolidated so it can not be answered");
                    return view('en.evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
                }
                else
                {
                    Session::flash('error','La encuesta se encuentra consolidada por lo que no puede ser respondida');
                    return view('evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
                }
            }
            else
            {   
                $res = array();

                //ACTUALIZACIÓN 24-08-17: Veremos si el id es mayor o igual al máximo permitido por INT
                if ($_POST['id'] >= 2147483647)
                {
                    //realizaremos división y utilizamos entero
                    $id = $_POST['id'] / 100;
                    $_POST['id'] = (int)$id;
                }
                //primero, verificamos que el usuario exista
                $user = DB::table('evaluation_stakeholder')
                            ->where('evaluation_id','=',$_POST['encuesta_id'])
                            ->where('stakeholder_id','=',$_POST['id'])
                            ->select('stakeholder_id')
                            ->first();

                if (!$user)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error',"The poll doesn't send to the entered user");
                        return view('en.evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
                    }
                    else
                    {
                        Session::flash('error','La encuesta no ha sido enviada al usuario ingresado');
                        return view('evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
                    }
                }
                else
                {   
                    //cada uno de los riesgos de la evaluación
                    $res = $this->getEvalRisks($_POST['encuesta_id'],$user->stakeholder_id);
                    
                    //ACTUALIZACIÓN 23-08-17: Obtenemos nombre org
                    $org_name = $res['riesgos'][0]['org'];
                    if (Session::get('languaje') == 'en')
                    {
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);
                        
                        return view('en.evaluacion.encuesta',['encuesta'=>$encuesta->name,'riesgos'=>$res['riesgos'],'tipo'=>$tipo,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$_POST['encuesta_id'],'user_answers' => $res['user_answers'],'stakeholder'=>$user->stakeholder_id,'org_name' => $org_name]);
                    }
                    else
                    {
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1);
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                        return view('evaluacion.encuesta',['encuesta'=>$encuesta->name,'riesgos'=>$res['riesgos'],'tipo'=>$tipo,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$_POST['encuesta_id'],'user_answers' => $res['user_answers'],'stakeholder'=>$user->stakeholder_id,'org_name' => $org_name]);
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

    //Función para enviar correo con link a la encuesta
    public function enviarCorreo(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //guardamos en un array todos los correos de los stakeholders
                $correos = array();
                $stakeholders = array();
                $i = 0;
                foreach ($request['stakeholder_id'] as $stakeholder_id)
                {
                    $stakeholder = \Ermtool\Stakeholder::find($stakeholder_id);

                        //ALMACENAMOS EN EVALUATION_STAKEHOLDER (funcionalidad agregada 13-05-2016) PARA SABER A QUIENES SE ENVÍA LA ENCUESTA. OBS: Debemos ver que el usuario no exista
                        try
                        {
                            DB::table('evaluation_stakeholder')
                                ->insert([
                                    'stakeholder_id' => $stakeholder->id,
                                    'evaluation_id' => $request['encuesta_id'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    ]);

                            $correos[$i] = $stakeholder->mail;
                        }
                        catch(\Illuminate\Database\QueryException $e)
                        {
                            //creamos array de error si es que no existe
                            if (!isset($errors))
                            {
                                $errors = new ArrayObject();
                            }

                            if (Session::get('languaje') == 'en')
                            {
                                $errors->append("The poll was already sent to the user ".$stakeholder->name." ".$stakeholder->surnames.". It can't be send again.");
                            }
                            else
                            {
                                $errors->append('Ya se le envió la encuesta al usuario '.$stakeholder->name.' '.$stakeholder->surnames.'. No se puede enviar nuevamente.');
                            }
                        }

                        if (isset($errors))
                        {
                            if ($errors)
                            {
                                Session::flash('error',$errors);
                            }
                        }

                        $i += 1;
                }


                try
                {
                    Mail::send('envio_mail',$request->all(), 
                    function ($msj) use ($correos)
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $msj->subject('Risks assessments poll');
                        }
                        else
                        {
                            $msj->subject('Encuesta evaluación de Riesgos');
                        }
                        //Seleccionamos correos de stakeholders
                        $i = 0; //verifica si se debe ingresar to o cc
                        foreach ($correos as $correo)
                        {
                            if ($i == 0)
                            {
                                $msj->to($correo);
                                $i += 1;
                            }
                            else
                                $msj->cc($correo);
                        }
                    }
                );
                }
                catch (\Exception $e)
                {
                    enviarMailSoporte($e);
                    return view('errors.query',['e' => $e]);
                }
                

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Poll successfully sent');
                }
                else
                {
                    Session::flash('message','Encuesta enviada correctamente');
                }

                return Redirect::to('/evaluacion_agregadas');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function guardarEvaluacion(Request $request)
    {
        try
        {
            //primero verificamos si el rut ingresado corresponde a algún stakeholder
            //ACTUALIZACIÓN 24-01-17: Si es que la evaluación es manual, se debe verificar que el usuario exista en la tabla users
            //ACTUALIZACIÓN 24-08-17: Veremos si el id es mayor o igual al máximo permitido por INT
            if ($request['rut'] >= 2147483647)
            {
                //realizaremos división y utilizamos entero
                $id = $request['rut'] / 100;
                $request['rut'] = (int)$id;
            }
            if (isset($_POST['tipo']) && $_POST['tipo'] == 0)
            {
                $stakeholder = \Ermtool\User::find($request['rut']);
            }
            else
            {
                $stakeholder = \Ermtool\Stakeholder::find($request['rut']);
            }

            //Validación: Si la validación es pasada, el código continua
            //$this->validate($request, [
            //    'rut' => 'exists:stakeholders,id'
            //]);
            $org = \Ermtool\Organization::getOrganizationByOrgRisk($_POST['evaluation_risk_id'][0]);
            if ($stakeholder) //si es que el rut ingresado es correcto, procedemos a guardar evaluación
            {
                global $rut;
                global $name;

                $rut = $stakeholder->id;
                $name = $stakeholder->name.' '.$stakeholder->surnames;
                
                DB::transaction(function() {
                    $logger = $this->logger;
                    //verificamos si tipo = 0 (significaria que es evaluación manual por lo tanto se debe crear)
                    $i = 0;
                    $evaluation_risk = array(); //array que guarda los riesgos que se estarán almacenando
                    if ($_POST['tipo'] == 0)
                    {
                        //primero creamos evaluación manual

                        $eval_id = DB::table('evaluations')->insertGetId([
                                'name' => 'Evaluación Manual',
                                'consolidation' => 1,
                                'description' => 'Evaluación Manual',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        //insertamos riesgos de evaluación
                        foreach ($_POST['evaluation_risk_id'] as $risk_id) //OBS: Pueden haber riesgos de negocio y de proceso, por lo que se debe verificar ACT2 30-03-17: Da lo mismo ya que se guardará organization_risk
                        {
                            if (isset($_POST['proba_'.$risk_id.'_subprocess'])) //existe el id como riesgo de proceso
                            {
                                //inseratmos riesgo evaluation_risk
                                $evaluation_risk[$i] = DB::table('evaluation_risk')->insertGetId([
                                    'evaluation_id' => $eval_id,
                                    'organization_risk_id' => $risk_id,
                                    'avg_probability' => $_POST['proba_'.$risk_id.'_subprocess'],
                                    'avg_impact' => $_POST['criticidad_'.$risk_id.'_subprocess']
                                    ]);

                                //ACTUALIZACIÓN 19-07-2017: calculamos nuevo valor de riesgo residual
                                $controls = new Controles;

                                //ahora obtenemos risk_id y organization_id desde organization_risk_id
                                $var = DB::table('organization_risk')
                                        ->where('organization_risk.id','=',$risk_id)
                                        ->select('organization_id','risk_id')
                                        ->first();

                                //ahora calculamos riesgo residual para ese riesgo
                                $controls->calcResidualRisk($var->organization_id,$var->risk_id,date('Y'),date('m'),date('d'));

                                if (isset($_POST['comments_'.$risk_id]) && $_POST['comments_'.$risk_id] != '') //si es que se agregaron comentarios
                                {
                                    $comments = $_POST['comments_'.$risk_id];
                                }
                                else
                                {
                                    $comments = NULL;
                                }

                                //ACTUALIZACIÓN 04-12-17: Debemos verificar largo del rut (porque puede tener Rest)
                                if ($_POST['rut'] >= 2147483647)
                                {
                                    //realizaremos división y guardamos entero
                                    $rut = $_POST['rut'] / 100;
                                    $rut = (int)$id;
                                }
                                else
                                {
                                    $rut = $_POST['rut'];
                                }
                                //insertamos en evaluation_risk_stakeholder
                                DB::table('evaluation_risk_stakeholder')->insert([
                                    'evaluation_risk_id'=>$evaluation_risk[$i],
                                    'user_id'=>$rut,
                                    'probability'=>$_POST['proba_'.$risk_id.'_subprocess'],
                                    'impact'=>$_POST['criticidad_'.$risk_id.'_subprocess'],
                                    'comments' => $comments
                                ]);

                                $i += 1;  
                            }

                            if (isset($_POST['proba_'.$risk_id.'_objective'])) //existe el id como riesgo de negocio
                            {
                                //inseratmos riesgo de negocio en evaluation_risk
                                $evaluation_risk[$i] = DB::table('evaluation_risk')->insertGetId([
                                    'evaluation_id' => $eval_id,
                                    'organization_risk_id' => $risk_id,
                                    'avg_probability' => $_POST['proba_'.$risk_id.'_objective'],
                                    'avg_impact' => $_POST['criticidad_'.$risk_id.'_objective']
                                    ]);

                                //ACTUALIZACIÓN 19-07-2017: calculamos nuevo valor de riesgo residual
                                $controls = new Controles;

                                //ahora obtenemos risk_id y organization_id desde organization_risk_id
                                $var = DB::table('organization_risk')
                                        ->where('organization_risk.id','=',$risk_id)
                                        ->select('organization_id','risk_id')
                                        ->first();

                                //ahora calculamos riesgo residual para ese riesgo
                                $controls->calcResidualRisk($var->organization_id,$var->risk_id,date('Y'),date('m'),date('d'));

                                if (isset($_POST['comments_'.$risk_id]) && $_POST['comments_'.$risk_id] != '') //si es que se agregaron comentarios
                                {
                                    $comments = $_POST['comments_'.$risk_id];
                                }
                                else
                                {
                                    $comments = NULL;
                                }

                                //ACTUALIZACIÓN 04-12-17: Debemos verificar largo del rut (porque puede tener Rest)
                                if ($_POST['rut'] >= 2147483647)
                                {
                                    //realizaremos división y guardamos entero
                                    $rut = $_POST['rut'] / 100;
                                    $rut = (int)$id;
                                }
                                else
                                {
                                    $rut = $_POST['rut'];
                                }

                                //insertamos en evaluation_risk_stakeholder
                                DB::table('evaluation_risk_stakeholder')->insert([
                                'evaluation_risk_id'=>$evaluation_risk[$i],
                                'user_id'=>$rut,
                                'probability'=>$_POST['proba_'.$risk_id.'_objective'],
                                'impact'=>$_POST['criticidad_'.$risk_id.'_objective'],
                                'comments' => $comments,
                                ]);

                                $i += 1; 
                            }
                        }
                    }

                    else //no es evaluación manual
                    {
                        foreach ($_POST['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
                        {
                            if (isset($_POST['comments_'.$evaluation_risk]) && $_POST['comments_'.$evaluation_risk] != '') //si es que se agregaron comentarios
                            {
                                $comments = $_POST['comments_'.$evaluation_risk];
                            }
                            else
                            {
                                $comments = NULL;
                            }

                            //ACTUALIZACIÓN 04-12-17: Debemos verificar largo del rut (porque puede tener Rest)
                            if ($_POST['rut'] >= 2147483647)
                            {
                                //realizaremos división y guardamos entero
                                $rut = $_POST['rut'] / 100;
                                $rut = (int)$id;
                            }
                            else
                            {
                                $rut = $_POST['rut'];
                            }

                            DB::table('evaluation_risk_stakeholder')->insert([
                                    'evaluation_risk_id' => $evaluation_risk,
                                    'stakeholder_id' => $rut,
                                    'probability'=> $_POST['proba_'.$evaluation_risk],
                                    'impact' => $_POST['criticidad_'.$evaluation_risk],
                                    'comments' => $comments
                                ]);  
                
                                //actualizamos promedio de probabilidad e impacto en tabla evaluation_risk
                                
                                //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
                                $prom_proba = DB::table('evaluation_risk')
                                        ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                        ->where('evaluation_risk.id',$evaluation_risk)
                                        ->avg('probability');

                                $prom_impacto = DB::table('evaluation_risk')
                                        ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                        ->where('evaluation_risk.id',$evaluation_risk)
                                        ->avg('impact');

                                DB::table('evaluation_risk')
                                    ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                    ->where('evaluation_risk.id',$evaluation_risk)
                                    ->update(['evaluation_risk.avg_probability' => $prom_proba,
                                            'evaluation_risk.avg_impact' => $prom_impacto
                                            ]);

                        }
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Answers successfully sent');
                    }
                    else
                    {
                        Session::flash('message','Respuestas enviadas correctamente');
                    }

                    $logger->info('El usuario '.$GLOBALS['name']. ', Rut: '.$GLOBALS['rut'].', ha realizado una evaluación de riesgos con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                //ACT 09-05-17: ENVIAMOS ÚLTIMA EVALUACIÓN
                $evals = $this->heatmapLastEvaluation();

                return view('evaluacion.encuestaresp',['nombre'=>$evals['nombre'],'descripcion'=>$evals['descripcion'],'riesgos'=>$evals['riesgos'],'prom_proba'=>$evals['prom_proba'],'prom_criticidad'=>$evals['prom_criticidad'],'org' => $evals['org']]);
                //print_r($_POST);
            }
            else //no se encontró el rut ingresado
            {
                if ($_POST['tipo'] == 0)
                {
                    $i = 0;
                    //volvemos a obtener riesgos para devolver vista correcta
                    foreach ($_POST['evaluation_risk_id'] as $risk)
                    {
                        if (isset($_POST['proba_'.$risk.'_subprocess'])) //existe el id como riesgo de proceso
                        {
                            //ACTUALIZACIÓN 30-03: organization_risk
                            $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                            //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                            foreach ($risk1 as $r)
                            {
                                //obtenemos subprocesos relacionados
                                $subprocesses = array();

                                $subs = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('risk_subprocess.risk_id','=',$r->id)
                                        ->select('subprocesses.name','subprocesses.description')
                                        ->get();
                                $j = 0;
                                foreach ($subs as $sub)
                                {
                                    $subprocesses[$j] = $sub;
                                    $j+=1;
                                }
                                $riesgos[$i] = array('type' => 'subprocess',
                                                    'org_risk_id' => $risk,
                                                    'risk_name' => $r->risk_name,
                                                    'description' => $r->description,
                                                    'subobj' => $subprocesses);
                                $i += 1;
                            }
                        }

                        if (isset($_POST['proba_'.$risk.'_objective'])) //existe el id como riesgo de negocio
                        {
                            //obtenemos nombre de riesgo y organizacion
                            $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                            foreach ($risk1 as $r)
                            {
                                //obtenemos objetivos relacionados
                                $objectives = array();

                                $objs = DB::table('objectives')
                                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                                        ->where('objective_risk.risk_id','=',$r->id)
                                        ->select('objectives.name','objectives.description')
                                        ->get();
                                $j = 0;
                                foreach ($objs as $obj)
                                {
                                    $objectives[$j] = $obj;
                                    $j+=1;
                                }
                                $riesgos[$i] = array('type' => 'objective',
                                                    'org_risk_id' => $risk,
                                                    'risk_name' => $r->risk_name,
                                                    'description' => $r->description,
                                                    'subobj' => $objectives);
                                $i += 1;
                            }
                        }
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message',"The entered Id is not in our database");

                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);
                        $org_name = $org->name;

                        return view('en.evaluacion.encuesta',['encuesta'=>'Manual Evaluation','riesgos'=>$riesgos,'tipo'=>0,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>0,'org_name'=>$org_name])->withInput(Input::all());
                    }
                    else
                    {
                        Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1); //2 es inglés
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);
                        $org_name = $org->name;

                        return view('evaluacion.encuesta',['encuesta'=>'Evaluación Manual','riesgos'=>$riesgos,'tipo'=>0,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>0,'org_name'=>$org_name])->withInput(Input::all()); //no funcion withInput
                    }
                }
                else
                {
                    return Redirect::to('evaluacion.encuesta.'.$request["evaluation_id"])->withInput();
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function listHeatmap()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //obtenemos encuestas distintas a las que corresponden a una evaluación manual
                $encuestas = \Ermtool\Evaluation::where('description','<>','NULL')->lists('name','id');
                $organizaciones = \Ermtool\Organization::where('status',0)->lists('name','id');
                //ACTUALIZACIÓN 02-03-17: Agregamos filtro de categorías de riesgos (todas las categorías)
                //ACTUALIZACIÓN 20-07-17: Sólo mostraremos categorías primarias
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones,'categories' => $categories]);
                }
                else
                {
                    return view('reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones,'categories' => $categories]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarHeatmap(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //print_r($_POST);
                //Nombre y descripción de la encuesta u organización
                $nombre = "";
                $descripcion = "";
                $exposicion = array();
                if (isset($_GET['kind2_1']))
                {
                    //asignamos niveles estándares de exposición (para dibujar en matriz)
                    for ($cont=100;$cont>0;$cont--)
                    {
                        //variable de resta (sin porcentaje)
                        $cont2 = $cont / 100;
                        $exposicion[$cont] = 1 - $cont2;
                    }
                } 

                $riesgo_temp = array();
                $riesgos = array();
                $i = 0;

                $ano = $_GET['ano'];

                if ($_GET['mes'] == NULL)
                {
                    $mes = "12";
                }
                else
                {
                    $mes = $_GET['mes'];

                    //ACT 11-04-17: formateamos mes en caso de que no se haya agregado un cero antes
                    if ((int)$mes < 10)
                    {
                        if (strlen($mes) < 2) //falta un cero
                        {
                            $mes = '0'.$mes;
                        }
                    }
                }

                //ACT 11-04-17: Formateamos $dia mes escogido
                //ACT 04-07-18: Agregamos posibilidad de ingresar día
                if (isset($_GET['dia']) && $_GET['dia'] != '')
                {
                    $dia = $_GET['dia'];

                    if ((int)$dia < 10)
                    {
                        if (strlen($dia) < 2) //falta un cero
                        {
                            $dia = '0'.$dia;
                        }
                    }
                }
                else //no se agregó día
                {
                    if ($mes == '01' || $mes == '03' || $mes == '05' || $mes == '07' || $mes == '08' || $mes == '10') //son 31 días
                    {
                        $dia = '31';
                    }
                    else if ($mes == '02') //febrero, sólo 28 días
                    {
                        $dia = '28';
                    }
                    else
                    {
                        $dia = '30';
                    }
                }

                //ACTUALIZACIÓN 02-03-2017: Filtro de categoría
                if (isset($_GET['risk_category_id']) && $_GET['risk_category_id'] != '')
                {
                    $category = $_GET['risk_category_id'];
                }
                else
                {
                    $category = NULL;
                }

                //ACTUALIZACIÓN 20-07-17: Filtro para subcategoría (además, si se selecciona una categoría también debe buscar los riesgos de su subcategoría)
                if (isset($_GET['risk_subcategory_id']) && $_GET['risk_subcategory_id'] != '')
                {
                    $subcategory = $_GET['risk_subcategory_id'];
                }
                else
                {
                    $subcategory = NULL;
                }

                //ACTUALIZACIÓN 02-03-2017: Vemos si se desea ver riesgos solo de org o tambien de org dependientes
                if (isset($_GET['sub_organizations']))
                {
                    $subs = TRUE;
                }
                else
                {
                    $subs = FALSE;
                }

                //obtenemos nombre y descripción de organización
                $datos = DB::table('organizations')->where('id',$_GET['organization_id'])->select('name','description')->get();

                foreach ($datos as $datos)
                {
                        $nombre = $datos->name;
                        $descripcion = $datos->description;
                }
                if ($_GET['kind'] == 0) //evaluaciones de riesgos de procesos
                {
                    //---- consulta multiples join para obtener los subprocesos evaluados relacionados a la organización ----//
                    //para riesgos inherente 
                    $evaluations = \Ermtool\Evaluation::getEvaluationRiskSubprocess($_GET['organization_id'],$category,$subcategory,$subs,$ano,$mes,$dia);  
                }
                else if ($_GET['kind'] == 1) //evaluaciones de riesgos de negocio
                {

                    $evaluations = \Ermtool\Evaluation::getEvaluationObjectiveRisk($_GET['organization_id'],$category,$subcategory,$subs,$ano,$mes,$dia); 
                }

                if (isset($evaluations) && $evaluations != null && !empty($evaluations))
                {
                    foreach ($evaluations as $evaluation)
                    {
                        //unseteamos variable de proba_impacto_ctrl para que no se repita
                        unset($proba_ctrl1);
                        unset($impact_ctrl1);
                        unset($control1);
                        unset($proba_ctrl2);
                        unset($impact_ctrl2);
                        unset($control2);
                        unset($proba_ctrl3);
                        unset($impact_ctrl3);
                        unset($control3);
                        unset($prom_proba_in);
                        unset($prom_impacto_in);
                            /*
                                $updated_at_in = \Ermtool\Evaluation::getMaxUpdatedAt($evaluation->risk_id,$ano,$mes,$dia);

                                //$updated_at_in = str_replace('-','',$updated_at_in);

                                //ACT 05-03-18: Ahora existirá kind2_1. kind2_2, kind2_3 (según los mapas que existan)
                                if (isset($_GET['kind2_1'])) //Mapa % de contribución
                                {
                                    $created_at_ctrl1 = \Ermtool\ControlledRisk::getMaxCreatedAt($evaluation->risk_id,$ano,$mes,$dia);

                                    //$updated_at_ctrl = str_replace('-','',$updated_at_ctrl);
                                }

                                if (isset($_GET['kind2_2'])) //Mapa Evaluación de controles
                                {
                                    $created_at_ctrl2 = \Ermtool\ResidualRisk::getMaxCreatedAt($evaluation->risk_id,$ano,$mes,$dia);

                                    //$updated_at_ctrl = str_replace('-','',$updated_at_ctrl);
                                }

                                if (isset($_GET['kind2_3'])) //Mapa residual manual
                                {
                                    $created_at_ctrl3 = \Ermtool\ControlledRiskManual::getMaxCreatedAt($evaluation->risk_id,$ano,$mes,$dia);

                                    //$updated_at_ctrl = str_replace('-','',$updated_at_ctrl);
                                }*/

                        //obtenemos promedio de probabilidad e impacto
                        $proba_impacto_in = \Ermtool\Evaluation::getProbaImpact($evaluation->risk_id,$ano,$mes,$dia);

                        if (isset($_GET['kind2_1'])) //Mapa % de contribución
                        {
                            //ACTUALIZACIÓN 01-12: Obtenemos valor de riesgo controlado de controlled_risk_criteria, según la evaluación de controlled_risk
                            $eval = \Ermtool\ControlledRisk::getResults($evaluation->risk_id,$ano,$mes,$dia);

                            //calculamos severidad y exposición al riesgo (dejaremos los nombres proba_ctrl para exposición, e impacto_ctrl para severidad para no modificar mucho la vista actual)
                            if (!empty($proba_impacto_in) && !empty($eval))
                            {
                                $impact_ctrl1 = $proba_impacto_in->avg_probability * $proba_impacto_in->avg_impact;
                                //obtenemos exposición (1-X%) dividiendo el valor residual por la severidad
                                $proba_ctrl1 = $eval->results / $impact_ctrl1;

                            }
                            else
                            {
                                $impact_ctrl1 = NULL;
                                $proba_ctrl1 = NULL;
                            }

                            //prom_proba_ctrl para controlado (si es que hay)
                            if ($proba_ctrl1 != NULL && $impact_ctrl1 != NULL)
                            {
                                $control1 = 1;
                            }
                            //ACTUALIZACIÓN 05-05-17: SI ES QUE NO HAY EVALUACIÓN DE RIESGO CONTROLADO (ES DECIR, NO HAY CONTROL). MOSTRAREMOS EL RIESGO INHERENTE COMO RESIDUAL
                            else
                            {
                                //agregamos variable para definir que el riesgo no está siendo controlado
                                $control1 = 0;
                            }
                        }
                        else
                        {
                            $impact_ctrl1 = NULL;
                            $proba_ctrl1 = NULL;
                            $control1 = NULL;
                        }

                        if (isset($_GET['kind2_2'])) //Mapa de evaluación de controles
                        {
                            $eval = \Ermtool\ResidualRisk::getProbaImpact($evaluation->risk_id,$ano,$mes,$dia);

                            //calculamos severidad y exposición al riesgo (dejaremos los nombres proba_ctrl para exposición, e impacto_ctrl para severidad para no modificar mucho la vista actual)
                            if (!empty($proba_impacto_in) && !empty($eval))
                            {
                                $impact_ctrl2 = $eval->impact;
                                //obtenemos exposición (1-X%) dividiendo el valor residual por la severidad
                                $proba_ctrl2 = $eval->probability;
                            }
                            else
                            {
                                $impact_ctrl2 = NULL;
                                $proba_ctrl2 = NULL;
                            }

                            //prom_proba_ctrl para controlado (si es que hay)
                            if (isset($proba_ctrl2) && isset($impact_ctrl2) && $proba_ctrl2 != NULL && $impact_ctrl2 != NULL)
                            {
                                $control2 = 1;
                            }
                                //ACTUALIZACIÓN 05-05-17: SI ES QUE NO HAY EVALUACIÓN DE RIESGO CONTROLADO (ES DECIR, NO HAY CONTROL). MOSTRAREMOS EL RIESGO INHERENTE COMO RESIDUAL
                            else
                            {
                                //agregamos variable para definir que el riesgo no está siendo controlado
                                $control2 = 0;
                            }
                        }
                        else
                        {
                            $impact_ctrl2 = NULL;
                            $proba_ctrl2 = NULL;
                            $control2 = NULL;
                        }

                        if (isset($_GET['kind2_3'])) //Mapa de evaluación residual manual
                        {
                            $eval = \Ermtool\ControlledRiskManual::getProbaImpact($evaluation->risk_id,$ano,$mes,$dia);

                            //calculamos severidad y exposición al riesgo (dejaremos los nombres proba_ctrl para exposición, e impacto_ctrl para severidad para no modificar mucho la vista actual)
                            if (!empty($proba_impacto_in) && !empty($eval))
                            {
                                $impact_ctrl3 = $eval->impact;
                                //obtenemos exposición (1-X%) dividiendo el valor residual por la severidad
                                $proba_ctrl3 = $eval->probability;
                            }
                            else
                            {
                                $impact_ctrl3 = NULL;
                                $proba_ctrl3 = NULL;
                            }

                            //prom_proba_ctrl para controlado (si es que hay)
                            if (isset($proba_ctrl3) && isset($impact_ctrl3) && $proba_ctrl3 != NULL && $impact_ctrl3 != NULL)
                            {
                                $control3 = 1;
                            }
                                //ACTUALIZACIÓN 05-05-17: SI ES QUE NO HAY EVALUACIÓN DE RIESGO CONTROLADO (ES DECIR, NO HAY CONTROL). MOSTRAREMOS EL RIESGO INHERENTE COMO RESIDUAL
                            else
                            {
                                //agregamos variable para definir que el riesgo no está siendo controlado
                                $control3 = 0;
                            }
                        }
                        else
                        {
                            $impact_ctrl3 = NULL;
                            $proba_ctrl3 = NULL;
                            $control3 = NULL;
                        }

                        if (!empty($proba_impacto_in))
                        {
                                //guardamos proba en $prom_proba
                                $prom_proba_in = $proba_impacto_in->avg_probability;
                                $prom_impacto_in = $proba_impacto_in->avg_impact;
                        }
                        else
                        {
                            $prom_proba_in = NULL;
                            $prom_impacto_in = NULL;
                        }

                            

                        //ACTUALIZACIÓN 25-07: OBTENEMOS DATOS DEL RIESGO Y LOS POSIBLES RIESGOS ASOCIADOS
                        $riesgo_temp = \Ermtool\Risk::find($evaluation->risk);
                        //$objectives = $riesgo_temp->objectives ----> NO SIRVE MUESTRA OBJ. DE OTRAS ORGANIZACIONES
                        if ($_GET['kind'] == 0) //riesgos de proceso
                        {
                            $subobj = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($riesgo_temp->id,$_GET['organization_id']);

                            //ACT 11-01-18: obtenemos subprocesos de filiales (para cualquier tipo de nivel)
                            $subobj2 = array(); //array temporal para guardar subprocesos

                            if (isset($_GET['sub_organizations'])) //verificamos que se haya seleccionado ver filiales
                            {
                                $orgs1 = \Ermtool\Organization::where('organization_id','=',$_GET['org'])->select('id')->get();
                                $orgs = array();

                                if (!empty($orgs1)) //Hay suborganizaciones
                                {
                                    while (!empty($orgs1)) //Ciclo recursiva para tomar todos los niveles
                                    {
                                        foreach ($orgs1 as $org)
                                        {
                                            $subs = getSubprocessesFromOrgRisk($riesgo_temp->id,$org->id);

                                            foreach ($subs as $s)
                                            {
                                                array_push($subobj2,$s);
                                            }
                                        }

                                        $orgs1 = \Ermtool\Organization::where('organization_id','=',$org->id)->select('id')->get();
                                    } 
                                }
                                        
                                $subobj2 = array_unique($subobj2,SORT_REGULAR);  
                                $subobj = array_merge($subobj,$subobj2);
                                $subobj = array_unique($subobj,SORT_REGULAR);
                            }
                        }
                        else if ($_GET['kind'] == 1) //riesgos de negocio
                        {
                            $subobj = \Ermtool\Objective::getObjectivesFromOrgRisk($riesgo_temp->id,$_GET['organization_id']);
                        }
                                
                        //ACT 12-12-17: Eliminamos saltos
                        $description = eliminarSaltos($riesgo_temp->description);
                        $name = eliminarSaltos($riesgo_temp->name);

                        $riesgos[$i] = [
                            'name' => $name,
                            'subobj' => $subobj,
                            'description' => $description,
                            'proba_in' => $prom_proba_in,
                            'impact_in' => $prom_impacto_in,
                            'impact_ctrl1' => $impact_ctrl1,
                            'proba_ctrl1' => $proba_ctrl1,
                            'control1' => $control1,
                            'impact_ctrl2' => $impact_ctrl2,
                            'proba_ctrl2' => $proba_ctrl2,
                            'control2' => $control2,
                            'impact_ctrl3' => $impact_ctrl3,
                            'proba_ctrl3' => $proba_ctrl3,
                            'control3' => $control3];

                        $i += 1;
                    }   
                }        

                //ACT 10-01-18: Si es que son muchos los riesgos, los juntamos. Para esto, realizamos contador de los riesgos para cada uno de los cuadrantes (primero en mapa inherente)
                $cont = array();
                for($i=1; $i <= 5; $i++)
                {
                    for ($j=1; $j <= 5; $j++)
                    {
                        $cont[$i][$j] = 0;
                    }
                }
                
                foreach ($riesgos as $r)
                {
                    $cont[intval($r['impact_in'])][intval($r['proba_in'])] += 1;
                }

                //ACT 11-01-18: Ahora realizamos la actualización para mapa de riesgos residuales
                $cont_ctrl = array();
                if (isset($_GET['kind2_1'])) //Mapa de % de Contribución de acciones mitigante
                {
                    for ($i=1; $i <= 25; $i++)
                    {
                        $cont_ctrl[$i][1] = 0;
                        $cont_ctrl[$i][2] = 0;
                        $cont_ctrl[$i][3] = 0;
                        $cont_ctrl[$i][4] = 0;
                    }
                    for ($i=1; $i <= 25; $i++) //ciclo de severidad
                    {
                        foreach ($riesgos as $r)
                        {
                            if (intval($r['impact_ctrl1']) == $i) //si es que la severidad del riesgo es igual a la del ciclo
                            {
                                if ($r['proba_ctrl1'] <= 0.05 && $r['proba_ctrl1'] >= 0)
                                {
                                    $cont_ctrl[$i][1] += 1;
                                }
                                else if ($r['proba_ctrl1'] <= 0.15 && $r['proba_ctrl1'] > 0.05)
                                {
                                    $cont_ctrl[$i][2] += 1;
                                }
                                else if ($r['proba_ctrl1'] <= 0.5 && $r['proba_ctrl1'] > 0.15)
                                {
                                    $cont_ctrl[$i][3] += 1;
                                }
                                else if ($r['proba_ctrl1'] <= 1 && $r['proba_ctrl1'] > 0.5)
                                {
                                    $cont_ctrl[$i][4] += 1;
                                }
                            }
                        }
                    }    
                }

                $cont2 = array();
                if (isset($_GET['kind2_2'])) //Mapa de Riesgos por evaluación de controles
                {   
                    for($i=1; $i <= 5; $i++)
                    {
                        for ($j=1; $j <= 5; $j++)
                        {
                            $cont2[$i][$j] = 0;
                        }
                    }
                    
                    foreach ($riesgos as $r)
                    {
                        if ($r['impact_ctrl2'] != NULL && $r['proba_ctrl2'] != NULL)
                        {
                            $cont2[intval($r['impact_ctrl2'])][intval($r['proba_ctrl2'])] += 1;
                        }
                        
                    }
                }
                $cont3 = array();
                if (isset($_GET['kind2_3'])) //Mapa de Riesgo Residual Manual
                {
                    for($i=1; $i <= 5; $i++)
                    {
                        for ($j=1; $j <= 5; $j++)
                        {
                            $cont3[$i][$j] = 0;
                        }
                    }
                    
                    foreach ($riesgos as $r)
                    {
                        if ($r['impact_ctrl3'] != NULL && $r['proba_ctrl3'] != NULL)
                        {
                            $cont3[intval($r['impact_ctrl3'])][intval($r['proba_ctrl3'])] += 1;
                        }
                    }
                }
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.heatmap',[
                        'nombre'=>$nombre,
                        'descripcion'=>$descripcion,
                        'riesgos'=>$riesgos,
                        'kind' => $_GET['kind'],
                        'kind2_1' => isset($_GET['kind2_1']) ? 1 : NULL,
                        'kind2_2' => isset($_GET['kind2_2']) ? 1 : NULL,
                        'kind2_3' => isset($_GET['kind2_3']) ? 1 : NULL,
                        'exposicion' => $exposicion,
                        'cont2' => $cont,
                        'cont_ctrl' => $cont_ctrl]);
                }
                else
                {
                    return view('reportes.heatmap',[
                        'nombre'=>$nombre,
                        'descripcion'=>$descripcion,
                        'riesgos'=>$riesgos,
                        'kind' => $_GET['kind'],
                        'kind2_1' => isset($_GET['kind2_1']) ? 1 : NULL,
                        'kind2_2' => isset($_GET['kind2_2']) ? 1 : NULL,
                        'kind2_3' => isset($_GET['kind2_3']) ? 1 : NULL,
                        'exposicion' => $exposicion,
                        'cont2' => $cont,
                        'cont3' => $cont2,
                        'cont4' => $cont3,
                        'cont_ctrl' => $cont_ctrl]);
                } 
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function evaluacionManual()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //obtenemos organizaciones
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.evaluacion.evaluacion_manual',['organizations' => $organizations,'categories' => $categories]);
                }
                else
                {
                    return view('evaluacion.evaluacion_manual',['organizations' => $organizations,'categories' => $categories]);
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
        //
    }

    /*
    Función que retorna los riesgos de una encuesta de evaluación que serán consolidados. 
        OBS: Sólo los riesgos consolidados son mostrados en el mapa de calor
    */
    public function getRiesgosConsolidar($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $evaluations_risks = array(); //guardaremos todos los riesgos con sus datos y evaluaciones
                $i = 0;
                //----obtenemos todos los riesgos asociados a la encuesta----//
                //primero obtenemos riesgos de negocio (de existir)
                //ACTUALIZACIÓN 31-03-17: Solo organization_risk
                $risks = DB::table('evaluation_risk')
                            ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                            ->join('risks','risks.id','=','organization_risk.risk_id')
                            ->where('evaluation_risk.evaluation_id','=',$id)
                            ->select('evaluation_risk.id as id','risks.name as risk_name','risks.description','evaluation_risk.avg_impact as impact','evaluation_risk.avg_probability as probability')
                            ->get();

                if ($risks) //si es que hay
                {
                    foreach ($risks as $risk)
                    {
                        $evaluations_risks[$i] = array(
                            'id' => $risk->id,
                            'risk_name' => $risk->risk_name,
                            'description' => $risk->description,
                            'impact' => $risk->impact,
                            'probability' => $risk->probability
                            );

                        $i += 1;
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.evaluacion.consolidar',['evaluations_risks' => $evaluations_risks,'evaluation_id' => $id]);
                }
                else
                {
                    return view('evaluacion.consolidar',['evaluations_risks' => $evaluations_risks,'evaluation_id' => $id]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //Función que consolida encuesta (cambia estado consolidation = 1)
    public function consolidar(Request $request)
    {
        try
        {
            //print_r($_POST);
            DB::transaction(function() {

                //primero obtenemos id de riesgos (evaluation_risk) de la encuesta
                $evaluation_risks = DB::table('evaluation_risk')
                                        ->where('evaluation_risk.evaluation_id',$_POST['evaluation_id'])
                                        ->select('evaluation_risk.id as id','evaluation_risk.organization_risk_id')
                                        ->get();

                foreach ($evaluation_risks as $evaluation_risk)
                {
                    //actualizaremos probabilidad e impacto para cada evaluation_risk
                    DB::table('evaluation_risk')
                        ->where('evaluation_risk.id',$evaluation_risk->id)
                        ->update([
                            'avg_probability' => $_POST['probability_'.$evaluation_risk->id],
                            'avg_impact' => $_POST['impact_'.$evaluation_risk->id]
                            ]);

                    //actualizamos atributo consolidation en tabla evaluations
                    \Ermtool\Evaluation::where('id',$_POST['evaluation_id'])
                                            ->update(['consolidation' => 1]);

                    //ACTUALIZACIÓN 19-07-2017: calculamos nuevo valor de riesgo residual
                    $controls = new Controles;

                    //ahora obtenemos risk_id y organization_id desde organization_risk_id
                    $var = DB::table('organization_risk')
                            ->where('organization_risk.id','=',$evaluation_risk->organization_risk_id)
                            ->select('organization_id','risk_id')
                            ->first();

                    //ahora calculamos riesgo residual para ese riesgo
                    $controls->calcResidualRisk($var->organization_id,$var->risk_id,date('Y'),date('m'),date('d'));
                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Poll successfully consolidated');
                }
                else
                {
                    Session::flash('message','Encuesta de evaluación consolidada correctamente');
                }

            });

            return Redirect::to('/evaluacion_agregadas');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
        
    }

    //eliminará una encuesta (que no sea manual), en caso de que esta no haya sido respondida por nadie
    public function delete($id1)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                global $id; 
                $id = $id1;

                DB::transaction(function() {
                    $logger = $this->logger;
                    //intentaremos obtener respuestas en evaluation_risk
                    $val = DB::table('evaluation_risk')
                                ->where('evaluation_id','=',$GLOBALS['id'])
                                ->select('id','avg_probability','avg_impact')
                                ->get();

                    if (!empty($val)) //si no existe $val la encuesta ni siquiera tiene riesgos, por lo que se puede eliminar
                    {
                        $cont = 0; //contador de valores distintos de cero
                        foreach ($val as $v)
                        {
                            if ($v->avg_probability != NULL || $v->avg_impact != NULL)
                            {
                                $cont += 1;
                            }
                        }

                        if ($cont == 0) //no hay evaluaciones
                        {
                            try
                            {
                                //eliminamos riesgos de evaluation_risk
                                DB::table('evaluation_risk')
                                    ->where('evaluation_id','=',$GLOBALS['id'])
                                    ->delete();

                                //eliminamos de evaluation_stakeholder (si es que hay)
                                DB::table('evaluation_stakeholder')
                                    ->where('evaluation_id','=',$GLOBALS['id'])
                                    ->delete();

                                //eliminamos evaluación
                                DB::table('evaluations')
                                    ->where('id','=',$GLOBALS['id'])
                                    ->delete();

                                //Session::flash('message','La encuesta se eliminó satisfactoriamente');
                                echo 0;
                            }
                            catch(\Illuminate\Database\QueryException $e)
                            {
                                echo 1;
                            }
                        }
                        else
                        {
                            //Session::flash('error','La encuesta no se puede eliminar ya que posee evaluaciones');
                            echo 1;
                        }

                        //por ahora no verificaremos nada en evaluation_risk_stakeholder, ya que si no hay valores de avg_impact y avg_probability no deberían haber evaluaciones de usuarios
                    }
                    else //eliminamos
                    {
                        try
                        {
                            $name = DB::table('evaluations')->where('id',$GLOBALS['id']);
                            //eliminamos de evaluation_stakeholder (si es que hay)
                            DB::table('evaluation_stakeholder')
                                ->where('evaluation_id','=',$GLOBALS['id'])
                                ->delete();

                            //eliminamos evaluación
                            DB::table('evaluations')
                                ->where('id','=',$GLOBALS['id'])
                                ->delete();

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la encuesta de riesgos con Id: '.$GLOBALS['id'].' llamada: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                            echo 0;
                        }
                        catch(\Illuminate\Database\QueryException $e)
                        {
                            echo 1;
                        }
                        //Session::flash('message','La encuesta se eliminó satisfactoriamente');
                    }
                });
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function verificadorUserEncuesta($id)
    {
        try
        {
            $encuesta = \Ermtool\Evaluation::find($id);

            if (Session::get('languaje') == 'en')
            {
                return view('en.evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
            }
            else
            {
                return view('evaluacion.verificar_encuesta',['encuesta'=>$encuesta]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
        
    }

    public function verRespuestas($eval_id,$rut)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $encuesta = \Ermtool\Evaluation::find($eval_id)->value('name');

                $user = DB::table('stakeholders')
                            ->where('id',$rut)
                            ->select('name','surnames')
                            ->first();

                $res = array();

                $res = $this->getEvalRisks($eval_id,$rut);

                if (Session::get('languaje') == 'en')
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);

                    return view('en.evaluacion.respuestas',['riesgos'=>$res['riesgos'],'user_answers'=>$res['user_answers'],'eval_id'=>$eval_id,'rut'=>$rut,'encuesta'=>$encuesta,'user'=>$user,'tipos_impacto'=>$tipos_impacto,'tipos_proba'=>$tipos_proba]);
                }
                else
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1); //2 es inglés
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                    return view('evaluacion.respuestas',['riesgos'=>$res['riesgos'],'user_answers'=>$res['user_answers'],'eval_id'=>$eval_id,'rut'=>$rut,'encuesta'=>$encuesta,'user'=>$user,'tipos_impacto'=>$tipos_impacto,'tipos_proba'=>$tipos_proba]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function updateEvaluacion(Request $request)
    {
        try
        {
            DB::transaction(function() {
                $logger = $this->logger;
                //verificamos si tipo = 0 (significaria que es evaluación manual por lo tanto se debe crear)
                $i = 0;
                $evaluation_risk = array(); //array que guarda los riesgos que se estarán actualizando
                //obtenemos stakeholder para logger
                $stakeholder = \Ermtool\Stakeholder::find($_POST['rut']);

                global $rut;
                global $name;

                $rut = $stakeholder->id;
                $name = $stakeholder->name.' '.$stakeholder->surnames;

                foreach ($_POST['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
                {
                            if (isset($_POST['comments_'.$evaluation_risk]) && $_POST['comments_'.$evaluation_risk] != '') //si es que se agregaron comentarios
                            {
                                $comments = $_POST['comments_'.$evaluation_risk];
                            }
                            else
                            {
                                $comments = NULL;
                            }
                            DB::table('evaluation_risk_stakeholder')
                                ->where('evaluation_risk_id','=',$evaluation_risk)
                                ->where('stakeholder_id','=',$_POST['rut'])
                                ->update([
                                'probability'=> $_POST['proba_'.$evaluation_risk],
                                'impact' => $_POST['criticidad_'.$evaluation_risk],
                                'comments' => $comments 
                                ]);  
            
                            //actualizamos promedio de probabilidad e impacto en tabla evaluation_risk
                            
                            //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
                            $prom_proba = DB::table('evaluation_risk')
                                    ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                    ->where('evaluation_risk.id','=',$evaluation_risk)
                                    ->avg('probability');

                            $prom_impacto = DB::table('evaluation_risk')
                                    ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                    ->where('evaluation_risk.id','=',$evaluation_risk)
                                    ->avg('impact');

                            DB::table('evaluation_risk')
                                ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                                ->where('evaluation_risk.id','=',$evaluation_risk)
                                ->update(['evaluation_risk.avg_probability' => $prom_proba,
                                        'evaluation_risk.avg_impact' => $prom_impacto
                                        ]);

                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Answers successfully updated');
                }
                else
                {
                    Session::flash('message','Respuestas actualizadas correctamente');
                }

                $logger->info('El usuario '.$GLOBALS['name']. ', Rut: '.$GLOBALS['rut'].', ha realizado una evaluación de riesgos con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });
            
            return view('evaluacion.encuestaresp');
            //print_r($_POST);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 26-03-18: heatmap por categorías de riesgos
    public function heatmapForCategories()
    {
        //obtenemos último nivel de categorías
        $categories = \Ermtool\Risk_category::getSubcategories();
        $cats = array(); //donde enviaremos las categorías con sus evaluaciones
        $i = 0; //contador de categorías
        foreach ($categories as $cat)
        {
            //creamos variables para promedio, tanto de encuestas como por evaluación manual (y tanto en impacto como en probabilidad)
            $prom_eval_i = 0;
            $prom_eval_p = 0;
            $prom_eval_manual_i = 0;
            $prom_eval_manual_p = 0;

            $cont_eval = 0; //contador de riesgos asociados a la categoría (que tienen evaluación)
            $cont_manual = 0; //contador de riesgos asociados a la categoría (que tienen eval. manual)
            //obtenemos riesgos asociados a categoría
            $risks = \Ermtool\Risk::getRisksFromCategory($cat['id']);

            $risksdata = array(); //info de los riesgos asociados a la categoría
            $j = 0;

            foreach ($risks as $r)
            {
                //primero obtenemos org_risk asociadas (es decir, en qué organizaciones se encuentra el riesgo)
                $org_risk = \Ermtool\Risk::getOrgRisks($r->id);
                foreach ($org_risk as $or)
                {
                    //obtenemos datos del riesgo
                    $org = \Ermtool\Organization::name($or->organization_id);
                    $risk_name = \Ermtool\Risk::name($r->id);
                    $risk_description = \Ermtool\Risk::description($r->id);
                    //obtenemos últimas evaluaciones del riesgo, primero realizadas a través de evaluación
                    $eval = \Ermtool\Evaluation::getLastEvaluation($or->id,2);

                    if ($eval && !empty($eval)) //si es que el riesgo tiene evaluación
                    {
                        $cont_eval += 1;
                        //hacemos sumatoria de impacto y probabilidad
                        $prom_eval_i += $eval->avg_impact;
                        $prom_eval_p += $eval->avg_probability;
                    }

                    //lo mismo para evaluaciones manuales
                    $evalm = \Ermtool\Evaluation::getLastEvaluation($or->id,3);

                    if ($evalm && !empty($evalm)) //si es que el riesgo tiene evaluación
                    {
                        $cont_manual += 1;
                        //hacemos sumatoria de impacto y probabilidad
                        $prom_eval_manual_i += $evalm->avg_impact;
                        $prom_eval_manual_p += $evalm->avg_probability;
                    }

                    $risksdata[$j] = [
                        'organization' => $org,
                        'risk_name' => $risk_name,
                        'risk_description' => $risk_description
                    ];

                    $j += 1;
                }       
            }

            //obtenemos promedios para probabilidades e impactos
            if ($cont_eval != 0) //verificamos que hayan evaluaciones dentro de esta categoría
            {
                $prom_eval_i = $prom_eval_i / $cont_eval;
                $prom_eval_p = $prom_eval_p / $cont_eval;
            }
            else
            {
                $prom_eval_i = 0;
                $prom_eval_p = 0;
            }
            
            if ($cont_manual != 0) //verificamos que hayan evaluaciones manuales dentro de esta categoría
            {
                $prom_eval_manual_i = $prom_eval_manual_i / $cont_manual;
                $prom_eval_manual_p = $prom_eval_manual_p / $cont_manual;
            }
            else
            {
                $prom_eval_manual_p = 0;
                $prom_eval_manual_i = 0;
            }
            
            $cats[$i] = [
                'id' => $cat['id'],
                'name' => $cat['name'],
                'level' => $cat['level'],
                'prom_eval_i' => $prom_eval_i,
                'prom_eval_p' => $prom_eval_p,
                'prom_eval_manual_i' => $prom_eval_manual_i,
                'prom_eval_manual_p' => $prom_eval_manual_p,
                'risksdata' => $risksdata
            ];

            $i += 1;
        }

        return $cats;
    }
    //heatmap última evaluación
    public function heatmapLastEvaluation()
    {
        try
        {
            //obtenemos id de última evaluación
            $id_eval = DB::table('evaluations')->max('id');
            //seteamos datos en NULL por si no existe evaluación
            $nombre = NULL;
            $descripcion = NULL;
            $riesgos = NULL;
            $prom_proba = NULL;
            $prom_criticidad = NULL;

            //---- consulta multiples join para obtener las respuestas relacionada a la encuesta ----// 
            $evaluations = DB::table('evaluation_risk')
                                ->where('evaluation_risk.evaluation_id',$id_eval)
                                ->select('evaluation_risk.id','evaluation_risk.risk_id',
                                    'evaluation_risk.organization_risk_id',
                                    'evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->get();

            //obtenemos nombre y descripcion de la última encuesta
            $datos = DB::table('evaluations')->where('id',$id_eval)->select('name','description')->get();

            foreach ($datos as $datos)
            {
                 $nombre = $datos->name;
                 $descripcion = $datos->description;
            }

            $prom_proba = array();
            $prom_criticidad = array();
            $riesgos = array();
            $i = 0;
            $j = 0; //para obtener sólo una vez la organización (solución rápida)

            $org2 = NULL; //inicializamos org por si no hay evaluaciones
            foreach ($evaluations as $evaluation)
            {
                //obtenemos organización sólo una vez
                if ($j == 0)
                {
                    $org = DB::table('organizations')
                            ->join('organization_risk','organization_risk.organization_id','=','organizations.id')
                            ->where('organization_risk.id','=',$evaluation->organization_risk_id)
                            ->select('organizations.name')
                            ->first();
                    $j += 1;   
                }

                $org2 = $org->name;
                 
                //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
                $prom_proba[$i] = $evaluation->avg_probability;

                $prom_criticidad[$i] = $evaluation->avg_impact;

                    //ACTUALIZACIÓN 29-03-17: Se mostrará sólo riesgo ya que ahora se evaluará sólo el riesgo (quizás después se pueden obtener los elementos asociados)
                

                $riesgo_temp = DB::table('organization_risk')
                                ->where('organization_risk.id','=',$evaluation->organization_risk_id)
                                ->join('risks','risks.id','=','organization_risk.risk_id')
                                ->select('risks.name as name','risks.description')
                                ->get();
                
                foreach ($riesgo_temp as $temp) //el riesgo recién obtenido (de subproceso o negocio) es almacenado en riesgos
                {
                    $riesgos[$i] = array('name' => $temp->name,
                                        'description' => $temp->description,);
                }
                
                $i += 1;
            }

            return (['riesgos' => $riesgos, 'prom_proba' => $prom_proba, 'prom_criticidad' => $prom_criticidad, 'nombre' => $nombre, 'descripcion' => $descripcion,'org' => $org2]);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACTUALIZACIÓN 28-08-17: Reporte Semáforos
    public function indexReporteRiesgos()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

        if (Session::get('languaje') == 'en')
        {
            return view('en.reportes.riesgos',['organizations'=>$organizations]);
        }
        else
        {
            return view('reportes.riesgos',['organizations'=>$organizations]);
        }
    }
    //OBS 27-12-17: Se creo pensando en CocaCola (por eso el nombre), pero es para todas las organizaciones
    //ACT 12-01-18: Se agrega kind2, para definir que tipo de reporte es (por categoría, por proceso, etc)
    public function reporteRiesgos2()
    {

        //try{
        $risk_subcategories = array();        
        $categories = array();

        $processes = array();
        $processes2 = array();

        if (isset($_GET['kind2']) && $_GET['kind2'] == 1) //por categorías
        {
            //obtenemos subcategories
            //ACT 22-03-18: Esta función deberá obtener el último nivel de categorías
            $categories = \Ermtool\Risk_category::getSubcategories();
            /*$i = 0;
            foreach ($risk_subcategories as $subcategory)
            {
                $categories[$i] = ['id' => $subcategory['id'],'name' => $subcategory[¿name];
                $i += 1;
            }*/ 
        }
        else if (isset($_GET['kind2']) && $_GET['kind2'] == 2) //por procesos
        {
            if (isset($_GET['kind']) && $_GET['kind'] == 0)
            {
                $processes2 = \Ermtool\Process::where('status',0)->get(['id','name']);

                $i = 0;
                foreach ($processes2 as $process)
                {
                    $processes[$i] = ['id' => $process->id, 'name' => $process->name];
                    $i += 1;
                }
            }
        }     
                //controlado
                $control = array(); //define si un riesgo está siendo controlado o no

                $riesgo_temp = array();
                $riesgos = array();
                $i = 0;

                $ano = date('Y');
                $mes = date('m');
                $dia = date('d');

                $subs = FALSE;

                if (isset($_GET['kind']) && $_GET['kind'] == 0) //evaluaciones de riesgos de procesos
                {
                    //---- consulta multiples join para obtener los subprocesos evaluados relacionados a la organización ----//
                    //para riesgos inherente 
                    $evaluations = \Ermtool\Evaluation::getEvaluationRiskSubprocess($_GET['organization_id'],NULL,NULL,$subs,$ano,$mes,$dia);

                    $consolidados = \Ermtool\Evaluation::getEvaluationRiskSubprocess(NULL,NULL,NULL,$subs,$ano,$mes,$dia);  
                }
                else if (isset($_GET['kind']) && $_GET['kind'] == 1) //evaluaciones de riesgos de negocio
                {
                    $evaluations = \Ermtool\Evaluation::getEvaluationObjectiveRisk($_GET['organization_id'],NULL,NULL,$subs,$ano,$mes,$dia); 
                }

                if (isset($evaluations) && $evaluations != null && !empty($evaluations))
                {
                    //inherente
                    $prom_proba_in = array();
                    $prom_criticidad_in = array();
                    //ACT 15-01-18: Se comenta esto ya que no se utiliza variable org (ni se declara en la función desde ahora)
                    //if ($org != 0 && $org != NULL)
                    //{
                    //    $riesgos = $this->getEvaluatedRisks($org,$evaluations,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,null,null,null);
                    //}
                    //else
                    //{
                        $riesgos = $this->getEvaluatedRisks($_GET['organization_id'],$evaluations,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,null,null,null);
                    //}
                }        


                if (isset($consolidados) && $consolidados != null && !empty($consolidados))
                {
                    //inherente
                    $prom_proba_in = array();
                    $prom_criticidad_in = array();
                    $riesgos_consolidados = $this->getEvaluatedRisks(NULL,$consolidados,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,null,null,null);
                }
                else
                {
                    $riesgos_consolidados = array();
                }   

                //obtenemos nombre de organización
                //ACT 15-01-18: Se comenta esto ya que no se utiliza variable org (ni se declara en la función desde ahora)
                //if ($org != 0 && $org != NULL)
                //{
                //   $organization = \Ermtool\Organization::name($org); 
                //}
                //else
                //{
                    $organization = \Ermtool\Organization::name($_GET['organization_id']);
                //}
                //Return

                if (Session::get('languaje') == 'en')
                {
                    //retornamos la misma vista con datos (inglés)
                    if (isset($_GET['kind2']) && $_GET['kind2'] == 1)
                    {
                        return view('en.reportes.riesgos',['riesgos'=>$riesgos,'riesgos_consolidados'=>$riesgos_consolidados,'control' => $control,'kind' => $_GET['kind'],'categories' => $categories,'organization' => $organization]);
                    }
                    else if (isset($_GET['kind2']) && $_GET['kind2'] == 2)
                    {
                        return view('en.reportes.riesgos2',['riesgos'=>$riesgos,'riesgos_consolidados'=>$riesgos_consolidados,'control' => $control,'kind' => $_GET['kind'],'processes' => $processes,'organization' => $organization]);
                    }
                }
                else
                {
                    //$count1 = count($riesgos);
                    //$count2 = count($riesgos_consolidados);
                    if (isset($_GET['kind2']) && $_GET['kind2'] == 1)
                    {
                        return view('reportes.riesgos',['riesgos'=>$riesgos,'riesgos_consolidados'=>$riesgos_consolidados,'control' => $control,'kind' => $_GET['kind'],'categories' => $categories,'organization' => $organization]);
                    }
                    else if (isset($_GET['kind2']) && $_GET['kind2'] == 2)
                    {
                        return view('reportes.riesgos2',['riesgos'=>$riesgos,'riesgos_consolidados'=>$riesgos_consolidados,'control' => $control,'kind' => $_GET['kind'],'processes' => $processes,'organization' => $organization]);
                    }
                } 
                
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    public function getEvaluatedRisks($org,$evaluations,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,$categories,$cont_categories,$risk_subcategories)
    {
        $i = 0;
        foreach ($evaluations as $evaluation)
        {
            $updated_at_in = DB::table('evaluation_risk')
                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                    ->where('evaluation_risk.organization_risk_id','=',$evaluation->risk_id)
                    ->where('evaluations.consolidation','=',1)
                    ->where('evaluations.type','=',1)
                    ->where('evaluations.updated_at','<=',date($ano.$mes.$dia.' 23:59:59'))
                    ->max('evaluations.updated_at');

                    //$updated_at_in = str_replace('-','',$updated_at_in);


            //ACTUALIZACIÓN 22-11-16: Obtendremos los riesgos controlados a través de la tabla controlled_risk sólo para la organización y el tipo seleccionado
            $updated_at_ctrl = DB::table('controlled_risk')
                    ->join('organization_risk','organization_risk.id','=','controlled_risk.organization_risk_id')
                    ->where('controlled_risk.organization_risk_id','=',$evaluation->risk_id)
                    ->where('controlled_risk.created_at','<=',date($ano.$mes.$dia.' 23:59:59'))
                    ->max('controlled_risk.created_at');

                    //$updated_at_ctrl = str_replace('-','',$updated_at_ctrl);
                           

            //obtenemos promedio de probabilidad e impacto
            $proba_impacto_in = DB::table('evaluation_risk')
                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                    ->where('evaluations.updated_at','=',$updated_at_in)
                    ->where('evaluation_risk.organization_risk_id','=',$evaluation->risk_id)
                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                    ->first();


            //proba controlado (si es que hay)
            if (isset($updated_at_ctrl) && $updated_at_ctrl != NULL)
            {
                //ACTUALIZACIÓN 01-12: Obtenemos valor de riesgo controlado de controlled_risk_criteria, según la evaluación de controlled_risk
                $eval = DB::table('controlled_risk')
                        ->where('controlled_risk.organization_risk_id','=',$evaluation->risk_id)
                        ->where('controlled_risk.created_at','=',$updated_at_ctrl)
                        ->select('results')
                        ->first();

                //calculamos severidad y exposición al riesgo (dejaremos los nombres proba_ctrl para exposición, e impacto_ctrl para severidad para no modificar mucho la vista actual)
                $impacto_ctrl = $proba_impacto_in->avg_probability * $proba_impacto_in->avg_impact;
                //obtenemos exposición (1-X%) dividiendo el valor residual por la severidad
                $proba_ctrl = $eval->results / $impacto_ctrl;
            }

            //guardamos proba en $prom_proba
            $prom_proba_in[$i] = $proba_impacto_in->avg_probability;
            $prom_criticidad_in[$i] = $proba_impacto_in->avg_impact;

            //prom_proba_ctrl para controlado (si es que hay)
            if (isset($proba_ctrl) && isset($impacto_ctrl))
            {
                $prom_proba_ctrl[$i] = $proba_ctrl;
                $prom_impacto_ctrl[$i] = $impacto_ctrl;
                $control[$i] = 1;
            }
            else
            {
                $prom_proba_ctrl[$i] = 0;
                $prom_impacto_ctrl[$i] = 0;
            }

            //unseteamos variable de proba_impacto_ctrl para que no se repita
            unset($proba_ctrl);
            unset($impacto_ctrl);

            //ACTUALIZACIÓN 25-07: OBTENEMOS DATOS DEL RIESGO Y LOS POSIBLES RIESGOS ASOCIADOS
            $riesgo_temp = \Ermtool\Risk::find($evaluation->risk);
            //$objectives = $riesgo_temp->objectives ----> NO SIRVE MUESTRA OBJ. DE OTRAS ORGANIZACIONES
            if (isset($_GET['kind']) && $_GET['kind'] == 0) //riesgos de proceso
            {
                if ($org == NULL)
                {
                    //ACT 12-01-18: Vemos por organización igual ya que para cada organización habrá distinta evaluación
                    $subobj = DB::table('subprocesses')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                        ->where('organization_risk.id','=',$evaluation->risk_id)
                        ->where('risk_subprocess.risk_id','=',$riesgo_temp->id)
                        ->select('subprocesses.name')
                        ->get();
                }
                else
                {
                    $subobj = DB::table('subprocesses')
                    ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('risk_subprocess.risk_id','=',$riesgo_temp->id)
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->select('subprocesses.name')
                    ->get();
                }
                
            }
            else if (isset($_GET['kind']) && $_GET['kind'] == 1) //riesgos de negocio
            {
                if ($org == NULL)
                {
                    //ACT 12-01-18: Vemos por organización igual ya que para cada organización habrá distinta evaluación
                    $subobj = DB::table('objectives')
                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                        ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                        ->where('organization_risk.id','=',$evaluation->risk_id)
                        ->where('objective_risk.risk_id','=',$riesgo_temp->id)
                        ->select('objectives.name')
                        ->get();
                }
                else
                {
                    $subobj = DB::table('objectives')
                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                        ->where('objective_risk.risk_id','=',$riesgo_temp->id)
                        ->where('objectives.organization_id','=',$_GET['organization_id'])
                        ->select('objectives.name')
                        ->get();
                }
            }
            else //para gráfico de inicio, tanto subprocesos como objetivos
            {
                $subobj = DB::table('objectives')
                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                        ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                        ->where('organization_risk.id','=',$evaluation->risk_id)
                        ->where('objective_risk.risk_id','=',$riesgo_temp->id)
                        ->select('objectives.name')
                        ->get();

                if (!isset($subobj) || empty($subobj))
                {
                    $subobj = DB::table('subprocesses')
                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                        ->where('organization_risk.id','=',$evaluation->risk_id)
                        ->where('risk_subprocess.risk_id','=',$riesgo_temp->id)
                        ->select('subprocesses.name')
                        ->get();
                }
            }
                                
            //obtenemos categoría
            $risk_category = \Ermtool\Risk_category::name($riesgo_temp->risk_category_id);

            $j = 0;

            //contamos y asignamos cateogoría
            if ($risk_subcategories != null && !empty($risk_subcategories))
            {
                foreach ($risk_subcategories as $category)
                {
                    if ($category->id == $riesgo_temp->risk_category_id)
                    {
                        $cont_categories[$j] += 1;
                        break;
                    }
                    $j += 1;
                }
            }
            
            //ACT 15-01-18: Para reporte de Riesgos por Procesos
            $processes = array();
            if (isset($_GET['kind2']) &&  $_GET['kind2'] == 2) //reporte por procesos
            {
                //obtenemos todos los procesos asociado al Riesgo (puede ser uno o muchos)
                $processes = DB::table('processes')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                            ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                            ->where('organization_risk.id','=',$evaluation->risk_id)
                            ->select('processes.id','processes.name')
                            ->get();
            }

            //obtenemos nombre de responsable
            if ($org == NULL)
            {
                $stake = \Ermtool\Stakeholder::getStakeholderFromOrgRisk($evaluation->risk_id);
            }
            else
            {
                $stake = \Ermtool\Stakeholder::getRiskStakeholder($org,$evaluation->risk);

                if ($stake->id != null && !empty($stake))
                {
                    $stake = \Ermtool\Stakeholder::getName($stake->id);
                }
                else
                {
                    $stake = 'No definido';
                }
            }

            //eliminamos posibles espacios que puedan llevar a error en descripción
            //ACT 12-12-17: Eliminamos saltos en todo (también en nombre), con función eliminarSaltos de Helpers
            $description = eliminarSaltos($riesgo_temp->description);
            $comments = eliminarSaltos($riesgo_temp->comments);
            $name = eliminarSaltos($riesgo_temp->name);
            
            $riesgos[$i] = [
                'name' => $name,
                'subobj' => $subobj,
                'description' => $description,
                'risk_category_id' => $riesgo_temp->risk_category_id,
                'risk_category' => $risk_category,
                'comments' => $comments,
                'exposicion' => $prom_proba_ctrl[$i] * $prom_impacto_ctrl[$i], //exposición = exposición efectiva
                'exposicion2' => $prom_proba_ctrl[$i], //exposición2 = 1 - %Contribución
                'responsable' => $stake,
                'processes' => $processes,
            ];

            $i += 1;
        }

        return $riesgos;   
    }

}
