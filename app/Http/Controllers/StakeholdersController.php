<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use Auth;
use DateTime;
//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class StakeholdersController extends Controller
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
        $this->logger = new Logger('usuarios');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/usuarios.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //definimos por si no hay
                $stakeholder = array();
                $organizaciones = array(); 
                $roles = array(); 
                
                if (isset($_GET['verbloqueados']))
                {
                    $stakeholders = \Ermtool\Stakeholder::where('status',1)->get(); //select stakeholders bloqueadas  
                }
                else
                {
                    $stakeholders = \Ermtool\Stakeholder::where('status',0)->get(); //select stakeholders desbloqueadas
                }

                $i = 0;
                $j = 0; //contador de organizaciones relacionadas 
                $k = 0; //contador de roles
                // ---recorremos todas los stakeholders para asignar formato de datos correspondientes--- //
                foreach ($stakeholders as $persona)
                {
                    //ahora obtenemos todas las organizaciones a las que pertenece cada persona
                    $orgs = \Ermtool\Stakeholder::find($persona['id'])->organizations;
                    
                    //ACTUALIZACIÓN 24-08-17: Configuramos ID extranjero
                    if ($persona['rest_id'] != NULL)
                    {
                        //lo pasamos a string
                        $id1 = (string)$persona['id'];
                        $id2 = (string)$persona['rest_id'];
                        $id_temp = $id1.$id2;
                        //$id_temp = (int)$id_temp;
                        //ACTUALIZACIÓN 01-09-17: FLOAT POR INT PARA NÚMEROS LARGOS
                        $id_temp = (float)$id_temp;
                    }
                    else
                    {
                        $id_temp = $persona['id'];
                    }

                    foreach ($orgs as $organization)
                    {
                         $organizaciones[$j] = array('stakeholder_id'=>$id_temp,
                                                     'id'=>$organization['id'],
                                                     'nombre'=>$organization['name']);

                         $j += 1;
                    }

                    //obtenemos todos los roles a los que pertenece una persona
                    $roles_temp = \Ermtool\Stakeholder::find($persona['id'])->roles;
                    
                    foreach ($roles_temp as $role)
                    {
                        
                         $roles[$k] = array('stakeholder_id'=>$id_temp,
                                                     'id'=>$role['id'],
                                                     'nombre'=>$role['name']);

                         $k += 1;
                    }

                    if ($persona['position'] == NULL)
                        $cargo = NULL;
                    else
                        $cargo = $persona['position'];

                     //damos formato a fecha creación
                    if ($persona['created_at'] != NULL)
                    {
                        $lala = new DateTime($persona['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_creacion = NULL;

                    //damos formato a fecha de actualización 
                    if ($persona['updated_at'] != NULL)
                    {
                        $lala = new DateTime($persona['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");;
                    }

                    else
                        $fecha_act = NULL;

                    $stakeholder[$i] = array('id'=>$id_temp,
                                        'dv'=>$persona['dv'],
                                        'nombre'=>$persona['name'],
                                        'apellidos'=>$persona['surnames'],
                                        'fecha_creacion'=>$fecha_creacion,
                                        'fecha_act'=>$fecha_act,
                                        'cargo'=>$cargo,
                                        'correo'=>$persona['mail'],
                                        'estado'=>$persona['status']);
                    $i += 1;
                }
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.stakeholders.index',['stakeholders'=>$stakeholder,
                                                    'organizaciones'=>$organizaciones,'roles'=>$roles]);
                }
                else
                {
                    return view('datos_maestros.stakeholders.index',['stakeholders'=>$stakeholder,
                                                    'organizaciones'=>$organizaciones,'roles'=>$roles]); 
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
                return Redirect::route('/');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                $roles = \Ermtool\Role::all()->lists('name','id');

                $dv = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','k'=>'k'];
                //si es create, campo rut estara desbloqueado
                $required = 'required';
                $disabled = "";
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.stakeholders.create',['organizations'=>$organizations,'disabled'=>$disabled,'required'=>$required,'roles'=>$roles,'dv'=>$dv]);
                }
                else
                {
                    return view('datos_maestros.stakeholders.create',['organizations'=>$organizations,'disabled'=>$disabled,'required'=>$required,'roles'=>$roles,'dv'=>$dv]);
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
                return Redirect::route('/');
            }
            else
            {
                if (isset($_POST['nacionalidad']))
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
                            'id' => 'unique:stakeholders|min:7',
                            'name' => 'required|max:255|min:2',
                            'surnames' => 'required|min:2'
                        ]);
                        
                        DB::transaction(function()
                        {
                            $logger = $this->logger;

                            if ($_POST['position'] == NULL || $_POST['position'] == "")
                            {
                                $pos = NULL;
                            }
                            else
                            {
                                $pos = $_POST['position'];
                            }
                            /*
                            DB::statement("
                                SET IDENTITY_INSERT stakeholders ON;
                                insert into stakeholders
                                (id, dv, name, surnames, mail, position, updated_at, created_at) 
                                values (".$_POST["id"].",'".$_POST['dv']."','".$_POST['name']."','".$_POST['surnames']."','".$_POST['mail']."','".$pos."','".date("Ymd H:i:s")."','".date("Ymd H:i:s")."')"); */
                            if (isset($GLOBALS['id2']))
                            {
                                $usuario = \Ermtool\Stakeholder::create([
                                    'id' => $GLOBALS['id'],
                                    'dv' => $GLOBALS['dv'],
                                    'name' => $_POST['name'],
                                    'surnames' => $_POST['surnames'],
                                    'position' => $_POST['position'],
                                    'mail' => $_POST['mail'],
                                    'rest_id' => $GLOBALS['id2']
                                ]);
                            }
                            else
                            {
                                $usuario = \Ermtool\Stakeholder::create([
                                    'id' => $GLOBALS['id'],
                                    'dv' => $GLOBALS['dv'],
                                    'name' => $_POST['name'],
                                    'surnames' => $_POST['surnames'],
                                    'position' => $_POST['position'],
                                    'mail' => $_POST['mail']
                                ]);
                            }

                            //otra forma para agregar relaciones -> en comparación a attach utilizado en por ej. SubprocesosController
                            foreach($_POST['organization_id'] as $organization_id)
                            {
                                DB::table('organization_stakeholder')->insert([
                                    'organization_id'=>$organization_id,
                                    'stakeholder_id'=>$GLOBALS['id']
                                    ]);
                            }

                            //INSERTAMOS ROLES
                                //primero verificamos si es que se está agregando un nuevo rol
                                if (isset($_POST['rol_nuevo']))
                                {
                                    $role = \Ermtool\Role::create([
                                        'name' => $_POST['rol_nuevo'],
                                        'status' => 0
                                    ]);

                                    //insertamos relación
                                    DB::table('role_stakeholder')->insert([
                                            'stakeholder_id' => $GLOBALS['id'],
                                            'role_id' => $role->id
                                            ]);
                                }

                                else //se están seleccionando roles existentes
                                {
                                    foreach ($_POST['role_id'] as $role_id) //insertamos cada rol seleccionado
                                    {
                                        DB::table('role_stakeholder')->insert([
                                            'stakeholder_id' => $GLOBALS['id'],
                                            'role_id' => $role_id
                                            ]);
                                    }
                                }

                                if (Session::get('languaje') == 'en')
                                {
                                    Session::flash('message','Stakeholder successfully created');
                                }
                                else
                                {
                                    Session::flash('message','Usuario agregado correctamente');
                                }

                                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el usuario (stakeholder) con Rut: '.$usuario->id.' llamado: '.$usuario->name.' '.$usuario->surnames.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                        });

                        return Redirect::to('/stakeholders');
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','The Id that you entered was incorrect. Please try again.');
                        }
                        else
                        {
                           Session::flash('message','El rut ingresado es incorrecto. Intentelo nuevamente'); 
                        }
                        return Redirect::to('/stakeholders.create')->withInput();
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','You must select nationality and then the Id of the user.');
                    }
                    else
                    {
                       Session::flash('message','Debe seleccionar nacionalidad y luego ingresar Rut o ID del usuario'); 
                    }
                    return Redirect::to('/stakeholders.create')->withInput();
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
            else
            {
                //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                if ($id >= 2147483647)
                {
                    //realizaremos división y guardamos entero
                    $id1 = $id / 100;
                    $id = (int)$id1;
                }
                $types_selected = array();
                $orgs_selected = array();
                $stakeholder = \Ermtool\Stakeholder::find($id);
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                $roles = \Ermtool\Role::all()->lists('name','id');
                $dv = ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','k'=>'k'];

                //buscamos el o los tipos del stakeholder
                $types = DB::table('role_stakeholder')
                            ->where('stakeholder_id','=',$stakeholder->id)
                            ->select('role_id')
                            ->get();

                $i = 0;
                foreach ($types as $type)
                {
                    $types_selected[$i] = $type->role_id;
                    $i += 1;
                }

                //buscamos organizaciones del stakeholder
                $orgs = DB::table('organization_stakeholder')
                            ->where('stakeholder_id','=',$stakeholder->id)
                            ->select('organization_id')
                            ->get();

                $i = 0;
                foreach ($orgs as $org)
                {
                    $orgs_selected[$i] = $org->organization_id;
                    $i += 1;
                }
                //si es edit, campo rut estara bloqueado y no habrá required
                $disabled = 'disabled';
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.stakeholders.edit',['stakeholder'=>$stakeholder,'organizations'=>$organizations,'disabled'=>$disabled,'required'=>'','roles'=>$roles,'dv'=>$dv,'types_selected' => $types_selected,'orgs_selected' => $orgs_selected]);
                }
                else
                {
                    return view('datos_maestros.stakeholders.edit',['stakeholder'=>$stakeholder,'organizations'=>$organizations,'disabled'=>$disabled,'required'=>'','roles'=>$roles,'dv'=>$dv,'types_selected' => $types_selected,'orgs_selected' => $orgs_selected]);
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
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;

                    //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                    if ($GLOBALS['id1'] >= 2147483647)
                    {
                        //realizaremos división y guardamos entero
                        $idtemp = $GLOBALS['id1'] / 100;
                        $GLOBALS['id1'] = (int)$idtemp;
                    }

                    $stakeholder = \Ermtool\Stakeholder::find($GLOBALS['id1']);

                    $stakeholder->name = $_POST['name'];
                    $stakeholder->surnames = $_POST['surnames'];
                    $stakeholder->position = $_POST['position'];
                    $stakeholder->mail = $_POST['mail'];
            
                    $stakeholder->save();

                    //primero que todo, eliminaremos las organizaciones anteriores del stakeholder para evitar repeticiones
                    DB::table('organization_stakeholder')->where('stakeholder_id',$GLOBALS['id1'])->delete();

                    //ahora, agregamos posibles nuevas relaciones
                    foreach($_POST['organization_id'] as $organization_id)
                    {
                        DB::table('organization_stakeholder')->insert([
                            'organization_id'=>$organization_id,
                            'stakeholder_id'=>$GLOBALS['id1']
                            ]);
                    }

                    //nuevamente eliminaremos los roles anteriores del stakeholder para evitar repeticiones
                    DB::table('role_stakeholder')->where('stakeholder_id',$GLOBALS['id1'])->delete();

                    //primero verificamos si es que se está agregando un nuevo rol
                    if (isset($_POST['rol_nuevo']))
                    {
                        $role = \Ermtool\Role::create([
                            'name' => $_POST['rol_nuevo'],
                            'status' => 0
                        ]);

                        //insertamos relación
                        DB::table('role_stakeholder')->insert([
                                'stakeholder_id' => $GLOBALS['id1'],
                                'role_id' => $role->id
                                ]);
                    }
                    else
                    {
                        //ahora, agregamos posibles nuevas relaciones
                        foreach($_POST['role_id'] as $role_id)
                        {
                            DB::table('role_stakeholder')->insert([
                                'role_id'=>$role_id,
                                'stakeholder_id'=>$GLOBALS['id1']
                                ]);
                        }
                    }
                    

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Stakeholder successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Usuario actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el usuario (stakeholder) con Rut: '.$GLOBALS['id1'].' llamado: '.$stakeholder->name.' '.$stakeholder->surnames.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                    return Redirect::to('/stakeholders');
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
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;

                    //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                    if ($GLOBALS['id1'] >= 2147483647)
                    {
                        //realizaremos división y guardamos entero
                        $idtemp = $GLOBALS['id1'] / 100;
                        $GLOBALS['id1'] = (int)$idtemp;
                    }

                    $stakeholder = \Ermtool\Stakeholder::find($GLOBALS['id1']);
                    $stakeholder->status = 1;
                    $stakeholder->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Stakeholder successfully blocked');
                    }
                    else
                    {
                        Session::flash('message','Usuario bloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el usuario (stakeholder) con Rut: '.$GLOBALS['id1'].' llamado: '.$stakeholder->name.' '.$stakeholder->surnames.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                //return Redirect::to('/stakeholders');
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
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;

                    //ACTUALIZACIÓN 24-08-17: Ver si id es mayor a máximo de INT
                    if ($GLOBALS['id1'] >= 2147483647)
                    {
                        //realizaremos división y guardamos entero
                        $idtemp = $GLOBALS['id1'] / 100;
                        $GLOBALS['id1'] = (int)$idtemp;
                    }

                    $stakeholder = \Ermtool\Stakeholder::find($GLOBALS['id1']);
                    $stakeholder->status = 0;
                    $stakeholder->save();
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Stakeholder successfully unblocked');
                    }
                    else
                    {
                        Session::flash('message','Usuario desbloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el usuario (stakeholder) con Rut: '.$GLOBALS['id1'].' llamado: '.$stakeholder->name.' '.$stakeholder->surnames.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                });
                return Redirect::to('/stakeholders');
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
            /*Se debe verificar que no exista en cada tabla en que se necesita un stakeholder, estas son por el momento (08-08-2016):
                - evaluation_risk_stakeholder
                - poll_stakeholder
                - answers
                - evaluation_stakeholder
                - audit_plan_stakeholder
                - audit_tests
                - audit_audit_plan_audit_program
                - action_plans
                - controls
                - risks
                - kpi

            Donde se puede eliminar es:
                - organization_stakeholder
                - role_stakeholder
            */
            
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

                //evaluation_risk_stakeholder
                $rev = DB::table('evaluation_risk_stakeholder')
                        ->where('stakeholder_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //pol_stakeholder
                    $rev = DB::table('poll_stakeholder')
                            ->where('stakeholder_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                    if (empty($rev))
                    {
                        //answers
                        $rev = DB::table('answers')
                                ->where('stakeholder_id','=',$GLOBALS['id1'])
                                ->select('id')
                                ->get();

                        if (empty($rev))
                        {
                            //evaluation_stakeholder
                            $rev = DB::table('evaluation_stakeholder')
                                    ->where('stakeholder_id','=',$GLOBALS['id1'])
                                    ->select('id')
                                    ->get();

                            if (empty($rev))
                            {
                                //audit_plan_stakeholder
                                $rev = DB::table('audit_plan_stakeholder')
                                        ->where('stakeholder_id','=',$GLOBALS['id1'])
                                        ->select('id')
                                        ->get();

                                if (empty($rev))
                                {
                                    $rev = DB::table('audit_tests')
                                            ->where('stakeholder_id','=',$GLOBALS['id1'])
                                            ->select('id')
                                            ->get();

                                    if (empty($rev))
                                    {
                                        $rev = DB::table('audit_audit_plan_audit_program')
                                                ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                ->select('id')
                                                ->get();

                                        if (empty($rev))
                                        {
                                            $rev = DB::table('action_plans')
                                                ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                ->select('id')
                                                ->get();

                                            if (empty($rev))
                                            {
                                                //ACT 04-05-18: Seteamos null en control y control_organization_risk
                                                DB::table('control_organization_risk')
                                                    ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                    ->update([
                                                        'stakeholder_id' => NULL
                                                    ]);

                                                $rev = DB::table('control_organization')
                                                    ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                    ->select('id')
                                                    ->get();

                                                if (empty($rev))
                                                {
                                                    $rev = DB::table('organization_risk')
                                                        ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                        ->select('id')
                                                        ->get();

                                                    if (empty($rev))
                                                    {
                                                        $rev = DB::table('kpi')
                                                            ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                            ->select('id')
                                                            ->get();

                                                        if (empty($rev))
                                                        {
                                                            //obtenemos nombre
                                                            $name = \Ermtool\Stakeholder::getName($GLOBALS['id1']);
                                                            //ahora se puede borrar
                                                            DB::table ('organization_stakeholder')
                                                                ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                                ->delete();

                                                            DB::table('role_stakeholder')
                                                                ->where('stakeholder_id','=',$GLOBALS['id1'])
                                                                ->delete();

                                                            DB::table('stakeholders')
                                                                ->where('id','=',$GLOBALS['id1'])
                                                                ->delete();

                                                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el usuario (stakeholder) con Rut: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                                                            $GLOBALS['res'] = 0;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }    
    }

    //obtiene stakeholders pertenecientes a una organización
    //ACT 24-01-18: Puede ser de cualquier organización
    public function getStakeholders($org)
    {
        try
        {
            $results = array();

            $stakeholders = \Ermtool\Stakeholder::where('status',0)->orderBy('name','asc')->get();

            $i = 0;
            foreach ($stakeholders as $stake)
            {
                $results[$i] = [
                        'rut' => $stake->id,
                        'fullname' => $stake->name.' '.$stake->surnames
                ];
                $i += 1;
            }

            return json_encode($results);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
