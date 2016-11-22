<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use dateTime;
use Auth;

class SubprocesosController extends Controller
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
            $subproceso = array();

            if(isset($_GET['verbloqueados']))
            {
                $subprocesos = \Ermtool\Subprocess::where('status',1)->get(); //select subprocesos bloqueados 
            }
            else
            {
                $subprocesos = \Ermtool\Subprocess::where('status',0)->get(); //select subprocesos desbloqueados
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
            
                $subprocesos_dependientes = \Ermtool\Subprocess::where('subprocess_id',$subprocess['id'])->get();
                
                
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
                    $fecha_exp = NULL;
                }
                else 
                {
                    $expiration_date = new DateTime($subprocess['expiration_date']);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                }

                //damos formato a fecha creación
                if ($subprocess['created_at'] != NULL)
                {
                    $fecha_creacion = date_format($subprocess['created_at'],"d-m-Y");
                }
                else
                    $fecha_creacion = NULL;

                //damos formato a fecha de actualización 
                if ($subprocess['updated_at'] != NULL)
                {
                    $fecha_act = date_format($subprocess['updated_at'],"d-m-Y");
                }
                else
                    $fecha_act = NULL;

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

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.subprocesos.index',['subprocesos'=>$subproceso,'sub_dependientes'=>$sub_dependientes,'organizaciones'=>$organizaciones]);
            }
            else
            {
                return view('datos_maestros.subprocesos.index',['subprocesos'=>$subproceso,'sub_dependientes'=>$sub_dependientes,'organizaciones'=>$organizaciones]);
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
            $procesos = \Ermtool\Process::where('status',0)->lists('name','id');

            //Seleccionamos subprocesos que pueden ser padres
            $subprocesos = \Ermtool\Subprocess::where('subprocess_id',NULL)->where('status',0)->lists('name','id');

            $organizaciones = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.subprocesos.create',['procesos'=>$procesos,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);
            }
            else
            {
                return view('datos_maestros.subprocesos.create',['procesos'=>$procesos,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);
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
            DB::transaction(function()
            {
                if($_POST['subprocess_id'] == NULL)
                {
                    $subprocess_id = NULL;
                }
                else
                {
                    $subprocess_id = $_POST['subprocess_id'];
                }

                $new_subprocess = \Ermtool\Subprocess::create([
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'expiration_date' => $_POST['expiration_date'],
                    'process_id' => $_POST['process_id'],
                    'subprocess_id' => $subprocess_id,
                    ]);

                //agregamos la relación a cada organización
                    // primero obtenemos subproceso que acabamos de agregar   
                    $subprocess = $new_subprocess->id;

                    foreach ($_POST['organization_id'] as $organization_id)
                    {
                        $organization = \Ermtool\Organization::find($organization_id);
                        //agregamos la relación (para agregar en atributos)
                        $organization->subprocesses()->attach($subprocess);
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Process successfully created');
                    }
                    else
                    {
                        Session::flash('message','Subproceso agregado correctamente');
                    }
            });
            return Redirect::to('/subprocesos');
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
            $orgs_selected = array();
            $subproceso = \Ermtool\Subprocess::find($id);
            $procesos = \Ermtool\Process::where('status',0)->lists('name','id');

            //obtenemos organizaciones del subproceso
            $orgs = DB::table('organization_subprocess')
                    ->where('subprocess_id','=',$id)
                    ->select('organization_id')
                    ->get();

            $i = 0;
            foreach ($orgs as $org)
            {
                $orgs_selected[$i] = $org->organization_id;
                $i += 1;
            }
            //Seleccionamos subprocesos que pueden ser padres
            $subprocesos = \Ermtool\Subprocess::where('subprocess_id',NULL)->where('status',0)->where('id','<>',$id)->lists('name','id');

            $organizaciones = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.subprocesos.edit',['procesos'=>$procesos,'subprocesos'=>$subprocesos,'subproceso'=>$subproceso,'organizaciones'=>$organizaciones,'orgs_selected' => $orgs_selected]);
            }
            else
            {
                return view('datos_maestros.subprocesos.edit',['procesos'=>$procesos,'subprocesos'=>$subprocesos,'subproceso'=>$subproceso,'organizaciones'=>$organizaciones,'orgs_selected' => $orgs_selected]);
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
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $subproceso = \Ermtool\Subprocess::find($GLOBALS['id1']);

                //vemos si tiene subproceso padre
                if($_POST['subprocess_id'] != "")
                {
                    $subprocess_id = $_POST['subprocess_id'];
                }
                else
                {
                    $subprocess_id = NULL;
                }

                $subproceso->name = $_POST['name'];
                $subproceso->description = $_POST['description'];
                $subproceso->expiration_date = $_POST['expiration_date'];
                $subproceso->process_id = $_POST['process_id'];
                $subproceso->subprocess_id = $subprocess_id;

                //deberemos quitar las relaciones, y luego agregar las nuevas para este subproceso
                //primero eliminaremos todas las relaciones de organizaciones con subprocesos donde el subproceso sea el que se está editando
                $org_sub = DB::table('organization_subprocess')->where('subprocess_id',$GLOBALS['id1'])->lists('organization_id');

                foreach ($org_sub as $organization_id)
                {
                    $subproceso->organizations()->detach($organization_id);
                }

                //ahora agregamos las relaciones con las nuevas organizaciones
                foreach ($_POST['organization_id'] as $organization_id)
                {
                    $organization = \Ermtool\Organization::find($organization_id);
                    //agregamos la relación (para agregar en atributos)
                       $organization->subprocesses()->attach($GLOBALS['id1']);
                }

                $subproceso->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Subprocess successfully updated');
                }
                else
                {
                    Session::flash('message','Subproceso actualizado correctamente');
                }
            });

            return Redirect::to('/subprocesos');
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
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $subproceso = \Ermtool\Subprocess::find($GLOBALS['id1']);
                $subproceso->status = 1;
                $subproceso->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Subprocess successfully blocked');
                }
                else
                {
                    Session::flash('message','Subproceso bloqueado correctamente');
                }
            });
            return Redirect::to('/subprocesos');
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
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $subproceso = \Ermtool\Subprocess::find($GLOBALS['id1']);
                $subproceso->status = 0;
                $subproceso->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Subprocess successfully unblocked');
                }
                else
                {
                    Session::flash('message','Subproceso desbloqueado correctamente');
                }
            });
            return Redirect::to('/subprocesos');
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

            //vemos si es que tiene issues agregadas
            $rev = DB::table('issues')
                    ->where('subprocess_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

            if (empty($rev))
            {
                //ahora vemos si es que tiene algún riesgo
                $rev = DB::table('risk_subprocess')
                        ->where('subprocess_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //ahora si es que tiene alguna prueba de auditoría
                    $rev = DB::table('audit_tests')
                            ->where('subprocess_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                    if (empty($rev))
                    {

                        //ahora se puede eliminar, primero que todo se deben cambiar aquellos subprocesos que dependan de éste
                        DB::table('subprocesses')
                            ->where('subprocess_id','=',$GLOBALS['id1'])
                            ->update(['subprocess_id' => NULL]);

                        //ahora se debe eliminar de organization_subprocess
                        DB::table('organization_subprocess')
                            ->where('subprocess_id','=',$GLOBALS['id1'])
                            ->delete();

                        DB::table('subprocesses')
                            ->where('id','=',$GLOBALS['id1'])
                            ->delete();

                        $GLOBALS['res'] = 0;
                    }
                }
            }
        });

        return $res;
    }

    public function getSubprocesses($org)
    {
        $subprocesses = \Ermtool\Subprocess::getSubprocesses($org);
        
        return json_encode($subprocesses);
    }
}
