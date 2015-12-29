<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use dateTime;

class SubprocesosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subproceso = array();

        if(isset($_GET['verbloqueados']))
        {
            $subprocesos = \Ermtool\Subprocess::all()->where('status',1); //select subprocesos bloqueados 
        }
        else
        {
            $subprocesos = \Ermtool\Subprocess::all()->where('status',0); //select subprocesos desbloqueados
        }
        $i = 0;
        $j = 0; //contador de organizaciones relacionadas 
        $k = 0; //contador de subprocesos relacionados
        // ---recorremos todas los procesos para asignar formato de datos correspondientes--- //
        $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
        $sub_dependientes = array();
        foreach ($subprocesos as $subprocess)
        {

            //ahora obtenemos todas las organizaciones a las que pertenece cada subproceso
            $orgs = \Ermtool\Subprocess::find($subprocess['id'])->organizations;

            foreach ($orgs as $organization)
            {
                 $organizaciones[$j] = array('subprocess_id'=>$subprocess['id'],
                                             'id'=>$organization['id'],
                                             'nombre'=>$organization['name']);

                 $j += 1;
            }
        
            $subprocesos_dependientes = \Ermtool\Subprocess::all()->where('subprocess_id',$subprocess['id']);
            
            
            foreach ($subprocesos_dependientes as $hijos)
            {
                $sub_dependientes[$k] = array('subprocess_id'=>$subprocess['id'],
                                             'id'=>$hijos['id'],
                                             'nombre'=>$hijos['name']);
                $k += 1;
            }

            //damos formato a fecha expiración
            if ($subprocess['expiration_date'] == NULL OR $subprocess['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
            {
                $expiration_date = new DateTime($subprocess['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            //damos formato a fecha creación
            if ($subprocess['created_at'] != NULL)
            {
                $fecha_creacion = date_format($subprocess['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($subprocess['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización 
            if ($subprocess['updated_at'] != NULL)
            {
                $fecha_act = date_format($subprocess['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($subprocess['updated_at'],"H:i:s");
            }
            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            //$proceso = \Ermtool\Subprocess::find($subprocess['id'])->processes; No me funciono
            $proceso = \Ermtool\Process::find($subprocess['process_id']);

            $subproceso[$i] = array('id'=>$subprocess['id'],
                                'nombre'=>$subprocess['name'],
                                'descripcion'=>$subprocess['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'proceso_relacionado'=>$proceso['name'],
                                'estado'=>$subprocess['status']);
            $i += 1;
        }

        return view('datos_maestros.subprocesos.index',['subprocesos'=>$subproceso,'sub_dependientes'=>$sub_dependientes,'organizaciones'=>$organizaciones]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $procesos = \Ermtool\Process::where('status',0)->lists('name','id');

        //Seleccionamos subprocesos que pueden ser padres
        $subprocesos = \Ermtool\Subprocess::where('subprocess_id',NULL)->where('status',0)->lists('name','id');

        $organizaciones = \Ermtool\Organization::where('status',0)->lists('name','id');

        return view('datos_maestros.subprocesos.create',['procesos'=>$procesos,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);
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

        if($request['subprocess_id'] == NULL)
        {
            $subprocess_id = NULL;
        }
        else
        {
            $subprocess_id = $request['subprocess_id'];
        }

        \Ermtool\Subprocess::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'expiration_date' => $fecha_exp,
            'process_id' => $request['process_id'],
            'subprocess_id' => $subprocess_id,
            ]);

        //agregamos la relación a cada organización
            // primero obtenemos subproceso que acabamos de agregar   
            $subprocess = \Ermtool\Subprocess::max('id');

            foreach ($request['organization_id'] as $organization_id)
            {
                $organization = \Ermtool\Organization::find($organization_id);
                //agregamos la relación (para agregar en atributos)
                $organization->subprocesses()->attach($subprocess);
            }

            Session::flash('message','Subproceso agregado correctamente');

            return Redirect::to('/subprocesos');
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
        $subproceso = \Ermtool\Subprocess::find($id);
        $procesos = \Ermtool\Process::all()->where('status',0)->lists('name','id');

        //Seleccionamos subprocesos que pueden ser padres
        $subprocesos = \Ermtool\Subprocess::all()->where('subprocess_id',NULL)->where('status',0)->where('id','<>',$id)->lists('name','id');

        $organizaciones = \Ermtool\Organization::all()->where('status',0)->lists('name','id');

        return view('datos_maestros.subprocesos.edit',['procesos'=>$procesos,'subprocesos'=>$subprocesos,
            'subproceso'=>$subproceso,'organizaciones'=>$organizaciones]);
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
        $subproceso = \Ermtool\Subprocess::find($id);
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

        //vemos si tiene subproceso padre
        if($request['subprocess_id'] != "")
        {
            $subprocess_id = $request['subprocess_id'];
        }
        else
        {
            $subprocess_id = NULL;
        }

        $subproceso->name = $request['name'];
        $subproceso->description = $request['description'];
        $subproceso->expiration_date = $fecha_exp;
        $subproceso->process_id = $request['process_id'];
        $subproceso->subprocess_id = $subprocess_id;

        //deberemos quitar las relaciones, y luego agregar las nuevas para este subproceso
        //primero eliminaremos todas las relaciones de organizaciones con subprocesos donde el subproceso sea el que se está editando
        $org_sub = DB::table('organization_subprocess')->where('subprocess_id',$id)->lists('organization_id');

        foreach ($org_sub as $organization_id)
        {
            $subproceso->organizations()->detach($organization_id);
        }

        //ahora agregamos las relaciones con las nuevas organizaciones
        foreach ($request['organization_id'] as $organization_id)
        {
            $organization = \Ermtool\Organization::find($organization_id);
            //agregamos la relación (para agregar en atributos)
               $organization->subprocesses()->attach($id);
        }

        $subproceso->save();

        Session::flash('message','Subproceso actualizado correctamente');

        return Redirect::to('/subprocesos');
    }

    public function bloquear($id)
    {
        $subproceso = \Ermtool\Subprocess::find($id);
        $subproceso->status = 1;
        $subproceso->save();

        Session::flash('message','Subproceso bloqueado correctamente');

        return Redirect::to('/subprocesos');
    }

    public function desbloquear($id)
    {
        $subproceso = \Ermtool\Subprocess::find($id);
        $subproceso->status = 0;
        $subproceso->save();

        Session::flash('message','Subproceso desbloqueado correctamente');

        return Redirect::to('/subprocesos');
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
