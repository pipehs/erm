<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //definimos por si no hay
        $roles2 = array();
        $cantidad = 0; 
        
        if (isset($_GET['verbloqueados']))
        {
            $roles = \Ermtool\Role::where('status',1)->get(); //select stakeholders bloqueadas  
        }
        else
        {
            $roles = \Ermtool\Role::where('status',0)->get(); //select stakeholders desbloqueadas
        }

        $i = 0;

        // ---recorremos todas los roles para asignar formato de datos correspondientes--- //
        foreach ($roles as $rol)
        {
            //contamos cantidad de roles
            $cont = DB::table('role_stakeholder')
                        ->where('role_id','=',$rol['id'])
                        ->count();
             //damos formato a fecha creación
            if ($rol['created_at'] != NULL)
            {
                $fecha_creacion = date_format($rol['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($rol['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización 
            if ($rol['updated_at'] != NULL)
            {
                $fecha_act = date_format($rol['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($rol['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $roles2[$i] = array('id'=>$rol['id'],
                                'nombre'=>$rol['name'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'status'=>$rol['status'],
                                'cantidad' => $cont);
            $i += 1;
        }
        return view('datos_maestros.roles.index',['roles'=>$roles2]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('datos_maestros.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Ermtool\Role::create([
            'name' => $request['name'],
            'status' => 0
            ]);

            Session::flash('message','Rol agregado correctamente');

            return Redirect::to('/roles');
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
        $rol = \Ermtool\Role::find($id);
        return view('datos_maestros.roles.edit',['rol' => $rol]);
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
            $role = \Ermtool\Role::find($id);

            $role->name = $request['name'];
    
            $role->save();

            Session::flash('message','Rol actualizado correctamente');

            return Redirect::to('/roles');
    }

    public function bloquear($id)
    {
        $role = \Ermtool\Role::find($id);
        $role->status = 1;
        $role->save();

        Session::flash('message','Rol bloqueado correctamente');

        return Redirect::to('/roles');
    }

    public function desbloquear($id)
    {
        $role = \Ermtool\Role::find($id);
        $role->status = 0;
        $role->save();

        Session::flash('message','Rol desbloqueado correctamente');

        return Redirect::to('/roles');
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
