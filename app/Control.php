<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;
use stdClass;

class Control extends Model
{
    
     protected $fillable = ['name','description','expiration_date','type','type2','evidence','periodicity','purpose','expected_cost','porcentaje_cont','key_control','objective','establishment','application','supervision','test_plan'];

/*
    public function stakeholders()
    {
    	return $this->belongsTo('Ermtool\Stakeholder');
    }
*/
    public static function name($id)
    {
        $res = DB::table('controls')->where('id', $id)->value('name');
        return $res;
    }

    public static function getBussinessControls($org,$risk_id)
    {
        //ACTUALIZACIÓN 21-11-17: Agregamos posible filtro de Riesgo
        //ACTUALIZACIÓN 31-03-17: Obtenemos primero riesgos de negocio
        if ($risk_id != NULL)
        {
            $risks = DB::table('objective_risk')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->where('objectives.organization_id','=',$org)
                    ->where('objective_risk.risk_id','=',$risk_id)
                    ->groupBy('objective_risk.risk_id')
                    ->select('objective_risk.risk_id')
                    ->get();
        }
        else
        {
            $risks = DB::table('objective_risk')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->where('objectives.organization_id','=',$org)
                    ->groupBy('objective_risk.risk_id')
                    ->select('objective_risk.risk_id')
                    ->get();
        }

        $controls = array();
        $i = 0;
        foreach ($risks as $risk)
        {
            $ctrls = DB::table('controls')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('organization_risk.risk_id','=',$risk->risk_id)
                    ->select('controls.*')
                    ->distinct('controls.id','control_organization_risk.stakeholder_id')
                    ->get();

            foreach ($ctrls as $ctrl)
            {
                $controls[$i] = [
                    'id' => $ctrl->id,
                    'name' => $ctrl->name,
                    'description' => $ctrl->description,
                    'created_at' => $ctrl->created_at,
                    'updated_at' => $ctrl->updated_at,
                    'expiration_date' => $ctrl->expiration_date,
                    'type' => $ctrl->type,
                    'type2' => $ctrl->type2,
                    'evidence' => $ctrl->evidence,
                    'periodicity' => $ctrl->periodicity,
                    'purpose' => $ctrl->purpose,
                    'expected_cost' => $ctrl->expected_cost,
                    'stakeholder_id' => $ctrl->stakeholder_id,
                    'porcentaje_cont' => $ctrl->porcentaje_cont,
                    'key_control' => $ctrlctrl->key_control,
                    'objective' => $ctrl->objective,
                    'establishment' => $ctrl->establishment,
                    'application' => $ctrl->application,
                    'supervision' => $ctrl->supervision,
                    'test_plan' => $ctrl->test_plan
                ];

                $i+=1;
            }
        }

        $controls = array_unique($controls,SORT_REGULAR);
        return $controls;
    }

    public static function getProcessesControls($org,$risk_id)
    {
        //ACTUALIZACIÓN 21-11-17: Agregamos posible filtro de Riesgo
        //ACTUALIZACIÓN 31-03-17: Obtenemos primero riesgos de proceso
        if ($risk_id != NULL)
        {
            $risks = DB::table('risk_subprocess')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->where('risk_subprocess.risk_id','=',$risk_id)
                        ->groupBy('risk_subprocess.risk_id')
                        ->select('risk_subprocess.risk_id')
                        ->get();
        }
        else
        {
            $risks = DB::table('risk_subprocess')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->groupBy('risk_subprocess.risk_id')
                        ->select('risk_subprocess.risk_id')
                        ->get();
        }

        $controls = array();
        $i = 0;
        foreach ($risks as $risk)
        {
            $ctrls = DB::table('controls')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('organization_risk.risk_id','=',$risk->risk_id)
                    ->select('controls.*','control_organization_risk.stakeholder_id')
                    ->distinct('controls.id')
                    ->get();

            //ACTUALIZACIÓN 30-11-17: Los responsables dependen de la organización

            foreach ($ctrls as $ctrl)
            {
                $controls[$i] = [
                    'id' => $ctrl->id,
                    'name' => $ctrl->name,
                    'description' => $ctrl->description,
                    'created_at' => $ctrl->created_at,
                    'updated_at' => $ctrl->updated_at,
                    'expiration_date' => $ctrl->expiration_date,
                    'type' => $ctrl->type,
                    'type2' => $ctrl->type2,
                    'evidence' => $ctrl->evidence,
                    'periodicity' => $ctrl->periodicity,
                    'purpose' => $ctrl->purpose,
                    'expected_cost' => $ctrl->expected_cost,
                    'stakeholder_id' => $ctrl->stakeholder_id,
                    'porcentaje_cont' => $ctrl->porcentaje_cont,
                    'key_control' => $ctrl->key_control,
                    'objective' => $ctrl->objective,
                    'establishment' => $ctrl->establishment,
                    'application' => $ctrl->application,
                    'supervision' => $ctrl->supervision,
                    'test_plan' => $ctrl->test_plan
                ];

                $i+=1;
            }
        }

        $controls = array_unique($controls,SORT_REGULAR);
        return $controls;
    }

    //obtiene controles de proceso de una organización que tienen issues
    public static function getProcessesControlsFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            //controles de issues generados directamente a través de mantenedor de hallazgos
            $controls1 = DB::table('control_organization_risk')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('issues','issues.control_id','=','control_organization_risk.control_id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->where('subprocesses.process_id','=',$kind)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();

            //a través de evaluación de controles
            $controls2 = DB::table('control_evaluation')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->where('subprocesses.process_id','=',$kind)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();
        }
        else
        {
            //controles de issues generados directamente a través de mantenedor de hallazgos
            $controls1 = DB::table('control_organization_risk')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('issues','issues.control_id','=','control_organization_risk.control_id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();

            //a través de evaluación de controles
            $controls2 = DB::table('control_evaluation')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();
        }

        $controls = array_merge($controls1,$controls2);

        $controls = array_unique($controls,SORT_REGULAR);

        return $controls;
    }

