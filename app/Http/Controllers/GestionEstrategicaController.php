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

class GestionEstrategicaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function kpi()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.kpi',['organizations' => $organizations]);
        }
        else
        {
            return view('gestion_estrategica.kpi',['organizations' => $organizations]);
        }
    }

    public function kpi2()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

        $org_selected = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');
        $kpi = array();

        $financiera = 0;
        $procesos = 0;
        $clientes = 0;
        $aprendizaje = 0;

        $kpiquery = DB::table('kpi')
                    ->join('kpi_objective','kpi_objective.kpi_id','=','kpi.id')
                    ->join('objectives','objectives.id','=','kpi_objective.objective_id')
                    ->where('objectives.organization_id','=',$_GET['organization_id'])
                    ->select('kpi.id','kpi.name','kpi.description','kpi.stakeholder_id','kpi.goal','objectives.name as obj_name','objectives.perspective as perspective')
                    ->get();

        $i = 0;
        foreach ($kpiquery as $k)
        {
            //hacemos ciclo para obtener última medición de cada kpi
            //para esto primero obtenemos la fecha de la última medición (si es que hay mediciones)
            $max_date = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$k->id)
                        ->where('status','=',1)
                        ->max('updated_at');

            //ahora si es que hay fecha, obtenemos datos de última eval
            if ($max_date)
            {
                $last_eval = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$k->id)
                        ->where('updated_at','=',$max_date)
                        ->select('value','status')
                        ->first();

                $last_eval_value = $last_eval->value;
                $last_eval_status = $last_eval->status;
                $date_last = new DateTime($max_date);
                $date_last_eval = date_format($date_last, 'd-m-Y');
            }
            else
            {   //Resulta más fácil configurarlo los mensajes aquí que en la vista (en este caso)
                if (Session::get('languaje') == 'en')
                {
                    $last_eval_value = "No valid assessments";
                    $date_last_eval = "No valid assessments";
                }
                else
                {
                    $last_eval_value = "No hay evaluaciones validadas";
                    $date_last_eval = "No hay evaluaciones validadas";
                }
                $last_eval_status = NULL;
            }

            //vemos si existe eval para validar
            $id_eval = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$k->id)
                        ->where('status','=',0)
                        ->select('id')
                        ->first();

            if ($id_eval) //existe evaluación para validar
            {
                $status = TRUE;
            }
            else
            {
                $status = FALSE;
            }

            $stakeholder = DB::table('stakeholders')
                        ->where('id','=',$k->stakeholder_id)
                        ->select('name','surnames')
                        ->first();

            if ($stakeholder)
            {
                $stake = $stakeholder->name.' '.$stakeholder->surnames;
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    $stake = "No responsable assigned";
                }
                else
                {
                    $stake = "No se ha asignado responsable";   
                }
            }

            //realizamos un contador de perspectivas, para poder mostrar ordenadamente en gráfica

            switch ($k->perspective) {
                        case 1:
                            $financiera += 1;
                            break;
                        case 2:
                            $procesos += 1;
                            break;
                        case 3:
                            $clientes += 1;
                            break;
                        case 4:
                            $aprendizaje += 1;
                            break;
                        default:
                            break;
                    } 

            if ($k->goal == NULL)
            {
                if (Session::get('languaje') == 'en')
                {
                    $goal = "No defined goals";
                }
                else
                {
                    $goal = "No se han definido metas";
                }
            }
            else
            {
                $goal = $k->goal;
            }
            $kpi[$i] = [
                'id' => $k->id,
                'name' => $k->name,
                'description' => $k->description,
                'stakeholder' => $stake,
                'last_eval' => $last_eval_value,
                'status' => $last_eval_status,
                'date_last_eval' => $date_last_eval,
                'goal' => $goal,
                'perspective' => $k->perspective,
                'objective' => $k->obj_name,
                'status_validate' => $status,
            ];

            $i += 1;
        }

        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.kpi',['organizations' => $organizations, 'org_selected' => $org_selected, 'kpi' => $kpi,'org_id' => $_GET['organization_id'],'financiera' => $financiera,'procesos' => $procesos,'clientes' => $clientes,'aprendizaje' => $aprendizaje]);
        }
        else
        {
            return view('gestion_estrategica.kpi',['organizations' => $organizations, 'org_selected' => $org_selected, 'kpi' => $kpi,'org_id' => $_GET['organization_id'],'financiera' => $financiera,'procesos' => $procesos,'clientes' => $clientes,'aprendizaje' => $aprendizaje]);
        }
    }

    public function mapas()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.mapas',['organizations' => $organizations]);
        }
        else
        {
            return view('gestion_estrategica.mapas',['organizations' => $organizations]);
        }
    }

    public function mapas2()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

        $vision = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('vision');
        $org_selected = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');
        $objectives = array();

        $objectives = DB::table('objectives')
                    ->where('objectives.organization_id','=',$_GET['organization_id'])
                    ->where('status','=',0)
                    ->select('name','id','perspective','description')
                    ->get();
        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.mapas',['organizations' => $organizations, 'vision' => $vision, 'objectives' => $objectives, 'org_selected' => $org_selected]);
        }
        else
        {
            return view('gestion_estrategica.mapas',['organizations' => $organizations, 'vision' => $vision, 'objectives' => $objectives, 'org_selected' => $org_selected]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Nuevo KPI para la organización de id = $id
    public function kpiCreate($id)
    {
        //obtenemos todos los objetivos de la organización
        $objectives = \Ermtool\Objective::where('organization_id','=',$id)->where('status',0)->lists('name','id');

        $org_selected = \Ermtool\Organization::where('id',$id)->value('name');

        $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.createkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $id,'stakeholders'=>$stakeholders]);
        }
        else
        {
            return view('gestion_estrategica.createkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $id,'stakeholders'=>$stakeholders]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function kpiStore(Request $request)
    {
        DB::transaction(function() {

            if ($_POST['calculation_method'] == "")
            {
                $calc_method = NULL;
            }
            else
            {
                $calc_method = $_POST['calculation_method'];
            }

            if ($_POST['periodicity'] == "")
            {
                $periodicity = NULL;
            }
            else
            {
                $periodicity = $_POST['periodicity'];
            }

            if ($_POST['stakeholder_id'] == "")
            {
                $stake = NULL;
            }
            else
            {
                $stake = $_POST['stakeholder_id'];
            }

            if ($_POST['initial_value'] == "")
            {
                $initial_value = NULL;
            }
            else
            {
                $initial_value = $_POST['initial_value'];
            }

            if ($_POST['initial_date'] == "")
            {
                $initial_date = NULL;
            }
            else
            {
                $initial_date = $_POST['initial_date'];
            }

            if ($_POST['final_date'] == "")
            {
                $final_date = NULL;
            }
            else
            {
                $final_date = $_POST['final_date'];
            }

            if ($_POST['goal'] == "")
            {
                $goal = NULL;
            }
            else
            {
                $goal = $_POST['goal'];
            }

            //luego de seteados todos los posibles datos nulos, guardamos primero KPI y obtenemos id
            $kpi = \Ermtool\kpi::create([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'calculation_method' => $calc_method,
                'periodicity' => $periodicity,
                'stakeholder_id' => $stake,
                'initial_date' => $initial_date,
                'final_date' => $final_date,
                'initial_value' => $initial_value,
                'goal' => $goal
                ]);

            //ahora guardamos en kpi_objective cada objetivo

            foreach ($_POST['objective_id'] as $obj)
            {
                DB::table('kpi_objective')
                    ->insert([
                        'kpi_id' => $kpi->id,
                        'objective_id' => $obj,
                        'created_at' => date('Y-m-d H:i:s')
                        ]);
            }

            if (isset($kpi))
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','KPI successfully created');
                }
                else
                {
                    Session::flash('message','KPI generado correctamente');
                }
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('error','Error at storing KPI');
                }
                else
                {
                    Session::flash('error','Error al grabar KPI');
                }
            }
        });

        return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
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
    public function kpiEdit($id)
    {
        $obj_selected = array();
        $kpi = \Ermtool\kpi::find($id);
        //obtenemos todos los objetivos de la organización
        $objectives = \Ermtool\Objective::where('organization_id','=',$_GET['org_id'])->where('status',0)->lists('name','id');

        $org_selected = \Ermtool\Organization::where('id',$_GET['org_id'])->value('name');

        $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw('CONCAT(name, " ", surnames) AS full_name'))
        ->orderBy('name')
        ->lists('full_name', 'id');

        //obtenemos los objetivos relacionados al kpi
        $objs = DB::table('kpi_objective')
                    ->where('kpi_id','=',$kpi->id)
                    ->select('objective_id')
                    ->get();
        $i = 0;
        foreach ($objs as $obj)
        {
            $obj_selected[$i] = $obj->objective_id;
            $i += 1;
        }

        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.editkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $_GET['org_id'],'stakeholders'=>$stakeholders,'obj_selected' => $obj_selected,'kpi' => $kpi]);
        }
        else
        {
            return view('gestion_estrategica.editkpi',['objectives' => $objectives,'org_selected' => $org_selected,'org_id' => $_GET['org_id'],'stakeholders'=>$stakeholders,'obj_selected' => $obj_selected,'kpi' => $kpi]);
        }
    }

    public function kpiEvaluate($id)
    {
        $kpi = \Ermtool\kpi::find($id);

        //obtenemos AÑO De última evaluación
        $year = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$id)
                            ->where('status','=',1)
                            ->max('year');

        $min_year = date('Y')-1; //Se puede medir KPI como mínimo desde el año anterior

        if ($year) //si es que hay fecha de evaluación
        {
            // Obtenemos el formato para mostrar la fecha de la última evaluación (dependiendo de la periodicidad del KPI)

            if ($kpi->periodicity == 1) //Mensual
            {
                $month = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$id)
                            ->where('year','=',$year)
                            ->where('status','=',1)
                            ->max('month');

                $last_eval = $month;
            }
            else if ($kpi->periodicity == 2) //Semestral
            {
                $semester = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$id)
                            ->where('year','=',$year)
                            ->where('status','=',1)
                            ->max('semester');
                
                $last_eval = $semester;

            }
            else if ($kpi->periodicity == 3) //Trimestral
            {
                $trimester = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$id)
                            ->where('year','=',$year)
                            ->where('status','=',1)
                            ->max('trimester');

                $last_eval = $trimester;
            }
            else if ($kpi->periodicity == 4) //Anual
            {
                $last_eval = $year;
            }
        }
        else
        {
            $last_eval = NULL;
            $year = NULL;
        }

        //ahora verificaremos que existan una evaluación no validada para actualizar
        $eval = DB::table('kpi_measurements')
                            ->where('kpi_id','=',$id)
                            ->where('status','=',0)
                            ->select('id','month','semester','trimester','year','value')
                            ->first();

        if ($eval)
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval]);
            }
            else
            {
                return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year,'eval'=>$eval]);
            }
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year]);
            }
            else
            {
                return view('gestion_estrategica.medirkpi',['kpi' => $kpi,'org_id' => $_GET['org_id'],'last_eval' => $last_eval,'year' => $year]);
            }
        }
    }

    public function kpiStoreEvaluate()
    {
        //print_r($_POST);
        //verificamos que la eval no exista
        if (isset($_POST['trimestre']))
        {
            $eval = DB::table('kpi_measurements')
                ->where('kpi_id','=',$_POST['kpi_id'])
                ->where('trimester','=',$_POST['trimestre'])
                ->where('year','=',$_POST['ano'])
                ->where('status','=',1)
                ->select('id')
                ->first();
        }
        else if (isset($_POST['mes']))
        {
            $eval = DB::table('kpi_measurements')
                ->where('kpi_id','=',$_POST['kpi_id'])
                ->where('month','=',$_POST['mes'])
                ->where('year','=',$_POST['ano'])
                ->where('status','=',1)
                ->select('id')
                ->first();
        }
        else if (isset($_POST['semestre']))
        {
            $eval = DB::table('kpi_measurements')
                ->where('kpi_id','=',$_POST['kpi_id'])
                ->where('semester','=',$_POST['semestre'])
                ->where('year','=',$_POST['ano'])
                ->where('status','=',1)
                ->select('id')
                ->first();
        }
        else //es anual
        {
            $eval = DB::table('kpi_measurements')
                ->where('kpi_id','=',$_POST['kpi_id'])
                ->where('year','=',$_POST['ano'])
                ->where('status','=',1)
                ->select('id')
                ->first();
        }
        if ($eval)
        {
            if (Session::get('languaje') == 'en')
            {
                Session::flash('error','The evaluation period already exists. Please evaluate on a new period.');
            }
            else
            {
                Session::flash('error','El periodo de evaluación ya existe. Debe evaluar en un periodo nuevo');
            }
            return Redirect::to('kpi.evaluate.'.$_POST['kpi_id'].'?org_id='.$_POST['org_id'])->withInput();
        }
        else
        {
            DB::transaction(function() {

                if (isset($_POST['trimestre']))
                {
                        //ahora vemos si se está actualizando o creando una nueva evaluación
                        $eval2 = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$_POST['kpi_id'])
                        ->where('trimester','=',$_POST['trimestre'])
                        ->where('year','=',$_POST['ano'])
                        ->where('status','=',0)
                        ->select('id')
                        ->first();

                        if ($eval2) //se está actualizando evaluación
                        {
                            DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                ->update([
                                    'value' => $_POST['value'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                        }
                        else //es evaluación nueva
                        {
                            DB::table('kpi_measurements')
                                ->insert([
                                    'kpi_id' => $_POST['kpi_id'],
                                    'value' => $_POST['value'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'trimester' => $_POST['trimestre'],
                                    'year' => $_POST['ano']
                                    ]);
                        }

                        Session::forget('error');
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Measurement successfully saved');
                        }
                        else
                        {
                            Session::flash('message','Medición guardada con éxito');
                        }
                }
                else if (isset($_POST['semester']))
                {
                        //ahora vemos si se está actualizando o creando una nueva evaluación
                        $eval2 = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$_POST['kpi_id'])
                        ->where('semester','=',$_POST['semester'])
                        ->where('year','=',$_POST['ano'])
                        ->where('status','=',0)
                        ->select('id')
                        ->first();

                        if ($eval2) //se está actualizando evaluación
                        {
                            DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                ->update([
                                    'value' => $_POST['value'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                        }
                        else //es evaluación nueva
                        {
                            DB::table('kpi_measurements')
                                ->insert([
                                    'kpi_id' => $_POST['kpi_id'],
                                    'value' => $_POST['value'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'semester' => $_POST['semester'],
                                    'year' => $_POST['ano']
                                    ]);
                        }

                        Session::forget('error');
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Measurement successfully saved');
                        }
                        else
                        {
                            Session::flash('message','Medición guardada con éxito');
                        }
                }
                else if (isset($_POST['mes']))
                {
                        //ahora vemos si se está actualizando o creando una nueva evaluación
                        $eval2 = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$_POST['kpi_id'])
                        ->where('month','=',$_POST['mes'])
                        ->where('year','=',$_POST['ano'])
                        ->where('status','=',0)
                        ->select('id')
                        ->first();

                        if ($eval2) //se está actualizando evaluación
                        {
                            DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                ->update([
                                    'value' => $_POST['value'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                        }
                        else //es evaluación nueva
                        {
                            DB::table('kpi_measurements')
                                ->insert([
                                    'kpi_id' => $_POST['kpi_id'],
                                    'value' => $_POST['value'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'month' => $_POST['mes'],
                                    'year' => $_POST['ano']
                                    ]);
                        }

                        Session::forget('error');
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Measurement successfully saved');
                        }
                        else
                        {
                            Session::flash('message','Medición guardada con éxito');
                        }
                }

                else //es anual
                {
                    //ahora vemos si se está actualizando o creando una nueva evaluación
                        $eval2 = DB::table('kpi_measurements')
                        ->where('kpi_id','=',$_POST['kpi_id'])
                        ->where('year','=',$_POST['ano'])
                        ->where('status','=',0)
                        ->select('id')
                        ->first();

                        if ($eval2) //se está actualizando evaluación
                        {
                            DB::table('kpi_measurements')->where('id','=',$eval2->id)
                                ->update([
                                    'value' => $_POST['value'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                        }
                        else //es evaluación nueva
                        {
                            DB::table('kpi_measurements')
                                ->insert([
                                    'kpi_id' => $_POST['kpi_id'],
                                    'value' => $_POST['value'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'status' => 0,
                                    'year' => $_POST['ano']
                                    ]);
                        }

                        Session::forget('error');
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Measurement successfully saved');
                        }
                        else
                        {
                            Session::flash('message','Medición guardada con éxito');
                        }
                }
            });

            return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
        }

    }

    public function kpiValidate($id1)
    {
        global $id;
        $id = $id1;
        //cambiamos estado de kpi validado
        DB::transaction(function() {
            $kpi = DB::table('kpi_measurements')
                ->where('kpi_id','=',$GLOBALS['id'])
                ->where('status','=',0)
                ->update([
                    'status' => 1,
                    ]);

            if ($kpi)
            {
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','KPI successfully validated');
                }
                else
                {
                    Session::flash('message','KPI validado con éxito');
                }
            }
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function kpiUpdate(Request $request, $id1)
    {
        global $id;
        $id = $id1;
        DB::transaction(function() {

            $kpi = \Ermtool\kpi::find($GLOBALS['id']);

            if ($_POST['calculation_method'] == "")
            {
                $calc_method = NULL;
            }
            else
            {
                $calc_method = $_POST['calculation_method'];
            }

            if ($_POST['periodicity'] == "")
            {
                $periodicity = NULL;
            }
            else
            {
                $periodicity = $_POST['periodicity'];
            }

            if ($_POST['stakeholder_id'] == "")
            {
                $stake = NULL;
            }
            else
            {
                $stake = $_POST['stakeholder_id'];
            }

            if ($_POST['initial_value'] == "")
            {
                $initial_value = NULL;
            }
            else
            {
                $initial_value = $_POST['initial_value'];
            }

            if ($_POST['initial_date'] == "")
            {
                $initial_date = NULL;
            }
            else
            {
                $initial_date = $_POST['initial_date'];
            }

            if ($_POST['final_date'] == "")
            {
                $final_date = NULL;
            }
            else
            {
                $final_date = $_POST['final_date'];
            }

            if ($_POST['goal'] == "")
            {
                $goal = NULL;
            }
            else
            {
                $goal = $_POST['goal'];
            }

            $kpi->name = $_POST['name'];
            $kpi->description = $_POST['description'];
            $kpi->calculation_method = $calc_method;
            $kpi->periodicity = $periodicity;
            $kpi->stakeholder_id = $stake;
            $kpi->initial_value = $initial_value;
            $kpi->initial_date = $initial_date;
            $kpi->final_date = $final_date;
            $kpi->goal = $goal;

            //primero que todo, eliminaremos los objetivos anteriores del kpi para evitar repeticiones
            DB::table('kpi_objective')->where('kpi_id',$GLOBALS['id'])->delete();

            //ahora, agregamos posibles nuevas relaciones
            foreach($_POST['objectives_id'] as $obj_id)
            {
                DB::table('kpi_objective')->insert([
                    'objective_id'=>$obj_id,
                    'kpi_id'=>$GLOBALS['id']
                    ]);
            }

            $kpi->save();
            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','KPI successfully generated');
            }
            else
            {
                Session::flash('message','KPI generado correctamente');
            }
        });

        return Redirect::to('kpi2?organization_id='.$_POST['org_id']);
    }

    public function kpiMonitor()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
        if (Session::get('languaje') == 'en')
        {
            return view('en.gestion_estrategica.monitorkpi',['organizations' => $organizations]);
        }
        else
        {
            return view('gestion_estrategica.monitorkpi',['organizations' => $organizations]);
        }
    }

    public function kpiMonitor2()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

        $org_selected = \Ermtool\Organization::where('id',$_GET['organization_id'])->value('name');

        //obtenemos info del KPI
        $kpi = \Ermtool\kpi::find($_GET['kpi_id']);

        //obtenemos mediciones del KPI
        $measures = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$_GET['kpi_id'])
                    ->select('value','month','trimester','semester','year')
                    ->get();

        //obtenemos datos del responsable
        if ($kpi->stakeholder_id != NULL)
        {
            $stake = \Ermtool\Stakeholder::where('id',$kpi->stakeholder_id)->select('name','surnames')->first();
        }
        else
        {
            $stake = NULL;
        }

        if (Session::get('languaje') == 'en')
        {
            $meses = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $trimestres = ['First quarter','Second quarter','Third quarter','Fourth quarter'];
            $semestres = ['First half','Second half'];

            return view('en.gestion_estrategica.monitorkpi',['organizations' => $organizations,'org_selected' => $org_selected,'kpi' => $kpi, 'measures' => $measures,'stake' => $stake,'meses' => $meses, 'trimestres' => $trimestres, 'semestres' => $semestres]);
        }
        else
        {
            $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            $trimestres = ['Primer trimestre','Segundo trimestre','Tercer trimestre','Cuarto trimestre'];
            $semestres = ['Primer semestre','Segundo semestre'];

            return view('gestion_estrategica.monitorkpi',['organizations' => $organizations,'org_selected' => $org_selected,'kpi' => $kpi, 'measures' => $measures,'stake' => $stake,'meses' => $meses, 'trimestres' => $trimestres, 'semestres' => $semestres]);
        }
    }

    public function getKpi($org)
    {
        //obtenemos KPI de determinada organización
        $kpi = DB::table('kpi')
                ->join('kpi_objective','kpi_objective.kpi_id','=','kpi.id')
                ->join('objectives','objectives.id','=','kpi_objective.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->select('kpi.name','kpi.id')
                ->orderBy('kpi.name')
                ->distinct()
                ->get();

        return json_encode($kpi);
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
}
