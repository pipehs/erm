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

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class RiesgosController extends Controller
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
        $this->logger = new Logger('riesgos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/riesgos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    //AGREGADO 26-07 SELECCIONAR PRIMERO ORGANIZACIÓN
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
                $riesgos = array();
                
                $i = 0; //contador de riesgos
                $riesgos2 = \Ermtool\Risk::getRisks(NULL,NULL);

                foreach ($riesgos2 as $riesgo)
                {
                    $j = 0; //contador de subprocesos u objetivos relacionados
                    $responsables = array();
                    $orgs = array();
                    //obtenemos organizaciones y responsables por organización
                    $orgs2 = \Ermtool\Organization::getOrganizationsFromRisk($riesgo->id);

                    $l = 0; //contador de organizaciones
                    foreach ($orgs2 as $o)
                    {
                        $orgs[$l] = \Ermtool\Organization::name($o->organization_id);

                        if ($o->stakeholder_id != NULL)
                        {
                            $responsables[$l] = \Ermtool\Stakeholder::getName($o->stakeholder_id);
                        }
                        else
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $responsables[$l] = 'No specified';
                            }
                            else
                            {
                                $responsables[$l] = 'No especificado';
                            }
                        }
                        $l += 1;
                    }
                    //damos formato a tipo de riesgo
                    if ($riesgo->type == 0)
                    {
                        $tipo = 0;
                        //obtenemos subprocesos relacionados
                        //$subprocesses = \Ermtool\Risk::find($riesgo['id'])->subprocesses;
                        $subprocesses = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('risk_subprocess.risk_id','=',$riesgo->id)
                                        ->groupBy('subprocesses.name','subprocesses.id')
                                        ->select('subprocesses.name','subprocesses.id')
                                        ->get();

                        foreach($subprocesses as $subprocess)
                        {
                            //agregamos org_name ya que este estará identificado si el riesgo es de negocio
                            //$relacionados[$j] = array('risk_id'=>$riesgo->id,
                                                //'id'=>$subprocess->id,
                                                //'nombre'=>$subprocess->name);
                            $subobj[$j] = ['id' => $subprocess->id, 'name' => $subprocess->name];
                            $j += 1;
                        }
                    }
                    else if ($riesgo->type == 1)
                    {
                        $tipo = 1;
                        //primero obtenemos objetivos relacionados
                        //$objectives = \Ermtool\Risk::find($riesgo['id'])->objectives;
                        $objectives = DB::table('objectives')
                                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                                        ->where('objective_risk.risk_id','=',$riesgo->id)
                                        ->select('objectives.name','objectives.id')
                                        ->get();

                        foreach ($objectives as $objective)
                        {
                            //obtenemos organización
                            //$org = \Ermtool\Organization::where('id',$objective['organization_id'])->value('name');
                            //$relacionados[$j] = array('risk_id'=>$riesgo->id,
                                                    //'id'=>$objective->id,
                                                    //'nombre'=>$objective->name);

                            $subobj[$j] = ['id' => $subprocess->id, 'name' => $subprocess->name];
                            $j += 1;

                        }
                    }

                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($riesgo->created_at == NULL OR $riesgo->created_at == "0000-00-00" OR $riesgo->created_at == "")
                    {
                        $fecha_creacion = NULL;
                    }

                    else
                    {
                        $fecha_creacion = new DateTime($riesgo->created_at);
                        $fecha_creacion = date_format($fecha_creacion,"d-m-Y");
                    }

                    //damos formato a fecha expiración
                    if ($riesgo->expiration_date == NULL OR $riesgo->expiration_date == "0000-00-00")
                    {
                        $fecha_exp = NULL;
                    }
                    else
                    { 
                        $expiration_date = new DateTime($riesgo->expiration_date);
                        $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    }

                    //damos formato a fecha de actualización 
                    if ($riesgo->updated_at != NULL)
                    {
                        $fecha_act = new DateTime($riesgo->updated_at);
                        $fecha_act = date_format($fecha_act,"d-m-Y");
                    }

                    //obtenemos nombre de categoría
                    $categoria = \Ermtool\Risk_category::where('id',$riesgo->risk_category_id)->value('name');

                    //obtenemos causas si es que tiene
                    $causes = DB::table('cause_risk')
                                ->join('causes','causes.id','=','cause_risk.cause_id')
                                ->where('cause_risk.risk_id','=',$riesgo->id)
                                ->select('causes.name','causes.description')
                                ->get();

                    if ($causes)
                    {
                        $causas = array();
                        $k = 0;
                        foreach ($causes as $cause)
                        {
                            $causas[$k] = $cause->name.' - '.$cause->description;
                            $k += 1;
                        }
                    }
                    else
                    {
                        $causas = NULL;
                    }

                    //obtenemos efectos si es que existen
                    $effects = DB::table('effect_risk')
                                ->join('effects','effects.id','=','effect_risk.effect_id')
                                ->where('effect_risk.risk_id','=',$riesgo->id)
                                ->select('effects.name','effects.description')
                                ->get();

                    if ($effects)
                    {
                        $efectos = array();
                        $k = 0;
                        foreach ($effects as $effect)
                        {
                            $efectos[$k] = $effect->name.' - '.$effect->description;
                            $k += 1;
                        }
                    }
                    else
                    {
                        $efectos = NULL;
                    }

                    //ACTUALIZACIÓN 01-12-17: Agregamos probabilidad e impacto en mantenedor
                    if (Session::get('languaje') == 'en')
                    {
                        $proba_string = ['Very low','Low','Medium','High','Very high'];
                        $impact_string = ['Very low','Low','Medium','High','Very high'];
                    }
                    else
                    {
                        $proba_string = ['Muy poco probable','Poco probable','Intermedio','Probable','Muy probable'];
                        $impact_string = ['Muy poco impacto','Poco impacto','Intermedio','Alto impacto','Muy alto impacto'];
                    }

                    //primero obtenemos maxima fecha de evaluacion para el riesgo
                    $fecha = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$riesgo->id)
                                    ->max('evaluations.updated_at');

                    //ACT 04-04-17: Sacamos guiones para SQL Server
                    //$fecha = str_replace('-','',$fecha);

                    //obtenemos proba, impacto y score
                    $eval_risk = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$riesgo->id)
                                    ->where('evaluations.updated_at','=',$fecha)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->get();

                    if (!empty($eval_risk))
                    {
                        foreach ($eval_risk as $eval)
                        {
                            if ($eval->avg_probability != NULL AND $eval->avg_impact != NULL)
                            {
                                $impacto = $eval->avg_impact.' ('.$impact_string[$eval->avg_impact-1].')';
                                $probabilidad = $eval->avg_probability.' ('.$proba_string[$eval->avg_probability-1].')';
                                $score = $impacto * $probabilidad;
                            }
                        }
                    }
                    else
                    {
                        $impacto = 'Sin eval.';
                        $probabilidad = 'Sin eval.';
                        $score = 'Sin eval.';
                    }

                    //ACT 25-04-17: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($riesgo->description,0,100);

                    $riesgos[$i] = array('id'=>$riesgo->id,
                                        'nombre'=>$riesgo->name,
                                        'descripcion'=>$riesgo->description,
                                        'tipo'=>$tipo,
                                        'fecha_creacion'=>$fecha_creacion,
                                        'responsables'=>$responsables,
                                        'fecha_exp'=>$fecha_exp,
                                        'categoria'=>$categoria,
                                        'causas'=>$causas,
                                        'efectos'=>$efectos,
                                        'short_des'=>$short_des,
                                        'orgs' => $orgs,
                                        'subobj' => $subobj,
                                        'impacto' => $impacto,
                                        'probabilidad' => $probabilidad,
                                        'score' => $score);

                    $i += 1;

                }

                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                //ACTUALIZACIÓN 02-03-17: Agregamos filtro de categorías de riesgos (todas las categorías)
                //ACT 17-08-17: Mostramos sólo categorías principales
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.riesgos.index',['organizations' => $organizations,'categories' => $categories,'riesgos' => $riesgos]);
                }
                else
                {
                    return view('riesgos.index',['organizations' => $organizations, 'categories' => $categories,'riesgos' => $riesgos]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function index2()
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                //ACTUALIZACIÓN 02-03-17: Agregamos filtro de categorías de riesgos (todas las categorías)
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                $org = \Ermtool\Organization::find($_GET['organization_id']);

                $riesgos = array();
                $relacionados = array();
                $i = 0; //contador de riesgos

                //ACTUALIZACIÓN 01-11-17: Vemos si se agregó sub-subcategoría
                if (isset($_GET['risk_subcategory_id2']) && $_GET['risk_subcategory_id2'] != '')
                {
                    $category = $_GET['risk_subcategory_id2'];
                }
                //ACTUALIZACIÓN 18-08-17: Vemos si se agregó subcategoría
                else if (isset($_GET['risk_subcategory_id']) && $_GET['risk_subcategory_id'] != '')
                {
                    $category = $_GET['risk_subcategory_id'];
                }
                else if (isset($_GET['risk_category_id']))
                {
                    $category = $_GET['risk_category_id'];
                }
                else
                {
                    $category = NULL;
                }

                $riesgos2 = \Ermtool\Risk::getRisks($_GET['organization_id'],$category);

                $j = 0; //contador de subprocesos u objetivos relacionados

                foreach ($riesgos2 as $riesgo)
                {
                    //damos formato a tipo de riesgo
                    if ($riesgo->type == 0)
                    {
                        $tipo = 0;
                        //primero obtenemos subprocesos relacionados
                        //$subprocesses = \Ermtool\Risk::find($riesgo['id'])->subprocesses;
                        $subprocesses = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('risk_subprocess.risk_id','=',$riesgo->id)
                                        ->where('organization_subprocess.organization_id','=',$_GET['organization_id'])
                                        ->select('subprocesses.name','subprocesses.id')
                                        ->get();

                        foreach($subprocesses as $subprocess)
                        {
                            //agregamos org_name ya que este estará identificado si el riesgo es de negocio
                            $relacionados[$j] = array('risk_id'=>$riesgo->id,
                                                'id'=>$subprocess->id,
                                                'nombre'=>$subprocess->name);
                            $j += 1;
                        }
                    }
                    else if ($riesgo->type == 1)
                    {
                        $tipo = 1;
                        //primero obtenemos objetivos relacionados
                        //$objectives = \Ermtool\Risk::find($riesgo['id'])->objectives;
                        $objectives = DB::table('objectives')
                                        ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                                        ->where('objective_risk.risk_id','=',$riesgo->id)
                                        ->where('objectives.organization_id','=',$_GET['organization_id'])
                                        ->select('objectives.name','objectives.id')
                                        ->get();

                        foreach ($objectives as $objective)
                        {
                            //obtenemos organización
                            //$org = \Ermtool\Organization::where('id',$objective['organization_id'])->value('name');
                            $relacionados[$j] = array('risk_id'=>$riesgo->id,
                                                    'id'=>$objective->id,
                                                    'nombre'=>$objective->name);

                            $j += 1;
                        }
                    }

                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($riesgo->created_at == NULL OR $riesgo->created_at == "0000-00-00" OR $riesgo->created_at == "")
                    {
                        $fecha_creacion = NULL;
                    }

                    else
                    {
                        $fecha_creacion = new DateTime($riesgo->created_at);
                        $fecha_creacion = date_format($fecha_creacion,"d-m-Y");
                    }

                    //damos formato a fecha expiración
                    if ($riesgo->expiration_date == NULL OR $riesgo->expiration_date == "0000-00-00")
                    {
                        $fecha_exp = NULL;
                    }
                    else
                    { 
                        $expiration_date = new DateTime($riesgo->expiration_date);
                        $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    }

                    //damos formato a fecha de actualización 
                    if ($riesgo->updated_at != NULL)
                    {
                        $fecha_act = new DateTime($riesgo->updated_at);
                        $fecha_act = date_format($fecha_act,"d-m-Y");
                    }

                    //obtenemos nombre de categoría
                    $categoria = \Ermtool\Risk_category::where('id',$riesgo->risk_category_id)->value('name');

                    //obtenemos causas si es que tiene
                    $causes = DB::table('cause_risk')
                                ->join('causes','causes.id','=','cause_risk.cause_id')
                                ->where('cause_risk.risk_id','=',$riesgo->id)
                                ->select('causes.name','causes.description')
                                ->get();

                    if ($causes)
                    {
                        $causas = array();
                        $k = 0;
                        foreach ($causes as $cause)
                        {
                            $causas[$k] = $cause->name.' - '.$cause->description;
                            $k += 1;
                        }
                    }
                    else
                    {
                        $causas = NULL;
                    }

                    $stakeholder = DB::table('stakeholders')
                                        ->where('id',$riesgo->stakeholder_id)
                                        ->select('name','surnames')
                                        ->first();

                    if (!$stakeholder)
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $stakeholder = (object) array('name'=>'No','surnames'=>'specified');
                        }
                        else
                        {
                            $stakeholder = (object) array('name'=>'No','surnames'=>'especificado');
                        }
                    }

                    //obtenemos efectos si es que existen
                    $effects = DB::table('effect_risk')
                                ->join('effects','effects.id','=','effect_risk.effect_id')
                                ->where('effect_risk.risk_id','=',$riesgo->id)
                                ->select('effects.name','effects.description')
                                ->get();

                    if ($effects)
                    {
                        $efectos = array();
                        $k = 0;
                        foreach ($effects as $effect)
                        {
                            $efectos[$k] = $effect->name.' - '.$effect->description;
                            $k += 1;
                        }
                    }
                    else
                    {
                        $efectos = NULL;
                    }

                    //ACTUALIZACIÓN 01-12-17: Agregamos probabilidad e impacto en mantenedor
                    if (Session::get('languaje') == 'en')
                    {
                        $proba_string = ['Very low','Low','Medium','High','Very high'];
                        $impact_string = ['Very low','Low','Medium','High','Very high'];
                    }
                    else
                    {
                        $proba_string = ['Muy poco probable','Poco probable','Intermedio','Probable','Muy probable'];
                        $impact_string = ['Muy poco impacto','Poco impacto','Intermedio','Alto impacto','Muy alto impacto'];
                    }

                    //ACT 18-12-17: Obtenemos org_risk_id
                    $org_risk = \Ermtool\Risk::getOrgRisk($riesgo->id,$_GET['organization_id']);

                    //primero obtenemos maxima fecha de evaluacion para el riesgo
                    $fecha = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$org_risk->id)
                                    ->where('evaluations.consolidation','=',1)
                                    ->max('evaluations.updated_at');

                    //ACT 04-04-17: Sacamos guiones para SQL Server
                    //$fecha = str_replace('-','',$fecha);

                    //obtenemos proba, impacto y score
                    $eval_risk = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$org_risk->id)
                                    ->where('evaluations.updated_at','=',$fecha)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->get();

                    if (!empty($eval_risk))
                    {
                        $impacto = '';
                        $probabilidad = '';
                        $score = '';
                        foreach ($eval_risk as $eval)
                        {
                            if ($eval->avg_probability != NULL AND $eval->avg_impact != NULL)
                            {
                                $impacto = $eval->avg_impact.' ('.$impact_string[$eval->avg_impact-1].')';
                                $probabilidad = $eval->avg_probability.' ('.$proba_string[$eval->avg_probability-1].')';
                                $score = $impacto * $probabilidad;
                            }
                        }
                    }
                    else
                    {
                        $impacto = 'Sin eval.';
                        $probabilidad = 'Sin eval.';
                        $score = 'Sin eval.';
                    }
                    

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($riesgo->description,0,100);

                    $riesgos[$i] = array('id'=>$riesgo->id,
                                        'nombre'=>$riesgo->name,
                                        'descripcion'=>$riesgo->description,
                                        'tipo'=>$tipo,
                                        'fecha_creacion'=>$fecha_creacion,
                                        'stakeholder'=>$stakeholder->name.' '.$stakeholder->surnames,
                                        'fecha_exp'=>$fecha_exp,
                                        'categoria'=>$categoria,
                                        'causas'=>$causas,
                                        'efectos'=>$efectos,
                                        'short_des'=>$short_des,
                                        'impacto' => $impacto,
                                        'probabilidad' => $probabilidad,
                                        'score' => $score);

                    $i += 1;

                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.riesgos.index',['riesgos'=>$riesgos,'relacionados'=>$relacionados,'organizations' => $organizations,'org_selected' => $org->name, 'org_id' => $org->id,'categories' => $categories]);
                }
                else
                {
                    return view('riesgos.index',['riesgos'=>$riesgos,'relacionados'=>$relacionados,'organizations' => $organizations,'org_selected' => $org->name, 'org_id' => $org->id,'categories' => $categories]);
                }
                //return json_encode(['riesgos'=>$riesgos,'relacionados'=>$relacionados]);
                //print_r($relacionados);
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
        //try
        //{
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //categorias de riesgo
                $categorias = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                //causas preingresadas
                $causas = \Ermtool\Cause::where('status',0)->select('name','id','description')->get();

                //efectos preingresados
                $efectos = \Ermtool\Effect::where('status',0)->select('name','id','description')->get();

                //riesgos tipo
                $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

                //obtenemos lista de stakeholders
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                //ACTUALIZACIÓN 29-03-17: SELECCIONAMOS SI SE DESEA AGREGAR A OTRAS ORGANIZACIONES
                $organizations = \Ermtool\Organization::where('status',0)->where('id','<>',$_GET['org'])->lists('name','id');

                //ACTUALIZACIÓN 04-01-18: Agregamos tipos de moneda para materialidad
                $kinds = ['1'=>'Peso','2'=>'Dólar','3'=>'Euro','4'=>'UF']; 

                //Obtenemos EBT. Para esto, primero vemos si esta organización tiene EBT, sino buscamos EBT de org principal
                $ebt = \Ermtool\Organization::getEBT($_GET['org'],1);

                if ($ebt->ebt == NULL)
                {
                    $ebt = \Ermtool\Organization::getEBT($_GET['org'],2);

                    //si org principal no tiene EBT
                    if ($ebt->ebt == NULL)
                    {
                        $ebt = array();
                    }
                }

                if(isset($_GET['P']))
                {
                    //ACTUALIZACIÓN 26-07-16: SOLO MOSTRAMOS PROCESOS PERTENECIENTES A LA EMPRESA QUE SE ESTÁ CONSULTANDO
                    //ACT 07-03-18: También seleccionamos proceso (para casos como Emaresa donde existen subprocesos con el mismo nombre para distinto proceso)
                    $subprocesos = DB::table('subprocesses')
                                    ->join('processes','processes.id','=','subprocesses.process_id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                    ->where('subprocesses.status','=',0)
                                    ->select('processes.name as process','subprocesses.name','subprocesses.id')
                                    ->get();

                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                            'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo,'stakeholders'=>$stakeholders,'org_id' => $_GET['org'],'organizations' => $organizations,'kinds'=>$kinds,'ebt' => $ebt]);
                    }
                    else
                    {
                        return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                            'efectos'=>$efectos,'subprocesos'=>$subprocesos,'riesgos_tipo'=>$riesgos_tipo,'stakeholders'=>$stakeholders,'org_id' => $_GET['org'],'organizations' => $organizations,'kinds'=>$kinds,'ebt' => $ebt]);
                    }
                }

                else if (isset($_GET['N']))
                {
                    
                    $objectives = DB::table('objectives')
                                    ->where('organization_id','=',$_GET['org'])
                                    ->where('status','=',0)
                                    ->lists('name','id');

                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                                'efectos'=>$efectos,'objetivos'=>$objectives,'riesgos_tipo'=>$riesgos_tipo,'stakeholders'=>$stakeholders,'org_id' => $_GET['org'],'organizations' => $organizations,'kinds'=>$kinds,'ebt' => $ebt]);
                    }
                    else
                    {
                        return view('riesgos.create',['categorias'=>$categorias,'causas'=>$causas,
                                'efectos'=>$efectos,'objetivos'=>$objectives,'riesgos_tipo'=>$riesgos_tipo,'stakeholders'=>$stakeholders,'org_id' => $_GET['org'],'organizations' => $organizations,'kinds'=>$kinds,'ebt' => $ebt]);
                    }
                }
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //try
        //{
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                global $evidence;
                $evidence = $request->file('evidence_doc');
                //creamos una transacción para cumplir con atomicidad
                DB::transaction(function()
                {
                    $logger = $this->logger;
                        //vemos si es de proceso o de negocio
                            if (isset($_POST['subprocess_id']))
                            {
                                $type = 0;
                            }
                            else if (isset($_POST['objective_id']))
                            {
                                $type = 1;
                            }

                            if (!isset($_POST['stakeholder_id']) || $_POST['stakeholder_id'] == "")
                            {
                                $stake = NULL;
                            }
                            else
                            {
                                $stake = $_POST['stakeholder_id'];
                            }

                            if (!isset($_POST['description']) || $_POST['description'] == "")
                            {
                                $description = NULL;
                            }
                            else
                            {
                                $description = $_POST['description'];
                            }

                            //ACTUALIZACIÓN 18-08-17: Vemos si se agregó subcategoría
                            //ACTUALIZACIÓN 01-11-17: Vemos si se agregó sub-subcategoría
                            if (isset($_GET['risk_subcategory_id2']) && $_GET['risk_subcategory_id2'] != '')
                            {
                                $risk_category_id = $_POST['risk_subcategory_id2'];
                            }
                
                            else if (isset($_POST['risk_subcategory_id']) && $_POST['risk_subcategory_id'] != '')
                            {
                                $risk_category_id = $_POST['risk_subcategory_id'];
                            }
                            else
                            {
                                if (!isset($_POST['risk_category_id']) || $_POST['risk_category_id'] == "")
                                {
                                    $risk_category_id = NULL;
                                }
                                else
                                {
                                    $risk_category_id = $_POST['risk_category_id'];
                                }
                            }

                            if (!isset($_POST['expected_loss']) || $_POST['expected_loss'] == "")
                            {
                                $expected_loss = NULL;
                            }
                            else
                            {
                                $expected_loss = $_POST['expected_loss'];
                            }

                            if (!isset($_POST['expiration_date']) || $_POST['expiration_date'] == "")
                            {
                                $expiration_date = NULL;
                            }
                            else
                            {
                                $expiration_date = $_POST['expiration_date'];
                            }

                            //ACTUALIZACIÓN 26-08-17: Agregado comentarios o posibles comentarios al crear riesgo
                            if (!isset($_POST['comments']) || $_POST['comments'] == "")
                            {
                                $comments = NULL;
                            }
                            else
                            {
                                $comments = $_POST['comments'];
                                $comments = eliminarSaltos($comments);
                            }

                            if (!isset($_POST['comments2']) || $_POST['comments2'] == "")
                            {
                                $comments2 = NULL;
                            }
                            else
                            {
                                $comments2 = $_POST['comments2'];
                                $comments2 = eliminarSaltos($comments2);
                            }

                            //ACTUALIZACIÓN 08-01-17: Agregamos impacto y probabilidades brutas 
                            if (!isset($_POST['impact']) || $_POST['impact'] == "")
                            {
                                $impact = NULL;
                            }
                            else
                            {
                                $impact = $_POST['impact'];
                            }

                            if (!isset($_POST['probability']) || $_POST['expiration_date'] == "")
                            {
                                $expiration_date = NULL;
                            }
                            else
                            {
                                $expiration_date = $_POST['expiration_date'];
                            } 

                            $description = eliminarSaltos($description);

                            $risk = \Ermtool\Risk::create([
                                'name'=>$_POST['name'],
                                'description'=>$description,
                                'type'=>$type,
                                'type2'=>1,
                                'expiration_date'=>$expiration_date,
                                'risk_category_id'=>$risk_category_id,
                                //'stakeholder_id'=>$stake,
                                'expected_loss'=>$expected_loss,
                                'comments' => $comments
                                ]);

                            //vemos si se agrego alguna causa nueva
                            if (isset($_POST['causa_nueva']))
                            {
                                $new_causa = \Ermtool\Cause::create([
                                    'name'=>$_POST['causa_nueva']
                                ]);

                                //guardamos en cause_risk
                                DB::table('cause_risk')
                                    ->insert([
                                        'risk_id' => $risk->id,
                                        'cause_id' => $new_causa->id,
                                        ]);
                            }
                            else //se están agregando causas ya creadas
                            {
                                if (isset($_POST['cause_id']))
                                {
                                    foreach ($_POST['cause_id'] as $cause_id)
                                    {
                                        //insertamos cada causa en cause_risk
                                        DB::table('cause_risk')
                                            ->insert([
                                                'risk_id' => $risk->id,
                                                'cause_id' => $cause_id
                                                ]);
                                    }
                                } 
                            }

                            //vemos si se agrego algún efecto nuevo
                            if (isset($_POST['efecto_nuevo']))
                            {
                                $new_effect = \Ermtool\Effect::create([
                                    'name'=>$_POST['efecto_nuevo']
                                    ]);

                                 //guardamos en cause_risk
                                DB::table('effect_risk')
                                    ->insert([
                                        'risk_id' => $risk->id,
                                        'effect_id' => $new_effect->id,
                                        ]);
                            }
                            else
                            {
                                if (isset($_POST['effect_id']))
                                {
                                    foreach ($_POST['effect_id'] as $effect_id)
                                    {
                                        //insertamos cada causa en cause_risk
                                        DB::table('effect_risk')
                                            ->insert([
                                                'risk_id' => $risk->id,
                                                'effect_id' => $effect_id
                                                ]);
                                    }
                                } 
                            }

                        //agregamos en tabla risk_subprocess o objective_risk
                        //obtenemos id de riesgo recien ingresado
                        //$risk = $risk->id;

                        if ($type == 0)
                        {        
                            //agregamos en tabla risk_subprocess

                            foreach ($_POST['subprocess_id'] as $subprocess_id)
                            {
                                $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                                $subprocess->risks()->attach($risk->id);
                            }       
                        }

                        else if ($type == 1)
                        {
                            //agregamos en tabla objective_risk

                            foreach ($_POST['objective_id'] as $objective_id)
                            {
                                $objective = \Ermtool\Objective::find($objective_id);
                                $objective->risks()->attach($risk->id);
                            }       
                        }

                        //ACTUALIZACIÓN 29-03-17: Agregamos en tabla organization_risk
                        //ACTUALIZACIÓN 16-08-17: Agregamos aquí también stakeholder_id
                        //ACTUALIZACIÓN 08-01-17: Agregamos aquí también comentarios
                        //$organization = \Ermtool\Organization::find($_POST['org_id']);
                        //$organization->risks()->attach($risk->id);

                        \Ermtool\Risk::insertOrganizationRisk($_POST['org_id'],$risk->id,$stake,$comments2);

                        //ACTUALIZACIÓN 16-08-17: Agregamos riesgo y subproceso para otras organizaciones
                        if (isset($_POST['organization_id']) && $_POST['organization_id'] != "")
                        {
                            foreach ($_POST['organization_id'] as $org)
                            {
                                foreach($_POST['subprocesses_'.$org] as $sub)
                                {
                                    //primero verificamos que el subproceso no exista previamente
                                    $risk_subprocess = DB::table('risk_subprocess')
                                                        ->where('risk_id','=',$risk->id)
                                                        ->where('subprocess_id','=',$sub)
                                                        ->get(['id']);

                                    if (empty($risk_subprocess) || $risk_subprocess == null) //agregamos el risk_subprocess
                                    {
                                        $subprocess = \Ermtool\Subprocess::find($sub);
                                        $subprocess->risks()->attach($risk->id); 
                                    } 
                                }

                                //ahora agregamos organization_risk con responsable (si es que se agregó)
                                if (isset($_POST['stakeholder_'.$org]) && $_POST['stakeholder_'.$org] != '' && !empty($_POST['stakeholder_'.$org]))
                                {
                                    \Ermtool\Risk::insertOrganizationRisk($org,$risk->id,$_POST['stakeholder_'.$org],null);
                                }
                                else
                                {
                                    \Ermtool\Risk::insertOrganizationRisk($org,$risk->id,null,null);
                                }
                            }
                        }

                        //guardamos archivos de evidencias (si es que hay)
                        if($GLOBALS['evidence'] != NULL)
                        {
                            foreach ($GLOBALS['evidence'] as $evidence)
                            {
                                if ($evidence != NULL)
                                {
                                    upload_file($evidence,'riesgos',$risk->id);
                                }
                            }                    
                        }

                        //ACT 08-01-18: Agregamos materialidad
                        if (isset($_POST['impact']) && $_POST['impact'] != "")
                        {
                            if (isset($_POST['probability']) && $_POST['probability'] != "")
                            {
                                //obtenemos org_risk_id
                                $org_risk = \Ermtool\Risk::getOrganizationRisk($_POST['org_id'],$risk->id);

                                DB::table('materiality')
                                    ->insert([
                                        'impact' => $_POST['impact'],
                                        'probability' => $_POST['probability'],
                                        'kind' => $_POST['kind2'],
                                        'calification' => $_POST['calification2'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'organization_risk_id' => $org_risk->id
                                    ]);
                            }
                        }



                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Risk successfully created');
                        }
                        else
                        {
                            Session::flash('message','Riesgo agregado correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el riesgo con Id: '.$risk->id.' llamado: '.$risk->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('riesgos.index2?organization_id='.$_POST['org_id']);
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    //setea datos de un riesgo tipo cuando se está identificando un riesgo
    public function setRiesgoTipo($id)
    {
        try
        {
            $riesgo = \Ermtool\Risk::find($id);

            //obtenemos causas y efectos de riesgo tipo
            $causes = $riesgo->causes;
            $effects = $riesgo->effects;

            $datos = ['name'=>$riesgo['name'],'description'=>$riesgo['description'],
                        'risk_category_id'=>$riesgo['risk_category_id'],
                        'expiration_date'=>$riesgo['expiration_date'],
                        'causes'=>$causes,'effects'=>$effects];

            return json_encode($datos);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //try
        //{
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //obtenemos riesgo
                $risk = \Ermtool\Risk::find($id);
                if ($risk->type == 0) //es de proceso
                {
                    //ACTUALIZACIÓN 16-19-17: Vemos si se seleccionó organización o se editará el riesgo en general
                    if (isset($_GET['org']) && $_GET['org'] != NULL)
                    {
                        //ACT 07-03-18: También seleccionamos proceso (para casos como Emaresa donde existen subprocesos con el mismo nombre para distinto proceso)
                        $subprocesos = DB::table('subprocesses')
                                    ->join('processes','processes.id','=','subprocesses.process_id')
                                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                    ->where('organization_subprocess.organization_id','=',$_GET['org'])
                                    ->where('subprocesses.status','=',0)
                                    ->select('processes.name as process','subprocesses.name','subprocesses.id')
                                    ->get();

                        $sub_selected = array();
                        $subs = DB::table('risk_subprocess')
                                    ->where('risk_subprocess.risk_id','=',$id)
                                    ->select('risk_subprocess.subprocess_id')
                                    ->get();

                        $i = 0;
                        foreach ($subs as $sub)
                        {
                            $sub_selected[$i] = $sub->subprocess_id;
                            $i += 1;
                        }
                    }
                }
                else if ($risk->type == 1) //es de negocio
                {
                    //ACTUALIZACIÓN 16-19-17: Vemos si se seleccionó organización o se editará el riesgo en general
                    if (isset($_GET['org']) && $_GET['org'] != NULL)
                    {
                        $objectives = DB::table('objectives')
                                        ->where('organization_id','=',$_GET['org'])
                                        ->where('status','=',0)
                                        ->lists('name','id');

                        $obj_selected = array();
                        $objs = DB::table('objective_risk')
                                    ->where('objective_risk.risk_id','=',$id)
                                    ->select('objective_risk.objective_id')
                                    ->get();

                        $i = 0;
                        foreach ($objs as $obj)
                        {
                            $obj_selected[$i] = $obj->objective_id;
                            $i += 1;
                        }
                    }
                }

                //categorias de riesgo
                $categorias = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');
                //causas
                $causas = \Ermtool\Cause::where('status',0)->select('name','id','description')->get();
                //efectos
                $efectos = \Ermtool\Effect::where('status',0)->select('name','id','description')->get();
                $causes_selected = array();
                $effects_selected = array();
                //obtenemos causas seleccionadas
                $causes = DB::table('cause_risk')
                                    ->where('risk_id','=',$id)
                                    ->select('cause_risk.cause_id')
                                    ->get();

                $i = 0;
                foreach ($causes as $cause)
                {
                    $causes_selected[$i] = $cause->cause_id;
                    $i += 1;
                }

                //obtenemos efectos seleccionados
                $effects = DB::table('effect_risk')
                                ->where('risk_id','=',$id)
                                ->select('effect_risk.effect_id')
                                ->get();

                $i = 0;
                foreach ($effects as $effect)
                {
                    $effects_selected[$i] = $effect->effect_id;
                    $i += 1;
                }

                //ACT 28-12-17: obtenemos categoría principal (2 en caso que hayan 3 niveles) y seteamos distintos niveles
                if ($risk->risk_category_id != NULL)
                {
                    $risk_category1 = DB::table('risk_categories')
                                    ->where('id','=',$risk->risk_category_id)
                                    ->select('risk_category_id as id')
                                    ->first();

                    if ($risk_category1->id != NULL)
                    {
                       //Ahora obtenemos próxima categoría principal (si es que hay)
                        $risk_category0 = DB::table('risk_categories')
                                        ->where('id','=',$risk_category1->id)
                                        ->select('risk_category_id as id')
                                        ->first();

                        if ($risk_category0->id == NULL) //seteamos 3 niveles (con último vacío)
                        {
                            $risk_category0 = $risk_category1->id;
                            $risk_category1 = $risk->risk_category_id;
                            $risk_category2 = NULL;
                        }
                        else
                        {
                            $risk_category0 = $risk_category0->id;
                            $risk_category1 = $risk_category1->id;
                            $risk_category2 = $risk->risk_category_id;
                        } 
                    }
                    else //sólo hay una categoría
                    {
                        $risk_category0 = $risk->risk_category_id;
                        $risk_category1 = NULL;
                        $risk_category2 = NULL;
                    }
                    
                }
                else
                {
                    $risk_category0 = NULL;
                    $risk_category1 = NULL;
                    $risk_category2 = NULL;
                }
                
                //riesgos tipo
                $riesgos_tipo = \Ermtool\Risk::where('status',0)->where('type2',0)->lists('name','id');

                if (isset($_GET['org']) && $_GET['org'] != NULL)
                {
                    //obtenemos lista de stakeholders
                    $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);

                    //ACT 17-08-17: Obtenemos stakeholder_id de organization_risk
                    $stakeholder = \Ermtool\Stakeholder::getRiskStakeholder($_GET['org'],$risk->id);

                    //ACT 08-01-17: Agregamos comentarios específicos de la organización, ebt
                    $comments2 = DB::table('organization_risk')
                                    ->where('organization_id','=',$_GET['org'])
                                    ->where('risk_id','=',$risk->id)
                                    ->select('comments')
                                    ->first();

                    $comments2 = $comments2->comments;

                    $ebt = \Ermtool\Organization::getEBT($_GET['org'],1);

                    if ($ebt->ebt == NULL)
                    {
                        $ebt = \Ermtool\Organization::getEBT($_GET['org'],2);

                        //si org principal no tiene EBT
                        if ($ebt->ebt == NULL)
                        {
                            $ebt = array();
                        }
                    }

                    //obtenemos último impacto y probabilidad (materialidad)
                    $last_m = \Ermtool\Risk::getLastMateriality($_GET['org'],$risk->id);
                }
                else
                {
                    $last_m = NULL;
                    $ebt = NULL;
                    $comments2 = NULL;
                }
                //ACTUALIZACIÓN 29-03-17: SELECCIONAMOS SI SE DESEA AGREGAR A OTRAS ORGANIZACIONES
                //ACT 17-08-17: No lo haremos al editar 
                //ACT 16-10-17: Si lo haremos al editar
                $organizations = \Ermtool\Organization::organizationsWithoutRisk($id);

                

                //ACTUALIZACIÓN 04-01-18: Agregamos tipos de moneda para materialidad
                $kinds = ['1'=>'Peso','2'=>'Dólar','3'=>'Euro','4'=>'UF']; 

                if (Session::get('languaje') == 'en')
                {
                    if (isset($_GET['org']) && $_GET['org'] != NULL)
                    {
                        if ($risk->type == 0)
                        {
                            return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders, 'stakeholder' => $stakeholder,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'subprocesos' => $subprocesos,'sub_selected' => $sub_selected,'org_id' => $_GET['org'],'organizations' => $organizations,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                        else
                        {
                            return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders, 'stakeholder' => $stakeholder,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'objetivos' => $objectives,'obj_selected' => $obj_selected,'org_id' => $_GET['org'],'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                    }
                    else
                    {
                        if ($risk->type == 0)
                        {
                            return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                        else
                        {
                            return view('en.riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                    }
                }
                else
                {
                    if (isset($_GET['org']) && $_GET['org'] != NULL)
                    {
                        if ($risk->type == 0)
                        {
                            return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders, 'stakeholder' => $stakeholder,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'subprocesos' => $subprocesos,'sub_selected' => $sub_selected,'org_id' => $_GET['org'],'organizations' => $organizations,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                        else
                        {
                            return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'stakeholders' => $stakeholders, 'stakeholder' => $stakeholder,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'objetivos' => $objectives,'obj_selected' => $obj_selected,'org_id' => $_GET['org'],'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                    }
                    else
                    {
                        if ($risk->type == 0)
                        {
                            return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                        else
                        {
                            return view('riesgos.edit',['risk'=>$risk,'riesgos_tipo'=>$riesgos_tipo,'causas'=>$causas,'categorias'=>$categorias,'efectos'=>$efectos,'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected,'risk_category0' => $risk_category0,'risk_category1' => $risk_category1,'risk_category2' => $risk_category2,'last_m' => $last_m,'ebt'=>$ebt,'comments2'=>$comments2,'kinds' => $kinds]);
                        }
                    }
                }
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
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
                global $evidence;
                $evidence = $request->file('evidence_doc');
                //creamos una transacción para cumplir con atomicidad
                DB::transaction(function()
                {
                    $logger = $this->logger;
                        $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
                            
                        //vemos si se agrego alguna causa nueva
                        if (isset($_POST['causa_nueva']))
                        {

                            $new_causa = \Ermtool\Cause::create([
                                'name'=>$_POST['causa_nueva']
                            ]);

                            //guardamos en cause_risk
                            DB::table('cause_risk')
                                ->insert([
                                    'risk_id' => $riesgo->id,
                                    'cause_id' => $new_causa->id,
                                    ]);
                        }
                        else //se están agregando causas ya creadas
                        {
                            if (isset($_POST['cause_id']))
                            {
                                foreach ($_POST['cause_id'] as $cause_id)
                                {
                                    //primero buscamos si es que existe previamente
                                    $cause = DB::table('cause_risk')
                                        ->where('cause_id','=',$cause_id)
                                        ->where('risk_id','=',$riesgo->id)
                                        ->first();

                                    if (!$cause) //no existe, por lo que se agrega
                                    {
                                        DB::table('cause_risk')
                                        ->insert([
                                            'risk_id' => $riesgo->id,
                                            'cause_id' => $cause_id
                                            ]);
                                    }
                                }
                            } 
                        }

                        //vemos si se agrego algún efecto nuevo
                        if (isset($_POST['efecto_nuevo']))
                        {

                            $new_effect = \Ermtool\Effect::create([
                                'name'=>$_POST['efecto_nuevo']
                                ]);

                             //guardamos en cause_risk
                            DB::table('effect_risk')
                                ->insert([
                                    'risk_id' => $riesgo->id,
                                    'effect_id' => $new_effect->id,
                                    ]);
                        }
                        else //efectos existentes
                        {
                            if (isset($_POST['effect_id']))
                            {
                                foreach ($_POST['effect_id'] as $effect_id)
                                {
                                    //primero buscamos si es que existe previamente
                                    $effect = DB::table('effect_risk')
                                        ->where('effect_id','=',$effect_id)
                                        ->where('risk_id','=',$riesgo->id)
                                        ->first();

                                    if (!$effect) //no existe, por lo que se agrega
                                    {
                                        //insertamos cada causa en cause_risk
                                        DB::table('effect_risk')
                                            ->insert([
                                                'risk_id' => $riesgo->id,
                                                'effect_id' => $effect_id
                                                ]);
                                    }
                                }
                            } 
                        }

                        //ahora recorreremos todas las causas y efectos de este riesgo, para saber si es que no se borró alguna
                        $causas = DB::table('cause_risk')
                                    ->where('risk_id','=',$riesgo->id)
                                    ->select('cause_id')
                                    ->get();

                        //ACTUALIZACIÓN 28-07-17: Hay que verificar que se estén seleccionando causas
                        if (isset($_POST['cause_id']))
                        {
                            foreach($causas as $cause)
                            {
                                $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                                //ahora recorremos todas las causas que se agregaron para comparar
                                foreach ($_POST['cause_id'] as $cause_add)
                                {
                                    if ($cause_add == $cause->cause_id)
                                    {
                                        $cont += 1;
                                    }
                                }

                                if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                                {
                                    DB::table('cause_risk')
                                        ->where('risk_id','=',$riesgo->id)
                                        ->where('cause_id','=',$cause->cause_id)
                                        ->delete();
                                }
                            }
                        }            

                        //lo mismo ahora para efectos
                        $efectos = DB::table('effect_risk')
                                    ->where('risk_id','=',$riesgo->id)
                                    ->select('effect_id')
                                    ->get();

                        //ACTUALIZACIÓN 28-07-17: Hay que verificar que se estén seleccionando efectos
                        if (isset($_POST['effect_id']))
                        {
                            foreach($efectos as $effect)
                            {
                                $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                                //ahora recorremos todas las causas que se agregaron para comparar
                                foreach ($_POST['effect_id'] as $effect_add)
                                {
                                    if ($effect_add == $effect->effect_id)
                                    {
                                        $cont += 1;
                                    }
                                }

                                if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                                {
                                    DB::table('effect_risk')
                                        ->where('risk_id','=',$riesgo->id)
                                        ->where('effect_id','=',$effect->effect_id)
                                        ->delete();
                                }
                            }
                        }

                        if (!isset($_POST['stakeholder_id']) || $_POST['stakeholder_id'] == "")
                        {
                            $stake = NULL;
                        }
                        else
                        {
                            $stake = $_POST['stakeholder_id'];
                        }

                        if (isset($_POST['subprocess_id']))
                        {
                            if ($riesgo->type == 0)
                            {
                                //primero eliminamos relaciones previas
                                //ACTUALIZACIÓN 02-03-2017: NO SE PUEDE ELIMINAR!!! YA QUE PUEDE TENER COSAS ASOCIADAS
                                //DB::table('risk_subprocess')
                                //    ->where('risk_id','=',$riesgo->id)
                                //    ->delete();

                                //agregamos en tabla risk_subprocess
                                foreach ($_POST['subprocess_id'] as $subprocess_id)
                                {
                                    //vemos si el subproceso ya se encuentra para el riesgo
                                    $risk_sub = DB::table('risk_subprocess')
                                                ->where('risk_id','=',$riesgo->id)
                                                ->where('subprocess_id','=',$subprocess_id)
                                                ->get(['id']);

                                    if (empty($risk_sub)) //si es que está vacío significa que la relación no existe
                                    {
                                        $subprocess = \Ermtool\Subprocess::find($subprocess_id);
                                        $subprocess->risks()->attach($riesgo->id);
                                    }
                                }       
                            }
                        }
                        
                        if (isset($_POST['objective_id']))
                        {
                            if ($riesgo->type == 1)
                            {
                                //primero eliminamos relaciones previas
                                //ACTUALIZACIÓN 02-03-2017: NO SE PUEDE ELIMINAR!!! YA QUE PUEDE TENER COSAS ASOCIADAS
                                //DB::table('objective_risk')
                                //    ->where('risk_id','=',$riesgo->id)
                                //    ->delete();

                                //agregamos en tabla objective_risk
                                foreach ($_POST['objective_id'] as $objective_id)
                                {
                                    //vemos si el objetivo ya se encuentra para el riesgo
                                    $obj_risk = DB::table('objective_risk')
                                                ->where('risk_id','=',$riesgo->id)
                                                ->where('objective_id','=',$objective_id)
                                                ->get(['id']);

                                    if (empty($obj_risk)) //si es que está vacío significa que la relación no existe
                                    {
                                        $objective = \Ermtool\Objective::find($objective_id);
                                        $objective->risks()->attach($riesgo->id);
                                    }
                                    
                                }       
                            }
                        }
                        
                        //ahora para cada posible organización agregada
                        /* ACTUALIZACIÓN 17-08-17: No agregaremos organizaciones al editar
                        //ACT 16-10-17: Si agregaremos organizaciones al editar */
                        if (isset($_POST['organization_id']) && $_POST['organization_id'] != "")
                        {

                            foreach ($_POST['organization_id'] as $org)
                            {
                                //ACTUALIZACIÓN 17-08-17: Debemos verificar que se ingrese al menos un subproceso
                                if (isset($_POST['subprocesses_'.$org]))
                                {
                                    foreach($_POST['subprocesses_'.$org] as $sub)
                                    {
                                        //primero verificamos que el subproceso no exista previamente
                                        $risk_subprocess = DB::table('risk_subprocess')
                                                            ->where('risk_id','=',$riesgo->id)
                                                            ->where('subprocess_id','=',$sub)
                                                            ->get(['id']);

                                        if (empty($risk_subprocess) || $risk_subprocess == null) //agregamos el risk_subprocess
                                        {
                                            $subprocess = \Ermtool\Subprocess::find($sub);
                                            $subprocess->risks()->attach($riesgo->id); 
                                        } 
                                    }

                                    //ahora agregamos organization_risk con responsable (si es que se agregó)
                                    if (isset($_POST['stakeholder_'.$org]) && $_POST['stakeholder_'.$org] != '' && !empty($_POST['stakeholder_'.$org]))
                                    {
                                        \Ermtool\Risk::insertOrganizationRisk($org,$riesgo->id,$_POST['stakeholder_'.$org],null);
                                    }
                                    else
                                    {
                                        \Ermtool\Risk::insertOrganizationRisk($org,$riesgo->id,null,null);
                                    }
                                }
                            }
                        } 

                        if($GLOBALS['evidence'] != NULL)
                        {
                            foreach ($GLOBALS['evidence'] as $evidence)
                            {
                                if ($evidence != NULL)
                                {
                                    upload_file($evidence,'riesgos',$GLOBALS['id1']);
                                }
                            }                    
                        }

                        if (!isset($_POST['description']) || $_POST['description'] == "")
                        {
                            $description = NULL;
                        }
                        else
                        {
                            $description = $_POST['description'];
                        }

                        //ACTUALIZACIÓN 18-08-17: Vemos si se agregó subcategoría
                        if (isset($_POST['risk_subcategory_id']) && $_POST['risk_subcategory_id'] != '')
                        {
                            $risk_category_id = $_POST['risk_subcategory_id'];
                        }
                        else
                        {
                            if (!isset($_POST['risk_category_id']) || $_POST['risk_category_id'] == "")
                            {
                                $risk_category_id = NULL;
                            }
                            else
                            {
                                $risk_category_id = $_POST['risk_category_id'];
                            }
                        }

                        if (!isset($_POST['expected_loss']) || $_POST['expected_loss'] == "")
                        {
                            $expected_loss = NULL;
                        }
                        else
                        {
                            $expected_loss = $_POST['expected_loss'];
                        }
                        if (!isset($_POST['expiration_date']) || $_POST['expiration_date'] == "")
                        {
                            $expiration_date = NULL;
                        }
                        else
                        {
                            $expiration_date = $_POST['expiration_date'];
                        }

                        //ACTUALIZACIÓN 26-08-17: Agregado comentarios o posibles comentarios al crear riesgo
                        if (!isset($_POST['comments']) || $_POST['comments'] == "")
                        {
                            $comments = NULL;
                        }
                        else
                        {
                            $comments = $_POST['comments'];
                            $comments = eliminarSaltos($comments);
                        }

                        $description = eliminarSaltos($description);
                        
                        $riesgo->name = $_POST['name'];
                        $riesgo->description = $description;
                        $riesgo->expiration_date = $expiration_date;
                        $riesgo->type2 = 1;
                        $riesgo->risk_category_id = $risk_category_id;
                        $riesgo->expected_loss = $expected_loss;
                        $riesgo->comments = $comments;
                        //$riesgo->stakeholder_id = $stake;

                        //ACTUALIZACIÓN 16-10-17: Si es que no se agrega org_id es porque se está modificando el riesgo en general
                        if (isset($_POST['org_id']))
                        {
                            //ACTUALIZACIÓN 17-08-17: Actualizamos stakeholder en organization_risk
                            DB::table('organization_risk')
                                ->where('organization_id','=',$_POST['org_id'])
                                ->where('risk_id','=',$riesgo->id)
                                ->update([
                                    'stakeholder_id' => $stake
                                    ]);
                        }

                        //ACT 08-01-18: Agregamos materialidad
                        if (isset($_POST['impact']) && $_POST['impact'] != "")
                        {
                            if (isset($_POST['probability']) && $_POST['probability'] != "")
                            {
                                //obtenemos org_risk_id
                                $org_risk = \Ermtool\Risk::getOrganizationRisk($_POST['org_id'],$riesgo->id);

                                DB::table('materiality')
                                    ->insert([
                                        'impact' => $_POST['impact'],
                                        'probability' => $_POST['probability'],
                                        'kind' => $_POST['kind2'],
                                        'calification' => $_POST['calification2'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'organization_risk_id' => $org_risk->id
                                    ]);
                            }
                        }
                        
                        $riesgo->save();

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Risk successfully updated');
                        }
                        else
                        {
                            Session::flash('message','Riesgo actualizado correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el riesgo con Id: '.$riesgo->id.' llamado: '.$riesgo->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                
                if (isset($_POST['org_id']))
                {
                    return Redirect::to('riesgos.index2?organization_id='.$_POST['org_id']);
                }
                else
                {
                    return Redirect::to('riesgos');
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //matriz de riesgos
    public function matrices()
    {
        //try
        //{
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                //ACTUALIZACIÓN 02-03-17: Agregamos filtro de categorías de riesgos (todas las categorías)
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                //ACT 02-01-18: Realizamos vista consolidada (para todas las organizaciones)
                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //si no se está generando excel
                {
                    $value = NULL;
                    $org_temp = NULL;

                    //ACTUALIZACIÓN 21-08-17: Vemos si hay subcategoría
                    if (isset($_GET['risk_subcategory_id']) && $_GET['risk_subcategory_id'] != '')
                    {
                        $category = $_GET['risk_subcategory_id'];
                    }

                    else if (isset($_GET['risk_category_id']) && $_GET['risk_category_id'] != '')
                    {
                        $category = $_GET['risk_category_id'];   
                    }
                    else
                    {
                        $category = NULL;
                    }
                }
                //por ahora si es excel se deja como NULL la categoría (02-03-17)
                //ACT 27-12-17: Ahora estamos agregando categoría 
                else 
                {
                    if ($cat == NULL)
                    {
                        $category = NULL;
                    }
                    else
                    {
                        $category = (int)$cat;
                    }
                    
                    $org_temp = NULL;
                }
    
                $i = 0;
                $datos = array();

                $datos = $this->generateRiskMatrix($org_temp,$category,$value);

                if (Session::get('languaje') == 'en')
                {
                    //return view('en.reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('en.reportes.matriz_riesgos',['datos'=>$datos,'organizations'=>$organizations,'categories' => $categories]);
                }
                else
                {
                    //return view('reportes.matriz_riesgos',['organizations'=>$organizations,'categories' => $categories]);
                    return view('reportes.matriz_riesgos',['datos'=>$datos,'organizations'=>$organizations,'categories' => $categories]);
                }
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    public function generarMatriz($value,$org,$cat)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                //ACTUALIZACIÓN 02-03-17: Agregamos filtro de categorías de riesgos (todas las categorías)
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //si no se está generando excel
                {
                    $value = $_GET['kind'];
                    $org = $_GET['organization_id'];

                    //ACTUALIZACIÓN 21-08-17: Vemos si hay subcategoría
                    if (isset($_GET['risk_subcategory_id']) && $_GET['risk_subcategory_id'] != '')
                    {
                        $category = $_GET['risk_subcategory_id'];
                    }

                    else if (isset($_GET['risk_category_id']) && $_GET['risk_category_id'] != '')
                    {
                        $category = $_GET['risk_category_id'];   
                    }
                    else
                    {
                        $category = NULL;
                    }
                }
                //por ahora si es excel se deja como NULL la categoría (02-03-17)
                //ACT 27-12-17: Ahora estamos agregando categoría 
                else 
                {
                    if ($cat == NULL)
                    {
                        $category = NULL;
                    }
                    else
                    {
                        $category = (int)$cat;
                    }
                    
                    if ($org == NULL)
                    {
                        $org = NULL;
                    }
                    else
                    {
                        $org = (int)$org;
                    }
                }
                
                $i = 0;
                $datos = array();

                $datos = $this->generateRiskMatrix($org,$category,$value);

                if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
                {
                    return $datos;
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.reportes.matriz_riesgos',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org,'categories' => $categories]);
                    }
                    else
                    {
                        return view('reportes.matriz_riesgos',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org,'categories' => $categories]);
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

    //ACT 02-01-18: Función para generar matriz de riesgos
    public function generateRiskMatrix($org,$category,$value)
    {
        $i = 0;
        $datos = array();
                if (Session::get('languaje') == 'en')
                {
                    $proba_string = ['Very low','Low','Medium','High','Very high'];
                    $impact_string = ['Very low','Low','Medium','High','Very high'];
                }
                else
                {
                    $proba_string = ['Muy poco probable','Poco probable','Intermedio','Probable','Muy probable'];
                    $impact_string = ['Muy poco impacto','Poco impacto','Intermedio','Alto impacto','Muy alto impacto'];
                }
                
                //ACT 04-04-17: Obtenemos riesgos según tipo (value)
                
                $risks = \Ermtool\Risk::getRisksWithType($org,$category,$value);
            
                foreach ($risks as $risk)
                {       
                        if (Session::get('languaje') == 'en')
                        {
                            $probabilidad = "No evaluation";
                            $impacto = "No evaluation";
                            $score = "No evaluation";
                        }
                        else
                        {
                            $probabilidad = "No tiene evaluación";
                            $impacto = "No tiene evaluación";
                            $score = "No tiene evaluación";
                        }
                        // -- seteamos datos --//
                        //seteamos causa y efecto

                        if ($risk->type == 0) //obtenemos procesos y subprocesos
                        {
                            $processes = \Ermtool\Process::getProcessesFromRisk($org,$risk->id);

                            $subprocesses = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($risk->id,$org);

                            if (!empty($processes))
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                                {
                                    $procesos = array();
                                    $j = 0;
                                }
                                else
                                {
                                    $procesos = '';
                                }

                                $last = end($processes);
                                foreach ($processes as $p)
                                {
                                    if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $procesos[$j] = $p->name;
                                        $j+=1;
                                    }
                                    else
                                    {
                                        if ($last == $p)
                                        {
                                            $procesos .= $p->name;
                                        }
                                        else
                                        {
                                            $procesos .= $p->name.', ';
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $procesos = array();
                            }
                            if (!empty($subprocesses))
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                                {
                                    $subprocesos = array();
                                    $j = 0;
                                }
                                else
                                {
                                    $subprocesos = '';
                                }

                                $last = end($subprocesses);
                                foreach ($subprocesses as $s)
                                {
                                    if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $subprocesos[$j] = $s->name;
                                        $j+=1;
                                    }
                                    else
                                    {
                                        if ($last == $s)
                                        {
                                            $subprocesos .= $s->name;
                                        }
                                        else
                                        {
                                            $subprocesos .= $s->name.', ';
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $subprocesos = array();
                            }
                        }
                        else if ($risk->type == 1) //obtenemos objetivos
                        {
                            $objectives = \Ermtool\Objective::getObjectivesFromOrgRisk($risk->id,$org);

                            if (!empty($objectives))
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                                {
                                    $objetivos = array();
                                    $j = 0;
                                }
                                else
                                {
                                    $objetivos = '';
                                }

                                $last = end($objectives);
                                foreach ($objectives as $o)
                                {
                                    if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $objetivos[$j] = $o->name;
                                        $j+=1;
                                    }
                                    else
                                    {
                                        if ($last == $o)
                                        {
                                            $objetivos .= $o->name;
                                        }
                                        else
                                        {
                                            $objetivos .= $o->name.', ';
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $objetivos = array();
                            }
                            
                        }
                        //obtenemos causas
                        $causes = \Ermtool\Cause::getCausesFromRisk($risk->id);

                        if (!empty($causes))
                        {
                            if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                            {
                                $causas = array();
                                $j = 0;
                            }
                            else
                            {
                                $causas = '';
                            }

                            $last = end($causes); //guardamos final para no agregarle coma
                            foreach ($causes as $cause)
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                {
                                    $causas[$j] = $cause->name;
                                    $j+=1;
                                }
                                else
                                {
                                    if ($cause != $last)
                                        $causas .= $cause->name.', ';
                                    else
                                        $causas .= $cause->name;
                                }  
                            }
                        }
                        else
                        {
                            $causas = array();
                        }

                        //obtenemos efectos
                        $effects = \Ermtool\Effect::getEffectsFromRisk($risk->id);


                        if (!empty($effects))
                        {
                            if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                            {
                                $efectos = array();
                                $j = 0;
                            }
                            else
                            {
                                $efectos = '';
                            }

                            $last = end($effects); //guardamos final para no agregarle coma
                            foreach ($effects as $effect)
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                {
                                    $efectos[$j] = $effect->name;
                                    $j+=1;
                                }
                                else
                                {
                                    if ($effect != $last)
                                        $efectos .= $effect->name.', ';
                                    else
                                        $efectos .= $effect->name;
                                }
                            }
                        }
                        else
                        {
                            $efectos = array();
                        }
                        //primero obtenemos maxima fecha de evaluacion para el riesgo
                        $fecha = DB::table('evaluation_risk')
                                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                        ->where('evaluation_risk.organization_risk_id','=',$risk->id)
                                        ->max('evaluations.updated_at');

                        //ACT 04-04-17: Sacamos guiones para SQL Server
                        //$fecha = str_replace('-','',$fecha);

                        //obtenemos proba, impacto y score
                        $eval_risk = DB::table('evaluation_risk')
                                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                        ->where('evaluation_risk.organization_risk_id','=',$risk->id)
                                        ->where('evaluations.updated_at','=',$fecha)
                                        ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                        ->get();

                            foreach ($eval_risk as $eval)
                            {
                                if ($eval->avg_probability != NULL AND $eval->avg_impact != NULL)
                                {
                                    $impacto = $eval->avg_impact.' ('.$impact_string[$eval->avg_impact-1].')';
                                    $probabilidad = $eval->avg_probability.' ('.$proba_string[$eval->avg_probability-1].')';
                                    $score = $impacto * $probabilidad;
                                }
                            }
                            //obtenemos controles
                            $controls = \Ermtool\Control::getControlsFromRisk($org,$risk->id);

                        //seteamos controles
                        if ($controls == NULL || empty($controls))
                        {
                            $controles = array();
                        }
                        else
                        {
                            if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //ver como mostrar datos
                            {
                                $controles = array();
                                $j = 0;
                            }
                            else
                            {
                                $controles = '';
                            }
                            $last = end($controls); //guardamos final para no agregarle coma
                            foreach ($controls as $control)
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                {
                                    $controles[$j] = $control->name;
                                    $j+=1;
                                }
                                else
                                {
                                    if ($control != $last)
                                        $controles .= $control->name.', ';
                                    else
                                        $controles .= $control->name; 
                                }
                                
                            }
                        }

                        //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                        if ($risk->created_at == NULL OR $risk->created_at == "0000-00-00" OR $risk->created_at == "")
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $fecha_creacion = "Failed to register creation date";
                            }
                            else
                            {
                                $fecha_creacion = "Error al registrar fecha de creación";
                            }
                        }

                        else
                        {
                            //primero sacamos la hora
                            $fecha_temp1 = explode(' ',$risk->created_at);

                            //sacamos solo fecha y ordenamos
                            $fecha_temp2 = explode('-',$fecha_temp1[0]);

                            //ponemos fecha
                            $fecha_creacion = $fecha_temp2[2].'-'.$fecha_temp2[1].'-'.$fecha_temp2[0].' a las '.$fecha_temp1[1];
                        }

                        //damos formato a fecha expiración
                        if ($risk->expiration_date == NULL OR $risk->expiration_date == "0000-00-00")
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $expiration_date = "None";
                            }
                            else
                            {
                                $expiration_date = "Ninguna";
                            }
                        }
                        else
                        { 
                            //sacamos solo fecha y ordenamos
                            $fecha_temp1 = explode('-',$risk->expiration_date);
                            $expiration_date = $fecha_temp1[2].'-'.$fecha_temp1[1].'-'.$fecha_temp1[0];
                        }

                        if ($risk->expected_loss == 0 || $risk->expected_loss == NULL)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $expected_loss = "Not assigned expected loss";
                            }
                            else
                            {
                                $expected_loss = "No se ha asignado pérdida esperada";
                            }
                        }
                        else
                        {
                            $expected_loss = $risk->expected_loss;
                        }

                        if ($risk->description == NULL || $risk->description == '')
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $description = 'Not assigned description';
                            }
                            else
                            {
                                $description = 'No se ha asignado descripción';
                            }
                        }
                        else
                        {
                            $description = $risk->description;
                        }

                        if ($risk->risk_category_id != NULL && $risk->risk_category_id != '')
                        {
                            $risk_category = \Ermtool\Risk_category::name($risk->risk_category_id);
                        } 
                        else
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $risk_category = 'Not defined';
                            }
                            else
                            {
                                $risk_category = 'No definido';
                            }
                        }
                        //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                        $short_des = substr($description,0,100);

                        if (isset($org) && $org != NULL)
                        {
                                $orgs_name = \Ermtool\Organization::name($org);
                        }
                        else
                        {
                            $orgs = \Ermtool\Organization::getOrganizationsFromRisk($risk->id);

                            $last = end($orgs);
                            $orgs_name = '';
                            foreach ($orgs as $org1)
                            {
                                if ($last == $org1)
                                {
                                    $orgs_name .= $org1->name;
                                }
                                else
                                {
                                    $orgs_name .= $org1->name.', ';
                                }
                            }
                        }

                        if ($org == NULL && strstr($_SERVER["REQUEST_URI"],'genexcel'))
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                if ($risk->type == 0)
                                {
                                    $type = 'Process';
                                    $temp = $procesos;
                                }
                                else
                                {
                                    $type = 'Bussiness';
                                    $temp = $objetivos;
                                }
                                $datos[$i] = [//'id' => $control->id,
                                        'Organizations' => $orgs_name,
                                        'Kind' => $type,
                                        'Process/Objective' => $temp,
                                        'Risk' => $risk->name,
                                        'Description' => $description,
                                        'Category' => $risk_category,
                                        'Causes' => $causas,
                                        'Effects' => $efectos,
                                        'Expected_loss' => $expected_loss,
                                        'Probability' => $probabilidad,
                                        'Impact' => $impacto,
                                        'Score' => $score,
                                        'Identification_date' => $fecha_creacion,
                                        'Expiration_date' => $expiration_date,
                                        'Controls' => $controles,];
                            }
                            else
                            {
                                if ($risk->type == 0)
                                {
                                    $type = 'De Proceso';
                                    $temp = $procesos;
                                }
                                else
                                {
                                    $type = 'De Negocio';
                                    $temp = $objetivos;
                                }

                                $datos[$i] = [//'id' => $control->id,
                                        'Organizaciones' => $orgs_name,
                                        'Tipo' => $type,
                                        'Procesos/Objetivo' => $temp,
                                        'Riesgo' => $risk->name,
                                        'Descripción' => $description,
                                        'Categoría' => $risk_category,
                                        'Causas' => $causas,
                                        'Efectos' => $efectos,
                                        'Pérdida_esperada' => $expected_loss,
                                        'Probabilidad' => $probabilidad,
                                        'Impacto' => $impacto,
                                        'Score' => $score,
                                        'Fecha_identificación' => $fecha_creacion,
                                        'Fecha_expiración' => $expiration_date,
                                        'Controles' => $controles,];
                            }
                            $i += 1;
                        }
                        else
                        {
                            //Seteamos datos
                            if ($risk->type == 0) //guardamos datos de riesgos de procesos
                            {
                                
                                if (Session::get('languaje') == 'en')
                                {
                                    if (strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $datos[$i] = [//'id' => $control->id,
                                                'Organizations' => $orgs_name,
                                                'Process' => $procesos,
                                                'Subprocess' => $subprocesos,
                                                'Risk' => $risk->name,
                                                'Description' => $description,
                                                'Category' => $risk_category,
                                                'Causes' => $causas,
                                                'Effects' => $efectos,
                                                'Expected_loss' => $expected_loss,
                                                'Probability' => $probabilidad,
                                                'Impact' => $impacto,
                                                'Score' => $score,
                                                'Identification_date' => $fecha_creacion,
                                                'Expiration_date' => $expiration_date,
                                                'Controls' => $controles,];
                                    }
                                    else
                                    {
                                        $datos[$i] = ['id' => $risk->id,
                                                'Organizations' => $orgs_name,
                                                'Process' => $procesos,
                                                'Subprocess' => $subprocesos,
                                                'Risk' => $risk->name,
                                                'Description' => $description,
                                                'Category' => $risk_category,
                                                'Causes' => $causas,
                                                'Effects' => $efectos,
                                                'Expected_loss' => $expected_loss,
                                                'Probability' => $probabilidad,
                                                'Impact' => $impacto,
                                                'Score' => $score,
                                                'Identification_date' => $fecha_creacion,
                                                'Expiration_date' => $expiration_date,
                                                'Controls' => $controles,
                                                'short_des' => $short_des,
                                                'type' => $risk->type];
                                    }
                                }
                                else
                                {
                                    if (strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $datos[$i] = [//'id' => $control->id,
                                                    'Organizaciones' => $orgs_name,
                                                    'Procesos' => $procesos,
                                                    'Subprocesos' => $subprocesos,
                                                    'Riesgo' => $risk->name,
                                                    'Descripción' => $description,
                                                    'Categoría' => $risk_category,
                                                    'Causas' => $causas,
                                                    'Efectos' => $efectos,
                                                    'Pérdida_esperada' => $expected_loss,
                                                    'Probabilidad' => $probabilidad,
                                                    'Impacto' => $impacto,
                                                    'Score' => $score,
                                                    'Fecha_identificación' => $fecha_creacion,
                                                    'Fecha_expiración' => $expiration_date,
                                                    'Controles' => $controles,];
                                    }
                                    else
                                    {
                                        $datos[$i] = ['id' => $risk->id,
                                                    'Organizaciones' => $orgs_name,
                                                    'Procesos' => $procesos,
                                                    'Subprocesos' => $subprocesos,
                                                    'Riesgo' => $risk->name,
                                                    'Descripción' => $description,
                                                    'Categoría' => $risk_category,
                                                    'Causas' => $causas,
                                                    'Efectos' => $efectos,
                                                    'Pérdida_esperada' => $expected_loss,
                                                    'Probabilidad' => $probabilidad,
                                                    'Impacto' => $impacto,
                                                    'Score' => $score,
                                                    'Fecha_identificación' => $fecha_creacion,
                                                    'Fecha_expiración' => $expiration_date,
                                                    'Controles' => $controles,
                                                    'short_des' => $short_des,
                                                    'type' => $risk->type];
                                    }
                                }
                                $i += 1;
                            }

                            else if ($risk->type == 1) //guardamos datos de riesgos de negocio
                            {
                                
                                if (Session::get('languaje') == 'en')
                                {
                                    if (strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $datos[$i] = [//'id' => $control->id,
                                                    'Organizations' => $orgs_name,
                                                    'Objective' => $objetivos,
                                                    'Risk' => $risk->name,
                                                    'Description' => $description,
                                                    'Category' => $risk_category,
                                                    'Causes' => $causas,
                                                    'Effects' => $efectos,              
                                                    'Expected_loss' => $risk->expected_loss,
                                                    'Probability' => $probabilidad,
                                                    'Impact' => $impacto,
                                                    'Score' => $score,
                                                    'Identification_date' => $fecha_creacion,
                                                    'Expiration_date' => $expiration_date,
                                                    'Controls' => $controles];
                                    }
                                    else
                                    {
                                        $datos[$i] = ['id' => $risk->id,
                                                    'Organizations' => $orgs_name,
                                                    'Objective' => $objetivos,
                                                    'Risk' => $risk->name,
                                                    'Description' => $description,
                                                    'Category' => $risk_category,
                                                    'Causes' => $causas,
                                                    'Effects' => $efectos,              
                                                    'Expected_loss' => $risk->expected_loss,
                                                    'Probability' => $probabilidad,
                                                    'Impact' => $impacto,
                                                    'Score' => $score,
                                                    'Identification_date' => $fecha_creacion,
                                                    'Expiration_date' => $expiration_date,
                                                    'Controls' => $controles,
                                                    'short_des' => $short_des,
                                                    'type' => $risk->type];
                                    }

                                }
                                else
                                {
                                    if (strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                    {
                                        $datos[$i] = [//'id' => $control->id,
                                                    'Organizaciones' => $orgs_name,
                                                    'Objetivos' => $objetivos,
                                                    'Riesgo' => $risk->name,
                                                    'Descripción' => $description,
                                                    'Categoría' => $risk_category,
                                                    'Causas' => $causas,
                                                    'Efectos' => $efectos,              
                                                    'Pérdida_esperada' => $risk->expected_loss,
                                                    'Probabilidad' => $probabilidad,
                                                    'Impacto' => $impacto,
                                                    'Score' => $score,
                                                    'Fecha_identificación' => $fecha_creacion,
                                                    'Fecha_expiración' => $expiration_date,
                                                    'Controles' => $controles];
                                    }
                                    else
                                    {
                                        $datos[$i] = ['id' => $risk->id,
                                                    'Organizaciones' => $orgs_name,
                                                    'Objetivos' => $objetivos,
                                                    'Riesgo' => $risk->name,
                                                    'Descripción' => $description,
                                                    'Categoría' => $risk_category,
                                                    'Causas' => $causas,
                                                    'Efectos' => $efectos,              
                                                    'Pérdida_esperada' => $risk->expected_loss,
                                                    'Probabilidad' => $probabilidad,
                                                    'Impacto' => $impacto,
                                                    'Score' => $score,
                                                    'Fecha_identificación' => $fecha_creacion,
                                                    'Fecha_expiración' => $expiration_date,
                                                    'Controles' => $controles,
                                                    'short_des' => $short_des,
                                                    'type' => $risk->type];
                                    }

                                }
                                $i += 1;
                            }
                        }
                        
                }

        return $datos;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$org_id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;

            global $org;


            $org = $org_id;


            DB::transaction(function() {
                $logger = $this->logger;
                $name = \Ermtool\Risk::name($GLOBALS['id1']);
                //ACTUALIZACIÓN 29-03-17: YA NO EXISTE CONTROL_OBJECTIVE_RISK O CONTROL_RISK_SUBPROCESS
                //CORRECCIÓN DISEÑO: SIEMPRE TENDRÁ O UN OBJECTIVE_RISK O UN RISK_SUBPROCESS
                //primero vemos si contiene algún objetivo asociado 
                
                //$rev = DB::table('action_plans')
                //    ->where('')
                //OJO: En evaluation_risk sólo se buscará en el campo risk_id, ya que después se revisará objective_risk o risk_subprocess

                    //revisamos objective_subprocess_risk (hay q que ver objective_risk y subprocess_risk)
                    $rev = DB::table('objective_subprocess_risk')
                        ->where('objective_risk_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                    if (empty($rev))
                    {
                        //ahora revisamos en la misma tabla subprocess_risk
                        $rev = DB::table('objective_subprocess_risk')
                            ->where('risk_subprocess_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                        if (empty($rev))
                        {
                            //KRI
                            $rev = DB::table('kri')
                                    ->where('kri.risk_id','=',$GLOBALS['id1'])
                                    ->select('id')
                                    ->get();

                            if (empty($rev))
                            {
                                    //ahora vemos verificamos las uniones con objective_risk o subprocess_risk
                                    //primero usaremos una variable local para verificar posteriormente
                                    //ACTUALIZACIÓN 29-03-17: SOLO VEREMOS ORGANIZATION_RISK por lo tanto obtenemos su id
                                $res2 = 1;

                                if ($GLOBALS['org'] == NULL)
                                {
                                }
                                else
                                {
                                    $risk = DB::table('organization_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->where('organization_id','=',$GLOBALS['org'])
                                        ->select('id')
                                        ->first();

                                            //foreach ($risks as $risk)
                                            //{
                                                //evaluation_risk
                                    $rev = DB::table('evaluation_risk')
                                                ->where('organization_risk_id','=',$risk->id)
                                                ->select('id')
                                                ->get();

                                    if (empty($rev))
                                    {
                                        $rev = DB::table('audit_plan_risk')
                                            ->where('organization_risk_id','=',$risk->id)
                                            ->select('id')
                                            ->get();

                                        if (empty($rev))
                                        {
                                            $rev = DB::table('audit_risk')
                                                    ->where('organization_risk_id','=',$risk->id)
                                                    ->select('id')
                                                    ->get();

                                            if (empty($rev))
                                            {
                                                //para verificar que sea parar todos los objective_risk

                                                $rev = DB::table('control_organization_risk')
                                                    ->where('organization_risk_id','=',$risk->id)
                                                    ->get();
                                                    
                                                if (empty($rev))
                                                {
                                                    //se puede borrar
                                                    $res2 = 0;
                                                }
                                                    
                                            }
                                            else
                                            {
                                                $res2 = 1;
                                            }
                                        }
                                        else
                                        {
                                            $res2 = 1;
                                        }
                                    }
                                    else
                                    {
                                        $res2 = 1;
                                    }
                                }
                                
                            }
                            else
                            {
                                $res2 = 1;
                            }
                                        //} 

                            if ($res2 == 0)
                            {
                                        //se puede borrar

                                        //eliminamos objective_risk(s) asociados (si es que hay)
                                        //ACTUALIZACIÓN 30-03-17: Sólo los objective_risk asociados al riesgo que se está borrando
                                        //ACTUALIZACIÓN 16-10-17: Si es que se está borrando todas (sin seleccionar organización), se eliminan todos los objetivos o subprocesos asociados

                                        if ($GLOBALS['org'] != NULL)
                                        {
                                            $objectives = DB::table('objectives')
                                                ->where('objectives.organization_id','=',$GLOBALS['org'])
                                                ->select('id')
                                                ->get();

                                            foreach ($objectives as $obj)
                                            {
                                                DB::table('objective_risk')
                                                    ->where('objective_risk.objective_id','=',$obj->id)
                                                    ->where('objective_risk.risk_id','=',$GLOBALS['id1'])
                                                    ->delete();
                                            }                                    

                                            //eliminamos de risk(s)_subprocess asociados (si es que hay)
                                            //ACTUALIZACIÓN 30-03-17: Por ahora los riesgos de proceso se eliminarán para todas las organizaciones, ya que un subproceso puede estar en muchas organizaciones

                                            

                                                    //ACT 29-06-17: Debemos eliminar en organization_risk, risk_subprocess y/o objective_risk
                                                    //$revX = DB::table('organization_risk')
                                                    //        ->where('risk_id','=',$GLOBALS['id1'])
                                                    //        ->get();
                                                    //Seleccionamos subprocesos u objetivos que sean de la organización y se encuentre el riesgo

                                            $risk_subprocesses = DB::table('risk_subprocess')
                                                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                                                            ->where('organization_subprocess.organization_id','=',$GLOBALS['org'])
                                                            ->where('risk_subprocess.risk_id','=',$GLOBALS['id1'])
                                                            ->select('risk_subprocess.id')
                                                            ->get();

                                            foreach ($risk_subprocesses as $r)
                                            {
                                                DB::table('risk_subprocess')        
                                                    ->where('risk_subprocess.id','=',$r->id)
                                                    ->delete();
                                            }

                                            DB::table('organization_risk')
                                                ->where('organization_id','=',$GLOBALS['org'])
                                                ->where('risk_id','=',$GLOBALS['id1'])
                                                ->delete();
                                        }
                                        
                                        //Ahora si podemos revisar si se puede eliminar lo demás (en caso de que el riesgo no exista para otras organizaciones)

                                        $revX = DB::table('organization_risk')
                                            ->where('risk_id','=',$GLOBALS['id1'])
                                            ->first();

                                        if (empty($revX))
                                        {
                                                    //Sólo de esta manera (vacío en todas partes), eliminamos el riesgo de la tabla risks

                                                    //eliminamos posibles causas asociadas
                                                    DB::table('cause_risk')
                                                        ->where('risk_id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    //eliminamos posibles efectos asociados
                                                    DB::table('effect_risk')
                                                        ->where('risk_id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    DB::table('risks')
                                                        ->where('id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el riesgo con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                                        }

                                $GLOBALS['res'] = 0;
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

    //ACT 16-10-17: destroy sin organización
    public function destroy2($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;

            global $org;

            $org = NULL;
            

            DB::transaction(function() {
                $logger = $this->logger;
                $name = \Ermtool\Risk::name($GLOBALS['id1']);
                //ACTUALIZACIÓN 29-03-17: YA NO EXISTE CONTROL_OBJECTIVE_RISK O CONTROL_RISK_SUBPROCESS
                //CORRECCIÓN DISEÑO: SIEMPRE TENDRÁ O UN OBJECTIVE_RISK O UN RISK_SUBPROCESS
                //primero vemos si contiene algún objetivo asociado 
                
                //$rev = DB::table('action_plans')
                //    ->where('')
                //OJO: En evaluation_risk sólo se buscará en el campo risk_id, ya que después se revisará objective_risk o risk_subprocess

                    //revisamos objective_subprocess_risk (hay q que ver objective_risk y subprocess_risk)
                    $rev = DB::table('objective_subprocess_risk')
                        ->where('objective_risk_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                    if (empty($rev))
                    {
                        //ahora revisamos en la misma tabla subprocess_risk
                        $rev = DB::table('objective_subprocess_risk')
                            ->where('risk_subprocess_id','=',$GLOBALS['id1'])
                            ->select('id')
                            ->get();

                        if (empty($rev))
                        {
                            //KRI
                            $rev = DB::table('kri')
                                    ->where('kri.risk_id','=',$GLOBALS['id1'])
                                    ->select('id')
                                    ->get();

                            if (empty($rev))
                            {
                                    //ahora vemos verificamos las uniones con objective_risk o subprocess_risk
                                    //primero usaremos una variable local para verificar posteriormente
                                    //ACTUALIZACIÓN 29-03-17: SOLO VEREMOS ORGANIZATION_RISK por lo tanto obtenemos su id
                                $res2 = 1;

                                if ($GLOBALS['org'] == NULL) //VERIFICACIÓN NO NECESARIA
                                {
                                    $risks = DB::table('organization_risk')
                                        ->where('risk_id','=',$GLOBALS['id1'])
                                        ->select('id')
                                        ->get();

                                    foreach ($risks as $risk)
                                    {
                                        $rev = DB::table('evaluation_risk')
                                                ->where('organization_risk_id','=',$risk->id)
                                                ->select('id')
                                                ->get();

                                        if (empty($rev))
                                        {
                                            $rev = DB::table('audit_plan_risk')
                                                ->where('organization_risk_id','=',$risk->id)
                                                ->select('id')
                                                ->get();

                                            if (empty($rev))
                                            {
                                                $rev = DB::table('audit_risk')
                                                        ->where('organization_risk_id','=',$risk->id)
                                                        ->select('id')
                                                        ->get();

                                                if (empty($rev))
                                                {
                                                    //para verificar que sea parar todos los objective_risk

                                                    $rev = DB::table('control_organization_risk')
                                                        ->where('organization_risk_id','=',$risk->id)
                                                        ->get();
                                                        
                                                    if (empty($rev))
                                                    {
                                                        //se puede borrar
                                                        $res2 = 0;
                                                    }
                                                        
                                                }
                                                else
                                                {
                                                    $res2 = 1;
                                                    break; //si al menos en alguna org se tienen datos, no se podrá borrar riesgo para todas
                                                }
                                            }
                                            else
                                            {
                                                $res2 = 1; //si al menos en alguna org se tienen datos, no se podrá borrar riesgo para todas
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            $res2 = 1; //si al menos en alguna org se tienen datos, no se podrá borrar riesgo para todas
                                            break;
                                        }
                                    }
                                }
                                
                            }
                            else
                            {
                                $res2 = 1;
                            }
                                        //} 

                            if ($res2 == 0)
                            {
                                        //se puede borrar

                                        //eliminamos objective_risk(s) asociados (si es que hay)
                                        //ACTUALIZACIÓN 30-03-17: Sólo los objective_risk asociados al riesgo que se está borrando
                                        //ACTUALIZACIÓN 16-10-17: Si es que se está borrando todas (sin seleccionar organización), se eliminan todos los objetivos o subprocesos asociados

                                        if ($GLOBALS['org'] != NULL)
                                        {
                                        }
                                        else
                                        {
                                            $objectives = DB::table('objectives')
                                                ->select('id')
                                                ->get();

                                            foreach ($objectives as $obj)
                                            {
                                                DB::table('objective_risk')
                                                    ->where('objective_risk.objective_id','=',$obj->id)
                                                    ->where('objective_risk.risk_id','=',$GLOBALS['id1'])
                                                    ->delete();
                                            }                                    

                                            $risk_subprocesses = DB::table('risk_subprocess')
                                                            ->where('risk_subprocess.risk_id','=',$GLOBALS['id1'])
                                                            ->select('risk_subprocess.id')
                                                            ->get();

                                            foreach ($risk_subprocesses as $r)
                                            {
                                                DB::table('risk_subprocess')        
                                                    ->where('risk_subprocess.id','=',$r->id)
                                                    ->delete();
                                            }

                                            DB::table('organization_risk')
                                                ->where('risk_id','=',$GLOBALS['id1'])
                                                ->delete();
                                        }
                                        
                                        //Ahora si podemos revisar si se puede eliminar lo demás (en caso de que el riesgo no exista para otras organizaciones)

                                        $revX = DB::table('organization_risk')
                                            ->where('risk_id','=',$GLOBALS['id1'])
                                            ->first();

                                        if (empty($revX))
                                        {
                                                    //Sólo de esta manera (vacío en todas partes), eliminamos el riesgo de la tabla risks

                                                    //eliminamos posibles causas asociadas
                                                    DB::table('cause_risk')
                                                        ->where('risk_id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    //eliminamos posibles efectos asociados
                                                    DB::table('effect_risk')
                                                        ->where('risk_id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    DB::table('risks')
                                                        ->where('id','=',$GLOBALS['id1'])
                                                        ->delete();

                                                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el riesgo con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                                        }

                                $GLOBALS['res'] = 0;
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
    //función para obtener riesgos de una organización
    public function getRisks($org)
    {
        try
        {
            //obtenemos riesgos de subproceso
            //En el inicio, org será NULL
            if ($org == NULL)
            {
                $risks = DB::table('risks')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                    ->where('risks.status','=',0)
                    ->select('risks.id','risks.name','risks.description','risks.type','risks.risk_category_id')
                    ->distinct('risks.id')
                    ->get();
            }
            else
            {
                $risks = DB::table('risks')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('risks.status','=',0)
                    ->select('risks.id','risks.name','risks.description','risks.type','risks.risk_category_id')
                    ->distinct('risks.id')
                    ->get();
            }
            
            

            $i = 0;

            foreach ($risks as $risk)
            {
                //obtenemos nombre de categoria
                $risk_category = \Ermtool\Risk_category::find($risk->risk_category_id);

                //enviamos además si es que es categoría principal o secundaria
                if ($risk_category->risk_category_id == NULL)
                {
                    //es categoría principal
                    $sec = 0;
                }
                else 
                {
                    $sec = 1;
                }

                $results[$i] = [
                    'id' => $risk->id,
                    'name' => $risk->name,
                    'description' => $risk->description,
                    'type' => $risk->type,
                    'risk_category_id' => $risk->risk_category_id,
                    'risk_category' => $risk_category->name,
                    'sec' => $sec
                ];

                $i += 1;
            }

            //obtenemos riesgos de negocio
            //En el inicio, org será NULL
            if ($org == NULL)
            {
                $risks = DB::table('risks')
                    ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                    ->where('risks.status','=',0)
                    ->select('risks.id','risks.name','risks.description','risks.type','risks.risk_category_id')
                    ->distinct('risks.id')
                    ->get();
            }
            else
            {
                $risks = DB::table('risks')
                    ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->where('objectives.organization_id','=',$org)
                    ->where('risks.status','=',0)
                    ->select('risks.id','risks.name','risks.description','risks.type','risks.risk_category_id')
                    ->distinct('risks.id')
                    ->get();
            }

            foreach ($risks as $risk)
            {
                //obtenemos nombre de categoria
                $risk_category = \Ermtool\Risk_category::find($risk->risk_category_id);

                //enviamos además si es que es categoría principal o secundaria
                if ($risk_category->risk_category_id == NULL)
                {
                    //es categoría principal
                    $sec = 0;
                }
                else 
                {
                    $sec = 1;
                }
                $results[$i] = [
                    'id' => $risk->id,
                    'name' => $risk->name,
                    'description' => $risk->description,
                    'type' => $risk->type,
                    'risk_category_id' => $risk->risk_category_id,
                    'risk_category' => $risk_category->name,
                    'sec' => $sec
                ];

                $i += 1;
            }

            //si es en inicio, no enviaremos los datos a través de json
            if ($org == NULL)
            {
                return $results;
            }
            else
            {
                return json_encode($results);
            }
            
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getRisks2($org,$type)
    {
        try
        {
            $risks = array();

            if ($type == 1)
            {
                //riesgos de negocio
                $risks = \Ermtool\Risk::getObjectiveRisks($org);
            }
            else if ($type == 0)
            {
                //riesgos de proceso
                $risks = \Ermtool\Risk::getRiskSubprocess($org);
            }
            return json_encode($risks);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtiene todas las causas
    public function getCauses()
    {
        try
        {
            $causes = \Ermtool\Cause::all(['id','name']);
            return json_encode($causes);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }

    }

    //obtiene todos los efectos
    public function getEffects()
    {
        try
        {
            $effects = \Ermtool\Effect::all(['id','name']);
            return json_encode($effects);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //Función obtiene riesgos de negocio a través de JSON al crear plan de pruebas (también utilizado para crear encuesta de evaluación)
    public function getRiesgosObjetivos($org)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //obtenemos riesgos de negocio para la organización seleccionada
                $objective_risk = \Ermtool\Risk::getObjectiveRisks((int)$org);
                //obtenemos evaluaciones
                $results = $this->getEvaluationData($objective_risk);
                //ordenamos resultados según función cmp
                usort($results, array($this,"cmp"));
                return json_encode($results);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }   
    }

    //Función obtiene riesgos de proceso a través de JSON al crear plan de pruebas
    public function getRiesgosProcesos($org)
    {
        //try
        //{
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                //obtenemos riesgos de proceso para la organización seleccionada
                $risk_subprocess = \Ermtool\Risk::getRiskSubprocess((int)$org);
                $results = $this->getEvaluationData($risk_subprocess);
                //ordenamos resultados según función cmp   
                usort($results, array($this,"cmp"));
                return json_encode($results);
            }
        /*}
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }   */
    }

    public function getEvaluationData($risks)
    {
        //try
       //{
            $results = array();
            $i = 0; //contador de riesgos de negocio
            foreach ($risks as $risk)
            {
            //obtengo maxima fecha para obtener última evaluación
                    $fecha = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$risk->org_risk_id)
                                    ->max('updated_at');

                    $fecha_up = new DateTime($fecha);
                    //$fecha_up = date_format($fecha_up,"d-m-Y");
                    //obtenemos evaluación de riesgo de negocio (si es que hay)---> Ultima (mayor fecha updated_at)
                    $evaluations = DB::table('evaluation_risk')
                                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                    ->where('evaluation_risk.organization_risk_id','=',$risk->org_risk_id)
                                    ->where('evaluations.consolidation','=',1)
                                    ->whereNotNull('evaluation_risk.avg_probability')
                                    ->whereNotNull('evaluation_risk.avg_impact')
                                    ->where('evaluations.updated_at','=',$fecha_up)
                                    ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                    ->get();
                    //seteamos por si no hay evaluación
                    $avg_probability = "Falta Eval.";
                    $avg_impact = "Falta Eval.";
                    $score = "Falta Eval.";
                    $proba_def = "";
                    $impact_def = "";
                    foreach ($evaluations as $evaluation)
                    {
                        //seteamos nombres de probabilidad e impacto
                        if (Session::get('languaje') == 'en')
                        {
                            switch ($evaluation->avg_probability)
                            {
                                case ($evaluation->avg_probability >= 1 && $evaluation->avg_probability < 2):
                                    $proba = 'Very improbable';
                                    break;
                                case ($evaluation->avg_probability >= 2 && $evaluation->avg_probability < 3):
                                    $proba = 'Unlikely';
                                    break;
                                case ($evaluation->avg_probability >= 3 && $evaluation->avg_probability < 4):
                                    $proba = 'Possible';
                                    break;
                                case ($evaluation->avg_probability >= 4 && $evaluation->avg_probability < 5):
                                    $proba = 'Likely';
                                    break;
                                case 5:
                                    $proba = 'Very likely';
                                    break;
                            }
                            switch ($evaluation->avg_impact)
                            {
                                case ($evaluation->avg_impact >= 1 && $evaluation->avg_impact < 2):
                                    $impact = 'Despicable';
                                    break;
                                case ($evaluation->avg_impact >= 2 && $evaluation->avg_impact < 3):
                                    $impact = 'Less';
                                    break;
                                case ($evaluation->avg_impact >= 3 && $evaluation->avg_impact < 4):
                                    $impact = 'Moderate';
                                    break;
                                case ($evaluation->avg_impact >= 4 && $evaluation->avg_impact < 5):
                                    $impact = 'Severe';
                                    break;
                                case 5:
                                    $impact = 'Catastrophic';
                                    break;
                            }
                        }
                        else
                        {
                            //ACTUALIZACIÓN 26-08-17: Puede ser FLOAT, por lo que se debe hacer por rangos
                            switch ($evaluation->avg_probability)
                            {
                                case ($evaluation->avg_probability >= 1 && $evaluation->avg_probability < 2):
                                    $proba = 'Muy poco probable';
                                    break;
                                case ($evaluation->avg_probability >= 2 && $evaluation->avg_probability < 3):
                                    $proba = 'Poco probable';
                                    break;
                                case ($evaluation->avg_probability >= 3 && $evaluation->avg_probability < 4):
                                    $proba = 'Intermedio';
                                    break;
                                case ($evaluation->avg_probability >= 4 && $evaluation->avg_probability < 5):
                                    $proba = 'Probable';
                                    break;
                                case 5:
                                    $proba = 'Muy probable';
                                    break;
                            }

                            switch ($evaluation->avg_impact)
                            {
                                case ($evaluation->avg_impact >= 1 && $evaluation->avg_impact < 2):
                                    $impact = 'Muy poco impacto';
                                    break;
                                case ($evaluation->avg_impact >= 2 && $evaluation->avg_impact < 3):
                                    $impact = 'Poco impacto';
                                    break;
                                case ($evaluation->avg_impact >= 3 && $evaluation->avg_impact < 4):
                                    $impact = 'Intermedio';
                                    break;
                                case ($evaluation->avg_impact >= 4 && $evaluation->avg_impact < 5):
                                    $impact = 'Alto impacto';
                                    break;
                                case 5:
                                    $impact = 'Muy alto impacto';
                                    break;
                            }
                        }
                        $avg_probability = $evaluation->avg_probability;
                        $avg_impact = $evaluation->avg_impact;
                        $impact_def = $impact;
                        $proba_def = $proba;
                        $score = $evaluation->avg_probability * $evaluation->avg_impact;
                    }
                    $results[$i] = [
                                'name' => $risk->risk_name,
                                'description' => $risk->description,
                                'risk_category_id' => $risk->risk_category_id,
                                 'id' => $risk->org_risk_id,
                                 'avg_probability' => $avg_probability,
                                 'avg_impact' => $avg_impact,
                                 'proba_def' => $proba_def,
                                 'impact_def' => $impact_def,
                                 'score' => $score
                                ];
                    $i += 1;
            }

            return $results;
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
    }

    /*
    función identifica si se seleccionarán riesgos/subprocesos o riesgos/objetivos
    al momento de crear un control */
    public function subneg($value,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                if ($value == 0) //son riesgos de subprocesos
                {
                    $datos = \Ermtool\Risk::getRiskSubprocess($org);
                }
                else if ($value == 1) //son riesgos de negocio
                {
                    $datos = \Ermtool\Risk::getObjectiveRisks($org);
                }
                return json_encode($datos);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //compara scores para ordenar de mayor a menor
    function cmp($a, $b)
    {
        try
        {
            if ($a['score'] == $b['score'])
            {
                return 0;
            }
            return ($a['score'] > $b['score']) ? -1 : 1;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
