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
        
        //obtenemos riesgos generales
        //$riesgos_gral = DB::table('risks')->where('type2',1)->distinct()->lists('name','name');


        //obtenemos riesgos de objetivos (junto a su objetivo y organización)
        $riesgos_obj = DB::table('objective_risk')
                            ->join('risks','objective_risk.risk_id','=','risks.id')
                            ->join('objectives','objective_risk.objective_id','=','objectives.id')
                            ->join('organizations','objectives.organization_id','=','organizations.id')
                            ->select('objective_risk.id as id',
                                DB::raw('CONCAT(risks.name, " - ", objectives.name, " - ",organizations.name) AS name'))
                            ->lists('name','id');

        //obtenemos riesgos de subprocesos (junto al subproceso asociado)
        $riesgos_sub = DB::table('risk_subprocess')
                            ->join('risks','risk_subprocess.risk_id','=','risks.id')
                            ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                            ->select('risk_subprocess.id as id',
                                DB::raw('CONCAT(risks.name, " - ", subprocesses.name) AS name'))
                            ->lists('name','id');

        //juntamos riesgos
        //$riesgos = $riesgos_sub+$riesgos_obj; no se pueden juntar ya que se puede repetir id

        
        return view('evaluacion.crear_evaluacion',['riesgos_obj'=>$riesgos_obj,'riesgos_sub'=>$riesgos_sub]);
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
        if ($request['fecha_exp'] != "")
        {
            $fecha = explode("/",$request['fecha_exp']);
            $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }
        else
        {
            $fecha_exp = NULL;
        }

        //agregamos evaluación y obtenemos id
        $eval_id = DB::table('evaluations')->insertGetId([
            'name' => $request['nombre'],
            'description' => $request['descripcion'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'expiration_date' => $fecha_exp,
            'max_levels' => 5,
            ]);

        //ahora debemos agregar los riesgos en la tabla evaluation_risk

        if (isset($request['objective_risk_id'])) //insertamos primero riesgos de negocio -> si es que se agregaron
        {
            foreach ($request['objective_risk_id'] as $objective_risk_id)
            {
                //insertamos riesgo de negocio en evaluation_risk
                DB::table('evaluation_risk')->insert([
                    'evaluation_id' => $eval_id,
                    'objective_risk_id' => $objective_risk_id,
                    ]);
            }
        }

        if (isset($request['risk_subprocess_id'])) //ahora insertamos riesgos de subproceso (si es que se agregaron)
        {
            foreach ($request['risk_subprocess_id'] as $subprocess_risk_id)
            {
                //inseratmos riesgo de subproceso en evaluation_risk
                DB::table('evaluation_risk')->insert([
                    'evaluation_id' => $eval_id,
                    'risk_subprocess_id' => $subprocess_risk_id,
                    ]);
            }
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
        $poll = \Ermtool\Evaluation::find($id);

        $encuesta = array();

        $encuesta['id'] = $poll['id'];
        $encuesta['name'] = $poll['name'];
        $encuesta['description'] = $poll['description'];

        $encuesta['created_at'] = date_format($poll['created_at'],"d-m-Y");
        $encuesta['created_at'] .= " a las ".date_format($poll['created_at'],"H:i:s");

        if ($poll['expiration_date'] != NULL)
        {
            $encuesta['expiration_date'] = date_format($poll['expiration_date'],"d-m-Y");
            $encuesta['expiration_date'] .= " a las ".date_format($poll['expiration_date'],"H:i:s");
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

        /*
        $risks = array();
        $i = 0;


        //obtenemos riesgos
        foreach ($poll->risks as $risk)
        {
            $risks[$i] = array('risk_id'=>$risk['id'],
                                'nombre'=>$risk['name']);
            $i += 1;
        }
        */

        return view('evaluacion.show',['encuesta'=>$encuesta,'riesgos'=>$riesgos]);
    }

    public function enviar($id)
    {
        //Se debe inicializar en caso de que no haya sido ingresado ningún stakeholder aun
        //$stakeholders = \Ermtool\Stakeholder::lists('CONCAT(nombre, " ", apellidos)','id');
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
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
        $evaluation_risk = DB::table('evaluation_risk')->where('evaluation_id','=',$id)->get();
        $riesgos = array();
        $i = 0;
        
        //cada uno de los riesgos de la evaluación
        foreach ($evaluation_risk as $risk)
        {
            //-- vemos si es de proceso o de negocio --//
            if ($risk->risk_subprocess_id != NULL) //es de proceso
            {
                $sub = DB::table('risk_subprocess')
                        ->where('risk_subprocess.id','=',$risk->risk_subprocess_id)
                        ->join('risks','risk_subprocess.risk_id','=','risks.id')
                        ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                        ->select('risks.name as risk_name','subprocesses.name as subprocess_name')
                        ->get();

                //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                foreach ($sub as $sub)
                {
                    $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                        'risk_name' => $sub->risk_name,
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
                    $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                        'risk_name' => $neg->risk_name,
                                        'subobj' => $neg->organization_name);
                }
            }

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
            $correos[$i] = $stakeholders[$i]->mail;
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
            foreach ($request['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
            {
                /* NO ES NECESARIO LO ESTOY MANDANDO DESDE EL FORMULARIO 
                //primero obtenemos el id de la tabla evaluation_risk
                $evaluation_risk = DB::table('evaluation_risk')
                                        ->select('id')
                                        ->where('risk_id',$riesgo_id)
                                        ->where('evaluation_id',$request['evaluation_id'])->get(); */

                    DB::table('evaluation_risk_stakeholder')->insert([
                        'evaluation_risk_id'=>$evaluation_risk,
                        'stakeholder_id'=>$request['rut'],
                        'probability'=>$request['proba_'.$evaluation_risk],
                        'impact'=>$request['criticidad_'.$evaluation_risk]
                        ]);
            }


            Session::flash('message','Respuestas enviadas correctamente');
            return view('evaluacion.encuestaresp');
            //print_r($_POST);
        }
        else //no se encontró el rut ingresado
        {
            Session::flash('message','El rut ingresado no se encuentra en nuestra base de datos');
                return Redirect::to('evaluacion.encuesta.'.$request["evaluation_id"]);
        }
    }

    public function listHeatmap()
    {
        $encuestas = \Ermtool\Evaluation::lists('name','id');
        $organizaciones = \Ermtool\Organization::lists('name','id');
        return view('reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones]); 
    }

    public function generarHeatmap($evaluation_id)
    {
        if ($_POST['evaluation_id'] != "") //se selecciono ver mapa para encuesta específica
        {
            //---- consulta multiples join para obtener las respuestas relacionada a la encuesta ----// 
            $evaluations = DB::table('evaluation_risk')
                            ->where('evaluation_risk.evaluation_id',$_POST['evaluation_id'])
                            ->select('evaluation_risk.id','evaluation_risk.risk_id',
                                'evaluation_risk.objective_risk_id','evaluation_risk.risk_subprocess_id')->get();

            //obtenemos nombre y descripcion de la encuesta
            $datos = DB::table('evaluations')->where('id',$_POST['evaluation_id'])->select('name','description')->get();

            foreach ($datos as $datos)
            {
                 $nombre = $datos->name;
                 $descripcion = $datos->description;
            }
        }

        else if ($_POST['organization_id'] != "") //se seleccionó ver heatmap por organización
        {
             //---- consulta multiples join para obtener las respuestas relacionada a la organización ----// 
                $evaluations = DB::table('evaluation_risk')
                                ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                ->where('organizations.id','=',$_POST['organization_id'])
                                ->select('evaluation_risk.id','evaluation_risk.objective_risk_id')
                                ->groupBy('evaluation_risk.objective_risk_id')->get();

            //obtenemos nombre y descripción de organización
            $datos = DB::table('organizations')->where('id',$_POST['organization_id'])->select('name','description')->get();

            foreach ($datos as $datos)
            {
                 $nombre = $datos->name;
                 $descripcion = $datos->description;
            }
                        
        }

        $prom_proba = array();
        $prom_criticidad = array();
        $riesgos = array();
        $i = 0;
        foreach ($evaluations as $evaluation)
        {

            //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
            $prom_proba[$i] = DB::table('evaluation_risk')
                        ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                        ->where('evaluation_risk.objective_risk_id',$evaluation->objective_risk_id)
                        ->avg('probability');

            $prom_criticidad[$i] = DB::table('evaluation_risk')
                        ->join('evaluation_risk_stakeholder','evaluation_risk_stakeholder.evaluation_risk_id','=','evaluation_risk.id')
                        ->where('evaluation_risk.objective_risk_id',$evaluation->objective_risk_id)
                        ->avg('impact');

            /* Volvemos a verificar si se está viendo por organización o por encuesta, ya que si se está viendo por
            organización las variables risk_subprocess_id y risk_id no existirán (no son necesarias), y en vez de mostrar
            el nombre de la organización se mostrará el nombre del objetivo */
            if ($_POST['evaluation_id'] != "")
            {
                //primero verificamos de que tipo de riesgo se trata
                if($evaluation->risk_subprocess_id != NULL) //si es riesgo de subproceso
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de subproceso
                    $riesgo_temp = DB::table('risk_subprocess')
                                    ->where('risk_subprocess.id','=',$evaluation->risk_subprocess_id)
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->select('risks.name as name','subprocesses.name as subobj')->get();
                }

                else if ($evaluation->objective_risk_id != NULL) //es riesgo de negocio
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                    $riesgo_temp = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$evaluation->objective_risk_id)
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('organizations','organizations.id','=','objectives.organization_id')
                                    ->select('risks.name as name','organizations.name as subobj')->get();
                }

                else
                {
                    //aun no se soluciona para riesgos generales
                    $riesgo_temp = array();
                }
            }
            else
            {
                //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                    $riesgo_temp = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$evaluation->objective_risk_id)
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('organizations','organizations.id','=','objectives.organization_id')
                                    ->select('risks.name as name','objectives.name as subobj')->get();
            }
            foreach ($riesgo_temp as $temp) //el riesgo recién obtenido (de subproceso o negocio) es almacenado en riesgos
            {
                $riesgos[$i] = array('name' => $temp->name,
                                    'subobj' => $temp->subobj);
            }
            //obtenemos nombre del riesgo y lo guardamos en array de riesgo con foreach
            //$riesgos[$i] = \Ermtool\Risk::where('id',$evaluation->risk_id)->value('name');

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
