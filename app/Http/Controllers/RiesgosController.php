<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use dateTime;
use Auth;

class RiesgosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //AGREGADO 26-07 SELECCIONAR PRIMERO ORGANIZACIÓN
    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.riesgos.index',['organizations' => $organizations]);
            }
            else
            {
                return view('riesgos.index',['organizations' => $organizations]);
            }
        }
    }

    public function index2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            $org = \Ermtool\Organization::find($_GET['organization_id']);

            $riesgos = array();
            $relacionados = array();
            $i = 0; //contador de riesgos

            $riesgos_temp1 = DB::table('risks')
                        ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                        ->where('risks.type2','=',1)
                        ->groupBy('risks.id')
                        ->select('risks.*')
                        ->get(); //Selecciona todos los riesgos ya identificados de la org seleccionada

            $riesgos_temp2 = DB::table('risks')
                        ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$_GET['organization_id'])
                        ->where('risks.type2','=',1)
                        ->groupBy('risks.id')
                        ->select('risks.*')
                        ->get();

            $riesgos2 = array_merge($riesgos_temp1,$riesgos_temp2); //juntamos todos los riesgos de la org.
            $j = 0; //contador de subprocesos u objetivos relacionados

            foreach ($riesgos2 as $riesgo)
            {
                //damos formato a tipo de riesgo
                if ($riesgo->type == 0)
                {
                    $tipo = 0;
                    //primero obtenemos subprocesos relacionados
                    //$subprocesses = \Ermtool\Risk::find($riesgo['id'])->subprocesses;
                    $subprocesses = DB::table('subprocesses')
                                    ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('risk_subprocess.risk_id','=',$riesgo->id)
                                    ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                                    ->select('subprocesses.name','subprocesses.id')
                                    ->get();

                    foreach($subprocesses as $subprocess)
                    {
                        //agregamos org_name ya que este estará identificado si el riesgo es de negocio
                        $relacionados[$j] = array('risk_id'=>$riesgo->id,
                                            'id'=>$subprocess->id,
                                            'nombre'=>$subprocess->name);
                        $j += 1;
                    }
                }
                else if ($riesgo->type == 1)
                {
                    $tipo = 1;
                    //primero obtenemos objetivos relacionados
                    //$objectives = \Ermtool\Risk::find($riesgo['id'])->objectives;
                    $objectives = DB::table('objectives')
                                    ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                                    ->where('objective_risk.risk_id','=',$riesgo->id)
                                    ->where('objectives.organization_id','=',$_GET['organization_id'])
                                    ->select('objectives.name','objectives.id')
                                    ->get();

                    foreach ($objectives as $objective)
                    {

                        //obtenemos organización
                        //$org = \Ermtool\Organization::where('id',$objective['organization_id'])->value('name');
                        $relacionados[$j] = array('risk_id'=>$riesgo->id,
                                                'id'=>$objective->id,
                                                'nombre'=>$objective->name);

                        $j += 1;
                    }
                }

                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($riesgo->created_at == NULL OR $riesgo->created_at == "0000-00-00" OR $riesgo->created_at == "")
                {
                    $fecha_creacion = NULL;
                }

                else
                {
                    $fecha_creacion = new DateTime($riesgo->created_at);
                    $fecha_creacion = date_format($fecha_creacion,"d-m-Y");
                }

                //damos formato a fecha expiración
                if ($riesgo->expiration_date == NULL OR $riesgo->expiration_date == "0000-00-00")
                {
                    $fecha_exp = NULL;
                }
                else
                { 
                    $expiration_date = new DateTime($riesgo->expiration_date);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                }

                //damos formato a fecha de actualización 
                if ($riesgo->updated_at != NULL)
                {
                    $fecha_act = new DateTime($riesgo->updated_at);
                    $fecha_act = date_format($fecha_act,"d-m-Y");
                }

                //obtenemos nombre de categoría
                $categoria = \Ermtool\Risk_category::where('id',$riesgo->risk_category_id)->value('name');

                //obtenemos causas si es que tiene
                $causes = DB::table('cause_risk')
                            ->join('causes','causes.id','=','cause_risk.cause_id')
                            ->where('cause_risk.risk_id','=',$riesgo->id)
                            ->select('causes.name')
                            ->get();

                if ($causes)
                {
                    $causas = array();
                    $k = 0;
                    foreach ($causes as $cause)
                    {
                        $causas[$j] = $cause->name;
                        $k += 1;
                    }
                }
                else
                {
                    $causas = NULL;
                }

                $stakeholder = DB::table('stakeholders')
                                    ->where('id',$riesgo->stakeholder_id)
                                    ->select('name','surnames')
                                    ->first();

                if (!$stakeholder)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $stakeholder = (object) array('name'=>'No','surnames'=>'specified');
                    }
                    else
                    {
                        $stakeholder = (object) array('name'=>'No','surnames'=>'especificado');
                    }
                }

                //obtenemos efectos si es que existen
                $effects = DB::table('effect_risk')
                            ->join('effects','effects.id','=','effect_risk.effect_id')
                            ->where('effect_risk.risk_id','=',$riesgo->id)
                            ->select('effects.name')
                            ->get();

                if ($effects)
                {
                    $efectos = array();
                    $k = 0;
                    foreach ($effects as $effect)
                    {
                        $efectos[$j] = $effect->name;
                        $k += 1;
                    }
                }
                else
                {
                    $efectos = NULL;
                }

                $riesgos[$i] = array('id'=>$riesgo->id,
                                    'nombre'=>$riesgo->name,
                                    'descripcion'=>$riesgo->description,
                                    'tipo'=>$tipo,
                                    'fecha_creacion'=>$fecha_creacion,
                                    'stakeholder'=>$stakeholder->name.' '.$stakeholder->surnames,
                                    'fecha_exp'=>$fecha_exp,
                                    'categoria'=>$categoria,
                                    'causas'=>$causas,
                                    'efectos'=>$efectos);

                $i += 1;

            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.riesgos.index',['riesgos'=>$riesgos,'relacionados'=>$relacionados,'organizations' => $organizations,'org_selected' => $org->name, 'org_id' => $org->id]);
            }
            else
            {
                return view('riesgos.index',['riesgos'=>$riesgos,'relacionados'=>$relacionados,'organizations' => $organizations,'org_selected' => $org->name, 'org_id' => $org->id]);
            }
            //return json_encode(['riesgos'=>$riesgos,'relacionados'=>$relacionados]);
            //print_r($relacionados);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //categorias de riesgo
            $categorias = \Ermtool\Risk_category::where('status',0)->lists('name','id');

            //causas preingresadas
            $causas = \Ermtool\Cause::where('status',0)->lists('name','id');

            //efectos preingresados
            $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');

            //riesgos tipo
            $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

            //obtenemos lista de stakeholders
            $stakeholders = \Ermtool\Stakeholder::listStakeholders($_GET['org']);

            if(isset($_GET['P']))
            {
                //ACTUALIZACIÓN 26-07: SOLO MOSTRAMOS PROCESOS PERTENECIENTES A LA EMPRESA QUE SE ESTÁ CONSULTANDO
                //$subprocesos = \Ermtool\Subprocess::where('status',0)->lists('name','id');
                $subprocesos = DB::table('subprocesses')
                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                ->where('subprocesses.status','=',0)
                                ->lists('subprocesses.name','subprocesses.id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                        'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo,
                        'stakeholders'=>$stakeholders,'org_id' => $_GET['org']]);
                }
                else
                {
                    return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                        'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo,
                        'stakeholders'=>$stakeholders,'org_id' => $_GET['org']]);
                }
            }

            else if (isset($_GET['N']))
            {
                
                $objectives = DB::table('objectives')
                                ->where('organization_id','=',$_GET['org'])
                                ->where('status','=',0)
                                ->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                            'efectos'=>$efectos,'objetivos'=>$objectives,'riesgos_tipo'=>$riesgos_tipo,
                            'stakeholders'=>$stakeholders,'org_id' => $_GET['org']]);
                }
                else
                {
                    return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                            'efectos'=>$efectos,'objetivos'=>$objectives,'riesgos_tipo'=>$riesgos_tipo,
                            'stakeholders'=>$stakeholders,'org_id' => $_GET['org']]);
                }
            }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
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

                        if (!isset($_POST['stakeholder_id']) || $_POST['stakeholder_id'] == "")
                        {
                            $stake = NULL;
                        }
                        else
                        {
                            $stake = $_POST['stakeholder_id'];
                        }

                        $risk = \Ermtool\Risk::create([
                            'name'=>$_POST['name'],
                            'description'=>$_POST['description'],
                            'type'=>$type,
                            'type2'=>1,
                            'expiration_date'=>$_POST['expiration_date'],
                            'risk_category_id'=>$_POST['risk_category_id'],
                            'stakeholder_id'=>$stake,
                            'expected_loss'=>$_POST['expected_loss'],
                            ]);

                        //vemos si se agrego alguna causa nueva
                        if (isset($_POST['causa_nueva']))
                        {
                            $new_causa = \Ermtool\Cause::create([
                                'name'=>$_POST['causa_nueva']
                            ]);

                            //guardamos en cause_risk
                            DB::table('cause_risk')
                                ->insert([
                                    'risk_id' => $risk->id,
                                    'cause_id' => $new_causa->id,
                                    ]);
                        }
                        else //se están agregando causas ya creadas
                        {
                            if (isset($_POST['cause_id']))
                            {
                                foreach ($_POST['cause_id'] as $cause_id)
                                {
                                    //insertamos cada causa en cause_risk
                                    DB::table('cause_risk')
                                        ->insert([
                                            'risk_id' => $risk->id,
                                            'cause_id' => $cause_id
                                            ]);
                                }
                            } 
                        }

                        //vemos si se agrego algún efecto nuevo
                        if (isset($_POST['efecto_nuevo']))
                        {
                            $new_effect = \Ermtool\Effect::create([
                                'name'=>$_POST['efecto_nuevo']
                                ]);

                             //guardamos en cause_risk
                            DB::table('effect_risk')
                                ->insert([
                                    'risk_id' => $risk->id,
                                    'effect_id' => $new_effect->id,
                                    ]);
                        }
                        else
                        {
                            if (isset($_POST['effect_id']))
                            {
                                foreach ($_POST['effect_id'] as $effect_id)
                                {
                                    //insertamos cada causa en cause_risk
                                    DB::table('effect_risk')
                                        ->insert([
                                            'risk_id' => $risk->id,
                                            'effect_id' => $effect_id
                                            ]);
                                }
                            } 
                        }

                    //agregamos en tabla risk_subprocess o objective_risk
                    //obtenemos id de riesgo recien ingresado
                    $risk = $risk->id;

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

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Risk successfully created');
                    }
                    else
                    {
                        Session::flash('message','Riesgo agregado correctamente');
                    }
            });

            return Redirect::to('riesgos.index2?organization_id='.$_POST['org_id']);
        }
    }

    //setea datos de un riesgo tipo cuando se está identificando un riesgo
    public function setRiesgoTipo($id)
    {
        
        $riesgo = \Ermtool\Risk::find($id);

        //obtenemos causas y efectos de riesgo tipo
        $causes = $riesgo->causes;
        $effects = $riesgo->effects;

        $datos = ['name'=>$riesgo['name'],'description'=>$riesgo['description'],
                    'risk_category_id'=>$riesgo['risk_category_id'],
                    'expiration_date'=>$riesgo['expiration_date'],
                    'causes'=>$causes,'effects'=>$effects];

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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos riesgo
            $risk = \Ermtool\Risk::find($id);
            if ($risk->type == 0) //es de proceso
            {
                $subprocesos = DB::table('subprocesses')
                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                ->where('subprocesses.status','=',0)
                                ->lists('subprocesses.name','subprocesses.id');

                $sub_selected = array();
                $subs = DB::table('risk_subprocess')
                            ->where('risk_subprocess.risk_id','=',$id)
                            ->select('risk_subprocess.subprocess_id')
                            ->get();

                $i = 0;
                foreach ($subs as $sub)
                {
                    $sub_selected[$i] = $sub->subprocess_id;
                    $i += 1;
                }
            }
            else if ($risk->type == 1) //es de negocio
            {
                $objectives = DB::table('objectives')
                                ->where('organization_id','=',$_GET['org'])
                                ->where('status','=',0)
                                ->lists('name','id');

                $obj_selected = array();
                $orgs = DB::table('objective_risk')
                            ->where('objective_risk.risk_id','=',$id)
                            ->select('objective_risk.objective_id')
                            ->get();

                $i = 0;
                foreach ($subs as $sub)
                {
                    $obj_selected[$i] = $obj->objective_id;
                    $i += 1;
                }
            }
            //categorias de riesgo
            $categorias = \Ermtool\Risk_category::where('status',0)->lists('name','id');
            //causas
            $causas = \Ermtool\Cause::where('status',0)->lists('name','id');
            //efectos
            $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');
            $causes_selected = array();
            $effects_selected = array();
            //obtenemos causas seleccionadas
            $causes = DB::table('cause_risk')
                                ->where('risk_id','=',$id)
                                ->select('cause_risk.cause_id')
                                ->get();

            $i = 0;
            foreach ($causes as $cause)
            {
                $causes_selected[$i] = $cause->cause_id;
                $i += 1;
            }

            //obtenemos efectos seleccionados
            $effects = DB::table('effect_risk')
                            ->where('risk_id','=',$id)
                            ->select('effect_risk.effect_id')
                            ->get();

            $i = 0;
            foreach ($effects as $effect)
            {
                $effects_selected[$i] = $effect->effect_id;
                $i += 1;
            }
            //riesgos tipo
            $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

            //obtenemos lista de stakeholders
            $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');

            if (Session::get('languaje') == 'en')
            {
                if ($risk->type == 0)
                {
                    return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,
                                        'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders,
                                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,
                                        'subprocesos' => $subprocesos,'sub_selected' => $sub_selected,'org_id' => $_GET['org']]);
                }
                else
                {
                    return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,
                                        'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders,
                                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,
                                        'objetivos' => $objetivos,'obj_selected' => $obj_selected,'org_id' => $_GET['org']]);
                }
            }
            else
            {
                if ($risk->type == 0)
                {
                    return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,
                                        'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders,
                                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,
                                        'subprocesos' => $subprocesos,'sub_selected' => $sub_selected,'org_id' => $_GET['org']]);
                }
                else
                {
                    return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,
                                        'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders,
                                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,
                                        'objetivos' => $objetivos,'obj_selected' => $obj_selected,'org_id' => $_GET['org']]);
                }
            }
        }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
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
                            'name'=>$_POST['causa']
                        ]);

                        //guardamos en cause_risk
                        DB::table('cause_risk')
                            ->insert([
                                'risk_id' => $riesgo->id,
                                'cause_id' => $new_causa->id,
                                ]);
                    }
                    else //se están agregando causas ya creadas
                    {
                        if (isset($_POST['cause_id']))
                        {
                            foreach ($_POST['cause_id'] as $cause_id)
                            {
                                //primero buscamos si es que existe previamente
                                $cause = DB::table('cause_risk')
                                    ->where('cause_id','=',$cause_id)
                                    ->where('risk_id','=',$riesgo->id)
                                    ->first();

                                if (!$cause) //no existe, por lo que se agrega
                                {
                                    DB::table('cause_risk')
                                    ->insert([
                                        'risk_id' => $riesgo->id,
                                        'cause_id' => $cause_id
                                        ]);
                                }
                            }
                        } 
                    }

                    //vemos si se agrego algún efecto nuevo
                    if (isset($_POST['efecto_nuevo']))
                    {

                        $new_effect = \Ermtool\Effect::create([
                            'name'=>$_POST['efecto']
                            ]);

                         //guardamos en cause_risk
                        DB::table('effect_risk')
                            ->insert([
                                'risk_id' => $riesgo->id,
                                'effect_id' => $new_effect->id,
                                ]);
                    }
                    else //efectos existentes
                    {
                        if (isset($_POST['effect_id']))
                        {
                            foreach ($_POST['effect_id'] as $effect_id)
                            {
                                //primero buscamos si es que existe previamente
                                $effect = DB::table('effect_risk')
                                    ->where('effect_id','=',$effect_id)
                                    ->where('risk_id','=',$riesgo->id)
                                    ->first();

                                if (!$effect) //no existe, por lo que se agrega
                                {
                                    //insertamos cada causa en cause_risk
                                    DB::table('effect_risk')
                                        ->insert([
                                            'risk_id' => $riesgo->id,
                                            'effect_id' => $effect_id
                                            ]);
                                }
                            }
                        } 
                    }

                    //ahora recorreremos todas las causas y efectos de este riesgo, para saber si es que no se borró alguna
                    $causas = DB::table('cause_risk')
                                ->where('risk_id','=',$riesgo->id)
                                ->select('cause_id')
                                ->get();

                    foreach($causas as $cause)
                    {
                        $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                        //ahora recorremos todas las causas que se agregaron para comparar
                        foreach ($_POST['cause_id'] as $cause_add)
                        {
                            if ($cause_add == $cause->cause_id)
                            {
                                $cont += 1;
                            }
                        }

                        if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                        {
                            DB::table('cause_risk')
                                ->where('risk_id','=',$riesgo->id)
                                ->where('cause_id','=',$cause->cause_id)
                                ->delete();
                        }
                    }

                    //lo mismo ahora para efectos
                    $efectos = DB::table('effect_risk')
                                ->where('risk_id','=',$riesgo->id)
                                ->select('effect_id')
                                ->get();

                    foreach($efectos as $effect)
                    {
                        $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                        //ahora recorremos todas las causas que se agregaron para comparar
                        foreach ($_POST['effect_id'] as $effect_add)
                        {
                            if ($effect_add == $effect->effect_id)
                            {
                                $cont += 1;
                            }
                        }

                        if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                        {
                            DB::table('effect_risk')
                                ->where('risk_id','=',$riesgo->id)
                                ->where('effect_id','=',$effect->effect_id)
                                ->delete();
                        }
                    }

                    if (!isset($_POST['stakeholder_id']) || $_POST['stakeholder_id'] == "")
                    {
                        $stake = NULL;
                    }
                    else
                    {
                        $stake = $_POST['stakeholder_id'];
                    }

                    if ($riesgo->type == 0)
                    {
                        //primero eliminamos relaciones previas
                        DB::table('risk_subprocess')
                            ->where('risk_id','=',$riesgo->id)
                            ->delete();

                        //agregamos en tabla risk_subprocess
                        foreach ($_POST['subprocess_id'] as $subprocess_id)
                        {
                            $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                            $subprocess->risks()->attach($riesgo);
                        }       
                    }

                    else if ($riesgo->type == 1)
                    {
                        //primero eliminamos relaciones previas
                        DB::table('objective_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->delete();

                        //agregamos en tabla objective_risk
                        foreach ($_POST['objective_id'] as $objective_id)
                        {
                            $objective = \Ermtool\Objective::find($objective_id);
                            $objective->risks()->attach($riesgo);
                        }       
                    }

                    //eliminamos salto de linea del final de cada una de las textarea (en este caso solo descripción)

                    $riesgo->name = $_POST['name'];
                    $riesgo->description = $_POST['description'];
                    $riesgo->expiration_date = $_POST['expiration_date'];
                    $riesgo->type2 = 1;
                    $riesgo->risk_category_id = $_POST['risk_category_id'];
                    $riesgo->expected_loss = $_POST['expected_loss'];
                    $riesgo->stakeholder_id = $stake;

                    $riesgo->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Risk successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Riesgo actualizado correctamente');
                    }
            });

            return Redirect::to('riesgos.index2?organization_id='.$_POST['org_id']);
        }
    }

    //matriz de riesgos
    public function matrices()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
            if (Session::get('languaje') == 'en')
            {
                return view('en.reportes.matriz_riesgos',['organizations'=>$organizations]);
            }
            else
            {
                return view('reportes.matriz_riesgos',['organizations'=>$organizations]);  
            }
        }
    }

    public function generarMatriz($value,$org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
            {
                $value = $_GET['kind'];
                $org = $_GET['organization_id'];
            }
            
            $i = 0; //contador de controles/subprocesos o controles/objetivos
            $datos = array();

            if (Session::get('languaje') == 'en')
            {
                $proba_string = ['Very low','Low','Medium','High','Very high'];
                $impact_string = ['Very low','Low','Medium','High','Very high'];
            }
            else
            {
                $proba_string = ['Muy poco probable','Poco probable','Intermedio','Probable','Muy probable'];
                $impact_string = ['Muy poco impacto','Poco impacto','Intermedio','Alto impacto','Muy alto impacto'];
            }

            if ($value == 0) //Se generará la matriz de controles de procesos
            {

                //---------- OBS: EXISTE PROBLEMA SI ES QUE EL RIESGO NO CONTIENE CAUSA Y EFECTO --------//
                $risks = DB::table('risk_subprocess')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->join('processes','subprocesses.process_id','=','processes.id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->join('risk_categories','risk_categories.id','=','risks.risk_category_id')
                                    ->where('risks.type2','=',1)
                                    ->where('organization_subprocess.organization_id','=',$org)
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
                                    ->where('objectives.organization_id','=',$org)
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
                    if (Session::get('languaje') == 'en')
                    {
                        $probabilidad = "No evaluation";
                        $impacto = "No evaluation";
                        $score = "No evaluation";
                    }
                    else
                    {
                        $probabilidad = "No tiene evaluación";
                        $impacto = "No tiene evaluación";
                        $score = "No tiene evaluación";
                    }
                    // -- seteamos datos --//
                    //seteamos causa y efecto

                    //obtenemos causas
                    $causes = DB::table('cause_risk')
                                    ->join('causes','causes.id','=','cause_risk.cause_id')
                                    ->where('risk_id','=',$risk->id)
                                    ->select('causes.name')
                                    ->get();

                    if ($causes)
                    {
                        $last = end($causes); //guardamos final para no agregarle coma
                        foreach ($causes as $cause)
                        {
                            if ($cause != $last)
                                $causas .= $cause->name.', ';
                            else
                                $causas .= $cause->name;
                        }
                    }
                    else
                    {
                        //se realizarán acá los textos (y no en la vista) para el caso en que se esté exportando a excel
                        if (Session::get('languaje') == 'en')
                        {
                            $causas = "No defined cause";
                        }
                        else
                        {
                            $causas = "No tiene causas definidas";
                        }
                    }

                    //obtenemos efectos
                    $effects = DB::table('effect_risk')
                                    ->join('effects','effects.id','=','effect_risk.effect_id')
                                    ->where('risk_id','=',$risk->id)
                                    ->select('effects.name')
                                    ->get();


                    if ($effects)
                    {
                        $last = end($effects); //guardamos final para no agregarle coma
                        foreach ($effects as $effect)
                        {
                            if ($effect != $last)
                                $efectos .= $effect->name.', ';
                            else
                                $efectos .= $effect->name;
                        }
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $efectos = "No defined effects";
                        }
                        else
                        {
                            $efectos = "No tiene efectos definidos";
                        }
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
                                $impacto = $eval->avg_impact.' ('.$impact_string[$eval->avg_impact-1].')';
                                $probabilidad = $eval->avg_probability.' ('.$proba_string[$eval->avg_probability-1].')';
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
                        if (Session::get('languaje') == 'en')
                        {
                            $controles = "No controls specified";
                        }
                        else
                        {
                            $controles = "No se han especificado controles";
                        }
                    }
                    else
                    {
                        $last = end($controls); //guardamos final para no agregarle coma
                        foreach ($controls as $control)
                        {
                            if ($control != $last)
                                $controles .= $control->name.', ';
                            else
                                $controles .= $control->name;
                        }
                    }
                    /* IMPORTANTE!!!
                        Los nombres de las variables serán guardados en español para mostrarlos
                        en el archivo excel que será exportado
                    */
                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($risk->created_at == NULL OR $risk->created_at == "0000-00-00" OR $risk->created_at == "")
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $fecha_creacion = "Failed to register creation date";
                        }
                        else
                        {
                            $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
                        }
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
                        if (Session::get('languaje') == 'en')
                        {
                            $expiration_date = "None";
                        }
                        else
                        {
                            $expiration_date = "Ninguna";
                        }
                    }
                    else
                    { 
                        //sacamos solo fecha y ordenamos
                        $fecha_temp1 = explode('-',$risk->expiration_date);
                        $expiration_date = $fecha_temp1[2].'-'.$fecha_temp1[1].'-'.$fecha_temp1[0];
                    }

                    if ($risk->expected_loss == 0 || $risk->expected_loss == NULL)
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $expected_loss = "Not assigned expected loss";
                        }
                        else
                        {
                            $expected_loss = "No se ha asignado p&eacute;rdida esperada";
                        }
                    }
                    else
                    {
                        $expected_loss = $risk->expected_loss;
                    }

                    //Seteamos datos
                    if ($value == 0) //guardamos datos de riesgos de procesos
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $datos[$i] = [//'id' => $control->id,
                                        'Process' => $risk->process_name,
                                        'Subprocess' => $risk->subprocess_name,
                                        'Risk' => $risk->name,
                                        'Description' => $risk->description,
                                        'Category' => $risk->risk_category_name,
                                        'Causes' => $causas,
                                        'Effects' => $efectos,
                                        'Expected_loss' => $expected_loss,
                                        'Probability' => $probabilidad,
                                        'Impact' => $impacto,
                                        'Score' => $score,
                                        'Identification_date' => $fecha_creacion,
                                        'Expiration_date' => $expiration_date,
                                        'Controls' => $controles,];
                        }
                        else
                        {
                            $datos[$i] = [//'id' => $control->id,
                                        'Proceso' => $risk->process_name,
                                        'Subproceso' => $risk->subprocess_name,
                                        'Riesgo' => $risk->name,
                                        'Descripción' => $risk->description,
                                        'Categoría' => $risk->risk_category_name,
                                        'Causas' => $causas,
                                        'Efectos' => $efectos,
                                        'Pérdida_esperada' => $expected_loss,
                                        'Probabilidad' => $probabilidad,
                                        'Impacto' => $impacto,
                                        'Score' => $score,
                                        'Fecha_identificación' => $fecha_creacion,
                                        'Fecha_expiración' => $expiration_date,
                                        'Controles' => $controles,];
                        }
                        $i += 1;
                    }

                    else if ($value == 1) //guardamos datos de riesgos de negocio
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $datos[$i] = [//'id' => $control->id,
                                        'Organization' => $risk->organization_name,
                                        'Objective' => $risk->objective_name,
                                        'Risk' => $risk->name,
                                        'Description' => $risk->description,
                                        'Category' => $risk->risk_category_name,
                                        'Causes' => $causas,
                                        'Effects' => $efectos,              
                                        'Expected_loss' => $risk->expected_loss,
                                        'Probability' => $probabilidad,
                                        'Impact' => $impacto,
                                        'Score' => $score,
                                        'Identification_date' => $fecha_creacion,
                                        'Expiration_date' => $expiration_date,
                                        'Controls' => $controles];
                        }
                        else
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
                        }
                        $i += 1;
                    }
            }

            if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
            {
                return $datos;
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.matriz_riesgos',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org]);
                }
                else
                {
                    return view('reportes.matriz_riesgos',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org]);
                }
                //return json_encode($datos);
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
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;

        DB::transaction(function() {
            //CORRECCIÓN DISEÑO: SIEMPRE TENDRÁ O UN OBJECTIVE_RISK O UN RISK_SUBPROCESS
            //primero vemos si contiene algún objetivo asociado 
            
            //$rev = DB::table('action_plans')
            //    ->where('')
            //OJO: En evaluation_risk sólo se buscará en el campo risk_id, ya que después se revisará objective_risk o risk_subprocess
            $rev = DB::table('evaluation_risk')
                ->where('risk_id','=',$GLOBALS['id1'])
                ->select('id')
                ->get();

            if (empty($rev))
            {
                //revisamos objective_subprocess_risk (hayq que ver objective_risk y subprocess_risk)
                $rev = DB::table('objective_subprocess_risk')
                    ->where('objective_risk_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

                if (empty($rev))
                {
                    //ahora revisamos en la misma tabla subprocess_risk
                    $rev = DB::table('objective_subprocess_risk')
                        ->where('risk_subprocess_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                    if (empty($rev))
                    {
                        //pruebas de auditoría
                        $rev = DB::table('audit_tests')
                            ->where('risk_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                        if (empty($rev))
                        {
                            //KRI
                            $rev = DB::table('kri')
                                ->where('risk_id','=',$GLOBALS['id1'])
                                ->select('id')
                                ->get();

                            if (empty($rev))
                            {
                                //ahora vemos verificamos las uniones con objective_risk o subprocess_risk
                                //primero usaremos una variable local para verificar posteriormente
                                $res2 = 1;

                                $risks = DB::table('risk_subprocess')
                                    ->where('risk_id','=',$GLOBALS['id1'])
                                    ->select('id')
                                    ->get();

                                if (empty($risks)) //entonces es objective_risk
                                {
                                    $risks = DB::table('objective_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->select('id')
                                        ->get();

                                    foreach ($risks as $risk)
                                    {
                                        //vemos si tiene control_objective_risk
                                        $rev = DB::table('control_objective_risk')
                                            ->where('objective_risk_id','=',$risk->id)
                                            ->select('id')
                                            ->get();

                                        if (empty($rev))
                                        {
                                            //evaluation_risk
                                            $rev = DB::table('evaluation_risk')
                                                ->where('objective_risk_id','=',$risk->id)
                                                ->select('id')
                                                ->get();

                                            if (empty($rev))
                                            {
                                                $rev = DB::table('audit_plan_risk')
                                                    ->where('objective_risk_id','=',$risk->id)
                                                    ->select('id')
                                                    ->get();

                                                if (empty($rev))
                                                {
                                                    $rev = DB::table('audit_risk')
                                                        ->where('objective_risk_id','=',$risk->id)
                                                        ->select('id')
                                                        ->get();

                                                    if (empty($rev))
                                                    {
                                                        //para verificar que sea parar todos los objective_risk
                                                        $res2 = 0;
                                                    }
                                                    else
                                                    {
                                                        $res2 = 1;
                                                    }
                                                }
                                                else
                                                {
                                                    $res2 = 1;
                                                }
                                            }
                                            else
                                            {
                                                $res2 = 1;
                                            }
                                        }
                                        else
                                        {
                                            $res2 = 1;
                                        }

                                        if ($res2 == 1)
                                        {
                                            break;
                                        }
                                    } 
                                }
                                else
                                {
                                    foreach ($risks as $risk)
                                    {
                                        //vemos si tiene control_risk_subprocess
                                        $rev = DB::table('control_risk_subprocess')
                                            ->where('risk_subprocess_id','=',$risk->id)
                                            ->select('id')
                                            ->get();

                                        if (empty($rev))
                                        {
                                            //evaluation_risk
                                            $rev = DB::table('evaluation_risk')
                                                ->where('risk_subprocess_id','=',$risk->id)
                                                ->select('id')
                                                ->get();

                                            if (empty($rev))
                                            {
                                                $rev = DB::table('audit_plan_risk')
                                                    ->where('risk_subprocess_id','=',$risk->id)
                                                    ->select('id')
                                                    ->get();

                                                if (empty($rev))
                                                {
                                                    $rev = DB::table('audit_risk')
                                                        ->where('risk_subprocess_id','=',$risk->id)
                                                        ->select('id')
                                                        ->get();

                                                    if (empty($rev))
                                                    {
                                                        $res2 = 0;
                                                        //eliminamos risk(s)_subprocess asociados
                                                        //DB::table('risk_subprocess')
                                                        //    ->where('id','=',$risk->id)
                                                        //    ->delete();
                                                    }
                                                    else
                                                    {
                                                        $res2 = 1;
                                                    }
                                                }
                                                else
                                                {
                                                    $res2 = 1;
                                                }
                                            }
                                            else
                                            {
                                                $res2 = 1;
                                            }
                                        }
                                        else
                                        {
                                            $res2 = 1;
                                        }

                                        if ($res2 == 1)
                                        {
                                            break;
                                        }
                                    } 
                                }

                                if ($res2 == 0)
                                {
                                    //se puede borrar

                                    //eliminamos objective_risk(s) asociados (si es que hay)
                                    DB::table('objective_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->delete();

                                    //eliminamos de risk(s)_subprocess asociados (si es que hay)
                                    DB::table('risk_subprocess')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->delete();

                                    //eliminamos posibles causas asociadas
                                    DB::table('cause_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->delete();

                                    //eliminamos posibles efectos asociados
                                    DB::table('effect_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->delete();
                                    DB::table('risks')
                                        ->where('id','=',$GLOBALS['id1'])
                                        ->delete();

                                    $GLOBALS['res'] = 0;
                                }
                                
                            }
                        }
                    }
                }
            }

        });

        return $res;
        
    }

    //función para obtener riesgos de una organización
    public function getRisks($org)
    {
        $subprocess_risks = array();
        $objective_risks = array();

        //obtenemos riesgos de subproceso
        $risks = DB::table('risks')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('risks.status','=',0)
                ->select('risks.id','risks.name')
                ->distinct('risks.id')
                ->get();

        $i = 0;

        foreach ($risks as $risk)
        {
            $results[$i] = [
                'id' => $risk->id,
                'name' => $risk->name
            ];

            $i += 1;
        }

        //obtenemos riesgos de negocio
        $risks = DB::table('risks')
                ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->where('risks.status','=',0)
                ->select('risks.id','risks.name')
                ->distinct('risks.id')
                ->get();

        foreach ($risks as $risk)
        {
            $results[$i] = [
                'id' => $risk->id,
                'name' => $risk->name
            ];

            $i += 1;
        }

        return json_encode($results);
    }

    //obtiene todas las causas
    public function getCauses()
    {
        $causes = \Ermtool\Cause::all(['id','name']);
        return json_encode($causes);

    }

    //obtiene todos los efectos
    public function getEffects()
    {
        $effects = \Ermtool\Effect::all(['id','name']);
        return json_encode($effects);

    }
}
