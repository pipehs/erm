<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Auth;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
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
                }
                else
                    $fecha_creacion = NULL;

                //damos formato a fecha de actualización 
                if ($rol['updated_at'] != NULL)
                {
                    $fecha_act = date_format($rol['updated_at'],"d-m-Y");
                }

                else
                    $fecha_act = NULL;

                $roles2[$i] = array('id'=>$rol['id'],
                                    'nombre'=>$rol['name'],
                                    'fecha_creacion'=>$fecha_creacion,
                                    'fecha_act'=>$fecha_act,
                                    'status'=>$rol['status'],
                                    'cantidad' => $cont);
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.roles.index',['roles'=>$roles2]);
            }
            else
            {
                return view('datos_maestros.roles.index',['roles'=>$roles2]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.roles.create');
            }
            else
            {
                return view('datos_maestros.roles.create');
            }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            \Ermtool\Role::create([
                'name' => $request['name'],
                'status' => 0
                ]);

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Role successfully created');
                }
                else
                {
                    Session::flash('message','Rol agregado correctamente');
                }

            return Redirect::to('/roles');
        }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $rol = \Ermtool\Role::find($id);
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.roles.edit',['rol' => $rol]);
            }
            else
            {
                return view('datos_maestros.roles.edit',['rol' => $rol]);
            }
        }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $role = \Ermtool\Role::find($id);

            $role->name = $request['name'];
    
            $role->save();
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Role successfully updated');
            }
            else
            {
                Session::flash('message','Rol actualizado correctamente');
            }

            return Redirect::to('/roles');
        }
    }

    public function bloquear($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $role = \Ermtool\Role::find($id);
            $role->status = 1;
            $role->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Role successfully blocked');
            }
            else
            {
                Session::flash('message','Rol bloqueado correctamente');
            }

            return Redirect::to('/roles');
        }
    }

    public function desbloquear($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $role = \Ermtool\Role::find($id);
            $role->status = 0;
            $role->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Role successfully unblocked');
            }
            else
            {
                Session::flash('message','Rol desbloqueado correctamente');
            }

            return Redirect::to('/roles');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;

        DB::transaction(function() {

            //eliminamos primero role_stakeholder (si es que tiene)
            DB::table('role_stakeholder')
                ->where('role_id','=',$GLOBALS['id1'])
                ->delete();

            //ahora eliminamos rol
            DB::table('roles')
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

            $GLOBALS['res'] = 0;
        });

        return $res;
    }
}
