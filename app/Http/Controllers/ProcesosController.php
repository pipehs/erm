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
                return Redirect::route('/');
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
                

                foreach ($procesos as $process)
                {
                    global $id;
                    $id = $process['id'];

                    //ACT 07-06-18: Ahora se obtienen las orgs desde tabla organization_process_stakeholder
                    $orgs = \Ermtool\OrganizationProcessStakeholder::where('process_id',$GLOBALS['id'])->get();

                    /*
                    $orgs = DB::table('organizations')
                            ->join('organization_subprocess','organization_subprocess.organization_id','=','organizations.id')
                            ->join('subprocesses','subprocesses.id','=','organization_subprocess.subprocess_id')
                            ->join('processes','processes.id','=','subprocesses.process_id')
                            ->where((function ($query) {
                                $query->where('subprocesses.process_id','=',$GLOBALS['id'])
                                      ->orWhere('processes.process_id','=',$GLOBALS['id']);
                            }))
                            ->select('organizations.id','organizations.name')
                            ->groupBy('organizations.id','organizations.name')
                            ->get();
                    */
                    $k = 0; //contador de organizaciones
                    
                

                    // ---recorremos todas los procesos para asignar formato de datos correspondientes--- //
                    $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
                    
                    foreach ($orgs as $ops)
                    {
                        $org = \Ermtool\Organization::find($ops->organization_id);

                        //ACT 08-06-18: Agregamos responsable correspondiente a la organización
                        $responsable = $ops->stakeholder_id ? \Ermtool\Stakeholder::getName($ops->stakeholder_id) : NULL;

                        $organizaciones[$k] = [
                            'id'=>$org->id,
                            'nombre'=>$org->name,
                            'responsable' => $responsable];

                        $k += 1;
                    }


                    $j = 0; //contador de subprocesos
                    $subprocesos = array(); //en este array almacenraemos todos los subprocesos relacionados a un proceso

                   //obtenemos subprocesos relacionados
                    $subprocesses = \Ermtool\Process::find($process['id'])->subprocesses;

                    foreach ($subprocesses as $subprocess)
                    {
                       
                        $subprocesos[$j] = array('id'=>$subprocess['id'],
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
                                        'short_des' => $short_des,
                                        'organizaciones' => $organizaciones,
                                        'subprocesos' => $subprocesos
                                    );
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.procesos.index',['procesos'=>$proceso]);
                }
                else
                {
                    return view('datos_maestros.procesos.index',['procesos'=>$proceso]);
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
                //ACT 07-06-18: Enviamos organizaciones
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                //Seleccionamos procesos que pueden ser padres
                $procesos = \Ermtool\Process::where('process_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en') 
                {
                    return view('en.datos_maestros.procesos.create',['procesos'=>$procesos, 'organizations' => $organizations]);
                } 
                else 
                {
                    return view('datos_maestros.procesos.create',['procesos'=>$procesos, 'organizations' => $organizations]);
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
                global $evidence;
                $evidence = $request->file('evidence_doc');

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

                    //ACT 07-06-18: Agregamos en organization_process_stakeholder
                    foreach ($_POST['organization_id'] as $organization_id)
                    {
                        \Ermtool\OrganizationProcessStakeholder::create([
                            'organization_id' => $organization_id,
                            'process_id' => $process->id
                        ]);
                    }

                    if($GLOBALS['evidence'] != NULL)
                    {
                        foreach ($GLOBALS['evidence'] as $evidence)
                        {
                            if ($evidence != NULL)
                            {
                                upload_file($evidence,'procesos',$process->id);
                            }
                        }                    
                    }

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
                return Redirect::route('/');
            }
            else
            {
                $proceso = \Ermtool\Process::find($id);
                $combobox = \Ermtool\Process::where('id','<>',$id)->lists('name','id');

                //obtenemos organizaciones del proceso
                $orgs_selected = \Ermtool\OrganizationProcessStakeholder::where('process_id',$id)
                                ->select('organization_id')
                                ->get();

                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox, 'organizations' => $organizations, 'orgs_selected' => $orgs_selected]);
                }
                else
                {
                    return view('datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox, 'organizations' => $organizations, 'orgs_selected' => $orgs_selected]);
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
                return Redirect::route('/');
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
                return Redirect::route('/');
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
                return Redirect::route('/');
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

                    //ACT 08-06-18: No se eliminan para no perder stakeholder. Haremos proceso más largo
                    $ops = \Ermtool\OrganizationProcessStakeholder::where('process_id',$proceso->id)->get();

                    foreach ($ops as $o)
                    {
                        $cont = 0; //para verificar que esté entre los seleccionados
                        if (isset($_POST['organization_id']))
                        {
                            foreach ($_POST['organization_id'] as $org)
                            {
                                if ($o->organization_id == $org)
                                {
                                    $cont += 1;
                                }
                            }    
                        }

                        //Vemos si es que efectivamente está en las seleccionadas. Si no está, se elimina
                        if ($cont == 0)
                        {
                            $o->delete();
                        }
                    }

                    //Ahora agregamos las que no existan
                    if (isset($_POST['organization_id']))
                    {
                        foreach ($_POST['organization_id'] as $org)
                        {
                            //Vemos si es que existe (lo hacemos a través de DB para ver si es que está en deleted_at)
                            $ops = \Ermtool\OrganizationProcessStakeholder::withTrashed()->where('process_id',$proceso->id)->where('organization_id',$org)->first();

                            if (empty($ops)) //Creamos
                            {
                                \Ermtool\OrganizationProcessStakeholder::create([
                                    'process_id' => $proceso->id,
                                    'organization_id' => $org
                                ]);
                            }
                            else if ($ops->trashed()) //vemos si está eliminado con soft_deleting
                            {
                                $ops->restore(); //Restauramos
                            }
                        }
                    }

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

                        //ACT 31-08-18: Eliminamos también de organization_process_stakeholder
                        $ops = \Ermtool\OrganizationProcessStakeholder::where('process_id','=',$GLOBALS['id1'])->get();

                        foreach ($ops as $o)
                        {
                            //Eliminamos docs asociados a proceso_org
                            eliminarArchivo($o->id,10,NULL);
                            $o->forceDelete();
                        }

                        //Eliminamos docs asociados
                        eliminarArchivo($GLOBALS['id1'],9,NULL);
                        
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

    //ACT 07-06-18: Actualizamos tabla organization_process_stakeholder y también organization_subprocess
    public function updateOrganizationProcessStakeholder()
    {
        DB::transaction(function(){
            $org_sub = \Ermtool\OrganizationSubprocess::all();

            foreach ($org_sub as $os)
            {
                //obtenemos subproceso asociado (no como eloquent ya que necesitamos created y updated_at)
                $subprocess = DB::table('subprocesses')->where('id','=',$os->subprocess_id)->first();

                //primero actualizamos created_at y updated_at en organization_subprocess
                
                DB::table('organization_subprocess')
                    ->where('subprocess_id','=',$os->subprocess_id)
                    ->update([
                        'created_at' => $subprocess->created_at,
                        'updated_at' => $subprocess->updated_at
                    ]);

                //Ahora vemos si este subproceso depende de otro
                if ($subprocess->subprocess_id != NULL)
                {
                    //Tiene un macrosubproceso relacionado que se debe asignar tambien en la tabla
                    //Primero vemos si existe
                    $orgsub = DB::table('organization_subprocess')
                            ->where('subprocess_id','=',$subprocess->subprocess_id)
                            ->where('organization_id','=',$os->organization_id)
                            ->first(['id']);

                    if (empty($orgsub)) //Si no existe, creamos
                    {
                        DB::table('organization_subprocess')
                            ->insert([
                                'organization_id' => $os->organization_id,
                                'subprocess_id' => $subprocess->subprocess_id,
                                'created_at' => $subprocess->created_at,
                                'updated_at' => $subprocess->updated_at
                            ]);
                    }
                }
               
                //Ahora actualizamos organization_process_stakeholder
                //Primero que todo, vemos si el proceso asociado al subproceso ya existe en la tabla organization_process_stakeholder, si es así no hacemos nada
                $ops = DB::table('organization_process_stakeholder')
                    ->where('process_id','=',$subprocess->process_id)
                    ->where('organization_id','=',$os->organization_id)
                    ->first(['id']);

                if (empty($ops)) //Si no existe, creamos
                {
                    DB::table('organization_process_stakeholder')
                        ->insert([
                            'organization_id' => $os->organization_id,
                            'process_id' => $subprocess->process_id,
                            'created_at' => $subprocess->created_at,
                            'updated_at' => $subprocess->updated_at
                        ]);
                }

                //Ahora vemos si este proceso depende de otro
                $process = \Ermtool\Process::find($subprocess->process_id);

                if ($process->process_id != NULL)
                {
                    //Tiene un macroproceso relacionado que se debe asignar tambien en la tabla
                    //Primero vemos si existe
                    $ops = DB::table('organization_process_stakeholder')
                            ->where('process_id','=',$process->process_id)
                            ->where('organization_id','=',$os->organization_id)
                            ->first(['id']);

                    if (empty($ops)) //Si no existe, creamos
                    {
                        DB::table('organization_process_stakeholder')
                            ->insert([
                                'organization_id' => $os->organization_id,
                                'process_id' => $process->process_id,
                                'created_at' => $subprocess->created_at,
                                'updated_at' => $subprocess->updated_at
                            ]);
                    }
                }
            }
        });
    }

    //ACT 08-06-18: Función para asignar responsables y para identificar otros atributos del proceso
    public function attributes($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //obtenemos todas las organizaciones a las que pertenece el proceso
                $ops = \Ermtool\OrganizationProcessStakeholder::where('process_id',$id)->get();

                foreach ($ops as $o)
                {
                    $o->org = \Ermtool\Organization::name($o->organization_id);
                }

                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                $process = \Ermtool\Process::where('id',$id)->value('name');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.procesos.attributes',['id'=>$id,'ops'=>$ops,'stakeholders' => $stakeholders,'process' => $process]);
                }
                else
                {
                    return view('datos_maestros.procesos.attributes',['id'=>$id,'ops'=>$ops,'stakeholders' => $stakeholders,'process' => $process]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function assignAttributes(Request $request)
    {
        try
        {
            global $request2;
            $request2 = $request;
            //Guardamos responsables
            DB::transaction(function(){

                foreach ($_POST as $id=>$p)
                {
                    if (strpos($id,"takeholder")) //No se porqué me funciona sin la s...
                    {
                        //verificamos que se haya ingresado algún valor
                        //if ($p != '' && $p != NULL)
                        //{
                        //obtenemos organización
                        $org = explode('_', $id);

                        //obtenemos modelo y actualizamos
                        $ops = \Ermtool\OrganizationProcessStakeholder::where('organization_id',$org[1])
                                    ->where('process_id','=',$_POST['process_id'])->first();

                        //ACT 13-06-18: asignamos todos los datos
                        $ops->stakeholder_id = $p != '' ? $p : NULL;
                        $ops->key_process = $_POST['key_process_'.$org[1]] != '' ? $_POST['key_process_'.$org[1]] : NULL;
                        $ops->criticality = $_POST['criticality_'.$org[1]] != '' ? $_POST['criticality_'.$org[1]] : NULL;

                        if($GLOBALS['request2']->file('evidence_doc_'.$org[1]) != NULL)
                        {
                            foreach ($GLOBALS['request2']->file('evidence_doc_'.$org[1]) as $evidence)
                            {
                                if ($evidence != NULL)
                                {
                                    upload_file($evidence,'procesos_org',$ops->id);
                                }
                            }                    
                        }

                        $ops->save();
                        //}
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Process attributes was successfully updated');
                }
                else
                {
                    Session::flash('message','Atributos del proceso asignados correctamente');
                }
            });

            return Redirect::to('/procesos');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 13-06-18: Reporte de matriz de procesos
    public function matrix()
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

                //OBS: el nombre es con un 1 para diferenciar de los procesos de función generateMatrix
                $processes1 = \Ermtool\Process::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    //return view('en.reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('en.reportes.matriz_procesos',['organizations'=>$organizations,'processes1' => $processes1]);
                }
                else
                {
                    //return view('reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('reportes.matriz_procesos',['organizations'=>$organizations,'processes1' => $processes1]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //Generación de matriz de procesos
    public function generateMatrix()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //Por ahora no hay excel (13-06-18)
                /*
                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //si no se está generando excel
                {}
                else 
                {}*/
                
                $i = 0;

                //obtenemos todos los registros de organization_process_stakeholder
                if (isset($_GET['process_id']) && $_GET['process_id'] != '')
                {
                    $processes = \Ermtool\OrganizationProcessStakeholder::where('process_id',$_GET['process_id'])->get();
                }
                else if (isset($_GET['organization_id']) && $_GET['organization_id'] != '')
                {
                    $processes = \Ermtool\OrganizationProcessStakeholder::where('organization_id',$_GET['organization_id'])->get();
                }
                else
                {
                    $processes = \Ermtool\OrganizationProcessStakeholder::all();
                }

                foreach ($processes as $p) //asignamos datos faltantes
                {
                    //guardamos nombre de stakeholder
                    $p->stakeholder = $p->stakeholder_id != NULL ? \Ermtool\Stakeholder::getName($p->stakeholder_id) : NULL;

                    $process = \Ermtool\Process::find($p->process_id);

                    $p->name = $process->name;
                    $p->description = $process->description;
                    $p->organization = \Ermtool\Organization::name($p->organization_id);

                    //obtenemos nombre de proceso padre (de existir)
                    $p->macroprocess = $process->process_id != NULL ? \Ermtool\Process::where('id',$process->process_id)->value('name') : NULL;

                    //descripción corta
                    $p->short_des = substr($process->description,0,100);

                    //obtenemos subprocesos
                    $p->subprocesses = \Ermtool\Subprocess::getSubprocesses2($p->organization_id,$p->process_id);
                }

                //$datos = $this->generateRiskMatrix($org,$category,$value);


                if (strstr($_SERVER["REQUEST_URI"],'genexcel')) 
                {
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.reportes.matriz_procesos',['processes'=>$processes]);
                    }
                    else
                    {
                        return view('reportes.matriz_procesos',['processes'=>$processes]);
                    }
                    //return json_encode($datos);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
