<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Control extends Model
{
     protected $fillable = ['name','description','expiration_date','type','type2','evidence','periodicity','purpose','stakeholder_id','expected_cost'];

    public function stakeholders()
    {
    	return $this->belongsTo('Ermtool\Stakeholder');
    }

    public static function name($id)
    {
        $res = DB::table('controls')->where('id', $id)->value('name');
        return $res;
    }

    public static function getBussinessControls($org)
    {
    	$ctrl = DB::table('controls')
                    ->join('control_objective_risk','control_objective_risk.control_id','=','controls.id')
                    ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->where('objectives.organization_id','=',$org)
                    ->select('controls.id','controls.name','controls.description')
                    ->distinct('controls.id')
                    ->get();

        return $ctrl;
    }

    public static function getProcessesControls($org)
    {
    	$ctrl = DB::table('controls')
                    ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                    ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->select('controls.id','controls.name','controls.description')
                    ->distinct('controls.id')
                    ->get();

        return $ctrl;
    }

    //obtiene controles de proceso de una organización que tienen issues
    public static function getProcessesControlsFromIssues($org)
    {
        //controles de issues generados directamente a través de mantenedor de hallazgos
        $controls1 = DB::table('control_risk_subprocess')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('issues','issues.control_id','=','control_risk_subprocess.control_id')
                        ->join('controls','controls.id','=','control_risk_subprocess.control_id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('controls.id','controls.name','controls.description')
                        ->groupBy('controls.id')
                        ->get();

        //a través de evaluación de controles
        $controls2 = DB::table('control_evaluation')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','control_evaluation.control_id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->join('issues','issues.id','=','control_evaluation.issue_id')
                        ->join('controls','controls.id','=','control_risk_subprocess.control_id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('controls.id','controls.name','controls.description')
                        ->groupBy('controls.id')
                        ->get();

        $controls = array_merge($controls1,$controls2);

        $controls = array_unique($controls,SORT_REGULAR);

        return $controls;
    }

    //obtiene controles de negocio de una organización que tienen issues
    public static function getObjectivesControlsFromIssues($org)
    {
        //controles de issues generados directamente a través de mantenedor de hallazgos
        $controls1 = DB::table('control_objective_risk')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->join('issues','issues.control_id','=','control_objective_risk.control_id')
                        ->join('controls','controls.id','=','control_objective_risk.control_id')
                        ->where('objectives.organization_id','=',$org)
                        ->select('controls.id','controls.name','controls.description')
                        ->groupBy('controls.id')
                        ->get();

        //a través de evaluación de controles
        $controls2 = DB::table('control_evaluation')
                        ->join('control_objective_risk','control_objective_risk.control_id','=','control_evaluation.control_id')
                        ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->join('issues','issues.id','=','control_evaluation.issue_id')
                        ->join('controls','controls.id','=','control_objective_risk.control_id')
                        ->where('objectives.organization_id','=',$org)
                        ->select('controls.id','controls.name','controls.description')
                        ->groupBy('controls.id')
                        ->get();

        $controls = array_merge($controls1,$controls2);

        $controls = array_unique($controls,SORT_REGULAR);

        return $controls;
    }

    //obtiene controles de un subproceso en específico
    public static function getSubprocessControls($subprocess)
    {
        $controls = DB::table('controls')
                    ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                    ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                    ->where('risk_subprocess.subprocess_id','=',$subprocess)
                    ->select('controls.id','controls.name','controls.description')
                    ->groupBy('controls.id')
                    ->get();

        return $controls;
    }

    public static function getControlsFromRiskSubprocess($risk_subprocess_id)
    {
        return DB::table('control_risk_subprocess')
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
    }
}
