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
    public static function getRisksFromControl($control)
    {
        $risks = DB::table('risks')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                    ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                    ->where('control_risk_subprocess.control_id','=',$control)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id')
                    ->get();

        if (empty($risks))
        {
            $risks = DB::table('risks')
                    ->join('objective_risk','objective_risk.risk_id','=','risks.id')
                    ->join('control_objective_risk','control_objective_risk.objective_risk_id','=','objective_risk.id')
                    ->where('control_objective_risk.control_id','=',$control)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id')
                    ->get();
        }

        return $risks;
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
}
