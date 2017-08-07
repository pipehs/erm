<?php

namespace Ermtool\Http\Controllers;
use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use DateTime;
use Auth;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

//$logger = new Logger('my_logger');
//$logger->pushHandler(new StreamHandler(__DIR__.'/procesos.log', Logger::INFO));
//$logger->pushHandler(new FirePHPHandler());

class ProcesosController extends Controller
{
    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('procesos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/procesos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                $proceso = array();

                if(isset($_GET['verbloqueados']))
                {
                    $procesos = \Ermtool\Process::where('status',1)->get(); //select procesos bloqueados 
                }
                else
                {
                    $procesos = \Ermtool\Process::where('status',0)->get(); //select procesos desbloqueados
                }
                $i = 0;
                $j = 0; //contador de subprocesos
                $k = 0; //contador de organizaciones

                // ---recorremos todas los procesos para asignar formato de datos correspondientes--- //
                $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
                $subprocesos = array(); //en este array almacenraemos todos los subprocesos relacionados a un proceso

                foreach ($procesos as $process)
                {

                    //obtenemos todas las organizaciones a las que pertenece cada subproceso relacionado
                    $orgs = DB::select('SELECT organizations.id, organizations.name
                                        FROM organizations
                                        WHERE organizations.id IN (SELECT DISTINCT organization_subprocess.organization_id
                                            FROM organization_subprocess
                                        WHERE organization_subprocess.subprocess_id IN (SELECT subprocesses.id
                                            FROM subprocesses WHERE process_id = '.$process["id"].'))');

                    foreach ($orgs as $organization)
                    {
                        $organizaciones[$k] = array('proceso_id'=>$process['id'],
                                                    'id'=>$organization->id,
                                                    'nombre'=>$organization->name);

                        $k += 1;
                    }


                   //obtenemos subprocesos relacionados
                    $subprocesses = \Ermtool\Process::find($process['id'])->subprocesses;

                    foreach ($subprocesses as $subprocess)
                    {
                       
                        $subprocesos[$j] = array('proceso_id'=>$process['id'],
                                                'id'=>$subprocess['id'],
                                                'nombre'=>$subprocess['name']);

                        $j += 1;

                    }

                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($process['created_at'] == NULL OR $process['created_at'] == "0000-00-00" OR $process['created_at'] == "")
                    {
                        $fecha_creacion = NULL;
                    }

                    else
                    {
                        //$fecha_creacion = date('d-m-Y',strtotime($process['created_at']));
                        $lala = new DateTime($process['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                    }

                    //damos formato a fecha expiración
                    if ($process['expiration_date'] == NULL OR $process['expiration_date'] == "0000-00-00")
                    {
                        $fecha_exp = NULL;
                    }
                    else
                    { 
                        $expiration_date = new DateTime($process['expiration_date']);
                        $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    }

                    //damos formato a fecha de actualización 
                    if ($process['updated_at'] != NULL)
                    {
                        $lala = new DateTime($process['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
                        //$fecha_act = date('d-m-Y',strtotime($process['updated_at']));
                    }

                    else
                        $fecha_act = NULL;

                    //damos formato si depende de otro proceso
                    if ($process['process_id'] == NULL)
                    {
                        $proceso_dependiente['name'] = "No";
                        $proceso_dependiente['id'] = NULL;
                    }
                    else
                        $proceso_dependiente = \Ermtool\Process::find($process['process_id']);

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($process['description'],0,100);

                    $proceso[$i] = array('id'=>$process['id'],
                                        'nombre'=>$process['name'],
                                        'descripcion'=>$process['description'],
                                        'fecha_creacion'=>$fecha_creacion,
                                        'fecha_act'=>$fecha_act,
                                        'fecha_exp'=>$fecha_exp,
                                        'proceso_dependiente'=>$proceso_dependiente['name'],
                                        'proceso_dependiente_id'=>$proceso_dependiente['id'],
                                        'estado'=>$process['status'],
                                        'short_des' => $short_des);
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.procesos.index',['procesos'=>$proceso,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);
                }
                else
                {
                    return view('datos_maestros.procesos.index',['procesos'=>$proceso,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);
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
                //Seleccionamos procesos que pueden ser padres
                $procesos = \Ermtool\Process::where('process_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en') {
                    return view('en.datos_maestros.procesos.create',['procesos'=>$procesos]);
                } else {
                    return view('datos_maestros.procesos.create',['procesos'=>$procesos]);
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
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    //vemos si tiene proceso dependiente
                    if ($_POST['process_id'] != "")
                    {
                        $process_id = $_POST['process_id'];
                    }
                    else
                        $process_id = NULL;

                    $process = \Ermtool\Process::create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'expiration_date' => $_POST['expiration_date'],
                        'process_id' => $process_id,
                        ]);

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el proceso con Id: '.$process->id.' llamado: '.$process->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Process successfully created');
                    }
                    else
                    {
                        Session::flash('message','Proceso agregado correctamente');
                    }
                });

                return Redirect::to('/procesos');
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
                $proceso = \Ermtool\Process::find($id);
                $combobox = \Ermtool\Process::where('id','<>',$id)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox]);
                }
                else
                {
                    return view('datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox]);
                }
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
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $proceso = \Ermtool\Process::find($GLOBALS['id1']);
                    $proceso->status = 1;
                    $proceso->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Process successfully blocked');
                    }
                    else
                    {
                        Session::flash('message','Proceso bloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el proceso con Id: '.$GLOBALS['id1'].' llamado: '.$proceso->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    
                });

                return Redirect::to('/procesos');
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
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $proceso = \Ermtool\Process::find($GLOBALS['id1']);
                    $proceso->status = 0;
                    $proceso->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Process successfully unblocked');
                    }
                    else
                    {
                        Session::flash('message','Proceso desbloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el proceso con Id: '.$GLOBALS['id1'].' llamado: '.$proceso->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/procesos');
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
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;

                    $proceso = \Ermtool\Process::find($GLOBALS['id1']);
                    $fecha_exp = NULL;

                    //vemos si tiene proceso padre
                    if($_POST['process_id'] != "")
                    {
                        $process_id = $_POST['process_id'];
                    }
                    else
                    {
                        $process_id = NULL;
                    }

                    $proceso->name = $_POST['name'];
                    $proceso->description = $_POST['description'];
                    $proceso->expiration_date = $_POST['expiration_date'];
                    $proceso->process_id = $process_id;

                    $proceso->save();

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el proceso con Id: '.$GLOBALS['id1'].' llamado: '.$proceso->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Process successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Proceso actualizado correctamente');
                    }

                });
                return Redirect::to('/procesos');
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
                //vemos si es que tiene algún subproceso asociado
                $rev = DB::table('subprocesses')
                        ->where('process_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //ahora vemos si es que tiene issues agregadas
                    $rev = DB::table('issues')
                            ->where('process_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                    if (empty($rev))
                    {
                        //si es que se llega a esta instancia, se puede eliminar
                        //primero cambiamos aquellos procesos que posean a éste como relacionado
                        DB::table('processes')
                            ->where('process_id','=',$GLOBALS['id1'])
                            ->update(['process_id' => NULL]);

                        //obtenemos el nombre para guardar en log
                        $p = DB::table('processes')
                            ->where('id','=',$GLOBALS['id1'])
                            ->select('name')
                            ->first();
                        //ahora eliminamos
                        DB::table('processes')
                            ->where('id','=',$GLOBALS['id1'])
                            ->delete();

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el proceso con Id: '.$GLOBALS['id1'].' llamado: '.$p->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                        $GLOBALS['res'] = 0;
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


    //obtiene procesos de una organización
    public function getProcesses($org)
    {
        try
        {
            $processes = \Ermtool\Process::getProcesses($org);
            return json_encode($processes);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
