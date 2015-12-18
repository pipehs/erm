<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

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

        $riesgos2 = \Ermtool\Risk::all()->where('tipo2',1); //Selecciona todos los riesgos ya identificados
        $j = 0; //contador de subprocesos u objetivos relacionados

        foreach ($riesgos2 as $riesgo)
        {
            //damos formato a tipo de riesgo
            if ($riesgo['tipo'] == 0)
            {
                $tipo = "De Proceso";
                //primero obtenemos subprocesos relacionados
                $subprocesses = \Ermtool\Risk::find($riesgo['id'])->subprocesses;

                foreach($subprocesses as $subprocess)
                {
                    $relacionados[$j] = array('risk_id'=>$riesgo['id'],
                                        'id'=>$subprocess['id'],
                                        'nombre'=>$subprocess['nombre']);
                    $j += 1;
                }
            }
            else if ($riesgo['tipo'] == 1)
            {
                $tipo = "De Negocio";
                //primero obtenemos objetivos relacionados
                $objectives = \Ermtool\Risk::find($riesgo['id'])->objectives;

                foreach ($objectives as $objective)
                {
                    $relacionados[$j] = array('risk_id'=>$riesgo['id'],
                                            'id'=>$objective['id'],
                                            'nombre'=>$objective['nombre']);
                    $j += 1;
                }
            }

            //damos formato a fecha de expiración
            if ($riesgo['fecha_exp'] == NULL)
            {
                $fecha_exp = "Ninguna";
            }

            //obtenemos nombre de categoría
            $categoria = \Ermtool\Risk_Category::where('id',$riesgo['risk_category_id'])->value('nombre');

            //obtenemos nombre de causa
            if ($riesgo['cause_id'] != NULL)
            {
                $causa = \Ermtool\Cause::where('id',$riesgo['cause_id'])->value('nombre');
            }
            else
                $causa = "No especifica";

            //obtenemos nombre de efecto
            if ($riesgo['effect_id'] != NULL)
            {
                $efecto = \Ermtool\Effect::where('id',$riesgo['effect_id'])->value('nombre');
            }
            else
                $efecto = "No especifica";

            $riesgos[$i] = array('id'=>$riesgo['id'],
                                'nombre'=>$riesgo['nombre'],
                                'descripcion'=>$riesgo['descripcion'],
                                'tipo'=>$tipo,
                                'fecha_creacion'=>$riesgo['fecha_creacion'],
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
        $categorias = \Ermtool\Risk_Category::where('estado',0)->lists('nombre','id');

        //causas preingresadas
        $causas = \Ermtool\Cause::where('estado',0)->lists('nombre','id');

        //efectos preingresados
        $efectos = \Ermtool\Effect::where('estado',0)->lists('nombre','id');

        //riesgos tipo
        $riesgos_tipo = \Ermtool\Risk::where('estado',0)->where('tipo2',0)->lists('nombre','id');

        if(isset($_GET['P']))
        {
            $subprocesos = \Ermtool\Subprocess::where('estado',0)->lists('nombre','id');
            return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                    'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo]);
        }

        else if (isset($_GET['N']))
        {
            $objetivos = \Ermtool\Objective::where('estado',0)->lists('nombre','id');
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
                $tipo = 0;
            }
            else if (isset($request['objective_id']))
            {
                $tipo = 1;
            }

        //Verificamos si se ingreso un riesgo tipo
        if ($request['riesgo_tipo'] != "")
        {
            $new_risk = \Ermtool\Risk::find($request['riesgo_tipo']);

            //creamos nuevo riesgo con los mismos datos
            \Ermtool\Risk::create([
                'nombre'=>$new_risk->nombre,
                'descripcion'=>$new_risk->descripcion,
                'tipo'=>$tipo,
                'tipo2'=>1,
                'fecha_creacion'=>$new_risk->fecha_creacion,
                'fecha_exp'=>$new_risk->fecha_exp,
                'risk_category_id'=>$new_risk->risk_category_id,
                'cause_id'=>$new_risk->cause_id,
                'effect_id'=>$new_risk->effect_id,
                ]);
        }

        else
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
            
             //vemos si se agrego alguna causa nueva
            if (isset($request['causa_nueva']))
            {
                \Ermtool\Cause::create([
                    'nombre'=>$request['causa_nueva'],
                    'fecha_creacion'=>date('Y-m-d'),
                ]);

                //obtenemos id de causa recien agregada
                $causa = \Ermtool\Cause::max('id');
            }
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
                    'nombre'=>$request['efecto_nuevo'],
                    'fecha_creacion'=>date('Y-m-d'),
                    ]);

                //obtenemos id de efecto agregado
                $efecto = \Ermtool\Effect::max('id');
            }
            else
            {
                if ($request['effect_id'] == NULL)
                    $efecto = NULL;
                else
                    $efecto = $request['effect_id'];
            }

            \Ermtool\Risk::create([
                'nombre'=>$request['nombre'],
                'descripcion'=>$request['descripcion'],
                'tipo'=>$tipo,
                'tipo2'=>1,
                'fecha_creacion'=>$fecha_creacion,
                'fecha_exp'=>$fecha_exp,
                'risk_category_id'=>$request['risk_category_id'],
                'cause_id'=>$causa,
                'effect_id'=>$efecto,
                ]);
        }

        //agregamos en tabla risk_subprocess o objective_risk
        //obtenemos id de riesgo recien ingresado
        $risk = \Ermtool\Risk::max('id');

        if ($tipo == 0)
        {        
            //agregamos en tabla risk_subprocess

            foreach ($request['subprocess_id'] as $subprocess_id)
            {
                $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                $subprocess->risks()->attach($risk);
            }       
        }

        else if ($tipo == 1)
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
