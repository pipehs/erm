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
            $riesgostipo2 = \Ermtool\Risk::all()->where('type2',0)->where('status',1); //select riesgos tipo bloqueados  
        }
        else
        {
            $riesgostipo2 = \Ermtool\Risk::all()->where('type2',0)->where('status',0); //select riesgos tipo desbloqueados
        }

        $i = 0;

        // ---recorremos todos los riesgos tipo para asignar formato de datos correspondientes--- //
        foreach ($riesgostipo2 as $riesgo)
        {
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

             //damos formato a fecha creación
            if ($riesgo['created_at'] != NULL)
            {
                $fecha_creacion = date_format($riesgo['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($riesgo['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización 
            if ($riesgo['updated_at'] != NULL)
            {
                $fecha_act = date_format($riesgo['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($riesgo['updated_at'],"H:i:s");
            }
            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

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
                                'nombre'=>$riesgo['name'],
                                'descripcion'=>$riesgo['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'causa'=>$causa['name'],
                                'efecto'=>$efecto['name'],
                                'categoria'=>$categoria['name'],
                                'estado'=>$riesgo['status']);
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
        $categorias = \Ermtool\Risk_Category::where('status',0)->lists('name','id');
        $causas = \Ermtool\Cause::where('status',0)->lists('name','id');
        $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');
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
            'type2'=>0,
            'expiration_date'=>$fecha_exp,
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
        $categorias = \Ermtool\Risk_Category::where('status',0)->lists('name','id');
        $causas = \Ermtool\Cause::where('status',0)->lists('name','id');
        $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');
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
        $fecha_exp = NULL;

        if (strpos($request['expiration_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha expiración
            if ($request['expiration_date'] != "" OR $request['expiration_date'] != "0000-00-00")
            {
                $fecha = explode("/",$request['expiration_date']);
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
                'name'=>$request['causa_nueva']
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
                'name'=>$request['efecto_nuevo']
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

        $riesgo->name = $request['name'];
        $riesgo->description = $request['description'];
        $riesgo->expiration_date = $fecha_exp;
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
        $riesgo->status = 1;
        $riesgo->save();

        Session::flash('message','Riesgo tipo bloqueado correctamente');

        return Redirect::to('/riesgostipo');
    }

    public function desbloquear($id)
    {
        $riesgo = \Ermtool\Risk::find($id);
        $riesgo->status = 0;
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
