<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Redirect;
use Session;
use dateTime;
use Storage;

class AuditoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $planes = array();
        $plans = \Ermtool\Audit_plan::all();
        $i = 0; //contador de planes

        foreach ($plans as $plan)
        {
            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($plan['created_at'] == NULL OR $plan['created_at'] == "0000-00-00" OR $plan['created_at'] == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                $fecha_creacion = date_format($plan['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($plan['created_at'],"H:i:s");
            }

            //damos formato a fecha de actualización 
            if ($plan['updated_at'] != NULL)
            {
                $fecha_act = date_format($plan['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($plan['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $planes[$i] = [
                            'id' => $plan['id'],
                            'name' => $plan['name'],
                            'description' => $plan['description'],
                            'created_at' => $fecha_creacion,
                            'updated_at' => $fecha_act
                        ];
            $i += 1;
        }

        return view('auditorias.index',['planes' => $planes]);
    }

    public function indexAuditorias()
    {
        $audits = array();
        $auditorias = \Ermtool\Audit::all();
        $i = 0; //contador de planes

        foreach ($auditorias as $audit)
        {
            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($audit['created_at'] == NULL OR $audit['created_at'] == "0000-00-00" OR $audit['created_at'] == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                $fecha_creacion = date_format($audit['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($audit['created_at'],"H:i:s");
            }

            //damos formato a fecha de actualización 
            if ($audit['updated_at'] != NULL)
            {
                $fecha_act = date_format($audit['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($audit['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $audits[$i] = [
                            'id' => $audit['id'],
                            'name' => $audit['name'],
                            'description' => $audit['description'],
                            'created_at' => $fecha_creacion,
                            'updated_at' => $fecha_act
                        ];
            $i += 1;
        }
        return view('auditorias.index_auditorias',['audits'=>$audits]);
    }


    //Creación de plan de auditoría
    public function create()
    {
        //obtenemos lista de stakeholders
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        //obtenemos lista de organizaciones
        $organizations = \Ermtool\Organization::lists('name','id');

        //obtenemos universo de auditorias
        $audits = \Ermtool\Audit::lists('name','id');

        //obtenemos riesgos de proceso
        $risk_subprocess = DB::table('risk_subprocess')
                                ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                ->where('risks.type2','=',1)
                                ->select('risk_subprocess.id AS riskid','risks.name')
                                ->lists('risks.name','riskid');

        //obtenemos riesgos de negocio


        return view('auditorias.create',['stakeholders'=>$stakeholders,'organizations'=>$organizations,
                                        'audits'=>$audits,'risk_subprocess'=>$risk_subprocess]);
    }

    public function createPruebas()
    {
        //plan de auditoría
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        $audit_tests = \Ermtool\Audit_test::lists('name','id');

        //echo $audit_tests;

        return view('auditorias.create_test',['audit_plans'=>$audit_plans,'audit_tests'=>$audit_tests]);     
    }

    public function createAuditoria()
    {
        return view('auditorias.create_auditoria');
    }

    public function storePrueba(Request $request)
    {
        //print_r($_POST);

        //si es que no tiene valor kind, significa que es una prueba nueva
        if ($request['kind'] == "")
        {
            $audit_test = DB::table('audit_tests')
                                ->insertGetId([
                                    'name' => $request['name'],
                                    'description' => $request['description'],
                                    'type' => $request['type'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
        }
        else
        {
            $audit_test = \Ermtool\Audit_test::find($request['audit_test_id'])->value('id');            
        }

            //insertamos en audit_audit_plan_audit_test
            $audit_audit_plan_audit_test = DB::table('audit_audit_plan_audit_test')
                        ->insertGetId([
                                'audit_test_id' => $audit_test,
                                'audit_audit_plan_id' => $request['audit'],
                                'results' => 2,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

            //insertamos controles de proceso (si es que hay)
            if (isset($request['control_subprocess_id']))
            {
                foreach ($request['control_subprocess_id'] as $control_sub_id)
                {
                DB::table('audit_control_risk')
                    ->insert([
                        'control_id' => $control_sub_id,
                        'audit_audit_plan_audit_test_id' => $audit_audit_plan_audit_test
                        ]);
                }
            }
            
            //insertamos controles de negocio (si es que hay)
            if (isset($request['control_objective_id']))
            {
                foreach ($request['control_objective_id'] as $control_obj_id)
                {
                DB::table('audit_control_risk')
                    ->insert([
                        'control_id' => $control_obj_id,
                        'audit_audit_plan_audit_test_id' => $audit_audit_plan_audit_test
                        ]);
                }
            }

            $i = 1; //contador de actividades
            //insertamos cada una de las actividades
            while (isset($request['activity_'.$i]))
            {
                DB::table('activities')
                    ->insert([
                        'name' => $request['activity_'.$i],
                        'results' => 0,
                        'audit_audit_plan_audit_test_id' => $audit_audit_plan_audit_test,
                        'status' => 0
                        ]);
                $i += 1;    
            }

        Session::flash('message','Prueba de auditor&iacute;a creada correctamente');
        return Redirect::to('/crear_pruebas');
        
    }

    public function storeAuditoria(Request $request)
    {
        \Ermtool\Audit::create([
            'name' => $request['name'],
            'description' => $request['description']
            ]);

        Session::flash('message','Auditor&iacute;a creada correctamente');

        return Redirect::to('/auditorias');
    }

    public function ejecutar()
    {
        //plan de auditoría
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        $audit_tests = \Ermtool\Audit_test::lists('name','id');

        return view('auditorias.ejecutar',['audit_plans' => $audit_plans]);
    }

    public function storeEjecution(Request $request)
    {
        //print_r($_POST);

        //primero que todo, actualizamos las actividades
        //para esto, separamos primer string del array id_activities por sus comas
        $id_activities = explode(',',$request['id_activities'][0]);

        foreach ($id_activities as $id)
        {
            //actualizamos resultados (si es que el estado de la prueba es cerrado (igual a 2))
            if ($request['status_'.$id] == 2)
            {
                //actualizamos actividad de identificador $id (status y results)
                DB::table('activities')
                    ->where('id','=',$id)
                    ->update([ 
                        'status' => $request['status_'.$id],
                        'results' => $request['result_'.$id],
                        'updated_at' => date('Y-m-d H:i:s')
                        ]);
            }

            else
            {
                //actualizamos actividad de identificador $id
                DB::table('activities')
                    ->where('id','=',$id)
                    ->update([ 
                        'status' => $request['status_'.$id],
                        'updated_at' => date('Y-m-d H:i:s') ]);
            }
        }

        //ahora actualizamos resultados de prueba (si es que hay)
        //para esto, primero separamos primer string del array tests_id

        $tests_id = explode(',',$request['tests_id'][0]);

        foreach ($tests_id as $id)
        {
            //si es que la prueba se asigna como inefectiva (0) se deberá ingresar un comentario
            if ($request['test_result_'.$id] == 0)
            {
                DB::table('issues')
                    ->where('id','=',$id)
                    ->insert([
                        'name' => $request['issue_name_'.$id],
                        'description' => $request['issue_description_'.$id],
                        'recommendations' => $request['issue_recommendations_'.$id],
                        'classification' => $request['issue_classification_'.$id],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'audit_audit_plan_audit_test_id' => $id
                        ]);

            }

            //actualizamos resultado de la prueba

            DB::table('audit_audit_plan_audit_test')
                ->where('id','=',$id)
                ->update(['results' => $request['test_result_'.$id]]);   
        }
        
        Session::flash('message','Auditor&iacute;a ejecutada correctamente');

        return Redirect::to('/ejecutar_pruebas');


    }

    public function supervisar()
    {
        //plan de auditoría
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        $audit_tests = \Ermtool\Audit_test::lists('name','id');

        return view('auditorias.supervisar',['audit_plans' => $audit_plans]);
    }

    public function storeSupervision(Request $request)
    {
        //print_r($_POST);

        //primero vemos si se está agregando una nota o una evidencia
        
            $res = DB::table('notes')
                    ->insertGetId([
                        'name' => $request['name_'.$request['test_id']],
                        'description' => $request['description_'.$request['test_id']],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'audit_audit_plan_audit_test_id' => $request['test_id'],
                        'status' => 0
                        ]);
        
            //guardamos archivo de evidencia (si es que hay)
            if($request->file('evidencia_'.$request['test_id']) != NULL)
            {
                //separamos nombre archivo extension
                $file = explode('.',$request->file('evidencia_'.$request['test_id'])->getClientOriginalName());

                Storage::put(
                    'evidencias_notas/'. $file[0] . "___" . $res . "." . $file[1],
                    file_get_contents($request->file('evidencia_'.$request['test_id'])->getRealPath())
                );
            }

            if ($res)
            {
                Session::flash('message','Nota agregada correctamente');

                return Redirect::to('/supervisar');
            }

            else
            {
                Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');

                return Redirect::to('/supervisar');
            }   
        
    }
    //Antes de almacenar un plan de auditoría, se deben ingresar los datos necesarios
    //para crear un auditoría perteneciente a dicho plan (audit_audit_plan)
    //En esta función se envían los datos del plan para poder generarlo despues en conjunto a su auditoría
    public function datosAuditoria(Request $request)
    {
        //print_r($_POST);
        $audits = array();
        $plan = array();
        $objective_id = array();
        $objective_risk_id = array();
        $risk_subprocess_id = array();
        $stakeholder_team = array();

        //primero obtenemos datos de auditorias
        $i = 0;
        foreach ($request['audits'] as $audit)
        {
            $auditoria = DB::table('audits')
                        ->where('id','=',$audit)
                        ->select('name')
                        ->get();

            foreach ($auditoria as $auditoria2)
            {
                $audits[$i] = [
                        'id' => $audit,
                        'name' => $auditoria2->name
                        ];
                $i += 1;
            }
        }
        //ahora guardamos (para mandar) los datos del plan de auditoría que se creará
        $plan = [
                'organization_id' => $request['organization_id'],
                'name' => $request['name'],
                'description' => $request['description'],
                'objectives' => $request['objectives'],
                'scopes' => $request['scopes'],
                'resources' => $request['resources'],
                'stakeholder_id' => $request['stakeholder_id'],
                'methodology' => $request['methodology'],
                'initial_date' => $request['initial_date'],
                'final_date' => $request['final_date'],
                'rules' => $request['rules']
        ];

        //mandamos equipo de stakeholders (auditores) si es que hay
        if (isset($request['stakeholder_team']))
        {
            foreach ($request['stakeholder_team'] as $stakeholder)
            {
                $stakeholder_team[$i] = $stakeholder;
                $i += 1;
            }
        }

        //mandamos objetivos
        $i = 0;
        foreach ($request['objective_id'] as $objective)
        {
            $objective_id[$i] = $objective;
            $i += 1;
        }


            //obtenemos lista de nombre e id de riesgos de objetivo
            $i = 0;
            if (isset($request['objective_risk_id']))
            {
                foreach ($request['objective_risk_id'] as $objective_risk)
                {
                    //obtenemos nombre del riesgo de objetivo
                    $risk_name = DB::table('objective_risk')
                                    ->where('objective_risk.id',$objective_risk)
                                    ->join('risks','objective_risk.risk_id','=','risks.id')
                                    ->select('risks.name')
                                    ->get();

                    //almacenamos id y nombre del riesgo
                    foreach ($risk_name as $name)
                    {
                        $objective_risk_id[$i] = [
                                    'id' => $objective_risk,
                                    'name' => $name->name
                                    ];
                        $i += 1;    
                    }
                }
            }

            //obtenemos lista de nombre e id de riesgos de proceso
            $i = 0;
            if (isset($request['risk_subprocess_id']))
            {
                foreach ($request['risk_subprocess_id'] as $risk_subprocess)
                {
                    //obtenemos nombre del riesgo de de proceso
                    $risk_name = DB::table('risk_subprocess')
                                    ->where('risk_subprocess.id',$risk_subprocess)
                                    ->join('risks','risk_subprocess.risk_id','=','risks.id')
                                    ->select('risks.name')
                                    ->get();

                    //almacenamos id y nombre del riesgo
                    foreach ($risk_name as $name)
                    {
                        $risk_subprocess_id[$i] = [
                                    'id' => $risk_subprocess,
                                    'name' => $name->name
                                    ];
                        $i += 1;    
                    }
                }
            }

        $i = 0;

        return view('auditorias.create2',['audits' => $audits,
                                          'plan' => $plan, 
                                          'objective_id' => $objective_id,
                                          'objective_risk_id' => $objective_risk_id,
                                          'risk_subprocess_id' => $risk_subprocess_id,
                                          'stakeholder_team' => $stakeholder_team]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //print_r($_POST);

        //damos formato a fecha de inicio
        $fecha = explode("/",$request['initial_date']);
        $fecha_inicio = $fecha[2]."-".$fecha[0]."-".$fecha[1];

        //damos formato a fecha de término
        $fecha = explode("/",$request['final_date']);
        $fecha_termino = $fecha[2]."-".$fecha[0]."-".$fecha[1];

        //insertamos plan y obtenemos ID
        $audit_plan_id = DB::table('audit_plans')->insertGetId([
                'name'=>$request['name'],
                'description'=>$request['description'],
                'objectives'=>$request['objectives'],
                'scopes'=>$request['scopes'],
                'status'=>0,
                'resources'=>$request['resources'],
                'methodology'=>$request['methodology'],
                'initial_date'=>$fecha_inicio,
                'final_date'=>$fecha_termino,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'rules'=>$request['rules'],
                'organization_id'=>$request['organization_id']
                ]);

        //insertamos en audit_plan_stakeholder primero al encargado del plan y luego al equipo
                DB::table('audit_plan_stakeholder')
                    ->insert([
                        'role' => 0,
                        'audit_plan_id' => $audit_plan_id,
                        'stakeholder_id' => $request['stakeholder_id']
                        ]);

        //ahora insertamos equipo de stakeholders (si es que hay)
        if (isset($request['stakeholder_team']))
        {
            foreach ($request['stakeholder_team'] as $stakes)
            {
                DB::table('audit_plan_stakeholder')
                        ->insert([
                            'role' => 1,
                            'audit_plan_id' => $audit_plan_id,
                            'stakeholder_id' => $stakes
                            ]);
            }
        }

         //insertamos riesgos del negocio (si es que existen)
        if (isset($request['objective_risk_id']))
        {
            foreach ($request['objective_risk_id'] as $objective_risk)
            {
                    DB::table('audit_plan_risk')
                        ->insert([
                            'audit_plan_id' => $audit_plan_id,
                            'objective_risk_id' => $objective_risk
                            ]);
            }
        }
        //insertamos riesgos de proceso (si es que existen)
        if (isset($request['risk_subprocess_id']))
        {
            foreach ($request['risk_subprocess_id'] as $risk_subprocess)
            {
                    DB::table('audit_plan_risk')
                        ->insert([
                            'audit_plan_id' => $audit_plan_id,
                            'risk_subprocess_id' => $risk_subprocess
                            ]);
            }
        }

        //ahora guardamos auditorías que no son nuevas

        //insertamos cada auditoria (de universo de auditorias) en audit_audit_plan
        $i = 1;
        if (isset($request['audits']))
        {
            foreach ($request['audits'] as $audit)
            {
                //insertamos y obtenemos id para ingresarlo en audit_risk y otros
                $audit_audit_plan_id = DB::table('audit_audit_plan')
                            ->insertGetId([
                                'audit_plan_id' => $audit_plan_id,
                                'audit_id' => $audit,
                                'initial_date' => $request['audit_'.$audit.'_initial_date'],
                                'final_date' => $request['audit_'.$audit.'_final_date'],
                                'resources' => $request['audit_'.$audit.'_resources']
                                ]);
                
                //insertamos riesgos de negocio de la auditoría
                if (isset($request['audit_'.$audit.'_objective_risks']))
                {
                    foreach ($request['audit_'.$audit.'_objective_risks'] as $audit_objective_risk)
                    {
                        //insertamos audit risks
                        DB::table('audit_risk')
                            ->insert([
                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                    'objective_risk_id' => $audit_objective_risk
                                ]);
                    }
                }

                //insertamos riesgos de proceso de la auditoría
                if (isset($request['audit_'.$audit.'_risk_subprocess']))
                {
                    foreach ($request['audit_'.$audit.'_risk_subprocess'] as $audit_risk_subprocess)
                    {
                        //insertamos audit risks
                        DB::table('audit_risk')
                            ->insert([
                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                    'risk_subprocess_id' => $audit_risk_subprocess
                                ]);
                    }
                }
            }

            //ahora guardamos auditorías nuevas
            $i = 1; //contador para auditorías nuevas
            while (isset($request['audit_new'.$i.'_name']))
            {
                //primero insertamos en tabla audits y obtenemos id
                $audit_id = DB::table('audits')
                            ->insertGetId([
                                'name' => $request['audit_new'.$i.'_name'],
                                'description' => $request['audit_new'.$i.'_description'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                                ]);

                //ahora insertamos en audit_audit_plan
                $audit_audit_plan_id = DB::table('audit_audit_plan')
                            ->insertGetId([
                                'audit_plan_id' => $audit_plan_id,
                                'audit_id' => $audit_id,
                                'initial_date' => $request['audit_new'.$i.'_initial_date'],
                                'final_date' => $request['audit_new'.$i.'_final_date'],
                                'resources' => $request['audit_new'.$i.'_resources']
                                ]);

                //insertamos riesgos de negocio (de haber)
               if (isset($request['audit_new'.$i.'_objective_risks']))
               {
                    foreach ($request['audit_new'.$i.'_objective_risks'] as $objective_risk)
                    {
                        DB::table('audit_risk')
                            ->insert([
                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                    'objective_risk_id' => $objective_risk
                                ]);
                    }
               }

               //insertamos nuevo riesgo de proceso (de haber)
               if (isset($request['audit_new'.$i.'_objective_risks']))
               {
                    foreach ($request['audit_new'.$i.'_risk_subprocess'] as $risk_subprocess)
                    {
                        DB::table('audit_risk')
                            ->insert([
                                    'audit_audit_plan_id' => $audit_audit_plan_id,
                                    'risk_subprocess_id' => $risk_subprocess
                                ]);
                    }
               }

               $i += 1;
            }
        }

        Session::flash('message','Plan de auditor&iacute;a generado correctamente');

        return Redirect::to('/plan_auditoria');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auditorias = array();
        $plan_auditoria = array();
        $objetivos = array();
        $organizacion = NULL;
        $riesgos_neg = array();
        $riesgos_proc = array();

        $j = 0; //contador de auditorías
        $k = 0; //contador de objetivos (nombre)
        $l = 0; //contador de riesgos de negocio
        $i = 0; //contador de riesgos de proceso

        //obtenemos plan de auditoría
        $audit_plan = \Ermtool\Audit_plan::all()->where('id',(int)$id);

        foreach ($audit_plan as $plan)
        {
            //damos formato a estado
            if ($plan['status'] == 0)
            {
                $estado = 'Abierto';
            }
            else if ($plan['status'] == 1)
            {
                $estado = 'Cerrado';
            }

            //damos formato a fecha inicial
            $fecha_inicial = date("d-m-Y",strtotime($plan['initial_date']));

            //damos formato a fecha final
            $fecha_final = date("d-m-Y",strtotime($plan['final_date']));

            //obtenemos organizacion
            $organization = \Ermtool\Organization::all()->where('id',$plan['organization_id']);

            foreach ($organization as $org)
            {
                //guardamos nombre de organizacion (siempre será el mismo)
                $organizacion = $org['name'];
            }

            //obtenemos objetivos de negocios y riesgos, según los riesgos de negocio asociados al plan
            $objective = DB::table('audit_plan_risk')
                                ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->where('audit_plan_risk.audit_plan_id','=',$plan['id'])
                                ->whereNotNull('audit_plan_risk.objective_risk_id')
                                ->select('objectives.name','objectives.id')
                                ->distinct()
                                ->get();

            foreach ($objective as $obj)
            {
                $objetivos[$k] = $obj->name;
                $k += 1;

                //obtenemos riesgos de negocio asociados al objetivo
                $objective_risk = DB::table('risks')
                                    ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                                    ->where('objective_risk.objective_id','=',$obj->id)
                                    ->select('risks.name')
                                    ->get();

                foreach ($objective_risk as $risk)
                {
                    $riesgos_neg[$l] = $risk->name;
                    $l += 1;
                }
            }

            //obtenemos riesgos de procesos
            $risk_subprocess = DB::table('audit_plan_risk')
                                    ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->where('audit_plan_risk.audit_plan_id','=',$plan['id'])
                                    ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                                    ->select('risks.name')
                                    ->get();

            foreach ($risk_subprocess as $risk)
            {
                $riesgos_proc[$i] = $risk->name;
                $i += 1;
            }

            //obtenemos universo de auditorías
            $audits = DB::table('audit_audit_plan')
                        ->where('audit_plan_id','=',$plan['id'])
                        ->get();

            //cada una de las auditorías pertenecientes al plan
            foreach ($audits as $audit)
            {
                //obtenemos datos de auditoría
                $audite = \Ermtool\Audit::all()->where('id',$audit->audit_id);

                foreach ($audite as $audit1)
                {
                    $auditorias[$j] = [
                                    'name' => $audit1['name'],
                                    'description' => $audit1['description']
                                    ];
                    $j += 1;
                }
            }

            //obtenemos riesgos relacionados
            //$risks = DB::table('audit_plan_risk')

            $plan_auditoria = [
                            'name' => $plan['name'],
                            'description' => $plan['description'],
                            'objectives' => $plan['objectives'],
                            'scopes' => $plan['scopes'],
                            'status' => $estado,
                            'resources' => $plan['resources'],
                            'methodology' => $plan['methodology'],
                            'initial_date' => $fecha_inicial,
                            'final_date' => $fecha_final,
                            'rules' => $plan['rules']
                            ];

            return view('auditorias.show',['plan_auditoria' => $plan_auditoria,
                                            'auditorias' => $auditorias,
                                            'objetivos' => $objetivos,
                                            'organizacion' => $organizacion,
                                            'riesgos_proc' => $riesgos_proc,
                                            'riesgos_neg' => $riesgos_neg]);
        }

    }

    public function showAuditoria($id)
    {
        $planes = array();

        $k = 0; //contador de planes

        //obtenemos auditoría
        $audite = \Ermtool\Audit::all()->where('id',(int)$id);

        foreach ($audite as $audit)
        {
            //obtenemos planes en los que se está aplicando
            $audit_plans = DB::table('audit_audit_plan')
                        ->where('audit_id','=',$audit['id'])
                        ->get();

            //cada una de los planes en los que se aplica la auditoria
            foreach ($audit_plans as $plan)
            {
                //obtenemos nombre del plan
                $plan_name = \Ermtool\Audit_plan::where('id',$plan->audit_plan_id)->value('name');

                $planes[$k] = [
                        'id' => $plan->audit_plan_id,
                        'name' => $plan_name,
                ];

                $k += 1;
            }

            $auditoria = [
                        'id' => $audit['id'],
                        'name' => $audit['name'],
                        'description' => $audit['description']
            ];
        }

        return view('auditorias.show_audit',['auditoria' => $auditoria, 'planes' => $planes]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

    //función para ver todas las pruebas
    public function pruebas()
    {
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        return view('auditorias.pruebas',['audit_plans' => $audit_plans]);
    }

    //función para revisar las notas agregadas por el auditor jefe
    public function notas()
    {
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        return view('auditorias.notas',['audit_plans' => $audit_plans]);
    }

    //función para responder una nota por parte de un auditori
    public function responderNota(Request $request)
    {
        //print_r($_POST);

        $id = $request['note_id'];

        //insertamos y obtenemos id para verificar que se guarde
        $res = DB::table('notes_answers')
                ->insertGetId([
                        'answer' => $request['answer_'.$id],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'note_id' => $id 
                    ]);

        //guardamos archivo de evidencia (si es que hay)
        if($request->file('evidencia_'.$id) != NULL)
        {
            //separamos nombre archivo extension
            $file = explode('.',$request->file('evidencia_'.$id)->getClientOriginalName());

            Storage::put(
                'evidencias_resp_notas/'. $file[0] . "___" . $res . "." . $file[1],
                file_get_contents($request->file('evidencia_'.$id)->getRealPath())
            );
        }

        if ($res)
        {
            Session::flash('message','Respuesta agregada correctamente');

            return Redirect::to('/notas');
        }

        else
        {
            Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');

            return Redirect::to('/notas');
        }  
    }

    public function actionPlans()
    {
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');
        //$stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        //->orderBy('name')
        //->lists('full_name', 'id');

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

        return view('auditorias.planes_accion',['audit_plans' => $audit_plans,'stakeholders' => $stakeholders]);
    }

    public function storePlan(Request $request)
    {
        //print_r($_POST);

        //id del issue que se está agregando
        $id = $request['issue_id'];

        $new_id = DB::table('action_plans')
                        ->insertGetId([
                                'issue_id' => $id,
                                'stakeholder_id' => $request['responsable_'.$id],
                                'description' => $request['description_'.$id],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'final_date' => $request['final_date_'.$id],
                                'status' => 0
                            ]);

        if ($new_id)
        {
            Session::flash('message','Plan de acción agregado correctamente');

            return Redirect::to('/planes_accion');
        }
        else
        {
            Session::flash('error','Problema al agregar plan de acción. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');

            return Redirect::to('/planes_accion');
        }

    }

    //función para reporte de planes de acción
    public function actionPlansReport()
    {
        $organizations = \Ermtool\Organization::lists('name','id');

        return view('reportes.planes_accion',['organizations' => $organizations]);
    }

    public function generarReportePlanes($org)
    {
        $results = array();
        $i = 0;
        //obtenemos datos de plan de auditoría, auditoría, issue y plan de acción
        $action_plans = DB::table('action_plans')
            ->join('issues','issues.id','=','action_plans.issue_id')
            ->join('audit_audit_plan_audit_test','audit_audit_plan_audit_test.id','=','issues.audit_audit_plan_audit_test_id')
            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_test.audit_audit_plan_id')
            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
            ->join('audits','audits.id','=','audit_audit_plan.audit_id')
            ->where('audit_plans.organization_id','=',(int)$org)
            ->select('audit_plans.name as audit_plan_name',
                     'audits.name as audit_name',
                     'issues.name as issue_name',
                     'action_plans.description',
                     'action_plans.final_date',
                     'action_plans.created_at',
                     'action_plans.status')
            ->get();

        foreach ($action_plans as $action_plan)
        {
            $fecha_creacion = date('d-m-Y',strtotime($action_plan->created_at));
            $fecha_creacion .= ' a las '.date('H:i:s',strtotime($action_plan->created_at));

            //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
            $fecha_final = date('d-m-Y',strtotime($action_plan->final_date));
            $fecha_final .= ' a las 00:00:00';

            if ($action_plan->status == 0)
            {
                $estado = 'Abierto';
            }
            else if ($action_plan->status == 1)
            {
                $estado = 'Cerrado';
            }
            else
            {
                $estado = 'Error al obtener estado';
            }

            $results[$i] = [
                        'Plan_de_auditoría' => $action_plan->audit_plan_name,
                        'Auditoría' => $action_plan->audit_name,
                        'Debilidad' => $action_plan->issue_name,
                        'Plan_de_acción' => $action_plan->description,
                        'Estado' => $estado,
                        'Fecha_creación' => $fecha_creacion,
                        'Fecha_final' => $fecha_final,
            ];

            $i += 1;
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcelplan')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $results;
        }
        else
            return json_encode($results);
    }


    //función que obtiene objetivos y riesgos de objetivos a través de JsON para la creación de un plan de pruebas
    public function getObjetivos($org)
    {
        $results = array();
        $objectives = \Ermtool\Objective::all()->where('organization_id',(int)$org);
        $i = 0; //contador de objetivos
        foreach ($objectives as $objective)
        {
            $results[$i] = [
                            'name' => $objective['name'],
                            'id' => $objective['id']
                            ];


            $i += 1;
        }

        return json_encode($results);
    }

    //Función obtiene riesgos de negocio a través de JSON al crear plan de pruebas
    public function getRiesgosObjetivos($org)
    {
        $results = array();
        $i = 0; //contador de riesgos de negocio
            //obtenemos riesgos de negocio para la organización seleccionada
            $objective_risk = DB::table('objective_risk')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('objectives.organization_id','=',(int)$org)
                                ->groupBy('risks.id')
                                ->select('risks.name','objective_risk.id as riskid')
                                ->get();

        foreach ($objective_risk as $risk)
        {
            //obtengo maxima fecha para obtener última evaluación
            $fecha = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.objective_risk_id','=',$risk->riskid)
                            ->max('updated_at');

            //obtenemos evaluación de riesgo de negocio (si es que hay)---> Ultima (mayor fecha updated_at)
            $evaluations = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.objective_risk_id','=',$risk->riskid)
                            ->where('evaluations.consolidation','=',1)
                            ->whereNotNull('evaluation_risk.avg_probability')
                            ->whereNotNull('evaluation_risk.avg_impact')
                            ->where('evaluations.updated_at','=',$fecha)
                            ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                            ->get();

            //seteamos por si no hay evaluación
            $avg_probability = "Falta Eval.";
            $avg_impact = "Falta Eval.";
            $proba_def = "";
            $impact_def = "";

            foreach ($evaluations as $evaluation)
            {
                //seteamos nombres de probabilidad e impacto

                switch ($evaluation->avg_probability)
                {
                    case 1:
                        $proba = 'Muy poco probable';
                        break;
                    case 2:
                        $proba = 'Poco probable';
                        break;
                    case 3:
                        $proba = 'Intermedio';
                        break;
                    case 4:
                        $proba = 'Probable';
                        break;
                    case 5:
                        $proba = 'Muy probable';
                        break;
                }

                switch ($evaluation->avg_impact)
                {
                    case 1:
                        $impact = 'Muy poco impacto';
                        break;
                    case 2:
                        $impact = 'Poco impacto';
                        break;
                    case 3:
                        $impact = 'Intermedio';
                        break;
                    case 4:
                        $impact = 'Alto impacto';
                        break;
                    case 5:
                        $impact = 'Muy alto impacto';
                        break;
                }

                $avg_probability = $evaluation->avg_probability;
                $avg_impact = $evaluation->avg_impact;
                $impact_def = $impact;
                $proba_def = $proba;
            }

            $results[$i] = [
                        'name' => $risk->name,
                         'id' => $risk->riskid,
                         'avg_probability' => $avg_probability,
                         'avg_impact' => $avg_impact,
                         'proba_def' => $proba_def,
                         'impact_def' => $impact_def
                        ];
            $i += 1;
        }

        return json_encode($results);   
    }

    //Función obtiene riesgos de proceso a través de JSON al crear plan de pruebas
    public function getRiesgosProcesos($org)
    {
        $results = array();
        $i = 0; //contador de riesgos de proceso
            //obtenemos riesgos de proceso para la organización seleccionada
            $risk_subprocess = DB::table('risk_subprocess')
                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                                ->join('organizations','organizations.id','=','organization_subprocess.organization_id')
                                ->where('organizations.id','=',(int)$org)
                                ->groupBy('risks.id')
                                ->select('risks.name','risk_subprocess.id as riskid')
                                ->get();

        foreach ($risk_subprocess as $risk)
        {
            //obtengo maxima fecha para obtener última evaluación
            $fecha = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.risk_subprocess_id','=',$risk->riskid)
                            ->max('updated_at');

            //obtenemos evaluación de riesgo de negocio (si es que hay)---> Ultima (mayor fecha updated_at)
            $evaluations = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->where('evaluation_risk.risk_subprocess_id','=',$risk->riskid)
                            ->where('evaluations.consolidation','=',1)
                            ->whereNotNull('evaluation_risk.avg_probability')
                            ->whereNotNull('evaluation_risk.avg_impact')
                            ->where('evaluations.updated_at','=',$fecha)
                            ->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                            ->get();

            //seteamos por si no hay evaluación
            $avg_probability = "Falta Eval.";
            $avg_impact = "Falta Eval.";
            $proba_def = "";
            $impact_def = "";

            foreach ($evaluations as $evaluation)
            {
            //seteamos nombres de probabilidad e impacto

                switch ($evaluation->avg_probability)
                {
                    case 1:
                        $proba = 'Muy poco probable';
                        break;
                    case 2:
                        $proba = 'Poco probable';
                        break;
                    case 3:
                        $proba = 'Intermedio';
                        break;
                    case 4:
                        $proba = 'Probable';
                        break;
                    case 5:
                        $proba = 'Muy probable';
                        break;
                }

                switch ($evaluation->avg_impact)
                {
                    case 1:
                        $impact = 'Muy poco impacto';
                        break;
                    case 2:
                        $impact = 'Poco impacto';
                        break;
                    case 3:
                        $impact = 'Intermedio';
                        break;
                    case 4:
                        $impact = 'Alto impacto';
                        break;
                    case 5:
                        $impact = 'Muy alto impacto';
                        break;
                }

                $avg_probability = $evaluation->avg_probability;
                $avg_impact = $evaluation->avg_impact;
                $impact_def = $impact;
                $proba_def = $proba;
            }
            $results[$i] = [
                        'name' => $risk->name,
                         'id' => $risk->riskid,
                         'avg_probability' => $avg_probability,
                         'avg_impact' => $avg_impact,
                         'proba_def' => $proba_def,
                         'impact_def' => $impact_def,
                        ];
            $i += 1;
        }

        return json_encode($results);   
    }

    //Función que obtiene todos los stakeholders menos auditor responsable, al crear plan de auditoría
    public function getStakeholders($rut)
    {
        $results = array();
        $i = 0; //contador de usuarios

        $users = DB::table('stakeholders')
                    ->where('id','<>',(int)$rut)
                    ->select('stakeholders.name','stakeholders.surnames','stakeholders.id')
                    ->get();

        foreach ($users as $user)
        {
            $results[$i] = [
                    'name' => $user->name.' '.$user->surnames,
                    'id' => $user->id
            ];
            $i += 1;
        }

        return json_encode($results);
    }

    //función que obtiene una prueba de auditoría (al crear una nueva prueba basada en una antigua)
    public function getAuditTest($id)
    {
        $results = array();
        $activities2 = array();
        //Seleccionamos prueba de auditoría y actividades
        $audit_test = DB::table('audit_tests')
                    ->where('id','=',$id)
                    ->select('audit_tests.name','audit_tests.id','audit_tests.description',
                            'audit_tests.type')
                    ->get();

        foreach ($audit_test as $test)
        {
            $i = 0; //contador de actividades
            //obtenemos actividades
            $activities = DB::table('activities')
                            ->join('audit_audit_plan_audit_test','audit_audit_plan_audit_test.id','=','activities.audit_audit_plan_audit_test_id')
                            ->where('audit_audit_plan_audit_test.audit_test_id','=',$test->id)
                            ->select('activities.name')
                            ->get();

            foreach ($activities as $activity)
            {
                $activities2[$i] = $activity->name;
                $i += 1;
            }

            switch ($test->type)
            {
                case 0:
                    $tipo = 'Prueba de diseño';
                    break;
                case 1:
                    $tipo = 'Prueba de efectividad operativa';
                    break;
                case 2:
                    $tipo = 'Prueba de cumplimiento';
                    break;
                case 3:
                    $tipo = 'Prueba sustantiva';
                    break;
            }
            $results = [
                    'name' => $test->name,
                    'id' => $test->id,
                    'description' => $test->description,
                    'type' => $test->type,
                    'type_name' => $tipo,
                    'activities' => $activities2
            ];
        }

        return json_encode($results);
    }

    /*función que obtiene los datos y las actividades de una prueba de auditoría (al revisar un plan de auditoría)
    a través del identificador de audit_audit_plan (auditoría + plan de auditoría) */
    public function getAuditTest2($id)
    {
        $audit_tests = array();
        $results = array();
        
        $j = 0; //contador de pruebas de auditoría
        //Seleccionamos pruebas de auditorías y actividades
        $audit_test = DB::table('audit_audit_plan_audit_test')
                    ->join('audit_tests','audit_tests.id','=','audit_audit_plan_audit_test.audit_test_id')
                    ->where('audit_audit_plan_audit_test.audit_audit_plan_id','=',$id)
                    ->select('audit_tests.name','audit_audit_plan_audit_test.id','audit_tests.description',
                            'audit_audit_plan_audit_test.results')
                    ->get();

        foreach ($audit_test as $test)
        {
            $i = 0; //contador de actividades
            //obtenemos actividades
            $activities = DB::table('activities')
                            ->join('audit_audit_plan_audit_test','audit_audit_plan_audit_test.id','=','activities.audit_audit_plan_audit_test_id')
                            ->where('audit_audit_plan_audit_test.id','=',$test->id)
                            ->select('activities.name','activities.results','activities.id','activities.status')
                            ->get();

            $activities2 = array(); //seteamos en 0 variable de actividades

            foreach ($activities as $activity)
            {
                switch ($activity->results)
                {
                    case 0:
                        $activities_result = 'Inefectiva';
                        break;
                    case 1:
                        $activities_result = 'Efectiva';
                        break;
                    case 2:
                        $activities_result = 'En proceso';
                        break;
                }

                $activities2[$i] = [
                        'name' => $activity->name,
                        'result' => $activities_result,
                        'id' => $activity->id,
                        'status' => $activity->status
                        ];

                $i += 1;
            }

            //obtenemos issues
            $issues = DB::table('issues')
                            ->join('audit_audit_plan_audit_test','audit_audit_plan_audit_test.id','=','issues.audit_audit_plan_audit_test_id')
                            ->where('audit_audit_plan_audit_test.id','=',$test->id)
                            ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                            ->get();

            $debilidades = array();
            $i = 0;
            foreach ($issues as $issue)
            {
                switch($issue->classification)
                {
                    case 0:
                        $class = 'Oportunidad de mejora';
                        break;
                    case 1:
                        $class = 'Deficiencia';
                        break;
                    case 2:
                        $class = 'Debilidad significativa';
                        break;
                }

                $debilidades[$i] = [
                    'id' => $issue->id,
                    'name' => $issue->name,
                    'description' => $issue->description,
                    'classification' => $class,
                    'recommendations' => $issue->recommendations
                ];

                $i += 1;
            }

            $audit_tests[$j] = [
                    'name' => $test->name,
                    'id' => $test->id,
                    'description' => $test->description,
                    'results' => $test->results,
                    'activities' => $activities2,
                    'issues' => $debilidades,
            ];

            $j += 1;
        }

        $results = $audit_tests;

        return json_encode($results);
    }

    //obtiene los controles relacionados a un plan de auditoría (según la organización que esté involucrada
    //con el plan de auditoría)
    public function getObjectiveControls($id)
    {
        $results = array();
        $objective_risks = array();
        $i = 0; //contador de pruebas

        //obtendremos riesgos de negocio
        $objective_risk = DB::table('audit_plan_risk')
                    ->join('control_objective_risk','control_objective_risk.objective_risk_id','=','audit_plan_risk.objective_risk_id')
                    ->join('controls','control_objective_risk.control_id','=','controls.id')
                    ->where('audit_plan_risk.audit_plan_id','=',$id)
                    ->where('audit_plan_risk.objective_risk_id','<>','NULL')
                    ->select('controls.name','controls.id')
                    ->groupBy('controls.id')
                    ->get();

        foreach ($objective_risk as $obj)
        {
            $results[$i] = [
                    'name' => $obj->name,
                    'id' => $obj->id,
            ];
            $i += 1;
        }

        return json_encode($results);
    }

    //obtiene los controles relacionados a un plan de auditoría (según la organización que esté involucrada
    //con el plan de auditoría)
    public function getSubprocessControls($id)
    {
        $results = array();
        $objective_risks = array();
        $i = 0; //contador de pruebas

        //obtendremos controles de procesos
        $risk_subprocess = DB::table('audit_plan_risk')
                    ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','audit_plan_risk.risk_subprocess_id')
                    ->join('controls','control_risk_subprocess.control_id','=','controls.id')
                    ->where('audit_plan_risk.audit_plan_id','=',$id)
                    ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                    ->select('controls.name','controls.id')
                    ->groupBy('controls.id')
                    ->get();

        foreach ($risk_subprocess as $sub)
        {
            $results[$i] = [
                    'name' => $sub->name,
                    'id' => $sub->id,
            ];
            $i += 1;
        }

        return json_encode($results);
    }

    //obtiene auditorías relacionadas a un plan de auditoría
    public function getAudits($id)
    {
        $results = array();
        $objective_risks = array();
        $i = 0; //contador de auditorias

        //obtendremos auditorias
        $audits = DB::table('audit_audit_plan')
                    ->where('audit_audit_plan.audit_plan_id','=',$id)
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->select('audits.name AS name','audit_audit_plan.id AS id')
                    ->get();

        foreach ($audits as $audit)
        {
            $results[$i] = [
                    'name' => $audit->name,
                    'id' => $audit->id,
            ];
            $i += 1;
        }

        return json_encode($results);
    }
    //obtiene pruebas asociadas a una plan auditoria + auditoria
    public function getTests($id)
    {
        $results = array();
        $i = 0; //contador de pruebas

        //obtenemos pruebas
        $tests = DB::table('audit_audit_plan_audit_test')
                    ->join('audit_audit_plan','audit_audit_plan_audit_test.audit_audit_plan_id','=',$id)
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->join('audit_plans','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                    ->join('audit_tests','audit_tests.id','=','audit_audit_plan_audit_test.audit_test_id')
                    ->select('audit_tests.name AS name','audit_plans.name AS audit_plan_name',
                            'audits.name AS audit_name','audit_tests.description AS description',
                            'audit_tests.activities AS activities')
                    ->get();
        foreach ($tests as $test)
        {
            $results[$i] = [
                    'audit_plan_name' => $test->audit_plan_name,
                    'audit_name' => $test->audit_name,
                    'name' => $test->name,
                    'description' => $test->description,
                    'activities' => $test->activities
            ];
            $i += 1;
        }

        return json_encode($results);
    }

    //obtiene datos asociados al último plan de auditoría generado para una organización
    public function getAuditPlan($org)
    {
        $results = array();
        $auditorias = array();
        $sub_plan_risks = array();
        $obj_plan_risks = array();
        $audit_tests = array();
        $i = 0; //contador de pruebas

        //primero obtenemos último id de plan de auditoría generado para una organización
        $audit_plan_id = DB::table('audit_plans')
                                ->where('organization_id','=',$org)
                                ->max('id');

        //verificamos que existe plan
        if ($audit_plan_id)
        {
            //primero obtenemos info del plan
            $audit_plan_info = \Ermtool\Audit_plan::find($audit_plan_id);

            //obtenemos stakeholders
            $stakeholders = DB::table('audit_plan_stakeholder')
                                ->join('audit_plans','audit_plans.id','=','audit_plan_stakeholder.audit_plan_id')
                                ->join('stakeholders','stakeholders.id','=','audit_plan_stakeholder.stakeholder_id')
                                ->where('audit_plan_stakeholder.audit_plan_id','=',$audit_plan_id)
                                ->select('stakeholders.name','stakeholders.surnames','audit_plan_stakeholder.role')
                                ->get();

            $responsable = NULL;
            $users = NULL;
            $j = 0; //contador de stakeholders
            foreach ($stakeholders as $stakes)
            {
                if ($stakes->role == 0)
                {
                    $responsable = $stakes->name.' '.$stakes->surnames;
                }
                else
                {
                    $users[$j] = $stakes->name.' '.$stakes->surnames;
                    $j += 1;    
                }
                
            }

             //obtenemos riesgos de negocio del plan
            $objective_risks = DB::table('audit_plan_risk')
                                ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('audit_plan_risk.audit_plan_id','=',$audit_plan_id)
                                ->select('objective_risk.id as id','risks.name as risk_name','objectives.name as objective_name')
                                ->get();

            //obtenemos riesgos de negocio del plan
            $subprocess_risks = DB::table('audit_plan_risk')
                                ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                ->join('processes','processes.id','=','subprocesses.process_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('audit_plan_risk.audit_plan_id','=',$audit_plan_id)
                                ->select('risk_subprocess.id as id','risks.name as risk_name','subprocesses.name as subprocess_name',
                                        'processes.name as process_name')
                                ->get();

            $j = 0; //contador de riesgos en cero
            //seteamos en variables los riesgos de negocio
            foreach ($objective_risks as $objective_risk)
            {

                $obj_plan_risks[$j] = ['name' => $objective_risk->risk_name,
                                       'objective_name' => $objective_risk->objective_name,
                                       ];
                $j += 1;
            }

            $j = 0; //contador de riesgos en cero
            //seteamos en variables los riesgos de proceso
            foreach ($subprocess_risks as $subprocess_risk)
            {
                $sub_plan_risks[$j] = ['name' => $subprocess_risk->risk_name,
                                       'subprocess_name' => $subprocess_risk->subprocess_name,
                                       'process_name' => $subprocess_risk->process_name];
                $j += 1;
            }

            
            //ahora obtenemos los datos requeridos de auditoría para el plan
            $audits = DB::table('audit_audit_plan')
                                ->join('audits','audits.id','=','audit_audit_plan.id')
                                ->where('audit_audit_plan.audit_plan_id','=',$audit_plan_id)
                                ->select('audit_audit_plan.id as id','audits.name as name',
                                        'audits.description as description','audit_audit_plan.initial_date as audit_initial_date',
                                        'audit_audit_plan.final_date as audit_final_date','audit_audit_plan.resources as resources')
                                ->get();

            foreach ($audits as $audit)
            {
                //formateamos datos
                if ($audit->resources == NULL)
                {
                    $resources = 'No hay informaci&oacute;n';
                }
                else
                    $resources = $audit->resources;

                if ($audit->audit_initial_date == NULL)
                {
                    $initial_date = 'No hay informaci&oacute;n';
                }
                else
                {
                    //ordenamos fechas
                    $initial_date_tmp = new DateTime($audit->audit_initial_date);
                    $initial_date = date_format($initial_date_tmp, 'd-m-Y');
                }
                 if ($audit->audit_final_date == NULL)
                {
                    $final_date = 'No hay informaci&oacute;n';
                }
                else
                {
                    $final_date_tmp = new DateTime($audit->audit_final_date);
                    $final_date = date_format($final_date_tmp, 'd-m-Y');
                }

                if ($audit->description == NULL)
                {
                    $description = 'No hay informaci&oacute;n';
                }
                else
                    $description = $audit->description;

                $obj_risks = array();
                $sub_risks = array();
                //obtenemos riesgos de negocio asociados a la auditoría
                $objective_risks = DB::table('audit_risk')
                                ->join('objective_risk','objective_risk.id','=','audit_risk.objective_risk_id')
                                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('audit_risk.audit_audit_plan_id','=',$audit->id)
                                ->select('risks.name as risk_name','objectives.name as objective_name')
                                ->get();

                //obtenemos riesgos de negocio asociados a la auditoría
                $subprocess_risks = DB::table('audit_risk')
                                ->join('risk_subprocess','risk_subprocess.id','=','audit_risk.risk_subprocess_id')
                                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                ->join('processes','processes.id','=','subprocesses.process_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('audit_risk.audit_audit_plan_id','=',$audit->id)
                                ->select('risks.name as risk_name','subprocesses.name as subprocess_name',
                                        'processes.name as process_name')
                                ->get();

                //obtenemos pruebas de auditoría realizadas en cada auditoría (si es que hay)
                $audit_tests1 = DB::table('audit_audit_plan_audit_test')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_test.audit_audit_plan_id')
                            ->join('audit_tests','audit_tests.id','=','audit_audit_plan_audit_test.audit_test_id')
                            ->where('audit_audit_plan_audit_test.audit_audit_plan_id','=',$audit->id)
                            ->select('audit_tests.name as audit_test_name','audit_audit_plan_audit_test.results as results')
                            ->get();

                if ($audit_tests1)
                {
                    $j = 0; //contador de pruebas en cero
                    foreach ($audit_tests1 as $test)
                    {
                        //seteamos resultados
                        if ($test->results == NULL)
                        {
                            $results = 'No hay resultados';
                        }
                        else 
                            $results = $test->results;

                        $audit_tests[$j] = ['audit_id' => $audit->id,
                                            'name' => $test->audit_test_name,
                                            'results' => $results,
                                        ];
                        $j += 1;
                    }
                }

                $j = 0; //contador de riesgos en cero
                //seteamos en variables los riesgos de negocio (si es que hay)
                foreach ($objective_risks as $objective_risk)
                {
                    $obj_risks[$j] = ['audit_id' => $audit->id,
                                      'name' => $objective_risk->risk_name,
                                      'objective_name' => $objective_risk->objective_name];
                    $j += 1;
                }

                $j = 0; //contador de riesgos en cero
                //seteamos en variables los riesgos de proceso
                foreach ($subprocess_risks as $subprocess_risk)
                {
                    $sub_risks[$j] = ['audit_id' => $audit->id,
                                      'name' => $subprocess_risk->risk_name,
                                      'subprocess_name' => $subprocess_risk->subprocess_name,
                                      'process_name' => $subprocess_risk->process_name];
                    $j += 1;
                }

                //guardamos datos de pruebas de auditoría de la auditoría seleccionada

                $auditorias[$i] = ['name' => $audit->name,
                                    'description' => $description,
                                    'initial_date' => $initial_date,
                                    'final_date' => $final_date,
                                    'resources' => $resources,
                                    'obj_risks' => $obj_risks,
                                    'sub_risks' => $sub_risks,
                                    'audit_tests' => $audit_tests,
                                ];
                $i += 1;
            }

            //ordenamos fechas
            $initial_date_tmp = new DateTime($audit_plan_info['initial_date']);
            $initial_date = date_format($initial_date_tmp, 'd-m-Y');
            $final_date_tmp = new DateTime($audit_plan_info['final_date']);
            $final_date = date_format($final_date_tmp, 'd-m-Y');

            //damos formato a status
            if ($audit_plan_info['status'] == 0)
                $status = 'Abierto';
            else
                $status = 'Cerrado';

            //guardamos datos finales que serán enviados
            $results = ['name' => $audit_plan_info['name'],
                        'description' => $audit_plan_info['description'],
                        'objectives' => $audit_plan_info['objectives'],
                        'scopes' => $audit_plan_info['scopes'],
                        'status' => $status,
                        'resources' => $audit_plan_info['resources'],
                        'methodology' => $audit_plan_info['methodology'],
                        'initial_date' => $initial_date,
                        'final_date' => $final_date,
                        'rules' => $audit_plan_info['rules'],
                        'users' => $users,
                        'responsable' => $responsable,
                        'obj_plan_risks' => $obj_plan_risks,
                        'sub_plan_risks' => $sub_plan_risks,
                        'audits' => $auditorias
                ];

            return json_encode($results);

        } //if ($audit_plan_id)
    }

    //obtiene issues asociadas a una plan auditoria + auditoria
    public function getIssue($id)
    {
        $results = array();
        //obtenemos solo primer issue (en caso de que existieran muchos)
        $issue = DB::table('issues')
                    ->where('issues.audit_audit_plan_audit_test_id','=',$id)
                    ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification')
                    ->first();

        if ($issue == NULL)
        {
            $results = NULL;
        }

        else
        {
            $results = [
                'id' => $issue->id,
                'name' => $issue->name,
                'description' => $issue->description,
                'recommendations' => $issue->recommendations,
                'classification' => $issue->classification,
            ];
        }
        
        return json_encode($results);
    }

    //obtiene notas asociadas a una prueba de auditoría
    public function getNotes($id)
    {
        $results = array();
        $i = 0;
        $notes = DB::table('notes')
                    ->where('notes.audit_audit_plan_audit_test_id','=',$id)
                    ->select('notes.id','notes.name','notes.description','notes.created_at','notes.status',
                             'notes.audit_audit_plan_audit_test_id as test_id')
                    ->get();

        if (empty($notes))
        {
            $results = NULL;
        }
        else
        {
            foreach ($notes as $note)
            {
                if ($note->status == 0)
                {
                    $estado = 'Abierta';
                }
                else
                {
                    $estado = 'Cerrada';
                }

                //obtenemos respuestas a la nota (si es que existen)
                $answers_notes = DB::table('notes_answers')
                            ->where('note_id',$note->id)
                            ->select('notes_answers.id','notes_answers.answer','notes_answers.created_at','notes_answers.updated_at')
                            ->get();

                if (empty($answers_notes))
                {
                    $answers = NULL;
                }
                else
                {
                    $j = 0; //contador de respuestas para las notas
                    //seteamos cada respuesta de la nota
                    foreach ($answers_notes as $ans)
                    {
                        //obtenemos evidencias de la respuesta (si es que existen)
                        $evidences = $this->getEvidences(1,$ans->id);

                        $answers[$j] = [
                                'id' => $ans->id,
                                'answer' => $ans->answer,
                                'created_at' => $ans->created_at,
                                'updated_at' => $ans->updated_at,
                                'ans_evidences' => $evidences,
                        ];

                        $j += 1;
                    }

                    
                }
                //obtenemos evidencias de la nota (si es que existe)
                $evidences = $this->getEvidences(0,$note->id);

                $fecha_creacion = date('d-m-Y',strtotime($note->created_at));
                $fecha_creacion .= ' a las '.date('H:i:s',strtotime($note->created_at));
                $results[$i] = [
                    'id' => $note->id,
                    'name' => $note->name,
                    'description' => $note->description,
                    'created_at' => $fecha_creacion,
                    'status' => $estado,
                    'status_origin' => $note->status,
                    'test_id' => $note->test_id,
                    'answers' => $answers,
                    'evidences' => $evidences
                    ];

                $i += 1;
            }
        }
        

        return json_encode($results);
    }
    //obtiene plan de acción para una debilidad dada
    public function getActionPlan($id)
    {
        $results = array();
        //obtenemos action plan
        $action_plan = DB::table('action_plans')
                    ->join('stakeholders','stakeholders.id','=','action_plans.stakeholder_id')
                    ->where('issue_id','=',$id)
                    ->select('action_plans.id','action_plans.description',
                            'action_plans.final_date','stakeholders.name as name','stakeholders.surnames as surnames')
                    ->get();

        if ($action_plan == NULL)
        {
            $results = NULL;
        }

        else
        {
            foreach ($action_plan as $ap) //aunque solo existirá uno
            {
                $results = [
                    'id' => $ap->id,
                    'description' => $ap->description,
                    'final_date' => $ap->final_date,
                    'stakeholder' => $ap->name.' '.$ap->surnames,
                ];
            }
        }
        
        return json_encode($results);
    }
/*
    public function getFile($archivo)
    {
        $public_path = public_path();
        $url = 'C:virtualhost\\erm\\storage\\app\\evidencias_notas\\'.$archivo;
        //verificamos si el archivo existe y lo retornamos
        return response()->download($url);
        //if (Storage::exists($archivo))
        //{
            //return response()->download($url);
        //    echo "hola";
        //}
        //si no se encuentra lanzamos un error 404.
        //abort(404);
    }
*/
    //función interna que obtiene los archivos subidos si es que hay
    public function getEvidences($kind,$id)
    {
        $public_path = public_path();
        if ($kind == 0) //se están solicitando evidencias de una nota
        {
            //seleccionamos carpeta de notas
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_notas';
        }
        else if ($kind == 1) //se están solicitando respuestas a evidencias de una nota
        {
            //seleccionamos carpeta de respuestas evidencias
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_resp_notas';
        }

        $archivos = scandir($carpeta);

        foreach ($archivos as $archivo)
        {
                        
                //dividimos archivos para buscar id
                if (strpos($archivo,'___'))
                {   
                    $j = 0;
                    $temp = explode('___',$archivo);

                    //sacamos extensión del archivo
                    $temp2 = explode('.',$temp[1]);

                    if ($temp2[0] == $id)
                    {
                        $evidences[$j] = [
                            'note_id' => $id,
                            'url' => $archivo,
                        ];

                        $j += 1;
                    }
                }
                else
                    $evidences = NULL;                      
        }

        return $evidences;
    }

    public function closeNote($id)
    {
        $res = 0;

        DB::table('notes')
            ->where('id','=',$id)
            ->update([
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                ]);

        $res = 1;

        return $res;
    }



}
