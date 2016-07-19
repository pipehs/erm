<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;
use DB;

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
            $organizations = \Ermtool\Organization::where('status',1)->get(); //select organizaciones bloqueadas  
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->get(); //select organizaciones desbloqueadas
        }

        $org_dependientes = array();
        $i = 0;
        // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
        foreach ($organizations as $organizaciones)
        {
            //buscamos organizaciones que dependen de ésta
            $organizaciones_dependientes = \Ermtool\Organization::where('organization_id',$organizaciones['id'])->get();
            
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
                $fecha_exp = NULL;
            }
            else 
            {
                $expiration_date = new DateTime($organizaciones['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            if ($organizaciones['mision'] == NULL || $organizaciones['mision'] == "")
            {
                $mision = NULL;
            }
            else
            {
                $mision = $organizaciones['mision'];
            }

            if ($organizaciones['vision'] == NULL || $organizaciones['vision'] == "")
            {
                $vision = NULL;
            }
            else
            {
                $vision = $organizaciones['vision'];
            }

            if ($organizaciones['target_client'] == NULL || $organizaciones['target_client'] == "")
            {
                $target_client = NULL;      
            }
            else
            {
                $target_client = $organizaciones['target_client'];
            }
            

            $organization[$i] = array('id'=>$organizaciones['id'],
                                'nombre'=>$organizaciones['name'],
                                'descripcion'=>$organizaciones['description'],
                                'target_client'=>$target_client,
                                'mision'=>$mision,
                                'vision'=>$vision,
                                'fecha_exp'=>$fecha_exp,
                                'serv_compartidos'=>$organizaciones['shared_services'],
                                'estado'=>$organizaciones['status']);
            $i += 1;
        }

        if (Session::get('languaje') == 'en')
        {
            return view('en.datos_maestros.organization.index',['organizations'=>$organization,'org_dependientes'=>$org_dependientes]);
        }
        else
        {
            return view('datos_maestros.organization.index',['organizations'=>$organization,'org_dependientes'=>$org_dependientes]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Session::get('languaje') == 'en')
        {
            $organizations = \Ermtool\Organization::where('status',0)->where('organization_id',NULL)->lists('name','id');
            return view('en.datos_maestros.organization.create',['organizations'=>$organizations]);
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->where('organization_id',NULL)->lists('name','id');
            return view('datos_maestros.organization.create',['organizations'=>$organizations]);
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
        DB::transaction(function()
        {
            //vemos si tiene organización padre
            if($_POST['organization_id'] != "")
            {
                $organizacion_padre = $_POST['organization_id'];
            }
            else
            {
                $organizacion_padre = NULL;
            }

            if ($_POST['mision'] != "")
                $mision = $_POST['mision'];
            else
                $mision = NULL;

            if ($_POST['vision'] != "")
                $vision = $_POST['vision'];
            else
                $vision = NULL;

            if ($_POST['target_client'] != "")
                $target_client = $_POST['target_client'];
            else
                $target_client = NULL;

            \Ermtool\Organization::create([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'expiration_date' => $_POST['expiration_date'],
                'shared_services' => $_POST['shared_services'],
                'organization_id' => $organizacion_padre,
                'mision' => $mision,
                'vision' => $vision,
                'target_client' => $target_client
                ]);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Organization created successfully');
            }
            else
            {
                Session::flash('message','Organizaci&oacute;n creada correctamente');
            }
        });

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

        if (Session::get('languaje') == 'en')
        {
            return view('en.datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org]);
        }
        else
        {
            return view('datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org]);
        }
    }


    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function() 
        {
            $organization = \Ermtool\Organization::find($GLOBALS['id1']);
            $organization->status = 1;
            $organization->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Organization blocked successfully');
            }
            else
            {
                Session::flash('message','Organizaci&oacute;n bloqueada correctamente');
            }
        });
        return Redirect::to('/organization');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function() 
        {
            $organization = \Ermtool\Organization::find($GLOBALS['id1']);
            $organization->status = 0;
            $organization->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Organization unblocked successfully');
            }
            
            else
            {
                Session::flash('message','Organizaci&oacute;n desbloqueada correctamente');
            }
            
        });
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
        //hacemos variable id como variable global
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $organization = \Ermtool\Organization::find($GLOBALS['id1']);
            $fecha_creacion = $organization->created_at;
            $fecha_exp = NULL;

            //vemos si tiene organización padre
            if($_POST['organization_id'] != "")
            {
                $organizacion_padre = $_POST['organization_id'];
            }
            else
            {
                $organizacion_padre = NULL;
            }
            if ($_POST['mision'] != "")
                $mision = $_POST['mision'];
            else
                $mision = NULL;

            if ($_POST['vision'] != "")
                $vision = $_POST['vision'];
            else
                $vision = NULL;

            if ($_POST['target_client'] != "")
                $target_client = $_POST['target_client'];
            else
                $target_client = NULL;

            $organization->name = $_POST['name'];
            $organization->description = $_POST['description'];
            $organization->expiration_date = $_POST['expiration_date'];
            $organization->shared_services = $_POST['shared_services'];
            $organization->organization_id = $organizacion_padre;
            $organization->mision = $mision;
            $organization->vision = $vision;
            $organization->target_client = $target_client;

            $organization->save();
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Organization update successfully');
            }    
            else
            {
                Session::flash('message','Organizaci&oacute;n actualizada correctamente');
            }
        });

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
