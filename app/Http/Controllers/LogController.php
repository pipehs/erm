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

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
                if ($role == 6)
                {
                    return Redirect::route('home');
                }
            }
        }

        $dv = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','k'=>'k'];
        //si es create, campo rut estara desbloqueado
        $required = 'required';
        $disabled = "";

        $system_roles = \Ermtool\System_role::lists('role','id');
        return view('usuarios.create',['system_roles' => $system_roles,'dv' => $dv,'required' => $required,'disabled' => $disabled]);
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
            ]);

            global $req;
            $req = $request;

            DB::transaction(function() {

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
                
                Session::flash('message','Usuario creado con &eacute;xito');
            });
        
            return Redirect::to('/');
        }
        else
        {
            Session::flash('message','El rut ingresado es incorrecto. Intentelo nuevamente');
            return Redirect::to('crear_usuario')->withInput();
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
        //return $request->email;
        if (Auth::attempt(['email'=>$request['email'], 'password' => $request['password']]))
        {
            //echo Auth::user()->id.'<br>'.Auth::user()->name;

            //obtenemos roles del usuario
            $id = Auth::user()->id;

            $roles1 = DB::table('system_role_user')
                        ->where('user_id','=',$id)
                        ->select('system_role_id')
                        ->get();

            $i = 0;
            foreach ($roles1 as $role)
            {
                Session::push('roles',$role->system_role_id);
                $i += 1;
            }
        }

        Session::flash('message-error','Usuario y/o contraseña incorrecta! vuelva a intentarlo');
        return Redirect::to('/');
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('roles');
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
        //
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
        //
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
