<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Mail;
use Session;
use Redirect;
use DB;

class EvaluacionRiesgosController extends Controller
{
    public function mensaje($id)
    {
            //Mensaje predeterminado al enviar encuestas
        $mensaje = "Estimado Usuario.

                    Le enviamos la siguiente encuesta para la evaluación de riesgos. Ud deberá asignar un valor de probabilidad y criticidad para cada uno de los riesgos asociados a la encuesta. Para responderla deberá acceder al siguiente link.

                    http://erm.local/public/evaluacion.encuesta.{$id}

                    Saludos cordiales,
                    Administrador.";
        return $mensaje;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $riesgos = \Ermtool\Risk::where('tipo2',1)->lists('nombre','id');
        return view('evaluacion.crear_evaluacion',['riesgos'=>$riesgos]);
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
         //obtenemos orden correcto de fecha creación
        $fecha = explode("/",$request['fecha_creacion']);
        $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];

        //obtenemos orden correcto de fecha expiración
        if ($request['fecha_exp'] != "")
        {
            $fecha = explode("/",$request['fecha_exp']);
            $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }
        else
        {
            $fecha_exp = NULL;
        }

        \Ermtool\Evaluation::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            'max_niveles' => $request['niveles'],
            ]);

        //ahora debemos agregar los riesgos en la tabla evaluation_risk_stakeholder
        //Primero obtenemos id de evaluación recién agregada

        $eval_id = \Ermtool\Evaluation::max('id');

        foreach ($request['risk_id'] as $risk_id)
        {
            $risk = \Ermtool\Risk::find($risk_id);
            //agregamos la relación (para agregar en atributos)
            $risk->evaluations()->attach($eval_id);
        }

        Session::flash('message','Encuesta de evaluacion agregada correctamente');

        return Redirect::to('/evaluacion');

    }

    //Función que mostrará lista de encuestas agregadas
    public function encuestas()
    {
        $encuestas = \Ermtool\Evaluation::all();
        return view('evaluacion.encuestas',['encuestas'=>$encuestas]);
    }

    public function show($id)
    {
        $encuesta = \Ermtool\Evaluation::find($id);

        $risks = array();
        $i = 0;
        //obtenemos riesgos
        foreach ($encuesta->risks as $risk)
        {
            $risks[$i] = array('risk_id'=>$risk['id'],
                                'nombre'=>$risk['nombre']);
            $i += 1;
        }

        return view('evaluacion.show',['encuesta'=>$encuesta,'riesgos'=>$risks]);
    }

    public function enviar($id)
    {
        //Se debe inicializar en caso de que no haya sido ingresado ningún stakeholder aun
        //$stakeholders = \Ermtool\Stakeholder::lists('CONCAT(nombre, " ", apellidos)','id');
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(nombre, " ", apellidos) AS full_name'))
        ->orderBy('nombre')
        ->lists('full_name', 'id');
        return view('evaluacion.enviar',['encuesta_id'=>$id,'stakeholders'=>$stakeholders,'mensaje'=>$this->mensaje($id)]);
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

    //función que generará la encuesta para que el usuario pueda responderla
    public function generarEncuesta($id)
    {
        $encuesta = \Ermtool\Evaluation::find($id);
        $riesgos = array();
        $i = 0;
        foreach ($encuesta->risks as $risk)
        {
             $riesgos[$i] = array('risk_id'=>$risk['id'],
                                'nombre'=>$risk['nombre']);
            $i += 1; 
        }

        return view('evaluacion.encuesta',['encuesta'=>$encuesta,'riesgos'=>$riesgos]);
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
                $msj->subject('Encuesta evaluación de Riesgos');
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
                return Redirect::to('/evaluacion.encuestas');
    }

    public function guardarEvaluacion(Request $request)
    {
        //primero verificamos si el rut ingresado corresponde a algún stakeholder
        $stakeholder = \Ermtool\Stakeholder::find($request['rut']);

        if ($stakeholder) //si es que el rut ingresado es correcto, procedemos a guardar evaluación
        {
            foreach ($request['riesgos_id'] as $riesgo_id) //para cada riesgo de la encuesta hacemos un insert
            {
                //primero obtenemos el id de la tabla evaluation_risk
                $evaluation_risk = DB::table('evaluation_risk')
                                        ->select('id')
                                        ->where('risk_id',$riesgo_id)
                                        ->where('evaluation_id',$request['evaluation_id'])->get();

                foreach ($evaluation_risk as $eval_risk)
                {
                    DB::table('evaluation_risk_stakeholder')->insert([
                        'evaluation_risk_id'=>$eval_risk->id,
                        'stakeholder_id'=>$request['rut'],
                        'probabilidad'=>$request['proba_'.$riesgo_id],
                        'criticidad'=>$request['criticidad_'.$riesgo_id]
                        ]);
                }
            }


            Session::flash('message','Respuestas enviadas correctamente');
            return view('evaluacion.encuestaresp');
            //print_r($_POST);
            //echo "Encuesta agregada con éxito";
            ///////////////////// MEJORAR MENSAJE ////////////////////////
        }
        else
        {
            Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                return Redirect::to('evaluacion.encuesta.'.$request["evaluation_id"]);
        }
    }

    public function listHeatmap()
    {
        $encuestas = \Ermtool\Evaluation::lists('nombre','id');
        $organizaciones = \Ermtool\Organization::lists('nombre','id');
        return view('reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones]); 
    }

    public function generarHeatmap($evaluation_id)
    {
        //---- consulta multiples join para obtener las respuestas relacionada a la encuesta ----// 
        $evaluations = DB::table('evaluation_risk')
                        ->where('evaluation_risk.evaluation_id',$_POST['evaluation_id'])
                        ->select('evaluation_risk.id','evaluation_risk.risk_id')->get();

        //obtenemos nombre y descripcion de la encuesta
        $datos = DB::table('evaluations')->where('id',$_POST['evaluation_id'])->select('nombre','descripcion')->get();

        foreach ($datos as $datos)
        {
             $nombre = $datos->nombre;
             $descripcion = $datos->descripcion;
        }

        $prom_proba = array();
        $prom_criticidad = array();
        $riesgos = array();
        $i = 0;
        foreach ($evaluations as $evaluation)
        {

            //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
            $prom_proba[$i] = DB::table('evaluation_risk_stakeholder')
                        ->where('evaluation_risk_id',$evaluation->id)
                        ->avg('probabilidad');

            $prom_criticidad[$i] = DB::table('evaluation_risk_stakeholder')
                        ->where('evaluation_risk_id',$evaluation->id)
                        ->avg('criticidad');

            //obtenemos nombre del riesgo y lo guardamos en array de riesgo con foreach
            $riesgos[$i] = \Ermtool\Risk::where('id',$evaluation->risk_id)->value('nombre');

       /*
            echo 
                 "Riesgo: ".$riesgos[$i]."<br>".
                 "Proba: ".$prom_proba[$i]."<br>".
                 "Criti: ".$prom_criticidad[$i]."<hr>"; */
            
            $i += 1;
        }

        //retornamos la misma vista con datos
        return view('reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                        'riesgos'=>$riesgos,'prom_proba'=>$prom_proba,
                                        'prom_criticidad'=>$prom_criticidad]);
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
