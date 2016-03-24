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
        if (isset($request['manual'])) //Se está evaluando manualmente
        {

            //agregamos evaluación y obtenemos id
            $eval_id = DB::table('evaluations')->insertGetId([
                'name' => "Evaluación Manual",
                'consolidation' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'max_levels' => 5,
                ]);

            //creamos atributo para identificar que la evaluación es manual 
            //(posteriormente al guardar los resultados para poder consolidarlos inmediatamente)
            
            //Guardaremos una variable de session con nombre type (NO ES NECESARIO)
            //Session::set('type',1);
        }

        else //se está creando encuesta de evaluación
        {

            //agregamos evaluación y obtenemos id
            $eval_id = DB::table('evaluations')->insertGetId([
                'name' => $_POST['nombre'],
                'consolidation' => 0,
                'description' => $_POST['descripcion'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'expiration_date' => $_POST['expiration_date'],
                'max_levels' => 5,
                ]);

            //Guardaremos una variable de session con nombre type (NO ES NECESARIO)
            //Session::set('type',0);
        }
            //ahora debemos agregar los riesgos en la tabla evaluation_risk

            //guardaremos los riesgos en un array en caso de que sea el ingreso manual
            //$obj_risks = array();
            //$sub_risks = array();

            if (isset($request['objective_risk_id'])) //insertamos primero riesgos de negocio -> si es que se agregaron
            {
                foreach ($request['objective_risk_id'] as $objective_risk_id)
                {
                    //guardamos en array
                    //$obj_risks = array('objective_risk_id'=>$objective_risk_id);

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
                    //guardamos en array
                    //$sub_risks = array('risk_subprocess_id'=>$subprocess_risk_id);

                    //inseratmos riesgo de subproceso en evaluation_risk
                    DB::table('evaluation_risk')->insert([
                        'evaluation_id' => $eval_id,
                        'risk_subprocess_id' => $subprocess_risk_id,
                        ]);
                }
            }

        if (isset($request['manual']))
        {
            return $this->generarEncuesta($eval_id);
        }
        else
        {
            Session::flash('message','Encuesta de evaluacion agregada correctamente');

            return Redirect::to('/evaluacion');
        }    

    }

    //Función que mostrará lista de encuestas agregadas
    public function encuestas()
    {
        $encuestas = \Ermtool\Evaluation::all()->where('consolidation',0); //se muestran las encuestas NO consolidadas
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
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->select('risks.name as risk_name','subprocesses.name as subprocess_name',
                                'processes.name as process_name')
                        ->get();

                //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                foreach ($sub as $sub)
                {
                    $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                        'risk_name' => $sub->risk_name,
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
                        ->select('risks.name as risk_name','organizations.name as organization_name',
                                'objectives.name as objective_name')
                        ->get();

                foreach ($neg as $neg)
                {
                    $riesgos[$i] = array('evaluation_risk_id' => $risk->id,
                                        'risk_name' => $neg->risk_name,
                                        'subobj' => $neg->objective_name,
                                        'orgproc' => $neg->organization_name);
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
                return Redirect::to('/evaluacion_encuestas');
    }

    public function guardarEvaluacion(Request $request)
    {
        //primero verificamos si el rut ingresado corresponde a algún stakeholder
        $stakeholder = \Ermtool\Stakeholder::find($request['rut']);

        if ($stakeholder) //si es que el rut ingresado es correcto, procedemos a guardar evaluación
        {
            foreach ($request['evaluation_risk_id'] as $evaluation_risk) //para cada riesgo de la encuesta hacemos un insert
            {
                    DB::table('evaluation_risk_stakeholder')->insert([
                        'evaluation_risk_id'=>$evaluation_risk,
                        'stakeholder_id'=>$request['rut'],
                        'probability'=>$request['proba_'.$evaluation_risk],
                        'impact'=>$request['criticidad_'.$evaluation_risk]
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
        //obtenemos encuestas distintas a las que corresponden a una evaluación manual
        $encuestas = \Ermtool\Evaluation::where('description','<>','NULL')->lists('name','id');
        $organizaciones = \Ermtool\Organization::lists('name','id');
        return view('reportes.heatmap',['encuestas'=>$encuestas,'organizaciones'=>$organizaciones]); 
    }

    public function generarHeatmap($evaluation_id)
    {
        //print_r($_POST);
        //Nombre y descripción de la encuesta u organización
        $nombre = "";
        $descripcion = "";
        $prom_proba = array();
        $prom_criticidad = array();
        $riesgo_temp = array();
        $riesgos = array();
        $i = 0;
        /* POR AHORA NO SE VERÁ HEATMAP POR ENCUESTA DE EVALUACIÓN --> PROBABLEMENTE NO ES NECESARIO
        if ($_POST['evaluation_id'] != "") //se seleccionó ver mapa para encuesta específica
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
        { */

            $ano = $_POST['ano'];

            if ($_POST['mes'] == NULL)
            {
                $mes = "12";
            }
            else
            {
                $mes = $_POST['mes'];
            }

            //obtenemos nombre y descripción de organización
            $datos = DB::table('organizations')->where('id',$_POST['organization_id'])->select('name','description')->get();

            foreach ($datos as $datos)
            {
                 $nombre = $datos->name;
                 $descripcion = $datos->description;
            }
            if ($_POST['kind'] == 0)
            {
                 //---- consulta multiples join para obtener los objetivos evaluados relacionados a la organización ----// 
                $evaluations = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                                ->whereNotNull('evaluation_risk.risk_subprocess_id')
                                ->where('organization_subprocess.organization_id','=',$_POST['organization_id'])
                                ->where('evaluations.consolidation','=',1)
                                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                                ->select('evaluation_risk.risk_subprocess_id as risk_id')
                                ->groupBy('risk_id')->get();

                foreach ($evaluations as $evaluation)
                {
                /* Volvemos a verificar si se está viendo por organización o por encuesta, ya que si se está viendo por
                organización las variables risk_subprocess_id y risk_id no existirán (no son necesarias), y en vez de mostrar
                el nombre de la organización se mostrará el nombre del objetivo. Además en caso de ser por organización las
                consultas son distintas */
                /* NO SE UTILIZARÁ POR AHORA
                if ($_POST['organization_id'] != "")
                {
                    /*Para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad.
                     Para esto, primero buscaremos la última consolidación realizada en el periodo ingresado, y luego 
                     tomaremos los atributos avg_probability y avg_impact de la última consolidación realizada para
                     los riesgos */
                    //volvemos a verificar tipo para consultas
                
                    $updated_at = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.risk_subprocess_id',$evaluation->risk_id)
                                ->where('evaluations.consolidation','=',1)
                                ->where('evaluations.updated_at','<',date($ano.'-'.$mes.'-31 23:59:59'))
                                ->max('evaluations.updated_at');

                    //obtenemos promedio de probabilidad e impacto
                    $proba_impacto= DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluations.updated_at','=',$updated_at)
                                ->where('evaluation_risk.risk_subprocess_id',$evaluation->risk_id)
                                ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->get();

                    //guardamos proba en $prom_proba
                    foreach ($proba_impacto as $probaimp)
                    {
                        $prom_proba[$i] = $probaimp->avg_probability;
                        $prom_criticidad[$i] = $probaimp->avg_impact;
                    }

                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                        $riesgo_temp = DB::table('risk_subprocess')
                                        ->where('risk_subprocess.id','=',$evaluation->risk_id)
                                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                        ->select('risks.name as name','risks.description as description',
                                                 'subprocesses.name as subobj')
                                        ->get();

                    foreach ($riesgo_temp as $temp) //el riesgo recién obtenido es almacenado en riesgos
                    {
                        $riesgos[$i] = array('name' => $temp->name,
                                            'subobj' => $temp->subobj,
                                            'description' => $temp->description);
                    }

                    $i += 1;
                }
            }
            else if ($_POST['kind'] == 1) //evaluaciones de riesgos de negocio
            {
                //---- consulta multiples join para obtener los objetivos evaluados relacionados a la organización ----// 
                $evaluations = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                ->where('organizations.id','=',$_POST['organization_id'])
                                ->where('evaluations.consolidation','=',1)
                                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                                ->select('evaluation_risk.objective_risk_id as risk_id')
                                ->groupBy('risk_id')->get();

                //print_r($evaluations);                     
    //   }
                foreach ($evaluations as $evaluation)
                {
                    $updated_at = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluation_risk.objective_risk_id',$evaluation->risk_id)
                                ->where('evaluations.consolidation','=',1)
                                ->where('evaluations.updated_at','<',date($ano.'-'.$mes.'-31 23:59:59'))
                                ->max('evaluations.updated_at');

                    //obtenemos promedio de probabilidad e impacto
                    $proba_impacto= DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluations.updated_at','=',$updated_at)
                                ->where('evaluation_risk.objective_risk_id',$evaluation->risk_id)
                                ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->get();

                    //guardamos proba en $prom_proba
                    foreach ($proba_impacto as $probaimp)
                    {
                        $prom_proba[$i] = $probaimp->avg_probability;
                        $prom_criticidad[$i] = $probaimp->avg_impact;
                    }

                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                    $riesgo_temp = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$evaluation->risk_id)
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->select('risks.name as name','risks.description as description','objectives.name as subobj')
                                    ->get();

                    foreach ($riesgo_temp as $temp) //el riesgo recién obtenido es almacenado en riesgos
                    {
                        $riesgos[$i] = array('name' => $temp->name,
                                            'subobj' => $temp->subobj,
                                            'description' => $temp->description);
                    }

                    $i += 1;
                }      
            }
        /*
            if ($_POST['evaluation_id'] != "")
            {

                //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
                $proba_impact = DB::table('evaluation_risk')
                        ->where('evaluation_risk.objective_risk_id',$evaluation->objective_risk_id)
                        ->where('evaluation_risk.risk_subprocess_id',$evaluation->risk_subprocess_id)
                        ->where('evaluation_risk.evaluation_id','=',$_POST['evaluation_id'])
                        ->select('avg_probability','avg_impact')->get();

                foreach($proba_impact as $proba_impacto)
                {
                    $prom_proba[$i] = $proba_impacto->avg_probability;
                    $prom_criticidad[$i] = $proba_impacto->avg_impact;
                }

                //primero verificamos de que tipo de riesgo se trata
                if($evaluation->risk_subprocess_id != NULL) //si es riesgo de subproceso
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de subproceso
                    $riesgo_temp = DB::table('risk_subprocess')
                                    ->where('risk_subprocess.id','=',$evaluation->risk_subprocess_id)
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->select('risks.name as name','risks.description as description','subprocesses.name as subobj')->get();
                }

                else if ($evaluation->objective_risk_id != NULL) //es riesgo de negocio
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                    $riesgo_temp = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$evaluation->objective_risk_id)
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('organizations','organizations.id','=','objectives.organization_id')
                                    ->select('risks.name as name','risks.description as description','organizations.name as subobj')->get();
                }

                else
                {
                    //aun no se soluciona para riesgos generales
                    $riesgo_temp = array();
                }
            } */

                        
       // }

        //retornamos la misma vista con datos
        return view('reportes.heatmap',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                        'riesgos'=>$riesgos,'prom_proba'=>$prom_proba,
                                        'prom_criticidad'=>$prom_criticidad,
                                        'kind' => $_POST['kind']]);
    }


    public function evaluacionManual()
    {
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

        
        return view('evaluacion.evaluacion_manual',['riesgos_obj'=>$riesgos_obj,'riesgos_sub'=>$riesgos_sub]);
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

        return view('evaluacion.consolidar',['evaluations_risks' => $evaluations_risks,'evaluation_id' => $id]);
        //print_r($evaluations_risks);

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

        Session::flash('message','Encuesta de evaluación consolidada correctamente');

        return Redirect::to('/evaluacion_encuestas');
    }
}
