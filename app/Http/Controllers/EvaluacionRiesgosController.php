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

class EvaluacionRiesgosController extends Controller
{
    public function getEvalRisks($eval_id,$rut)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos datos
            $evaluation_risk = DB::table('evaluation_risk')->where('evaluation_id','=',$eval_id)->get();

            $user_answers = array(); //posibles respuestas ingresadas anteriormente
            $j = 0; //contador de posibles respuestas previas
            $i = 0;

            foreach ($evaluation_risk as $risk)
            {
                //obtenemos posibles respuestas (si es que ya se ha ingresado)
                $respuestas = DB::table('evaluation_risk_stakeholder')
                                    ->where('evaluation_risk_id','=',$risk->id)
                                    ->where('stakeholder_id','=',$rut)
                                    ->select('evaluation_risk_id as id','probability','impact')
                                    ->get();

                foreach ($respuestas as $respuesta)
                {
                    $user_answers[$j] = array(
                                'impact'=>$respuesta->impact,
                                'probability' => $respuesta->probability,
                                'id'=>$respuesta->id,
                            );
                    $j += 1;
                }
                    //-- vemos si es de proceso o de negocio --//
                    if ($risk->risk_subprocess_id != NULL) //es de proceso
                    {
                        $sub = DB::table('risk_subprocess')
                                ->where('risk_subprocess.id','=',$risk->risk_subprocess_id)
                                ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                                ->join('processes','processes.id','=','subprocesses.process_id')
                                ->select('risks.name as risk_name','risks.description','subprocesses.name as subprocess_name',
                                        'processes.name as process_name')
                                ->get();

                        //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                        foreach ($sub as $sub)
                        {
                            $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                                'risk_name' => $sub->risk_name,
                                                'description' => $sub->description,
                                                'subobj' => $sub->subprocess_name,
                                                'orgproc' => $sub->process_name);
                        }
                    }

