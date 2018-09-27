<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;
use DB;
use Auth;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class OrganizationController extends Controller
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
        $this->logger = new Logger('organizaciones');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/organizaciones.log', Logger::INFO));
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
                $organization = array();
                if (isset($_GET['verbloqueados']))
                {
                    $organizations = \Ermtool\Organization::where('status',1)->get(); //select organizaciones bloqueadas  
                }
                else
                {
                    $organizations = \Ermtool\Organization::where('status',0)->get(); //select organizaciones desbloqueadas
                }

                $org_dependientes = array();
                $i = 0;
                $j = 0;
                // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
                foreach ($organizations as $organizaciones)
                {
                    //buscamos organizaciones que dependen de ésta
                    $organizaciones_dependientes = \Ermtool\Organization::where('organization_id',$organizaciones['id'])->get();
                    
                    
                    foreach ($organizaciones_dependientes as $hijos)
                    {
                        $org_dependientes[$j] = array('organization_id'=>$organizaciones['id'],
                                                     'id'=>$hijos['id'],
                                                     'nombre'=>$hijos['name']);
                        $j += 1;
                    }

                    //damos formato a fecha expiración
                    if ($organizaciones['expiration_date'] == NULL OR $organizaciones['expiration_date'] == "0000-00-00")
                    {
                        $fecha_exp = NULL;
                    }
                    else 
                    {
                        $expiration_date = new DateTime($organizaciones['expiration_date']);
                        $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    }

                    if ($organizaciones['mision'] == NULL || $organizaciones['mision'] == "")
                    {
                        $mision = NULL;
                    }
                    else
                    {
                        $mision = $organizaciones['mision'];
                    }

                    if ($organizaciones['vision'] == NULL || $organizaciones['vision'] == "")
                    {
                        $vision = NULL;
                    }
                    else
                    {
                        $vision = $organizaciones['vision'];
                    }

                    if ($organizaciones['target_client'] == NULL || $organizaciones['target_client'] == "")
                    {
                        $target_client = NULL;      
                    }
                    else
                    {
                        $target_client = $organizaciones['target_client'];
                    }

                    //ACT 07-06-2018: Obtenemos responsable
                    if ($organizaciones['stakeholder_id'] == NULL)
                    {
                        $stakeholder = NULL;      
                    }
                    else
                    {
                        $stakeholder = \Ermtool\Stakeholder::getName($organizaciones['stakeholder_id']);
                    }
                    
                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($organizaciones['description'],0,100);

                    $organization[$i] = [
                        'id'=>$organizaciones['id'],
                        'nombre'=>$organizaciones['name'],
                        'descripcion'=>$organizaciones['description'],
                        'target_client'=>$target_client,
                        'mision'=>$mision,
                        'vision'=>$vision,
                        'fecha_exp'=>$fecha_exp,
                        'serv_compartidos'=>$organizaciones['shared_services'],
                        'estado'=>$organizaciones['status'],
                        'short_des'=>$short_des,
                        'stakeholder' => $stakeholder
                    ];
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.organization.index',['organizations'=>$organization,'org_dependientes'=>$org_dependientes]);
                }
                else
                {
                    return view('datos_maestros.organization.index',['organizations'=>$organization,'org_dependientes'=>$org_dependientes]);
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
                //ACTUALIZACIÓN 04-01-18: Agregamos tipos de moneda para materialidad
                $kinds = ['1'=>'Peso','2'=>'Dólar','3'=>'Euro','4'=>'UF']; 

                //ACT 07-06-18: obtenemos lista de stakeholders
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                if (Session::get('languaje') == 'en')
                {
                    //ACTUALIZACIÓN 31-10-17: Existirán distintos niveles de organizaciones, por lo que no sólo se mostrarán las de primer nivel, sino que todas
                    //$organizations = \Ermtool\Organization::where('status',0)->where('organization_id',NULL)->lists('name','id');
                    $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                    return view('en.datos_maestros.organization.create',['organizations'=>$organizations, 'kinds'=>$kinds, 'stakeholders' => $stakeholders]);
                }
                else
                {
                    //ACTUALIZACIÓN 31-10-17: Existirán distintos niveles de organizaciones, por lo que no sólo se mostrarán las de primer nivel, sino que todas
                    //$organizations = \Ermtool\Organization::where('status',0)->where('organization_id',NULL)->lists('name','id');
                    $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                    return view('datos_maestros.organization.create',['organizations'=>$organizations, 'kinds'=>$kinds, 'stakeholders' => $stakeholders]);
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
                DB::transaction(function()
                {
                    $logger = $this->logger;
                   

                    $org = \Ermtool\Organization::create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'expiration_date' => isset($_POST['expiration_date']) && $_POST['expiration_date'] != '' ? $_POST['expiration_date'] : NULL,
                        'shared_services' => $_POST['shared_services'],
                        'organization_id' => isset($_POST['organization_id']) && $_POST['organization_id'] != '' ? $_POST['organization_id'] : NULL,
                        'mision' => isset($_POST['mision']) && $_POST['mision'] != '' ? $_POST['mision'] : NULL,
                        'vision' => isset($_POST['vision']) && $_POST['vision'] != '' ? $_POST['vision'] : NULL,
                        'target_client' => isset($_POST['target_client']) && $_POST['target_client'] != '' ? $_POST['target_client'] : NULL,
                        'ebt' => isset($_POST['ebt']) && $_POST['ebt'] != '' ? $_POST['ebt'] : NULL,
                        'kind_ebt' => isset($_POST['kind_ebt']) && $_POST['kind_ebt'] != '' ? $_POST['kind_ebt'] : NULL,
                        'stakeholder_id' => isset($_POST['stakeholder_id']) && $_POST['stakeholder_id'] != '' ? $_POST['stakeholder_id'] : NULL,
                        ]);

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Organization created successfully');
                    }
                    else
                    {
                        Session::flash('message','Organizaci&oacute;n creada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la organización con Id: '.$org->id.' llamada: '.$org->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('/organization');
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
                //ACTUALIZACIÓN 04-01-18: Agregamos tipos de moneda para materialidad
                $kinds = ['1'=>'Peso','2'=>'Dólar','3'=>'Euro','4'=>'UF']; 

                //ACT 07-06-18: obtenemos lista de stakeholders
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                global $id2;
                $id2 = $id; 
                $organizations = \Ermtool\Organization::where('id','<>',$id)
                        ->where('status',0)
                        ->where((function ($query) {
                        $query->where('organization_id','<>',$GLOBALS['id2'])
                            ->orWhere('organization_id','=',NULL);
                        }))
                        ->lists('name','id');
                $org = \Ermtool\Organization::find($id);

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org, 'kinds'=>$kinds, 'stakeholders' => $stakeholders]);
                }
                else
                {
                    return view('datos_maestros.organization.edit',['organizations'=>$organizations,'organization'=>$org, 'kinds'=>$kinds, 'stakeholders' => $stakeholders]);
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
                    $organization = \Ermtool\Organization::find($GLOBALS['id1']);
                    $organization->status = 1;
                    $organization->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Organization blocked successfully');
                    }
                    else
                    {
                        Session::flash('message','Organizaci&oacute;n bloqueada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado la organización con Id: '.$organization->id.' llamada: '.$organization->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/organization');
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
                    $organization = \Ermtool\Organization::find($GLOBALS['id1']);
                    $organization->status = 0;
                    $organization->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Organization unblocked successfully');
                    }
                    
                    else
                    {
                        Session::flash('message','Organizaci&oacute;n desbloqueada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado la organización con Id: '.$organization->id.' llamada: '.$organization->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                    
                });
                return Redirect::to('/organization');
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
                //hacemos variable id como variable global
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $organization = \Ermtool\Organization::find($GLOBALS['id1']);

                    //vemos si tiene organización padre
                    if($_POST['organization_id'] != "")
                    {
                        $organizacion_padre = $_POST['organization_id'];
                    }
                    else
                    {
                        $organizacion_padre = NULL;
                    }
                    if ($_POST['mision'] != "")
                        $mision = $_POST['mision'];
                    else
                        $mision = NULL;

                    if ($_POST['vision'] != "")
                        $vision = $_POST['vision'];
                    else
                        $vision = NULL;

                    if ($_POST['target_client'] != "")
                        $target_client = $_POST['target_client'];
                    else
                        $target_client = NULL;

                    if ($_POST['expiration_date'] == NULL || $_POST['expiration_date'] == "")
                        $exp_date = NULL;
                    else
                        $exp_date = $_POST['expiration_date'];

                    //ACT 08-01-17: Se agrega EBT al crear organización
                    if (isset($_POST['ebt']))
                    {
                        if ($_POST['ebt'] != '')
                        {
                            $ebt = $_POST['ebt'];
                            $kind = $_POST['kind_ebt'];
                        }
                        else
                        {
                            $ebt = NULL;
                            $kind = NULL;
                        }
                    }
                    else
                    {
                        $ebt = NULL;
                        $kind = NULL;
                    }

                    $organization->name = $_POST['name'];
                    $organization->description = $_POST['description'];
                    $organization->expiration_date = $exp_date;
                    $organization->shared_services = $_POST['shared_services'];
                    $organization->organization_id = $organizacion_padre;
                    $organization->mision = $mision;
                    $organization->vision = $vision;
                    $organization->target_client = $target_client;
                    $organization->ebt = $ebt;
                    $organization->kind_ebt = $kind;
                    $organization->stakeholder_id = isset($_POST['stakeholder_id']) && $_POST['stakeholder_id'] != '' ? $_POST['stakeholder_id'] : NULL;

                    $organization->save();
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Organization update successfully');
                    }    
                    else
                    {
                        Session::flash('message','Organizaci&oacute;n actualizada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado la organización con Id: '.$organization->id.' llamada: '.$organization->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('/organization');
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
            /* /NO veremos si es que existe algún elemento enlazado a la organización
             de ser así, la organización no podrá ser eliminada NO/

            ACTUALIZACIÓN 08-08-2016: Sólo veremos si es que no posee objetivos, issues o planes de auditoría,
            ya que son los únicos elementos que dependen totalmente de una organización (un subproceso u 
            stakeholder puede pertenecer a otra organización) */

            //vemos si tiene objetivos asociados
            $rev = DB::table('objectives')
                    ->where('organization_id','=',$id)
                    ->select('id')
                    ->get();


            if (empty($rev))
            {
                //vemos si tiene issues asociadas
                $rev = DB::table('issues')
                        ->where('organization_id','=',$id)
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //vemos si tiene planes de auditoría asociados
                    $rev = DB::table('audit_plans')
                            ->where('organization_id','=',$id)
                            ->select('id')
                            ->get();

                    if (empty($rev))
                    {
                        $logger = $this->logger;
                        //Eliminamos, primero de los datos no únicos
                        //nombre de org
                        $name = \Ermtool\Organization::name($id);

                        DB::table('organization_subprocess')
                            ->where('organization_id','=',$id)
                            ->delete();

                        DB::table('organization_stakeholder')
                            ->where('organization_id','=',$id)
                            ->delete();

                        DB::table('organizations')
                            ->where('id','=',$id)
                            ->delete();

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la organización con Id: '.$id.' llamada: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                        return 0;
                    }
                    else
                    {
                        return 1;
                    }
                }
                else
                {
                    return 1;
                }
            }
            else
            {
                return 1;
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function organizationChart()
    {
        if (Session::get('languaje') == 'en')
        {
            return view('en.reportes.organigrama');
        }
        else
        {
            return view('reportes.organigrama');
        }
    }

    public function getOrganizations()
    {
        $organizations = \Ermtool\Organization::where('status',0)->get();

        foreach ($organizations as $o)
        {
            //Obtenemos nombre de org padre
            $org = $o->organization_id != NULL ? \Ermtool\Organization::name($o->organization_id) : '';
            $o->org_father = $org;
        }
        return json_encode($organizations);
    }
}
