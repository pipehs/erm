<?php

namespace Ermtool\Http\Controllers;

//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Mail;
use DB;

class EncuestasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            else if ($request['destinatarios'] == 2) //Se enviará por cargo
            {
                $dest = DB::table('stakeholders')->distinct('tipo')->lists('tipo','tipo');
            }

            $encuesta = \Ermtool\Poll::find($request['encuesta']);

            //obtenemos preguntas
            ////////////////////////////////////////////$preguntas = \Ermtool\Question::all();


            //var_dump($preguntas->id);
            //obtenemos posible answers

            //$respuestas = \Ermtool\Posible_answer::where('question_id',$preguntas['id']);

            return view('identificacion_eventos_riesgos.enviarencuesta2',['tipo'=>$request['destinatarios'],
                'encuesta'=>$encuesta,'dest'=>$dest]);
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