                    else if ($risk->objective_risk_id != NULL) //es un riesgo de negocio
                    {
                        //obtenemos nombre de riesgo y organizacion
                        $neg = DB::table('objective_risk')
                                ->where('objective_risk.id','=',$risk->objective_risk_id)
                                ->join('risks','objective_risk.risk_id','=','risks.id')
                                ->join('objectives','objective_risk.objective_id','=','objectives.id')
                                ->join('organizations','objectives.organization_id','=','organizations.id')
                                ->select('risks.name as risk_name','risks.description','organizations.name as organization_name',
                                        'objectives.name as objective_name')
                                ->get();

                        foreach ($neg as $neg)
                        {
                            $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                                'risk_name' => $neg->risk_name,
                                                'description' => $neg->description,
                                                'subobj' => $neg->objective_name,
                                                'orgproc' => $neg->organization_name);
                        }
                    }

                    $i += 1;
            }

            return ['riesgos'=>$riesgos,'user_answers'=>$user_answers];
        }
    }
    public function mensaje($id)
    {
        if (Session::get('languaje') == 'en')
        {
            //Mensaje predeterminado al enviar encuestas (inglés)
            $mensaje = "Dear User.

                        We send to you the following poll for risk assessments. You must assign a value on probability and impact for each one of the risks associated to the survey. To answer this poll you have to access to the following link:

                        http://www.ixus.cl/bgrc/evaluacion_encuesta.{$id}

                        Best Regards,
                        Administration.";
                    
        }
        else
        {
            //Mensaje predeterminado al enviar encuestas
            $mensaje = "Estimado Usuario.

                        Le enviamos la siguiente encuesta para la evaluación de riesgos. Ud deberá asignar un valor de probabilidad y criticidad para cada uno de los riesgos asociados a la encuesta. Para responderla deberá acceder al siguiente link.

                        http://www.ixus.cl/bgrc/evaluacion_encuesta.{$id}

                        Saludos cordiales,
                        Administrador.";
        }
        return $mensaje;
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
            //obtenemos riesgos generales
            //$riesgos_gral = DB::table('risks')->where('type2',1)->distinct()->lists('name','name');


            //ACTUALIZACIÓN 25-07: En vez de obtener y enviar riesgos, enviamos organización para poder seleccionar
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            //juntamos riesgos
            //$riesgos = $riesgos_sub+$riesgos_obj; no se pueden juntar ya que se puede repetir id

            if (Session::get('languaje') == 'en')
            { 
                return view('en.evaluacion.crear_evaluacion',['organizations'=>$organizations]);
            }
            else
            {
                return view('evaluacion.crear_evaluacion',['organizations'=>$organizations]);
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
    public function store(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $riesgos_objetivos = array();
            $riesgos_subprocesos = array();
            if (isset($_POST['manual'])) //Se está evaluando manualmente
            {

                $tipo = 0; //evaluación manual

                $i = 0;
                if (isset($_POST['objective_risk_id'])) //insertamos primero riesgos de negocio -> si es que se agregaron
                {
                    foreach ($_POST['objective_risk_id'] as $objective_risk_id)
                    {
                        /*
                            //insertamos riesgo de negocio en evaluation_risk
                            DB::table('evaluation_risk')->insert([
                                'evaluation_id' => $eval_id,
                                'objective_risk_id' => $objective_risk_id,
                                ]); */
                        $riesgos_objetivos[$i] = $objective_risk_id;
                        $i += 1;
                    }
                }

                $i = 0;
                if (isset($_POST['risk_subprocess_id'])) //ahora insertamos riesgos de subproceso (si es que se agregaron)
                {
                    foreach ($_POST['risk_subprocess_id'] as $subprocess_risk_id)
                    {
                            /*
                            //inseratmos riesgo de subproceso en evaluation_risk
                        DB::table('evaluation_risk')->insert([
                            'evaluation_id' => $eval_id,
                            'risk_subprocess_id' => $subprocess_risk_id,
                            ]); */
                        $riesgos_subprocesos[$i] = $subprocess_risk_id;
                        $i += 1;
                    }
                }

                return $this->generarEvaluacionManual($riesgos_subprocesos,$riesgos_objetivos);

            }

            else //se está creando encuesta de evaluación
            {
                DB::transaction(function () {

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
                            DB::table('evaluation_risk')->insert([
                                'evaluation_id' => $eval_id,
                                'objective_risk_id' => $objective_risk_id,
                                ]); 
                        }
                    }

                    $i = 0;
                    if (isset($_POST['risk_subprocess_id'])) //ahora insertamos riesgos de subproceso (si es que se agregaron)
                    {
                        foreach ($_POST['risk_subprocess_id'] as $subprocess_risk_id)
                        {
                            //inseratmos riesgo de subproceso en evaluation_risk
                            DB::table('evaluation_risk')->insert([
                                'evaluation_id' => $eval_id,
                                'risk_subprocess_id' => $subprocess_risk_id,
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
                });

                return Redirect::to('evaluacion_agregadas');           
            }
        }
    }

    //Función que mostrará lista de encuestas agregadas
    public function encuestas()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $encuestas = \Ermtool\Evaluation::where('consolidation',0)->get(); //se muestran las encuestas NO consolidadas
            $i = 0;
            $fecha = array();
            foreach($encuestas as $encuesta)
            {
                if ($encuesta->expiration_date != NULL)
                {
                    $expiration_date = new DateTime($encuesta->expiration_date);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');         
                }
                else
                {
                    $fecha_exp = "Ninguna";
                }

                $fecha[$i] = ['evaluation_id' => $encuesta->id,
                              'expiration_date' => $fecha_exp];

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
    }

    public function show($id)
    {
        if (Auth::guest())
        {
            return view('login');
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
                if ($risk->risk_subprocess_id != NULL) //si es un riesgo de proceso
                {
                    //obtenemos nombre de riesgo y de subproceso
                    $sub = DB::table('risk_subprocess')
                            ->where('risk_subprocess.id','=',$risk->risk_subprocess_id)
                            ->join('risks','risk_subprocess.risk_id','=','risks.id')
                            ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                            ->select('risks.name as risk_name','subprocesses.name as subprocess_name')
                            ->get();

                    foreach ($sub as $sub)
                    {
                        $riesgos[$i] = array('risk_name' => $sub->risk_name,
                                                'subobj' => $sub->subprocess_name);
                    }
                }

                else if ($risk->objective_risk_id != NULL) //es un riesgo de negocio
                {
                    //obtenemos nombre de riesgo y organizacion
                    $neg = DB::table('objective_risk')
                            ->where('objective_risk.id','=',$risk->objective_risk_id)
                            ->join('risks','objective_risk.risk_id','=','risks.id')
                            ->join('objectives','objective_risk.objective_id','=','objectives.id')
                            ->join('organizations','objectives.organization_id','=','organizations.id')
                            ->select('risks.name as risk_name','organizations.name as organization_name')
                            ->get();

                    foreach ($neg as $neg)
                    {
                        $riesgos[$i] = array('risk_name' => $neg->risk_name,
                                                'subobj' => $neg->organization_name);
                    }
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

    public function enviar($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //Se debe inicializar en caso de que no haya sido ingresado ningún stakeholder aun
            //$stakeholders = \Ermtool\Stakeholder::lists('CONCAT(nombre, " ", apellidos)','id');
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');

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
        if (Auth::guest())
        {
            return view('login');
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

                    $sub = DB::table('risk_subprocess')
                            ->where('risk_subprocess.id','=',$risk)
                            ->join('risks','risk_subprocess.risk_id','=','risks.id')
                            ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                            ->join('processes','processes.id','=','subprocesses.process_id')
                            ->select('risks.name as risk_name','risks.description','subprocesses.name as subprocess_name',
                                    'processes.name as process_name')
                            ->get();

                    //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                    foreach ($sub as $sub)
                    {
                        $riesgos[$i] = array('type' => 'subprocess',
                                            'risk_id' => $risk,
                                            'risk_name' => $sub->risk_name,
                                            'description' => $sub->description,
                                            'subobj' => $sub->subprocess_name,
                                            'orgproc' => $sub->process_name);
                        $i += 1;
                    }
            }

            foreach ($objective_risk as $risk)
            {
                    //obtenemos nombre de riesgo y organizacion
                    $neg = DB::table('objective_risk')
                            ->where('objective_risk.id','=',$risk)
                            ->join('risks','objective_risk.risk_id','=','risks.id')
                            ->join('objectives','objective_risk.objective_id','=','objectives.id')
                            ->join('organizations','objectives.organization_id','=','organizations.id')
                            ->select('risks.name as risk_name','risks.description','organizations.name as organization_name',
                                    'objectives.name as objective_name')
                            ->get();

                    foreach ($neg as $neg)
                    {
                        $riesgos[$i] = array('type' => 'objective',
                                            'risk_id' => $risk,
                                            'risk_name' => $neg->risk_name,
                                            'description' => $neg->description,
                                            'subobj' => $neg->objective_name,
                                            'orgproc' => $neg->organization_name);
                        $i += 1;
                    }
                
            }
            if (Session::get('languaje') == 'en')
            {
                $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);

                return view('en.evaluacion.encuesta',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'tipo'=>$tipo,
                        'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$id]);
            }
            else
            {
                $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1);
                $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                return view('evaluacion.encuesta',['encuesta'=>$encuesta,'riesgos'=>$riesgos,'tipo'=>$tipo,
                        'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$id]);
            }
        }
    }
    //función que generará la encuesta para que el usuario pueda responderla
    public function generarEncuesta()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $tipo = 1; //identifica que es encuesta y no evaluación manual
            $encuesta = \Ermtool\Evaluation::find($_POST['encuesta_id']);
            $res = array();

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
                if (Session::get('languaje') == 'en')
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);
                    
                    return view('en.evaluacion.encuesta',['encuesta'=>$encuesta->name,'riesgos'=>$res['riesgos'],'tipo'=>$tipo,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$_POST['encuesta_id'],'user_answers' => $res['user_answers'],'stakeholder'=>$user->stakeholder_id]);
                }
                else
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1);
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                    return view('evaluacion.encuesta',['encuesta'=>$encuesta->name,'riesgos'=>$res['riesgos'],'tipo'=>$tipo,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>$_POST['encuesta_id'],'user_answers' => $res['user_answers'],'stakeholder'=>$user->stakeholder_id]);
                }
            }
        }
    }

    //Función para enviar correo con link a la encuesta
    public function enviarCorreo(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
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

    public function guardarEvaluacion(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //primero verificamos si el rut ingresado corresponde a algún stakeholder
            $stakeholder = \Ermtool\Stakeholder::find($request['rut']);

            //Validación: Si la validación es pasada, el código continua
            //$this->validate($request, [
            //    'rut' => 'exists:stakeholders,id'
            //]);

            if ($stakeholder) //si es que el rut ingresado es correcto, procedemos a guardar evaluación
            {   
                DB::transaction(function() {

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
                        foreach ($_POST['evaluation_risk_id'] as $risk_id) //OBS: Pueden haber riesgos de negocio y de proceso, por lo que se debe verificar
                        {
                            if (isset($_POST['proba_'.$risk_id.'_subprocess'])) //existe el id como riesgo de proceso
                            {
                                //inseratmos riesgo de subproceso en evaluation_risk
                                $evaluation_risk[$i] = DB::table('evaluation_risk')->insertGetId([
                                    'evaluation_id' => $eval_id,
                                    'risk_subprocess_id' => $risk_id,
                                    'avg_probability' => $_POST['proba_'.$risk_id.'_subprocess'],
                                    'avg_impact' => $_POST['criticidad_'.$risk_id.'_subprocess']
                                    ]);

                                //insertamos en evaluation_risk_stakeholder
                                DB::table('evaluation_risk_stakeholder')->insert([
                                'evaluation_risk_id'=>$evaluation_risk[$i],
                                'stakeholder_id'=>$_POST['rut'],
                                'probability'=>$_POST['proba_'.$risk_id.'_subprocess'],
                                'impact'=>$_POST['criticidad_'.$risk_id.'_subprocess']
                                ]);

                                $i += 1;  
                            }

                            if (isset($_POST['proba_'.$risk_id.'_objective'])) //existe el id como riesgo de negocio
                            {
                                //inseratmos riesgo de negocio en evaluation_risk
                                $evaluation_risk[$i] = DB::table('evaluation_risk')->insertGetId([
                                    'evaluation_id' => $eval_id,
                                    'objective_risk_id' => $risk_id,
                                    'avg_probability' => $_POST['proba_'.$risk_id.'_objective'],
                                    'avg_impact' => $_POST['criticidad_'.$risk_id.'_objective']
                                    ]);

                                //insertamos en evaluation_risk_stakeholder
                                DB::table('evaluation_risk_stakeholder')->insert([
                                'evaluation_risk_id'=>$evaluation_risk[$i],
                                'stakeholder_id'=>$_POST['rut'],
                                'probability'=>$_POST['proba_'.$risk_id.'_objective'],
                                'impact'=>$_POST['criticidad_'.$risk_id.'_objective']
                                ]);

                                $i += 1; 
                            }
                        }
                    }

                    else //no es evaluación manual
                    {
                        foreach ($_POST['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
                        {
                                DB::table('evaluation_risk_stakeholder')->insert([
                                    'evaluation_risk_id' => $evaluation_risk,
                                    'stakeholder_id' => $_POST['rut'],
                                    'probability'=> $_POST['proba_'.$evaluation_risk],
                                    'impact' => $_POST['criticidad_'.$evaluation_risk]
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
                });
                
                return view('evaluacion.encuestaresp');
                //print_r($_POST);
            }
            else //no se encontró el rut ingresado
            {
                if ($_POST['tipo'] == 0)
                {
                    $i = 0;
                    //volvemos a obtener riesgos para devolver vista correcta
                    foreach ($_POST['evaluation_risk_id'] as $risk_id)
                    {
                        if (isset($_POST['proba_'.$risk_id.'_subprocess'])) //existe el id como riesgo de proceso
                        {
                            $sub = DB::table('risk_subprocess')
                                ->where('risk_subprocess.id','=',$risk_id)
                                ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                                ->join('processes','processes.id','=','subprocesses.process_id')
                                ->select('risks.name as risk_name','risks.description','subprocesses.name as subprocess_name',
                                        'processes.name as process_name')
                                ->get();

                            //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                            foreach ($sub as $sub)
                            {
                                $riesgos[$i] = array('type' => 'subprocess',
                                                    'risk_id' => $risk_id,
                                                    'risk_name' => $sub->risk_name,
                                                    'description' => $sub->description,
                                                    'subobj' => $sub->subprocess_name,
                                                    'orgproc' => $sub->process_name);
                                $i += 1;
                            }
                        }

                        if (isset($_POST['proba_'.$risk_id.'_objective'])) //existe el id como riesgo de negocio
                        {
                            //obtenemos nombre de riesgo y organizacion
                            $neg = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$risk_id)
                                    ->join('risks','objective_risk.risk_id','=','risks.id')
                                    ->join('objectives','objective_risk.objective_id','=','objectives.id')
                                    ->join('organizations','objectives.organization_id','=','organizations.id')
                                    ->select('risks.name as risk_name','risks.description','organizations.name as organization_name',
                                            'objectives.name as objective_name')
                                    ->get();

                            foreach ($neg as $neg)
                            {
                                $riesgos[$i] = array('type' => 'objective',
                                                    'risk_id' => $risk_id,
                                                    'risk_name' => $neg->risk_name,
                                                    'description' => $neg->description,
                                                    'subobj' => $neg->objective_name,
                                                    'orgproc' => $neg->organization_name);
                                $i += 1;
                            }     
                        }
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message',"The entered Id is not in our database");

                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);

                        return view('en.evaluacion.encuesta',['encuesta'=>'Manual Evaluation','riesgos'=>$riesgos,'tipo'=>0,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>0]);
                    }
                    else
                    {
                        Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1); //2 es inglés
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                        return view('evaluacion.encuesta',['encuesta'=>'Evaluación Manual','riesgos'=>$riesgos,'tipo'=>0,'tipos_impacto' => $tipos_impacto,'tipos_proba' => $tipos_proba,'id'=>0])->withInput(Input::all()); //no funcion withInput
                    }
                }
                else
                {
                    return Redirect::to('evaluacion.encuesta.'.$request["evaluation_id"])->withInput();
                }
            }
        }
    }

    public function listHeatmap()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos encuestas distintas a las que corresponden a una evaluación manual
            $encuestas = \Ermtool\Evaluation::where('description','<>','NULL')->lists('name','id');
            $organizaciones = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones]);
            }
            else
            {
                return view('reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones]);
            }
        }
    }

    public function generarHeatmap(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            //Nombre y descripción de la encuesta u organización
            $nombre = "";
            $descripcion = "";
            //inherente
            $prom_proba_in = array();
            $prom_criticidad_in = array();
            //controlado
            $prom_proba_ctrl = array();
            $prom_criticidad_ctrl = array();

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
                }

                //obtenemos nombre y descripción de organización
                $datos = DB::table('organizations')->where('id',$_GET['organization_id'])->select('name','description')->get();

                foreach ($datos as $datos)
                {
                     $nombre = $datos->name;
                     $descripcion = $datos->description;
                }
                if ($_GET['kind'] == 0)
                {
                     //---- consulta multiples join para obtener los subprocesos evaluados relacionados a la organización ----//

                    //para riesgos inherente 
                    $evaluations = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->whereNotNull('evaluation_risk.risk_subprocess_id')
                                    ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                                    ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                                    ->where('evaluations.consolidation','=',1)
                                    ->select('evaluation_risk.risk_subprocess_id as risk_id','risks.id as risk')
                                    ->groupBy('risks.id')
                                    ->get();

                    foreach ($evaluations as $evaluation)
                    {                    
                        //obtenemos promedio de probabilidad e impacto (INHERENTE Y CONTROLADO)
                        $updated_at_in = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.risk_subprocess_id',$evaluation->risk_id)
                                    ->where('evaluations.consolidation','=',1)
                                    ->where('evaluations.type','=',1)
                                    ->where('evaluations.updated_at','<=',date($ano.'-'.$mes.'-31 23:59:59'))
                                    ->max('evaluations.updated_at');

                        if ($_GET['kind2'] == 1) //Si es 0 veremos solo mapa para riesgos inherentes
                        {

                            //ACTUALIZACIÓN 22-11-16: Obtendremos los riesgos controlados a través de la tabla controlled_risk sólo para la organización y el tipo seleccionado
                            $updated_at_ctrl = DB::table('controlled_risk')
                                                ->join('risk_subprocess','risk_subprocess.id','=','controlled_risk.risk_subprocess_id')
                                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                                                ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                                                ->where('controlled_risk.risk_subprocess_id','=',$evaluation->risk_id)
                                                ->where('controlled_risk.created_at','<=',date($ano.'-'.$mes.'-31 23:59:59'))
                                                ->max('controlled_risk.created_at');
                        }

                        $proba_impacto_in = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluations.updated_at','=',$updated_at_in)
                                    ->where('evaluation_risk.risk_subprocess_id','=',$evaluation->risk_id)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->first();

                        //proba controlado (si es que hay)
                        if (isset($updated_at_ctrl) && $updated_at_ctrl != NULL)
                        {
                            //ACTUALIZACIÓN 01-12: Obtenemos valor de riesgo controlado de controlled_risk_criteria, según la evaluación de controlled_risk
                            $eval = DB::table('controlled_risk')
                                        ->where('controlled_risk.risk_subprocess_id','=',$evaluation->risk_id)
                                        ->where('controlled_risk.created_at','=',$updated_at_ctrl)
                                        ->select('results')
                                        ->first();

                            //obtenemos valor de evaluación controlada, para este resultado y con los valores del riesgo inherente
                            $proba_ctrl = DB::table('controlled_risk_criteria')
                                                    ->where('dim_eval','=',1)
                                                    ->where('eval_in_risk','=',$proba_impacto_in->avg_probability)
                                                    ->where('control_evaluation','=',$eval->results)
                                                    ->select('eval_ctrl_risk as eval')
                                                    ->first();

                            $impacto_ctrl = DB::table('controlled_risk_criteria')
                                                    ->where('dim_eval','=',2)
                                                    ->where('eval_in_risk','=',$proba_impacto_in->avg_impact)
                                                    ->where('control_evaluation','=',$eval->results)
                                                    ->select('eval_ctrl_risk as eval')
                                                    ->first();

                        }

                        //guardamos proba en $prom_proba_in para inherente
                        $prom_proba_in[$i] = $proba_impacto_in->avg_probability;
                        $prom_criticidad_in[$i] = $proba_impacto_in->avg_impact;
                        

                        //prom_proba_ctrl para controlado (si es que hay)
                        if (isset($proba_impacto_ctrl))
                        {

                            $prom_proba_ctrl[$i] = $proba_ctrl->eval;
                            $prom_criticidad_ctrl[$i] = $impacto_ctrl->eval;

                        }
                        else
                        {
                            $prom_proba_ctrl[$i] = NULL;
                            $prom_criticidad_ctrl[$i] = NULL;
                        }

                        //unseteamos variable de proba_impacto_ctrl para que no se repita
                        unset($proba_impacto_ctrl);

                        //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                        //ACTUALIZACIÓN 25-07: OBTENEMOS DATOS DEL RIESGO Y LOS POSIBLES SUBPROCESOS ASOCIADOS
                        $riesgo_temp = \Ermtool\Risk::find($evaluation->risk);
                        //$subprocesses = $riesgo_temp->subprocesses; ---> NO SIRVE MUESTRA SUBPR. DE OTRAS ORGS.

                        $subprocesses = DB::table('subprocesses')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('risk_subprocess.risk_id','=',$riesgo_temp->id)
                                    ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                                    ->select('subprocesses.name')
                                    ->get();

                        //foreach ($riesgo_temp as $temp) //el riesgo recién obtenido es almacenado en riesgos
                        //{
                            //probamos eliminar espacios en descripcion
                            $description = preg_replace('(\n)',' ',$riesgo_temp->description);
                            $description = preg_replace('(\r)',' ',$description);

                            $riesgos[$i] = array('name' => $riesgo_temp->name,
                                                'subobj' => $subprocesses,
                                                'description' => $description);
                        //}

                        $i += 1;
                    }
                }
                else if ($_GET['kind'] == 1) //evaluaciones de riesgos de negocio
                {
                    //---- consulta multiples join para obtener los objective_risk evaluados relacionados a la organización ----// 
                    $evaluations = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->where('objectives.organization_id','=',$_GET['organization_id'])
                                    ->where('evaluations.consolidation','=',1)
                                    ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                                    ->select('evaluation_risk.objective_risk_id as risk_id','risks.id as risk')
                                    ->groupBy('risks.id')->get();
                   
                    foreach ($evaluations as $evaluation)
                    {
                        $updated_at_in = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.objective_risk_id','=',$evaluation->risk_id)
                                    ->where('evaluations.consolidation','=',1)
                                    ->where('evaluations.type','=',1)
                                    ->where('evaluations.updated_at','<=',date($ano.'-'.$mes.'-31 23:59:59'))
                                    ->max('evaluations.updated_at');

                        if ($_GET['kind2'] == 1) //Si es 0 veremos solo mapa para riesgos inherentes
                        {
                            //ACTUALIZACIÓN 22-11-16: Obtendremos los riesgos controlados a través de la tabla controlled_risk sólo para la organización y el tipo seleccionado
                            $updated_at_ctrl = DB::table('controlled_risk')
                                                ->join('objective_risk','objective_risk.id','=','controlled_risk.objective_risk_id')
                                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                                ->where('objectives.organization_id','=',$_GET['organization_id'])
                                                ->where('controlled_risk.objective_risk_id','=',$evaluation->risk_id)
                                                ->where('controlled_risk.created_at','<=',date($ano.'-'.$mes.'-31 23:59:59'))
                                                ->max('controlled_risk.created_at');
                        }

                        //obtenemos promedio de probabilidad e impacto
                        $proba_impacto_in = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluations.updated_at','=',$updated_at_in)
                                    ->where('evaluation_risk.objective_risk_id','=',$evaluation->risk_id)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->first();

                        //proba controlado (si es que hay)
                        if (isset($updated_at_ctrl) && $updated_at_ctrl != NULL)
                        {
                            //ACTUALIZACIÓN 01-12: Obtenemos valor de riesgo controlado de controlled_risk_criteria, según la evaluación de controlled_risk
                            $eval = DB::table('controlled_risk')
                                        ->where('controlled_risk.objective_risk_id','=',$evaluation->risk_id)
                                        ->where('controlled_risk.created_at','=',$updated_at_ctrl)
                                        ->select('results')
                                        ->first();

                            //obtenemos valor de evaluación controlada, para este resultado y con los valores del riesgo inherente
                            $proba_ctrl = DB::table('controlled_risk_criteria')
                                                    ->where('dim_eval','=',1)
                                                    ->where('eval_in_risk','=',$proba_impacto_in->avg_probability)
                                                    ->where('control_evaluation','=',$eval->results)
                                                    ->select('eval_ctrl_risk as eval')
                                                    ->first();

                            $impacto_ctrl = DB::table('controlled_risk_criteria')
                                                    ->where('dim_eval','=',2)
                                                    ->where('eval_in_risk','=',$proba_impacto_in->avg_impact)
                                                    ->where('control_evaluation','=',$eval->results)
                                                    ->select('eval_ctrl_risk as eval')
                                                    ->first();
                        }

                        //guardamos proba en $prom_proba
                        $prom_proba_in[$i] = $proba_impacto_in->avg_probability;
                        $prom_criticidad_in[$i] = $proba_impacto_in->avg_impact;

                        //prom_proba_ctrl para controlado (si es que hay)
                        if (isset($proba_ctrl) && isset($impacto_ctrl))
                        {
                            $prom_proba_ctrl[$i] = $proba_ctrl->eval;
                            $prom_criticidad_ctrl[$i] = $impacto_ctrl->eval;
                        }
                        else
                        {
                            $prom_proba_ctrl[$i] = NULL;
                            $prom_criticidad_ctrl[$i] = NULL;
                        }

                        //unseteamos variable de proba_impacto_ctrl para que no se repita
                        unset($proba_ctrl);
                        unset($impacto_ctrl);

                        //ACTUALIZACIÓN 25-07: OBTENEMOS DATOS DEL RIESGO Y LOS POSIBLES RIESGOS ASOCIADOS
                        $riesgo_temp = \Ermtool\Risk::find($evaluation->risk);
                        //$objectives = $riesgo_temp->objectives ----> NO SIRVE MUESTRA OBJ. DE OTRAS ORGANIZACIONES
                        $objectives = DB::table('objectives')
                                    ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                                    ->where('objective_risk.risk_id','=',$riesgo_temp->id)
                                    ->where('objectives.organization_id','=',$_GET['organization_id'])
                                    ->select('objectives.name')
                                    ->get();

                        //eliminamos posibles espacios que puedan llevar a error en descripción
                        $description = preg_replace('(\n)',' ',$riesgo_temp->description);
                        $description = preg_replace('(\r)',' ',$description);

                        $riesgos[$i] = array('name' => $riesgo_temp->name,
                                            'subobj' => $objectives,
                                            'description' => $description);

                        $i += 1;
                    }      
                }
            
            if ($_GET['kind2'] == 1) //Si es 0 veremos solo mapa para riesgos inherentes
            {
                if (Session::get('languaje') == 'en')
                {
                    //retornamos la misma vista con datos (inglés)
                    return view('en.reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                            'riesgos'=>$riesgos,'prom_proba_in'=>$prom_proba_in,
                                            'prom_criticidad_in'=>$prom_criticidad_in,
                                            'prom_proba_ctrl'=>$prom_proba_ctrl,
                                            'prom_criticidad_ctrl'=>$prom_criticidad_ctrl,
                                            'kind' => $_GET['kind'],
                                            'kind2' => $_GET['kind2']]);
                }
                else
                {
                    return view('reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                            'riesgos'=>$riesgos,'prom_proba_in'=>$prom_proba_in,
                                            'prom_criticidad_in'=>$prom_criticidad_in,
                                            'prom_proba_ctrl'=>$prom_proba_ctrl,
                                            'prom_criticidad_ctrl'=>$prom_criticidad_ctrl,
                                            'kind' => $_GET['kind'],
                                            'kind2' => $_GET['kind2']]);
                } 
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    //retornamos la misma vista con datos pero solo de riesgos inherentes (inglés)
                    return view('en.reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                            'riesgos'=>$riesgos,'prom_proba_in'=>$prom_proba_in,
                                            'prom_criticidad_in'=>$prom_criticidad_in,
                                            'kind' => $_GET['kind'],
                                            'kind2' => $_GET['kind2']]);
                }
                else
                {
                    return view('reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                            'riesgos'=>$riesgos,'prom_proba_in'=>$prom_proba_in,
                                            'prom_criticidad_in'=>$prom_criticidad_in,
                                            'kind' => $_GET['kind'],
                                            'kind2' => $_GET['kind2']]);
                } 
            }
        }
    }

    public function evaluacionManual()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos organizaciones
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.evaluacion.evaluacion_manual',['organizations' => $organizations]);
            }
            else
            {
                return view('evaluacion.evaluacion_manual',['organizations' => $organizations]);
            }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $evaluations_risks = array(); //guardaremos todos los riesgos con sus datos y evaluaciones
            $i = 0;
            //----obtenemos todos los riesgos asociados a la encuesta----//
            //primero obtenemos riesgos de negocio (de existir)
            $objective_risks = DB::table('evaluation_risk')
                                ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('evaluation_risk.evaluation_id','=',$id)
                                ->where('objective_risk_id',"<>","'NULL'")
                                ->select('evaluation_risk.id as id',
                                        'risks.name as risk_name','objectives.name as objective_name',
                                        'organizations.name as organization_name',
                                        'evaluation_risk.avg_impact as impact',
                                        'evaluation_risk.avg_probability as probability')->get();

            if ($objective_risks) //si es que hay
            {
                foreach ($objective_risks as $objective_risk)
                {
                    $evaluations_risks[$i] = array(
                        'id' => $objective_risk->id,
                        'risk_name' => $objective_risk->risk_name,
                        'subobj_name' => $objective_risk->objective_name,
                        'orgproc_name' => $objective_risk->organization_name,
                        'impact' => $objective_risk->impact,
                        'probability' => $objective_risk->probability
                        );

                    $i += 1;
                }
            }

            //ahora obtenemos riesgos de procesos (de existir)
            $risks_subprocesses = DB::table('evaluation_risk')
                                ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                ->join('processes','processes.id','=','subprocesses.process_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('evaluation_risk.evaluation_id','=',$id)
                                ->where('risk_subprocess_id',"<>","'NULL'")
                                ->select('evaluation_risk.id as id',
                                        'risks.name as risk_name','subprocesses.name as subprocess_name',
                                        'processes.name as process_name',
                                        'evaluation_risk.avg_impact as impact',
                                        'evaluation_risk.avg_probability as probability')->get();

            if ($risks_subprocesses) //si es que hay
            {
                foreach ($risks_subprocesses as $risk_subprocess)
                {
                    $evaluations_risks[$i] = array(
                        'id' => $risk_subprocess->id,
                        'risk_name' => $risk_subprocess->risk_name,
                        'subobj_name' => $risk_subprocess->subprocess_name,
                        'orgproc_name' => $risk_subprocess->process_name,
                        'impact' => $risk_subprocess->impact,
                        'probability' => $risk_subprocess->probability
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

    //Función que consolida encuesta (cambia estado consolidation = 1)
    public function consolidar(Request $request)
    {
            //print_r($_POST);

        //primero obtenemos id de riesgos (evaluation_risk) de la encuesta
        $evaluation_risks = DB::table('evaluation_risk')
                                ->where('evaluation_risk.evaluation_id',$request['evaluation_id'])
                                ->select('evaluation_risk.id as id')
                                ->get();

        foreach ($evaluation_risks as $evaluation_risk)
        {
            //actualizaremos probabilidad e impacto para cada evaluation_risk
            DB::table('evaluation_risk')
                ->where('evaluation_risk.id',$evaluation_risk->id)
                ->update([
                    'avg_probability' => $request['probability_'.$evaluation_risk->id],
                    'avg_impact' => $request['impact_'.$evaluation_risk->id]
                    ]);

            //actualizamos atributo consolidation en tabla evaluations
            \Ermtool\Evaluation::where('id',$request['evaluation_id'])
                                    ->update(['consolidation' => 1]);
        }

        if (Session::get('languaje') == 'en')
        {
            Session::flash('message','Poll successfully consolidated');
        }
        else
        {
            Session::flash('message','Encuesta de evaluación consolidada correctamente');
        }

        return Redirect::to('/evaluacion_agregadas');
        
    }

    //eliminará una encuesta (que no sea manual), en caso de que esta no haya sido respondida por nadie
    public function delete($id1)
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
                        //eliminamos de evaluation_stakeholder (si es que hay)
                        DB::table('evaluation_stakeholder')
                            ->where('evaluation_id','=',$GLOBALS['id'])
                            ->delete();

                        //eliminamos evaluación
                        DB::table('evaluations')
                            ->where('id','=',$GLOBALS['id'])
                            ->delete();

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

    public function verificadorUserEncuesta($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
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
    }

    public function verRespuestas($eval_id,$rut)
    {
        if (Auth::guest())
        {
            return view('login');
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

                return view('en.evaluacion.respuestas',['riesgos'=>$res['riesgos'],'user_answers'=>$res['user_answers'],
                                                     'eval_id'=>$eval_id,'rut'=>$rut,'encuesta'=>$encuesta,'user'=>$user,
                                                     'tipos_impacto'=>$tipos_impacto,'tipos_proba'=>$tipos_proba]);
            }
            else
            {
                $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1); //2 es inglés
                $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);

                return view('evaluacion.respuestas',['riesgos'=>$res['riesgos'],'user_answers'=>$res['user_answers'],
                                                     'eval_id'=>$eval_id,'rut'=>$rut,'encuesta'=>$encuesta,'user'=>$user,
                                                     'tipos_impacto'=>$tipos_impacto,'tipos_proba'=>$tipos_proba]);
            }
        }
    }

    public function updateEvaluacion(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {  
            DB::transaction(function() {

                //verificamos si tipo = 0 (significaria que es evaluación manual por lo tanto se debe crear)
                $i = 0;
                $evaluation_risk = array(); //array que guarda los riesgos que se estarán actualizando
                
                foreach ($_POST['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
                {
                            DB::table('evaluation_risk_stakeholder')
                                ->where('evaluation_risk_id','=',$evaluation_risk)
                                ->where('stakeholder_id','=',$_POST['rut'])
                                ->update([
                                'probability'=> $_POST['proba_'.$evaluation_risk],
                                'impact' => $_POST['criticidad_'.$evaluation_risk]
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
            });
            
            return view('en.evaluacion.encuestaresp');
            //print_r($_POST);
        }
    }

}
