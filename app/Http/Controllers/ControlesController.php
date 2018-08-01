<?php namespace Ermtool\Http\Controllers;
use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Storage;
use DateTime;
use Auth;
use ArrayObject;
use Ermtool\Http\Controllers\DocumentosController as Documentos;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

//sleep(2);
class ControlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger;
    public $logger2;
    public $logger3;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('controles');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/controles.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());

        $this->logger2 = new Logger('evaluacion_controles');
        $this->logger2->pushHandler(new StreamHandler($dir.'/storage/logs/evaluacion_controles.log', Logger::INFO));
        $this->logger2->pushHandler(new FirePHPHandler());

        $this->logger3 = new Logger('evaluacion_riesgos');
        $this->logger3->pushHandler(new StreamHandler($dir.'/storage/logs/evaluacion_riesgos.log', Logger::INFO));
        $this->logger3->pushHandler(new FirePHPHandler());
    }

    public function getControlReport($controles,$value)
    {
        try
        {
            $controls = array();  
            $controls_temp = array();
            $no_ejecutados = array();
            $efectivos = 0;
            $inefectivos = 0;
            $j = 0; //contador para ids de efectivos e inefectivos
            $id_inefectivos = array();
            $id_efectivos = array();
            $i = 0;
                foreach ($controles as $control)
                {
                    //primero obtenemos fecha del último resultado de evaluación del control
                    $max_date = DB::table('control_eval_risk_temp')
                                    ->where('control_id','=',$control->id)
                                    ->max('created_at');

                    //ACT 17-04-17: str_replace
                    $max_date = str_replace('-','',$max_date);

                    $controls_temp[$i] = $control->id;
                    $i += 1;
                    //para cada uno vemos si son efectivos o inefectivos: Si al menos una de las pruebas es inefectiva, el control es inefectivo
                    //ACTUALIZACIÓN 22-11-16: Sólo verificaremos en tabla control_eval_risk_temp
                    $res = DB::table('control_eval_risk_temp')
                                ->where('control_id','=',$control->id)
                                ->where('created_at','=',$max_date)
                                ->select('result')
                                ->first();

                    if (!empty($res))
                    {
                        if ($res->result == 2)
                        {
                            array_push($id_inefectivos,$control->id);
                            $inefectivos += 1;
                        }
                        else
                        {
                            array_push($id_efectivos,$control->id);
                            $efectivos += 1;
                        }
                    }
                }
                
                //ahora obtenemos los datos de los controles seleccionados
                $i = 0;
                foreach ($controls_temp as $id)
                {
                    $control = \Ermtool\Control::find($id);
                    //obtenemos resultado del control
                    //fecha de actualización del control
                    $updated_at = new DateTime($control->updated_at);
                    $updated_at = date_format($updated_at, 'd-m-Y');
                    $description = preg_replace("[\n|\r|\n\r]", ' ', $control->description); 
                    foreach ($id_efectivos as $id_ef)
                    {
                        if ($id_ef == $control->id)
                        {
                            $controls[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                                'results' => 2
                            ];
                            $i += 1;
                        }
                    }
                    foreach ($id_inefectivos as $id_inef)
                    {
                        if ($id_inef == $control->id)
                        {
                            $controls[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                                'results' => 1
                            ];
                            $i += 1;
                        }
                    }
                    
                }
                //guardamos cantidad de ejecutados
                $cont_ejec = $i;
                //ahora obtenemos el resto de controles (para obtener los no ejecutados)
                $controles = DB::table('controls')
                                ->whereNotIn('controls.id',$controls_temp)
                                ->select('id','name','description','updated_at')
                                ->get();
                //guardamos en array
                $i = 0;
                foreach ($controles as $control)
                {
                    $updated_at = new DateTime($control->updated_at);
                    $updated_at = date_format($updated_at, 'd-m-Y');
                    $description = preg_replace("[\n|\r|\n\r]", ' ', $control->description);  
                    $no_ejecutados[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                            ];
                    $i += 1;
                }
                //guardamos cantidad de no ejecutados
                $cont_no_ejec = $i;
                //return json_encode($controls);
                //echo $cont_ejec.' y '.$cont_no_ejec;
                //echo $efectivos. ' y '.$inefectivos;
                //print_r($id_efectivos);
                //print_r($id_inefectivos);
                //print_r($no_ejecutados);
                if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos')) 
                {
                    $res2 = array();
                    if ($value == 1) //reporte excel de controles ejecutados
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res2[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res2[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 2) //reporte excel de controles no ejecutados
                    {
                        $i = 0;
                        foreach ($no_ejecutados as $control)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res2[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res2[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 3) //reporte excel de controles efectivos
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if ($control['results'] == 2)
                            {
                                if (Session::get('languaje') == 'en')
                                {
                                    $res2[$i] = [
                                        'Name' => $control['name'],
                                        'Description' => $control['description'],
                                        'Updated date' => $control['updated_at'],
                                    ];
                                }
                                else
                                {
                                    $res2[$i] = [
                                        'Nombre' => $control['name'],
                                        'Descripción' => $control['description'],
                                        'Actualizado' => $control['updated_at'],
                                    ];
                                }    
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 4) //reporte excel de controles no efectivos
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if ($control['results'] == 1)
                            {
                                if (Session::get('languaje') == 'en')
                                {
                                    $res2[$i] = [
                                        'Name' => $control['name'],
                                        'Description' => $control['description'],
                                        'Updated date' => $control['updated_at'],
                                    ];
                                }
                                else
                                {
                                    $res2[$i] = [
                                        'Nombre' => $control['name'],
                                        'Descripción' => $control['description'],
                                        'Actualizado' => $control['updated_at'],
                                    ];
                                }    
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    
                }
                else
                {
                    return ['controls' => $controls,
                            'no_ejecutados' => $no_ejecutados,
                            'cont_ejec' => $cont_ejec,
                            'cont_no_ejec' => $cont_no_ejec,
                            'efectivos' => $efectivos,
                            'inefectivos' => $inefectivos];
                }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
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
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.index',['organizations' => $organizations]);
                }
                else
                {
                    return view('controles.index',['organizations' => $organizations]);
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
                return Redirect::route('/');
            }
            else
            {
                $controls1 = array();
                $controls2 = array();
                $objective_risks = array();
                $risks_subprocess = array();

                $i = 0; //contador de controles
                
                //ACTUALIZACIÓN 21-11-17: Vemos si se está filtrando por Riesgo
                //OBS 26-12-17: objective_risk_id y risk_subprocess_id en realidad están tomando organization_risk_id (por lo que nisiquiera sería necesario enviar la columna organization_id)
                if (isset($_GET['objective_risk_id']))
                {
                    $controles1 = \Ermtool\Control::getBussinessControls($_GET['organization_id'],$_GET['objective_risk_id']); 
                }
                else
                {
                    $controles1 = \Ermtool\Control::getBussinessControls($_GET['organization_id'],NULL);
                }

                if (isset($_GET['risk_subprocess_id']))
                {
                    $controles2 = \Ermtool\Control::getProcessesControls($_GET['organization_id'],$_GET['risk_subprocess_id']);
                }
                else
                {
                    $controles2 = \Ermtool\Control::getProcessesControls($_GET['organization_id'],NULL);
                }

                //controles de negocio
                foreach ($controles1 as $control) //se recorre cada uno de los controles de negocio
                {
                    $control = (object)$control;
                    $risks1 = \Ermtool\Risk::getRisksFromControl($_GET['organization_id'],$control->id);
                    
                    $k = 0; //contador de objetivos
                    $j = 0; //contador de riesgos
                    $risks = array(); //vacíamos o declaramos vacío los riesgos para no repetir
                    $objectives = array(); //objetivos asociados al control
                    //obtenemos subprocesos a partir de los riesgos
                    foreach ($risks1 as $risk)
                    {
                        //obtenemos objetivos a partir del riesgo
                        $objectives2 = \Ermtool\Objective::getObjectivesFromOrgRisk($risk->id,$_GET['organization_id']);

                        foreach ($objectives2 as $obj)
                        {
                            $objectives[$k] = [
                                'name' => $obj->name,
                                'description' => $obj->description,
                            ];

                            $k += 1;
                        }
                        $short_des = substr($risk->description,0,100);
                        //ACT 27-04-17: eliminamos saltos de línea
                        $desc = eliminarSaltos($risk->description);
                        $risks[$j] = [
                            'id' => $risk->id,
                            'name' => $risk->name,
                            'description' => $desc,
                            'short_des' => $short_des
                        ]; 
                        $j += 1;
                    }

                    //guardamos sólo los objetivos correspondientes
                    $objectives = array_unique($objectives,SORT_REGULAR);
                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($control->created_at == NULL OR $control->created_at == "0000-00-00" OR $control->created_at == "")
                    {
                        $fecha_creacion = NULL;
                    }
                    else
                    {
                        $fecha_creacion = new DateTime($control->created_at);
                        $fecha_creacion = date_format($fecha_creacion,"d-m-Y");
                    }
                    //damos formato a fecha de actualización 
                    if ($control->updated_at != NULL)
                    {
                        $fecha_act = new DateTime($control->updated_at);
                        $fecha_act = date_format($fecha_act,"d-m-Y");
                    }
                    else
                        $fecha_act = NULL;
                    //obtenemos nombre de responsable
                    $stakeholder = \Ermtool\Stakeholder::find($control->stakeholder_id);
                    if ($stakeholder)
                    {
                        $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                    }
                    else
                    {
                        $stakeholder2 = NULL;
                    }
                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($control->description,0,100);
                    //ACT 27-04-17: eliminamos saltos de línea
                    $description = eliminarSaltos($control->description);

                    $controls1[$i] = array('id'=>$control->id,
                                        'name'=>$control->name,
                                        'description'=>$description,
                                        'type'=>$control->type,
                                        'type2'=>$control->type2,
                                        'created_at'=>$fecha_creacion,
                                        'evidence'=>$control->evidence,
                                        'periodicity'=>$control->periodicity,
                                        'purpose'=>$control->purpose,
                                        'stakeholder'=>$stakeholder2,
                                        'expected_cost' => $control->expected_cost,
                                        'risks' => $risks,
                                        'objectives' => $objectives,
                                        'short_des' => $short_des,
                                        'porcentaje_cont' => $control->porcentaje_cont,
                                        'key_control' => $control->key_control,
                                        'objective' => $control->objective,
                                        'establishment' => $control->establishment,
                                        'application' => $control->application,
                                        'supervision' => $control->supervision,
                                        'test_plan' => $control->test_plan,
                                    );
                    $i += 1;
                }

                //controles de proceso
                foreach ($controles2 as $control)
                {
                    $control = (object)$control;
                    $risks1 = \Ermtool\Risk::getRisksFromControl($_GET['organization_id'],$control->id);
                    //almacenamos los nombres de los riesgos y subprocesos u objetivos asociados 
                    $subprocesses = array();
                    $j = 0; //contador de subprocesos
                    $risks = array(); //vacíamos o declaramos vacío los riesgos para no repetir
                    //obtenemos subprocesos a partir de los riesgos
                    $k = 0;
                    foreach ($risks1 as $risk)
                    {
                        $subprocesses2 = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($risk->id,$_GET['organization_id']);

                        foreach ($subprocesses2 as $sub)
                        {
                            $subprocesses[$k] = [
                                'name' => $sub->name,
                                'description' => $sub->description,
                            ];

                            $k += 1;
                        }

                        $short_des = substr($risk->description,0,100);
                        //ACT 27-04-17: eliminamos saltos de línea
                        $desc = eliminarSaltos($risk->description);
                        $risks[$j] = [
                            'id' => $risk->id,
                            'name' => $risk->name,
                            'description' => $desc,
                            'short_des' => $short_des
                        ]; 
                        $j += 1;
                    }  

                    //guardamos sólo los subprocesos correspondientes sin repetir
                    $subprocesses = array_unique($subprocesses,SORT_REGULAR);
                    //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                    if ($control->created_at == NULL OR $control->created_at == "0000-00-00" OR $control->created_at == "")
                    {
                        $fecha_creacion = NULL;
                    }
                    else
                    {
                        $fecha_creacion = new DateTime($control->created_at);
                        $fecha_creacion = date_format($fecha_creacion,"d-m-Y");
                    }
                    //damos formato a fecha de actualización 
                    if ($control->updated_at != NULL)
                    {
                        $fecha_act = new DateTime($control->updated_at);
                        $fecha_act = date_format($fecha_act,"d-m-Y");
                    }
                    else
                        $fecha_act = NULL;
                    //obtenemos nombre de responsable
                    $stakeholder = \Ermtool\Stakeholder::find($control->stakeholder_id);
                    if ($stakeholder)
                    {
                        $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                    }
                    else
                    {
                        $stakeholder2 = NULL;
                    }

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($control->description,0,100);
                    //ACT 27-04-17: eliminamos saltos de línea
                    $description = eliminarSaltos($control->description);

                    $controls2[$i] = array('id'=>$control->id,
                                        'name'=>$control->name,
                                        'description'=>$description,
                                        'type'=>$control->type,
                                        'type2'=>$control->type2,
                                        'created_at'=>$fecha_creacion,
                                        'updated_at'=>$fecha_act,
                                        'evidence'=>$control->evidence,
                                        'periodicity'=>$control->periodicity,
                                        'purpose'=>$control->purpose,
                                        'stakeholder'=>$stakeholder2,
                                        'expected_cost' => $control->expected_cost,
                                        'risks' => $risks,
                                        'subprocesses' => $subprocesses,
                                        'short_des' => $short_des,
                                        'porcentaje_cont' => $control->porcentaje_cont);
                    $i += 1;
                }

                $cocacola = 1; //Insertamos variable para mostrar en vista que es de coca cola
                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.index',['controls1' => $controls1,'controls2'=>$controls2,'org_id' => $_GET['organization_id'],'cocacola' => $cocacola]);
                }
                else
                {
                    return view('controles.index',['controls1' => $controls1,'controls2'=>$controls2,'org_id' => $_GET['organization_id'],'cocacola' => $cocacola]);
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
    public function create($org)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $stakeholders = \Ermtool\Stakeholder::listStakeholders($org);
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                //ACTUALIZACIÓN 16-11-17: Estados financieros
                $financial_statements = \Ermtool\Financial_statement::where('status',0)->lists('name','id');
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.create',['stakeholders'=>$stakeholders,'org'=>$org,'categories' => $categories,'financial_statements' => $financial_statements]);
                }
                else
                {
                    return view('controles.create',['stakeholders'=>$stakeholders,'org'=>$org,'categories' => $categories,'financial_statements' => $financial_statements]);
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
        //try
        //{
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //Validación: Si la validación es pasada, el código continua
                $this->validate($request, [
                    'name' => 'required|max:255',
                ]);
                //print_r($_POST);
                //guardamos variable global de evidencia
                global $request2;
                $request2 = $request;
                //creamos una transacción para cumplir con atomicidad
                DB::transaction(function()
                {
                    $logger = $this->logger;

                        if (isset($_POST['description']))
                        {
                            if ($_POST['description'] == NULL || $_POST['description'] == "")
                            {
                                $description = NULL;
                            }
                            else
                            {
                                $description = $_POST['description'];
                            }
                        }
                        else
                        {
                            $description = NULL;
                        }

                        if ($_POST['stakeholder_id'] == NULL)
                            $stakeholder = NULL;
                        else
                            $stakeholder = $_POST['stakeholder_id'];

                        if ($_POST['periodicity'] == NULL || $_POST['periodicity'] == "")
                        {
                            $periodicity = NULL;
                        }
                        else
                        {
                            $periodicity = $_POST['periodicity'];
                        }
                        if ($_POST['purpose'] == NULL || $_POST['purpose'] == "")
                        {
                            $purpose = NULL;
                        }
                        else
                        {
                            $purpose = $_POST['purpose'];
                        }
                        if ($_POST['type'] == NULL || $_POST['type'] == "")
                        {
                            $type = NULL;
                        }
                        else
                        {
                            $type = $_POST['type'];
                        }
                        if ($_POST['expected_cost'] == NULL || $_POST['expected_cost'] == "")
                        {
                            $expected_cost = NULL;
                        }
                        else
                        {
                            $expected_cost = $_POST['expected_cost'];
                        }

                        if ($_POST['evidence'] == NULL || $_POST['evidence'] == "")
                        {
                            $evidence = NULL;
                        }
                        else
                        {
                            $evidence = $_POST['evidence'];
                        }

                        //ACT 16-04-18: Agregamos comentarios para el control (asociado a la organización) en caso de que existan
                        if (isset($_POST['comments']) && $_POST['comments'] != "")
                        {
                            $comments = $_POST['comments'];
                        }
                        else
                        {
                            $comments = NULL;
                        }

                        //ACT 03-07-17: Se agrega porcentaje de contribución
                        if (isset($_POST['porcentaje_cont']))
                        {
                            if ($_POST['porcentaje_cont'] == NULL || $_POST['porcentaje_cont'] == "")
                            {
                                $porcentaje_cont = NULL;
                            }
                            else
                            {
                                $porcentaje_cont = $_POST['porcentaje_cont'];
                            }
                        }
                        else
                        {
                            $porcentaje_cont = NULL;
                        }

                        //ACTUALIZACIÓN 16-11-17: Nuevos atributos de control
                        if (isset($_POST['key_control']))
                        {
                            if ($_POST['key_control'] == NULL || $_POST['key_control'] == "")
                            {
                                $key_control = NULL;
                            }
                            else
                            {
                                $key_control = $_POST['key_control'];
                            }
                        }
                        else
                        {
                            $key_control = NULL;
                        }

                        if (isset($_POST['objective']))
                        {
                            if ($_POST['objective'] == NULL || $_POST['objective'] == "")
                            {
                                $objective = NULL;
                            }
                            else
                            {
                                $objective = $_POST['objective'];
                            }
                        }
                        else
                        {
                            $objective = NULL;
                        }

                        if (isset($_POST['establishment']))
                        {
                            if ($_POST['establishment'] == NULL || $_POST['establishment'] == "")
                            {
                                $establishment = NULL;
                            }
                            else
                            {
                                $establishment = $_POST['establishment'];
                            }
                        }
                        else
                        {
                            $establishment = NULL;
                        }

                        if (isset($_POST['application']))
                        {
                            if ($_POST['application'] == NULL || $_POST['application'] == "")
                            {
                                $application = NULL;
                            }
                            else
                            {
                                $application = $_POST['application'];
                            }
                        }
                        else
                        {
                            $application = NULL;
                        }

                        if (isset($_POST['supervision']))
                        {
                            if ($_POST['supervision'] == NULL || $_POST['supervision'] == "")
                            {
                                $supervision = NULL;
                            }
                            else
                            {
                                $supervision = $_POST['supervision'];
                            }
                        }
                        else
                        {
                            $supervision = NULL;
                        }

                        if (isset($_POST['objective']))
                        {
                            if ($_POST['objective'] == NULL || $_POST['objective'] == "")
                            {
                                $objective = NULL;
                            }
                            else
                            {
                                $objective = $_POST['objective'];
                            }
                        }
                        else
                        {
                            $objective = NULL;
                        }

                        if (isset($_POST['test_plan']))
                        {
                            if ($_POST['test_plan'] == NULL || $_POST['test_plan'] == "")
                            {
                                $test_plan = NULL;
                            }
                            else
                            {
                                $test_plan = $_POST['test_plan'];
                            }
                        }
                        else
                        {
                            $test_plan = NULL;
                        }

                        //insertamos control y obtenemos ID
                        $control_id = DB::table('controls')->insertGetId([
                                'name'=>$_POST['name'],
                                'description'=>$description,
                                'type'=>$type,
                                'type2'=>$_POST['subneg'],
                                //'evidence'=>$evidence,
                                'periodicity'=>$periodicity,
                                'purpose'=>$purpose,
                                //'stakeholder_id'=>$stakeholder,
                                'created_at'=>date('Y-m-d H:i:s'),
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'expected_cost'=>$expected_cost,
                                //'porcentaje_cont'=>$porcentaje_cont,
                                'key_control' => $key_control,
                                'objective' => $objective,
                                'establishment' => $establishment,
                                'application' => $application,
                                'supervision' => $supervision,
                                'test_plan' => $test_plan
                                ]);
                        
                        //ACTUALIZACIÓN 31-03-17: Agregamos en control_organization_risk
                        //ACTUALIZACIÓN 04-12-17: control_organization_risk con stakeholder
                        //ACT 16-04-18: control_organization_risk con evidence
                        foreach ($_POST['select_riesgos'] as $riesgo)
                        {
                            DB::table('control_organization_risk')
                                ->insert([
                                    'organization_risk_id' => $riesgo,
                                    'control_id' => $control_id,
                                    'stakeholder_id' => $stakeholder,
                                    'evidence' => $evidence,
                                    'comments' => $comments,
                                    'cont_percentage'=>$porcentaje_cont,
                                ]);
                        }

                        //ACT 17-04-18: Ahora estará en control_organization
                        $ctrl_org = \Ermtool\ControlOrganization::create([
                            'organization_id' => $_POST['org_id'],
                            'control_id' => $control_id,
                            'stakeholder_id' => $stakeholder,
                            'evidence' => $evidence,
                            'comments' => $comments,
                            'cont_percentage' => $porcentaje_cont,
                        ]);

                        //ACTUALIZACIÓN 16-11-17: Agregamos estados financieros
                        if (isset($_POST['financial_statement_id']))
                        {
                            foreach ($_POST['financial_statement_id'] as $fs)
                            {
                                DB::table('control_financial_statement')
                                ->insert([
                                    'control_id' => $ctrl_org->id,
                                    'financial_statement_id' => $fs
                                ]);
                            }
                        }

                        //Vemos si se están creando nuevos estados financieros
                        $i = 1;

                        while (isset($_POST['new_fs_'.$i]))
                        {
                            if ($_POST['new_fs_'.$i] != '' && $_POST['new_fs_'.$i] != NULL)
                            {
                                $fs = \Ermtool\Financial_statement::create([
                                        'name' => $_POST['new_fs_'.$i],
                                        'status' => 0
                                    ]);

                                //agregamos enlace
                                DB::table('control_financial_statement')
                                ->insert([
                                    'control_id' => $ctrl_org->id,
                                    'financial_statement_id' => $fs->id
                                ]);
                            }

                            $i += 1;
                        }

                        //ACTUALIZACION 05-07-2017: Para Coca Cola Andina, calcularemos aquí el valor del control y almacenaremos porcentaje de resultado (según evaluaciones y autoevaluaciones) en tabla control_eval_risk_temp

                        //Guardamos en control_eval_risk_temp valor del control (autoevaluación)

                        DB::table('control_eval_risk_temp')
                            ->insert([
                                'result' => $_POST['porcentaje_cont'],
                                'control_organization_id' => $ctrl_org->id,
                                'auto_evaluation' => 1,
                                'status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                                ]);

                        //ahora calcularemos el valor de el o los riesgos a los que apunte este control
                        $eval_risk = $this->calcControlledRiskAutoeval($control_id,$_POST['org_id'],date('Y'),date('m'),date('d'));


                        //guardamos archivos de evidencias (si es que hay)
                        if($GLOBALS['request2']->file('evidence_doc') != NULL)
                        {
                            foreach ($GLOBALS['request2']->file('evidence_doc') as $evidencedoc)
                            {
                                if ($evidencedoc != NULL)
                                {
                                    //ACT 24-04-18: Ahora se guardará control_org
                                    //upload_file($evidencedoc,'controles',$control_id);
                                    upload_file($evidencedoc,'controles_org',$ctrl_org->id);
                                }
                            }                    
                        }
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Control successfully created');
                        }
                        else
                        {
                            Session::flash('message','Control agregado correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el control con Id: '.$control_id.' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('controles.index2?organization_id='.$_POST['org_id']);
            }
        //}
        //catch (\Exception $e)
        //{
        //    enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
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
    public function edit($id,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $risks_selected = array(); //array de riesgos seleccionados previamente
                $control = \Ermtool\Control::find($id);
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');
                //seleccionamos riesgos de proceso u objetivo que fueron seleccionados previamente (según corresponda)
                //ACTUALIZACIÓN 02-04-17: Seleccionamos de control_organization_risk
                //seleccionamos riesgos de proceso seleccionados previamente
                
                $risks = DB::table('control_organization_risk')
                            ->join('organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                            ->join('risks','risks.id','=','organization_risk.risk_id')
                            ->where('control_organization_risk.control_id','=',$control->id)
                            ->where('organization_risk.organization_id','=',(int)$org)
                            ->select('organization_risk.id as id','risks.id as risk_id','risks.name as risk_name','risks.description')
                            ->get();
                
                $i = 0;
                foreach ($risks as $risk)
                {
                    $risks_selected[$i] = (int)$risk->id;

                    $i += 1;
                }

                //ACT 26-03-18: Obtenemos de aquí el responsable del control
                $stakeholder = \Ermtool\Control::getStakeholder($control->id,(int)$org);

                //ACT 16-04-18: Agregamos info de control_organization_risk
                //$control_org_risk = \Ermtool\Control::getControlOrganizationRisk($control->id,(int)$org);
                //ACT 18-04-18: Info la obtenemos de control_organization
                $control_org = \Ermtool\Control::getControlOrganization($control->id,(int)$org);
                //ACTUALIZACIÓN 16-11-17: Estados financieros
                $financial_statements = \Ermtool\Financial_statement::where('status',0)->lists('name','id');

                //Vemos estados financieros seleccionados previamente
                $fs_selected = \Ermtool\Financial_statement::getFSByControl($control->id);
                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.edit',['control'=>$control,'stakeholders'=>$stakeholders,'risks_selected'=>json_encode($risks_selected),'org' => (int)$org,'categories' => $categories,'fs_selected' => $fs_selected,'financial_statements' => $financial_statements,'stakeholder' => $stakeholder,'control_org' => $control_org]);
                }
                else
                {
                    return view('controles.edit',['control'=>$control,'stakeholders'=>$stakeholders,'risks_selected'=>json_encode($risks_selected),'org' => (int)$org,'categories' => $categories,'fs_selected' => $fs_selected,'financial_statements' => $financial_statements,'stakeholder' => $stakeholder,'control_org' => $control_org]);
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
                global $evidencedoc;
                $evidencedoc = $request->file('evidence_doc');
                DB::transaction(function() 
                {
                    $logger = $this->logger;
                    $control = \Ermtool\Control::find($GLOBALS['id1']);

                    if (isset($_POST['description']))
                    {
                        if ($_POST['description'] == NULL || $_POST['description'] == "")
                        {
                            $description = NULL;
                        }
                        else
                        {
                            $description = $_POST['description'];
                        }
                    }
                    else
                    {
                        $description = NULL;
                    }
                    if ($_POST['stakeholder_id'] == NULL)
                        $stakeholder = NULL;
                    else
                        $stakeholder = $_POST['stakeholder_id'];

                    if ($_POST['periodicity'] == NULL || $_POST['periodicity'] == "")
                    {
                        $periodicity = NULL;
                    }
                    else
                    {
                        $periodicity = $_POST['periodicity'];
                    }
                    if ($_POST['purpose'] == NULL || $_POST['purpose'] == "")
                    {
                        $purpose = NULL;
                    }
                    else
                    {
                        $purpose = $_POST['purpose'];
                    }
                    if ($_POST['type'] == NULL || $_POST['type'] == "")
                    {
                        $type = NULL;
                    }
                    else
                    {
                        $type = $_POST['type'];
                    }
                    if ($_POST['expected_cost'] == NULL || $_POST['expected_cost'] == "")
                    {
                        $expected_cost = NULL;
                    }
                    else
                    {
                        $expected_cost = $_POST['expected_cost'];
                    }
                    if ($_POST['evidence'] == NULL || $_POST['evidence'] == "")
                    {
                        $evidence = NULL;
                    }
                    else
                    {
                        $evidence = $_POST['evidence'];
                    }

                    //ACT 16-04-18: Agregamos comentarios para el control (asociado a la organización) en caso de que existan
                    if (isset($_POST['comments']) && $_POST['comments'] != "")
                    {
                        $comments = $_POST['comments'];
                    }
                    else
                    {
                        $comments = NULL;
                    }

                    //ACT 03-07-17: Se agrega porcentaje de contribución
                    if (isset($_POST['porcentaje_cont']))
                    {
                        if ($_POST['porcentaje_cont'] == NULL || $_POST['porcentaje_cont'] == "")
                        {
                            $porcentaje_cont = NULL;
                        }
                        else
                        {
                            $porcentaje_cont = $_POST['porcentaje_cont'];
                        }
                    }
                    else
                    {
                        $porcentaje_cont = NULL;
                    }

                    //ACTUALIZACIÓN 16-11-17: Nuevos atributos de control
                    if (isset($_POST['key_control']))
                    {
                        if ($_POST['key_control'] == NULL || $_POST['key_control'] == "")
                        {
                            $key_control = NULL;
                        }
                        else
                        {
                            $key_control = $_POST['key_control'];
                        }
                    }
                    else
                    {
                        $key_control = NULL;
                    }

                    if (isset($_POST['objective']))
                    {
                        if ($_POST['objective'] == NULL || $_POST['objective'] == "")
                        {
                            $objective = NULL;
                        }
                        else
                        {
                            $objective = $_POST['objective'];
                        }
                    }
                    else
                    {
                        $objective = NULL;
                    }

                    if (isset($_POST['establishment']))
                    {
                        if ($_POST['establishment'] == NULL || $_POST['establishment'] == "")
                        {
                            $establishment = NULL;
                        }
                        else
                        {
                            $establishment = $_POST['establishment'];
                        }
                    }
                    else
                    {
                        $establishment = NULL;
                    }

                    if (isset($_POST['application']))
                    {
                        if ($_POST['application'] == NULL || $_POST['application'] == "")
                        {
                            $application = NULL;
                        }
                        else
                        {
                            $application = $_POST['application'];
                        }
                    }
                    else
                    {
                        $application = NULL;
                    }

                    if (isset($_POST['supervision']))
                    {
                        if ($_POST['supervision'] == NULL || $_POST['supervision'] == "")
                        {
                            $supervision = NULL;
                        }
                        else
                        {
                            $supervision = $_POST['supervision'];
                        }
                    }
                    else
                    {
                        $supervision = NULL;
                    }

                    if (isset($_POST['objective']))
                    {
                        if ($_POST['objective'] == NULL || $_POST['objective'] == "")
                        {
                            $objective = NULL;
                        }
                        else
                        {
                            $objective = $_POST['objective'];
                        }
                    }
                    else
                    {
                        $objective = NULL;
                    }

                    if (isset($_POST['test_plan']))
                    {
                        if ($_POST['test_plan'] == NULL || $_POST['test_plan'] == "")
                        {
                            $test_plan = NULL;
                        }
                        else
                        {
                            $test_plan = $_POST['test_plan'];
                        }
                    }
                    else
                    {
                        $test_plan = NULL;
                    }

                    
                    $control->name = $_POST['name'];
                    $control->description = $description;
                    $control->type = $type;
                    //$control->evidence = $evidence;
                    $control->periodicity = $periodicity;
                    $control->purpose = $purpose;
                    //$control->stakeholder_id = $stakeholder;
                    $control->expected_cost = $expected_cost;
                    //$control->porcentaje_cont = $porcentaje_cont;
                    $control->establishment = $establishment;
                    $control->application = $application;
                    $control->supervision = $supervision;
                    $control->key_control = $key_control;
                    $control->test_plan = $test_plan;
                    $control->save();

                    //ACTUALIZACION 05-07-2017: Para Coca Cola Andina, calcularemos aquí el valor del control y almacenaremos porcentaje de resultado (según evaluaciones y autoevaluaciones) en tabla control_eval_risk_temp

                    //Guardamos en control_eval_risk_temp valor del control (autoevaluación)
                    //primero seteamos en 0 las otras evaluaciones (si es que hay)

                    //ACT 19-04-18: Obtenemos control_organization
                    $ctrl_org = \Ermtool\ControlOrganization::getByCO($control->id,$_POST['org_id']);

                    DB::table('control_eval_risk_temp')
                        ->where('control_id','=',$ctrl_org->id)
                        ->update(['status' => 0]);
                        
                    DB::table('control_eval_risk_temp')
                            ->insert([
                                'result' => $_POST['porcentaje_cont'],
                                'control_organization_id' => $ctrl_org->id,
                                'auto_evaluation' => 1,
                                'status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);

                    //ahora calcularemos el valor de el o los riesgos a los que apunte este control
                    $eval_risk = $this->calcControlledRiskAutoeval($GLOBALS['id1'],$_POST['org_id'],date('Y'),date('m'),date('d'));

                    //ACTUALIZACIÓN 03-04-17: primero eliminamos los riesgos antiguos para no repetir
                    //$control_organization_risk_id = \Ermtool\Control::getControlOrganizationRisk($control->id,$_POST['org_id']);

                    //ACTUALIZACIÓN 04-12-17: Insertamos stakeholder en control_organization_risk
                    //ACT 16-04-18: También la evidencia y el porcentaje de contribución
                    /*
                    DB::table('control_organization_risk')
                                ->where('id','=',$control_organization_risk_id[0]->id)
                                ->update([
                                    'stakeholder_id' => $stakeholder,
                                    'evidence' => $evidence,
                                    'cont_percentage' => $porcentaje_cont,
                                    'comments' => $comments
                                ]);*/

                    //ACT 17-04-18: Porcentaje de contribución en control_organization
                    $ctrl_org->stakeholder_id = $stakeholder;
                    $ctrl_org->evidence = $evidence;
                    $ctrl_org->cont_percentage = $porcentaje_cont;
                    $ctrl_org->comments = $comments;
                    $ctrl_org->save();


                    //guardamos archivos de evidencia (si es que hay)
                    if($GLOBALS['evidencedoc'] != NULL)
                    {
                        foreach ($GLOBALS['evidencedoc'] as $evidencedoc)
                        {
                            if ($evidencedoc != NULL)
                            {
                                //ACT 24-04-18: Ahora se guardará control_org
                                //upload_file($evidencedoc,'controles',$control_id);
                                upload_file($evidencedoc,'controles_org',$ctrl_org->id);
                            }
                        }                    
                    }
                    
                    if (isset($_POST['select_riesgos'])) //sólo realizar si es que se están agregando riesgos asociados al control
                    {
                        foreach ($control_organization_risk_id as $c)
                        {
                            DB::table('control_organization_risk')
                                ->where('id','=',$c->id)
                                ->delete();
                        }

                        //guardamos riesgos de proceso o de negocio
                        foreach ($_POST['select_riesgos'] as $org_risk_id)
                        {   
                            DB::table('control_organization_risk')
                                ->insert([
                                    'organization_risk_id' => $org_risk_id,
                                    'control_id' => $control->id,
                                    //'stakeholder_id' => $stakeholder,
                                    //'evidence' => $evidence,
                                    //'cont_percentage' => $porcentaje_cont,
                                    //'comments' => $comments
                                    ]);
                        }
                    }
                    //ACTUALIZACIÓN 16-11-17: Agregamos estados financieros
                    //vemos si el estado financiero ya existe
                    $fstemp =\Ermtool\Financial_statement::getFSByControl($ctrl_org->id);
                    
                    foreach ($fstemp as $fs)
                    {
                        //borramos estados financieros anteriores
                        DB::table('control_financial_statement')
                                ->where('id','=',$fs->control_financial_statement_id)
                                ->delete();
                    }

                    //ahora agregamos los posibles agregados
                    if (isset($_POST['financial_statement_id']))
                    {
                        foreach ($_POST['financial_statement_id'] as $fs)
                        {
                            DB::table('control_financial_statement')
                            ->insert([
                                'control_organization_id' => $ctrl_org->id,
                                'financial_statement_id' => $fs
                            ]);
                        }
                    }

                    //Vemos si se están creando nuevos estados financieros
                    $i = 1;

                    while (isset($_POST['new_fs_'.$i]))
                    {
                        if ($_POST['new_fs_'.$i] != '' && $_POST['new_fs_'.$i] != NULL)
                        {
                            $fs = \Ermtool\Financial_statement::create([
                                    'name' => $_POST['new_fs_'.$i],
                                    'status' => 0
                                ]);

                            //agregamos enlace
                            DB::table('control_financial_statement')
                            ->insert([
                                'control_id' => $ctrl_org->id,
                                'financial_statement_id' => $fs->id
                            ]);
                        }

                        $i += 1;
                    }
                    
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Control successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Control actualizado correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el control con Id: '.$GLOBALS['id1'].' llamado: '.$_POST['name'].', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('controles.index2?organization_id='.$_POST['org_id']);
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
    public function destroy($id,$org)
    {
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            global $org1;
            $org1 = $org;
            $res = 1;

            DB::transaction(function() {
                $logger = $this->logger;
                
                //ACT 18-04-18: SE DEBE HACER DE NUEVO
                //ACT 23-04-18: Obtenemos control_organization

                $co = \Ermtool\ControlOrganization::getByCO($GLOBALS['id1'],$GLOBALS['org1'])
                ;

                //vemos si tiene evaluaciones
                $rev = DB::table('control_evaluation')
                        ->where('control_organization_id','=',$co->id)
                        ->select('id')
                        ->get();

                if (empty($rev))
                {
                    //ahora vemos si tiene issues
                    $rev = DB::table('issues')
                        ->where('control_id','=',$GLOBALS['id1'])
                        ->where('organization_id','=',$GLOBALS['org1'])
                        ->select('id')
                        ->get();

                    if (empty($rev))
                    {
                        //audit_tests
                        //ACTUALIZACIÓN 25-01-17: Ahora audit_test se relaciona con control a través de la tabla audit_test_control
                        //ACT 23-04-18: Ahora se relacionará a través de control_organization
                        $rev = DB::table('audit_test_control')
                            ->where('control_organization_id','=',$co->id)
                            ->select('id')
                            ->get();

                        if (empty($rev))
                        {
                            //ACTUALIZACIÓN 26-07-17: ahora vemos si existe en control_eval_risk_temp
                            //ACT 23-04-18: Ahora se relacionará a través de control_organization
                            $rev = DB::table('control_eval_risk_temp')
                                    ->where('control_organization_id','=',$co->id)
                                    ->select('id')
                                    ->get();

                            if (!empty($rev))
                            {
                                //ACTUALIZACIÓN 18-07-17: Si tiene evaluaciones
                                //ACTUALIZACIÓN 18-07-17: Además, debemos eliminar de control_eval_risk_temp (para Coca Cola Andina), y volver a calcular el valor de riesgo controlado (riesgo residua)
                                DB::table('control_eval_risk_temp')
                                        ->where('control_organization_id','=',$co->id)
                                        ->delete();

                                //obtenemos todos los riesgos asociados al control (para posteriormente, volver a cálcular su valor residual) que sean de esta organización
                                $risks = DB::table('risks')
                                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                                    ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                                    ->where('control_organization_risk.control_id','=',$GLOBALS['id1'])
                                    ->where('organization_risk.organization_id','=',$GLOBALS['org1'])
                                    ->select('risks.id')
                                    ->get(); 
                            }

                            //ACT 23-04-18: Vemos si tiene evaluaciones en control_eval_temp2
                            $rev = DB::table('control_eval_temp2')
                                    ->where('control_organization_id','=',$co->id)
                                    ->select('id')
                                    ->get();

                            //variable para ver si se debe recalcular eval
                            $rec = 0;
                            if (!empty($rev))
                            {
                                DB::table('control_eval_temp2')
                                    ->where('control_organization_id','=',$co->id)
                                    ->delete();

                                $rec = 1;
                            }

                            $name = \Ermtool\Control::name($GLOBALS['id1']);
                            //se puede borrar
                            //ACTUALIZACIÓN 03-04-17: Obtenemos control_organization_risk
                            $ctrl_org_risks = \Ermtool\Control::getControlOrganizationRisk($GLOBALS['id1'],$GLOBALS['org1']);

                            foreach ($ctrl_org_risks as $ctrl_org_risk)
                            {
                                DB::table('control_organization_risk')
                                    ->where('id','=',$ctrl_org_risk->id)
                                    ->delete();
                            }

                            //ACT 18-04-18: Eliminamos de control_organization
                            $ctrl_org = \Ermtool\ControlOrganization::where('id','=',$co->id)->delete();

                            //verificamos que no hayan más ctrl_org_risk para otras organizaciones, si no hay se puede borrar el control
                            $ctrl_risk = DB::table('control_organization_risk')
                                            ->where('control_id','=',$GLOBALS['id1'])
                                            ->select('id')
                                            ->get();

                            if (empty($ctrl_risk)) //se puede borrar control
                            {
                                //ACTUALIZACIÓN 16-11-17: Eliminamos también asociación con estados financieros
                                DB::table('control_financial_statement')
                                    ->where('control_id','=',$GLOBALS['id1'])
                                    ->delete();

                                //ACT 23-04-18: Eliminamos tambien de control_organization
                                DB::table('control_organization')
                                    ->where('control_id','=',$GLOBALS['id1'])
                                    ->delete();

                                DB::table('controls')
                                    ->where('id','=',$GLOBALS['id1'])
                                    ->delete();
                            }

                            //ahora eliminamos las evidencias (si es que existen)
                            //$docs = new Documentos;
                            //$docs->deleteFiles('controles',$GLOBALS['id1'],NULL);
                            eliminarArchivo($GLOBALS['id1'],3,NULL);

                            //volvemos a calcular riesgo residual para cada uno de los riesgos obtenidos anteriormente
                            if (isset($risks))
                            {
                                foreach ($risks as $risk)
                                {
                                    if ($rec == 1)
                                    {
                                        $this->calcResidualRisk($GLOBALS['org1'],$risk->id,date('Y'),date('m'),date('d'));    
                                    }

                                    //ACT 23-04-18: También calculamos residual autoeval
                                    $this->calcResidualRiskAutoeval($GLOBALS['org1'],$risk->id,date('Y'),date('m'),date('d'));
                                }
                            }

                            $GLOBALS['res'] = 0;

                            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el control con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
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
    //index para evaluación de controles
    public function indexEvaluacion()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                /*$stakeholders = array();
                //$controles = \Ermtool\Control::lists('name','id');
                //stakeholders posibles responsables plan de acción
                $stakes = DB::table('stakeholders')->select('id','name','surnames')->get();
                $i = 0;
                foreach ($stakes as $stake)
                {
                    $stakeholders[$i] = [
                        'id' => $stake->id,
                        'name' => $stake->name.' '.$stake->surnames,
                    ];
                    $i += 1;
                } */

                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.evaluar',['organizations' => $organizations]);
                }
                else
                {
                    return view('controles.evaluar',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACTUALIZADO MODO DE EVALUAR CONTROLES EL 14-11-2016: Ahora se tendrá una lista con los datos del control
    public function indexEvaluacion2()
    {
        try
        {
            //print_r($_GET);
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //obtenemos datos del control 
                $control = \Ermtool\Control::find($_GET['control_id']);

                //ACT 26-03-18: Stakeholder está dentro de control_organization_risk
                //if ($control->stakeholder_id == NULL)

                //Obtenemos stakeholder
                $stake = \Ermtool\Control::getStakeholder($control->id,$_GET['organization_id']);

                if ($stake->id == NULL)
                {
                    $stakeholder = NULL;
                }
                else
                {
                    $stakeholder = \Ermtool\Stakeholder::getName($stake->id);
                }

                //buscamos datos de cada una de las últimas pruebas (independiente de si se está editando o creando una nueva)
                //ACT 28-03-18: Ahora las pruebas serán dinámicas, por lo que se obtendrán de la tabla evaluation_tests

                $tests = \Ermtool\Evaluation_test::all();
                $last_evaluations = array();
                $i = 0;

                foreach ($tests as $t)
                {
                    //ACT 18-04-18: Obtenemos prueba desde control_organization
                    $ctrl_org = \Ermtool\ControlOrganization::getByCO($_GET['control_id'],$_GET['organization_id']);
                    $last_evaluations[$i] = \Ermtool\Control_evaluation::getLastEvaluation($ctrl_org->id,$t->id);
                    $i += 1;
                }
                //$last_diseno = \Ermtool\Control_evaluation::getLastEvaluation($_GET['control_id'],0);
                //$last_efectividad = \Ermtool\Control_evaluation::getLastEvaluation($_GET['control_id'],1);
                //$last_sustantiva = \Ermtool\Control_evaluation::getLastEvaluation($_GET['control_id'],2);
                //$last_cumplimiento = \Ermtool\Control_evaluation::getLastEvaluation($_GET['control_id'],3);

                if (isset($_GET['organization_id']))
                {
                	$risks = \Ermtool\Risk::getRisksFromControl($_GET['organization_id'],$_GET['control_id']);
                }
                else
                {
                	$risks = array();
                }   

                //if ($_GET['control_kind'] == 1) //control de negocio
                //{
                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.evaluar2',['control' => $control,'risks' => $risks,'stakeholder' => $stakeholder,'last_evaluations' => $last_evaluations,'org_id' => $_GET['organization_id']]);
                }
                else
                {
                    return view('controles.evaluar2',['control' => $control,'risks' => $risks,'stakeholder' => $stakeholder,'last_evaluations' => $last_evaluations,'org_id' => $_GET['organization_id']]);
                }
                //}
                /*else if ($_GET['control_kind'] == 2) //control de proceso
                {
                    //$process = \Ermtool\Process::find($_GET['process_id']);

                    //$subprocess = \Ermtool\Subprocess::find($_GET['subprocess_id']);

                    if (Session::get('languaje') == 'en')
                    {
                        return view('en.controles.evaluar2',['control' => $control, 'risks' => $risks,'stakeholder' => $stakeholder,'last_diseno' => $last_diseno,'last_efectividad' => $last_efectividad,'last_sustantiva' => $last_sustantiva,'last_cumplimiento' => $last_cumplimiento,'kind' => $_GET['control_kind']]);
                    }
                    else
                    {
                        return view('controles.evaluar2',['control' => $control, 'risks' => $risks,'stakeholder' => $stakeholder,'last_diseno' => $last_diseno,'last_efectividad' => $last_efectividad,'last_sustantiva' => $last_sustantiva,'last_cumplimiento' => $last_cumplimiento,'kind' => $_GET['control_kind']]);
                    }
                }*/
                
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function createEvaluacion($id,$kind,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //responsables del plan de acción (si es que la prueba es inefectiva)
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                $control = \Ermtool\Control::name($id);

                //ACT 28-03-18: Obtenemos kind de tabla evalution_test
                $kind2 = \Ermtool\Evaluation_test::find($kind);

                if (Session::get('languaje') == 'en')
                {/*
                    switch ($kind) {
                        case 0:
                            $kind2 = 'Design test';
                            break;
                        case 1:
                            $kind2 = 'Operational effectiveness test';
                            break;
                        case 2:
                            $kind2 = 'Sustantive test';
                            break;
                        case 3:
                            $kind2 = 'Compliance test';
                            break;
                        default:
                            # code...
                            break;
                    }*/
                    return view('en.controles.create_evaluation',['kind' => $kind, 'kind2' => $kind2, 'id' => $id,'control' => $control,'stakeholders' => $stakeholders,'control_evaluation'=>NULL]);
                }
                else
                {/*
                    switch ($kind) {
                        case 0:
                            $kind2 = 'Prueba de diseño';
                            break;
                        case 1:
                            $kind2 = 'Prueba de efectividad operativa';
                            break;
                        case 2:
                            $kind2 = 'Prueba sustantiva';
                            break;
                        case 3:
                            $kind2 = 'Prueba de cumplimiento';
                            break;
                        default:
                            # code...
                            break;
                    }*/
                    return view('controles.create_evaluation',['kind' => $kind, 'kind2' => $kind2, 'id' => $id,'control' => $control,'stakeholders' => $stakeholders,'control_evaluation'=>NULL,'org_id' => $org]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function editEvaluacion($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $eval = \Ermtool\Control_evaluation::find($id);
                //responsables del plan de acción (si es que la prueba es inefectiva)
                $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
                $control = \Ermtool\Control::name($eval->control_id);

                //ACT 28-03-18: Obtenemos kind de tabla evalution_test
                $kind = \Ermtool\Evaluation_test::find($eval->evaluation_test_id);

                if (Session::get('languaje') == 'en')
                {/*
                    switch ($eval->kind) {
                        case 0:
                            $kind = 'Design test';
                            break;
                        case 1:
                            $kind = 'Operational effectiveness test';
                            break;
                        case 2:
                            $kind = 'Sustantive test';
                            break;
                        case 3:
                            $kind = 'Compliance test';
                            break;
                        default:
                            # code...
                            break;
                    }*/

                    //ACT 18-04-18: Obtenemos organization_id desde control_organization
                    $ctrl_org = \Ermtool\ControlOrganization::find($eval->control_organization_id);

                    return view('en.controles.edit_evaluation',['eval'=>$eval,'control'=>$control,'kind'=>$kind,'stakeholders' => $stakeholders,'control_evaluation'=>$eval->id,'id'=>$ctrl_org->control_id,'org_id' => $ctrl_org->organization_id]);
                }
                else
                {/*
                    switch ($eval->kind) {
                        case 0:
                            $kind = 'Prueba de diseño';
                            break;
                        case 1:
                            $kind = 'Prueba de efectividad operativa';
                            break;
                        case 2:
                            $kind = 'Prueba sustantiva';
                            break;
                        case 3:
                            $kind = 'Prueba de cumplimiento';
                            break;
                        default:
                            # code...
                            break;
                    }*/
                    //ACT 18-04-18: Obtenemos organization_id desde control_organization
                    $ctrl_org = \Ermtool\ControlOrganization::find($eval->control_organization_id);

                    return view('controles.edit_evaluation',['eval'=>$eval,'control'=>$control,'kind'=>$kind,'stakeholders' => $stakeholders,'control_evaluation'=>$eval->id,'id'=>$ctrl_org->control_id,'org_id' => $ctrl_org->organization_id]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function storeEvaluation(Request $request)
    {
        try
        {
            //print_r($_POST);
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                DB::transaction(function() {

                    $logger = $this->logger2;

                    if (isset($_POST['comments']) && $_POST['comments'] != '')
                    {
                        $comments = $_POST['comments'];
                    }
                    else
                    {
                        $comments = NULL;
                    }
                    //ACT 28-03-18: Ya no existe kind
                    //ACT 18-04-18: Insertamos control_organization_id
                    $ctrl_org = \Ermtool\ControlOrganization::getByCO($_POST['control_id'],$_POST['org_id']);

                    $eval = \Ermtool\Control_evaluation::create([
                                'control_organization_id' => $ctrl_org->id,
                                //'control_id' => $_POST['control_id'],
                                'description' => $_POST['description'],
                                'results' => $_POST['results'],
                                'comments' => $comments,
                                //'kind' => $_POST['kind'],
                                'evaluation_test_id' => $_POST['kind'],
                                'status' => 1,
                                //'organization_id' => $_POST['org_id']
                            ]);

                    global $eval2;
                    $eval2 = $eval;

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Control evaluation was successfully saved');
                    }
                    else
                    {
                        Session::flash('message','Evaluación de control generada correctamente');
                    }

                    $kind = \Ermtool\Evaluation_test::name($_POST['kind']);

                    $name = \Ermtool\Control::name($_POST['control_id']);

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha realizado una prueba '.$kind->name.' para el control con Id: '.$_POST['control_id'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('editar_evaluacion.'.$GLOBALS['eval2']->id);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function updateEvaluation($id)
    {
        try
        {
            global $id1;
            $id1 = $id;
            DB::transaction(function() {

                $logger = $this->logger2;
                $evaluation = \Ermtool\Control_evaluation::find($GLOBALS['id1']);

                $evaluation->description = $_POST['description'];
                $evaluation->results = $_POST['results'];

                if ($_POST['results'] == 1)
                {
                    if ($_POST['comments'] != '')
                    {
                        $evaluation->comments = $_POST['comments'];
                    }
                    else
                    {
                        $evaluation->comments = NULL;
                    }
                }
                else
                {
                    $evaluation->comments = NULL;
                }

                $evaluation->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Control evaluation was successfully updated');
                }
                else
                {
                    Session::flash('message','Evaluación de control actualizada correctamente');
                }

                switch ($evaluation->kind) {
                        case 0:
                            $kind = 'de Diseño';
                            break;
                        case 1:
                            $kind = 'de Efectividad operativa';
                            break;
                        case 2:
                            $kind = 'Sustantiva';
                            break;
                        case 3:
                            $kind = 'de Cumplimiento';
                            break;
                        default:
                            # code...
                            break;
                    }

                    $name = \Ermtool\Control::name($evaluation->control_id);

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado la prueba '.$kind.' para el control con Id: '.$evaluation->control_id.' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });

            return Redirect::to('editar_evaluacion.'.$id);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /*ACTUALIZACIÓN 21-11-16 función para cerrar una prueba; al cerrar una prueba se re evalua el valor del control (para ver si es efectivo o inefectivo), y su resultado es almacenado en la tabla
    control_eval_risk_temp, para luego re calcular el valor del riesgo controlado (a través de todos los controles que se encuentren en la tabla control_eval_risk_temp y que apunten a los riesgos creados en el sistema) */

    /* ACTUALIZACIÓN 04-07-2017: Esta función no se utilizará en Coca Cola, y serán modificadas las funciones calcControlValue y calcControlledRisk, de modo que cada vez que se cree o actualice un control, se calculará el promedio de los controles, y luego este valor será aplicado a todos los riesgos asociados */

    /* ACT 29-03-18: Se vuelve a activar esta función: Ahora se almacenará los valores en probabilidad e impacto como porcentaje (según pesos específicos almacenados en control_specific_weight). Luego se re-calculará valor residual de los riesgos asociados a este control y se almacenará en la tabla residual_risk. */

    public function closeEvaluation($id)
    {
        global $id1;
        $id1 = $id;

        DB::transaction(function() {
            $logger = $this->logger2;
            //primero que todo, cerramos el estado de la prueba de id = $id
            $eval = \Ermtool\Control_evaluation::find($GLOBALS['id1']);
            $eval->status = 2;
            $eval->save();
            //ahora calcularemos el resultado del control
            $control = $this->calcControlValue($eval->control_organization_id);

            //ahora calcularemos el valor de el o los riesgos a los que apunte este control
            //obtenemos datos de control_organization
            $co = \Ermtool\ControlOrganization::find($eval->control_organization_id);

            $eval_risk = $this->calcControlledRisk($co->control_id,$co->organization_id);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Control test was successfully closed');
            }
            else
            {
                Session::flash('message','La prueba fue cerrada satisfactoriamente');
            }

            //Obtenemos nombre de la prueba
            $eval_test = \Ermtool\Evaluation_test::name($eval->evaluation_test_id);

            //obtenemos datos de control y de org
            $org = \Ermtool\Organization::getOrganizationByCO($eval->control_organization_id);
            $name = \Ermtool\Control::nameByCO($eval->control_organization_id);

            //$logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha cerrado la prueba '.$eval_test->name.' para el control (asociado a la organización '.$org->name.' con Id: '.$eval->control_organization_id.' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
        });

        //return 0;
    }
    
    //función que retornará documento de evidencia subidos, junto al control que corresponden
    public function docs($id)
    {
        try
        {
            //obtenemos todos los archivos
            $files = Storage::files('controles');
            foreach ($files as $file)
            {
                //echo $file."<br>";
                //vemos buscamos por id del archivo que se esta buscando
                $file_temp = explode('___',$file);
                //sacamos la extensión
                $file_temp2 = explode('.',$file_temp[1]);
                if ($file_temp2[0] == $id)
                {
                    //$file = Storage::get($file);
                    return response()->download('../storage/app/'.$file);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
     //   $extension = Storage::
     //   $name = $name.'.'.$;
        //$evidencia = Storage::get('controles/'.$id.'.docx');
        
     //   return response()->download('../storage/app/controles/'.$name);
    }
    //función para reportes básicos->matriz de control
    public function matrices()
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
                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.matrices',['organizations'=>$organizations]);
                }
                else
                {
                    return view('reportes.matrices',['organizations'=>$organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    /***********
    * función generadora de matriz de control
    ***********/
    public function generarMatriz($value,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $i = 0; //contador de controles/subprocesos o controles/objetivos
                $datos = array();
                
                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                {
                    $value = $_GET['kind'];
                    $org = $_GET['organization_id'];
                }
                //obtenemos controles
                $controls = \Ermtool\Control::getControls($org);
                foreach ($controls as $control)
                {
                
                        $risk_obj = NULL;
                        $risk_sub = NULL;
                        // -- seteamos datos --//
                    if (Session::get('languaje') == 'en') //Realizamos traducciones aquí y no en vistas para el caso de exportación a excel
                    {
                        if ($control->type === NULL)
                        {
                            $type = "Not defined";
                        }
                        else
                        {
                            //Seteamos type. 0=Manual, 1=Semi-automático, 2=Automático
                            switch($control->type)
                            {
                                case 0:
                                    $type = "Manual";
                                    break;
                                case 1:
                                    $type = "Semi-automatic";
                                    break;
                                case 2:
                                    $type = "Automatic";
                            }
                        }
                        if ($control->periodicity === NULL)
                        {
                            $periodicity = "Not defined";
                        }
                        else
                        {
                            //Seteamos periodicity. 0=Diario, 1=Semanal, 2=Mensual, 3=Semestral, 4=Anual
                            switch ($control->periodicity)
                            {
                                case 0:
                                    $periodicity = "Daily";
                                    break;
                                case 1:
                                    $periodicity = "Weekly";
                                    break;
                                case 2:
                                    $periodicity = "Monthly";
                                    break;
                                case 3:
                                    $periodicity = "Biannual";
                                    break;
                                case 4:
                                    $periodicity = "Annual";
                                    break;
                                case 5:
                                    $periodicity = "Each time it occurs";
                                    break;
                                case 6:
                                    $periodicity = "Quarterly";
                                    break;
                            }
                        }
                        if ($control->purpose === NULL)
                        {
                            $purpose = "Not defined";
                        }
                        else
                        {
                            //Seteamos purpose. 0=Preventivo, 1=Detectivo, 2=Correctivo
                            switch ($control->purpose)
                            {
                                case 0:
                                    $purpose = "Preventive";
                                case 1:
                                    $purpose = "Detective";
                                case 2:
                                    $purpose = "Corrective";
                            }
                        }
                        if ($control->expected_cost === NULL)
                        {
                            $expected_cost = "Not defined";
                        }
                        else
                        {
                            $expected_cost = $control->expected_cost;
                        }
                        //ACT 16-04-18: Ahora evidencia depende de la organización, por eso tiene otro nombre
                        if ($control->control_evidence === NULL || $control->control_evidence == "")
                        {
                            $evidence = "Without evidence";
                        }
                        else
                        {
                            $evidence = $control->control_evidence;
                        }
                        
                        //Seteamos responsable del control
                        $stakeholder = \Ermtool\Stakeholder::find($control->stakeholder_id);
                        if ($stakeholder)
                        {
                            $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                        }
                        else
                        {
                            $stakeholder2 = "Not assigned";
                        }
                    }
                    else
                    {
                        if ($control->type === NULL)
                        {
                            $type = "No definido";
                        }
                        else
                        {
                            //Seteamos type. 0=Manual, 1=Semi-automático, 2=Automático
                            switch($control->type)
                            {
                                case 0:
                                    $type = "Manual";
                                    break;
                                case 1:
                                    $type = "Semi-automático";
                                    break;
                                case 2:
                                    $type = "Autom&aacute;tico";
                            }
                        }
                        if ($control->periodicity === NULL)
                        {
                            $periodicity = "No definido";
                        }
                        else
                        {
                            //Seteamos periodicity. 0=Diario, 1=Semanal, 2=Mensual, 3=Semestral, 4=Anual
                            switch ($control->periodicity)
                            {
                                case 0:
                                    $periodicity = "Diario";
                                    break;
                                case 1:
                                    $periodicity = "Semanal";
                                    break;
                                case 2:
                                    $periodicity = "Mensual";
                                    break;
                                case 3:
                                    $periodicity = "Semestral";
                                    break;
                                case 4:
                                    $periodicity = "Anual";
                                    break;
                                case 5:
                                    $periodicity = "Cada vez que ocurra";
                                    break;
                                case 6:
                                    $periodicity = "Trimestral";
                                    break;
                            }
                        }
                        if ($control->purpose === NULL)
                        {
                            $purpose = "No definido";
                        }
                        else
                        {
                            //Seteamos purpose. 0=Preventivo, 1=Detectivo, 2=Correctivo
                            switch ($control->purpose)
                            {
                                case 0:
                                    $purpose = "Preventivo";
                                case 1:
                                    $purpose = "Detectivo";
                                case 2:
                                    $purpose = "Correctivo";
                            }
                        }
                        if ($control->expected_cost === NULL)
                        {
                            $expected_cost = "No definido";
                        }
                        else
                        {
                            $expected_cost = $control->expected_cost;
                        }
                        if ($control->control_evidence === NULL || $control->control_evidence == "")
                        {
                            $evidence = "Sin evidencia";
                        }
                        else
                        {
                            $evidence = $control->control_evidence;
                        }
                        
                        //Seteamos responsable del control
                        $stakeholder = \Ermtool\Stakeholder::find($control->stakeholder_id);
                        if ($stakeholder)
                        {
                            $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                        }
                        else
                        {
                            $stakeholder2 = "No asignado";
                        }
                    }
                        /* IMPORTANTE!!!
                            Los nombres de las variables serán guardados en español para mostrarlos
                            en el archivo excel que será exportado
                        */
                            //obtenemos riesgo - objetivo - organización o riesgo - subproceso - organización para cada control
                            //ACTUALIZACIÓN 03-04: control_risk_subprocess control_objective_risk no existen

                    $risks = \Ermtool\Risk::getRisksFromControl($org,$control->id);

                    if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //para ver como mostraremos los datos
                    {
                        $risks2 = array();
                        $j = 0;
                    }
                    else
                    {
                        $risks2 = '';
                    }
                    $last = end($risks);
                    //ahora obtenemos subprocesos (si es que hay) y asignamos riesgos
                    foreach ($risks as $risk)
                    {
                        if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                        {
                            
                            $risks2[$j] = $risk->name;
                            $j += 1;    
                        }
                        else
                        {
                            if ($risk == $last)
                            {
                                $risks2 .= $risk->name;
                            }
                            else
                            {
                                $risks2 .= $risk->name.', ';
                            }
                        }
                    }

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($control->description,0,100);
                            
                    if ($value == 0) // matriz de controles de proceso
                    {

                        $subprocesses = \Ermtool\Subprocess::getSubprocessesFromControl($org,$control->id);

                        if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //para ver como mostramos los datos
                        {
                            $sub = array();
                            $j = 0;
                        }
                        else
                        {
                            $sub = '';
                        }
                        if ($subprocesses != NULL) //si es NULL, significa que el control que se está recorriendo es de negocio
                        {
                            $last = end($subprocesses);
                            //seteamos cada riesgo, subproceso y organización
                            foreach ($subprocesses as $subprocess)
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                {
                                    $sub[$j] = $subprocess->name;
                                    $j+=1;
                                }
                                else
                                {
                                    
                                    if ($last == $subprocess)
                                    {
                                        $sub .= $subprocess->name;
                                    }
                                    else
                                    {
                                        $sub .= $subprocess->name.', ';
                                    }
                                }
                            }

                            if (strstr($_SERVER["REQUEST_URI"],'genexcel')) 
                            {
                            
                                if (Session::get('languaje') == 'en')
                                {
                                    $datos[$i] = [//'id' => $control->id,
                                            'Control' => $control->name,
                                            'Description' => $control->description,
                                            'Responsable' => $stakeholder2,
                                            'Kind' => $type,
                                            'Periodicity' => $periodicity,
                                            'Purpose' => $purpose,
                                            'Expected_cost' => $expected_cost,
                                            'Evidence' => $evidence,
                                            'Cont_percentage' => $control->cont_percentage,
                                            'Subprocesses' => $sub,
                                            'Risks' => $risks2];
                                }
                                else
                                {
                                    $datos[$i] = [//'id' => $control->id,
                                            'Control' => $control->name,
                                            'Descripción' => $control->description,
                                            'Responsable' => $stakeholder2,
                                            'Tipo' => $type,
                                            'Periodicidad' => $periodicity,
                                            'Propósito' => $purpose,
                                            'Costo_control' => $expected_cost,
                                            'Evidencia' => $evidence,
                                            'Porcentaje_contribución' => $control->cont_percentage,
                                            'Riesgos' => $risks2,
                                            'Subprocesos' => $sub];
                                }

                            }
                            else
                            {
                                
                                $datos[$i] = ['id' => $control->id,
                                        'Control' => $control->name,
                                        'Descripción' => $control->description,
                                        'Responsable' => $stakeholder2,
                                        'Tipo' => $type,
                                        'Periodicidad' => $periodicity,
                                        'Propósito' => $purpose,
                                        'Costo_control' => $expected_cost,
                                        'Evidencia' => $evidence,
                                        'porcentaje_cont' => $control->cont_percentage,
                                        'Riesgos' => $risks2,
                                        'Subprocesos' => $sub,
                                        'short_des' => $short_des];
                            }
                            $i += 1;
                        }
                    }
                    else if ($value == 1)
                    {
                        $objectives = \Ermtool\Objective::getObjectivesFromControl($org,$control->id);

                        if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //como mostramos los datos
                        {
                            $objs = array();
                            $j = 0;
                        }
                        else
                        {
                            $objs = '';
                        }
                        
                        if ($objectives != NULL) //si es NULL, significa que el control que se está recorriendo es de proceso
                        {
                            $last = end($objectives);
                            //seteamos cada riesgo, objetivo y organización
                            foreach ($objectives as $obj)
                            {
                                if (!strstr($_SERVER["REQUEST_URI"],'genexcel'))
                                {
                                    $objs[$j] = $obj->name;
                                    $j+=1;        
                                }
                                else
                                {
                                    if ($obj == $last)
                                    {
                                        $objs .= $obj->name;
                                    }
                                    else
                                    {
                                        $objs .= $obj->name.', ';
                                    }
                                }
                            }
                                    
                            if (strstr($_SERVER["REQUEST_URI"],'genexcel'))
                            {
                                if (Session::get('languaje') == 'en')
                                {
                                    $datos[$i] = [//'id' => $control->id,
                                            'Control' => $control->name,
                                            'Description' => $control->description,
                                            'Responsable' => $stakeholder2,
                                            'Kind' => $type,
                                            'Periodicity' => $periodicity,
                                            'Purpose' => $purpose,
                                            'Expected_cost' => $expected_cost,
                                            'Evidence' => $evidence,
                                            'Cont_percentage' => $control->cont_percentage,
                                            'Objectives' => $objs,
                                            'Risks' => $risks2];
                                }
                                else
                                {
                                    $datos[$i] = [//'id' => $control->id,
                                            'Control' => $control->name,
                                            'Descripción' => $control->description,
                                            'Responsable' => $stakeholder2,
                                            'Tipo' => $type,
                                            'Periodicidad' => $periodicity,
                                            'Propósito' => $purpose,
                                            'Costo_control' => $expected_cost,
                                            'Evidencia' => $evidence,
                                            'Porcentaje_contribución' => $control->cont_percentage,
                                            'Objetivos' => $objs,
                                            'Riesgos' => $risks2];
                                }        
                                
                            }
                            else
                            {
                                $datos[$i] = ['id' => $control->id,
                                            'Control' => $control->name,
                                            'Descripción' => $control->description,
                                            'Responsable' => $stakeholder2,
                                            'Tipo' => $type,
                                            'Periodicidad' => $periodicity,
                                            'Propósito' => $purpose,
                                            'Costo_control' => $expected_cost,
                                            'Evidencia' => $evidence,
                                            'porcentaje_cont' => $control->cont_percentage,
                                            'Objetivos' => $objs,
                                            'Riesgos' => $risks2,
                                            'short_des' => $short_des];

                            }
                            $i += 1;
                        }
                    }
                }        
                
                if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
                {
                    return $datos;
                }
                else
                {
                    $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                    if (Session::get('languaje') == 'en')
                    { 
                        return view('en.reportes.matrices',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org]);
                    }
                    else
                    {
                        return view('reportes.matrices',['datos'=>$datos,'value'=>$value,'organizations'=>$organizations,'org_selected' => $org]);
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene los controles de una organización
    public function getControls($org)
    {
        try
        {
            $controls = array();
            //controles de negocio
            $controles = \Ermtool\Control::getBussinessControls($org,NULL);
            
            $i = 0;
            foreach ($controles as $control)
            {
                $controls[$i] = [
                    'id' => $control->id,
                    'name' => $control->name
                ];
                $i += 1;
            }
            //controles de proceso
            $controles = \Ermtool\Control::getProcessesControls($org,NULL);

            foreach ($controles as $control)
            {
                $controls[$i] = [
                    'id' => $control->id,
                    'name' => $control->name
                ];
                $i += 1;
            }
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getControls2($org,$type)
    {
        try
        {
            $controls = array();

            if ($type == 1)
            {
                //controles de negocio
                $controles = \Ermtool\Control::getBussinessControls($org,NULL);

                $i = 0;

                foreach ($controles as $control)
                {
                    $controls[$i] = [
                        'id' => $control['id'],
                        'name' => $control['name']
                    ];

                    $i += 1;
                }
            }
            else if ($type == 0)
            {
                //controles de proceso
                $controles = \Ermtool\Control::getProcessesControls($org,NULL);

                $i = 0;
                
                foreach ($controles as $control)
                {
                    $controls[$i] = [
                        'id' => $control['id'],
                        'name' => $control['name']
                    ];

                    $i += 1;
                }
            }
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene evaluación de control de id = $id
    public function getEvaluacion($id)
    {
        try
        {
            $evaluation = array();
            $max_update = NULL;
            //primero obtenemos fecha máxima de actualización de evaluaciones para el control
            $max_update = DB::table('control_evaluation')
                        ->where('control_id','=',$id)
                        ->max('updated_at');
            if ($max_update != NULL)
            {
                //ahora obtenemos los datos de la evaluación de fecha máxima
                $evals = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('updated_at','=',$max_update)
                            ->where('status','=',1)
                            ->select('*')
                            ->get();
                $i = 0;
                foreach ($evals as $eval)
                {
                    $evidence = getEvidences(3,$eval->id);
                    $evaluation[$i] = [
                            'id' => $eval->id,
                            'kind' => $eval->kind,
                            'results' => $eval->results,
                            'evidence' => $evidence, 
                        //    'comments' => $eval->comments,
                        ];
                    $i += 1;
                }
                return json_encode($evaluation);
            }
            else //retornamos NULL (max update será null si no hay evaluaciones)
            {
                return json_encode($max_update);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función obtiene datos de evaluación a través de id de la eval
    public function getEvaluacion2($id)
    {
        try
        {
            $evaluation = NULL;
            $eval = NULL;
            //obtenemos los datos de la evaluación
                $eval = DB::table('control_evaluation')
                            ->where('id','=',$id)
                            ->select('id','comments')
                            ->first();
            if ($eval != NULL)
            {
                if ($id != NULL)
                {
                    $evidence = getEvidences(3,$eval->id);
                }
                else
                {
                    $evidence = NULL;
                }
                $evaluation = [
                    'id' => $eval->id,
                    'comments' => $eval->comments,
                    'evidence' => $evidence, 
                ];
            }
                return json_encode($evaluation);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //función obtiene issue (si es que hay) a través de id de la eval
    public function getIssue($eval_id)
    {
        try
        {
            $issue = NULL;
            $eval = DB::table('control_evaluation')
                            ->where('id','=',$eval_id)
                            ->where('status','=',1)
                            ->select('issue_id')
                            ->first();
            $evidence = getEvidences(3,$eval_id);
            if($eval) //si es que hay evaluación => Puede ser que se esté agregando una nueva   
            {
                $issue = \Ermtool\Issue::find($eval->issue_id);
                $issue = [
                    'issue' => $issue,
                    'evidence' => $evidence,
                ];    
            }   
            return json_encode($issue);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene descripción del control (al evaluar)
    public function getDescription($control_id)
    {
        try
        {
            $description = \Ermtool\Control::where('id',$control_id)->value('description');
            return json_encode($description);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function indexGraficos()
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

                if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.controles_graficos',['organizations' => $organizations]);
                }
                else
                {
                    return view('reportes.controles_graficos',['organizations' => $organizations]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function indexGraficos2($value,$org)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $controls = array();
                $controls_temp = array();
                $no_ejecutados = array();
                $efectivos = 0;
                $inefectivos = 0;
                $j = 0; //contador para ids de efectivos e inefectivos
                $id_inefectivos = array();
                $id_efectivos = array();
                //primero seleccionamos id de controles de control_evaluation donde tengan status 2 (cerrado) y donde estos sean diferentes para que no se repitan
                /*$controles = DB::table('control_evaluation')
                                //->where('control_evaluation.status','=',2)
                                ->distinct()
                                ->get(['control_id as id']); */

                //ACT. 09-12-16: SELECCIONAMOS CONTROLES DE TABLA CONTROL_EVAL_TEMP2
                //ACT 18-04-18: Ahora se obtendrá control_organization
                if ($org == 0)
                {
                    $ctrl_org = \Ermtool\Control::getEvaluatedControls($_GET['organization_id']);
                }
                else
                {
                    $ctrl_org = \Ermtool\Control::getEvaluatedControls($org); //en el caso de que se esté generando excel, org tendrá valor
                }

                $i = 0;
                foreach ($ctrl_org as $co)
                {
                    //primero obtenemos fecha del último resultado de evaluación del control
                    $max_date = DB::table('control_eval_temp2')
                                    ->where('control_organization_id','=',$co->id)
                                    ->max('created_at');

                    $controls_temp[$i] = $co->control_id;
                    $i += 1;
                    //para cada uno vemos si son efectivos o inefectivos: Si al menos una de las pruebas es inefectiva, el control es inefectivo
                    //ACTUALIZACIÓN 22-11-16: Sólo verificaremos en tabla control_eval_risk_temp
                    $res = DB::table('control_eval_temp2')
                                ->where('control_organization_id','=',$co->id)
                                ->where('created_at','=',$max_date)
                                ->where('status','=',1)
                                ->select('probability','impact')
                                ->first();

                    if (!empty($res))
                    {
                        //ACT  26-06-18: Si la probabilidad calculada es menos a 50, el control se considerará inefectivo
                        if ($res->probability < 50 && $res->impact < 50)
                        {
                            array_push($id_inefectivos,$co->control_id);
                            $inefectivos += 1;
                        }
                        else
                        {
                            array_push($id_efectivos,$co->control_id);
                            $efectivos += 1;
                        }
                    }
                }
                //ahora en audit_tests y que no hayan sido encontrados en control_evaluation
                /*
                $controles = DB::table('audit_tests')
                                ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                                ->where('audit_tests.status','=',2)
                                ->whereNotIn('audit_test_control.control_id',$controls_temp)
                                ->distinct()
                                ->get(['control_id as id','results']);
                foreach ($controles as $control)
                {
                    $controls_temp[$i] = $control->id;
                    $i += 1;
                    if ($control->results == 0)
                    {
                        $inefectivos += 1;
                        array_push($id_inefectivos,$control->id);
                    }
                    else
                    {
                        $efectivos += 1;
                        array_push($id_efectivos,$control->id);
                    }
                } */
                //ahora obtenemos los datos de los controles seleccionados
                $i = 0;
                foreach ($controls as $id)
                {
                    $control = \Ermtool\Control::find($id);
                    //obtenemos resultado del control
                    //fecha de actualización del control
                    $updated_at = new DateTime($control->updated_at);
                    $updated_at = date_format($updated_at, 'd-m-Y');
                    $description = eliminarSaltos($control->description); 
                    foreach ($id_efectivos as $id_ef)
                    {
                        if ($id_ef == $control->id)
                        {
                            $controls[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                                'results' => 2
                            ];
                            $i += 1;
                        }
                    }
                    foreach ($id_inefectivos as $id_inef)
                    {
                        if ($id_inef == $control->id)
                        {
                            $controls[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                                'results' => 1
                            ];
                            $i += 1;
                        }
                    }
                    
                }
                //guardamos cantidad de ejecutados
                $cont_ejec = $i;
                //ahora obtenemos el resto de controles (para obtener los no ejecutados)

                $controles = \Ermtool\Control::whereNotIn('id',$controls_temp)
                            ->select('id','name','description','updated_at')
                            ->get();
                //guardamos en array
                $i = 0;
                foreach ($controles as $control)
                {
                    $updated_at = new DateTime($control->updated_at);
                    $updated_at = date_format($updated_at, 'd-m-Y');
                    $description = eliminarSaltos($control->description);  
                    $no_ejecutados[$i] = [
                                'id' => $control->id,
                                'name' => $control->name,
                                'description' => $description,
                                'updated_at' => $updated_at,
                            ];
                    $i += 1;
                }
                //guardamos cantidad de no ejecutados
                $cont_no_ejec = $i;
                //return json_encode($controls);
                //echo $cont_ejec.' y '.$cont_no_ejec;
                //echo $efectivos. ' y '.$inefectivos;
                //print_r($id_efectivos);
                //print_r($id_inefectivos);
                //print_r($no_ejecutados);
                if (strstr($_SERVER["REQUEST_URI"],'genexcelgraficos')) 
                {
                    $res2 = array();
                    if ($value == 1) //reporte excel de controles ejecutados
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res2[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res2[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 2) //reporte excel de controles no ejecutados
                    {
                        $i = 0;
                        foreach ($no_ejecutados as $control)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $res2[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res2[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 3) //reporte excel de controles efectivos
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if ($control['results'] == 2)
                            {
                                if (Session::get('languaje') == 'en')
                                {
                                    $res2[$i] = [
                                        'Name' => $control['name'],
                                        'Description' => $control['description'],
                                        'Updated date' => $control['updated_at'],
                                    ];
                                }
                                else
                                {
                                    $res2[$i] = [
                                        'Nombre' => $control['name'],
                                        'Descripción' => $control['description'],
                                        'Actualizado' => $control['updated_at'],
                                    ];
                                }    
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    else if ($value == 4) //reporte excel de controles no efectivos
                    {
                        $i = 0;
                        foreach ($controls as $control)
                        {
                            if ($control['results'] == 1)
                            {
                                if (Session::get('languaje') == 'en')
                                {
                                    $res2[$i] = [
                                        'Name' => $control['name'],
                                        'Description' => $control['description'],
                                        'Updated date' => $control['updated_at'],
                                    ];
                                }
                                else
                                {
                                    $res2[$i] = [
                                        'Nombre' => $control['name'],
                                        'Descripción' => $control['description'],
                                        'Actualizado' => $control['updated_at'],
                                    ];
                                }    
                            }
                            $i += 1;
                        }
                        return $res2;
                    }
                    
                }
                else
                {
                   if (Session::get('languaje') == 'en')
                    {
                        return view('en.reportes.controles_graficos',['controls'=>$controls,'no_ejecutados'=>$no_ejecutados,
                                                      'cont_ejec' => $cont_ejec,'cont_no_ejec'=>$cont_no_ejec,
                                                      'efectivos' => $efectivos,'inefectivos'=>$inefectivos,'org' => $_GET['organization_id']]);
                    }
                    else
                    {
                        return view('reportes.controles_graficos',['controls'=>$controls,'no_ejecutados'=>$no_ejecutados,
                                                      'cont_ejec' => $cont_ejec,'cont_no_ejec'=>$cont_no_ejec,
                                                      'efectivos' => $efectivos,'inefectivos'=>$inefectivos,'org' => $_GET['organization_id']]);
                    } 
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    public function controlledRiskCriteria()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                foreach (Session::get('roles') as $role)
                {
                    if ($role != 1)
                    {
                        return Redirect::route('home');
                    }
                    else
                    {
                        break;
                    }
                }
            }
            $tabla = DB::table('controlled_risk_criteria')->get();
            if (Session::get('languaje') == 'en')
            {
                return view('en.controlled_risk_criteria.index',['tabla' => $tabla]);
            }
            else
            {
                return view('controlled_risk_criteria.index',['tabla' => $tabla]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        } 
    }
    public function updateControlledRiskCriteria()
    {
        try
        {
            global $i;
            $i = 1;
            //hacemos actualización de todos los campos
            while (isset($_POST['eval_in_risk_'.$GLOBALS['i']]))
            {
                global $in;
                global $ctrl;
                $in = $_POST['eval_in_risk_'.$GLOBALS['i']];
                $ctrl = $_POST['eval_ctrl_risk_'.$GLOBALS['i']];
                DB::transaction(function() {
                    //actualizamos
                    DB::table('controlled_risk_criteria')
                        ->where('id','=',$GLOBALS['i'])
                        ->update([
                            'eval_in_risk' => $GLOBALS['in'],
                            'eval_ctrl_risk' => $GLOBALS['ctrl']
                            ]);
                    $GLOBALS['i'] += 1;
                }); 
            }
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Controlled risk criteria was successfully updated');
            }
            else
            {
                Session::flash('message','Criterio para riesgo controlado fue actualizado corrrectamente');
            }
            return Redirect::to('controlled_risk_criteria');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }  
    }

    //obtiene controles de objetivos de organización
    public function getObjectiveControls($org)
    {
        try
        {
            $controls = \Ermtool\Control::getBussinessControls($org,NULL);
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
    //obtiene controles de subproceso de una organización (por ahor (15-11-16) da lo mismo la organización ya que aunque un subproceso esté en distintas organizaciones tendrá los mismos controles)
    public function getSubprocessControls($org,$subprocess)
    {
        try
        {
            $controls = \Ermtool\Control::getControlsFromSubprocess($org,$subprocess);
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //calculamos el valor del control según las pruebas que posea
    //ACT 29-03-18: Esta funcionalidad es actulizada por completa, debido a nuevas funcionalidades, como por ejemplo el que se puedan crear N tipos de prueba distintos, con pesos específicos
    public function calcControlValue($ctrl_org_id)
    {
        GLOBAL $id1;
        $id1 = $ctrl_org_id;

        DB::transaction(function() {
            //Obtenemos los distintos tipos de prueba existentes
            $evaluation_tests = \Ermtool\Evaluation_test::all();

            $evaluations = array();
            //Variables para guardar peso total entre todas las pruebas que se han respondido
            $peso_total_p = 0;
            $peso_total_i = 0;
            $i = 0;

            foreach ($evaluation_tests as $test)
            {
                unset($cew); //ACT 01-08-18: Para corroborar que no se esté tomando el valor anterior   
                //Obtenemos primero la máxima fecha de evaluación de control asociada a esta prueba
                $c_max_date = DB::table('control_evaluation')
                            ->where('control_organization_id','=',$GLOBALS['id1'])
                            ->where('evaluation_test_id','=',$test->id)
                            ->where('status','=',2)
                            ->max('updated_at');

                //sacamos guiones de updated_at (SQL Server)
                // $c_max_date = str_replace('-','',$c_max_date);

                //Hacemos lo mismo para prueba de auditoría (para ver cuál tiene fecha mayor)
                $a_max_date = DB::table('audit_tests')
                            ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                            ->where('audit_test_control.control_organization_id','=',$GLOBALS['id1'])
                            ->where('audit_tests.evaluation_test_id','=',$test->id)
                            ->where('audit_tests.status','=',2)
                            ->max('updated_at');

                //sacamos guiones de updated_at (SQL Server)
                // $a_max_date = str_replace('-','',$a_max_date);

                if (!empty($c_max_date) && !empty($a_max_date))
                {
                    //vemos que fecha es mayor
                    if ($c_max_date > $a_max_date)
                    {
                        //echo "Fecha de control es mayor";
                        //obtenemos peso específico de la evaluacion en probabilidad e impacto
                        $cew = DB::table('control_specific_weight')
                                    ->where('evaluation_test_id','=',$test->id)
                                    ->select('p','i')
                                    ->first();

                        //obtenemos resultado de la evaluación
                        $eval = DB::table('control_evaluation')
                                ->where('control_organization_id','=',$GLOBALS['id1'])
                                ->where('updated_at','=',$c_max_date)
                                ->where('status','=',2)
                                ->select('results')
                                ->first();
                        //guardamos el valor de la evaluación
                        $evaluations[$i] = [
                            'pp' => $cew->p, //peso proba
                            'pi' => $cew->i, //peso impacto
                            'results' => $eval->results
                        ];

                        $i += 1;
                    }
                    else
                    {
                        //echo "Fecha de auditoría es mayor";
                        //obtenemos peso específico de la evaluacion en probabilidad e impacto
                        $cew = DB::table('control_specific_weight')
                                    ->where('evaluation_test_id','=',$test->id)
                                    ->select('p','i')
                                    ->first();

                        //obtenemos resultado de la evaluación
                        $eval = DB::table('audit_tests')
                            ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                            ->where('audit_test_control.control_organization_id','=',$GLOBALS['id1'])
                            ->where('audit_tests.updated_at','=',$a_max_date)
                            ->where('audit_tests.status','=',2)
                            ->select('audit_tests.results')
                            ->first();
                        //guardamos el valor de la evaluación
                        $evaluations[$i] = [
                            'pp' => $cew->p, //peso proba
                            'pi' => $cew->i, //peso impacto
                            'results' => $eval->results
                        ];

                        $i += 1;
                    }
                }
                else if (!empty($c_max_date))
                {
                    //obtenemos peso específico de la evaluacion en probabilidad e impacto
                    $cew = DB::table('control_specific_weight')
                                ->where('evaluation_test_id','=',$test->id)
                                ->select('p','i')
                                ->first();

                    //obtenemos resultado de la evaluación
                    $eval = DB::table('control_evaluation')
                            ->where('control_organization_id','=',$GLOBALS['id1'])
                            ->where('updated_at','=',$c_max_date)
                            ->where('status','=',2)
                            ->select('results')
                            ->first();
                    //guardamos el valor de la evaluación
                    $evaluations[$i] = [
                        'pp' => $cew->p, //peso proba
                        'pi' => $cew->i, //peso impacto
                        'results' => $eval->results
                    ];

                    $i += 1; 
                }
                else if (!empty($a_max_date))
                {
                    //obtenemos peso específico de la evaluacion en probabilidad e impacto
                    $cew = DB::table('control_specific_weight')
                                ->where('evaluation_test_id','=',$test->id)
                                ->select('p','i')
                                ->first();

                    //obtenemos resultado de la evaluación
                    $eval = DB::table('audit_tests')
                            ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                            ->where('audit_test_control.control_organization_id','=',$GLOBALS['id1'])
                            ->where('audit_tests.updated_at','=',$a_max_date)
                            ->where('audit_tests.status','=',2)
                            ->select('audit_tests.results')
                            ->first();
                    //guardamos el valor de la evaluación
                    $evaluations[$i] = [
                        'pp' => $cew->p, //peso proba
                        'pi' => $cew->i, //peso impacto
                        'results' => $eval->results
                    ];

                    $i += 1; 
                }

                //vemos si la prueba tuvo evaluación
                if (isset($cew))
                {
                    $peso_total_p += $cew->p;
                    $peso_total_i += $cew->i;
                }
            }

            //Calculamos resultado: Para esto, calculamos porcentaje según peso específico de las pruebas que se encuentren correctas y lo sumamos
            $result_p = 0;
            $result_i = 0;
            foreach ($evaluations as $eval)
            {
                if ($eval['results'] == 1) //es efectiva
                {
                    $result_p += ($eval['pp'] * 100)/$peso_total_p;
                    $result_i += ($eval['pi'] * 100)/$peso_total_i;
                }
            }

            //redondeamos porcentajes
            $result_p = round($result_p,2);
            $result_i = round($result_i,2);

            //ahora guardamos los resultados: Para esto primero cambiamos el status de posibles evaluaciones anteriores, y luego guardamos
            $update_evaluations = \Ermtool\Control_evaluation::changeStatus($GLOBALS['id1']);

            //Guardamos
            $control_eval = \Ermtool\Control_evaluation::saveControlValue($GLOBALS['id1'],$result_p,$result_i,Auth::user()->id);

        });

        return 0;
    }

    public function calcControlledRisk($control_id,$org)
    {
        try
        {
            global $org1;
            $org1 = $org;
            global $id;
            $id = $control_id;
            DB::transaction(function() {
                //primero que todo, obtenemos todos los riesgos a los que apunta este control (vemos si apunta a riesgos de proceso o de entidad)
                $risks = \Ermtool\Risk::getRisksFromControl($GLOBALS['org1'],$GLOBALS['id']);
                $kind = 1; //para facilitar la manipulación posterior del riesgo

                //ahora recorremos cada uno de esos riesgos, y obtenemos los controles que tiene asociado y cuáles de estos controles posee una evaluación en la tabla control_eval_temp2
                foreach ($risks as $risk)
                {
                    $this->calcResidualRisk($GLOBALS['org1'],$risk->id,date('Y'),date('m'),date('d'));
                }
            });
            

            return 0;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function calcResidualRisk($org,$risk_id,$ano,$mes,$dia)
    {
        try
        {
        //obtenemos todos los controles de este riesgo
        
           $controls = \Ermtool\Control::getControlsFromRisk($org,$risk_id);

           //ACT 03-04-17: Obtenemos organization_risk
           $risk2 = \Ermtool\Risk::getOrganizationRisk($org,$risk_id);

           $prom_p = 0; //promedio de probabilidad de todos los controles
           $prom_i = 0; //promedio de impacto de todos los controles
           $cont = 0; //contador de todos los controles asociados a cada riesgo
           foreach ($controls as $control)
           {
                $ctrl_org = \Ermtool\ControlOrganization::getByCO($control->id,$org);
                //Tendremos sólo una evaluación de control vigente, => cambiamos get por first()
                $eval = DB::table('control_eval_temp2')
                            ->where('control_organization_id','=',$ctrl_org->id)
                            ->where('status','=',1)
                            ->select('probability','impact')
                            //->get();
                            ->first();

                //$prom_p = 0;
                //$prom_i = 0;
                //$cont = 0;

                if (isset($eval) && $eval != NULL && !empty($eval))
                {
                    $prom_p += $eval->probability;
                    $prom_i += $eval->impact;
                    $cont += 1;
                    //calculamos promedio en probabilidad e impacto
                    //$prom_p = $prom_p / $cont;
                    //$prom_i = $prom_i / $cont;
                }

                //sumamos el promedio de este control, al que será el promedio de todos los controles (primero será la suma, y una vez contados todos los controles (en cont_controles), será el promedio)
                //$prom_controls += $prom;
                //$cont_controls += 1;
           }

           //Vemos si es que hay controles asociados al riesgo, si es así calculamos promedio en proba e impacto
            if ($cont != 0)
            {
                $prom_p = $prom_p / $cont;
                $prom_i = $prom_i / $cont;

                //---Cálculo de Riesgo Residual---//
                //primero obtenemos fecha máx de evaluación para el riesgo indicado
                $max_update = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.organization_risk_id','=',$risk2->id)
                            ->max('evaluations.updated_at');

                //verificamos que haya alguna evaluación para este riesgo (sino, no se calcula el riesgo residual)
                if (isset($max_update) && $max_update != null)
                {
                    //ahora debemos obtener severidad actual del riesgo
                    $s = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluations.updated_at','=',$max_update)
                                ->where('evaluation_risk.organization_risk_id','=',$risk2->id)
                                ->select('evaluation_risk.avg_probability as p','evaluation_risk.avg_impact as i')
                                ->first();

                    //CALCULAMOS proba e impacto a través de fórmula X-(X*Y/2) donde X es eval_in e Y porcentaje
                    //porcentaje lo dividimos por 100
                    $prom_p = $prom_p / 100;
                    $prom_i = $prom_i / 100;
                    //---- APLICACIÓN DE FÓRMULA ----//
                    $p = $s->p - ($s->p*$prom_p/2);
                    $i = $s->i - ($s->i*$prom_i/2);

                    //OBS: función insertResidualRisk estará en Risk, ya que está asociado a éste, y no sólo a control_evaluation
                    \Ermtool\Risk::insertResidualRisk($risk2->id,$p,$i,$ano,$mes,$dia);
                    //\Ermtool\Control_evaluation::insertControlledRisk($risk2->id,$riesgo_residual,2,$ano,$mes,$dia);
                }
            }
            else
            {
                //ACTUALIZACIÓN 19-07-17: significa que no hay controles, entonces eliminamos valor de riesgo residual
                //ACT 30-03-18: Ahora se guarda en residual_risk (ya que controlled_risk es para autoeval)
                DB::table('residual_risk')
                    ->where('organization_risk_id','=',$risk2->id)
                    ->delete();
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }      
    }


    //función que calcula el valor del o los riesgos controlados (a través de una nueva agregación o modificación en el valor de un control) en formato de Autoevaluación (utilizando fórmula de KOAndina)
    public function calcControlledRiskAutoeval($control_id,$org,$ano,$mes,$dia)
    {
        try
        {
            //primero que todo, obtenemos todos los riesgos a los que apunta este control (vemos si apunta a riesgos de proceso o de entidad)
            $risks = \Ermtool\Risk::getRisksFromControl($org,$control_id);
            $kind = 1; //para facilitar la manipulación posterior del riesgo

            //ahora recorremos cada uno de esos riesgos, y obtenemos los controles que tiene asociado y cuáles de estos controles posee una evaluación en la tabla control_eval_risk_temp
            foreach ($risks as $risk)
            {
                $this->calcResidualRiskAutoeval($org,$risk->id,$ano,$mes,$dia);
            }

            return 0;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function calcResidualRiskAutoeval($org,$risk_id,$ano,$mes,$dia)
    {
        try
        {
        //obtenemos todos los controles de este riesgo
        
           $controls = \Ermtool\Control::getControlsFromRisk($org,$risk_id);

           //ACT 03-04-17: Obtenemos organization_risk
           $risk2 = \Ermtool\Risk::getOrganizationRisk($org,$risk_id);

           $prom_controls = 0; //promedio de todos los controles
           $cont_controls = 0; //contador de todos los controles asociados a cada riesgo
           foreach ($controls as $control)
           {
                //ACT 05-07-17 (KOAndina): Obtenemos todas las pruebas vigentes
                //para cada control, obtenemos las pruebas vigentes (Auto evaluaciones o no)
                //ACT 18-04-18: Guardamos control_organization_id
                $ctrl_org = \Ermtool\ControlOrganization::getByCO($control->id,$org);

                $evals = DB::table('control_eval_risk_temp')
                            ->where('control_organization_id','=',$ctrl_org->id)
                            ->where('status',1)
                            ->select('result')
                            ->get();

                //creamos variable donde se guardará ponderado y contador
                $prom = 0;
                $cont = 0;

                if (isset($evals) && $evals != NULL)
                {
                    foreach ($evals as $eval)
                    {
                        $prom += $eval->result;
                        $cont += 1;
                    }

                    //calculamos promedio (OBS: POR AHORA (05-07-2017) SERÁ PROMEDIO, QUIZÁS DESPUÉS TENGAN DISTINTAS PONDERACIONES CADA PRUEBA)
                    $prom = $prom / $cont;
                }

                //sumamos el promedio de este control, al que será el promedio de todos los controles (primero será la suma, y una vez contados todos los controles (en cont_controles), será el promedio)
                $prom_controls += $prom;
                $cont_controls += 1;
           }

            //ahora calculamos el porcentaje de contribución de acciones mitigantes (obteniendo el promedio entre el promedio de todos sus controles evaluados)
            if ($cont_controls != 0)
            {
                $prom_controls = $prom_controls / $cont_controls;

                //---Cálculo de Riesgo Residual---//
                //primero obtenemos fecha máx de evaluación para el riesgo indicado
                $max_update = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.organization_risk_id','=',$risk2->id)
                            ->max('evaluations.updated_at');

                //verificamos que haya alguna evaluación para este riesgo (sino, no se calcula el riesgo residual)
                if (isset($max_update) && $max_update != null)
                {
                    //ahora debemos obtener severidad actual del riesgo
                    $s = DB::table('evaluation_risk')
                                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                                ->where('evaluations.updated_at','=',$max_update)
                                ->where('evaluation_risk.organization_risk_id','=',$risk2->id)
                                ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                                ->first();

                    //ahora calculamos nivel de exposición al riesgo (riesgo residual) según fórmula de KOAndina
                    $riesgo_residual = ($s->avg_probability * $s->avg_impact)*(1-($prom_controls/100));
                    $riesgo_residual = round($riesgo_residual, 2);
                    \Ermtool\Risk::insertControlledRisk($risk2->id,$riesgo_residual,2,$ano,$mes,$dia);
                }
            }
            else
            {
                //ACTUALIZACIÓN 19-07-17: significa que no hay controles, entonces eliminamos valor de riesgo residual
                DB::table('controlled_risk')
                    ->where('organization_risk_id','=',$risk2->id)
                    ->delete();
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }      
    }

    public function hallazgos($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //datos de evaluación de control
                $evaluation = \Ermtool\Control_evaluation::find($id);
                
                $issues = array();

                $issues1 = \Ermtool\Issue::getIssueByControlEvaluation($id);

                $control_name = \Ermtool\Control::nameByCO($evaluation->control_organization_id);

                $iss = new IssuesController;
                //print_r($_POST);
                $i = 0;
                foreach ($issues1 as $issue)
                {
                    
                    if ($issue['plan_description'] != NULL)
                    {
                        $temp = $iss->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],$issue['comments'],$issue['plan_description'],$issue['plan_status'],$issue['plan_final_date']);  
                    }
                    else
                    {
                        $temp = $iss->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],$issue['comments'],NULL,NULL,NULL);  
                    }

                    $issues[$i] = [
                        'id' => $temp['id'],
                        'name' => $temp['name'],
                        'classification' => $temp['classification'],
                        'recommendations' => $temp['recommendations'],
                        'comments' => $temp['comments'],
                        'plan' => $temp['plan'],
                        'status' => $temp['status'],
                        'status_origin' => $temp['status_origin'],
                        'final_date' => $temp['final_date'],
                        'evidence' => $issue['evidences']
                    ];

                    $i += 1; 
                }

                $org_id = \Ermtool\Organization::getOrganizationIdFromControl($evaluation->control_id);

                
                //ACT 28-03-18: Obtenemos kind de tabla evalution_test
                $kind = \Ermtool\Evaluation_test::find($evaluation->evaluation_test_id);

                if (Session::get('languaje') == 'en')
                {/*
                    switch ($evaluation->kind) {
                        case 0:
                            $kind = 'Design test';
                            break;
                        case 1:
                            $kind = 'Operational effectiveness test';
                            break;
                        case 2:
                            $kind = 'Sustantive test';
                            break;
                        case 3:
                            $kind = 'Compliance test';
                            break;
                        default:
                            # code...
                            break;
                    }*/
                    return view('en.hallazgos.index3',['issues'=>$issues, 'evaluation' => $evaluation,'control_name'=>$control_name,'org_id' => $org_id,'kind' => $kind]);
                }
                else
                {/*
                    switch ($evaluation->kind) {
                        case 0:
                            $kind = 'Prueba de diseño';
                            break;
                        case 1:
                            $kind = 'Prueba de efectividad operativa';
                            break;
                        case 2:
                            $kind = 'Prueba sustantiva';
                            break;
                        case 3:
                            $kind = 'Prueba de cumplimiento';
                            break;
                        default:
                            # code...
                            break;
                    }*/
                    return view('hallazgos.index3',['issues'=>$issues, 'evaluation' => $evaluation,'control_name'=>$control_name,'org_id' => $org_id,'kind' => $kind]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getControlsFromProcess($id,$org)
    {
        try
        {
            $controls = \Ermtool\Control::getControlsFromProcess($id,$org);
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //obtenemos controles (no repetidos) de los subprocesos seleccionados a través de jquery (al crear una prueba de auditoría) => $subprocesses = array con id de subprocesos
    public function getControlsFromSubprocess($id,$subprocesses)
    {
        try
        {
            $subs = explode(',',$subprocesses);
            $controls = array();
            $i = 0;
            foreach ($subs as $s)
            {
                $controls1 = \Ermtool\Control::getControlsFromSubprocess($id,$s);

                foreach ($controls1 as $c)
                {
                    $controls[$i] = [
                        'id' => $c->id,
                        'name' => $c->name,
                        'description' => $c->description,
                    ];

                    $i += 1;
                }
            }

            $controls = array_unique($controls,SORT_REGULAR);
            return json_encode(array_values($controls));
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getControlsFromPerspective($org,$perspective)
    {
        try
        {
            $controls = \Ermtool\Control::getControlsFromPerspective($org,$perspective);
            return json_encode($controls);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function docxGraficos()
    {
        try
        {
            $word = new \PhpOffice\PhpWord\PhpWord();
            $section = $word->createSection();

            $word->setDefaultFontName('Verdana');
            $word->setDefaultFontSize(10);

            //estilos
            $titleStyle = array('size' => 18, 'color' => '045FB4');
            $subTitle = array('size' => 16);
            $subsubTitle = array('bold' => true);

            //estilos de tablas
            $tableStyleName = 'Audit Plans';
            $tableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
            $tableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true);
            $tableFirstRowStyle2 = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true,'size' => 7);
            $tableCellStyle = array('valign' => 'center');
            $tableCellBtlrStyle = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
            $tableFontStyle = array('bold' => false);
            $tableFontStyle2 = array('bold' => false, 'size' => 7);
            $word->addTableStyle($tableStyleName, $tableStyle, $tableFirstRowStyle);

            $imageStyle = array('width'=>500, 'height'=>300, 'align'=>'center');

            $section->addText(
                    'Reporte de Gráficos de Controles',$titleStyle
                );

            //decodificamos los gráficos y los guardamos temporalmente
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['grafico1']));
            file_put_contents('image.png', $data);

            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['grafico2']));
            file_put_contents('image2.png', $data);

            $section->addTextBreak(1);

            $section->addText(
                'Controles ejecutados y no ejecutados', $subTitle   
            );  

            $section->addImage('image.png', $imageStyle);

            $section->addTextBreak(1);

            $section->addText('Controles Ejecutados',$subsubTitle);
            $table1 = $section->addTable($tableStyleName);
            $table1->addRow();
            $table1->addCell(1000)->addText('Nombre',$tableFirstRowStyle);
            $table1->addCell(5000)->addText('Descripción',$tableFirstRowStyle);
            $table1->addCell(1000)->addText('Fecha Actualizado',$tableFirstRowStyle);

            $section->addTextBreak(1);

            $section->addText('Controles No Ejecutados',$subsubTitle);
            $table2 = $section->addTable($tableStyleName);
            $table2->addRow();
            $table2->addCell(1000)->addText('Nombre',$tableFirstRowStyle);
            $table2->addCell(5000)->addText('Descripción',$tableFirstRowStyle);
            $table2->addCell(1000)->addText('Fecha Actualizado',$tableFirstRowStyle);

            $section->addTextBreak(1);

            $section->addText(
                'Controles efectivos y no efectivos', $subTitle   
            );  

            $section->addImage('image2.png', $imageStyle);

            $section->addText('Controles Efectivos',$subsubTitle);
            $table3 = $section->addTable($tableStyleName);
            $table3->addRow();
            $table3->addCell(1000)->addText('Nombre',$tableFirstRowStyle);
            $table3->addCell(5000)->addText('Descripción',$tableFirstRowStyle);
            $table3->addCell(1000)->addText('Fecha Actualizado',$tableFirstRowStyle);

            $section->addTextBreak(1);

            $section->addText('Controles Inefectivos',$subsubTitle);
            $table4 = $section->addTable($tableStyleName);
            $table4->addRow();
            $table4->addCell(1000)->addText('Nombre',$tableFirstRowStyle);
            $table4->addCell(5000)->addText('Descripción',$tableFirstRowStyle);
            $table4->addCell(1000)->addText('Fecha Actualizado',$tableFirstRowStyle);


            $controles = \Ermtool\Control::getEvaluatedControls($_POST['org']);

            $c = $this->getControlReport($controles,0);

            foreach ($c['controls'] as $ctrl)
            {
                $table1->addRow();
                $table1->addCell(1000)->addText($ctrl['name'],$tableFontStyle2);
                $table1->addCell(5000)->addText($ctrl['description'],$tableFontStyle2);
                $table1->addCell(1000)->addText($ctrl['updated_at'],$tableFontStyle2);

                if ($ctrl['results'] == 2) //efectivos
                {
                    $table3->addRow();
                    $table3->addCell(1000)->addText($ctrl['name'],$tableFontStyle2);
                    $table3->addCell(5000)->addText($ctrl['description'],$tableFontStyle2);
                    $table3->addCell(1000)->addText($ctrl['updated_at'],$tableFontStyle2);
                }
                else if ($ctrl['results'] == 1) //inefectivos
                {
                    $table4->addRow();
                    $table4->addCell(1000)->addText($ctrl['name'],$tableFontStyle2);
                    $table4->addCell(5000)->addText($ctrl['description'],$tableFontStyle2);
                    $table4->addCell(1000)->addText($ctrl['updated_at'],$tableFontStyle2);    
                }
            }

            foreach ($c['no_ejecutados'] as $ctrl)
            {
                $table2->addRow();
                $table2->addCell(1000)->addText($ctrl['name'],$tableFontStyle2);
                $table2->addCell(5000)->addText($ctrl['description'],$tableFontStyle2);
                $table2->addCell(1000)->addText($ctrl['updated_at'],$tableFontStyle2);
            }

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
            $objWriter->save('controles_graficos.docx');
            
            //generamos doc para guardar
            $file_url = 'controles_graficos.docx';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url); // do the double-download-dance (dirty but worky)

            //ahora borramos archivos temporales
            unlink('controles_graficos.docx');
            unlink('image.png');
            unlink('image2.png');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //ACT 24-01-18: Index de Evaluación de Riesgos residual manual
    public function residualManual()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //ACTUALIZACIÓN 25-07: En vez de obtener y enviar riesgos, enviamos organización para poder seleccionar
                $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');

                if (Session::get('languaje') == 'en')
                { 
                    return view('en.controles.residual_manual',['organizations'=>$organizations,'categories' => $categories]);
                }
                else
                {
                    return view('controles.residual_manual',['organizations'=>$organizations,'categories' => $categories]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //Página para agregar evaluación residual del riesgo
    public function residualManual2()
    {
        //try
        //{
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $riesgos = array();
                $i = 0;
                $id = 0;
                //Obtenemos EBT. Para esto, primero vemos si esta organización tiene EBT, sino buscamos EBT de org principal
                $ebt = \Ermtool\Organization::getEBT($_GET['organization_id'],1);

                if ($ebt->ebt == NULL)
                {
                    $ebt = \Ermtool\Organization::getEBT($_GET['organization_id'],2);

                    //si org principal no tiene EBT
                    if ($ebt->ebt == NULL)
                    {
                        $ebt = array();
                    }
                }

                //ACTUALIZACIÓN 04-01-18: Agregamos tipos de moneda para materialidad
                $kinds = ['1'=>'Peso','2'=>'Dólar','3'=>'Euro','4'=>'UF']; 

                //cada uno de los riesgos de subproceso
                if (isset($_GET['risk_subprocess_id']))
                {
                    foreach ($_GET['risk_subprocess_id'] as $risk)
                    {

                            $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                            //guardamos el riesgo de subproceso junto a su id de evaluation_risk para crear form de encuesta
                            foreach ($risk1 as $r)
                            {
                                //obtenemos último impacto y probabilidad (materialidad)
                                $last_m = \Ermtool\Risk::getLastMateriality($_GET['organization_id'],$r->id);

                                //$org_id = $r->org_id;
                                //obtenemos subprocesos relacionados
                                $subobj = \Ermtool\Subprocess::getSubprocessesFromOrgRisk($r->id,$_GET['organization_id']);

                                //obtenemos controles asociados al riesgo
                                $controls = \Ermtool\Control::getControlsFromRisk($_GET['organization_id'],$r->id);

                                //obtenemos evaluación del riesgo si es que hay
                                //ACT 26-03-18: Agregamos kind (1 es para cualquier tipo de evaluación)
                                $evaluation_risk = \Ermtool\Evaluation::getLastEvaluation($risk,1);

                                $riesgos[$i] = array('type' => 0,
                                                    'org_risk_id' => $risk,
                                                    'risk_name' => $r->risk_name,
                                                    'description' => $r->description,
                                                    'subobj' => $subobj,
                                                    'last_m' => $last_m,
                                                    'controls' => $controls,
                                                    'evaluation_risk' => $evaluation_risk);
                                $i += 1;
                            }
                    }
                }

                if (isset($_GET['objective_risk_id']))
                {
                    foreach ($_GET['objective_risk_id'] as $risk)
                    {
                            //obtenemos nombre de riesgo y organizacion
                            $risk1 = \Ermtool\Risk::getRisksFromOrgRisk($risk);

                            foreach ($risk1 as $r)
                            {
                                //obtenemos último impacto y probabilidad (materialidad)
                                $last_m = \Ermtool\Risk::getLastMateriality($_GET['organization_id'],$r->id);

                                //obtenemos objetivos relacionados
                                $subobj = \Ermtool\Objective::getObjectivesFromOrgRisk($r->id,$_GET['organization_id']);

                                //obtenemos controles asociados al riesgo
                                $controls = \Ermtool\Control::getControlsFromRisk($_GET['organization_id'],$r->id);

                                //obtenemos evaluación del riesgo si es que hay
                                //ACT 26-03-18: Agregamos kind (1 es para cualquier tipo de evaluación)
                                $evaluation_risk = \Ermtool\Evaluation::getLastEvaluation($risk,1);

                                $riesgos[$i] = array('type' => 1,
                                                    'org_risk_id' => $risk,
                                                    'risk_name' => $r->risk_name,
                                                    'description' => $r->description,
                                                    'subobj' => $subobj,
                                                    'last_m' => $last_m,
                                                    'controls' => $controls,
                                                    'evaluation_risk' => $evaluation_risk);
                                $i += 1;
                            }
                    }
                }     

                if (Session::get('languaje') == 'en')
                {
                    $tipos_impacto = \Ermtool\Eval_description::getImpactValues(2); //2 es inglés
                    $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(2);
                    $org_name = \Ermtool\Organization::name($_GET['organization_id']); 
                    return view('en.controles.residual_manual2',['riesgos'=>$riesgos,'org_name' => $org_name,'org_id'=>$_GET['organization_id'], 'ebt' => $ebt,'tipos_impacto' => $tipos_impacto, 'tipos_proba' => $tipos_proba, 'kinds' => $kinds]);
                }
                else
                {
                    if (!isset($_GET['risk_subprocess_id']) && !isset($_GET['objective_risk_id']))
                    {
                        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
                        $categories = \Ermtool\Risk_category::where('status',0)->where('risk_category_id',NULL)->lists('name','id');
                        Session::flash('error','Debe ingresar a lo menos un riesgo');

                        return view('controles.residual_manual',['organizations'=>$organizations,'categories' => $categories]);     
                    }
                    else
                    {
                        $tipos_impacto = \Ermtool\Eval_description::getImpactValues(1);
                        $tipos_proba = \Ermtool\Eval_description::getProbabilityValues(1);
                        $org_name = \Ermtool\Organization::name($_GET['organization_id']); 
                        return view('controles.residual_manual2',['riesgos' => $riesgos, 'org_name' => $org_name, 'org_id' => $_GET['organization_id'], 'ebt' => $ebt,'tipos_impacto' => $tipos_impacto, 'tipos_proba' => $tipos_proba, 'kinds' => $kinds]);
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

    public function storeResidualManual()
    {
        //print_r($_POST);
        DB::transaction(function(){
            $logger = $this->logger3;

            foreach ($_POST['evaluation_risk_id'] as $org_risk_id)
            {
                if (isset($_POST['comments_'.$org_risk_id]) && $_POST['comments_'.$org_risk_id] != '') //si es que se agregaron comentarios
                {
                    $comments = $_POST['comments_'.$org_risk_id];
                }
                else
                {
                    $comments = NULL;
                }

                //ACT 05-03-18: Sacamos rut de Auth
                $rut = Auth::user()->id;

                //Vemos si se ingreso valores netos de la evaluación (en caso de que no haya evaluación bruta o EBT definido, no se ingresará)
                if (isset($_POST['probability_'.$org_risk_id]))
                {
                    $probability = $_POST['probability_'.$org_risk_id];
                }
                else
                {
                    $probability = NULL;
                }

                if (isset($_POST['impact_'.$org_risk_id]))
                {
                    $impact = $_POST['impact_'.$org_risk_id];
                }
                else
                {
                    $impact = NULL;
                }
                //insertamos en controlled_risk_manual
                $eval_risk_id = DB::table('controlled_risk_manual')
                        ->insertGetId([
                            'organization_risk_id' => $org_risk_id,
                            'probability' => $_POST['proba_'.$org_risk_id],
                            'impact' => $_POST['criticidad_'.$org_risk_id],
                            'probability2' => $probability,
                            'impact2' => $impact,
                            'comments' => $comments,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'user_id' => $rut,
                        ]);
            }

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Answers successfully sent');
            }
            else
            {
                Session::flash('message','Respuestas enviadas correctamente');
            }

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames.', Rut: '.Auth::user()->id.', ha realizado una evaluación de riesgos residual manual con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
        });

        return Redirect::to('residual_manual'); 
    }


    //-- Funciones extras --//

    //Función para actualizar porcentaje de contribución
    public function updateContPercentage()
    {
        $controls = \Ermtool\Control::all();

        foreach ($controls as $control)
        {
            if ($control->porcentaje_cont != NULL)
            {
                //obtenemos control_organization_risk donde corresponde
                $cors = DB::table('control_organization_risk')
                    ->where('control_id','=',$control->id)
                    ->select('id')
                    ->get();

                foreach ($cors as $cor)
                {
                    DB::table('control_organization_risk')
                        ->where('id','=',$cor->id)
                        ->update([
                            'cont_percentage' => $control->porcentaje_cont
                        ]);
                }
            }
            

            //$control->porcentaje_cont = NULL;
            //$control->save();
        }
    }


    //ACT 17-04-18: Función para actualizar nueva tabla control_organization con los datos obtenidos de control_organization_risk
    public function updateControlOrganization()
    {
        $cor = DB::table('control_organization_risk')->get();

        foreach ($cor as $c)
        {
            //obtenemos organización desde organization_risk
            $org = DB::table('organization_risk')
                ->where('id','=',$c->organization_risk_id)
                ->select('organization_id as id')
                ->first();

            //vemos si es que existe o no, la combinación de control y organización en control_organization
            $co = DB::table('control_organization')
                    ->where('control_id','=',$c->control_id)
                    ->where('organization_id','=',$org->id)
                    ->select('id')
                    ->first();

            if (empty($co)) //si está vacío, podemos crearlo con la info de cor
            {
                //obtenemos info de control (por ejemplo, evidencia y los timestamp)
                $control = \Ermtool\Control::find($c->control_id);

                DB::table('control_organization')
                    ->insert([
                        'control_id' => $c->control_id,
                        'organization_id' => $org->id,
                        'created_at' => $control->created_at,
                        'updated_at' => $control->updated_at,
                        'comments' => $control->comments,
                        'evidence' => $control->evidence,
                        'stakeholder_id' => $c->stakeholder_id,
                        'cont_percentage' => $c->cont_percentage
                    ]);
            }
        }
    }

    //Función para actualizar tablas asociadas a control_organization:
    //--- control_evaluation
    //--- control_eval_risk_temp
    //--- control_eval_temp2
    //--- control_financial_statements
    public function updateAssociatesControlOrganization()
    {
        
        //control_evaluation
        $ce = \Ermtool\Control_evaluation::all();

        foreach ($ce as $c)
        {
            if ($c->control_id != NULL && $c->organization_id != NULL)
            {
                $co = \Ermtool\ControlOrganization::where('control_id',$c->control_id)->where('organization_id',$c->organization_id)->first();

                if (!empty($co))
                {
                    $c->control_organization_id = $co->id;
                    $c->save();
                }
            }
        }

        //control_eval_risk_temp
        $cert = DB::table('control_eval_risk_temp')->get();

        foreach ($cert as $c)
        {
            if ($c->control_id != NULL && $c->organization_id != NULL)
            {
                $co = \Ermtool\ControlOrganization::where('control_id',$c->control_id)->where('organization_id',$c->organization_id)->first();

                if (!empty($co))
                {
                    DB::table('control_eval_risk_temp')
                        ->where('id','=',$c->id)
                        ->update([
                            'control_organization_id' => $co->id
                        ]);
                }
            }
        }

        //control_eval_temp2
        $cet2 = DB::table('control_eval_temp2')->get();

        foreach ($cet2 as $c)
        {
            if ($c->control_id != NULL && $c->organization_id != NULL)
            {
                $co = \Ermtool\ControlOrganization::where('control_id',$c->control_id)->where('organization_id',$c->organization_id)->first();

                if (!empty($co))
                {
                    DB::table('control_eval_temp2')
                        ->where('id','=',$c->id)
                        ->update([
                            'control_organization_id' => $co->id
                        ]);
                }
            }
        }
        /*
        //control_financial_statement
        $cfs = DB::table('control_financial_statement')->get();

        foreach ($cfs as $c)
        {
            if ($c->control_id != NULL && $c->organization_id != NULL)
            {
                $co = \Ermtool\ControlOrganization::where('control_id',$c->control_id)->where('organization_id',$c->organization_id)->first();

                DB::table('control_financial_statement')
                    ->where('id','=',$c->id)
                    ->update([
                        'control_organization_id' => $co->id
                    ]);
            }
        }
        */
        //audit_test_control
        $atc = DB::table('audit_test_control')->get();

        foreach ($atc as $c)
        {
            //obtenemos org de audit_test
            $org = DB::table('audit_tests')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->where('audit_tests.id','=',$c->audit_test_id)
                ->select('audit_plans.organization_id as id')
                ->first();

            if ($c->control_id != NULL && $org->id != NULL)
            {
                $co = \Ermtool\ControlOrganization::where('control_id',$c->control_id)->where('organization_id',$org->id)->first();

                if (!empty($co))
                {
                    DB::table('audit_test_control')
                        ->where('id','=',$c->id)
                        ->update([
                            'control_organization_id' => $co->id
                        ]);
                }
            }
        }
    }
}