<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class RiesgosTipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $riesgostipo = array();
        if (isset($_GET['verbloqueados']))
        {
            $riesgostipo2 = \Ermtool\Risk::all()->where('tipo2',0)->where('estado',1); //select riesgos tipo bloqueados  
        }
        else
        {
            $riesgostipo2 = \Ermtool\Risk::all()->where('tipo2',0)->where('estado',0); //select riesgos tipo desbloqueados
        }

        $i = 0;

        // ---recorremos todos los riesgos tipo para asignar formato de datos correspondientes--- //
        foreach ($riesgostipo2 as $riesgo)
        {
            //damos formato a fecha expiración
            if ($riesgo['fecha_exp'] == NULL OR $riesgo['fecha_exp'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $riesgo['fecha_exp'];

            //obtenemos categoría de riesgo
            $categoria = \Ermtool\Risk_Category::find($riesgo['risk_category_id']);

            //obtenemos causa si es que tiene
            if ($riesgo['cause_id'] != NULL)
            {
                $causa = \Ermtool\Cause::find($riesgo['cause_id']);
            }
            else
                $causa['nombre'] = "No especificada";

            //obtenemos efecto si es que existe
            if ($riesgo['effect_id'] != NULL)
            {
                $efecto = \Ermtool\Effect::find($riesgo['effect_id']);
            }
            else
                $efecto['nombre'] = "No especificado";

            $riesgostipo[$i] = array('id'=>$riesgo['id'],
                                'nombre'=>$riesgo['nombre'],
                                'descripcion'=>$riesgo['descripcion'],
                                'fecha_creacion'=>$riesgo['fecha_creacion'],
                                'fecha_exp'=>$fecha_exp,
                                'causa'=>$causa['nombre'],
                                'efecto'=>$efecto['nombre'],
                                'categoria'=>$categoria['nombre'],
                                'estado'=>$riesgo['estado']);
            $i += 1;
        }
        return view('datos_maestros.riesgos_tipo.index',['riesgos'=>$riesgostipo]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = \Ermtool\Risk_Category::where('estado',0)->lists('nombre','id');
        $causas = \Ermtool\Cause::where('estado',0)->lists('nombre','id');
        $efectos = \Ermtool\Effect::where('estado',0)->lists('nombre','id');
        return view('datos_maestros.riesgos_tipo.create',
                    ['categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos]);
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
            'tipo2'=>0,
            'fecha_creacion'=>$fecha_creacion,
            'fecha_exp'=>$fecha_exp,
            'risk_category_id'=>$request['risk_category_id'],
            'cause_id'=>$causa,
            'effect_id'=>$efecto,
            ]);

        Session::flash('message','Riesgo tipo agregado correctamente');

        return Redirect::to('/riesgostipo');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $riesgo = \Ermtool\Risk::find($id);
        $categorias = \Ermtool\Risk_Category::where('estado',0)->lists('nombre','id');
        $causas = \Ermtool\Cause::where('estado',0)->lists('nombre','id');
        $efectos = \Ermtool\Effect::where('estado',0)->lists('nombre','id');
        return view('datos_maestros.riesgos_tipo.edit',['riesgo'=>$riesgo,
                    'categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos]);
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
        $riesgo = \Ermtool\Risk::find($id);
        $fecha_creacion = $riesgo->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
        $fecha_exp = NULL;

        if (strpos($request['fecha_creacion'],'/')) //primero verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha creación
            $fecha = explode("/",$request['fecha_creacion']);
            $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }

        if (strpos($request['fecha_exp'],'/')) //lo mismo para fecha de expiración
        {
            //obtenemos orden correcto de fecha expiración
            if ($request['fecha_exp'] != "" OR $request['fecha_exp'] != "0000-00-00")
            {
                $fecha = explode("/",$request['fecha_exp']);
                $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
            }
            else
            {
                $fecha_exp = NULL;
            }
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

        $riesgo->nombre = $request['nombre'];
        $riesgo->descripcion = $request['descripcion'];
        $riesgo->fecha_creacion = $fecha_creacion;
        $riesgo->fecha_exp = $fecha_exp;
        $riesgo->risk_category_id = $request['risk_category_id'];
        $riesgo->cause_id = $causa;
        $riesgo->effect_id = $efecto;

        $riesgo->save();

        Session::flash('message','Riesgo tipo actualizado correctamente');

        return Redirect::to('/riesgostipo');
    }

    public function bloquear($id)
    {
        $riesgo = \Ermtool\Risk::find($id);
        $riesgo->estado = 1;
        $riesgo->save();

        Session::flash('message','Riesgo tipo bloqueado correctamente');

        return Redirect::to('/riesgostipo');
    }

    public function desbloquear($id)
    {
        $riesgo = \Ermtool\Risk::find($id);
        $riesgo->estado = 0;
        $riesgo->save();

        Session::flash('message','Riesgo tipo desbloqueado correctamente');

        return Redirect::to('/riesgostipo');
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
