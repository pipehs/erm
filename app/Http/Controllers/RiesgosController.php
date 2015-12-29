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
                    'efectos'=>$efectos,'objetivos'=>$objetivos,'riesgos_tipo'=>$riesgos_tipo]);
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
        //vemos si es de proceso o de negocio
            if (isset($request['subprocess_id']))
            {
                $type = 0;
            }
            else if (isset($request['objective_id']))
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

            //obtenemos orden correcto de fecha expiración
            if ($request['expiration_date'] != "")
            {
                $fecha = explode("/",$request['expiration_date']);
                $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
            }
            else
            {
                $fecha_exp = NULL;
            }
            
             //vemos si se agrego alguna causa nueva
            if (isset($request['causa_nueva']))
            {
                \Ermtool\Cause::create([
                    'name'=>$request['causa_nueva']
                ]);

                //obtenemos id de causa recien agregada
                $causa = \Ermtool\Cause::max('id');
            }
            //agregamos la causa previamente creada, o en su defecto NULL
            else
            {
                if ($request['cause_id'] == NULL)
                    $causa = NULL;
                else
                    $causa = $request['cause_id'];
            }

            //vemos si se agrego algún efecto nuevo
            if (isset($request['efecto_nuevo']))
            {
                \Ermtool\Effect::create([
                    'name'=>$request['efecto_nuevo']
                    ]);

                //obtenemos id de efecto agregado
                $efecto = \Ermtool\Effect::max('id');
            }
            //agregamos efecto previamente creado, o en su defecto NULL
            else
            {
                if ($request['effect_id'] == NULL)
                    $efecto = NULL;
                else
                    $efecto = $request['effect_id'];
            }

            \Ermtool\Risk::create([
                'name'=>$request['name'],
                'description'=>$request['description'],
                'type'=>$type,
                'type2'=>1,
                'expiration_date'=>$fecha_exp,
                'risk_category_id'=>$request['risk_category_id'],
                'cause_id'=>$causa,
                'effect_id'=>$efecto,
                ]);

        //agregamos en tabla risk_subprocess o objective_risk
        //obtenemos id de riesgo recien ingresado
        $risk = \Ermtool\Risk::max('id');

        if ($type == 0)
        {        
            //agregamos en tabla risk_subprocess

            foreach ($request['subprocess_id'] as $subprocess_id)
            {
                $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                $subprocess->risks()->attach($risk);
            }       
        }

        else if ($type == 1)
        {
            //agregamos en tabla objective_risk

            foreach ($request['objective_id'] as $objective_id)
            {
                $objective = \Ermtool\Objective::find($objective_id);
                $objective->risks()->attach($risk);
            }       
        }

        Session::flash('message','Riesgo agregado correctamente');

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
        return $id;
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
