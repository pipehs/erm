<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Risk extends Model
{
   	protected $fillable = ['name','type','type2','description','expiration_date',
                            'status','stakeholder_id','risk_category_id','cause_id','effect_id','expected_loss'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function causes()
    {
    	return $this->belongsToMany('Ermtool\Cause');
    }

    public function effects()
    {
    	return $this->belongsToMany('Ermtool\Effect');
    }

    public function risk_categories()
    {
    	return $this->belongsTo('Ermtool\Risk_category');
    }
    public function subprocesses()
    {
        return $this->belongsToMany('Ermtool\Subprocess');
    }
    public function objectives()
    {
        return $this->belongsToMany('Ermtool\Objective');
    }

    public function evaluations()
    {
        return $this->belongsToMany('Ermtool\Evaluation');
    }

    //obtenemos riesgos de control. type=0 (de proceso) type=1 (de negocio)
    public static function getRisksFromControl($org,$control)
    {
        $risks = DB::table('risks')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                    ->where('control_risk_subprocess.control_id','=',$control)
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id')
                    ->get();

        if (empty($risks))
        {
            $risks = DB::table('risks')
                    ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->join('control_objective_risk','control_objective_risk.objective_risk_id','=','objective_risk.id')
                    ->where('control_objective_risk.control_id','=',$control)
                    ->where('objectives.organization_id','=',$org)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id')
                    ->get();
        }

        return $risks;
    }

    public static function getObjectiveRisks($org)
    {
        return DB::table('objective_risk')
                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                ->join('risks','risks.id','=','objective_risk.risk_id')
                ->join('organizations','organizations.id','=','objectives.organization_id')
                ->where('organizations.id','=',$org)
                ->where('risks.type2','=',1)
                ->select('objective_risk.id as id','risks.id as risk_id','risks.name as risk_name','objectives.name as objective_name')
                ->get();
    }

    public static function getRiskSubprocess($org)
    {
        return DB::table('risk_subprocess')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('risks.type2','=',1)
                ->select('risk_subprocess.id as id','risks.id as risk_id','risks.name as risk_name','subprocesses.name as subprocess_name')
                ->get();
    }

    public static function getRiskSubprocessFromControl($control_id)
    {
        return DB::table('control_risk_subprocess')
                    ->where('control_id','=',$control_id)
                    ->select('control_risk_subprocess.risk_subprocess_id as id')
                    ->get();
    }

    public static function getObjectiveRiskFromControl($control_id)
    {
        return DB::table('control_objective_risk')
                    ->where('control_id','=',$control_id)
                    ->select('control_objective_risk.objective_risk_id as id')
                    ->get();
    }

    public static function getObjectiveRisksFromControl($control_id)
    {
        return DB::table('control_objective_risk')
                    ->join('objective_risk','control_objective_risk.objective_risk_id','=','objective_risk.id')
                    ->join('objectives','objective_risk.objective_id','=','objectives.id')
                    ->join('organizations','organizations.id','=','objectives.organization_id')
                    ->join('risks','objective_risk.risk_id','=','risks.id')
                    ->where('control_objective_risk.control_id','=',$control_id)
                    ->select('objectives.name as obj_name','risks.name as risk_name')
                    ->get();
    }

    public static function getRisksSubprocessFromControl($control_id)
    {
        return DB::table('control_risk_subprocess')
                    ->join('risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                    ->join('subprocesses','risk_subprocess.subprocess_id','=','subprocesses.id')
                    ->join('risks','risk_subprocess.risk_id','=','risks.id')
                    ->where('control_risk_subprocess.control_id','=',$control_id)
                    ->select('subprocesses.name as sub_name','risks.name as risk_name')
                    ->get();
    }

    public static function getRisksFromProcess($org,$process_id)
    {
        return DB::table('risks')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('subprocesses.process_id','=',$process_id)
                ->select('risks.id','risks.name','risks.description')
                ->groupBy('risks.id')
                ->get();
    }

    public static function getRisksFromSubprocess($org,$subprocess_id)
    {
        return DB::table('risks')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('subprocesses.id','=',$subprocess_id)
                ->select('risks.id','risks.name','risks.description')
                ->groupBy('risks.id')
                ->get();
    }
}
