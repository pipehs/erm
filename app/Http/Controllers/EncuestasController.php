<?php

namespace Ermtool\Http\Controllers;

//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Redirect;
use Session;
use Mail;
use DB;
use ArrayObject;
use Auth;
use DateTime;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class EncuestasController extends Controller
{
    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('eventos_riesgos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/eventos_riesgos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
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
                $mensaje = "Dear {{ Usuario }}.
                            <space>
                            We send to you the following poll for the identification of risk events. Answer each one of the questions associated to the poll. To answer it you have to access to the following link:
                            <space>
                            http://".$conf->option_value."/identificacion.encuesta.{$id}
                            <space>
                            Best Regards,<space>
                            Administration.";
            }
            else
            {
                //Mensaje predeterminado al enviar encuestas
                $mensaje = "Estimado {{ Usuario }}.
                        <space>
                        Le enviamos la siguiente encuesta para la identificación de eventos de riesgos.<space>
                        Responda cada una de las preguntas asociadas a la encuesta. Para responderla deberá acceder al siguiente link.
                        <space>
                        http://".$conf->option_value."/identificacion.encuesta.{$id}
                        <space>
                        Saludos cordiales,<space>
                        Administrador.";
            } 
                
            
            return $mensaje;
        }
        else
        {
            return Redirect::to('configuration.create');
        }
        
    }

    //Muestra las encuestas en su formato (No respuestas)
    public function index()
    {
        
    }


    public function enviar(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                if (isset($_GET['aplicar']))
                {
                    //$tipo = Request::input('destinatarios');

                    if ($request['destinatarios'] == 0) //Se asignaran los destinatarios manualmente
                    {
                        //seleccionamos lista de stakeholders
                         $dest = \Ermtool\Stakeholder::listStakeholders(NULL);
                    }
                    else if ($request['destinatarios'] == 1) //Se enviará por organizacion
                    {
                        $dest = \Ermtool\Organization::lists('name','id');
                    }
                    else if ($request['destinatarios'] == 2) //Se enviará por tipo / rol
                    {
                        $dest = \Ermtool\Role::lists('name','id');
                    }

                    $encuesta = \Ermtool\Poll::find($request['encuesta']);

                    //obtenemos preguntas
                    $preguntas = DB::table('questions')->where('poll_id','=',$encuesta['id'])->get();

                    $answers = array(); //almacenaremos aquí respuestas posibles para las preguntas
                    $i = 0; //contador de respuestas
                    foreach ($preguntas as $pregunta)
                    {
                        if ($pregunta->answers_type != 0) //pregunta tiene alguna posible answer
                        {
                            $posible_answers = DB::table('posible_answers')->where('question_id',$pregunta->id)->get();

                            foreach ($posible_answers as $posible_answer)
                            {
                                $answers[$i] = array('id'=>$posible_answer->id,
                                                    'respuesta'=>$posible_answer->answer,
                                                    'question_id'=>$posible_answer->question_id);
                                $i += 1;
                            }
                        }
                    }

                    if (Session::get('languaje') == 'en')
                    { 
                        return view('en.encuestas.enviarencuesta2',['tipo'=>$request['destinatarios'],
                            'dest'=>$dest,'encuesta'=>$encuesta,'preguntas'=>$preguntas,'respuestas'=>$answers,
                            'mensaje'=>$this->mensaje($encuesta['id'])]);
                    }
                    else
                    {
                        return view('encuestas.enviarencuesta2',['tipo'=>$request['destinatarios'],
                            'dest'=>$dest,'encuesta'=>$encuesta,'preguntas'=>$preguntas,'respuestas'=>$answers,
                            'mensaje'=>$this->mensaje($encuesta['id'])]);
                    }
                }
                else if (isset($_GET['volver']))
                {
                    $polls = \Ermtool\Poll::lists('name','id');

                    if (Session::get('languaje') == 'en')
                    { 
                        return view('en.encuestas.enviarencuesta',['polls'=>$polls]);
                    }
                    else
                    {
                        return view('encuestas.enviarencuesta',['polls'=>$polls]);
                    }
                }
                else
                {
                    $polls = \Ermtool\Poll::lists('name','id');
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.encuestas.enviarencuesta',['polls'=>$polls]);
                    }
                    else
                    {
                        return view('encuestas.enviarencuesta',['polls'=>$polls]);
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

    public function create()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //ACT 06-04: Primero, verificamos que se encuentre configurado el link de la encuesta
                $url = \Ermtool\Configuration::where('option_name','=','system_url')->first();

                if (empty($url) || $url->option_value == NULL || !$url)
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
                if (isset($_POST['agregar'])) //se agregaron las preguntas, ahora se deben agregar las respuestas
                { /* NO CUMPLE AISLAMIENTO 
                    \Ermtool\Poll::create([
                        'name'=>$_POST['nombre'],
                        ]); */
                    $cont = $_POST['cantidad_preguntas'];
                    //obtenemos id de esta encuesta
                    $poll_id = \Ermtool\Poll::max('id');
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.encuestas.crearencuesta2',['cont'=>$cont,'name'=>$_POST['nombre']]);
                    }
                    else
                    {
                        return view('encuestas.crearencuesta2',['cont'=>$cont,'name'=>$_POST['nombre']]);
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.encuestas.crearencuesta');
                    }
                    else
                    {
                        return view('encuestas.crearencuesta');
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
                return Redirect::route('/');
            }
            else
            {
                DB::transaction(function() {

                    $logger = $this->logger;

                    $cont = $_POST['contpreguntas'];

                    //primero agregamos encuesta
                    $poll =  \Ermtool\Poll::create([
                        'name'=>$_POST['nombre_encuesta'],
                        ]);        
                    //agregamos preguntas y tipos de respuesta
                    for ($i=1; $i<=$cont; $i++)
                    {
                        if (isset($_POST['pregunta'.$i])) //si es que se agregó la pregunta de índice $i (ya que el usuario al crear puede agregar varias preguntas pero solo enviar 1)
                        {
                            $question = \Ermtool\Question::create([
                                'question'=>$_POST['pregunta'.$i],
                                'answers_type'=>$_POST['tipo_respuesta'.$i],
                                'poll_id'=>$poll->id,
                                ]);

                            //vemos tipo de respuesta para ver si se debe agregar en posible answers
                            if ($_POST['tipo_respuesta'.$i] != 0) //significa que es radio o checkbox
                            {

                                $j = 1; //contador cantidad de alternativas para cada pregunta
                                while (isset($_POST['pregunta'.$i.'_alternativa'.$j]) && $_POST['pregunta'.$i.'_alternativa'.$j] != "") //mientras hayan alternativas para la pregunta y la pregunta no se encuentre vacía
                                {
                                    \Ermtool\Posible_answer::create([
                                        'answer'=>$_POST['pregunta'.$i.'_alternativa'.$j],
                                        'question_id'=>$question->id,
                                        ]);
                                    $j += 1;
                                }

                            // echo "Cantidad de alternativas para pregunta ".$i.": ".($j-1)."<br>";
                            }
                        }
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message',"Poll created successfully");
                    }
                    else
                    {
                        Session::flash('message','Encuesta creada con &eacute;xito');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la encuesta de eventos de riesgo con Id: '.$poll->id.' llamada: '.$poll->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                if (Session::get('languaje') == 'en')
                {
                    return Redirect::to('en.ver_encuestas');
                }
                else
                {
                    return Redirect::to('ver_encuestas');
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función que primero verificará que el usuario no haya respondido previamente (si es que respondio permitirá editar sus respuestas)
    public function verificadorUserEncuesta($id)
    {
        try
        {
            $encuesta = \Ermtool\Poll::find($id);

            if (Session::get('languaje') == 'en')
            {
                return view('en.encuestas.verificar_encuesta',['encuesta'=>$encuesta]);
            }
            else
            {
                return view('encuestas.verificar_encuesta',['encuesta'=>$encuesta]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarEncuesta()
    {
        try
        {
            $encuesta = \Ermtool\Poll::find($_GET['encuesta_id']);

            //ACTUALIZACIÓN 24-08-17: Veremos si el id es mayor o igual al máximo permitido por INT
            if ($_GET['id'] >= 2147483647)
            {
                //realizaremos división y utilizamos entero
                $id = $_GET['id'] / 100;
                $_GET['id'] = (int)$id;
            }
            //primero, verificamos que el usuario exista
            $user = DB::table('poll_stakeholder')
                        ->where('poll_id','=',$_GET['encuesta_id'])
                        ->where('stakeholder_id','=',$_GET['id'])
                        ->select('stakeholder_id')
                        ->first();

            if (!$user)
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('error',"The poll doesn't send to the entered user");
                    return view('en.encuestas.verificar_encuesta',['encuesta'=>$encuesta]);
                }
                else
                {
                    Session::flash('error','La encuesta no ha sido enviada al usuario ingresado');
                    return view('encuestas.verificar_encuesta',['encuesta'=>$encuesta]);
                }
            }
            else
            {
                //obtenemos preguntas
                $preguntas = DB::table('questions')->where('poll_id','=',$encuesta['id'])->get();
                $user_answers = array();
                $answers = array(); //almacenaremos aquí respuestas posibles para las preguntas
                $i = 0; //contador de respuestas
                $j = 0; //contador de respuestas ingresadas previamente
                foreach ($preguntas as $pregunta)
                {
                    if ($pregunta->answers_type != 0) //pregunta tiene alguna posible answer
                    {
                        $posible_answers = DB::table('posible_answers')->where('question_id',$pregunta->id)->get();

                        foreach ($posible_answers as $posible_answer)
                        {
                            $answers[$i] = array('id'=>$posible_answer->id,
                                                'answer'=>$posible_answer->answer,
                                                'question_id'=>$posible_answer->question_id);
                            $i += 1;
                        }
                    }

                    //obtenemos posibles respuestas (si es que ya se ha ingresado)
                    $respuestas = DB::table('answers')
                                    ->where('question_id','=',$pregunta->id)
                                    ->where('stakeholder_id','=',$user->stakeholder_id)
                                    ->select('question_id','answer')
                                    ->get();

                    foreach ($respuestas as $respuesta)
                    {
                        $user_answers[$j] = array(
                                    'answer'=>$respuesta->answer,
                                    'question_id'=>$respuesta->question_id,
                                    );
                        $j += 1;
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.encuestas.encuesta',['encuesta'=>$encuesta,'preguntas'=>$preguntas,'respuestas'=>$answers,'user_answers'=>$user_answers,'user'=>$user->stakeholder_id]);
                }
                else
                {
                    return view('encuestas.encuesta',['encuesta'=>$encuesta,'preguntas'=>$preguntas,'respuestas'=>$answers,'user_answers'=>$user_answers,'user'=>$user->stakeholder_id]);
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
                DB::transaction(function() {
                    //guardamos en un array todos los correos de los stakeholders
                    $correos = array();
                    $stakeholders = array();
                    $i = 0;

                    //OBS: STAKEHOLDER_ID[] PUEDE SER STAKEHOLDERS, ORGANIZACIONES O TIPO DE STAKEHOLDERS
                    if ($_POST['tipo'] == 0) //Se asignaron stakeholders manualmente
                    {
                        foreach ($_POST['stakeholder_id'] as $stakeholder_id)
                        {
                            $stakeholder = \Ermtool\Stakeholder::find($stakeholder_id);

                            //ALMACENAMOS EN POLL_STAKEHOLDER (funcionalidad agregada 13-05-2016) PARA SABER A QUIENES SE ENVÍA LA ENCUESTA. OBS: Debemos ver que el usuario no exista
                            try
                            {
                                DB::table('poll_stakeholder')
                                    ->insert([
                                        'stakeholder_id' => $stakeholder->id,
                                        'poll_id' => $_POST['poll_id'],
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

                                //ACT 13-07-18: De todas maneras enviamos correo
                                if (Session::get('languaje') == 'en')
                                {
                                    $errors->append("The poll has been send again to the user ".$stakeholder->name." ".$stakeholder->surnames.".");
                                }
                                else
                                {
                                    $errors->append('Se ha enviado nuevamente la encuesta a '.$stakeholder->name.' '.$stakeholder->surnames.'.');
                                }

                                $correos[$i] = $stakeholder->mail;
                            }

                            if (isset($errors))
                            {
                                Session::flash('error',$errors);
                            }
                            $i += 1;
                        }
                    }
                    else if ($_POST['tipo'] == 1) //Se asignaron stakeholders por organización
                    {
                        foreach ($_POST['stakeholder_id'] as $stakeholder_id)
                        {
                            //obteneos los id de stakeholders de la organizacion (ACT: 23-01-17 stakeholders que no se encuentren bloqueados)
                            $stakeholders = DB::table('organization_stakeholder')
                                                ->join('stakeholders','stakeholders.id','=','organization_stakeholder.stakeholder_id')
                                                ->where('stakeholders.status','=',0)
                                                ->where('organization_id',$stakeholder_id)
                                                ->select('stakeholder_id as id')->get();

                            //para cada stakeholder de la organización
                            foreach ($stakeholders as $stakeholder)
                            {
                                //obtenemos datos de stakeholder
                                $stakeholder = \Ermtool\Stakeholder::find($stakeholder->id);
                                try
                                {
                                    DB::table('poll_stakeholder')
                                        ->insert([
                                            'stakeholder_id' => $stakeholder->id,
                                            'poll_id' => $_POST['poll_id'],
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

                                    //ACT 13-07-18: De todas maneras enviamos correo
                                    if (Session::get('languaje') == 'en')
                                    {
                                        $errors->append("The poll has been send again to the user ".$stakeholder->name." ".$stakeholder->surnames.".");
                                    }
                                    else
                                    {
                                        $errors->append('Se ha enviado nuevamente la encuesta a '.$stakeholder->name.' '.$stakeholder->surnames.'.');
                                    }

                                    $correos[$i] = $stakeholder->mail;
                                }

                                if (isset($errors))
                                {
                                    Session::flash('error',$errors);
                                }
                                $i += 1;
                            }
                        }
                    }
                    else if ($_POST['tipo'] == 2) //Se asignaron stakeholders por rol
                    {
                        foreach ($_POST['stakeholder_id'] as $stakeholder_id) //para cada id de rol
                        {
                            //obtenemos los id de stakeholders del rol seleccionado
                            $stakeholders = DB::table('role_stakeholder')
                                                ->join('stakeholders','stakeholders.id','=','role_stakeholder.stakeholder_id')
                                                ->where('stakeholders.status','=',0)
                                                ->where('role_id',$stakeholder_id)
                                                ->select('stakeholder_id as id')->get();

                            //para cada stakeholder con el rol seleccionado
                            foreach ($stakeholders as $stakeholder)
                            {
                                //obtenemos datos de stakeholder
                                $stakeholder = \Ermtool\Stakeholder::find($stakeholder->id);
                                try
                                {
                                    DB::table('poll_stakeholder')
                                        ->insert([
                                            'stakeholder_id' => $stakeholder->id,
                                            'poll_id' => $_POST['poll_id'],
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

                                    //ACT 13-07-18: De todas maneras enviamos correo
                                    if (Session::get('languaje') == 'en')
                                    {
                                        $errors->append("The poll has been send again to the user ".$stakeholder->name." ".$stakeholder->surnames.".");
                                    }
                                    else
                                    {
                                        $errors->append('Se ha enviado nuevamente la encuesta a '.$stakeholder->name.' '.$stakeholder->surnames.'.');
                                    }

                                    $correos[$i] = $stakeholder->mail;
                                }

                                if (isset($errors))
                                {
                                    Session::flash('error',$errors);
                                }
                                $i += 1;
                            }
                        }
                    }

                    //ACT 20-06-18
                    //obtenemos stakeholder (responsable) para enviarle un correo informando la situación

                    foreach ($correos as $c)
                    {
                        $user = \Ermtool\Stakeholder::getUserByMail($c);
                        $name = \Ermtool\Stakeholder::getName($user->id);

                        $mensaje = array();

                        //Hacemos un replace de nombre de stakeholder, nombre de plan de acción y días de vencimiento
                        $message = str_replace('{{ Usuario }}', $name, $_POST['mensaje']);

                        //Separamos en distintos mensajes
                        $mensaje = explode('<space>', $message);

                        Mail::queue('envio_mail',['mensaje' => $mensaje], function ($msj) use ($c)
                        {       
                            $msj->to($c)->subject($_POST['title']);
                        });

                    }
                    

                    

                    if (isset($errors))
                    {
                        if (sizeof($errors) != sizeof($_POST['stakeholder_id']) && $_POST['tipo'] == 0) //para verificar que se enviaron correos en caso de que se hayan asignado manualmente los stakeholders
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Poll successfully sent');
                            }
                            else
                            {
                                Session::flash('message','Encuesta enviada correctamente');    
                            }
                        }
                        else if (sizeof($errors) == sizeof($_POST['stakeholder_id']) && ($_POST['tipo'] == 1 || $_POST['tipo'] == 2)) //pueden ser iguales en tamaño en caso de que se esté enviando por organización o por rol
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Poll successfully sent');
                            }
                            else
                            {
                                Session::flash('message','Encuesta enviada correctamente');    
                            }
                        }
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Poll successfully sent');
                        }
                        else
                        {
                            Session::flash('message','Encuesta enviada correctamente');    
                        }
                    }
                });
            }

            return Redirect::to('enviar_encuesta');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }    
    }

    public function guardarEvaluacion($id)
    {
        try
        {
            DB::transaction(function() {
                $logger = $this->logger;

                if (isset($_POST['pregunta_id'])) //para verificar en caso de que sea una encuesta sin preguntas (mal hecha)
                {
                    foreach ($_POST['pregunta_id'] as $pregunta_id) //vemos cada pregunta
                    {
                        if (gettype($_POST['respuesta'.$pregunta_id]) == "array") //vemos si la respuesta es un array (caso de checkbox)
                        {
                            //si es checkbox, deberemos agregar cada respuesta
                            foreach ($_POST['respuesta'.$pregunta_id] as $respuesta)
                            {
                                //obtenemos valor de la respuesta -> de la tabla posible_answers
                                $resp = DB::table('posible_answers')->where('id',$respuesta)->value('answer');
                                
                                //agregamos respuesta
                                \Ermtool\Answer::create([
                                                'answer'=>$resp,
                                                'question_id'=>$pregunta_id,
                                                'stakeholder_id'=>$_POST['stakeholder_id'],
                                                ]);
                            }
                        }
                        else
                        {
                            //obtenemos valor de la respuesta -> de la tabla posible_answers
                            //ACTUALIZACIÓN 01-09-2017: forzamos int
                            $resp = DB::table('posible_answers')->where('id',(int)$_POST['respuesta'.$pregunta_id])->value('answer');
                                
                            //vemos si es radio button (debería existir $resp)
                            if ($resp)
                            {
                                //agregamos respuesta
                                \Ermtool\Answer::create([
                                                'answer'=>$resp,
                                                'question_id'=>$pregunta_id,
                                                'stakeholder_id'=>$_POST['stakeholder_id'],
                                                ]);
                            }
                            else //es texto
                            {
                                //agregamos respuesta
                                \Ermtool\Answer::create([
                                                'answer'=>$_POST['respuesta'.$pregunta_id],
                                                'question_id'=>$pregunta_id,
                                                'stakeholder_id'=>$_POST['stakeholder_id'],
                                                ]);
                            }
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

                    $stakeholder = \Ermtool\Stakeholder::getName($_POST['stakeholder_id']);

                    foreach ($_POST['pregunta_id'] as $pregunta_id)
                    {
                        $poll = \Ermtool\Poll::getPollByQuestion($pregunta_id);
                        break;
                    }

                    $logger->info('El usuario '.$stakeholder. ', Rut: '.$_POST['stakeholder_id'].', ha respondido la encuesta '.$poll->name.'con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','It could not answer a poll without questions');
                    }
                    else
                    {
                        Session::flash('error','No se puede responder una encuesta sin preguntas');
                    }
                }
                 
            });
            
            if (Session::get('languaje') == 'en')
            {
                return Redirect::to('encuestaresp');
            }
            else
            {
                return Redirect::to('encuestaresp');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function updateEvaluacion(Request $request, $id)
    {
        try
        {
            DB::transaction(function () {
                $logger = $this->logger;
                    foreach ($_POST['pregunta_id'] as $pregunta_id) //vemos cada pregunta
                    {
                        if (gettype($_POST['respuesta'.$pregunta_id]) == "array") //vemos si la respuesta es un array (caso de checkbox)
                        {
                            //primero eliminamos respuestas anteriores
                            $delete = DB::table('answers')
                                        ->where('question_id','=',$pregunta_id)
                                        ->where('stakeholder_id','=',$_POST['stakeholder_id'])
                                        ->delete();

                            //si es checkbox, deberemos agregar cada respuesta
                            foreach ($_POST['respuesta'.$pregunta_id] as $respuesta)
                            {
                                //obtenemos valor de la respuesta -> de la tabla posible_answers
                                $resp = DB::table('posible_answers')->where('id',$respuesta)->value('answer');
                                
                                //agregamos respuesta
                                \Ermtool\Answer::create([
                                                'answer'=>$resp,
                                                'question_id'=>$pregunta_id,
                                                'stakeholder_id'=>$_POST['stakeholder_id'],
                                                ]);
                            }
                        }
                        else
                        {
                            //obtenemos valor de la respuesta -> de la tabla posible_answers
                            //ACTUALIZACIÓN 01-09-2017: forzamos int
                            $resp = DB::table('posible_answers')->where('id',(int)$_POST['respuesta'.$pregunta_id])->value('answer');
                                
                            //vemos si es radio button (debería existir $resp)
                            if ($resp)
                            {
                                //modificamos respuesta
                                    DB::table('answers')
                                        ->where('question_id','=',$pregunta_id)
                                        ->where('stakeholder_id','=',$_POST['stakeholder_id'])
                                        ->update([
                                            'answer' => $resp
                                            ]);
                            }
                            else //es texto
                            {
                                //modificamos respuesta
                                    DB::table('answers')
                                        ->where('question_id','=',$pregunta_id)
                                        ->where('stakeholder_id','=',$_POST['stakeholder_id'])
                                        ->update([
                                            'answer' => $_POST['respuesta'.$pregunta_id]
                                            ]);
                            }
                        }
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Answers successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Respuestas modificadas correctamente');
                    }

                    foreach ($_POST['pregunta_id'] as $pregunta_id)
                    {
                        $poll = \Ermtool\Poll::getPollByQuestion($pregunta_id);
                        break;
                    }

                    $stakeholder = \Ermtool\Stakeholder::getName($_POST['stakeholder_id']);

                    $logger->info('El usuario '.$stakeholder. ', Rut: '.$_POST['stakeholder_id'].', ha modificado sus respuestas para la encuesta '.$poll->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });
             
            if (Session::get('languaje') == 'en')
            {
                return Redirect::to('encuestaresp');
            }
            else
            {
                return Redirect::to('encuestaresp');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function encuestaRespondida()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message',"The entered Id is not in our database");
                }
                else
                {
                    Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                } 
                        
                return Redirect::to('identificacion.encuesta.'.$request["encuesta_id"]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //función para ver las respuestas enviadas a las encuestas
    public function verEncuestas()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                
                $polls = \Ermtool\Poll::all();

                if (Session::get('languaje') == 'en')
                {
                    return view('en.encuestas.encuestas',['polls'=>$polls]);
                }
                else
                {
                     return view('encuestas.encuestas',['polls'=>$polls]);
                }    
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function encuestas2($id)
    {
        $poll = \Ermtool\Poll::find($id);

        $questions;

        $answers = $poll->answers;
        $stakeholders = $poll->stakeholders;

        if (Session::get('languaje') == 'en')
        {
            return view('en.encuestas.encuestas',['stakeholders'=>$stakeholders,'poll_id'=>$id,'answers'=>$answers]);
        }
        else
        {
            return view('encuestas.encuestas',['stakeholders'=>$stakeholders,'poll_id'=>$id,'answers'=>$answers]);
        }
    }

    //función para revisar encuestas en su format (NO respuestas)
    public function showEncuesta()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                if (isset($_GET['encuesta']))  //se seleccionó la encuesta a revisar
                {
                    
                }
                else
                {
                    //$polls = \Ermtool\Poll::lists('name','id');
                    $polls = array();
                    $encuestas = DB::table('polls')
                                    ->select('id','name','created_at')
                                    ->get();

                    $i = 0;
                    foreach ($encuestas as $poll)
                    {
                        $created_at = new DateTime($poll->created_at);
                        $created_at = date_format($created_at, 'd-m-Y');

                        $polls[$i] = [
                            'id' => $poll->id,
                            'name' => $poll->name,
                            'created_at' => $created_at
                        ];

                        $i += 1;
                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.encuestas.ver_encuestas',['polls'=>$polls]);
                    }
                    else
                    {
                        return view('encuestas.ver_encuestas',['polls'=>$polls]); 
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

    //ACTUALIZADO 31-08: SE MOSTRARÁ DE DISTINTA FORMA INDEX DE ENCUESTAS PARA PODER AGREGAR BOTÓN DE ELIMINAR (ya no será a través de select)
    public function showEncuesta2($id)
    {
        try
        {
            $poll = \Ermtool\Poll::find($id);

                //obtenemos preguntas
                $preguntas = DB::table('questions')->where('poll_id','=',$poll['id'])->get();

                $answers = array(); //almacenaremos aquí respuestas posibles para las preguntas
                $i = 0; //contador de respuestas
                foreach ($preguntas as $pregunta)
                {
                    if ($pregunta->answers_type != 0) //pregunta tiene alguna posible answer
                    {
                        $posible_answers = DB::table('posible_answers')->where('question_id',$pregunta->id)->get();

                        foreach ($posible_answers as $posible_answer)
                        {
                            $answers[$i] = array('id'=>$posible_answer->id,
                                                'respuesta'=>$posible_answer->answer,
                                                'question_id'=>$posible_answer->question_id);
                            $i += 1;
                        }
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.encuestas.ver_encuestas',['encuesta'=>$poll,'preguntas'=>$preguntas,'respuestas'=>$answers]);
                }
                else
                {
                    return view('encuestas.ver_encuestas',['encuesta'=>$poll,'preguntas'=>$preguntas,'respuestas'=>$answers]);
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
    //Muestra encuesta y respuestas enviadas por un usuario
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
                //obtenemos preguntas de encuesta
                //$questions = \Ermtool\Question::where('poll_id',$id)->get();
                $questions = DB::table('questions')->where('poll_id',$id)->get();

                //obtenemos stakeholder para luego mostrar sus datos
                $stakeholder = \Ermtool\Stakeholder::find($_GET['stakeholder_id']);

                //obtenemos rol o roles del stakeholder
                $roles = \Ermtool\Stakeholder::find($_GET['stakeholder_id'])->roles;

                //nombre de encuesta
                $encuesta = \Ermtool\Poll::where('id',$id)->value('name');

                $answers = array();
                $i = 0;
                foreach ($questions as $question)
                {
                    $answers[$i] = DB::table('answers')
                                    ->where('question_id',$question->id)
                                    ->where('stakeholder_id',$_GET['stakeholder_id'])
                                    ->get();

                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.encuestas.encuesta2',['questions'=>$questions,'answers'=>$answers,
                                                'stakeholder'=>$stakeholder,'encuesta'=>$encuesta,
                                                'roles'=>$roles]);
                }
                else
                {
                    return view('encuestas.encuesta2',['questions'=>$questions,'answers'=>$answers,
                                                'stakeholder'=>$stakeholder,'encuesta'=>$encuesta,
                                                'roles'=>$roles]);
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
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;

            DB::transaction(function() {
                $logger = $this->logger;
                //vemos si es que tiene alguna respuesta asociada
                $rev = DB::table('answers')
                        ->join('questions','questions.id','=','answers.question_id')
                        ->where('questions.poll_id','=',$GLOBALS['id1'])
                        ->select('answers.id')
                        ->get();

                if (empty($rev))
                {
                    //seleccionamos preguntas de la encuesta
                    $questions = DB::table('questions')
                        ->where('poll_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                    foreach ($questions as $q)
                    {
                        //obtenemos posible answer si es que hay
                        $posibles = DB::table('posible_answers')
                                    ->where('question_id','=',$q->id)
                                    ->get(['id']);

                        if (!empty($posibles))
                        {
                            //eliminamos posible answers
                            foreach ($posibles as $p)
                            {
                                DB::table('posible_answers')
                                    ->where('id','=',$p->id)
                                    ->delete();
                            }
                        }

                        //eliminamos ahora la pregunta
                        DB::table('questions')
                            ->where('id','=',$q->id)
                            ->delete();
                    }

                    $poll = \Ermtool\Poll::find($GLOBALS['id1']);

                    //primero eliminamos posible relación en poll_stakeholder (ACTUALIZACIÓN 23-01-17)
                    DB::table('poll_stakeholder')
                        ->where('poll_id','=',$GLOBALS['id1'])
                        ->delete();

                    //ahora eliminamos la encuesta
                    DB::table('polls')
                        ->where('id','=',$GLOBALS['id1'])
                        ->delete();

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la encuesta '.$poll->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                    $GLOBALS['res'] = 0;
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
}
