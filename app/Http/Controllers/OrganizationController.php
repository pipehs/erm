<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = array();
        if (isset($_GET['verbloqueados']))
        {
            $organizations = \Ermtool\Organization::all()->where('estado',1); //select organizaciones bloqueadas  
        }
        else
        {
            $organizations = \Ermtool\Organization::all()->where('estado',0); //select organizaciones desbloqueadas
        }

        $org_dependientes = array();
        $i = 0;
        // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
        foreach ($organizations as $organizaciones)
        {
            //buscamos organizaciones que dependen de ésta
            $organizaciones_dependientes = \Ermtool\Organization::all()->where('organization_id',$organizaciones['id']);
            
            $j = 0;
            foreach ($organizaciones_dependientes as $hijos)
            {
                $org_dependientes[$j] = array('organization_id'=>$organizaciones['id'],
                                             'id'=>$hijos['id'],
                                             'nombre'=>$hijos['nombre']);
                $j += 1;
            }

            //damos formato a fecha expiración
            if ($organizaciones['fecha_exp'] == NULL OR $organizaciones['fecha_exp'] == "0000-00-00")
            {
                $fecha_expiracion = "Ninguna";
            }
            else 
                $fecha_expiracion = $organizaciones['fecha_exp'];

            //damos formato a servicios compartidos
            if ($organizaciones['serv_compartidos'] == 0)
            {
                $serv_compartidos = "No";
            }
            else
                $serv_compartidos = "Si";

            $organization[$i] = array('id'=>$organizaciones['id'],
                                'nombre'=>$organizaciones['nombre'],
                                'descripcion'=>$organizaciones['descripcion'],
                                'fecha_creacion'=>$organizaciones['fecha_creacion'],
                                'fecha_exp'=>$fecha_expiracion,
                                'serv_compartidos'=>$serv_compartidos,
                                'estado'=>$organizaciones['estado']);
            $i += 1;
        }

        return view('datos_maestros.organization.index',['organizations'=>$organization,'org_dependientes'=>$org_dependientes]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = \Ermtool\Organization::where('estado',0)->where('organization_id',NULL)->lists('nombre','id');
        return view('datos_maestros.organization.create',['organizations'=>$organizations]);
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

        //vemos si tiene organización padre
        if($request['organization_id'] != "")
        {
            $organizacion_padre = $request['organization_id'];
        }
        else
        {
            $organizacion_padre = NULL;
        }

        \Ermtool\Organization::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            'serv_compartidos' => $request['serv_compartidos'],
            'organization_id' => $organizacion_padre,
            ]);

        Session::flash('message','Organizaci&oacute;n creada correctamente');

        return Redirect::to('/organization');

    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $organizations = \Ermtool\Organization::where('id','<>',$id)->where('estado',0)->where('organization_id',NULL)->lists('nombre','id');
        $org = \Ermtool\Organization::find($id);
        return view('datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org]);
    }


    public function bloquear($id)
    {
        $organization = \Ermtool\Organization::find($id);
        $organization->estado = 1;
        $organization->save();

        Session::flash('message','Organizaci&oacute;n bloqueada correctamente');

        return Redirect::to('/organization');
    }

    public function desbloquear($id)
    {
        $organization = \Ermtool\Organization::find($id);
        $organization->estado = 0;
        $organization->save();

        Session::flash('message','Organizaci&oacute;n desbloqueada correctamente');

        return Redirect::to('/organization');
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
        $organization = \Ermtool\Organization::find($id);
        $fecha_creacion = $organization->fecha_creacion;
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

        //vemos si tiene organización padre
        if($request['organization_id'] != "")
        {
            $organizacion_padre = $request['organization_id'];
        }
        else
        {
            $organizacion_padre = NULL;
        }

        $organization->nombre = $request['nombre'];
        $organization->descripcion = $request['descripcion'];
        $organization->fecha_creacion = $fecha_creacion;
        $organization->fecha_exp = $fecha_exp;
        $organization->serv_compartidos = $request['serv_compartidos'];
        $organization->organization_id = $organizacion_padre;

        $organization->save();

        Session::flash('message','Organizaci&oacute;n actualizada correctamente');

        return Redirect::to('/organization');
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
