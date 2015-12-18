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
                 $dest = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(nombre, " ", apellidos) AS full_name'))
                                                        ->orderBy('nombre')
                                                        ->lists('full_name', 'id');
            }
            else if ($request['destinatarios'] == 1) //Se enviará por organizacion
            {
                $dest = \Ermtool\Organization::lists('nombre','id');
            }
            else if ($request['destinatarios'] == 2) //Se enviará por tipo / rol
            {
                $dest = DB::table('stakeholders')->distinct('tipo')->lists('tipo','tipo');
            }

            $encuesta = \Ermtool\Poll::find($request['encuesta']);

            //obtenemos preguntas
            $preguntas = DB::table('questions')->where('poll_id','=',$encuesta['id'])->get();

            $answers = array(); //almacenaremos aquí respuestas posibles para las preguntas
            $i = 0; //contador de respuestas
            foreach ($preguntas as $pregunta)
            {
                if ($pregunta->tipo_respuestas != 0) //pregunta tiene alguna posible answer
                {
                    $posible_answers = DB::table('posible_answers')->where('question_id',$pregunta->id)->get();

                    foreach ($posible_answers as $posible_answer)
                    {
                        $answers[$i] = array('id'=>$posible_answer->id,
                                            'respuesta'=>$posible_answer->respuesta,
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
            $polls = \Ermtool\Poll::lists('nombre','id');

            return view('identificacion_eventos_riesgos.enviarencuesta',['polls'=>$polls]);
        }
        else
        {
            $polls = \Ermtool\Poll::lists('nombre','id');
            return view('identificacion_eventos_riesgos.enviarencuesta',['polls'=>$polls]);
        }
    }

    public function create()
    {
        if (isset($_POST['agregar'])) //se agregaron las preguntas, ahora se deben agregar las respuestas
        {
            \Ermtool\Poll::create([
                'nombre'=>$_POST['nombre'],
                'fecha_creacion'=>date('Y-m-d'),
                ]);
            $cont = $_POST['cantidad_preguntas'];

            //obtenemos id de esta encuesta
            $poll_id = \Ermtool\Poll::max('id');
            return view('identificacion_eventos_riesgos.crearencuesta2',['cont'=>$cont,'poll_id'=>$poll_id]);
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
        $cont = $_POST['contpreguntas'];
        
        //agregamos preguntas y tipos de respuesta
        for ($i=1; $i<=$cont; $i++)
        {
            \Ermtool\Question::create([
                'pregunta'=>$request['pregunta'.$i],
                'tipo_respuestas'=>$request['tipo_respuesta'.$i],
                'poll_id'=>$request['poll_id'],
                ]);

            //vemos tipo de respuesta para ver si se debe agregar en posible answers
            if ($request['tipo_respuesta'.$i] != 0) //significa que es radio o checkbox
            {
                //obtenemos id de pregunta recién ingresada
                $question_id = \Ermtool\Question::max('id');

                $j = 1; //contador cantidad de alternativas para cada pregunta
                while (isset($_POST['pregunta'.$i.'_alternativa'.$j])) //mientras hayan alternativas para la pregunta
                {
                    \Ermtool\Posible_answer::create([
                        'respuesta'=>$request['pregunta'.$i.'_alternativa'.$j],
                        'question_id'=>$question_id,
                        ]);
                    $j += 1;
                }

            // echo "Cantidad de alternativas para pregunta ".$i.": ".($j-1)."<br>";
            }
        }

        return view('identificacion_eventos_riesgos.encuestacreada',['post'=>$_POST]);
    }

    //función que generará la encuesta para que el usuario pueda responderla
    public function generarEncuesta($id)
    {
        $encuesta = \Ermtool\Poll::find($id);

        //obtenemos preguntas
        $preguntas = DB::table('questions')->where('poll_id','=',$encuesta['id'])->get();

        $answers = array(); //almacenaremos aquí respuestas posibles para las preguntas
        $i = 0; //contador de respuestas
        foreach ($preguntas as $pregunta)
        {
            if ($pregunta->tipo_respuestas != 0) //pregunta tiene alguna posible answer
            {
                $posible_answers = DB::table('posible_answers')->where('question_id',$pregunta->id)->get();

                foreach ($posible_answers as $posible_answer)
                {
                    $answers[$i] = array('id'=>$posible_answer->id,
                                        'respuesta'=>$posible_answer->respuesta,
                                        'question_id'=>$posible_answer->question_id);
                    $i += 1;
                }
            }
        }

        return view('identificacion_eventos_riesgos.encuesta',['encuesta'=>$encuesta,'preguntas'=>$preguntas,
            'respuestas'=>$answers]);
    }

    //Función para enviar correo con link a la encuesta
    public function enviarCorreo(Request $request)
    {
        //guardamos en un array todos los correos de los stakeholders
        $correos = array();
        $stakeholders = array();
        $i = 0;
        foreach ($request['stakeholder_id'] as $stakeholder_id)
        {
            $stakeholders[$i] = \Ermtool\Stakeholder::find($stakeholder_id);
            $correos[$i] = $stakeholders[$i]->correo;
            $i += 1;
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
        //primero verificamos si el rut ingresado corresponde a algún stakeholder
        $stakeholder = \Ermtool\Stakeholder::find($request['id']);

        if ($stakeholder) //si es que el rut ingresado es correcto, procedemos a guardar evaluación
        {
            foreach ($_POST['pregunta_id'] as $pregunta_id) //vemos cada pregunta
            {
                if (gettype($_POST['respuesta'.$pregunta_id]) == "array") //vemos si la respuesta es un array (caso de checkbox)
                {
                    //si es checkbox, deberemos agregar cada respuesta
                    foreach ($_POST['respuesta'.$pregunta_id] as $respuesta)
                    {
                        //obtenemos valor de la respuesta -> de la tabla posible_answers
                        $resp = DB::table('posible_answers')->where('id',$respuesta)->value('respuesta');
                        
                        //agregamos respuesta
                        \Ermtool\Answer::create([
                                        'respuesta'=>$resp,
                                        'question_id'=>$pregunta_id,
                                        'stakeholder_id'=>$request['id'],
                                        ]);
                    }
                }
                else
                {
                    //obtenemos valor de la respuesta -> de la tabla posible_answers
                        $resp = DB::table('posible_answers')->where('id',$_POST['respuesta'.$pregunta_id])->value('respuesta');
                        
                        //agregamos respuesta
                        \Ermtool\Answer::create([
                                        'respuesta'=>$resp,
                                        'question_id'=>$pregunta_id,
                                        'stakeholder_id'=>$request['id'],
                                        ]);
                }
            }

            echo "Encuesta agregada con éxito";
            ///////////////////// MEJORAR MENSAJE ////////////////////////
        }
        else
        {
            Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                return Redirect::to('identificacion.encuesta.'.$request["encuesta_id"]);
        }
    }

    public function encuestaRespondida()
    {

    }

    //función para ver las respuestas enviadas a las encuestas
    public function verEncuestas()
    {
        if (isset($_GET['encuesta']))  //se seleccionó la encuesta a revisar
        {
            $poll = \Ermtool\Poll::find($_GET['encuesta']);

            $questions;

            $answers = $poll->answers;

            $ruts = array(); //guardaremos los distintos rut de stakeholders
            $i = 0;
            foreach ($answers as $answer)
            {
                $ruts[$i] = $answer['stakeholder_id'];
                $i += 1;
            }

            $ruts = array_unique($ruts); //seleccionamos de array stakeholders solo los que son distintos (para no repetir)

            //obtenemos todos los datos de stakeholders (para mostrar)
            $stakeholders = array();
            $i = 0;
            foreach ($ruts as $rut)
            {
                $stakeholders[$i] = \Ermtool\Stakeholder::find($rut);
                $i += 1;
            }

            return view('reportes.encuestas',['stakeholders'=>$stakeholders,'poll_id'=>$_GET['encuesta']]);
        }
        else
        {
            $polls = \Ermtool\Poll::lists('nombre','id');
            return view('reportes.encuestas',['polls'=>$polls]);    
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
        //obtenemos preguntas de encuesta
        //$questions = \Ermtool\Question::where('poll_id',$id)->get();
        $questions = DB::table('questions')->where('poll_id',$id)->get();

        //obtenemos stakeholder para luego mostrar sus datos
        $stakeholder = \Ermtool\Stakeholder::find($_GET['stakeholder_id']);

        //nombre de encuesta
        $encuesta = \Ermtool\Poll::where('id',$id)->value('nombre');

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

        return view('reportes.encuesta',['questions'=>$questions,'answers'=>$answers,
                                        'stakeholder'=>$stakeholder,'encuesta'=>$encuesta]);
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
