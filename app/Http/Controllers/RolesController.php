<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Auth;
use DateTime;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('roles');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/roles.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }
    
    public function index()
    {
        try
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
                        $lala = new DateTime($rol['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_creacion = NULL;

                    //damos formato a fecha de actualización 
                    if ($rol['updated_at'] != NULL)
                    {
                        $lala = new DateTime($rol['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger;

                $role = \Ermtool\Role::create([
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

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el rol con Id: '.$role->id.' llamado: '.$role->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/roles');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
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
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger;

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

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el rol con Id: '.$id.' llamado: '.$role->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/roles');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function bloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger;

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

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el rol con Id: '.$id.' llamado: '.$role->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/roles');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function desbloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $logger = $this->logger;

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

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el rol con Id: '.$id.' llamado: '.$role->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/roles');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;

            DB::transaction(function() {

                $logger = $this->logger;

                //obtenemos nombre
                $name = \Ermtool\Role::name($GLOBALS['id1']);
                //eliminamos primero role_stakeholder (si es que tiene)
                DB::table('role_stakeholder')
                    ->where('role_id','=',$GLOBALS['id1'])
                    ->delete();

                //ahora eliminamos rol
                DB::table('roles')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el rol con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                $GLOBALS['res'] = 0;
            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