    //obtiene controles de negocio de una organización que tienen issues
    public static function getObjectivesControlsFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            //controles de issues generados directamente a través de mantenedor de hallazgos
            $controls1 = DB::table('control_organization_risk')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->join('issues','issues.control_id','=','control_organization_risk.control_id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('objectives.id','=',$kind)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();

            //a través de evaluación de controles
            $controls2 = DB::table('control_evaluation')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('objectives.id','=',$kind)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();
        }
        else
        {
            //controles de issues generados directamente a través de mantenedor de hallazgos
            $controls1 = DB::table('control_organization_risk')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->join('issues','issues.control_id','=','control_organization_risk.control_id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('objectives.organization_id','=',$org)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();

            //a través de evaluación de controles
            $controls2 = DB::table('control_evaluation')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','control_evaluation.control_id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->join('controls','controls.id','=','control_organization_risk.control_id')
                            ->where('objectives.organization_id','=',$org)
                            ->select('controls.id','controls.name','controls.description')
                            ->groupBy('controls.id','controls.name','controls.description')
                            ->get();
        }

        $controls = array_merge($controls1,$controls2);

        $controls = array_unique($controls,SORT_REGULAR);

        return $controls;
    }

    //obtiene controles de un subproceso en específico
    /* //ACT 03-04-17: Usaremos sólo función getControlsFromSubprocess 
    public static function getSubprocessControls($subprocess)
    {
        
        $risks = DB::table('risk_subprocess')
                ->
        $controls = DB::table('controls')
                    ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                    ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                    ->where('risk_subprocess.subprocess_id','=',$subprocess)
                    ->select('controls.id','controls.name','controls.description')
                    ->select('controls.id','controls.name','controls.description')
                    ->get();

        return $controls;
    } */

    /*
    public static function getControlsFromRiskSubprocess($risk_subprocess_id)
    {
        return DB::table('control_organization_risk')
                ->where('risk_subprocess_id','=',$risk_subprocess_id)
                ->select('control_id as id')
                ->get();
    }

    public static function getControlsFromObjectiveRisk($objective_risk_id)
    {
        return DB::table('control_objective_risk')
                ->where('objective_risk_id','=',$objective_risk_id)
                ->select('control_id as id')
                ->get();
    }*/

    public static function getControlsFromProcess($org,$process)
    {
        return DB::table('controls')
               ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->where('organization_risk.organization_id','=',$org)
                ->where('subprocesses.process_id','=',$process)
                ->select('controls.id','controls.name','controls.description')
                ->groupBy('controls.id','controls.name','controls.description')
                ->get();
    }

    public static function getControlsFromSubprocess($org, $subprocess)
    {
        //ACT 03-04-17: Primero seleccionamos riesgos asociados al subproceso y a la org
        $risks = DB::table('risk_subprocess')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('risk_subprocess.subprocess_id','=',$subprocess)
                    ->select('risk_subprocess.risk_id as id')
                    ->get();
        $controls = array();
        $i = 0;
        foreach ($risks as $risk)
        {
            $controls[$i] = DB::table('controls')
                        ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                        ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                        ->where('organization_risk.organization_id','=',$org)
                        ->where('organization_risk.risk_id','=',$risk->id)
                        ->select('controls.id','controls.name','controls.description')
                        ->groupBy('controls.id','controls.name','controls.description')
                        ->get();
            $i += 1;
        }
        return $controls;
    }

    public static function getControlsFromPerspective($org,$perspective)
    {
        return DB::table('controls')
                ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->where('objectives.perspective','=',$perspective)
                ->select('controls.id','controls.name','controls.description')
                ->groupBy('controls.id','controls.name','controls.description')
                ->get();
    }

    public static function getEvaluatedControls($org)
    {
        $controls = DB::table('control_eval_risk_temp')
                ->join('control_organization_risk','control_organization_risk.control_id','=','control_eval_risk_temp.control_id')
                ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                ->where('organization_risk.organization_id','=',$org)
                ->select('control_eval_risk_temp.control_id as id')
                ->distinct()
                ->get();

        return $controls; 
    }

    //obtenemos controles de riesgo.
    public static function getControlsFromRisk($org,$risk)
    {
        return DB::table('controls')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('organization_risk.risk_id','=',$risk)
                    ->where('organization_risk.organization_id','=',$org)
                    ->select('controls.id','controls.name','controls.description')
                    ->groupBy('controls.id','controls.name','controls.description')
                    ->get();
    }

    public static function getControlOrganizationRisk($control_id,$org_id)
    {
        return DB::table('control_organization_risk')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('control_organization_risk.control_id','=',$control_id)
                    ->where('organization_risk.organization_id','=',$org_id)
                    ->select('control_organization_risk.id')
                    ->get();
    }

    public static function listControls($org,$type)
    {
        return DB::table('controls')
            ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
            ->where('organization_risk.organization_id','=',$_GET['org'])
            ->where('controls.type2','=',$type)
            ->lists('controls.name','controls.id');
    }

    public static function getControls($org)
    {
        return DB::table('controls')
                ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                ->where('organization_risk.organization_id','=',(int)$org)
                ->select('controls.*','control_organization_risk.stakeholder_id')
                ->distinct()
                ->get();
    }

    public static function getControlByName($name)
    {
        return DB::table('controls')
                ->where('name','=',$name)
                ->select('*')
                ->first();
    }

    public static function getControlByDescription($description)
    {
        return DB::table('controls')
                ->where('description','=',$description)
                ->select('*')
                ->first();
    }
}
