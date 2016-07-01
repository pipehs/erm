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

class EncuestasController extends Controller
{

    public function mensaje($id)
    {
            //Mensaje predeterminado al enviar encuestas
        $mensaje = "Estimado Usuario.

                    Le enviamos la siguiente encuesta para la identificación de eventos de riesgos. 
                    Responda cada una de las preguntas asociadas a la encuesta. 
                    Para responderla deberá acceder al siguiente link.

                    http://erm.local/public/identificacion.encuesta.{$id}

                    Saludos cordiales,
                    Administrador.";
        return $mensaje;
    }

    //Muestra las encuestas en su formato (No respuestas)
    public function index()
    {
        
    }


    public function enviar(Request $request)
    {
        if (isset($_GET['aplicar']))
        {
            //$tipo = Request::input('destinatarios');

            if ($request['destinatarios'] == 0) //Se asignaran los destinatarios manualmente
            {
                //seleccionamos lista de stakeholders
                 $dest = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
                                                        ->orderBy('name')
                                                        ->lists('full_name', 'id');
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

            return view('identificacion_eventos_riesgos.enviarencuesta2',['tipo'=>$request['destinatarios'],
                'dest'=>$dest,'encuesta'=>$encuesta,'preguntas'=>$preguntas,'respuestas'=>$answers,
                'mensaje'=>$this->mensaje($encuesta['id'])]);
        }
        else if (isset($_GET['volver']))
        {
            $polls = \Ermtool\Poll::lists('name','id');

            return view('identificacion_eventos_riesgos.enviarencuesta',['polls'=>$polls]);
        }
        else
        {
            $polls = \Ermtool\Poll::lists('name','id');
            return view('identificacion_eventos_riesgos.enviarencuesta',['polls'=>$polls]);
        }
    }

    public function create()
    {
        if (isset($_POST['agregar'])) //se agregaron las preguntas, ahora se deben agregar las respuestas
        { /* NO CUMPLE AISLAMIENTO 
            \Ermtool\Poll::create([
                'name'=>$_POST['nombre'],
                ]); */
            $cont = $_POST['cantidad_preguntas'];
            //obtenemos id de esta encuesta
            $poll_id = \Ermtool\Poll::max('id');
            return view('identificacion_eventos_riesgos.crearencuesta2',['cont'=>$cont,'name'=>$_POST['nombre']]);
        }
        else
        {
            return view('identificacion_eventos_riesgos.crearencuesta');
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
        DB::transaction(function() {
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
                        while (isset($_POST['pregunta'.$i.'_alternativa'.$j])) //mientras hayan alternativas para la pregunta
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
        });
        
        return view('identificacion_eventos_riesgos.encuestacreada',['post'=>$_POST]);
    }

    //función que primero verificará que el usuario no haya respondido previamente (si es que respondio permitirá editar sus respuestas)
    public function verificadorUserEncuesta($id)
    {
        $encuesta = \Ermtool\Poll::find($id);

        return view('identificacion_eventos_riesgos.verificar_encuesta',['encuesta'=>$encuesta]);
    }

    public function generarEncuesta()
    {
        $encuesta = \Ermtool\Poll::find($_POST['encuesta_id']);

        //primero, verificamos que el usuario exista
        $user = DB::table('poll_stakeholder')
                    ->where('poll_id','=',$_POST['encuesta_id'])
                    ->where('stakeholder_id','=',$_POST['id'])
                    ->select('stakeholder_id')
                    ->first();

        if (!$user)
        {
            Session::flash('error','La encuesta no ha sido enviada al usuario ingresado');
            return view('identificacion_eventos_riesgos.verificar_encuesta',['encuesta'=>$encuesta]);
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

            return view('identificacion_eventos_riesgos.encuesta',['encuesta'=>$encuesta,'preguntas'=>$preguntas,
                'respuestas'=>$answers,'user_answers'=>$user_answers,'user'=>$user->stakeholder_id]);
        }
    }

    //Función para enviar correo con link a la encuesta
    public function enviarCorreo(Request $request)
    {
        //guardamos en un array todos los correos de los stakeholders
        $correos = array();
        $stakeholders = array();
        $i = 0;

        //OBS: STAKEHOLDER_ID[] PUEDE SER STAKEHOLDERS, ORGANIZACIONES O TIPO DE STAKEHOLDERS
        if ($request['tipo'] == 0) //Se asignaron stakeholders manualmente
        {
            foreach ($request['stakeholder_id'] as $stakeholder_id)
            {
                
                $stakeholder = \Ermtool\Stakeholder::find($stakeholder_id);

                //ALMACENAMOS EN POLL_STAKEHOLDER (funcionalidad agregada 13-05-2016) PARA SABER A QUIENES SE ENVÍA LA ENCUESTA. OBS: Debemos ver que el usuario no exista
                try
                {
                    DB::table('poll_stakeholder')
                        ->insert([
                            'stakeholder_id' => $stakeholder->id,
                            'poll_id' => $request['poll_id'],
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

                    $errors->append('Ya se le envió la encuesta al usuario '.$stakeholder->name.' '.$stakeholder->surnames.'. No se puede enviar nuevamente.');
                }

                if (isset($errors))
                {
                    Session::flash('error',$errors);
                }
                $i += 1;
            }
        }
        else if ($request['tipo'] == 1) //Se asignaron stakeholders por organización
        {
            foreach ($request['stakeholder_id'] as $stakeholder_id)
            {
                //obteneos los id de stakeholders de la organizacion
                $stakeholders = DB::table('organization_stakeholder')
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
                                'poll_id' => $request['poll_id'],
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

                        $errors->append('Ya se le envió la encuesta al usuario '.$stakeholder->name.' '.$stakeholder->surnames.'. No se puede enviar nuevamente.');
                    }

                    if ($errors)
                    {
                        Session::flash('error',$errors);
                    }
                    $i += 1;
                }
            }
        }
        else if ($request['tipo'] == 2) //Se asignaron stakeholders por rol
        {
            foreach ($request['stakeholder_id'] as $stakeholder_id) //para cada id de rol
            {
                //obtenemos los id de stakeholders del rol seleccionado
                $stakeholders = DB::table('role_stakeholder')
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
                                'poll_id' => $request['poll_id'],
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

                        $errors->append('Ya se le envió la encuesta al usuario '.$stakeholder->name.' '.$stakeholder->surnames.'. No se puede enviar nuevamente.');
                    }

                    if ($errors)
                    {
                        Session::flash('error',$errors);
                    }
                    $i += 1;
                }
            }
        }

        Mail::send('envio_mail',$request->all(), 
            function ($msj) use ($correos)
            {
                $msj->subject('Encuesta identificación de eventos de Riesgos');
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

        Session::flash('message','Encuesta enviada correctamente');
                return Redirect::to('enviar_encuesta');
    }

    public function guardarEvaluacion(Request $request)
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
                                        'stakeholder_id'=>$request['stakeholder_id'],
                                        ]);
                    }
                }
                else
                {
                    //obtenemos valor de la respuesta -> de la tabla posible_answers
                        $resp = DB::table('posible_answers')->where('id',$_POST['respuesta'.$pregunta_id])->value('answer');
                        
                    //vemos si es radio button (debería existir $resp)
                    if ($resp)
                    {
                        //agregamos respuesta
                        \Ermtool\Answer::create([
                                        'answer'=>$resp,
                                        'question_id'=>$pregunta_id,
                                        'stakeholder_id'=>$request['stakeholder_id'],
                                        ]);
                    }
                    else //es texto
                    {
                        //agregamos respuesta
                        \Ermtool\Answer::create([
                                        'answer'=>$_POST['respuesta'.$pregunta_id],
                                        'question_id'=>$pregunta_id,
                                        'stakeholder_id'=>$request['stakeholder_id'],
                                        ]);
                    }
                }
            }

            //echo "Encuesta agregada con éxito";
            ///////////////////// MEJORAR MENSAJE ////////////////////////

            Session::flash('message','Respuestas enviadas correctamente');
            return view('evaluacion.encuestaresp');
    }

    public function updateEvaluacion(Request $request)
    {
        DB::transaction(function () {
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
                        $resp = DB::table('posible_answers')->where('id',$_POST['respuesta'.$pregunta_id])->value('answer');
                        
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

            Session::flash('message','Respuestas modificadas correctamente');
        });
            
            return view('evaluacion.encuestaresp');
    }

    public function encuestaRespondida()
    {
        Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                return Redirect::to('identificacion.encuesta.'.$request["encuesta_id"]);
    }

    //función para ver las respuestas enviadas a las encuestas
    public function verEncuestas()
    {
        if (isset($_GET['encuesta']))  //se seleccionó la encuesta a revisar
        {
            $poll = \Ermtool\Poll::find($_GET['encuesta']);

            $questions;

            $answers = $poll->answers;
            $stakeholders = $poll->stakeholders;

            return view('identificacion_eventos_riesgos.encuestas',['stakeholders'=>$stakeholders,'poll_id'=>$_GET['encuesta'],'answers'=>$answers]);
        }
        else
        {
            $polls = \Ermtool\Poll::lists('name','id');
            return view('identificacion_eventos_riesgos.encuestas',['polls'=>$polls]);    
        }
        
    }

    //función para revisar encuestas en su format (NO respuestas)
    public function showEncuesta()
    {
        if (isset($_GET['encuesta']))  //se seleccionó la encuesta a revisar
        {
            $poll = \Ermtool\Poll::find($_GET['encuesta']);

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

            return view('identificacion_eventos_riesgos.ver_encuestas',['encuesta'=>$poll,'preguntas'=>$preguntas,'respuestas'=>$answers]);
        }
        else
        {
            $polls = \Ermtool\Poll::lists('name','id');
            return view('identificacion_eventos_riesgos.ver_encuestas',['polls'=>$polls]);
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

        return view('identificacion_eventos_riesgos.encuesta2',['questions'=>$questions,'answers'=>$answers,
                                        'stakeholder'=>$stakeholder,'encuesta'=>$encuesta,
                                        'roles'=>$roles]);
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
        //
    }
}
