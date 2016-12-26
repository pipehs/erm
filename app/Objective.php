<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Objective extends Model
{
    protected $fillable = ['code','name','description','organization_id','objective_category_id','status','perspective','perspective2','strategic_plan_id'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;
    public static function name($objective_id)
    {
        $res = DB::table('objectives')->where('id', $objective_id)->value('name');
        return $res;
    }

    public function risks()
    {
    	return $this->belongsToMany('\Ermtool\Risk');
    }

    public function strategic_plans()
    {
    	return $this->belongsTo('\Ermtool\Strategic_plan');
    }

    public static function getObjectivesFromControl($org,$control_id)
    {
        return DB::table('objectives')
                ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                ->join('risks','risks.id','=','objective_risk.risk_id')
                ->join('control_objective_risk','control_objective_risk.objective_risk_id','=','objective_risk.id')
                ->where('control_objective_risk.control_id','=',$control_id)
                ->where('objectives.organization_id','=',$org)
                ->select('objectives.id','objectives.name','objectives.description')
                ->groupBy('objectives.id')
                ->get();
    }
}
