<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;

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
            $organizations = \Ermtool\Organization::all()->where('status',1); //select organizaciones bloqueadas  
        }
        else
        {
            $organizations = \Ermtool\Organization::all()->where('status',0); //select organizaciones desbloqueadas
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
                                             'nombre'=>$hijos['name']);
                $j += 1;
            }

            //damos formato a fecha expiración
            if ($organizaciones['expiration_date'] == NULL OR $organizaciones['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
            {
                $expiration_date = new DateTime($organizaciones['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            //damos formato a servicios compartidos
            if ($organizaciones['shared_services'] == 0)
            {
                $serv_compartidos = "No";
            }
            else
                $serv_compartidos = "Si";

            //damos formato a fecha creación
            if ($organizaciones['created_at'] != NULL)
            {
                $fecha_creacion = date_format($organizaciones['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($organizaciones['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización 
            if ($organizaciones['updated_at'] != NULL)
            {
                $fecha_act = date_format($organizaciones['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($organizaciones['updated_at'],"H:i:s");
            }
            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";
            

            $organization[$i] = array('id'=>$organizaciones['id'],
                                'nombre'=>$organizaciones['name'],
                                'descripcion'=>$organizaciones['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'serv_compartidos'=>$serv_compartidos,
                                'estado'=>$organizaciones['status']);
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
        $organizations = \Ermtool\Organization::where('status',0)->where('organization_id',NULL)->lists('name','id');
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
            'name' => $request['name'],
            'description' => $request['description'],
            'expiration_date' => $fecha_exp,
            'shared_services' => $request['shared_services'],
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
        $organizations = \Ermtool\Organization::where('id','<>',$id)->where('status',0)->where('organization_id',NULL)->lists('name','id');
        $org = \Ermtool\Organization::find($id);
        return view('datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org]);
    }


    public function bloquear($id)
    {
        $organization = \Ermtool\Organization::find($id);
        $organization->status = 1;
        $organization->save();

        Session::flash('message','Organizaci&oacute;n bloqueada correctamente');

        return Redirect::to('/organization');
    }

    public function desbloquear($id)
    {
        $organization = \Ermtool\Organization::find($id);
        $organization->status = 0;
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
        $fecha_creacion = $organization->created_at;
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

        //vemos si tiene organización padre
        if($request['organization_id'] != "")
        {
            $organizacion_padre = $request['organization_id'];
        }
        else
        {
            $organizacion_padre = NULL;
        }

        $organization->name = $request['name'];
        $organization->description = $request['description'];
        $organization->expiration_date = $fecha_exp;
        $organization->shared_services = $request['shared_services'];
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
