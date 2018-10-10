<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Redirect;
use DB;
use Hash;
use Ermtool\Http\Requests;
use Ermtool\Http\Requests\LoginRequest;
use Ermtool\Http\Controllers\Controller;
use DateTime;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class LogController extends Controller
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
        $this->logger = new Logger('usuarios_sistema');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/usuarios_sistema.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());

        $this->logger2 = new Logger('pass');
        $this->logger2->pushHandler(new StreamHandler($dir.'/storage/logs/cambio_pass.log', Logger::INFO));
        $this->logger2->pushHandler(new FirePHPHandler());

        $this->logger3 = new Logger('sessions');
        $this->logger3->pushHandler(new StreamHandler($dir.'/storage/logs/sessions.log', Logger::INFO));
        $this->logger3->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
            {
                return Redirect::to('home');
            }
            else
            {
                $users = array();
                $usuarios = \Ermtool\User::all();

                $i = 0;
                foreach ($usuarios as $user)
                {
                    //obtenemos roles
                    $roles = array();
                    $roles1 = DB::table('system_role_user')
                                ->join('system_roles','system_roles.id','=','system_role_user.system_role_id')
                                ->where('system_role_user.user_id','=',$user->id)
                                ->select('system_roles.role')
                                ->get();
                    $j = 0;
                    foreach ($roles1 as $rol)
                    {
                        $roles[$j] = $rol->role;
                        $j += 1;
                    }
                    
                    $lala = new DateTime($user->created_at);
                    $created_at = date_format($lala, 'd-m-Y');

                    //ACTUALIZACIÓN 24-08-17: Configuramos ID extranjero
                    if ($user->rest_id != NULL)
                    {
                        //lo pasamos a string
                        $id1 = (string)$user->id;
                        $id2 = (string)$user->rest_id;
                        $id_temp = $id1.$id2;
                        $id_temp = (int)$id_temp;
                    }
                    else
                    {
                        $id_temp = $user->id;
                    }

                    $users[$i] = [
                        'id' => $id_temp,
                        'dv' => $user->dv,
                        'name' => $user->name,
                        'surnames' => $user->surnames,
                        'email' => $user->email,
                        'created_at' => $created_at,
                        'roles' => $roles,
                    ];
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                   return view('en.usuarios.index',['users' => $users]); 
                }
                else
                {
                    return view('usuarios.index',['users' => $users]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function createUser()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
            {
                return Redirect::to('home');
            }
            else
            {
                foreach (Session::get('roles') as $role)
                {
                    if ($role != 1)
                    {
                        return Redirect::route('home');
                    }
                    else
                    {
                        break;
                    }
                }
            }

            $dv = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','k'=>'k'];
            //si es create, campo rut estara desbloqueado
            $required = 'required';
            $disabled = "";

            $system_roles = \Ermtool\System_role::lists('role','id');

            if (Session::get('languaje') == 'en')
            {
               return view('en.usuarios.create',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled]); 
            }
            else
            {
                return view('usuarios.create',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function storeUser(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
            {
                return Redirect::to('home');
            }
            else
            {
                global $id;
                global $dv;
                //ACTUALIZACIÓN 20-08-17: Validaremos rut sólo si se ingresa Chileno
                if ($_POST['nacionalidad'] == 'chileno')
                {
                    //validamos rut
                    $rut = $_POST['id'].'-'.$_POST['dv'];
                    $res = validaRut($rut);
                    $id = $_POST['id'];
                    $dv = $_POST['dv'];
                }
                else
                {
                    //ACTUALIZACIÓN 24-08-17: Veremos si el id es mayor o igual al máximo permitido por INT
                    if ($_POST['id2'] >= 2147483647)
                    {
                        //realizaremos división y guardamos entero
                        $id = $_POST['id2'] / 100;
                        $id = (int)$id;

                        //ahora guardamos resto (utilizamos función substr por si resto parte con 0)
                        global $id2;
                        $id2 = (string)$_POST['id2'];
                        $id2 = substr($id2, -2);
                        $res = true;
                        $dv = null;
                    }
                    else
                    {
                        $res = true;
                        $id = $_POST['id2'];
                        $dv = null;
                    }
                }

                if ($res)
                {
                    //Validación: Si la validación es pasada, el código continua
                    $this->validate($request, [
                        'id' => 'unique:users|min:7',
                        'name' => 'required|max:45|min:2',
                        'email' => 'unique:users',
                        'password' => 'required|min:4'
                    ]);

                    global $req;
                    $req = $request;

                    DB::transaction(function() {

                        $logger = $this->logger;

                        $GLOBALS['req']->merge(['password' => Hash::make($GLOBALS['req']->password)]);

                        if (isset($GLOBALS['id2']))
                        {
                            $user = \Ermtool\User::create([
                                    'id' => $GLOBALS['id'],
                                    'dv' => $GLOBALS['dv'],
                                    'name' => $_POST['name'],
                                    'surnames' => $_POST['surnames'],
                                    'email' => $_POST['email'],
                                    'password' => $GLOBALS['req']['password'],
                                    'rest_id' => $GLOBALS['id2'],
                                    'cc_user' => isset($_POST['cc_user']) ? $_POST['cc_user'] : NULL
                                ]);
                        }
                        else
                        {
                            $user = \Ermtool\User::create([
                                    'id' => $GLOBALS['id'],
                                    'dv' => $GLOBALS['dv'],
                                    'name' => $_POST['name'],
                                    'surnames' => $_POST['surnames'],
                                    'email' => $_POST['email'],
                                    'password' => $GLOBALS['req']['password'],
                                    'cc_user' => isset($_POST['cc_user']) ? $_POST['cc_user'] : NULL
                                ]);
                        }
                        //agregamos en system_role_user (si es que se agregaron roles)
                        if (isset($GLOBALS['req']['system_roles_id']))
                        {
                            foreach ($GLOBALS['req']['system_roles_id'] as $role)
                            {
                                DB::table('system_role_user')
                                    ->insert([
                                        'user_id' => $GLOBALS['id'],
                                        'system_role_id' => $role,
                                    ]);
                            }
                        }
                        
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','User successfully created');
                        }
                        else
                        {
                            Session::flash('message','Usuario creado con &eacute;xito');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el usuario con Id: '.$user->id.' llamado: '.$user->name.' '.$user->surnames.', con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));
                    });
                
                    return Redirect::to('usuarios');
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','The entered id was incorrect. Try again');
                        return Redirect::to('usuario.create')->withInput();
                    }
                    else
                    {
                        Session::flash('message','El rut ingresado es incorrecto. Intentelo nuevamente');
                        return Redirect::to('usuario.create')->withInput();
                    }
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
    public function store(LoginRequest $request)
    {
        try
        {
            Session::put('languaje',$request['languaje']);

            //ACT 27-04-18: Agregamos nombre de organización a la Sesión, para envío de correos de error
            $org = \Ermtool\Configuration::where('option_name','organization')->first(['option_value']);

            if (!empty($org))
            {
                $org_name = $org->option_value;
            }
            else
            {
                $org_name = NULL;
            }

            //ACT 10-10-18: También agregamos short_name para style.css
            $short_name = \Ermtool\Configuration::where('option_name','short_name')->first(['option_value']);

            if (!empty($short_name))
            {
                $short_name = $short_name->option_value;
            }
            else
            {
                $short_name = NULL;
            }

            //Guardamos en variable de sesión
            Session::put('org',$org_name);
            Session::put('short_name',$short_name);

            //return $request->email;
            if (Auth::attempt(['email'=>$request['email'], 'password' => $request['password']]))
            {
                $logger = $this->logger3;
                //echo Auth::user()->id.'<br>'.Auth::user()->name;
                
                //obtenemos roles del usuario
                $id = Auth::user()->id;

                $roles1 = DB::table('system_role_user')
                            ->join('system_roles','system_roles.id','=','system_role_user.system_role_id')
                            ->where('system_role_user.user_id','=',$id)
                            ->select('system_roles.id','system_roles.role')
                            ->get();

                $i = 0;
                foreach ($roles1 as $role)
                {
                    Session::push('roles',$role->id);
                    Session::push('roles_name',$role->role);
                    $i += 1;
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha ingresado al sistema con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));
            }
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message-error','Incorrect User and/or Pass. Try again.');
            }
            else
            {
                Session::flash('message-error','Usuario y/o contraseña incorrecta! vuelva a intentarlo');
            }
            return Redirect::to('/');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('roles');
        Session::forget('roles_name');
        Session::forget('languaje');
        return Redirect::to('/');
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
                return Redirect::route('/');
            }
            else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
            {
                return Redirect::to('home');
            }
            else
            {
                foreach (Session::get('roles') as $role)
                {
                    if ($role != 1)
                    {
                        return Redirect::route('home');
                    }
                    else
                    {
                        break;
                    }
                }
            }

            //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
            if ($id >= 2147483647)
            {
                //realizaremos división y guardamos entero
                $id1 = $id / 100;
                $id = (int)$id1;
            }
            $user = \Ermtool\User::find($id);
            $dv = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','k'=>'k'];
            //si es create, campo rut estara desbloqueado
            $required = '';
            $disabled = "disabled";
            $system_roles_selected = array();
            $system_roles = \Ermtool\System_role::lists('role','id');

            //obtenemos roles seleccionados
            $roles = DB::table('system_role_user')
                        ->where('user_id','=',$user->id)
                        ->select('system_role_id')
                        ->get();

            $i = 0;
            foreach ($roles as $rol)
            {
                $system_roles_selected[$i] = $rol->system_role_id;
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
               return view('en.usuarios.edit',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled,'user' => $user, 'system_roles_selected' => $system_roles_selected]); 
            }
            else
            {
                return view('usuarios.edit',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled, 'user' => $user, 'system_roles_selected' => $system_roles_selected]);
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
            //OBS: Hacer validación manual de e-mail
            //$this->validate($request, [
            //    'name' => 'required|max:45|min:4',
            //    'password' => 'required|min:4'
            //]);

            global $req;
            $req = $request;

            global $id1;
            $id1 = $id;

            DB::transaction(function() {

                $logger = $this->logger;

                //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                if ($GLOBALS['id1'] >= 2147483647)
                {
                    //realizaremos división y guardamos entero
                    $idtemp = $GLOBALS['id1'] / 100;
                    $GLOBALS['id1'] = (int)$idtemp;
                }

                $user = \Ermtool\User::find($GLOBALS['id1']);

                $GLOBALS['req']->merge(['password' => Hash::make($GLOBALS['req']->password)]);

                $user->name = $_POST['name'];
                $user->surnames = $_POST['surnames'];
                $user->email = $_POST['email'];
                $user->password = $GLOBALS['req']->password;
                $user->cc_user = isset($_POST['cc_user']) ? $_POST['cc_user'] : NULL;
                $user->save();

                //nuevamente eliminaremos los roles anteriores del stakeholder para evitar repeticiones
                DB::table('system_role_user')->where('user_id',$GLOBALS['id1'])->delete();
                
                //ahora agregamos en system_role_user
                if (isset($GLOBALS['req']['system_roles_id']))
                {
                    foreach ($GLOBALS['req']['system_roles_id'] as $role)
                    {
                        DB::table('system_role_user')
                            ->insert([
                                'user_id' => $GLOBALS['id1'],
                                'system_role_id' => $role,
                            ]);
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','User successfully created');
                }
                else
                {
                    Session::flash('message','Usuario actualizado con &eacute;xito');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el usuario con Id: '.$user->id.' llamado: '.$user->name.' '.$user->surnames.', con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));
            });
            
            return Redirect::to('usuarios');
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

                //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                if ($GLOBALS['id1'] >= 2147483647)
                {
                    //realizaremos división y guardamos entero
                    $idtemp = $GLOBALS['id1'] / 100;
                    $GLOBALS['id1'] = (int)$idtemp;
                }
                //obtenemos nombre para log
                $user = DB::table('users')
                        ->where('id','=',$GLOBALS['id1'])
                        ->select('name','surnames')
                        ->first();

                //primero eliminamos de system_role_user
                DB::table('system_role_user')
                    ->where('user_id','=',$GLOBALS['id1'])
                    ->delete();

                DB::table('users')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                $GLOBALS['res'] = 0;

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el usuario con Id: '.$GLOBALS['id1'].' llamado: '.$user->name.' '.$user->surnames.', con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));

            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function changePass()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //$id = Auth::user()->id;
                if (Session::get('languaje') == 'en')
                {
                   return view('en.usuarios.cambiopass',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled]); 
                }
                else
                {
                    return view('cambiopass');
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function storeNewPass()
    {
        try
        {
            //print_r($_POST);

            //echo '<br>'.Auth::user()->id;
            //echo '<br>'.Auth::user()->password;
            $logger = $this->logger2;

            //Verificamos que la contraseña antigua ingresada sea la misma
            if (Hash::check($_POST['pass_old'], Auth::user()->password))
            {
                $user = \Ermtool\User::find(Auth::user()->id);
                $newpass = Hash::make($_POST['password']);
                //actualizamos pass de Auth por si se vuelve a cambiar
                Auth::user()->password = $newpass;
                $user->password = $newpass;
                $user->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Password successfully updated');
                }
                else
                {
                    Session::flash('message','Contraseña actualizada con &eacute;xito');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha modificado su contraseña, con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));

                return Redirect::route('home');
            }
            else
            {
                Session::flash('message','La contraseña actual ingresada no es correcta. Por favor inténtelo nuevamente');
                    return Redirect::to('cambiopass')->withInput();
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
