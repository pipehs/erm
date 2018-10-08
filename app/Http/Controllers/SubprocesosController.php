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
use Storage;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class SubprocesosController extends Controller
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
        $this->logger = new Logger('subprocesos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/subprocesos.log', Logger::INFO));
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
                
                foreach ($subprocesos as $subprocess)
                {

                    //ahora obtenemos todas las organizaciones a las que pertenece cada subproceso
                    //$orgs = \Ermtool\Subprocess::find($subprocess['id'])->organizations;

                    global $id;
                    $id = $subprocess['id'];
                    /*
                    $orgs = DB::table('organizations')
                            ->join('organization_subprocess','organization_subprocess.organization_id','=','organizations.id')
                            ->join('subprocesses','subprocesses.id','=','organization_subprocess.subprocess_id')
                            ->where((function ($query) {
                                $query->where('subprocesses.subprocess_id','=',$GLOBALS['id'])
                                      ->orWhere('organization_subprocess.subprocess_id','=',$GLOBALS['id']);
                            }))
                            ->select('organizations.id','organizations.name')
                            ->groupBy('organizations.id','organizations.name')
                            ->get();
                    */
                    //ACT 14-06-18: Probamos consulta más simple y con todos los datos
                    $os = \Ermtool\OrganizationSubprocess::where('subprocess_id',$GLOBALS['id'])->get();
                    $j = 0; //contador de organizaciones relacionadas
                    $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
                    
                    foreach ($os as $o)
                    {
                        $org = \Ermtool\Organization::find($o->organization_id);

                        //ACT 08-06-18: Agregamos responsable correspondiente a la organización
                        $responsable = $o->stakeholder_id ? \Ermtool\Stakeholder::getName($o->stakeholder_id) : NULL;

                         $organizaciones[$j] = [
                                    'id'=>$org->id,
                                    'nombre'=>$org->name,
                                    'responsable' => $responsable];

                         $j += 1;
                    }
                
                    $subprocesos_dependientes = \Ermtool\Subprocess::where('subprocess_id',$subprocess['id'])->get();
                    
                    $k = 0; //contador de subprocesos relacionados
                    $sub_dependientes = array(); 

                    foreach ($subprocesos_dependientes as $s)
                    {
                        $sub_dependientes[$k] = [
                                    'nombre' => $s->name,
                                    'descripcion' => $s->description];
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
                        $lala = new DateTime($subprocess['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_creacion = NULL;

                    //damos formato a fecha de actualización 
                    if ($subprocess['updated_at'] != NULL)
                    {
                        $lala = new DateTime($subprocess['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_act = NULL;

                    //$proceso = \Ermtool\Subprocess::find($subprocess['id'])->processes; No me funciono
                    $proceso = \Ermtool\Process::find($subprocess['process_id']);
                    
                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($subprocess['description'],0,100);

                    $subproceso[$i] = [
                                'id'=>$subprocess['id'],
                                'nombre'=>$subprocess['name'],
                                'descripcion'=>$subprocess['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'proceso_relacionado'=>$proceso['name'],
                                'estado'=>$subprocess['status'],
                                'short_des'=>$short_des,
                                'systems' => $subprocess['systems'],
                                'habeas_data' => $subprocess['habeas_data'],
                                'regulatory_framework' => $subprocess['regulatory_framework'],
                                'organizaciones' => $organizaciones,
                                'sub_dependientes' => $sub_dependientes
                            ];
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.subprocesos.index',['subprocesos'=>$subproceso]);
                }
                else
                {
                    return view('datos_maestros.subprocesos.index',['subprocesos'=>$subproceso]);
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

                try {

                    DB::transaction(function()
                    {
                        $logger = $this->logger;

                        if($_POST['subprocess_id'] == NULL || $_POST['subprocess_id'] == '' || !isset($_POST['subprocess_id']))
                        {
                            $subprocess_id = NULL;
                        }
                        else
                        {
                            $subprocess_id = $_POST['subprocess_id'];
                        }

                        if (isset($_POST['expiration_date']) && $_POST['expiration_date'] != '')
                        {
                            $expiration_date = $_POST['expiration_date'];
                        }
                        else
                        {
                            $expiration_date = NULL;
                        }

                        //ACTUALIZACIÓN 21-11-17: Vemos si se agregaron atributos: Sistemas, Habeas data, o Marco regulatorio
                        if (isset($_POST['systems']) && $_POST['systems'] != '')
                        {
                            $systems = $_POST['systems'];
                        }
                        else
                        {
                            $systems = NULL;
                        }

                        if (isset($_POST['habeas_data']) && $_POST['habeas_data'] != '')
                        {
                            $habeas_data = $_POST['habeas_data'];
                        }
                        else
                        {
                            $habeas_data = NULL;
                        }

                        if (isset($_POST['regulatory_framework']) && $_POST['regulatory_framework'] != '')
                        {
                            $regulatory_framework = $_POST['regulatory_framework'];
                        }
                        else
                        {
                            $regulatory_framework = NULL;
                        }

                        $subprocess = \Ermtool\Subprocess::create([
                            'name' => $_POST['name'],
                            'description' => $_POST['description'],
                            'expiration_date' => $expiration_date,
                            'process_id' => $_POST['process_id'],
                            'subprocess_id' => $subprocess_id,
                            'systems' => $systems,
                            'habeas_data' => $habeas_data,
                            'regulatory_framework' => $regulatory_framework
                        ]);

                        //agregamos la relación a cada organización
                            // primero obtenemos subproceso que acabamos de agregar

                            foreach ($_POST['organization_id'] as $organization_id)
                            {
                                $organization = \Ermtool\Organization::find($organization_id);
                                //agregamos la relación (para agregar en atributos)
                                $organization->subprocesses()->attach($subprocess->id);
                            }

                            if($GLOBALS['evidence'] != NULL)
                            {
                                foreach ($GLOBALS['evidence'] as $evidence)
                                {
                                    if ($evidence != NULL)
                                    {
                                        upload_file($evidence,'subprocesos',$subprocess->id);
                                    }
                                }                    
                            }

                            if (Session::get('languaje') == 'en')
                            {
                                Session::flash('message','Subprocess successfully created');
                            }
                            else
                            {
                                Session::flash('message','Subproceso agregado correctamente');
                            }

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el subproceso con Id: '.$subprocess->id.' llamado: '.$subprocess->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    });
                    return Redirect::to('/subprocesos');
                } 
                catch(\Exception $e) 
                {
                    Session::flash('message','Subproceso no pudo agregarse. '.$e);
                    return Redirect::to('/subprocesos');
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

                    if (isset($_POST['expiration_date']) && $_POST['expiration_date'] != '')
                    {
                        $expiration_date = $_POST['expiration_date'];
                    }
                    else
                    {
                        $expiration_date = NULL;
                    }

                    //ACTUALIZACIÓN 21-11-17: Vemos si se agregaron atributos: Sistemas, Habeas data, o Marco regulatorio
                    if (isset($_POST['systems']) && $_POST['systems'] != '')
                    {
                        $systems = $_POST['systems'];
                    }
                    else
                    {
                        $systems = NULL;
                    }

                    if (isset($_POST['habeas_data']) && $_POST['habeas_data'] != '')
                    {
                        $habeas_data = $_POST['habeas_data'];
                    }
                    else
                    {
                        $habeas_data = NULL;
                    }

                    if (isset($_POST['regulatory_framework']) && $_POST['regulatory_framework'] != '')
                    {
                        $regulatory_framework = $_POST['regulatory_framework'];
                    }
                    else
                    {
                        $regulatory_framework = NULL;
                    }

                    $subproceso->expiration_date = $expiration_date;
                    $subproceso->process_id = $_POST['process_id'];
                    $subproceso->subprocess_id = $subprocess_id;
                    $subproceso->systems = $systems;
                    $subproceso->habeas_data = $habeas_data;
                    $subproceso->regulatory_framework = $regulatory_framework;

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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el subproceso con Id: '.$GLOBALS['id1'].' llamado: '.$subproceso->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('/subprocesos');
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el subproceso con Id: '.$GLOBALS['id1'].' llamado: '.$subproceso->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/subprocesos');
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

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el subproceso con Id: '.$GLOBALS['id1'].' llamado: '.$subproceso->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/subprocesos');
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
                            //ahora se puede eliminar, primero que todo se deben cambiar aquellos subprocesos que dependan de éste

                            //obtenemos nombre
                            $name = \Ermtool\Subprocess::name($GLOBALS['id1']);

                            DB::table('subprocesses')
                                ->where('subprocess_id','=',$GLOBALS['id1'])
                                ->update(['subprocess_id' => NULL]);

                            //ahora se debe eliminar de organization_subprocess
                            //DB::table('organization_subprocess')
                            //    ->where('subprocess_id','=',$GLOBALS['id1'])
                            //    ->delete();
                            //ACT 31-08-18: Incluimos archivos y salto en protección soft_deleting
                            $os = \Ermtool\OrganizationSubprocess::where('subprocess_id','=',$GLOBALS['id1'])->get();

                            foreach ($os as $o)
                            {
                                //Eliminamos docs asociados a subprocesos_org
                                eliminarArchivo($o->id,12,NULL);
                                $o->forceDelete();
                            }

                            //Eliminamos docs asociados
                            eliminarArchivo($GLOBALS['id1'],11,NULL);

                            DB::table('subprocesses')
                                ->where('id','=',$GLOBALS['id1'])
                                ->delete();

                            $GLOBALS['res'] = 0;

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el subproceso con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
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

    public function getSubprocesses($org)
    {
        try
        {
            $subprocesses = \Ermtool\Subprocess::getSubprocesses($org); 
            return json_encode($subprocesses);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getSubprocesses2($org,$process)
    {
        try
        {
            $subprocesses = \Ermtool\Subprocess::getSubprocesses2($org,$process);
            return json_encode($subprocesses);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getSubprocessesFromProcess($org,$process)
    {
        try
        {
            $subprocesses = \Ermtool\Subprocess::getSubprocessesFromProcess($org,$process);
            return json_encode($subprocesses);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 08-06-18: Función para asignar responsables y para identificar otros atributos del subproceso
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
                //obtenemos todas las organizaciones a las que pertenece el subproceso
                $os = \Ermtool\OrganizationSubprocess::where('subprocess_id',$id)->get();

                foreach ($os as $o)
                {
                    $o->org = \Ermtool\Organization::name($o->organization_id);
                    //ACT 08-10-18: obtenemos archivos
                    $o->files = Storage::files('subprocesos_org/'.$o->id);
                }

                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                $subprocess = \Ermtool\Subprocess::where('id',$id)->value('name');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.subprocesos.attributes',['id'=>$id,'os'=>$os,'stakeholders' => $stakeholders,'subprocess' => $subprocess]);
                }
                else
                {
                    return view('datos_maestros.subprocesos.attributes',['id'=>$id,'os'=>$os,'stakeholders' => $stakeholders,'subprocess' => $subprocess]);
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
                        $org = explode('_', $id);

                        //obtenemos modelo y actualizamos
                        $os = \Ermtool\OrganizationSubprocess::where('organization_id',$org[1])
                                    ->where('subprocess_id','=',$_POST['subprocess_id'])->first();

                        //ACT 13-06-18: asignamos todos los datos
                        $os->stakeholder_id = $p != '' ? $p : NULL;
                        $os->key_subprocess = $_POST['key_subprocess_'.$org[1]] != '' ? $_POST['key_subprocess_'.$org[1]] : NULL;
                        $os->criticality = $_POST['criticality_'.$org[1]] != '' ? $_POST['criticality_'.$org[1]] : NULL;

                        if($GLOBALS['request2']->file('evidence_doc_'.$org[1]) != NULL)
                        {
                            foreach ($GLOBALS['request2']->file('evidence_doc_'.$org[1]) as $evidence)
                            {
                                if ($evidence != NULL)
                                {
                                    upload_file($evidence,'subprocesos_org',$os->id);
                                }
                            }                    
                        }

                        $os->save();
                    }
                }

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Subprocess attributes was successfully updated');
                }
                else
                {
                    Session::flash('message','Atributos del subproceso asignados correctamente');
                }
            });

            return Redirect::to('/subprocesos');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 14-06-18: Reporte de matriz de subprocesos
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
                $subprocesses1 = \Ermtool\Subprocess::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    //return view('en.reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('en.reportes.matriz_subprocesos',['organizations'=>$organizations,'subprocesses1' => $subprocesses1]);
                }
                else
                {
                    //return view('reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('reportes.matriz_subprocesos',['organizations'=>$organizations,'subprocesses1' => $subprocesses1]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //Generación de matriz de subprocesos
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
                if (isset($_GET['subprocess_id']) && $_GET['subprocess_id'] != '')
                {
                    $subprocesses = \Ermtool\OrganizationSubprocess::where('subprocess_id',$_GET['subprocess_id'])->get();
                }
                else if (isset($_GET['organization_id']) && $_GET['organization_id'] != '')
                {
                    $subprocesses = \Ermtool\OrganizationSubprocess::where('organization_id',$_GET['organization_id'])->get();
                }
                else
                {
                    $subprocesses = \Ermtool\OrganizationSubprocess::all();
                }

                foreach ($subprocesses as $s) //asignamos datos faltantes
                {
                    //guardamos nombre de stakeholder
                    $s->stakeholder = $s->stakeholder_id != NULL ? \Ermtool\Stakeholder::getName($s->stakeholder_id) : NULL;

                    $subprocess = \Ermtool\Subprocess::find($s->subprocess_id);

                    $s->name = $subprocess->name;
                    $s->description = $subprocess->description;
                    $s->organization = \Ermtool\Organization::name($s->organization_id);

                    //obtenemos nombre de subproceso padre (de existir)
                    $s->macroprocess = $subprocess->subprocess_id != NULL ? \Ermtool\Subprocess::where('id',$subprocess->subprocess_id)->value('name') : NULL;

                    //descripción corta
                    $s->short_des = substr($subprocess->description,0,100);

                    //obtenemos proceso
                    $s->process = \Ermtool\Process::find($subprocess->process_id);
                }

                //$datos = $this->generateRiskMatrix($org,$category,$value);


                if (strstr($_SERVER["REQUEST_URI"],'genexcel')) 
                {
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.reportes.matriz_subprocesos',['subprocesses'=>$subprocesses]);
                    }
                    else
                    {
                        return view('reportes.matriz_subprocesos',['subprocesses'=>$subprocesses]);
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
