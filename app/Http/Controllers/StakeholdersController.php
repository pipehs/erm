<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;

class StakeholdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
           

            foreach ($orgs as $organization)
            {
                 $organizaciones[$j] = array('stakeholder_id'=>$persona['id'],
                                             'id'=>$organization['id'],
                                             'nombre'=>$organization['name']);

                 $j += 1;
            }

            //obtenemos todos los roles a los que pertenece una persona
            $roles_temp = \Ermtool\Stakeholder::find($persona['id'])->roles;
           

            foreach ($roles_temp as $role)
            {
                 $roles[$k] = array('stakeholder_id'=>$persona['id'],
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
                $fecha_creacion = date_format($persona['created_at'],"d-m-Y");
            }
            else
                $fecha_creacion = NULL;

            //damos formato a fecha de actualización 
            if ($persona['updated_at'] != NULL)
            {
                $fecha_act = date_format($persona['updated_at'],"d-m-Y");;
            }

            else
                $fecha_act = NULL;

            $stakeholder[$i] = array('id'=>$persona['id'],
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validamos rut
        $rut = $_POST['id'].'-'.$_POST['dv'];
        $res = validaRut($rut);

        if ($res)
        {
            //Validación: Si la validación es pasada, el código continua
            $this->validate($request, [
                'id' => 'unique:stakeholders|min:7',
                'name' => 'required|max:45|min:4',
                'surnames' => 'required|min:4',
                'mail' => 'unique:stakeholders',
            ]);
            DB::transaction(function()
            {
                \Ermtool\Stakeholder::create([
                    'id' => $_POST['id'],
                    'dv' => $_POST['dv'],
                    'name' => $_POST['name'],
                    'surnames' => $_POST['surnames'],
                    'position' => $_POST['position'],
                    'mail' => $_POST['mail']
                    ]);

                //otra forma para agregar relaciones -> en comparación a attach utilizado en por ej. SubprocesosController
                foreach($_POST['organization_id'] as $organization_id)
                {
                    DB::table('organization_stakeholder')->insert([
                        'organization_id'=>$organization_id,
                        'stakeholder_id'=>$_POST['id']
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
                                'stakeholder_id' => $_POST['id'],
                                'role_id' => $role->id
                                ]);
                    }

                    else //se están seleccionando roles existentes
                    {
                        foreach ($_POST['role_id'] as $role_id) //insertamos cada rol seleccionado
                        {
                            DB::table('role_stakeholder')->insert([
                                'stakeholder_id' => $_POST['id'],
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
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

            //ahora, agregamos posibles nuevas relaciones
            foreach($_POST['role_id'] as $role_id)
            {
                DB::table('role_stakeholder')->insert([
                    'role_id'=>$role_id,
                    'stakeholder_id'=>$GLOBALS['id1']
                    ]);
            }

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Stakeholder successfully updated');
            }
            else
            {
                Session::flash('message','Usuario actualizado correctamente');
            }
        });
            return Redirect::to('/stakeholders');
    }

    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
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
        });
        return Redirect::to('/stakeholders');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
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
        });
        return Redirect::to('/stakeholders');
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

    //obtiene stakeholders pertenecientes a una organización
    public function getStakeholders($org)
    {
        $results = array();

        $stakeholders = \Ermtool\Organization::find($org)->stakeholders;

        $i = 0;
        foreach ($stakeholders as $stake)
        {
            $results[$i] = [
                    'rut' => $stake['id'],
                    'fullname' => $stake['name'].' '.$stake['surnames']
            ];
            $i += 1;
        }

        return json_encode($results);
    }
}
