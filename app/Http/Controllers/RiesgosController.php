<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use dateTime;

class RiesgosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $riesgos = array();
        $relacionados = array();
        $i = 0; //contador de riesgos

        $riesgos2 = \Ermtool\Risk::all()->where('type2',1); //Selecciona todos los riesgos ya identificados
        $j = 0; //contador de subprocesos u objetivos relacionados

        foreach ($riesgos2 as $riesgo)
        {
            //damos formato a tipo de riesgo
            if ($riesgo['type'] == 0)
            {
                $tipo = "De Proceso";
                //primero obtenemos subprocesos relacionados
                $subprocesses = \Ermtool\Risk::find($riesgo['id'])->subprocesses;

                foreach($subprocesses as $subprocess)
                {
                    //agregamos org_name ya que este estará identificado si el riesgo es de negocio
                    $relacionados[$j] = array('risk_id'=>$riesgo['id'],
                                        'id'=>$subprocess['id'],
                                        'nombre'=>$subprocess['name'],
                                        'org_name'=>"");
                    $j += 1;
                }
            }
            else if ($riesgo['type'] == 1)
            {
                $tipo = "De Negocio";
                //primero obtenemos objetivos relacionados
                $objectives = \Ermtool\Risk::find($riesgo['id'])->objectives;

                foreach ($objectives as $objective)
                {
                    //obtenemos organización
                    $org = \Ermtool\Organization::where('id',$objective['organization_id'])->value('name');
                    $relacionados[$j] = array('risk_id'=>$riesgo['id'],
                                            'id'=>$objective['id'],
                                            'nombre'=>$objective['name'],
                                            'org_name'=>$org);
                    $j += 1;
                }
            }

            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($riesgo['created_at'] == NULL OR $riesgo['created_at'] == "0000-00-00" OR $riesgo['created_at'] == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                $fecha_creacion = date_format($riesgo['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($riesgo['created_at'],"H:i:s");
            }

            //damos formato a fecha expiración
            if ($riesgo['expiration_date'] == NULL OR $riesgo['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else
            { 
                $expiration_date = new DateTime($riesgo['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            //damos formato a fecha de actualización 
            if ($riesgo['updated_at'] != NULL)
            {
                $fecha_act = date_format($riesgo['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($riesgo['updated_at'],"H:i:s");
            }

            //obtenemos nombre de categoría
            $categoria = \Ermtool\Risk_Category::where('id',$riesgo['risk_category_id'])->value('name');

            //obtenemos nombre de causa
            if ($riesgo['cause_id'] != NULL)
            {
                $causa = \Ermtool\Cause::where('id',$riesgo['cause_id'])->value('name');
            }
            else
                $causa = "No especifica";

            //obtenemos nombre de efecto
            if ($riesgo['effect_id'] != NULL)
            {
                $efecto = \Ermtool\Effect::where('id',$riesgo['effect_id'])->value('name');
            }
            else
                $efecto = "No especifica";

            $riesgos[$i] = array('id'=>$riesgo['id'],
                                'nombre'=>$riesgo['name'],
                                'descripcion'=>$riesgo['description'],
                                'tipo'=>$tipo,
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'causa'=>$causa,
                                'efecto'=>$efecto);

            $i += 1;

        }

        return view('riesgos.index',['riesgos'=>$riesgos,'relacionados'=>$relacionados]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //categorias de riesgo
        $categorias = \Ermtool\Risk_Category::where('status',0)->lists('name','id');

        //causas preingresadas
        $causas = \Ermtool\Cause::where('status',0)->lists('name','id');

        //efectos preingresados
        $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');

        //riesgos tipo
        $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

        //obtenemos lista de stakeholders
        $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        if(isset($_GET['P']))
        {
            $subprocesos = \Ermtool\Subprocess::where('status',0)->lists('name','id');
            return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                    'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo]);
        }

        else if (isset($_GET['N']))
        {
            
                /* Consulta de tipo :
                            SELECT objectives.id,objectives.name,organizations.name 
                            FROM organizations,objectives WHERE organizations.id = objectives.organization_id
                Con Query Builder, para obtener los objetivos con su organizacion asociada en forma de lista */

            $objetivos = DB::table('objectives')
                            ->join('organizations','objectives.organization_id','=','organizations.id')
                            ->select('objectives.id as id',
                                DB::raw('CONCAT(objectives.name, " - ", organizations.name) AS name'))
                            ->lists('name','id');
            
            return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                    'efectos'=>$efectos,'objetivos'=>$objetivos,'riesgos_tipo'=>$riesgos_tipo,
                    'stakeholders'=>$stakeholders]);
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

        //creamos una transacción para cumplir con atomicidad
        DB::transaction(function()
        {
                //vemos si es de proceso o de negocio
                    if (isset($_POST['subprocess_id']))
                    {
                        $type = 0;
                    }
                    else if (isset($_POST['objective_id']))
                    {
                        $type = 1;
                    }

            /* POR EL MOMENTO NO IMPORTA YA QUE EL USUARIO PUEDE MODIFICAR LOS DATOS DE UN RIESGO TIPO (AL IDENTIFICAR RIESGO)
                //Verificamos si se ingreso un riesgo tipo
                if ($request['risk_id'] != "")
                {
                    $new_risk = \Ermtool\Risk::find($request['risk_id']);

                    //creamos nuevo riesgo con los mismos datos
                    \Ermtool\Risk::create([
                        'name'=>$new_risk->name,
                        'description'=>$new_risk->description,
                        'type'=>$type,
                        'type2'=>1,
                        'expiration_date'=>$new_risk->expiration_date,
                        'risk_category_id'=>$new_risk->risk_category_id,
                        'cause_id'=>$new_risk->cause_id,
                        'effect_id'=>$new_risk->effect_id,
                        ]);
                }
            */
                    
                     //vemos si se agrego alguna causa nueva
                    if (isset($_POST['causa_nueva']))
                    {
                        $cause = \Ermtool\Cause::create([
                            'name'=>$_POST['causa_nueva']
                        ]);

                        //obtenemos id de causa recien agregada
                        $causa = $cause->id;
                    }
                    //agregamos la causa previamente creada, o en su defecto NULL
                    else
                    {
                        if ($_POST['cause_id'] == NULL)
                            $causa = NULL;
                        else
                            $causa = $_POST['cause_id'];
                    }

                    //vemos si se agrego algún efecto nuevo
                    if (isset($_POST['efecto_nuevo']))
                    {
                        $effect = \Ermtool\Effect::create([
                            'name'=>$_POST['efecto_nuevo']
                            ]);

                        //obtenemos id de efecto agregado
                        $efecto = $effect->id;
                    }
                    //agregamos efecto previamente creado, o en su defecto NULL
                    else
                    {
                        if ($_POST['effect_id'] == NULL)
                            $efecto = NULL;
                        else
                            $efecto = $_POST['effect_id'];
                    }

                    $new_risk = \Ermtool\Risk::create([
                        'name'=>$_POST['name'],
                        'description'=>$_POST['description'],
                        'type'=>$type,
                        'type2'=>1,
                        'expiration_date'=>$_POST['expiration_date'],
                        'risk_category_id'=>$_POST['risk_category_id'],
                        'stakeholder_id'=>$_POST['stakeholder_id'],
                        'cause_id'=>$causa,
                        'effect_id'=>$efecto,
                        'expected_loss'=>$_POST['expected_loss'],
                        ]);

                //agregamos en tabla risk_subprocess o objective_risk
                //obtenemos id de riesgo recien ingresado
                $risk = $new_risk->id;

                if ($type == 0)
                {        
                    //agregamos en tabla risk_subprocess

                    foreach ($_POST['subprocess_id'] as $subprocess_id)
                    {
                        $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                        $subprocess->risks()->attach($risk);
                    }       
                }

                else if ($type == 1)
                {
                    //agregamos en tabla objective_risk

                    foreach ($_POST['objective_id'] as $objective_id)
                    {
                        $objective = \Ermtool\Objective::find($objective_id);
                        $objective->risks()->attach($risk);
                    }       
                }

                Session::flash('message','Riesgo agregado correctamente');
        });

        return Redirect::to('/riesgos');
    }

    public function setRiesgoTipo($id)
    {
        
        $riesgo = \Ermtool\Risk::find($id);

        $datos = ['name'=>$riesgo['name'],'description'=>$riesgo['description'],
                    'risk_category_id'=>$riesgo['risk_category_id'],
                    'expiration_date'=>$riesgo['expiration_date'],
                    'cause_id'=>$riesgo['cause_id'],'effect_id'=>$riesgo['effect_id']];

        return json_encode($datos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //categorias de riesgo
        $categorias = \Ermtool\Risk_Category::where('status',0)->lists('name','id');

        //causas preingresadas
        $causas = \Ermtool\Cause::where('status',0)->lists('name','id');

        //efectos preingresados
        $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');

        //riesgos tipo
        $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

        $risk = \Ermtool\Risk::find($id);
        return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,
                                    'categorias'=>$categorias,'efectos'=>$efectos]);
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
        global $id1;
        $id1 = $id;
        //creamos una transacción para cumplir con atomicidad
        DB::transaction(function()
        {
                $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
                    
                     //vemos si se agrego alguna causa nueva
                    if (isset($_POST['causa_nueva']))
                    {
                        $new_causa = \Ermtool\Cause::create([
                            'name'=>$_POST['causa_nueva']
                        ]);

                        //obtenemos id de causa recien agregada
                        $causa = $new_causa->id;
                    }
                    //agregamos la causa previamente creada, o en su defecto NULL
                    else
                    {
                        if ($_POST['cause_id'] == NULL)
                            $causa = NULL;
                        else
                            $causa = $_POST['cause_id'];
                    }

                    //vemos si se agrego algún efecto nuevo
                    if (isset($_POST['efecto_nuevo']))
                    {
                        $new_efecto = \Ermtool\Effect::create([
                            'name'=>$request['efecto_nuevo']
                            ]);

                        //obtenemos id de efecto agregado
                        $efecto = $new_efecto->id;
                    }
                    //agregamos efecto previamente creado, o en su defecto NULL
                    else
                    {
                        if ($_POST['effect_id'] == NULL)
                            $efecto = NULL;
                        else
                            $efecto = $_POST['effect_id'];
                    }

                $riesgo->name = $_POST['name'];
                $riesgo->description = $_POST['description'];
                $riesgo->expiration_date = $_POST['expiration_date'];
                $riesgo->type2 = 1;
                $riesgo->risk_category_id = $_POST['risk_category_id'];
                $riesgo->cause_id = $causa;
                $riesgo->effect_id = $efecto;
                $riesgo->expected_loss = $_POST['expected_loss'];

                $riesgo->save();

                Session::flash('message','Riesgo actualizado correctamente');
        });

        return Redirect::to('/riesgos');
    }

    //matriz de riesgos
    public function matrices()
    {
        return view('reportes.matriz_riesgos');
    }

    public function generarMatriz($value)
    {
        $i = 0; //contador de controles/subprocesos o controles/objetivos
        $datos = array();

        if ($value == 0) //Se generará la matriz de controles de procesos
        {

            //---------- OBS: EXISTE PROBLEMA SI ES QUE EL RIESGO NO CONTIENE CAUSA Y EFECTO --------//
            $risks = DB::table('risk_subprocess')
                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                ->join('processes','subprocesses.process_id','=','processes.id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->join('risk_categories','risk_categories.id','=','risks.risk_category_id')
                                ->where('risks.type2','=',1)
                                ->select('risks.*',
                                        'subprocesses.name as subprocess_name',
                                        'processes.name as process_name',
                                        'risk_categories.name as risk_category_name',
                                        'risk_subprocess.id as risk_subprocess_id')
                                ->get();
        }

        else if ($value == 1) //Se generará matriz para riesgos de negocio
        {
            $risks = DB::table('objective_risk')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->join('risk_categories','risk_categories.id','=','risks.risk_category_id')
                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                ->where('risks.type2','=',1)
                                ->select('risks.*',
                                        'objectives.name as objective_name',
                                        'organizations.name as organization_name',
                                        'risk_categories.name as risk_category_name',
                                        'objective_risk.id as objective_risk_id')
                                ->get();
        }

        foreach ($risks as $risk)
        {
                $controles = NULL;
                $causas = NULL;
                $efectos = NULL;
                $probabilidad = "No tiene evaluación";
                $impacto = "No tiene evaluación";
                $score = "No tiene evaluación";
                // -- seteamos datos --//
                //seteamos causa y efecto
                if ($risk->cause_id != NULL)
                {
                    $causes = DB::table('causes')
                            ->where('causes.id','=',$risk->cause_id)
                            ->select('causes.name')
                            ->get();

                    foreach ($causes as $cause)
                    {
                        $causas .= '<li>'.$cause->name.'</li>';
                    }
                }
                else
                {
                    $causas = "No tiene causa definida";
                }

                if ($risk->effect_id != NULL)
                {
                    $effects = DB::table('effects')
                            ->where('effects.id','=',$risk->cause_id)
                            ->select('effects.name')
                            ->get();

                    foreach ($effects as $effect)
                    {
                        $efectos .= '<li>'.$cause->name.'</li>';
                    }
                }
                else
                {
                    $efectos = "No tiene efecto definido";
                }

                

                if ($value == 0) //controles y eval para riesgos de proceso
                {
                    //primero obtenemos maxima fecha de evaluacion para el riesgo
                    $fecha = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.risk_subprocess_id','=',$risk->risk_subprocess_id)
                                    ->max('evaluations.updated_at');

                    //obtenemos proba, impacto y score
                    $eval_risk = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.risk_subprocess_id','=',$risk->risk_subprocess_id)
                                    ->where('evaluations.updated_at','=',$fecha)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->get();

                    foreach ($eval_risk as $eval)
                    {
                        if ($eval->avg_probability != NULL AND $eval->avg_impact != NULL)
                        {
                            $impacto = $eval->avg_impact;
                            $probabilidad = $eval->avg_probability;
                            $score = $impacto * $probabilidad;
                        }
                    }
                    //obtenemos controles
                    $controls = DB::table('controls')
                                    ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                                    ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->where('risks.id','=',$risk->id)
                                    ->select('controls.name')
                                    ->get();
                }               
                else if ($value == 1) //controles y eval para riesgos de negocio
                {
                    //primero obtenemos maxima fecha de evaluacion para el riesgo
                    $fecha = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.objective_risk_id','=',$risk->objective_risk_id)
                                    ->max('evaluations.updated_at');

                    //obtenemos proba, impacto y score
                    $eval_risk = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.objective_risk_id','=',$risk->objective_risk_id)
                                    ->where('evaluations.updated_at','=',$fecha)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->get();

                    foreach ($eval_risk as $eval)
                    {
                        $impacto = $eval->avg_impact;
                        $probabilidad = $eval->avg_probability;
                        $score = $impacto * $probabilidad;
                    }

                    //obtenemos controles
                    $controls = DB::table('controls')
                                    ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                                    ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->where('risks.id','=',$risk->id)
                                    ->select('controls.name')
                                    ->get();
                }

                //seteamos controles
                if ($controls == NULL)
                {
                    $controles = "No se han especificado controles";
                }
                else
                {
                    foreach ($controls as $control)
                    {
                        $controles .= '<li>'.$control->name.'</li>';
                    }
                }
                /* IMPORTANTE!!!
                    Los nombres de las variables serán guardados en español para mostrarlos
                    en el archivo excel que será exportado
                */
                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($risk->created_at == NULL OR $risk->created_at == "0000-00-00" OR $risk->created_at == "")
                {
                    $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
                }

                else
                {
                    //primero sacamos la hora
                    $fecha_temp1 = explode(' ',$risk->created_at);

                    //sacamos solo fecha y ordenamos
                    $fecha_temp2 = explode('-',$fecha_temp1[0]);

                    //ponemos fecha
                    $fecha_creacion = $fecha_temp2[2].'-'.$fecha_temp2[1].'-'.$fecha_temp2[0].' a las '.$fecha_temp1[1];
                }

                //damos formato a fecha expiración
                if ($risk->expiration_date == NULL OR $risk->expiration_date == "0000-00-00")
                {
                    $expiration_date = "Ninguna";
                }
                else
                { 
                    //sacamos solo fecha y ordenamos
                    $fecha_temp1 = explode('-',$risk->expiration_date);
                    $expiration_date = $fecha_temp1[2].'-'.$fecha_temp1[1].'-'.$fecha_temp1[0];
                }

                //Seteamos datos
                if ($value == 0) //guardamos datos de riesgos de procesos
                {
                    $datos[$i] = [//'id' => $control->id,
                                'Proceso' => $risk->process_name,
                                'Subproceso' => $risk->subprocess_name,
                                'Riesgo' => $risk->name,
                                'Descripción' => $risk->description,
                                'Categoría' => $risk->risk_category_name,
                                'Causas' => $causas,
                                'Efectos' => $efectos,
                                'Pérdida_esperada' => $risk->expected_loss,
                                'Probabilidad' => $probabilidad,
                                'Impacto' => $impacto,
                                'Score' => $score,
                                'Fecha_identificación' => $fecha_creacion,
                                'Fecha_expiración' => $expiration_date,
                                'Controles' => $controles,];
                    $i += 1;
                }

                else if ($value == 1) //guardamos datos de riesgos de negocio
                {
                    $datos[$i] = [//'id' => $control->id,
                                'Organización' => $risk->organization_name,
                                'Objetivo' => $risk->objective_name,
                                'Riesgo' => $risk->name,
                                'Descripción' => $risk->description,
                                'Categoría' => $risk->risk_category_name,
                                'Causas' => $causas,
                                'Efectos' => $efectos,              
                                'Pérdida_esperada' => $risk->expected_loss,
                                'Probabilidad' => $probabilidad,
                                'Impacto' => $impacto,
                                'Score' => $score,
                                'Fecha_identificación' => $fecha_creacion,
                                'Fecha_expiración' => $expiration_date,
                                'Controles' => $controles];
                    $i += 1;
                }
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $datos;
        }
        else
            return json_encode($datos);
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
