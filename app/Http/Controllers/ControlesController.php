<?php
namespace Ermtool\Http\Controllers;
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
//sleep(2);
class ControlesController extends Controller
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
    public function index2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $controls1 = array();
            $controls2 = array();
            $objective_risks = array();
            $risks_subprocess = array();

            $i = 0; //contador de controles
            
            $controles1 = \Ermtool\Control::getBussinessControls($_GET['organization_id']);
            $controles2 = \Ermtool\Control::getProcessesControls($_GET['organization_id']);
            $j = 0; //contador de riesgos y subprocesos u objetivos
            foreach ($controles1 as $control) //se recorre cada uno de los controles de negocio
            {

                $objectives_risks = \Ermtool\Risk::getObjectiveRisksFromControl($control->id);
                //almacenamos los nombres de los riesgos y subprocessos asociados al control
                foreach ($objectives_risks as $objective_risk)
                {
                        $objective_risks[$j] = array('control_id' => $control->id,
                                            'subneg' => $objective_risk->obj_name,
                                            'risk' => $objective_risk->risk_name);
                    $j += 1;
                }

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
                $controls1[$i] = array('id'=>$control->id,
                                    'name'=>$control->name,
                                    'description'=>$control->description,
                                    'type'=>$control->type,
                                    'type2'=>$control->type2,
                                    'created_at'=>$fecha_creacion,
                                    'updated_at'=>$fecha_act,
                                    'evidence'=>$control->evidence,
                                    'periodicity'=>$control->periodicity,
                                    'purpose'=>$control->purpose,
                                    'stakeholder'=>$stakeholder2);
                $i += 1;
            }

            $j = 0; //contador de riesgos y subprocesos u objetivos
            foreach ($controles2 as $control)
            {
                $risks_subprocesses = \Ermtool\Risk::getRisksSubprocessFromControl($control->id);
                //almacenamos los nombres de los riesgos y subprocesos u objetivos asociados asociados al control
                foreach ($risks_subprocesses as $risk_subprocess)
                {
                    $risks_subprocess[$j] = array('control_id' => $control->id,
                                            'subneg' => $risk_subprocess->sub_name,
                                            'risk' => $risk_subprocess->risk_name);
                    $j += 1;
                }

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
                $controls2[$i] = array('id'=>$control->id,
                                    'name'=>$control->name,
                                    'description'=>$control->description,
                                    'type'=>$control->type,
                                    'type2'=>$control->type2,
                                    'created_at'=>$fecha_creacion,
                                    'updated_at'=>$fecha_act,
                                    'evidence'=>$control->evidence,
                                    'periodicity'=>$control->periodicity,
                                    'purpose'=>$control->purpose,
                                    'stakeholder'=>$stakeholder2);
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.index',['controls1' => $controls1,'controls2'=>$controls2,'risks_subprocess' => $risks_subprocess,'objective_risks'=>$objective_risks,'org_id' => $_GET['organization_id']]);
            }
            else
            {
                return view('controles.index',['controls1' => $controls1,'controls2'=>$controls2,'risks_subprocess' => $risks_subprocess,'objective_risks'=>$objective_risks,'org_id' => $_GET['organization_id']]);
            }
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $stakeholders = \Ermtool\Stakeholder::listStakeholders($org);

            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.create',['stakeholders'=>$stakeholders,'org'=>$org]);
            }
            else
            {
                return view('controles.create',['stakeholders'=>$stakeholders,'org'=>$org]);
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
            //Validación: Si la validación es pasada, el código continua
            $this->validate($request, [
                'name' => 'required|max:255',
                'description' => 'required',
            ]);
            //print_r($_POST);
            //guardamos variable global de evidencia
            global $evidence;
            $evidence = $request->file('evidence_doc');
            //creamos una transacción para cumplir con atomicidad
            DB::transaction(function()
            {
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
                    //insertamos control y obtenemos ID
                    $control_id = DB::table('controls')->insertGetId([
                            'name'=>$_POST['name'],
                            'description'=>$_POST['description'],
                            'type'=>$_POST['type'],
                            'type2'=>$_POST['subneg'],
                            'evidence'=>$_POST['evidence'],
                            'periodicity'=>$periodicity,
                            'purpose'=>$purpose,
                            'stakeholder_id'=>$stakeholder,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                            'expected_cost'=>$_POST['expected_cost']
                            ]);
                    //insertamos en control_risk_subprocess o control_objective_risk
                    if ($_POST['subneg'] == 0) //es control de proceso
                    {
                        foreach ($_POST['select_procesos'] as $subproceso)
                        {
                            DB::table('control_risk_subprocess')
                                ->insert([
                                    'risk_subprocess_id' => $subproceso,
                                    'control_id' => $control_id
                                    ]);
                        }
                    }
                    else if ($_POST['subneg'] == 1) //es control de objetivo
                    {
                        foreach ($_POST['select_objetivos'] as $objetivo)
                        {
                            DB::table('control_objective_risk')
                                ->insert([
                                    'objective_risk_id' => $objetivo,
                                    'control_id' => $control_id
                                    ]);
                        }
                    }
                    //guardamos archivos de evidencias (si es que hay)
                    if($GLOBALS['evidence'] != NULL)
                    {
                        foreach ($GLOBALS['evidence'] as $evidence)
                        {
                            if ($evidence != NULL)
                            {
                                upload_file($evidence,'controles',$control_id);
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
            });
            return Redirect::to('/controles');
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
            $risks_selected = array(); //array de riesgos seleccionados previamente
            $control = \Ermtool\Control::find($id);
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');
            //seleccionamos riesgos de proceso u objetivo que fueron seleccionados previamente (según corresponda)
            if ($control->type2 == 0)
            {
                //seleccionamos riesgos de proceso seleccionados previamente
                $risks = DB::table('control_risk_subprocess')
                            ->where('control_risk_subprocess.control_id','=',$control->id)
                            ->select('risk_subprocess_id as id')
                            ->get();
            }
            else if ($control->type2 == 1)
            {
                //seleccionamos riesgo de negocio
                $risks = DB::table('control_objective_risk')
                            ->where('control_objective_risk.control_id','=',$control->id)
                            ->select('objective_risk_id as id')
                            ->get();
            }
            $i = 0;
            foreach ($risks as $risk)
            {
                $risks_selected[$i] = $risk->id;
                $i += 1;
            }
            
            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.edit',['control'=>$control,'stakeholders'=>$stakeholders,
                        'risks_selected'=>json_encode($risks_selected)
                        ]);
            }
            else
            {
                return view('controles.edit',['control'=>$control,'stakeholders'=>$stakeholders,
                        'risks_selected'=>json_encode($risks_selected)
                        ]);
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
            global $evidence;
            $evidence = $request->file('evidence_doc');
            DB::transaction(function() 
            {
                $control = \Ermtool\Control::find($GLOBALS['id1']);
                if ($_POST['stakeholder_id'] == NULL)
                    $stakeholder = NULL;
                else
                    $stakeholder = $_POST['stakeholder_id'];
                //guardamos archivos de evidencia (si es que hay)
                if($GLOBALS['evidence'] != NULL)
                {
                    foreach ($GLOBALS['evidence'] as $evidence)
                    {
                        if ($evidence != NULL)
                        {
                            upload_file($evidence,'controles',$control->id);
                        }
                    }                    
                }
                $control->name = $_POST['name'];
                $control->description = $_POST['description'];
                $control->type = $_POST['type'];
                $control->evidence = $_POST['evidence'];
                $control->periodicity = $_POST['periodicity'];
                $control->purpose = $_POST['purpose'];
                $control->stakeholder_id = $stakeholder;
                $control->expected_cost = $_POST['expected_cost'];
                $control->save();
                //guardamos riesgos de proceso o de negocio
                if (isset($_POST['select_procesos']))
                {
                    //primero eliminamos los riesgos antiguos para no repetir
                    DB::table('control_risk_subprocess')
                        ->where('control_id','=',$control->id)
                        ->delete();
                    //ahora insertamos
                    foreach ($_POST['select_procesos'] as $subproceso)
                    {
                        DB::table('control_risk_subprocess')
                            ->insert([
                                'risk_subprocess_id' => $subproceso,
                                'control_id' => $control->id
                                ]);
                    }
                }
                else if (isset($_POST['select_objetivos']))
                {
                    //primero eliminamos los riesgos antiguos para no repetir
                    DB::table('control_objective_risk')
                        ->where('control_id','=',$control->id)
                        ->delete();
                    foreach ($_POST['select_objetivos'] as $objetivo)
                    {
                        DB::table('control_objective_risk')
                            ->insert([
                                'objective_risk_id' => $objetivo,
                                'control_id' => $control->id
                                ]);
                    }
                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Control successfully updated');
                }
                else
                {
                    Session::flash('message','Control actualizado correctamente');
                }
            });
            return Redirect::to('/controles');
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

            //vemos si tiene evaluaciones
            $rev = DB::table('control_evaluation')
                    ->where('control_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

            if (empty($rev))
            {
                //ahora vemos si tiene issues
                $rev = DB::table('issues')
                    ->where('control_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

                if (empty($rev))
                {
                    //audit_tests
                    $rev = DB::table('audit_tests')
                        ->where('control_id','=',$GLOBALS['id1'])
                        ->select('id')
                        ->get();

                    if (empty($rev))
                    {
                        //se puede borrar
                        //primero debemos borrar control_risk_subprocess o control_objective_risk según corresponda
                        $control_objective_risk = DB::table('control_objective_risk')
                                                    ->where('control_id','=',$GLOBALS['id1'])
                                                    ->select('id')
                                                    ->get();

                        if (empty($control_objective_risk)) //entonces es control de proceso
                        {
                            //borramos todos los campos de control_risk_subprocess donde el control sea el seleccionado
                            DB::table('control_risk_subprocess')
                                ->where('control_id','=',$GLOBALS['id1'])
                                ->delete();
                        }
                        else //eliminamos de control_objective_risk
                        {
                            DB::table('control_objective_risk')
                                ->where('control_id','=',$GLOBALS['id1'])
                                ->delete();
                        }

                        //ahora eliminamos el control en si
                        DB::table('controls')
                            ->where('id','=',$GLOBALS['id1'])
                            ->delete();

                        //ahora eliminamos las evidencias (si es que existen)
                        $docs = new Documentos;
                        $docs->deleteFiles('controles',$GLOBALS['id1']);

                        $GLOBALS['res'] = 0;
                    }
                }
                
            }
        });

        return $res;
    }
    //index para evaluación de controles
    public function indexEvaluacion()
    {
        if (Auth::guest())
        {
            return view('login');
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

    //ACTUALIZADO MODO DE EVALUAR CONTROLES EL 14-11-2016: Ahora se tendrá una lista con los datos del control
    public function indexEvaluacion2()
    {
        //print_r($_GET);
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //obtenemos datos del control 
            $control = \Ermtool\Control::find($_GET['control_id']);

            if ($control->stakeholder_id == NULL)
            {
                $stakeholder = NULL;
            }
            else
            {
                $stakeholder = \Ermtool\Stakeholder::getName($control->stakeholder_id);
            }

            //buscamos datos de cada una de las últimas pruebas (independiente de si se está editando o creando una nueva)
            $last_diseno = \Ermtool\control_evaluation::getLastEvaluation($_GET['control_id'],0);
            $last_efectividad = \Ermtool\control_evaluation::getLastEvaluation($_GET['control_id'],1);
            $last_sustantiva = \Ermtool\control_evaluation::getLastEvaluation($_GET['control_id'],2);
            $last_cumplimiento = \Ermtool\control_evaluation::getLastEvaluation($_GET['control_id'],3);

            $risks = \Ermtool\Risk::getRisksFromControl($_GET['organization_id'],$_GET['control_id']);

            //if ($_GET['control_kind'] == 1) //control de negocio
            //{
                if (Session::get('languaje') == 'en')
                {
                    return view('en.controles.evaluar2',['control' => $control,'risks' => $risks,'stakeholder' => $stakeholder,'last_diseno' => $last_diseno,'last_efectividad' => $last_efectividad,'last_sustantiva' => $last_sustantiva,'last_cumplimiento' => $last_cumplimiento]);
                }
                else
                {
                    return view('controles.evaluar2',['control' => $control,'risks' => $risks,'stakeholder' => $stakeholder,'last_diseno' => $last_diseno,'last_efectividad' => $last_efectividad,'last_sustantiva' => $last_sustantiva,'last_cumplimiento' => $last_cumplimiento]);
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

    public function createEvaluacion($id,$kind)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //responsables del plan de acción (si es que la prueba es inefectiva)
            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
            $control = \Ermtool\Control::name($id);
            if (Session::get('languaje') == 'en')
            {
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
                }
                return view('en.controles.create_evaluation',['kind' => $kind, 'kind2' => $kind2, 'id' => $id,'control' => $control,'stakeholders' => $stakeholders,'control_evaluation'=>NULL]);
            }
            else
            {
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
                }
                return view('controles.create_evaluation',['kind' => $kind, 'kind2' => $kind2, 'id' => $id,'control' => $control,'stakeholders' => $stakeholders,'control_evaluation'=>NULL]);
            }
        }
    }

    public function editEvaluacion($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $eval = \Ermtool\Control_evaluation::find($id);
            //responsables del plan de acción (si es que la prueba es inefectiva)
            $stakeholders = \Ermtool\Stakeholder::listStakeholders(NULL);
            $control = \Ermtool\Control::name($eval->control_id);
            if (Session::get('languaje') == 'en')
            {
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
                }
                return view('en.controles.edit_evaluation',['eval'=>$eval,'control'=>$control,'kind'=>$kind,'stakeholders' => $stakeholders,'control_evaluation'=>$eval->id,'id'=>$eval->control_id]);
            }
            else
            {
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
                }
                return view('controles.edit_evaluation',['eval'=>$eval,'control'=>$control,'kind'=>$kind,'stakeholders' => $stakeholders,'control_evaluation'=>$eval->id,'id'=>$eval->control_id]);
            }
        }
    }

    public function storeEvaluation(Request $request)
    {
        //print_r($_POST);

        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            DB::transaction(function() {
                if (isset($_POST['comments']) && $_POST['comments'] != '')
                {
                    $comments = $_POST['comments'];
                }
                else
                {
                    $comments = NULL;
                }
                $eval = \Ermtool\Control_evaluation::create([
                            'control_id' => $_POST['control_id'],
                            'description' => $_POST['description'],
                            'results' => $_POST['results'],
                            'comments' => $comments,
                            'kind' => $_POST['kind'],
                            'status' => 1
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
            });

            return Redirect::to('editar_evaluacion.'.$GLOBALS['eval2']->id);
        }
    }

    public function updateEvaluation($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function() {
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
        });

        return Redirect::to('editar_evaluacion.'.$id);
    }

    /*ACTUALIZACIÓN 21-11 función para cerrar una prueba; al cerrar una prueba se re evalua el valor del control (para ver si es efectivo o inefectivo), y su resultado es almacenado en la tabla
    control_eval_risk_temp, para luego re calcular el valor del riesgo controlado (a través de todos los controles que se encuentren en la tabla control_eval_risk_temp y que apunten a los riesgos creados en el sistema) */
    public function closeEvaluation($id)
    {
        global $id1;
        $id1 = $id;

        DB::transaction(function() {
            //primero que todo, cerramos el estado de la prueba de id = $id
            $eval = \Ermtool\Control_evaluation::find($GLOBALS['id1']);
            $eval->status = 2;
            $eval->save();

            //ahora calcularemos el resultado del control
            $control = $this->calcControlValue($eval->control_id);

            //ahora calcularemos el valor de el o los riesgos a los que apunte este control
            $eval_risk = $this->calcControlledRisk($eval->control_id);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Control test was successfully closed');
            }
            else
            {
                Session::flash('message','La prueba fue cerrada satisfactoriamente');
            }
        });

        return 0;
    }
    /*
    función identifica si se seleccionarán riesgos/subprocesos o riesgos/objetivos
    al momento de crear un control */
    public function subneg($value,$org)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            if ($value == 0) //son riesgos de subprocesos
            {
                $risks_subprocesses = \Ermtool\Risk::getRiskSubprocess($org);
            }
            else if ($value == 1) //son riesgos de negocio
            {
                //query para obtener id de objective_risk, junto a nombre de riesgo, objetivo y organización
                $objectives_risks = \Ermtool\Risk::getObjectiveRisks($org);

            }
            return json_encode($datos);
        }
    }
    //función que retornará documento de evidencia subidos, junto al control que corresponden
    public function docs($id)
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
     //   $extension = Storage::
     //   $name = $name.'.'.$;
        //$evidencia = Storage::get('controles/'.$id.'.docx');
        
     //   return response()->download('../storage/app/controles/'.$name);
    }
    //función para reportes básicos->matriz de control
    public function matrices()
    {
        if (Auth::guest())
        {
            return view('login');
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
    /***********
    * función generadora de matriz de control
    ***********/
    public function generarMatriz($value,$org)
    {
        if (Auth::guest())
        {
            return view('login');
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
            $controls = DB::table('controls')                                    
                                    ->select('controls.*')
                                    ->get();
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
                    if ($control->evidence === NULL || $control->evidence == "")
                    {
                        $evidence = "Without evidence";
                    }
                    else
                    {
                        $evidence = $control->evidence;
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
                    if ($control->evidence === NULL || $control->evidence == "")
                    {
                        $evidence = "Sin evidencia";
                    }
                    else
                    {
                        $evidence = $control->evidence;
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
                        if ($value == 0)
                        {
                            $risk_subprocess = DB::table('control_risk_subprocess')
                                                ->join('controls','controls.id','=','control_risk_subprocess.control_id')
                                                ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                                ->join('organizations','organizations.id','=','organization_subprocess.organization_id')
                                                ->where('controls.id','=',$control->id)
                                                ->where('organizations.id','=',$org)
                                                ->select('subprocesses.name as subprocess_name',
                                                    'risks.name as risk_name')
                                                ->get();
                            if ($risk_subprocess != NULL) //si es NULL, significa que el control que se está recorriendo es de negocio
                            {
                                $last = end($risk_subprocess);
                                //seteamos cada riesgo, subproceso y organización
                                foreach ($risk_subprocess as $sub_risk)
                                {
                                    if ($sub_risk != $last)
                                    {
                                        if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //agregamos &nbsp; solo si no es excel
                                        {
                                            $risk_sub .= $sub_risk->risk_name.' / '.$sub_risk->subprocess_name.', &nbsp;';
                                        }
                                        else
                                        {
                                            $risk_sub .= $sub_risk->risk_name.' / '.$sub_risk->subprocess_name.', ';
                                        }
                                    }
                                    else
                                        $risk_sub .= $sub_risk->risk_name.' / '.$sub_risk->subprocess_name;
                                }
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
                                                'Risk_Subprocess' => $risk_sub,];
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
                                                'Riesgo_Subproceso' => $risk_sub,];
                                }
                                $i += 1;
                            }
                        }
                        else if ($value == 1)
                        {
                            //obtenemos riesgos, objetivos y organización
                            $objective_risk = DB::table('control_objective_risk')
                                                ->join('controls','controls.id','=','control_objective_risk.control_id')
                                                ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                                ->where('controls.id','=',$control->id)
                                                ->where('organizations.id','=',$org)
                                                ->select('objectives.name as objective_name',
                                                    'risks.name as risk_name')
                                                ->get();
                            if ($objective_risk != NULL) //si es NULL, significa que el control que se está recorriendo es de proceso
                            {
                                $last = end($objective_risk);
                                //seteamos cada riesgo, objetivo y organización
                                foreach ($objective_risk as $obj_risk)
                                {
                                    if ($obj_risk != $last)
                                    {
                                        if (!strstr($_SERVER["REQUEST_URI"],'genexcel')) //agregamos &nbsp; solo si no es excel
                                        {
                                            $risk_obj .= $obj_risk->risk_name.' / '.$obj_risk->objective_name.', &nbsp;';
                                        }
                                        else
                                            $risk_obj .= $obj_risk->risk_name.' / '.$obj_risk->objective_name.', ';
                                        
                                    }
                                    else
                                        $risk_obj .= $obj_risk->risk_name.' / '.$obj_risk->objective_name;
                                }
                                
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
                                                'Risk_Objective' => $risk_obj,];
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
                                                'Riesgo_Objetivo' => $risk_obj,];
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
    //obtiene los controles de una organización
    public function getControls($org)
    {
        $controls = array();
        //controles de negocio
        $controles = \Ermtool\Control::getBussinessControls($org);
        
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
        $controles = \Ermtool\Control::getProcessesControls($org);

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

    public function getControls2($org,$type)
    {
        $controls = array();

        if ($type == 1)
        {
            //controles de negocio
            $controles = DB::table('controls')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org)
                        ->select('controls.id','controls.name')
                        ->distinct('controls.id')
                        ->get();

            $i = 0;

            foreach ($controles as $control)
            {
                $controls[$i] = [
                    'id' => $control->id,
                    'name' => $control->name
                ];

                $i += 1;
            }
        }
        else if ($type == 0)
        {
            //controles de proceso
            $controles = DB::table('controls')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('controls.id','controls.name')
                        ->distinct('controls.id')
                        ->get();

            $i = 0;
            
            foreach ($controles as $control)
            {
                $controls[$i] = [
                    'id' => $control->id,
                    'name' => $control->name
                ];

                $i += 1;
            }
        }
        return json_encode($controls);
    }
    //obtiene evaluación de control de id = $id
    public function getEvaluacion($id)
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
    //función obtiene datos de evaluación a través de id de la eval
    public function getEvaluacion2($id)
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
    //función obtiene issue (si es que hay) a través de id de la eval
    public function getIssue($eval_id)
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
    //obtiene descripción del control (al evaluar)
    public function getDescription($control_id)
    {
        $description = \Ermtool\Control::where('id',$control_id)->value('description');
        return json_encode($description);
    }

    public function indexGraficos()
    {
        if (Auth::guest())
        {
            return view('login');
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
    public function indexGraficos2($value,$org)
    {
        if (Auth::guest())
        {
            return view('login');
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

            //ACT. 09-12-16: SELECCIONAMOS CONTROLES DE TABLA CONTROL_EVAL_RISK_TEMP
            if ($org == 0)
            {
                $controles = \Ermtool\Control::getEvaluatedControls($_GET['organization_id']);
            }
            else
            {
                $controles = \Ermtool\Control::getEvaluatedControls($org); //en el caso de que se esté generando excel, org tendrá valor
            }

            $i = 0;
            foreach ($controles as $control)
            {
                //primero obtenemos fecha del último resultado de evaluación del control
                $max_date = DB::table('control_eval_risk_temp')
                                ->where('control_id','=',$control->id)
                                ->max('created_at');

                $controls_temp[$i] = $control->id;
                $i += 1;
                //para cada uno vemos si son efectivos o inefectivos: Si al menos una de las pruebas es inefectiva, el control es inefectivo
                //ACTUALIZACIÓN 22-11-16: Sólo verificaremos en tabla control_eval_risk_temp
                $res = DB::table('control_eval_risk_temp')
                            ->where('control_id','=',$control->id)
                            ->where('created_at','=',$max_date)
                            ->select('result')
                            ->first();
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
    public function controlledRiskCriteria()
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
    public function updateControlledRiskCriteria()
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

    //obtiene controles de objetivos de organización
    public function getObjectiveControls($org)
    {
        $controls = \Ermtool\Control::getBussinessControls($org);

        return json_encode($controls);
    }
    //obtiene controles de subproceso de una organización (por ahor (15-11-16) da lo mismo la organización ya que aunque un subproceso esté en distintas organizaciones tendrá los mismos controles)
    public function getSubprocessControls($subprocess)
    {
        $controls = \Ermtool\Control::getSubprocessControls($subprocess);

        return json_encode($controls);
    }

    //calculamos el valor del control según las pruebas que posea
    public function calcControlValue($id)
    {
        //obtenemos la última evaluación de cada una de las pruebas (independientes de si están abiertas o cerradas)
        //primero obtenemos la fecha
        $last_diseno_updated = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',0)
                            ->max('updated_at');


        if (isset($last_diseno_updated) && !empty($last_diseno_updated))
        {
            //ahora obtenemos la evaluación en esa fecha
            $last_diseno = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',0)
                            ->where('updated_at','=',$last_diseno_updated)
                            ->select('results','status')
                            ->first();

        }
        else
        {
            $last_diseno = NULL;
        }

        //hacemos lo mismo con cada una de las pruebas
        $last_efectividad_updated = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',1)
                            ->max('updated_at');

        if (isset($last_efectividad_updated) && !empty($last_efectividad_updated))
        {
            //ahora obtenemos la evaluación en esa fecha
            $last_efectividad = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',1)
                            ->where('updated_at','=',$last_efectividad_updated)
                            ->select('results','status')
                            ->first();
        }
        else
        {
            $last_efectividad = NULL;
        }

        $last_sustantiva_updated = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',2)
                            ->max('updated_at');

        if (isset($last_sustantiva_updated) && !empty($last_sustantiva_updated))
        {
            //ahora obtenemos la evaluación en esa fecha
            $last_sustantiva = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',2)
                            ->where('updated_at','=',$last_sustantiva_updated)
                            ->select('results','status')
                            ->first();
        }
        else
        {
            $last_sustantiva = NULL;
        }

        $last_cumplimiento_updated = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',3)
                            ->max('updated_at');

        if (isset($last_cumplimiento_updated) && !empty($last_cumplimiento_updated))
        {
            //ahora obtenemos la evaluación en esa fecha
            $last_cumplimiento = DB::table('control_evaluation')
                            ->where('control_id','=',$id)
                            ->where('kind','=',3)
                            ->where('updated_at','=',$last_diseno_updated)
                            ->select('results','status')
                            ->first();
        }
        else
        {
            $last_cumplimiento = NULL;
        }

        //Actualización 05-12-16: Se debe ver también la última prueba de auditoría que se encuentre cerrada y que corresponda a dicho control
        //primero obtenemos la última prueba
        $max_date = \Ermtool\Audit_test::getMaxDate($id);

        //ahora obtenemos la prueba
        $test = \Ermtool\Audit_test::getTestFromDate($max_date,$id);

        //vemos cada una de las pruebas (que estén cerradas, y sumamos en variable de efectivo en el caso de que lo sean; además guardamos el total de pruebas)
        $efectivas = 0;
        $total = 0;
        if ($last_diseno != NULL && isset($last_diseno->status) && $last_diseno->status == 2)
        {
            $total += 1;
            if ($last_diseno->results == 1)
            {
                $efectivas += 1;
            }
        }

        if ($last_efectividad != NULL && isset($last_efectividad->status) && $last_efectividad->status == 2)
        {
            $total += 1;
            if ($last_efectividad->results == 1)
            {
                $efectivas += 1;
            }
        }

        if ($last_sustantiva != NULL && isset($last_sustantiva->status) && $last_sustantiva->status == 2)
        {
            $total += 1;
            if ($last_sustantiva->results == 1)
            {
                $efectivas += 1;
            }
        }

        if ($last_cumplimiento != NULL && isset($last_cumplimiento->status) && $last_cumplimiento->status == 2)
        {
            $total += 1;
            if ($last_cumplimiento->results == 1)
            {
                $efectivas += 1;
            }
        }

        if ($test != NULL)
        {
            $total += 1;
            if ($test->results == 1)
            {
                $efectivas += 1;
            }
        }

        //ahora vemos si el total de pruebas realizadas y cerradas es igual a la cantidad de pruebas efectivas
        if ($total == $efectivas)
        {
            //guardamos en control_eval_risk_temp como control efectivo
            DB::table('control_eval_risk_temp')
                ->insert([
                    'result' => 1,
                    'control_id' => $id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
        }
        else
        {
            //guardamos como inefectivo
            DB::table('control_eval_risk_temp')
                ->insert([
                    'result' => 2,
                    'control_id' => $id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
        }

        return 0;
    }

    //función que calcula el valor del o los riesgos controlados (a través de una nueva agregación o modificación en el valor de un control)
    public function calcControlledRisk($control_id)
    {
        //primero que todo, obtenemos todos los riesgos a los que apunta este control (vemos si apunta a riesgos de proceso o de entidad)
        $risks = \Ermtool\Risk::getRiskSubprocessFromControl($control_id);
        $kind = 1; //para facilitar la manipulación posterior del riesgo

        if (empty($risks)) //entonces apunta a riesgos de entidad
        {
            $kind = 2; //para facilitar la manipulación posterior del riesgo
            $risks = \Ermtool\Risk::getObjectiveRiskFromControl($control_id);
        }


        //ahora recorremos cada uno de esos riesgos, y obtenemos los controles que tiene asociado y cuáles de estos controles posee una evaluación en la tabla control_eval_risk_temp
        foreach ($risks as $risk)
        {
            //obtenemos todos los controles de este riesgo
            if ($kind == 1) //riesgo de proceso
            {
                $controls = \Ermtool\Control::getControlsFromRiskSubprocess($risk->id);
            }
            else
            {
                $controls = \Ermtool\Control::getControlsFromObjectiveRisk($risk->id);
            }

            //ahora para cada uno de estos controles, verificamos su ÚLTIMO resultado en la tabla control_eval_risk_temp
            $efectivos = 0;
            $inefectivos = 0;
            foreach ($controls as $control)
            {
                //obtenemos ÚLTIMA fecha de creación
                $max_date = DB::table('control_eval_risk_temp')
                                ->where('control_id','=',$control->id)
                                ->max('created_at');

                //obtenemos resultado del control
                $eval = DB::table('control_eval_risk_temp')
                            ->where('control_id','=',$control->id)
                            ->where('created_at','=',$max_date)
                            ->select('result')
                            ->first();

                
                if (isset($eval) && $eval != NULL)
                {
                    if ($eval->result  == 1)
                    {
                        $efectivos += 1;
                    }
                    else
                    {
                        $inefectivos += 1;
                    }
                }
            }

            //ahora hacemos una división de todos los efectivos con la suma de efectivos más inefectivos, y si esta división es mayor o igual a 0.5, entonces el riesgo es guardado como efectivo, sino será guardado como inefectivo

            $res = $efectivos / ($efectivos + $inefectivos);

            if ($res >= 0.5)
            {
                //el riesgo es efectivo
                if ($kind == 1)
                {
                    \Ermtool\Control_evaluation::insertControlledRisk($risk->id,1,1);
                }
                else
                {
                    \Ermtool\Control_evaluation::insertControlledRisk($risk->id,1,2);
                }
            }
            else //inefectivo
            {
                if ($kind == 1)
                {
                    \Ermtool\Control_evaluation::insertControlledRisk($risk->id,2,1);
                }
                else
                {
                    \Ermtool\Control_evaluation::insertControlledRisk($risk->id,2,2);
                }
            }
        }

        return 0;
    }

    public function hallazgos($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //datos de evaluación de control
            $evaluation = \Ermtool\Control_evaluation::find($id);
            
            $issues = array();

            $issues1 = \Ermtool\Issue::getIssueByControlEvaluation($id);

            $control_name = \Ermtool\Control::name($evaluation->control_id);

            $iss = new IssuesController;
            //print_r($_POST);
            $i = 0;
            foreach ($issues1 as $issue)
            {
                
                if ($issue['plan_description'] != NULL)
                {
                    $temp = $iss->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],$issue['plan_description'],$issue['plan_status'],$issue['plan_final_date']);  
                }
                else
                {
                    $temp = $iss->formatearIssue($issue['id'],$issue['name'],$issue['classification'],$issue['recommendations'],NULL,NULL,NULL);  
                }

                $issues[$i] = [
                    'id' => $temp['id'],
                    'name' => $temp['name'],
                    'classification' => $temp['classification'],
                    'recommendations' => $temp['recommendations'],
                    'plan' => $temp['plan'],
                    'status' => $temp['status'],
                    'status_origin' => $temp['status_origin'],
                    'final_date' => $temp['final_date'],
                    'evidence' => $issue['evidences']
                ];

                $i += 1; 
            }

            $org_id = \Ermtool\Organization::getOrganizationIdFromControl($evaluation->control_id);

            
            if (Session::get('languaje') == 'en')
            {
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
                }
                return view('en.hallazgos.index3',['issues'=>$issues, 'evaluation' => $evaluation,'org_id' => $org_id,'kind' => $kind]);
            }
            else
            {
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
                }
                return view('hallazgos.index3',['issues'=>$issues, 'evaluation' => $evaluation,'control_name'=>$control_name,'org_id' => $org_id,'kind' => $kind]);
            }
        }
    }

    public function getControlsFromProcess($id,$org)
    {
        $controls = \Ermtool\Control::getControlsFromProcess($id,$org);

        return json_encode($controls);
    }

    //obtenemos controles (no repetidos) de los subprocesos seleccionados a través de jquery (al crear una prueba de auditoría) => $subprocesses = array con id de subprocesos
    public function getControlsFromSubprocess($id,$subprocesses)
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

    public function getControlsFromPerspective($org,$perspective)
    {
        $controls = \Ermtool\Control::getControlsFromPerspective($org,$perspective);

        return json_encode($controls);
    }
}