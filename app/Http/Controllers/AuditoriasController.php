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
use stdClass;

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
        //obtenemos lista de stakeholders (LOS OBTENDREMOS SEGÚN ORGANIZACIÓN)
        //$stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        //->orderBy('name')
        //->lists('full_name', 'id');

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

        return view('auditorias.create',[/*'stakeholders'=>$stakeholders,*/'organizations'=>$organizations,
                                        'audits'=>$audits,'risk_subprocess'=>$risk_subprocess]);
    }

    //función para abrir la vista de gestión de programas de auditoría
    public function auditPrograms()
    {
        $programas = array();
        $programs = \Ermtool\Audit_program::all();

        $programs = DB::table('audit_audit_plan_audit_program')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->select('audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description',
                            'audit_audit_plan_audit_program.created_at','audit_audit_plan_audit_program.updated_at',
                            'audit_audit_plan_audit_program.expiration_date')
                        ->get();

                    
        $i = 0; //contador de planes

        foreach ($programs as $program)
        {
            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($program->created_at == NULL OR $program->created_at == "0000-00-00" OR $program->created_at == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                //damos formato a fecha inicial
                $fecha_creacion = date('d-m-Y',strtotime($program->created_at));
                $fecha_creacion .= ' a las '.date('H:i:s',strtotime($program->created_at));
            }

            //damos formato a fecha de actualización 
            if ($program->updated_at != NULL)
            {
                //damos formato a fecha final
                $fecha_act = date('d-m-Y',strtotime($program->updated_at));
                $fecha_act .= ' a las '.date('H:i:s',strtotime($program->updated_at));
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            //formato a fecha expiración
            if ($program->expiration_date)
            {
                $fecha_exp = date('d-m-Y',strtotime($program->expiration_date));
            }
            else
                $fecha_exp = "Ninguna";

            $programas[$i] = [
                            'id' => $program->id,
                            'name' => $program->name,
                            'description' => $program->description,
                            'created_at' => $fecha_creacion,
                            'updated_at' => $fecha_act,
                            'expiration_date' => $fecha_exp
                        ];
            $i += 1;
        }

        return view('auditorias.programas',['programs' => $programas]);         
    }

    //función crea PROGRAMAS de auditoría
    public function createPruebas()
    {
        //plan de auditoría
        $audit_plans = \Ermtool\Audit_plan::lists('name','id');

        $audit_programs = \Ermtool\Audit_program::lists('name','id');

         //obtenemos lista de stakeholders
        $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        //echo $audit_tests;

        return view('auditorias.create_test',['audit_plans'=>$audit_plans,'audit_programs'=>$audit_programs,
                                                'stakeholders' => $stakeholders]);     
    }

    public function createAuditoria()
    {
        return view('auditorias.create_auditoria');
    }

    //función que guarda programa de auditoría
    public function storePrueba(Request $request)
    {
        global $req;
        $req = $request;
        //creamos una transacción para cumplir con atomicidad
        DB::transaction(function()
        {
            $fecha = date('Y-m-d H:i:s');
            //si es que no tiene valor kind, significa que es un programa nuevo
            if ($_POST['kind'] == "")
            {
                $audit_program = \Ermtool\Audit_program::create([
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                            ]);

                $audit_program_id = $audit_program->id;
            }
            else
            {
                $audit_program_id = \Ermtool\Audit_program::find($_POST['audit_program_id'])->value('id');            
            }

                //insertamos en audit_audit_plan_audit_program
                $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')
                            ->insertGetId([
                                    'audit_program_id' => $audit_program_id,
                                    'audit_audit_plan_id' => $_POST['audit'],
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    //'stakeholder_id' => $_POST['stakeholder_id']
                                ]);

                //agregamos evidencia (si es que existe)
                if ($GLOBALS['req']->file('file_program') != NULL)
                {
                    upload_file($GLOBALS['req']->file('file_program'),'programas_auditoria',$audit_audit_plan_audit_program);    
                }

                $i = 1; //contador de pruebas
                //insertamos cada una de las pruebas
                while (isset($_POST['name_test_'.$i]) && $_POST['name_test_'.$i] != "")
                {
                    //si es que se ingreso descripción
                    if (isset($_POST['description_test_'.$i]))
                    {
                        $description = $_POST['description_test_'.$i];
                    }
                    else
                    {
                        $description = NULL;
                    }

                    //si es que se ingreso tipo
                    if (isset($_POST['type_test_'.$i]))
                    {
                        $type = $_POST['type_test_'.$i];
                    }
                    else
                    {
                        $type = NULL;
                    }

                    //si es que se ingreso stakeholder
                    if (isset($_POST['stakeholder_test_'.$i]))
                    {
                        $stakeholder = $_POST['stakeholder_test_'.$i];
                    }
                    else
                    {
                        $stakeholder = NULL;
                    }

                    //si es que se ingreso HH
                    if (isset($_POST['hh_test_'.$i]))
                    {
                        $hh = $_POST['hh_test_'.$i];
                    }
                    else
                    {
                        $hh = NULL;
                    }

                    //vemos si el tipo de prueba es de control, de proceso o de riesgo
                    if ($_POST['type2_test_'.$i] == 1) //es de control
                    {
                        if (isset($_POST['control_id_test_'.$i]))
                        {
                            $test_id = DB::table('audit_tests')
                            ->insertGetId([
                                    'audit_audit_plan_audit_program_id' => $audit_audit_plan_audit_program,
                                    'name' => $_POST['name_test_'.$i],
                                    'description' => $description, 
                                    'type' => $type,
                                    'status' => 0,
                                    'results' => 2,
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    'hh' => $hh,
                                    'stakeholder_id' => $stakeholder,
                                    'control_id' => $_POST['control_id_test_'.$i],
                                    'subprocess_id' => NULL,
                                    'risk_id' => NULL,
                            ]);
                        }
                    }

                    else if ($_POST['type2_test_'.$i] == 2) //es de riesgo
                    {
                        if (isset($_POST['risk_id_test_'.$i]))
                        {
                            $test_id = DB::table('audit_tests')
                            ->insertGetId([
                                    'audit_audit_plan_audit_program_id' => $audit_audit_plan_audit_program,
                                    'name' => $_POST['name_test_'.$i],
                                    'description' => $description, 
                                    'type' => $type,
                                    'status' => 0,
                                    'results' => 2,
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    'hh' => $hh,
                                    'stakeholder_id' => $stakeholder,
                                    'risk_id' => $_POST['risk_id_test_'.$i],
                                    'subprocess_id' => NULL,
                                    'control_id' => NULL,
                            ]);
                        }
                    }

                    else if ($_POST['type2_test_'.$i] == 3) //es de proceso
                    {
                        if (isset($_POST['subprocess_id_test_'.$i]))
                        {
                            $test_id = DB::table('audit_tests')
                            ->insertGetId([
                                    'audit_audit_plan_audit_program_id' => $audit_audit_plan_audit_program,
                                    'name' => $_POST['name_test_'.$i],
                                    'description' => $description, 
                                    'type' => $type,
                                    'status' => 0,
                                    'results' => 2,
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    'hh' => $hh,
                                    'stakeholder_id' => $stakeholder,
                                    'subprocess_id' => $_POST['subprocess_id_test_'.$i],
                                    'risk_id' => NULL,
                                    'control_id' => NULL,
                            ]);
                        }
                    }

                    //guardamos evidencia (si es que hay)
                    if ($GLOBALS['req']->file('file_'.$i) != NULL)
                    {
                        upload_file($_FILES['file_'.$i],'pruebas_auditoria',$test_id);    
                    }
                    
                    $i += 1;    
                }

            Session::flash('message','Programa de auditor&iacute;a creado correctamente');
        });
        return Redirect::to('/programas_auditoria');
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

        //haremos global de request
        global $req;
        $req = $request;
        //print_r($_POST);
        DB::transaction(function() {
            //primero que todo, actualizamos las pruebas
            //para esto, separamos primer string del array id_pruebas por sus comas
            $id_pruebas = explode(',',$_POST['id_pruebas'][0]);

            foreach ($id_pruebas as $id)
            {
                //actualizamos resultados e issue(si es que el estado de la prueba es cerrado (igual a 2))
                if ($_POST['status_'.$id] == 2)
                {
                    //actualizamos resultado de prueba de identificador $id (status y results)
                    DB::table('audit_tests')
                        ->where('id','=',$id)
                        ->update([ 
                            'status' => $_POST['status_'.$id],
                            'results' => $_POST['test_result_'.$id],
                            'updated_at' => date('Y-m-d H:i:s')
                            ]);

                    //obtenemos id de control para la evaluación de riesgo controlado
                    $control = DB::table('audit_tests')
                                ->where('id','=',$id)
                                ->select('control_id')
                                ->first();

                    //vemos si es inefectiva, por lo que debería tener un issue
                    if ($_POST['test_result_'.$id] == 0) //es inefectiva
                    {
                        $i = 1; //contador de issues
                        while (isset($_POST['new_issue_name_'.$id.'_'.$i]))
                        {
                            $issue = \Ermtool\Issue::create([
                                'name' => $_POST['new_issue_name_'.$id.'_'.$i],
                                'description' => $_POST['new_issue_description_'.$id.'_'.$i],
                                'recommendations' => $_POST['new_issue_recommendations_'.$id.'_'.$i],
                                'classification' => $_POST['new_issue_classification_'.$id.'_'.$i],
                                'audit_test_id' => $id
                                ]);

                            //vemos si se ingreso una nueva evidencia
                            if (isset($_POST['new_issue_evidence_'.$id.'_'.$i]) && $_POST['new_issue_evidence_'.$id.'_'.$i] != "")
                            {
                                upload_file($GLOBALS['req']->file('new_issue_evidence_'.$id.'_'.$i),'evidencias_hallazgos',$issue->id);
                            }

                            $i += 1; //para ver si es que hay más issues nuevos para esta prueba
                        }

                        if ($control->control_id != NULL)
                        {
                            //guardamos riesgo controlado: RECORDAR: Hay una diferencia (error al crear tablas) entre las evaluaciones de control directo y auditorias, al evaluar controles es 1=inefectiva; 2=efectiva. Al ejecutar auditoría 0=inefectiva, 1=efectiva. Por lo que para no modificar mucho la base de datos, sumaremos uno en esta sección a los resultados de la prueba para igualarlos con la evaluación de control directo, para enviar los datos a la función helpers->calc_controlled_risk()

                            $res = $_POST['test_result_'.$id] + 1;

                            $result = calc_controlled_risk($control->control_id,$res);
                        }
                        
                    }

                    $res = $_POST['test_result_'.$id] + 1;
                    //Verificamos que res no sea igual a 3 (prueba en proceso)
                    if ($control->control_id != NULL && $res == 2)
                    {
                        $result = calc_controlled_risk($control->control_id,$res);
                    }

                    if ($result == 0)
                    {
                        echo "Riesgo controlado actualizado correctamente";
                    }
                    else if ($result == 1)
                    {
                        echo "Error al actualizar valor de riesgo controlado";
                    }

                    
                    
                }

                else
                {
                    //sólo actualizamos resultado de prueba
                    DB::table('audit_tests')
                        ->where('id','=',$id)
                        ->update([ 
                            'status' => $_POST['status_'.$id],
                            'updated_at' => date('Y-m-d H:i:s')
                            ]);
                }
            

                //ahora actualizamos issues existentes (si es que hay)

                //primero que todo, actualizamos issues ya existentes (son enviados como array de $id_issues)
                //separamos distintos id de issues existentes (si es que hay)

                if (isset($_POST[$id.'_issues']))
                {
                    $issues_id = explode(',',$_POST[$id.'_issues'][0]);

                    //actualizamos issues de id = issue
                    foreach ($issues_id as $issue)
                    {

                                DB::table('issues')
                                    ->where('id','=',$issue)
                                    ->update([
                                        'name' => $_POST['issue_name_'.$issue],
                                        'description' => $_POST['issue_description_'.$issue],
                                        'recommendations' => $_POST['issue_recommendations_'.$issue],
                                        'classification' => $_POST['issue_classification_'.$issue],
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);

                                //vemos si se ingreso evidencia
                                if ($GLOBALS['req']->file('issue_evidence_'.$issue) != NULL)
                                {
                                    
                                    $res = upload_file($GLOBALS['req']->file('issue_evidence_'.$issue),'evidencias_hallazgos',$issue);
                                    if ($res == 1)
                                    {
                                        Session::flash('error','No se pudo guardar correctamente el archivo. Intentelo nuevamente');
                                    }

                                }
                    }
                }
            
                    /*
                    //ahora agregamos nuevos issues si es que hay           
                    while(isset($_POST['new_issue_classification'.$cont.'_'.$id]))
                    {
                        DB::table('issues')
                            ->where('id','=',$id)
                            ->insert([
                                    'name' => $_POST['new_issue_name'.$cont.'_'.$id],
                                    'description' => $_POST['new_issue_description'.$cont.'_'.$id],
                                    'recommendations' => $_POST['new_issue_recommendations'.$cont.'_'.$id],
                                    'classification' => $_POST['new_issue_classification'.$cont.'_'.$id],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'audit_audit_plan_audit_test_id' => $id
                                ]);

                        $cont += 1;
                    }
                    */
            }
            Session::flash('message','Auditor&iacute;a ejecutada correctamente');

        });
        
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

        global $evidence;
        $evidence = $request->file('evidencia_'.$request['test_id']);
        //primero vemos si se está agregando una nota o una evidencia
        DB::transaction(function() {
            $res = DB::table('notes')
                    ->insertGetId([
                        'name' => $_POST['name_'.$_POST['test_id']],
                        'description' => $_POST['description_'.$_POST['test_id']],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'audit_test_id' => $_POST['test_id'],
                        'status' => 0
                        ]);
        
            //guardamos archivo de evidencia (si es que hay)
            if($GLOBALS['evidence'] != NULL)
            {
                //separamos nombre archivo extension
                $file = explode('.',$GLOBALS['evidence']->getClientOriginalName());

                Storage::put(
                    'evidencias_notas/'. $file[0] . "___" . $res . "." . $file[1],
                    file_get_contents($GLOBALS['evidence']->getRealPath())
                );
            }

            if ($res)
            {
                Session::flash('message','Nota agregada correctamente');
            }

            else
            {
                Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
            }
        });
           
        return Redirect::to('/supervisar');    

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
        
        //Mantenemos atomicidad y consistencia
        DB::transaction(function()
        {
            //verificamos datos que no hayan sido ingresados
            if ($_POST['objectives'] == "")
            {
                $objectives = NULL;
            }
            else
            {
                $objectives = $_POST['objectives'];
            }

            if ($_POST['scopes'] == "")
            {
                $scopes = NULL;
            }
            else
            {
                $scopes = $_POST['scopes'];
            }

            if ($_POST['resources'] == "")
            {
                $resources = NULL;
            }
            else
            {
                $resources = $_POST['resources'];
            }

            if ($_POST['methodology'] == "")
            {
                $methodology = NULL;
            }
            else
            {
                $methodology = $_POST['methodology'];
            }

            if ($_POST['rules'] == "")
            {
                $rules = NULL;
            }
            else
            {
                $rules = $_POST['rules'];
            }

            if ($_POST['HH_plan'] == "")
            {
                $estimated_HH = NULL;
            }
            else
            {
                $estimated_HH = $_POST['HH_plan'];
            }
            //insertamos plan y obtenemos ID
            $audit_plan_id = DB::table('audit_plans')->insertGetId([
                    'name'=>$_POST['name'],
                    'description'=>$_POST['description'],
                    'objectives'=>$objectives,
                    'scopes'=>$scopes,
                    'status'=>0,
                    'resources'=>$resources,
                    'methodology'=>$methodology,
                    'initial_date'=>$_POST['initial_date'],
                    'final_date'=>$_POST['final_date'],
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'rules'=>$rules,
                    'hh'=>$estimated_HH,
                    'organization_id'=>$_POST['organization_id']
                    ]);

            //insertamos en audit_plan_stakeholder primero al encargado del plan y luego al equipo
                    DB::table('audit_plan_stakeholder')
                        ->insert([
                            'role' => 0,
                            'audit_plan_id' => $audit_plan_id,
                            'stakeholder_id' => $_POST['stakeholder_id']
                            ]);

            //ahora insertamos equipo de stakeholders (si es que hay)
            if (isset($_POST['stakeholder_team']))
            {
                foreach ($_POST['stakeholder_team'] as $stakes)
                {
                    DB::table('audit_plan_stakeholder')
                            ->insert([
                                'role' => 1,
                                'audit_plan_id' => $audit_plan_id,
                                'stakeholder_id' => $stakes
                                ]);
                }
            }
            
            if ($_POST['type'] == 0) //se agrego auditoría de procesos
            {
                //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                if (isset($_POST['processes_id']))
                {
                    foreach ($_POST['processes_id'] as $process_id)
                    {
                        //obtenemos subprocesses_risks
                        $risks = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('subprocesses.process_id','=',$process_id)
                                        ->select('risk_subprocess.id')
                                        ->get();

                        foreach ($risks as $risk)
                        {
                            //insertamos cada riesgo de subproceso
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $audit_plan_id,
                                    'risk_subprocess_id' => $risk->id
                                    ]);
                        }
                    }
                }
            }

            else if ($_POST['type'] == 1) //se agregó auditoría de negocios
            {
                //obtenemos riesgo asociado a cada objetivo (si es que hay)
                if (isset($_POST['objectives_id']))
                {
                    foreach ($_POST['objectives_id'] as $objective_id)
                    {
                        //obtenemos objective_risks
                        $risks = DB::table('objectives')
                                ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                ->select('objective_risk.id')
                                ->get();

                        foreach ($risks as $risk)
                        {
                            //insertamos cada riesgo de subproceso
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $audit_plan_id,
                                    'objective_risk_id' => $risk->id
                                    ]);
                        }
                    }
                }
            }

            else if ($_POST['type'] == 2)
            {
                //insertamos riesgos del negocio (si es que existen)
                if (isset($_POST['objective_risk_id']))
                {
                    foreach ($_POST['objective_risk_id'] as $objective_risk)
                    {
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $audit_plan_id,
                                    'objective_risk_id' => $objective_risk
                                    ]);
                    }
                }
                //insertamos riesgos de proceso (si es que existen)
                if (isset($_POST['risk_subprocess_id']))
                {
                    foreach ($_POST['risk_subprocess_id'] as $risk_subprocess)
                    {
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $audit_plan_id,
                                    'risk_subprocess_id' => $risk_subprocess
                                    ]);
                    }
                }
            }

            //ahora guardamos auditorías que no son nuevas (si es que hay)

            //insertamos cada auditoria (de universo de auditorias) en audit_audit_plan
            $i = 1;
            if (isset($_POST['audits']))
            {
                foreach ($_POST['audits'] as $audit)
                {
                    if ($_POST['audit_'.$audit.'_resources'] == "")
                    {
                        $resources = NULL;
                    }
                    else
                    {
                        $resources = $_POST['audit_'.$audit.'_resources'];
                    }

                    if ($_POST['audit_'.$audit.'_HH'] == "")
                    {
                        $estimated_HH = NULL;
                    }
                    else
                    {
                        $estimated_HH = $_POST['audit_'.$audit.'_HH'];
                    }
                    //insertamos y obtenemos id para ingresarlo en audit_risk y otros
                    $audit_audit_plan_id = DB::table('audit_audit_plan')
                                ->insertGetId([
                                    'audit_plan_id' => $audit_plan_id,
                                    'audit_id' => $audit,
                                    'initial_date' => $_POST['audit_'.$audit.'_initial_date'],
                                    'final_date' => $_POST['audit_'.$audit.'_final_date'],
                                    'resources' => $resources,
                                    'hh' => $estimated_HH
                                    ]);
                    
                    if ($_POST['type'] == 0) //se agrego auditoría de procesos
                    {
                        //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                        if (isset($_POST['audit_'.$audit.'_processes']))
                        {
                            foreach ($_POST['audit_'.$audit.'_processes'] as $process_id)
                            {
                                //obtenemos subprocesses_risks
                                $risks = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('subprocesses.process_id','=',$process_id)
                                        ->select('risk_subprocess.id')
                                        ->get();

                                foreach ($risks as $risk)
                                {
                                    //insertamos cada riesgo de subproceso
                                    DB::table('audit_risk')
                                        ->insert([
                                            'audit_audit_plan_id' => $audit_audit_plan_id,
                                            'risk_subprocess_id' => $risk->id
                                            ]);
                                }
                            }
                        }
                    }

                    else if ($_POST['type'] == 1) //se agregó auditoría de negocios
                    {
                        //obtenemos riesgo asociado a cada objetivo (si es que hay)
                        if (isset($_POST['audit_'.$audit.'_objectives']))
                        {
                            foreach ($_POST['audit_'.$audit.'_objectives'] as $objective_id)
                            {
                                //obtenemos objective_risks
                                $risks = DB::table('objectives')
                                        ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                        ->select('objective_risk.id')
                                        ->get();

                                foreach ($risks as $risk)
                                {
                                    //insertamos cada riesgo de subproceso
                                    DB::table('audit_risk')
                                        ->insert([
                                            'audit_audit_plan_id' => $audit_audit_plan_id,
                                            'objective_risk_id' => $risk->id
                                            ]);
                                }
                            }
                        }
                    }

                    else if ($_POST['type'] == 2)
                    {
                        //insertamos riesgos de negocio de la auditoría
                        if (isset($_POST['audit_'.$audit.'_objective_risks']))
                        {
                            foreach ($_POST['audit_'.$audit.'_objective_risks'] as $audit_objective_risk)
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
                        if (isset($_POST['audit_'.$audit.'_risk_subprocesses']))
                        {
                            foreach ($_POST['audit_'.$audit.'_risk_subprocesses'] as $audit_risk_subprocess)
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
                }
            } //fin isset($_POST['audits'])

            //ahora guardamos auditorías nuevas (si es que hay)
            $i = 1; //contador para auditorías nuevas
            if(isset($_POST['audit_new'.$i.'_name']))
            {   
                while (isset($_POST['audit_new'.$i.'_name']))
                {
                    //primero insertamos en tabla audits y obtenemos id
                    $audit_id = DB::table('audits')
                                ->insertGetId([
                                    'name' => $_POST['audit_new'.$i.'_name'],
                                    'description' => $_POST['audit_new'.$i.'_description'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                    //no están obligatorias las HH
                    if ($_POST['audit_new'.$i.'_HH'] != "")
                    {
                        $HH = $_POST['audit_new'.$i.'_HH'];
                    }
                    else
                        $HH = NULL;
                    //ahora insertamos en audit_audit_plan
                    $audit_audit_plan_id = DB::table('audit_audit_plan')
                                ->insertGetId([
                                    'audit_plan_id' => $audit_plan_id,
                                    'audit_id' => $audit_id,
                                    'initial_date' => $_POST['audit_new'.$i.'_initial_date'],
                                    'final_date' => $_POST['audit_new'.$i.'_final_date'],
                                    'resources' => $_POST['audit_new'.$i.'_resources'],
                                    'hh' => $HH
                                    ]);

                    if ($_POST['type'] == 0) //se agrego auditoría de procesos
                    {
                        //primero, obtenemos riesgos asociados a cada proceso (si es que hay)
                        if (isset($_POST['audit_new'.$i.'_processes']))
                        {
                            foreach ($_POST['audit_new'.$i.'_processes'] as $process_id)
                            {
                                //obtenemos subprocesses_risks
                                $risks = DB::table('subprocesses')
                                        ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                                        ->where('subprocesses.process_id','=',$process_id)
                                        ->select('risk_subprocess.id')
                                        ->get();

                                foreach ($risks as $risk)
                                {
                                    //insertamos cada riesgo de subproceso
                                    DB::table('audit_risk')
                                        ->insert([
                                            'audit_audit_plan_id' => $audit_audit_plan_id,
                                            'risk_subprocess_id' => $risk->id
                                            ]);
                                }
                            }
                        }
                    }

                    else if ($_POST['type'] == 1) //se agregó auditoría de negocios
                    {
                        //obtenemos riesgo asociado a cada objetivo (si es que hay)
                        if (isset($_POST['audit_new'.$i.'_objectives']))
                        {
                            foreach ($_POST['audit_new'.$i.'_objectives'] as $objective_id)
                            {
                                //obtenemos objective_risks
                                $risks = DB::table('objectives')
                                        ->join('objective_risk','objective_risk.objective_id','=',$objective_id)
                                        ->select('objective_risk.id')
                                        ->get();

                                foreach ($risks as $risk)
                                {
                                    //insertamos cada riesgo de subproceso
                                    DB::table('audit_audit_plan_risk')
                                        ->insert([
                                            'audit_audit_plan_id' => $audit_audit_plan_id,
                                            'objective_risk_id' => $risk->id
                                            ]);
                                }
                            }
                        }
                    }

                    else if ($_POST['type'] == 2)
                    {
                         //insertamos riesgos de negocio (de haber)
                           if (isset($_POST['audit_new'.$i.'_objective_risks']))
                           {
                                foreach ($_POST['audit_new'.$i.'_objective_risks'] as $objective_risk)
                                {
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'objective_risk_id' => $objective_risk
                                            ]);
                                }
                           }

                           //insertamos nuevo riesgo de proceso (de haber)
                           if (isset($_POST['audit_new'.$i.'_risk_subprocesses']))
                           {
                                foreach ($_POST['audit_new'.$i.'_risk_subprocesses'] as $risk_subprocess)
                                {
                                    DB::table('audit_risk')
                                        ->insert([
                                                'audit_audit_plan_id' => $audit_audit_plan_id,
                                                'risk_subprocess_id' => $risk_subprocess
                                            ]);
                                }
                           }
                    }           

                   $i += 1;
                }
            } //fin isset($_POST['audit_new'.$i.'_name']))

            Session::flash('message','Plan de auditor&iacute;a generado correctamente');
        });

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
                            'id' => $plan['id'],
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

    public function showProgram($id)
    {
        $tests = array();
        //obtenemos datos de programa (sólo el primero ya que id es único)
        $audit_program = DB::table('audit_audit_plan_audit_program')
                            ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                            ->where('audit_audit_plan_audit_program.id','=',$id)
                            ->select('audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description',
                                'audit_audit_plan_audit_program.created_at','audit_audit_plan_audit_program.expiration_date')
                            ->first();

        //damos formato a fecha creación
        $created_at = date('d-m-Y',strtotime($audit_program->created_at));
        $created_at.= ' a las '.date('H:i:s',strtotime($audit_program->created_at));

        //damos formato a fecha de expiración
        if ($audit_program->expiration_date == NULL)
        {
            $expiration_date = "No se ha especificado";
        }
        else
        {
            $expiration_date = date('d-m-Y',strtotime($audit_program->expiration_date));
        }
        


        //obtenemos pruebas de auditoría del programa
        $audit_tests = DB::table('audit_tests')
                        ->where('audit_audit_plan_audit_program_id','=',$id)
                        ->select('id','name','description','type','status','results','created_at',
                                 'updated_at','hh','stakeholder_id')
                        ->get();

        $i = 0;
        foreach ($audit_tests as $audit_test)
        {
            //obtenemos stakeholder
            if ($audit_test->stakeholder_id == NULL)
            {
                $stakeholder = "No asignado";
                $stakeholder2 = "No asignado";
            }
            else
            {
                $stakeholder = \Ermtool\Stakeholder::find($audit_test->stakeholder_id);
                $stakeholder2 = $stakeholder['name'].' '.$stakeholder['surnames'];
            }

            //damos formato a fecha creación
            $fecha_creacion = date('d-m-Y',strtotime($audit_test->created_at));
            $fecha_creacion .= ' a las '.date('H:i:s',strtotime($audit_test->created_at));

            //damos formato a fecha de actualización
            $fecha_act = date('d-m-Y',strtotime($audit_test->updated_at));
            $fecha_act .= ' a las '.date('H:i:s',strtotime($audit_test->updated_at));

            //seteamos tipo de prueba
            switch ($audit_test->type) {
                case 0:
                    $type = "Prueba de diseño";
                    break;
                case 1:
                    $type = "Prueba de efectividad operativa";
                    break;
                case 2:
                    $type = "Prueba de cumplimiento";
                    break;
                case 3:
                    $type = "Prueba sustantiva";
                    break;
                case NULL:
                    $type = "No especificado";
                    break;
            }
            //seteamos status
            switch ($audit_test->status) {
                case 0:
                    $status = "Abierta";
                    break;
                case 1:
                    $status = "En ejecución";
                    break;
                case 2:
                    $status = "Cerrada";
                    break;
                case NULL:
                    $type = "No especificado";
                    break;
            }

            //seteamos resultados
            switch ($audit_test->results) {
                case 0:
                    $results = "Inefectiva";
                    break;
                case 1:
                    $results = "Efectiva";
                    break;
                case 2:
                    $results = "En proceso";
                    break;
                case NULL:
                    $results = "No especificado";
                    break;
            }
            //seteamos descripción
            if ($audit_test->description == NULL)
            {
                $description = "Sin descripción";
            }
            else
            {
                $description = $audit_test->description;
            }

            //seteamos hh
            if ($audit_test->hh == NULL)
            {
                $hh = "Sin horas/hombre definidas";
            }
            else
            {
                $hh = $audit_test->hh;
            }

            $evidence = getEvidences(5,$audit_test->id);

            $tests[$i] = [
                'id' => $audit_test->id,
                'name' => $audit_test->name,
                'description' => $description,
                'type' => $type,
                'status' => $status,
                'results' => $results,
                'created_at' => $fecha_creacion,
                'updated_at' => $fecha_act,
                'stakeholder' => $stakeholder2,
                'hh' => $hh,
                'evidence' => $evidence,
            ];

            $i += 1;
        }

        $evidence = getEvidences(4,$audit_program->id);

        $programa = [
            'id' => $audit_program->id,
            'name' => $audit_program->name,
            'description' => $audit_program->description,
            'created_at' => $created_at,
            'expiration_date' => $expiration_date,
            'tests' => $tests,
            'evidence' => $evidence,
        ];

        return view('auditorias.show_program',['program'=>$programa]);
        //print_r($programa);
        
    }

    public function editProgram($id)
    {
        $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')->find($id);

        $audit_program = \Ermtool\Audit_program::find($audit_audit_plan_audit_program->audit_program_id);

        //obtenemos evidencias de la nota (si es que existe)
        $evidence = getEvidences(4,$audit_audit_plan_audit_program->id);

        return view('auditorias.edit_program',['program'=>$audit_program,
            'audit_audit_plan_audit_program'=>$audit_audit_plan_audit_program,
            'evidence'=>$evidence]);

    }

    public function editTest($program_id)
    {
        $audit_test = \Ermtool\Audit_test::find($program_id);

        //obtenemos audit_plan para despues obtener controles, riesgos o subproceso de la prueba
        $audit_plan = DB::table('audit_audit_plan_audit_program')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                    ->where('audit_tests.audit_audit_plan_audit_program_id','=',$audit_test->audit_audit_plan_audit_program_id)
                    ->select('audit_plans.id')
                    ->first();

        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        //seleccionamos tipo o categoría
        if ($audit_test->control_id != NULL)
        {
            $type2 = 1;
            $type_id = $audit_test->control_id;
        }
        else if ($audit_test->risk_id != NULL)
        {
            $type2 = 2;
            $type_id = $audit_test->risk_id;
        }
        else if ($audit_test->subprocess_id != NULL)
        {
            $type2 = 3;
            $type_id = $audit_test->subprocess_id;
        }
        else
        {
            $type2 = NULL;
            $type_id = NULL;
        }

        //obtenemos evidencias de prueba (si es que existen)
        $evidence = getEvidences(5,$audit_test->id);

        return view('auditorias.edit_test',['audit_test'=>$audit_test,'stakeholders'=>$stakeholders,'type2'=>$type2,'audit_plan' => $audit_plan->id,'type_id'=>$type_id,'evidence'=>$evidence]);

    }

    public function updateProgram(Request $request, $id)
    {
        global $id1;
        $id1 = $id;

        global $req;
        $req = $request;
        //echo "POST: ";
        //print_r($_POST);
        DB::transaction(function (){

            $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')->find($GLOBALS['id1']);

            $audit_program = \Ermtool\Audit_program::find($audit_audit_plan_audit_program->audit_program_id);

            $audit_program->name = $_POST['name'];

            $audit_program->description = $_POST['description'];

            DB::table('audit_audit_plan_audit_program')
                ->where('id',$audit_audit_plan_audit_program->id)
                ->update([
                        'expiration_date' => $_POST['expiration_date'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

            //agregamos evidencia (si es que existe)
            if ($GLOBALS['req']->file('file_program') != NULL)
            {
                upload_file($GLOBALS['req']->file('file_program'),'programas_auditoria',$audit_audit_plan_audit_program->id); 
            }
        
            $audit_program->save();
            //$audit_audit_plan_audit_program->save();

            Session::flash('message','Programa actualizado correctamente');

        });
        

        return Redirect::to('/programas_auditoria');

    }

    public function updateTest(Request $request, $id)
    {

        //obtenemos audit_tests
        global $audit_test;
        $audit_test = \Ermtool\Audit_test::find($id);
        //echo "POST: ";
        //print_r($_POST);
        global $req;
        $req = $request;
        DB::transaction(function (){

            $GLOBALS['audit_test']->name = $_POST['name'];

            //si es que se ingreso descripción
            if (isset($_POST['description']))
            {
                $GLOBALS['audit_test']->description = $_POST['description'];
            }
            else
            {
                $GLOBALS['audit_test']->description = NULL;
            }

            //si es que se ingreso tipo
            if (isset($_POST['type']))
            {
                $GLOBALS['audit_test']->type = $_POST['type'];
            }
            else
            {
                $GLOBALS['audit_test']->type = NULL;
            }

            //si es que se ingreso stakeholder
            if (isset($_POST['stakeholder_id']))
            {
                $GLOBALS['audit_test']->stakeholder_id = $_POST['stakeholder_id'];
            }
            else
            {
                $GLOBALS['audit_test']->stakeholder_id = NULL;
            }

            //si es que se ingreso HH
            if (isset($_POST['hh']))
            {
                $GLOBALS['audit_test']->hh = $_POST['hh'];
            }
            else
            {
                $GLOBALS['audit_test']->hh = NULL;
            }

            //vemos si el tipo de prueba es de control, de proceso o de riesgo
            if ($_POST['type2'] == 1) //es de control
            {
                if (isset($_POST['control_id_test_1']))
                {
                    $GLOBALS['audit_test']->control_id = $_POST['control_id_test_1'];
                }
            }

            else if ($_POST['type2'] == 2) //es de riesgo
            {
                if (isset($_POST['risk_id_test_1']))
                {
                    $GLOBALS['audit_test']->risk_id = $_POST['risk_id_test_1'];
                }
            }

            else if ($_POST['type2'] == 3) //es de proceso
            {
                if (isset($_POST['subprocess_id_test_1']))
                {
                    $GLOBALS['audit_test']->subprocess_id = $_POST['subprocess_id_test_1'];
                }
            }

            if ($GLOBALS['req']->file('file_1') != NULL)
            {
                upload_file($GLOBALS['req']->file('file_1'),'pruebas_auditoria',$GLOBALS['audit_test']->id);     
            }

            $GLOBALS['audit_test']->save();

            Session::flash('message','Prueba actualizada correctamente');

        });
        

        return Redirect::to('programas_auditoria.show.'.$GLOBALS['audit_test']->audit_audit_plan_audit_program_id);

    }

    public function createTest($id_program)
    {
        //obtenemos audit_plan para despues obtener controles, riesgos o subproceso de la prueba
        $audit_plan = DB::table('audit_audit_plan_audit_program')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->where('audit_audit_plan_audit_program.id','=',$id_program)
                    ->select('audit_plans.id')
                    ->first();

        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        $type2 = NULL;
        $type_id = NULL;
        return view('auditorias.create_test2',['audit_program'=>$id_program,'stakeholders'=>$stakeholders,'audit_plan' => $audit_plan->id,'type2'=>$type2,'type_id'=>$type_id]);
    }

    public function storeTest(Request $request)
    {
        //print_r($_POST);
        global $req;
        $req = $request;
        DB::transaction(function () {

            $fecha = date('Y-m-d H:i:s');

            if (isset($_POST['description']))
            {
                $description = $_POST['description'];
            }
             else
            {
                $description = NULL;
            }

            //si es que se ingreso tipo
            if (isset($_POST['type']))
            {
                $type = $_POST['type'];
            }
            else
            {
                $type = NULL;
            }

            //si es que se ingreso stakeholder
            if (isset($_POST['stakeholder_id']))
            {
                $stakeholder = $_POST['stakeholder_id'];
            }
            else
            {
                $stakeholder = NULL;
            }

            //si es que se ingreso HH
            if (isset($_POST['hh']))
            {
                $hh = $_POST['hh'];
            }
            else
            {
                $hh = NULL;
            }

            //vemos si el tipo de prueba es de control, de proceso o de riesgo
            if ($_POST['type2'] == 1) //es de control
            {
                if (isset($_POST['control_id_test_1']))
                {
                    $test_id = DB::table('audit_tests')
                            ->insertGetId([
                            'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                            'name' => $_POST['name'],
                            'description' => $description, 
                            'type' => $type,
                            'status' => 1,
                            'results' => 2,
                            'created_at' => $fecha,
                            'updated_at' => $fecha,
                            'hh' => $hh,
                            'stakeholder_id' => $stakeholder,
                            'control_id' => $_POST['control_id_test_1']
                    ]);
                }
            }

            else if ($_POST['type2'] == 2) //es de riesgo
            {
                if (isset($_POST['risk_id_test_1']))
                {
                            $test_id = DB::table('audit_tests')
                            ->insertGetId([
                                    'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                    'name' => $_POST['name'],
                                    'description' => $description, 
                                    'type' => $type,
                                    'status' => 0,
                                    'results' => 2,
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    'hh' => $hh,
                                    'stakeholder_id' => $stakeholder,
                                    'risk_id' => $_POST['risk_id_test_1'],
                            ]);
                }
            }

            else if ($_POST['type2'] == 3) //es de proceso
            {
                if (isset($_POST['subprocess_id_test_1']))
                {
                            $test_id = DB::table('audit_tests')
                            ->insertGetId([
                                    'audit_audit_plan_audit_program_id' => $_POST['audit_audit_plan_audit_program_id'],
                                    'name' => $_POST['name'],
                                    'description' => $description, 
                                    'type' => $type,
                                    'status' => 0,
                                    'results' => 2,
                                    'created_at' => $fecha,
                                    'updated_at' => $fecha,
                                    'hh' => $hh,
                                    'stakeholder_id' => $stakeholder,
                                    'subprocess_id' => $_POST['subprocess_id_test_1'],
                            ]);
                }
            }

            if ($GLOBALS['req']->file('file_1') != NULL)
            {
                upload_file($GLOBALS['req']->file('file_1'),'pruebas_auditoria',$test_id);    
            }

            Session::flash('message','Prueba de auditor&iacute;a creado correctamente');

        });
        return Redirect::to('/programas_auditoria.show.'.$_POST['audit_audit_plan_audit_program_id']);
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
        $riesgos_proc = array();
        $riesgos_neg = array();
        $audits_selected = array();
        $stakeholder_team = array();
         //obtenemos lista de stakeholders
        $stakeholders = \Ermtool\Stakeholder::select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        //obtenemos lista de organizaciones
        $organizations = \Ermtool\Organization::lists('name','id');

        //obtenemos universo de auditorias
        $audits = \Ermtool\Audit::lists('name','id');

        //obtenemos universo de auditorías seleccionadas
        $auditorias = DB::table('audit_audit_plan')
                    ->where('audit_plan_id','=',$id)
                    ->select('audit_audit_plan.audit_id as id')
                    ->get();

        //cada una de las auditorías pertenecientes al plan
        $i = 0;
        foreach ($auditorias as $audit)
        {
            $audits_selected[$i] = $audit->id;
            $i += 1;
        }

        //obtenemos riesgos de procesos que ya fueron seleccionados
        $riesgos_selected = DB::table('audit_plan_risk')
                                ->join('risk_subprocess','risk_subprocess.id','=','audit_plan_risk.risk_subprocess_id')
                                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                ->where('audit_plan_risk.audit_plan_id','=',$id)
                                ->whereNotNull('audit_plan_risk.risk_subprocess_id')
                                ->select('risk_subprocess.id')
                                ->get();
        $i = 0;
        foreach ($riesgos_selected as $risk)
        {
            $riesgos_proc[$i] = $risk->id;
            $i += 1;
        }

        //obtenemos riesgos de negocio que ya fueron seleccionados
        $riesgos_selected = DB::table('audit_plan_risk')
                                ->join('objective_risk','objective_risk.id','=','audit_plan_risk.objective_risk_id')
                                ->join('risks','risks.id','=','objective_risk.risk_id')
                                ->where('audit_plan_risk.audit_plan_id','=',$id)
                                ->whereNotNull('audit_plan_risk.objective_risk_id')
                                ->select('objective_risk.id')
                                ->get();
        $i = 0;
        foreach ($riesgos_selected as $risk)
        {
            $riesgos_neg[$i] = $risk->id;
            $i += 1;
        }


        //obtenemos stakeholder responsable
        $stakeholder1 = DB::table('audit_plan_stakeholder')
                        ->where('audit_plan_id','=',$id)
                        ->where('role','=',0)
                        ->select('stakeholder_id')
                        ->first();

        //obtenemos equipo de auditores
        $stakeholders2 = DB::table('audit_plan_stakeholder')
                            ->where('audit_plan_id','=',$id)
                            ->where('role','=',1)
                            ->select('stakeholder_id')
                            ->get();
        $i = 0;
        foreach ($stakeholders2 as $stakeholder)
        {
            $stakeholder_team[$i] = $stakeholder->stakeholder_id;
            $i += 1;
        }


        //enviamos id de org seleccionada
        $idorg = $id;

        $audit_plan = \Ermtool\Audit_plan::find($id);

        return view('auditorias.edit',['stakeholders'=>$stakeholders,'organizations'=>$organizations,
                                        'audits'=>$audits,'riesgos_neg' => json_encode($riesgos_neg),
                                        'riesgos_proc'=>json_encode($riesgos_proc),'audits_selected'=>json_encode($audits_selected),
                                        'stakeholder' => $stakeholder1, 'stakeholder_team' => json_encode($stakeholder_team),
                                        'id'=>$idorg,'audit_plan'=>$audit_plan]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtemp)
    {
        //print_r($_POST);
        global $id;
        $id = $idtemp;

        //creamos una transacción para cumplir con atomicidad
        DB::transaction(function()
        {
                if (strpos($_POST['initial_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
                {
                    //damos formato a fecha de inicio
                    $fecha = explode("/",$_POST['initial_date']);
                    $fecha_inicio = $fecha[2]."-".$fecha[0]."-".$fecha[1];
                }
                else
                    $fecha_inicio = $_POST['initial_date'];

                if (strpos($_POST['final_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
                {
                    //damos formato a fecha de término
                    $fecha = explode("/",$request['final_date']);
                    $fecha_termino = $fecha[2]."-".$fecha[0]."-".$fecha[1];
                }
                else
                    $fecha_termino = $_POST['final_date'];

                //actualizamos plan
                DB::table('audit_plans')
                        ->where('id','=',$GLOBALS['id'])
                        ->update([
                        'name'=>$_POST['name'],
                        'description'=>$_POST['description'],
                        'objectives'=>$_POST['objectives'],
                        'scopes'=>$_POST['scopes'],
                        'status'=>0,
                        'resources'=>$_POST['resources'],
                        'methodology'=>$_POST['methodology'],
                        'initial_date'=>$fecha_inicio,
                        'final_date'=>$fecha_termino,
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'rules'=>$_POST['rules'],
                        'organization_id'=>$_POST['organization_id']
                        ]);

                //primero actuakizamos stakeholder responsable
                $resp = DB::table('audit_plan_stakeholder')
                        ->where('audit_plan_id','=',$GLOBALS['id'])
                        ->where('role','=',0)
                        ->update([
                            'stakeholder_id' => $_POST['stakeholder_id']
                            ]);

                

                //ahora actualizamos equipo de stakeholders (si es que hay)
                if (isset($request['stakeholder_team']))
                {   
                    //eliminamos antiguos stakeholders del plan (que su rol sea 1)
                    DB::table('audit_plan_stakeholder')
                    ->where('audit_plan_id',$GLOBALS['id'])
                    ->where('role','=',1)
                    ->delete();

                    //ahora agregamos cada uno
                    foreach ($_POST['stakeholder_team'] as $stakes)
                    {
                        DB::table('audit_plan_stakeholder')
                                ->insert([
                                    'role' => 1,
                                    'audit_plan_id' => $GLOBALS['id'],
                                    'stakeholder_id' => $stakes
                                    ]);
                    }
                }

                 //actualizamos riesgos del negocio (si es que existen)
                if (isset($_POST['objective_risk_id']))
                {
                    //eliminamos antiguos si es que existen
                    DB::table('audit_plan_risk')
                    ->where('audit_plan_id',$GLOBALS['id'])
                    ->whereNotNull('objective_risk_id')
                    ->delete();

                    foreach ($_POST['objective_risk_id'] as $objective_risk)
                    {
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $GLOBALS['id'],
                                    'objective_risk_id' => $objective_risk
                                    ]);
                    }
                }

                //actualizamos riesgos de proceso (si es que existen)
                if (isset($_POST['risk_subprocess_id']))
                {
                    //eliminamos antiguos si es que existen
                    DB::table('audit_plan_risk')
                    ->where('audit_plan_id',$GLOBALS['id'])
                    ->whereNotNull('risk_subprocess_id')
                    ->delete();

                    foreach ($_POST['risk_subprocess_id'] as $risk_subprocess)
                    {
                            DB::table('audit_plan_risk')
                                ->insert([
                                    'audit_plan_id' => $GLOBALS['id'],
                                    'risk_subprocess_id' => $risk_subprocess
                                    ]);
                    }
                }

    /* PRIMERO DEBO HACER MODIFICABLE LAS AUDITORÍAS DEL PLAN 
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
*/
                    //ahora guardamos auditorías nuevas
                    $i = 1; //contador para auditorías nuevas
                    while (isset($_POST['audit_new'.$i.'_name']))
                    {
                        //primero insertamos en tabla audits y obtenemos id
                        $audit_id = DB::table('audits')
                                    ->insertGetId([
                                        'name' => $_POST['audit_new'.$i.'_name'],
                                        'description' => $_POST['audit_new'.$i.'_description'],
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                        ]);

                        //ahora insertamos en audit_audit_plan
                        $audit_audit_plan_id = DB::table('audit_audit_plan')
                                    ->insertGetId([
                                        'audit_plan_id' => $GLOBALS['id'],
                                        'audit_id' => $audit_id,
                                        'initial_date' => $_POST['audit_new'.$i.'_initial_date'],
                                        'final_date' => $_POST['audit_new'.$i.'_final_date'],
                                        'resources' => $_POST['audit_new'.$i.'_resources']
                                        ]);

                        //insertamos riesgos de negocio (de haber)
                       if (isset($_POST['audit_new'.$i.'_objective_risks']))
                       {
                            foreach ($_POST['audit_new'.$i.'_objective_risks'] as $objective_risk)
                            {
                                DB::table('audit_risk')
                                    ->insert([
                                            'audit_audit_plan_id' => $audit_audit_plan_id,
                                            'objective_risk_id' => $objective_risk
                                        ]);
                            }
                       }

                       //insertamos nuevo riesgo de proceso (de haber)
                       if (isset($_POST['audit_new'.$i.'_objective_risks']))
                       {
                            foreach ($_POST['audit_new'.$i.'_risk_subprocess'] as $risk_subprocess)
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
// DEBO AGREGAR MODIFICACIÓN A AUDITORIAS ANTIGUAS                }

                Session::flash('message','Plan de auditor&iacute;a actualizado correctamente');

                


        });

        return Redirect::to('/plan_auditoria');
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
        $id = $_POST['note_id'];
        global $evidence;
        $evidence = $request->file('evidencia_'.$id);

        DB::transaction(function() {

            $id = $_POST['note_id'];

            //insertamos y obtenemos id para verificar que se guarde
            $res = DB::table('notes_answers')
                    ->insertGetId([
                            'answer' => $_POST['answer_'.$id],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'note_id' => $id 
                        ]);

            //guardamos archivo de evidencia (si es que hay)
            if($GLOBALS['evidence'] != NULL)
            {
                //separamos nombre archivo extension
                $file = explode('.',$GLOBALS['evidence']->getClientOriginalName());

                Storage::put(
                    'evidencias_resp_notas/'. $file[0] . "___" . $res . "." . $file[1],
                    file_get_contents($GLOBALS['evidence']->getRealPath())
                );
            }

            if ($res)
            {
                Session::flash('message','Respuesta agregada correctamente');
            }

            else
            {
                Session::flash('error','Problema al agregar la nota. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
            } 
        });

        return Redirect::to('/notas');
         
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
        DB::transaction(function() {
            //id del issue que se está agregando
            $id = $_POST['issue_id'];

            $new_id = DB::table('action_plans')
                            ->insertGetId([
                                    'issue_id' => $id,
                                    'stakeholder_id' => $_POST['responsable_'.$id],
                                    'description' => $_POST['description_'.$id],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'final_date' => $_POST['final_date_'.$id],
                                    'status' => 0
                                ]);

            if ($new_id)
            {
                Session::flash('message','Plan de acción agregado correctamente');
            }
            else
            {
                Session::flash('error','Problema al agregar plan de acción. Intentelo nuevamente y si el problema persiste, contactese con el administrador del sistema.');
            }
        });
        
        return Redirect::to('/planes_accion');
    }

    //función para reporte de planes de acción
    public function actionPlansReport()
    {
        $organizations = \Ermtool\Organization::lists('name','id');

        return view('reportes.planes_accion',['organizations' => $organizations]);
    }

    //función para reporte de auditorías
    public function auditsReport()
    {
        $organizations = \Ermtool\Organization::lists('name','id');

        return view('reportes.auditorias',['organizations' => $organizations]);
    }

    //reporte de auditorías
    public function generarReporteAuditorias($org)
    {
        $results = array();
        $i = 0;
        
        //obtenemos datos de auditoria, (plan audit + audit + audit_risk + process + risk + issues + action_plans)
        //OBS: primero obtendremos auditorías de proceso
        $audits = DB::table('audit_plans')
                    ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->join('audit_risk','audit_risk.audit_audit_plan_id','=','audit_audit_plan.id')
                    ->join('risk_subprocess','risk_subprocess.id','=','audit_risk.risk_subprocess_id')
                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                    ->join('processes','processes.id','=','subprocesses.process_id')
                    ->join('audit_audit_plan_audit_test','audit_audit_plan_audit_test.audit_audit_plan_id','=','audit_audit_plan.id')
                    ->join('issues','issues.audit_audit_plan_audit_test_id','=','audit_audit_plan_audit_test.id')
                    ->join('action_plans','action_plans.issue_id','=','issues.id')
                    ->whereNotNull('audit_risk.risk_subprocess_id')
                    ->where('audit_plans.organization_id','=',(int)$org)
                    ->select('audit_plans.name as audit_plan_name',
                             'audits.name as audit_name',
                             'audits.description as audit_description',
                             'audit_audit_plan.initial_date as initial_date',
                             'audit_audit_plan.final_date as final_date',
                             'processes.name as process_name',
                             'risks.name as risk_name',
                             'issues.name as issue_name',
                             'issues.recommendations as issue_recommendation',
                             'action_plans.description as action_plan',
                             'action_plans.status as action_plan_status',
                             'action_plans.final_date as action_plan_final_date')
                    ->get();

        $type = 'Auditoría de procesos';

        foreach ($audits as $audit)
        {
            if ($audit->issue_recommendation == "")
            {
                $recommendation = 'No se agregaron recomendaciones';
            }
            else
                $recommendation = $audit->issue_recommendation;

            $fecha_inicial = date('d-m-Y',strtotime($audit->initial_date));

            //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
            $fecha_final = date('d-m-Y',strtotime($audit->final_date));

            if ($audit->action_plan_status == 0)
            {
                $estado_plan = 'Abierto';
            }
            else if ($audit->action_plan_status == 1)
            {
                $estado_plan = 'Cerrado';
            }
            else
            {
                $estado_plan = 'Error al obtener estado';
            }

            $fecha_final_plan = date('d-m-Y',strtotime($audit->action_plan_final_date));

            $results[$i] = [
                        'Plan_de_auditoría' => $audit->audit_plan_name,
                        'Auditoría' => $audit->audit_name,
                        'Descripción_auditoría' => $audit->audit_description,
                        'Fecha_inicio' => $fecha_inicial,
                        'Fecha_fin' => $fecha_final,
                        'Proceso_Objetivo' => $audit->process_name,
                        'Riesgo' => $audit->risk_name,
                        'Hallazgo' => $audit->issue_name,
                        'Recomendación' => $recommendation,
                        'Plan_de_acción' => $audit->action_plan,
                        'Estado' => $estado_plan,
                        'Fecha_final_plan' => $fecha_final_plan,
            ];

            $i += 1;
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcelaudit')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
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

    //función que obtiene un programa de auditoría (al crear uno nuevo basado en uno antiguo)
    public function getAuditProgram($id)
    {
        $results = array();
        $tests = array();
        //Seleccionamos programa de auditoría y pruebas
        $audit_program = DB::table('audit_programs')
                    ->where('id','=',$id)
                    ->select('audit_programs.name','audit_programs.id','audit_programs.description')
                    ->get();

        foreach ($audit_program as $program)
        {
            $i = 0; //contador de pruebas
            //obtenemos pruebas
            $audit_tests = DB::table('audit_tests')
                            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                            ->where('audit_audit_plan_audit_program.audit_program_id','=',$program->id)
                            ->select('audit_tests.name','audit_tests.description','audit_tests.type','audit_tests.hh',
                                    'audit_tests.control_id','audit_tests.subprocess_id','audit_tests.risk_id')
                            ->get();

            foreach ($audit_tests as $test)
            {
                if ($test->control_id != NULL)
                {
                    //obtenemos nombre de control
                    $control = \Ermtool\Control::find($test->control_id)->value('id');
                    $risk = NULL;
                    $subprocess = NULL;
                    $category = 1;
                }
                else if ($test->subprocess_id != NULL)
                {
                    //obtenemos nombre de subproceso
                    $subprocess = \Ermtool\Subprocess::find($test->subprocess_id)->value('id');
                    $risk = NULL;
                    $control = NULL;
                    $category = 2;
                }
                else if ($test->risk_id != NULL)
                {
                    //obtenemos nombre de riesgo
                    $risk = \Ermtool\Risk::find($test->risk_id)->value('id');
                    $subprocess = NULL;
                    $control = NULL;
                    $category = 3;
                }

                $tests[$i] = [
                        'name' => $test->name,
                        'description' => $test->description,
                        'type' => $test->type,
                        'hh' => $test->hh,
                        'risk' => $risk,
                        'control' => $control,
                        'subprocess' => $subprocess,
                        'category' => $category,
                            ];
                $i += 1;
            }

            $results = [
                    'name' => $program->name,
                    'id' => $program->id,
                    'description' => $program->description,
                    'tests' => $tests
            ];
        }

        return json_encode($results);
    }

    /*función que obtiene los datos y las pruebas de un programa de auditoría (al revisar un plan de auditoría)
    a través del identificador de audit_audit_plan (auditoría + plan de auditoría) */
    public function getAuditProgram2($id)
    {
        $audit_programs = array();
        
        $j = 0; //contador de pruebas de auditoría
        //Seleccionamos programas y pruebas de auditoria
        $audit_programs = DB::table('audit_audit_plan_audit_program')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$id)
                    ->select('audit_programs.name','audit_audit_plan_audit_program.expiration_date','audit_audit_plan_audit_program.id','audit_programs.description')
                    ->get();

        foreach ($audit_programs as $program)
        {
            $i = 0; //contador de pruebas
            $k = 0; //contador de programas
            $stakeholder = new stdClass();
            //obtenemos actividades
            $audit_tests = DB::table('audit_tests')
                            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                            ->where('audit_audit_plan_audit_program.id','=',$program->id)
                            ->select('audit_tests.name','audit_tests.description','audit_tests.results','audit_tests.id','audit_tests.status','audit_tests.stakeholder_id')
                            ->get();

            $audit_tests2 = array(); //seteamos en 0 variable de pruebas

            foreach ($audit_tests as $test)
            {
                switch ($test->results)
                {
                    case 0:
                        $test_result = 'Inefectiva';
                        break;
                    case 1:
                        $test_result = 'Efectiva';
                        break;
                    case 2:
                        $test_result = 'En proceso';
                        break;
                }

                if ($test->stakeholder_id == NULL)
                {
                    $stakeholder->name = "No se ha";
                    $stakeholder->surnames = "asignado responsable";
                }
                else
                {
                    //Obtenemos stakeholder
                    $stakeholder = \Ermtool\Stakeholder::find($test->stakeholder_id);
                }

                //obtenemos issues
                $issues = DB::table('issues')
                                ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                                ->where('audit_tests.id','=',$test->id)
                                ->select('issues.id','issues.name','issues.description','issues.classification','issues.recommendations')
                                ->get();

                $debilidades = array();
                $j = 0;
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

                    $debilidades[$j] = [
                        'id' => $issue->id,
                        'name' => $issue->name,
                        'description' => $issue->description,
                        'classification' => $class,
                        'recommendations' => $issue->recommendations
                    ];

                    $j += 1;
                }
                //seteamos status
                if ($test->status == 0)
                {
                    $estado = 'Abierta';
                }
                else if ($test->status == 1)
                {
                    $estado = 'En ejecución';
                }
                else if ($test->status == 2)
                {
                    $estado = 'Cerrada';
                }

                //seteamos results
                if ($test->results == 0)
                {
                    $result = 'Inefectiva';
                }
                else if ($test->results == 1)
                {
                    $result = 'Efectiva';
                }
                else if ($test->results == 2)
                {
                    $result = 'En proceso';
                }

                $audit_tests2[$i] = [
                        'name' => $test->name,
                        'description' => $test->description,
                        'result' => $test_result,
                        'id' => $test->id,
                        'status' => $test->status,
                        'status_name' => $estado,
                        'results' => $test->results,
                        'results_name' => $result,
                        'stakeholder' => $stakeholder->name.' '.$stakeholder->surnames,
                        'issues' => $debilidades
                        ];

                $i += 1;
            }

            $audit_programs[$k] = [
                    'name' => $program->name,
                    'id' => $program->id,
                    'description' => $program->description,
                    'audit_tests' => $audit_tests2
            ];

            $k += 1;
        }

        return json_encode($audit_programs);
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

    /* ESTÁ MALA, CORREGIR SI ES QUE ES NECESARIO 
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
    } */

    //obtiene datos asociados al último plan de auditoría generado para una organización
    public function getAuditPlan($org)
    {
        $results = array();
        $auditorias = array();
        $sub_plan_risks = array();
        $obj_plan_risks = array();
        $audit_programs = array();
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

                //obtenemos programas de auditoría realizadas en cada auditoría (si es que hay)
                $audit_programs1 = DB::table('audit_audit_plan_audit_program')
                            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                            ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                            ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$audit->id)
                            ->select('audit_programs.name as audit_program_name')
                            ->get();

                if ($audit_programs1)
                {
                    $j = 0; //contador de programas en cero
                    foreach ($audit_programs1 as $program)
                    {
                        $audit_programs[$j] = ['audit_id' => $audit->id,
                                            'name' => $program->audit_program_name
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
                                    'audit_programs' => $audit_programs,
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
        //OBS ACTUALIZACIÓN: Ahora (13-04-2016) se obtendrán todos los issues que existan asociados a un plan + audit
        $issues = DB::table('issues')
                    ->where('issues.audit_test_id','=',$id)
                    ->select('issues.id','issues.name','issues.description','issues.recommendations','issues.classification')
                    //->first();
                    ->get();
        $i = 0;

        if ($issues == NULL)
        {
            $results = NULL;
        }
        else
        {
            foreach ($issues as $issue)
            {
                //obtenemos evidencias de issue (si es que existen)
                $evidences = getEvidences(2,$issue->id);

                $results[$i] = [
                    'id' => $issue->id,
                    'name' => $issue->name,
                    'description' => $issue->description,
                    'recommendations' => $issue->recommendations,
                    'classification' => $issue->classification,
                    'evidences' => $evidences
                ];

                $i += 1;
            }
        }
        
        return json_encode($results);
    }

    //obtiene notas asociadas a una prueba de auditoría
    public function getNotes($id)
    {
        $results = array();
        $i = 0;
        $notes = DB::table('notes')
                    ->where('audit_test_id','=',$id)
                    ->select('notes.id','notes.name','notes.description','notes.created_at','notes.status',
                             'notes.audit_test_id as test_id')
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
                        $evidences = getEvidences(1,$ans->id);

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
                $evidences = getEvidences(0,$note->id);

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

    //obtenemos id de organización perteneciente a un plan de auditoría
    public function getOrganization($audit_plan)
    {
        $organization = NULL;

        $organization = DB::table('organizations')
                    ->join('audit_plans','audit_plans.organization_id','=','organizations.id')
                    ->where('audit_plans.id','=',$audit_plan)
                    ->select('organizations.id')
                    ->first();

        return json_encode($organization);
    }

    public function indexGraficos()
    {
        $planes_ejec = 0; //planes en ejecución
        $planes_abiertos = 0; //planes con al menos una prueba abierta
        $planes_cerrados = 0; //plan sin pruebas abiertas ni en ejecución, pero si cerradas 
        $audit_plans = array();

        //obtenemos todas las auditorías y pruebas de auditoría con su estado de ejecución

        $planes = \Ermtool\Audit_plan::all(['id','name']);
        $i = 0; //contador de planes
        foreach ($planes as $audit_plan)
        {
            $audits = array();
            $audit_programs = array();
            $audit_tests = array();
            //obtenemos auditorías
            $auditorias = DB::table('audit_audit_plan')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->where('audit_audit_plan.audit_plan_id','=',$audit_plan->id)
                        ->select('audit_audit_plan.id','audits.name')
                        ->get();

            $j = 0; //contador de auditorias por plan
            foreach ($auditorias as $audit)
            {
                $audits[$j] = $audit->name;
                $j += 1;
                //obtenemos programas
                $programs = DB::table('audit_audit_plan_audit_program')
                                ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                                ->where('audit_audit_plan_audit_program.audit_audit_plan_id','=',$audit->id)
                                ->select('audit_audit_plan_audit_program.id','audit_programs.name')
                                ->get();

                $k = 0; //contador de programas
                foreach ($programs as $program)
                {
                    $audit_programs[$k] = $program->name;
                    $k += 1;

                    //obtenemos pruebas
                    $tests = DB::table('audit_tests')
                                ->where('audit_audit_plan_audit_program_id','=',$program->id)
                                ->select('name','status')
                                ->get();

                    //vemos si hay alguna prueba en ejecución, si es así el plan estará en ejecución
                    $l = 0; //contador de pruebas
                    //estados de las pruebas
                    $ejecucion = 0;
                    $abiertas = 0;
                    $cerradas = 0;
                    foreach ($tests as $test)
                    {
                        $audit_tests[$l] = $test->name;
                        $l += 1;

                        if ($test->status == 0)
                        {
                            $abiertas += 1;
                        }
                        else if ($test->status == 1)
                        {
                            $ejecucion += 1;
                        }
                        else if ($test->status == 2)
                        {
                            $cerradas += 1;
                        }
                    }
                }
            }

            $audit_plans[$i] = [
                'name' => $audit_plan->name,
                'audits' => $audits,
                'programs' => $audit_programs,
                'tests' => $audit_tests,
                'ejecucion' => $ejecucion,
                'abiertas' => $abiertas,
                'cerradas' => $cerradas,
            ];
            $i += 1;

            //vemos si el plan está en ejecución o abierto
            if ($ejecucion > 0) //tiene al menos una prueba en ejecución
            {
                $planes_ejec += 1;
            }
            else if ($abiertas > 0) //no tiene pruebas en ejecución y tiene al menos una prueba abierta => plan abierto
            {
                $planes_abiertos += 1;
            }
            else if ($cerradas > 0) //no tiene ni pruebas abiertas ni en ejecución pero si cerradas => plan cerrado
            {
                $planes_cerrados += 1;
            }

        }

        //OBS:: Dejé planes abiertos en 2 y planes cerrados en 1 para mostrar gráfico, ya que por el momento (20-05-2016) no hay planes abiertos ni cerrados, sólo en ejecución
        //return view('reportes.auditorias_graficos',['audit_plans'=>$audit_plans,'planes_ejec'=>$planes_ejec,
        //                                            'planes_abiertos'=>2,'planes_cerrados'=>1]);
        //real
        return view('reportes.auditorias_graficos',['audit_plans'=>$audit_plans,'planes_ejec'=>$planes_ejec,
                                                    'planes_abiertos'=>$planes_abiertos,'planes_cerrados'=>$planes_cerrados]);

    }
}
