<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Storage;

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
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                $fecha_creacion = date_format($control['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($control['created_at'],"H:i:s");
            }

            //damos formato a fecha de actualización 
            if ($control['updated_at'] != NULL)
            {
                $fecha_act = date_format($control['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($control['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            //obtenemos nombre de responsable
            $stakeholder = \Ermtool\Stakeholder::find($control['stakeholder_id']);

            if ($stakeholder)
            {
                $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
            }
            else
            {
                $stakeholder2 = "No asignado";
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


        return view('controles.index',['controls' => $controls,'risk_subneg' => $risk_subneg]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');
        return view('controles.create',['stakeholders'=>$stakeholders]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validación: Si la validación es pasada, el código continua
        $this->validate($request, [
            'name' => 'required|max:45',
            'description' => 'required|max:255',
            'type' => 'required|digits:1',
            'periodicity' => 'required',
            'purpose' => 'required',
            'evidence' => 'required',
        ]);

        //print_r($_POST);

        if ($request['stakeholder_id'] == NULL)
            $stakeholder = NULL;
        else
            $stakeholder = $request['stakeholder_id'];

        //insertamos control y obtenemos ID
        $control_id = DB::table('controls')->insertGetId([
                'name'=>$request['name'],
                'description'=>$request['description'],
                'type'=>$request['type'],
                'type2'=>$request['subneg'],
                'evidence'=>$request['evidence'],
                'periodicity'=>$request['periodicity'],
                'purpose'=>$request['purpose'],
                'stakeholder_id'=>$stakeholder,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'expected_cost'=>$request['expected_cost']
                ]);

        //insertamos en control_risk_subprocess o control_objective_risk
        if ($request['subneg'] == 0) //es control de proceso
        {
            foreach ($request['select_procesos'] as $subproceso)
            {
                DB::table('control_risk_subprocess')
                    ->insert([
                        'risk_subprocess_id' => $subproceso,
                        'control_id' => $control_id
                        ]);
            }
        }
        else if ($request['subneg'] == 1) //es control de objetivo
        {
            foreach ($request['select_objetivos'] as $objetivo)
            {
                DB::table('control_objective_risk')
                    ->insert([
                        'objective_risk_id' => $objetivo,
                        'control_id' => $control_id
                        ]);
            }
        }

        //guardamos archivo de evidencia (si es que hay)
        if($request->file('evidence_doc') != NULL)
        {
            //separamos nombre archivo extension
            $file = explode('.',$request->file('evidence_doc')->getClientOriginalName());

            Storage::put(
                'controles/'. $file[0] . "___" . $control_id . "." . $file[1],
                file_get_contents($request->file('evidence_doc')->getRealPath())
            );
        }

        Session::flash('message','Control agregado correctamente');

        return Redirect::to('/controles');

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
        $control = \Ermtool\Control::find($id);
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');
        return view('controles.edit',['control'=>$control,'stakeholders'=>$stakeholders]);
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
        $control = \Ermtool\Control::find($id);

        if ($request['stakeholder_id'] == NULL)
            $stakeholder = NULL;
        else
            $stakeholder = $request['stakeholder_id'];

        //guardamos archivo de evidencia (si es que hay)
        if($request->file('evidence_doc') != NULL)
        {
            //separamos nombre archivo extension
            $file = explode('.',$request->file('evidence_doc')->getClientOriginalName());

            Storage::put(
                'controles/'. $file[0] . "___" . $id . "." . $file[1],
                file_get_contents($request->file('evidence_doc')->getRealPath())
            );
        }

        $control->name = $request['name'];
        $control->description = $request['description'];
        $control->type = $request['type'];
        $control->evidence = $request['evidence'];
        $control->periodicity = $request['periodicity'];
        $control->purpose = $request['purpose'];
        $control->stakeholder_id = $stakeholder;
        $control->expected_cost = $request['expected_cost'];

        $control->save();

        Session::flash('message','Control actualizado correctamente');

        return Redirect::to('/controles');
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

    /*
    función identifica si se seleccionarán riesgos/subprocesos o riesgos/objetivos
    al momento de crear un control */
    public function subneg($value)
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
        return view('reportes.matrices');
    }

    /***********
    * función generadora de matriz de control
    ***********/
    public function generarMatriz($value)
    {
        $i = 0; //contador de controles/subprocesos o controles/objetivos
        $datos = array();

        if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por la consulta para los datos es diferente 
                                                        //(ya que en excel los datos no se muestran igual que en html)
        {
            if ($value == 0) //Se generará la matriz de controles de procesos
            {
                $controls = DB::table('control_risk_subprocess')
                                        ->join('controls','controls.id','=','control_risk_subprocess.control_id')
                                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                        ->join('organizations','organizations.id','=','organization_subprocess.organization_id')
                                        ->select('controls.*','risks.name as risk_name',
                                                'subprocesses.name as subprocess_name',
                                                'organizations.name as organization_name')
                                        ->get();
            }

            else if ($value == 1) //Se generará matriz para controles de negocio
            {
                $controls = DB::table('control_objective_risk')
                                    ->join('controls','controls.id','=','control_objective_risk.control_id')
                                    ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('organizations','organizations.id','=','objectives.organization_id')
                                    ->select('controls.*','risks.name as risk_name',
                                            'objectives.name as objective_name',
                                            'organizations.name as organization_name')
                                    ->get();
            }
        }
        else
        {
            //obtenemos controles
            $controls = DB::table('controls')                                    
                                ->select('controls.*')
                                ->get();
        }


        foreach ($controls as $control)
        {
        
                $risk_obj_org = NULL;
                $risk_sub_org = NULL;
                // -- seteamos datos --//

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
                }

                //Seteamos purpose. 0=Preventivo, 1=Detectivo
                switch ($control->purpose)
                {
                    case 0:
                        $purpose = "Preventivo";
                    case 1:
                        $purpose = "Detectivo";
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

                /* IMPORTANTE!!!
                    Los nombres de las variables serán guardados en español para mostrarlos
                    en el archivo excel que será exportado
                */
                if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //si es que se está generando el excel solo se guardan los datos ya obtenidos
                {
                    if ($value == 0) //guardamos datos de controles de procesos
                    {
                        $datos[$i] = [//'id' => $control->id,
                                    'Control' => $control->name,
                                    'Descripción' => $control->description,
                                    'Riesgo' => $control->risk_name,
                                    'Subproceso' => $control->subprocess_name,
                                    'Organización' => $control->organization_name,
                                    'Tipo' => $type,
                                    'Periodicidad' => $periodicity,
                                    'Propósito' => $purpose,
                                    'Stakeholder' => $stakeholder2,
                                    'Evidencia' => $control->evidence,
                                    'Costo esperado' => $control->expected_cost];
                        $i += 1;
                    }

                    else if($value == 1)
                    {
                        $datos[$i] = [//'id' => $control->id,
                                    'Control' => $control->name,
                                    'Descripción' => $control->description,
                                    'Riesgo' => $control->risk_name,
                                    'Objetivo' => $control->objective_name,
                                    'Organización' => $control->organization_name,
                                    'Tipo' => $type,
                                    'Periodicidad' => $periodicity,
                                    'Propósito' => $purpose,
                                    'Stakeholder' => $stakeholder2,
                                    'Evidencia' => $control->evidence,
                                    'Costo esperado' => $control->expected_cost];
                        $i += 1;
                    }
                }
                else //los datos son mostrados en html
                {
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
                                            ->select('subprocesses.name as subprocess_name',
                                                'organizations.name as organization_name',
                                                'risks.name as risk_name')
                                            ->get();

                        if ($risk_subprocess != NULL) //si es NULL, significa que el control que se está recorriendo es de negocio
                        {
                            //seteamos cada riesgo, subproceso y organización
                            foreach ($risk_subprocess as $risk_sub)
                            {
                                    $risk_sub_org .= '<li>'.$risk_sub->risk_name.' / '.$risk_sub->subprocess_name.
                                                     ' / '.$risk_sub->organization_name.'</li>';
                            }
                            $datos[$i] = [//'id' => $control->id,
                                        'Control' => $control->name,
                                        'Descripción' => $control->description,
                                        'Riesgo_Subproceso_Organización' => $risk_sub_org,
                                        'Tipo' => $type,
                                        'Periodicidad' => $periodicity,
                                        'Propósito' => $purpose,
                                        'Stakeholder' => $stakeholder2,
                                        'Evidencia' => $control->evidence,
                                        'Costo_esperado' => $control->expected_cost];
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
                                            ->select('objectives.name as objective_name',
                                                'organizations.name as organization_name',
                                                'risks.name as risk_name')
                                            ->get();

                        if ($objective_risk != NULL) //si es NULL, significa que el control que se está recorriendo es de proceso
                        {
                            //seteamos cada riesgo, objetivo y organización
                            foreach ($objective_risk as $obj_risk)
                            {
                                    $risk_obj_org .= '<li>'.$obj_risk->risk_name.' / '.$obj_risk->objective_name.
                                                     ' / '.$obj_risk->organization_name.'</li>';
                            }
                            
                                           
                            $datos[$i] = [//'id' => $control->id,
                                        'Control' => $control->name,
                                        'Descripción' => $control->description,
                                        'Riesgo_Objetivo_Organización' => $risk_obj_org,
                                        'Tipo' => $type,
                                        'Periodicidad' => $periodicity,
                                        'Propósito' => $purpose,
                                        'Stakeholder' => $stakeholder2,
                                        'Evidencia' => $control->evidence,
                                        'Costo_esperado' => $control->expected_cost];
                            $i += 1;
                        }
                    }
                }
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcel')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $datos;
        }
        else
            return json_encode($datos);
    }
}