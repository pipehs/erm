<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use dateTime;
use DB;
use Auth;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class ObjetivosController extends Controller
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
        $this->logger = new Logger('objetivos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/objetivos.log', Logger::INFO));
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
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id'); //select organizaciones desbloqueadas en lista para select


                /*
                if (isset($_GET['organizacion'])) //se seleccionó la organización para ver objetivos
                {

                        //vemos si la organización seleccionada tiene algún plan activo
                    $strategic_plan = \Ermtool\Strategic_plan::where('status',1)->where('organization_id',$_GET['organizacion'])->select('id')->first();

                    if ($strategic_plan != "")
                    {
                        $objetivos = \Ermtool\Objective::where('organization_id',(int)$_GET['organizacion'])
                                                        ->where('strategic_plan_id',$strategic_plan['id'])
                                                            ->where('status',0)->get();

                        //seleccionamos todos los datos del plan para mostrarlo
                        $datos_plan = \Ermtool\Strategic_plan::find($strategic_plan['id']);
                        $nombre_organizacion = \Ermtool\Organization::name($_GET['organizacion']);
                        $i=0; //para saber si hay objetivos
                        $objectives = array(); //almacenará los objetivos con el formato correcto de sus atributos
                        foreach ($objetivos as $objetivo)
                        {
                            $i = $i+1;
                             //damos formato a fecha expiración
                            if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
                            {
                                $fecha_exp = NULL;
                            }
                            else 
                            {
                                $expiration_date = new DateTime($objetivo['expiration_date']);
                                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                            }

                            //damos formato a fecha creación
                            if ($objetivo['created_at'] != NULL)
                            {
                                $fecha_creacion = date_format($objetivo['created_at'],"d-m-Y");
                            }
                            else
                                $fecha_creacion = NULL;

                            //damos formato a fecha de actualización 
                            if ($objetivo['updated_at'] != NULL)
                            {
                                $fecha_act = date_format($objetivo['updated_at'],"d-m-Y");
                            }
                            else
                                $fecha_act = NULL;

                            //damos formato a categoría de objetivo
                            if ($objetivo['objective_category_id'] == NULL)
                            {
                                $categoria = NULL;
                            }
                            else
                                $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('name');

                            if ($objetivo['perspective'] == NULL)
                            {
                                $perspective = NULL;
                            }
                            else
                            {
                                $perspective = $objetivo['perspective'];   
                            }

                            $objectives[$i] = array('id'=>$objetivo['id'],
                                            'nombre'=>$objetivo['name'],
                                            'descripcion'=>$objetivo['description'],
                                            'fecha_creacion'=>$fecha_creacion,
                                            'fecha_act'=>$fecha_act,
                                            'fecha_exp'=>$fecha_exp,
                                            'categoria'=>$categoria,
                                            'estado'=>$objetivo['status'],
                                            'perspective' => $perspective);
                            $i += 1;

                        }
                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.datos_maestros.objetivos.index',['organizations'=>$organizations,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'datos_plan' => $datos_plan, 'probador' => $i,'strategic_plan_id' => $strategic_plan['id']]);
                        }
                        else
                        {
                            return view('datos_maestros.objetivos.index',['organizations'=>$organizations,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'datos_plan' => $datos_plan, 'probador' => $i,'strategic_plan_id' => $strategic_plan['id']]);
                        }
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            return view('en.datos_maestros.objetivos.index',['organizations'=>$organizations,'validador' => 1]);
                        }
                        else
                        {
                            return view('datos_maestros.objetivos.index',['organizations'=>$organizations, 'validador' => 1]);
                        }
                    }  
                } */

                //se dejará el if anterior (comentado) en caso que volviera a ser necesario, sin embargo expiró en 04-10-2016; actualmente los objetivos serán vistos por id de plan estratégico (a través de la función objetivosPlan)

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.objetivos.index',['organizations'=>$organizations]);
                }
                else
                {
                    return view('datos_maestros.objetivos.index',['organizations'=>$organizations]);
                }
                
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function objetivosPlan($plan_id)
    {
        try
        {
                if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
                {
                    $objetivos = \Ermtool\Objective::where('strategic_plan_id',$plan_id)
                                                ->where('status',1)->get();
                }
                else
                {
                    $objetivos = \Ermtool\Objective::where('strategic_plan_id',$plan_id)
                                                ->where('status',0)->get();
                } 

                    //seleccionamos todos los datos del plan para mostrarlo
                    $datos_plan = \Ermtool\Strategic_plan::find($plan_id);
                    $nombre_organizacion = \Ermtool\Organization::name($datos_plan->organization_id);
                    $i=0; //para saber si hay objetivos
                    $objectives = array(); //almacenará los objetivos con el formato correcto de sus atributos
                    foreach ($objetivos as $objetivo)
                    {
                        //damos formato a fecha expiración
                        if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
                        {
                            $fecha_exp = NULL;
                        }
                        else 
                        {
                            $expiration_date = new DateTime($objetivo['expiration_date']);
                            $fecha_exp = date_format($expiration_date, 'd-m-Y');
                        }

                        //damos formato a fecha creación
                        if ($objetivo['created_at'] != NULL)
                        {
                            //$fecha_creacion = date_format($objetivo['created_at'],"d-m-Y");
                            $created_at = new DateTime($objetivo['created_at']);
                            $created_at = date_format($created_at, 'd-m-Y');
                        }
                        else
                            $fecha_creacion = NULL;

                        //damos formato a fecha de actualización 
                        if ($objetivo['updated_at'] != NULL)
                        {
                            $lala = new DateTime($objetivo['updated_at']);
                            $fecha_act = date_format($lala,"d-m-Y");
                        }
                        else
                            $fecha_act = NULL;

                        //damos formato a categoría de objetivo
                        if ($objetivo['objective_category_id'] == NULL)
                        {
                            $categoria = NULL;
                        }
                        else
                            $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('name');

                        if ($objetivo['perspective'] == NULL)
                        {
                            $perspective = NULL;
                        }
                        else
                        {
                            $perspective = $objetivo['perspective'];   
                        }

                        $objectives[$i] = array('id'=>$objetivo['id'],
                                        'nombre'=>$objetivo['name'],
                                        'descripcion'=>$objetivo['description'],
                                        'fecha_creacion'=>$created_at,
                                        'fecha_act'=>$fecha_act,
                                        'fecha_exp'=>$fecha_exp,
                                        'categoria'=>$categoria,
                                        'code' => $objetivo['code'],
                                        'estado'=>$objetivo['status'],
                                        'perspective' => $perspective);
                        $i += 1;

                    }
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.datos_maestros.objetivos.index',['objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'datos_plan' => $datos_plan, 'probador' => $i,'strategic_plan_id' => $plan_id]);
                    }
                    else
                    {
                        return view('datos_maestros.objetivos.index',['objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'datos_plan' => $datos_plan, 'probador' => $i,'strategic_plan_id' => $plan_id]);
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
                //$categorias = \Ermtool\Objective_category::where('status',0)->lists('name','id');
                //$org_id = \Ermtool\Organization::where('id',$_GET['organizacion'])->value('id');
                //seleccionamos objetivos de la misma organización y del mismo plan estratégico; para esto primero obtenemos plan estratégico vigente
                //$strategic_plan_id = \Ermtool\Strategic_plan::where('status',1)->where('organization_id',$org_id)->select('id')->first();

                //ahora seleccionamos objetivos pertenecientes a este plan
                $objectives = \Ermtool\Objective::where('strategic_plan_id','=',$_GET['strategic_plan_id'])
                                            ->select('id', DB::raw("CONCAT (code, ' - ', name) AS code_name"))
                                            ->orderBy('code')
                                            ->where('status','=',0)
                                            ->lists('code_name','id');
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.objetivos.create',['objectives' => $objectives,'strategic_plan_id' => $_GET['strategic_plan_id']]);
                }
                else
                {
                    return view('datos_maestros.objetivos.create',['objectives' => $objectives,'strategic_plan_id' => $_GET['strategic_plan_id']]);
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
                $org = DB::table('organizations')
                                ->join('strategic_plans','strategic_plans.organization_id','=','organizations.id')
                                ->where('strategic_plans.id','=',$_POST['strategic_plan_id'])
                                ->select('organizations.name','organizations.id')
                                ->first();
                
                global $org2;
                $org2 = $org;
                //$nombre_org = \Ermtool\Organization::name($_POST['organization_id']);
                global $verificador;
                DB::transaction(function() 
                {
                    $logger = $this->logger;
                    //si es que se agrego categoría de objetivo
                    /*if ($_POST['objective_category_id'])
                    {
                        $categoria = $_POST['objective_category_id'];
                    }
                    else
                    {
                        $categoria = NULL;
                    }*/

                    //haremos una revisión manual de que el codigo no se encuentra en el mismo plan estratégico
                    //para esto, primero que todo buscaremos todos los codigos del plan estratégico
                    $codes = DB::table('objectives')
                                ->where('strategic_plan_id','=',$_POST['strategic_plan_id'])
                                ->select('code')
                                ->get();

                    $GLOBALS['verificador'] = 0;
                    foreach ($codes as $code)
                    {
                        //vemos si es que existe el código
                        if (strcmp($code->code, $_POST['code']) == 0) //son iguales
                        {
                            $GLOBALS['verificador'] = 1;
                            break;
                        }
                    }

                    if ($GLOBALS['verificador'] == 0)
                    {

                        if (isset($_POST['perspective2']) && $_POST['perspective2'] != '')
                        {
                            $perspective2 = $_POST['perspective2'];
                        }
                        else
                        {
                            $perspective2 = NULL;
                        }
                        $objective = \Ermtool\Objective::create([
                                    'code' => $_POST['code'],
                                    'name' => $_POST['name'],
                                    'description' => $_POST['description'],
                                    'organization_id' => $GLOBALS['org2']->id,
                                    'status' => 0,
                                    'perspective' => $_POST['perspective'],
                                    'perspective2' => $perspective2,
                                    'strategic_plan_id' => $_POST['strategic_plan_id']
                                ]);

                        if (isset($_POST['objectives_id']))
                        {
                            //guardamos los enlaces
                            foreach ($_POST['objectives_id'] as $obj)
                            {
                                DB::table('objectives_impact')
                                    ->insert([
                                        'objective_father_id' => $objective->id,
                                        'objective_impacted_id' => $obj
                                        ]);
                            }
                        }

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Bussiness objective was successfully created');
                        }
                        else
                        {
                            Session::flash('message','Objetivo corporativo agregado correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el objetivo corporativo de Id: '.$objective->id.' llamado '.$objective->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('error','Bussiness objective could not be created since the code entered already exists on this plan.');
                        }
                        else
                        {
                            Session::flash('error','Objetivo corporativo no pudo ser creado dado que el código ingresado ya se encuentra en este plan.');
                        } 
                    }
                    
                });

                if ($verificador == 0)
                {
                    return Redirect::to('objetivos_plan.'.$_POST['strategic_plan_id']);
                }
                else
                {
                    return Redirect::to('objetivos.create?nombre_organizacion='.$org->name.'&strategic_plan_id='.$_POST['strategic_plan_id'])->withInput();
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
                
                $objs_selected = array();
                $objetivo = \Ermtool\Objective::find($id);


                //$org_id = \Ermtool\Organization::where('id',$objetivo['organization_id'])->value('id');

                //lista de objetivos
                $objectives = \Ermtool\Objective::where('strategic_plan_id','=',$objetivo['strategic_plan_id'])
                                            ->where('perspective','<',$objetivo['perspective'])
                                            ->select('id', DB::raw("CONCAT (code, ' - ', name) AS code_name"))
                                            ->orderBy('code')
                                            ->where('status','=',0)
                                            ->lists('code_name','id');

                $objetivos_sel = DB::table('objectives_impact')
                                    ->where('objective_father_id','=',$id)
                                    ->select('objective_impacted_id')
                                    ->get();

                $i = 0;
                foreach ($objetivos_sel as $obj)
                {
                    $objs_selected[$i] = $obj->objective_impacted_id;
                    $i += 1;
                }
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.objetivos.edit',['objetivo'=>$objetivo,'objectives' => $objectives, 'objs_selected' => $objs_selected,'strategic_plan_id' => $objetivo['strategic_plan_id']]);
                }
                else
                {
                    return view('datos_maestros.objetivos.edit',['objetivo'=>$objetivo,'objectives' => $objectives, 'objs_selected' => $objs_selected,'strategic_plan_id' => $objetivo['strategic_plan_id']]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtenemos lista de objetivos que puede impactar según perspectiva ingresada
    public function getObjectivesImpact($strategic_plan_id, $perspective)
    {
        try
        {
            return json_encode(\Ermtool\Objective::getObjectivesImpact($strategic_plan_id,$perspective));
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

                    $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);
                    $objetivo->status = 1;
                    $objetivo->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Objective successfully blocked');
                    }
                    else
                    {
                        Session::flash('message','Objetivo bloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el objetivo corporativo de Id: '.$GLOBALS['id1'].' llamado '.$objetivo->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s')); 
                });

                return Redirect::to('/objetivos');
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
                    $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);
                    $objetivo->status = 0;
                    $objetivo->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Objective successfully unblocked');
                    }
                    else
                    {
                        Session::flash('message','Objetivo desbloqueado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el objetivo corporativo de Id: '.$GLOBALS['id1'].' llamado '.$objetivo->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                //obtenemos org
                    $id_org = \Ermtool\Objective::where('id',$id)->value('organization_id');
                return Redirect::to('/objetivos?organizacion='.$id_org);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
/* Expiró el 05-10-2016: Se agrego misma función en la función index
    public function verbloqueados($strategic_plan_id)
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        else
        {
            $combobox = \Ermtool\Organization::where('status',0)
                                            ->lists('name','id'); //guardamos array con lista de nombre de organizaciones + id

            $nombre_organizacion = \Ermtool\Organization::name($id_organizacion);

            //seleccionamos todos los datos del plan para mostrarlo
            $datos_plan = \Ermtool\Strategic_plan::find($strategic_plan_id);

            //$strategic_plan = \Ermtool\Strategic_plan::where('status',1)->select('id')->first();

            $objective = array();
            $objetivos = \Ermtool\Objective::where('status',1)
                                                ->where('organization_id',(int)$id_organizacion)
                                                ->where('strategic_plan_id','=',$strategic_plan_id)
                                                ->get(); //select objetivos bloqueadas



            $i = 0;
            // ---recorremos todos los objetivos para asignar formato de datos correspondientes--- //
            foreach ($objetivos as $objetivo)
            {

                //damos formato a fecha expiración
                if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
                {
                    $fecha_exp = NULL;
                }
                else 
                    $fecha_exp = $objetivo['fecha_exp'];


                if ($objetivo['perspective'] == NULL)
                {
                    $perspective = NULL;
                }
                else
                {
                    $perspective = $objetivo['perspective'];   
                }

                $objective[$i] = array('id'=>$objetivo['id'],
                                    'nombre'=>$objetivo['name'],
                                    'descripcion'=>$objetivo['description'],
                                    'estado'=>$objetivo['status'],
                                    'perspective' => $perspective);
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objective,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i,'organizacion'=>$id_organizacion,'datos_plan' => $datos_plan,'strategic_plan_id' => $datos_plan->id]);
            }
            else
            {
                return view('datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objective,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i,'organizacion'=>$id_organizacion,'datos_plan' => $datos_plan,'strategic_plan_id' => $datos_plan->id]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //validamos nuevamente el código ingresado
                $codes = DB::table('objectives')
                                ->where('strategic_plan_id','=',$_POST['strategic_plan_id'])
                                ->where('id','<>',$id)
                                ->select('code')
                                ->get();

                $verificador = 0;
                foreach ($codes as $code)
                {
                    //vemos si es que existe el código
                    if (strcmp($code->code, $_POST['code']) == 0) //son iguales
                    {
                        $verificador = 1;
                        break;
                    }
                }

                if ($verificador == 0)
                {
                    global $id1;
                    $id1 = $id;
                    DB::transaction(function() 
                    {
                        $logger = $this->logger;
                        $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);

                        if ($_POST['perspective'] != "")
                        {
                            $perspective = $_POST['perspective'];
                        }
                        else
                        {
                            $perspective = NULL;
                        }

                        if (isset($_POST['perspective2']) && $_POST['perspective2'] != '')
                        {
                            $perspective2 = $_POST['perspective2'];
                        }
                        else
                        {
                            $perspective2 = NULL;
                        }

                        $objetivo->code = $_POST['code'];
                        $objetivo->name = $_POST['name'];
                        $objetivo->description = $_POST['description'];
                        $objetivo->perspective = $perspective;
                        $objetivo->perspective2 = $perspective2;

                        $objetivo->save();

                        //actualizamos objetivos impactados
                        DB::table('objectives_impact')->where('objective_father_id',$GLOBALS['id1'])->delete();

                        //ahora, agregamos posibles nuevas relaciones (si es que hay)
                        if (isset($_POST['objectives_id']))
                        {
                            foreach ($_POST['objectives_id'] as $obj)
                            {
                                DB::table('objectives_impact')
                                    ->insert([
                                        'objective_father_id' => $GLOBALS['id1'],
                                        'objective_impacted_id' => $obj
                                        ]);
                            }
                        }

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Objective was successfully updated');
                        }
                        else
                        {
                            Session::flash('message','Objetivo actualizado correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el objetivo corporativo de Id: '.$GLOBALS['id1'].' llamado '.$objetivo->name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                        
                    });
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Bussiness objective could not be stored since the code entered already exists on this plan.');
                    }
                    else
                    {
                        Session::flash('error','Objetivo corporativo no pudo ser actualizado dado que el código ingresado ya se encuentra en este plan.');
                    }

                    return Redirect::to('/objetivos.edit.'.$id)->withInput();
                }

                $id_org = \Ermtool\Objective::where('id',$id)->value('organization_id');
                return Redirect::to('objetivos_plan.'.$_POST['strategic_plan_id']);
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
                //verificamos que no posea riesgos
                $rev = DB::table('objective_risk')
                        ->where('objective_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //ahora vemos si tiene algún KPI
                    $rev = DB::table('kpi_objective')
                            ->where('objective_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                    if (empty($rev))
                    {
                        $name = DB::table('objectives')->where('id',$GLOBALS['id1'])->value('name');
                        //si pasa ambas validaciones se puede borrar
                        $GLOBALS['res'] = \Ermtool\Objective::deleteObjective($GLOBALS['id1']);

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el objetivo corporativo de Id: '.$GLOBALS['id1'].' llamado '.$name.' con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
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

    //obtiene objetivos de una organización
    public function getObjectives($org)
    {
        try
        {
            $results = array();
            //obtenemos objetivos
            $objectives = \Ermtool\Objective::where('status',0)
                                            ->where('organization_id',(int)$org)
                                            ->select('name','id')
                                            ->groupBy('id')
                                            ->get();

            $i = 0;
            foreach ($objectives as $objective)
            {
                $results[$i] = [
                    'id' => $objective->id,
                    'name' => $objective->name,
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
