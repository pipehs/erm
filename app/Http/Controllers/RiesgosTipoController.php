<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use DateTime;

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
        DB::transaction(function()
        {
            //vemos si se agrego alguna causa nueva
            if (isset($_POST['causa_nueva']))
            {
                $new_causa = \Ermtool\Cause::create([
                    'name'=>$_POST['causa_nueva']
                ]);

                //guardamos id de causa recien agregada
                $causa = $new_causa->id;
            }
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
                $new_effect = \Ermtool\Effect::create([
                    'name'=>$_POST['efecto_nuevo']
                    ]);

                //obtenemos id de efecto agregado
                $efecto = $new_effect->id;
            }
            else
            {
                if ($_POST['effect_id'] == NULL)
                    $efecto = NULL;
                else
                    $efecto = $_POST['effect_id'];
            }

            \Ermtool\Risk::create([
                'name'=>$_POST['name'],
                'description'=>$_POST['description'],
                'type2'=>0,
                'expiration_date'=>$_POST['expiration_date'],
                'risk_category_id'=>$_POST['risk_category_id'],
                'cause_id'=>$causa,
                'effect_id'=>$efecto,
                ]);

            Session::flash('message','Riesgo tipo agregado correctamente');
        });
        return Redirect::to('/riskstype');

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
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);

            //vemos si se agrego alguna causa nueva
            if (isset($_POST['causa_nueva']))
            {
                $new_causa = \Ermtool\Cause::create([
                    'name'=>$request['causa_nueva']
                ]);

                //obtenemos id de causa recien agregada
                $causa = $new_causa->id;
            }
            else
            {
                if ($request['cause_id'] == NULL)
                    $causa = NULL;
                else
                    $causa = $request['cause_id'];
            }

            //vemos si se agrego algún efecto nuevo
            if (isset($_POST['efecto_nuevo']))
            {
                $new_effect = \Ermtool\Effect::create([
                    'name'=>$request['efecto_nuevo']
                    ]);

                //obtenemos id de efecto agregado
                $efecto = $new_effect->id;
            }
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
            $riesgo->risk_category_id = $_POST['risk_category_id'];
            $riesgo->cause_id = $causa;
            $riesgo->effect_id = $efecto;

            $riesgo->save();

            Session::flash('message','Riesgo tipo actualizado correctamente');
        });

        return Redirect::to('/riskstype');
    }

    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
            $riesgo->status = 1;
            $riesgo->save();

            Session::flash('message','Riesgo tipo bloqueado correctamente');
        });
        return Redirect::to('/riskstype');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
            $riesgo->status = 0;
            $riesgo->save();

            Session::flash('message','Riesgo tipo desbloqueado correctamente');
        });

        return Redirect::to('/riskstype');
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
