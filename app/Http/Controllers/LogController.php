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
        $this->logger = new Logger('usuarios_sistema');
        $this->logger->pushHandler(new StreamHandler($_SERVER['DOCUMENT_ROOT'].'/storage/logs/usuarios_sistema.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
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

                $users[$i] = [
                    'id' => $user->id,
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

    public function createUser()
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
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

    public function storeUser(Request $request)
    {
        //validamos rut
        $rut = $_POST['id'].'-'.$_POST['dv'];
        $res = validaRut($rut);

        if ($res)
        {
            //Validación: Si la validación es pasada, el código continua
            $this->validate($request, [
                'id' => 'unique:users|min:7',
                'name' => 'required|max:45|min:4',
                'email' => 'unique:users',
                'password' => 'required|min:4'
            ]);

            global $req;
            $req = $request;

            DB::transaction(function() {

                $logger = $this->logger;

                $GLOBALS['req']->merge(['password' => Hash::make($GLOBALS['req']->password)]);

                $user = \Ermtool\User::create($GLOBALS['req']->all());

                //agregamos en system_role_user
                foreach ($GLOBALS['req']['system_roles_id'] as $role)
                {
                    DB::table('system_role_user')
                        ->insert([
                            'user_id' => $GLOBALS['req']['id'],
                            'system_role_id' => $role,
                        ]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {
        Session::put('languaje',$request['languaje']);
        //return $request->email;
        if (Auth::attempt(['email'=>$request['email'], 'password' => $request['password']]))
        {
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
        if (Auth::guest())
        {
            return Redirect::route('/');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //OBS: Hacer validación manual de e-mail
        $this->validate($request, [
            'name' => 'required|max:45|min:4',
            'password' => 'required|min:4'
        ]);

        global $req;
        $req = $request;

        global $id1;
        $id1 = $id;

        DB::transaction(function() {

            $logger = $this->logger;

            $user = \Ermtool\User::find($GLOBALS['id1']);

            $GLOBALS['req']->merge(['password' => Hash::make($GLOBALS['req']->password)]);

            $user->name = $_POST['name'];
            $user->surnames = $_POST['surnames'];
            $user->email = $_POST['email'];
            $user->password = $GLOBALS['req']->password;
            
            $user->save();

            //nuevamente eliminaremos los roles anteriores del stakeholder para evitar repeticiones
            DB::table('system_role_user')->where('user_id',$GLOBALS['id1'])->delete();

            //ahora agregamos en system_role_user
            foreach ($GLOBALS['req']['system_roles_id'] as $role)
            {
                DB::table('system_role_user')
                    ->insert([
                        'user_id' => $GLOBALS['id1'],
                        'system_role_id' => $role,
                    ]);
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

            $logger = $this->logger;

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

            $logger->info('The user '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el usuario con Id: '.$GLOBALS['id1'].' llamado: '.$user->name.' '.$user->surnames.', con fecha '.date('d-m-Y H:i:s').' a las '.date('H:i:s'));

        });

        return $res;
    }

    public function changePass()
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

    public function storeNewPass()
    {
        //print_r($_POST);

        //echo '<br>'.Auth::user()->id;
        //echo '<br>'.Auth::user()->password;

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

            return Redirect::route('home');
        }
        else
        {
            Session::flash('message','La contraseña actual ingresada no es correcta. Por favor inténtelo nuevamente');
                return Redirect::to('cambiopass')->withInput();
        }
    }
}
