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
            $controls = array();
            $risk_subneg = array(); //array para almacenar riesgos y subprocesos u objetivos asociados 
                                    //(si es objetivos, además se almacena organización)
            $i = 0; //contador de controles
            $j = 0; //contador de riesgos y subprocesos u objetivos

            $controles = \Ermtool\Control::all();

            foreach ($controles as $control) //se recorre cada uno de los controles creados (de existir)
            {

                //obtenemos todos los riesgos asociados al control (sean de subprocesos o de negocio)

                if ($control['type2'] == 0) //el control está asociado a un riesgo de subproceso
                {
                    $risks_subprocesses = DB::table('control_risk_subprocess')
                                ->join('risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                                ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                                ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                ->where('control_risk_subprocess.control_id','=',$control['id'])
                                ->select('subprocesses.name as sub_name','risks.name as risk_name')
                                ->get();

                    //almacenamos los nombres de los riesgos y subprocesos u objetivos asociados asociados al control
                    foreach ($risks_subprocesses as $risk_subprocess)
                    {
                        $risk_subneg[$j] = array('control_id' => $control['id'],
                                                'organization' => NULL,
                                                'subneg' => $risk_subprocess->sub_name,
                                                'risk' => $risk_subprocess->risk_name);
                        $j += 1;
                    }
                }

                else if ($control['type2'] == 1) //el control está asociado a un riesgo de negocio
                {
                    $objectives_risks = DB::table('control_objective_risk')
                                ->join('objective_risk','control_objective_risk.objective_risk_id','=','objective_risk.id')
                                ->join('objectives','objective_risk.objective_id','=','objectives.id')
                                ->join('organizations','organizations.id','=','objectives.organization_id')
                                ->join('risks','objective_risk.risk_id','=','risks.id')
                                ->where('control_objective_risk.control_id','=',$control['id'])
                                ->select('objectives.name as obj_name','risks.name as risk_name','organizations.name as org_name')
                                ->get();

                    //almacenamos los nombres de los riesgos y subprocessos asociados al control
                    foreach ($objectives_risks as $objective_risk)
                    {
                        $risk_subneg[$j] = array('control_id' => $control['id'],
                                                'organization' => $objective_risk->org_name,
                                                'subneg' => $objective_risk->obj_name,
                                                'risk' => $objective_risk->risk_name);
                        $j += 1;
                    }
                }

                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($control['created_at'] == NULL OR $control['created_at'] == "0000-00-00" OR $control['created_at'] == "")
                {
                    $fecha_creacion = NULL;
                }

                else
                {
                    $fecha_creacion = date_format($control['created_at'],"d-m-Y");
                }

                //damos formato a fecha de actualización 
                if ($control['updated_at'] != NULL)
                {
                    $fecha_act = date_format($control['updated_at'],"d-m-Y");
                }

                else
                    $fecha_act = NULL;

                //obtenemos nombre de responsable
                $stakeholder = \Ermtool\Stakeholder::find($control['stakeholder_id']);

                if ($stakeholder)
                {
                    $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
                }
                else
                {
                    $stakeholder2 = NULL;
                }
                $controls[$i] = array('id'=>$control['id'],
                                    'name'=>$control['name'],
                                    'description'=>$control['description'],
                                    'type'=>$control['type'],
                                    'type2'=>$control['type2'],
                                    'created_at'=>$fecha_creacion,
                                    'updated_at'=>$fecha_act,
                                    'evidence'=>$control['evidence'],
                                    'periodicity'=>$control['periodicity'],
                                    'purpose'=>$control['purpose'],
                                    'stakeholder'=>$stakeholder2);
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.index',['controls' => $controls,'risk_subneg' => $risk_subneg]);
            }
            else
            {
                return view('controles.index',['controls' => $controls,'risk_subneg' => $risk_subneg]);
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
            $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
            ->orderBy('name')
            ->lists('full_name', 'id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.create',['stakeholders'=>$stakeholders]);
            }
            else
            {
                return view('controles.create',['stakeholders'=>$stakeholders]);
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

                    //guardamos archivo de evidencia (si es que hay)
                    if($GLOBALS['evidence'] != NULL)
                    {
                        upload_file($GLOBALS['evidence'],'controles',$control_id);
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

                //guardamos archivo de evidencia (si es que hay)
                if($GLOBALS['evidence'] != NULL)
                {
                    upload_file($GLOBALS['evidence'],'controles',$control->id);
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
        //
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
            $stakeholders = array();
            $controles = \Ermtool\Control::lists('name','id');

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
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.controles.evaluar',['controls'=>$controles,'stakeholders'=>$stakeholders]);
            }
            else
            {
                return view('controles.evaluar',['controls'=>$controles,'stakeholders'=>$stakeholders]);
            }
        }
    }

    //guarda evaluación de un control
    public function storeEvaluacion(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //para guardar en todas las tablas exactamente la misma fecha
            global $date;
            $date = date('Y-m-d H:i:s');
            //variables globales de evidencia
            global $file_diseno;
            global $file_efectividad;
            global $file_sustantiva;
            global $file_cumplimiento;
            $file_diseno = $request->file('file_diseno');
            $file_efectividad = $request->file('file_efectividad');
            $file_sustantiva = $request->file('file_sustantiva');
            $file_cumplimiento = $request->file('file_cumplimiento');
            //echo "Diseño: ".$file_diseno."Efectividad: ".$file_efectividad."Sustantiva: ".$file_sustantiva."Cumplimiento: ".$file_cumplimiento;

            //vemos si se está guardando una nueva evaluación o si se está editando una previa
            //print_r($_POST);
            
            if ($_POST['guardar'] == 0) //se está guardando una evaluación nueva
            {
                DB::transaction (function() {
                    //primero que todo, verificaremos que no haya una evaluación anterior abierta, y si es así, la cerramos
                    $last_eval = DB::table('control_evaluation')
                                ->where('control_id','=',$_POST['control_id'])
                                ->where('status','=',1)
                                ->select('control_evaluation.id')
                                ->get();

                    //sólo si se que hay hará la actualización
                    foreach ($last_eval as $eval)
                    {
                        //actualizamos dejando status en 2
                        DB::table('control_evaluation')
                            ->where('id','=',$eval->id)
                            ->update([ 'status'=>2 ]);
                    }

                    //si es que se evaluó diseño
                    if ($_POST['diseno'] != "")
                    {
                        $this->storeTests('diseno',0);
                    }
                    //lo mismo con efectividad operativa
                    if ($_POST['efectividad'] != "")
                    {
                        $this->storeTests('efectividad',1);
                    }
                    //lo mismo con pruebas sustantivas
                    if ($_POST['sustantiva'] != "")
                    {
                        $this->storeTests('sustantiva',2);
                    }
                    //lo mismo con pruebas de cumplimiento
                    if ($_POST['cumplimiento'] != "")
                    {
                        $this->storeTests('cumplimiento',3);
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Assessment successfully done');
                    }
                    else
                    {
                        Session::flash('message','Evaluación realizada correctamente');
                    }
                });
            }
            else if ($_POST['guardar'] == 1) //se está editando una evaluación previa
            {
                DB::transaction (function() {
                    //si es que se evaluó diseño
                    if ($_POST['diseno'] != "")
                    {
                        $this->editTests('diseno',0);
                    }
                    //lo mismo con efectividad operativa
                    if ($_POST['efectividad'] != "")
                    {
                        $this->editTests('efectividad',1);
                    }
                    //lo mismo con pruebas sustantivas
                    if ($_POST['sustantiva'] != "")
                    {
                        $this->editTests('sustantiva',2);
                    }
                    //lo mismo con pruebas de cumplimiento
                    if ($_POST['cumplimiento'] != "")
                    {
                        $this->editTests('cumplimiento',3);
                    }

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Assessment successfully updated');
                    }
                    else
                    {
                        Session::flash('message','Evaluación actualizada correctamente');
                    }
                });
            }

            $res = 0;
            //si el resultado de cualquier prueba es efectiva, se calcula el riesgo controlado
            if ($_POST['diseno'] == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST['diseno']);
            }
            else if ($_POST['efectividad'] == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST['efectividad']);
            }
            else if ($_POST['sustantiva'] == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST['sustantiva']);
            }
            else if ($_POST['cumplimiento'] == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST['cumplimiento']);
            }
                if (Session::get('languaje') == 'en')
                {
                    if ($res == 0)
                    {
                        Session::flash('message','Assessment successfully conducted');
                    }
                    else if ($res == 1)
                    {
                        Session::flash('message','Error storing the assessment');
                    }
                }
                else
                {
                    if ($res == 0)
                    {
                        Session::flash('message','Evaluación realizada correctamente');
                    }
                    else if ($res == 1)
                    {
                        Session::flash('message','Error al realizar la evaluación');
                    }
                }

            return Redirect::to('/evaluar_controles');
        }
    }

    /*
    función identifica si se seleccionarán riesgos/subprocesos o riesgos/objetivos
    al momento de crear un control */
    public function subneg($value)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $i = 0; //contador de riesgos/subprocesos o riesgos/objetivos
            $datos = array();
            if ($value == 0) //son riesgos de subprocesos
            {
                $risks_subprocesses = DB::table('risk_subprocess')
                                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                        ->select('risk_subprocess.id as id','risks.name as risk_name',
                                                'subprocesses.name as subprocess_name')
                                        ->get();

                foreach ($risks_subprocesses as $risk_subprocess)
                {
                    $datos[$i] = ['id' => $risk_subprocess->id,
                                'risk_name' => $risk_subprocess->risk_name,
                                'subprocess_name' => $risk_subprocess->subprocess_name];
                    $i += 1;
                }
            }
            else if ($value == 1) //son riesgos de negocio
            {
                //query para obtener id de objective_risk, junto a nombre de riesgo, objetivo y organización
                $objectives_risks = DB::table('objective_risk')
                                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                        ->join('risks','risks.id','=','objective_risk.risk_id')
                                        ->join('organizations','organizations.id','=','objectives.organization_id')
                                        ->select('objective_risk.id as id','risks.name as risk_name',
                                                'objectives.name as objective_name',
                                                'organizations.name as organization_name')
                                        ->get();

                foreach ($objectives_risks as $objective_risk)
                {
                    $datos[$i] = ['id' => $objective_risk->id,
                                'risk_name' => $objective_risk->risk_name,
                                'objective_name' => $objective_risk->objective_name,
                                'organization_name' => $objective_risk->organization_name];
                    $i += 1;
                }
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

    public function getControls($org,$type)
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

     //guarda pruebas de diseño, sustantivas, efectividad y cumplimiento (y otros tipos si es que hubieran)
    public function storeTests($test,$kind)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            
         if (isset($_POST['comentarios_'.$test]))
            {
                $comments = $_POST['comentarios_'.$test];
            }
            else
            {
                $comments = NULL;
            }
            //ahora guardamos datos de ISSUE si es que la prueba fue inefectiva
            if ($_POST[$test] == 2)
            {
                        if (isset($_POST['clasificacion_'.$test]))
                        {
                            $class = $_POST['clasificacion_'.$test];
                        }
                        else
                        {
                            $class = NULL;
                        }
                        $issue_id = DB::table('issues')
                                ->insertGetId([
                                    'classification' => $class,
                                    'name' => $_POST['name_hallazgo_'.$test],
                                    'description' => $_POST['description_hallazgo_'.$test],
                                    'recommendations' => $_POST['recomendaciones_'.$test],
                                    'created_at' => $GLOBALS['date'],
                                    'updated_at' => $GLOBALS['date'],
                                ]);

                         //vemos si existe responsable de diseño
                        if (isset($_POST['responsable_plan_'.$test]))
                        {
                            $responsable = $_POST['responsable_plan_'.$test];
                        }
                        else
                        {
                            $responsable = NULL;
                        }
                        //insertamos plan de acción
                        DB::table('action_plans')
                            ->insert([
                                'issue_id' => $issue_id,
                                'stakeholder_id' => $responsable,
                                'description' => $_POST['plan_accion_'.$test],
                                'created_at' => $GLOBALS['date'],
                                'updated_at' => $GLOBALS['date'],
                                'final_date' => $_POST['fecha_plan_'.$test],
                                'status' => 0
                                ]);
            }
            else
            {
                $issue_id = NULL;
            }

                        
            //si es inefectiva issue_id tendrá valor, si no será NULL
            $id_eval = DB::table('control_evaluation')
                    ->insertGetId([
                        'control_id' => $_POST['control_id'],
                        'kind' => $kind,
                        'comments' => $comments,
                        'results' => $_POST[$test],
                        'created_at' => $GLOBALS['date'],
                        'updated_at' => $GLOBALS['date'],
                        'status' => 1,
                        'issue_id' => $issue_id
                    ]);                           
            
            //cargamos evidencia
            if ($GLOBALS['file_'.$test] != NULL)
            {
                upload_file($GLOBALS['file_'.$test],'eval_controles',$id_eval);    
            }

            //si es que la prueba es de efectividad operativa, actualizamos riesgo controlado
            if ($kind == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST[$test]);

                if (Session::get('languaje') == 'en')
                {
                    if ($res == 0)
                    {
                        echo "Controlled risk successfully stored";
                    }
                    else if ($res == 1)
                    {
                        echo "Error storing the value of controlled risk";
                    }
                }
                else
                {
                    if ($res == 0)
                    {
                        echo "Riesgo controlado guardado correctamente";
                    }
                    else if ($res == 1)
                    {
                        echo "Error al guardar valor de riesgo controlado";
                    }
                }
            }
        }       
    }

     //guarda nueva versión (o primera si es que no existia) de pruebas de diseño, sustantivas, efectividad y cumplimiento (y otros tipos si es que hubieran)
    public function editTests($test,$kind)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //primero que todo, obtenemos datos de prueba anterior si es que existía
            $last_test = DB::table('control_evaluation')
                            ->where('status','=',1)
                            ->where('control_id','=',$_POST['control_id'])
                            ->where('kind','=',$kind)
                            ->select('id','results','issue_id')
                            ->first();

            if (isset($_POST['comentarios_'.$test]))
            {
                $comments = $_POST['comentarios_'.$test];
            }
            else
            {
                $comments = NULL;
            }
            //ahora guardamos datos de ISSUE si es que la prueba fue inefectiva
            if ($_POST[$test] == 2)
            {
                if (isset($_POST['clasificacion_'.$test]))
                {
                    $class = $_POST['clasificacion_'.$test];
                }
                else
                {
                    $class = NULL;
                }
                //vemos si anteriormente era inefectiva, si lo era, se debe actualizar issue. De lo contrario se crea uno nuevo
                if ($last_test->issue_id == NULL)
                {
                    
                    $issue_id = DB::table('issues')
                            ->insertGetId([
                                'classification' => $class,
                                'name' => $_POST['name_hallazgo_'.$test],
                                'description' => $_POST['description_hallazgo_'.$test],
                                'recommendations' => $_POST['recomendaciones_'.$test],
                                'created_at' => $GLOBALS['date'],
                                'updated_at' => $GLOBALS['date'],
                            ]);
                }
                else //sólo debe ser actualizado
                {
                    $issue_id = $last_test->issue_id; //se guarda para después comprobar plan de acción
                    DB::table('issues')
                        ->where('id','=',$issue_id)
                        ->update([
                            'classification' => $class,
                            'name' => $_POST['name_hallazgo_'.$test],
                            'description' => $_POST['description_hallazgo_'.$test],
                            'recommendations' => $_POST['recomendaciones_'.$test],
                            'updated_at' => $GLOBALS['date'],
                        ]);
                }

                $action_plan = NULL;
                //ahora vemos si existe un plan de acción para el issue 
                $action_plan = DB::table('action_plans')
                            ->where('issue_id','=',$issue_id)
                            ->select('id')
                            ->first();

                if ($action_plan == NULL) //agregamos nuevo plan de acción
                {
                    //vemos si existe responsable de diseño
                    if (isset($_POST['responsable_plan_'.$test]))
                    {
                        $responsable = $_POST['responsable_plan_'.$test];
                    }
                    else
                    {
                        $responsable = NULL;
                    }
                    //insertamos plan de acción
                    DB::table('action_plans')
                        ->insert([
                            'issue_id' => $issue_id,
                            'stakeholder_id' => $responsable,
                            'description' => $_POST['plan_accion_'.$test],
                            'created_at' => $GLOBALS['date'],
                            'updated_at' => $GLOBALS['date'],
                            'final_date' => $_POST['fecha_plan_'.$test],
                            'status' => 0
                            ]);
                }
                else //actualizamos plan de acción
                {
                    //vemos si existe responsable de diseño
                    if (isset($_POST['responsable_plan_'.$test]))
                    {
                        $responsable = $_POST['responsable_plan_'.$test];
                    }
                    else
                    {
                        $responsable = NULL;
                    }
                    //actualizamos plan de acción
                    DB::table('action_plans')
                        ->where('id','=',$action_plan->id)
                        ->update([
                            'issue_id' => $issue_id,
                            'stakeholder_id' => $responsable,
                            'description' => $_POST['plan_accion_'.$test],
                            'updated_at' => $GLOBALS['date'],
                            'final_date' => $_POST['fecha_plan_'.$test],
                            'status' => 0
                            ]);
                }
            }
            else
            {
                $issue_id = NULL;
            }

            //vemos ahora si la evaluación existia anteriormente o es nueva (se pueden haber creado una nueva prueba para una evaluación de un control)
            $id_eval_prev = NULL;
            $id_eval_prev = DB::table('control_evaluation')
                        ->where('control_id','=',$_POST['control_id'])
                        ->where('kind','=',$kind)
                        ->where('status','=',1)
                        ->select('id')
                        ->first();

            if ($id_eval_prev == NULL)
            {
                //si es inefectiva issue_id tendrá valor, si no será NULL
                $id_eval = DB::table('control_evaluation')
                        ->insertGetId([
                            'control_id' => $_POST['control_id'],
                            'kind' => $kind,
                            'comments' => $comments,
                            'results' => $_POST[$test],
                            'created_at' => $GLOBALS['date'],
                            'updated_at' => $GLOBALS['date'],
                            'status' => 1,
                            'issue_id' => $issue_id
                        ]);
            }
            else
            {
                $id_eval = $id_eval_prev->id;
                DB::table('control_evaluation')
                        ->where('id','=',$id_eval_prev->id)
                        ->update([
                            'control_id' => $_POST['control_id'],
                            'kind' => $kind,
                            'comments' => $comments,
                            'results' => $_POST[$test],
                            'updated_at' => $GLOBALS['date'],
                            'status' => 1,
                            'issue_id' => $issue_id
                        ]);
            }                  
                                       
            
            //**************** vemos si es que hay una evidencia cargada *******************
            //OBS: Lo anteriorun no es necesario, ya que por ahora (y no sé si habrá que hacerlo) la evidencia se agrega solo una vez y no hay opción a cambio de decisión
                    
            //cargamos evidencia
            if ($GLOBALS['file_'.$test] != NULL)
            {
                upload_file($GLOBALS['file_'.$test],'eval_controles',$id_eval);    
            }

            //si es que la prueba es de efectividad operativa, actualizamos riesgo controlado
            /* ACTUALIZACIÓN 30-08: El cálculo de riesgo controlado se hará cada vez que se hace una evaluación, independiente de su resultado o del tipo de ésta
            if ($kind == 1)
            {
                $res = calc_controlled_risk($_POST['control_id'],$_POST[$test]);
                if (Session::get('languaje') == 'en')
                {
                    if ($res == 0)
                    {
                        echo "Controlled risk successfully updated";
                    }
                    else if ($res == 1)
                    {
                        echo "Error updating the value of controlled risk";
                    }
                }
                else
                {
                    if ($res == 0)
                    {
                        echo "Riesgo controlado actualizado correctamente";
                    }
                    else if ($res == 1)
                    {
                        echo "Error al actualizar valor de riesgo controlado";
                    }
                }
            } */
        }         
    }

    public function indexGraficos($value)
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
            $controles = DB::table('control_evaluation')
                            //->where('control_evaluation.status','=',2)
                            ->distinct()
                            ->get(['control_id as id']);

            $i = 0;
            foreach ($controles as $control)
            {
                $controls_temp[$i] = $control->id;
                $i += 1;

                //para cada uno vemos si son efectivos o inefectivos: Si al menos una de las pruebas es inefectiva, el control es inefectivo
                $res = DB::table('control_evaluation')
                            ->where('control_id','=',$control->id)
                            ->where('results','=',2)
                            ->first();

                if ($res)
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
            $controles = DB::table('audit_tests')
                            ->where('audit_tests.status','=',2)
                            ->whereNotNull('audit_tests.control_id')
                            ->whereNotIn('audit_tests.control_id',$controls_temp)
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
                if ($value == 1) //reporte excel de controles ejecutados
                {
                    $i = 0;
                    foreach ($controls as $control)
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $res[$i] = [
                                'Name' => $control['name'],
                                'Description' => $control['description'],
                                'Updated date' => $control['updated_at'],
                            ];
                        }
                        else
                        {
                            $res[$i] = [
                                'Nombre' => $control['name'],
                                'Descripción' => $control['description'],
                                'Actualizado' => $control['updated_at'],
                            ];
                        }

                        $i += 1;
                    }

                    return $res;
                }

                else if ($value == 2) //reporte excel de controles no ejecutados
                {
                    $i = 0;
                    foreach ($no_ejecutados as $control)
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            $res[$i] = [
                                'Name' => $control['name'],
                                'Description' => $control['description'],
                                'Updated date' => $control['updated_at'],
                            ];
                        }
                        else
                        {
                            $res[$i] = [
                                'Nombre' => $control['name'],
                                'Descripción' => $control['description'],
                                'Actualizado' => $control['updated_at'],
                            ];
                        }

                        $i += 1;
                    }

                    return $res;
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
                                $res[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }    
                        }

                        $i += 1;
                    }

                    return $res;
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
                                $res[$i] = [
                                    'Name' => $control['name'],
                                    'Description' => $control['description'],
                                    'Updated date' => $control['updated_at'],
                                ];
                            }
                            else
                            {
                                $res[$i] = [
                                    'Nombre' => $control['name'],
                                    'Descripción' => $control['description'],
                                    'Actualizado' => $control['updated_at'],
                                ];
                            }    
                        }

                        $i += 1;
                    }

                    return $res;
                }
                
            }
            else
            {
               if (Session::get('languaje') == 'en')
                {
                    return view('en.reportes.controles_graficos',['controls'=>$controls,'no_ejecutados'=>$no_ejecutados,
                                                  'cont_ejec' => $cont_ejec,'cont_no_ejec'=>$cont_no_ejec,
                                                  'efectivos' => $efectivos,'inefectivos'=>$inefectivos]);
                }
                else
                {
                    return view('reportes.controles_graficos',['controls'=>$controls,'no_ejecutados'=>$no_ejecutados,
                                                  'cont_ejec' => $cont_ejec,'cont_no_ejec'=>$cont_no_ejec,
                                                  'efectivos' => $efectivos,'inefectivos'=>$inefectivos]);
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
}