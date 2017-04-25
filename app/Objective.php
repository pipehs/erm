<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Objective extends Model
{
    public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }
    
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
                ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                ->where('control_organization_risk.control_id','=',$control_id)
                ->where('objectives.organization_id','=',(int)$org)
                ->select('objectives.id','objectives.name','objectives.description')
                ->groupBy('objectives.id','objectives.name','objectives.description')
                ->get();
    }

    public static function getObjectivesImpact($strategic_plan_id,$perspective)
    {
        return DB::table('objectives')
                ->where('strategic_plan_id','=',$strategic_plan_id)
                ->where('perspective','<',$perspective)
                ->where('status','=',0)
                ->select('id', DB::raw("CONCAT (code, ' - ', name) AS code_name"))
                ->orderBy('code_name')
                ->get(['code_name','id']);
    }

    public static function deleteObjective($id)
    {
        //primero eliminamos de objectives_impact
        DB::table('objectives_impact')
            ->where('objective_father_id','=',$id)
            ->delete();

        DB::table('objectives_impact')
            ->where('objective_impacted_id','=',$id)
            ->delete();

        //ahora eliminamos riesgo
        DB::table('objectives')
            ->where('id','=',$id)
            ->delete();

        return 0;
    }

    public static function getObjectivesFromOrgRisk($risk,$org)
    {
        return DB::table('objectives')
                ->join('objective_risk','objective_risk.objective_id','=','objectives.id')
                ->where('objective_risk.risk_id','=',$risk)
                ->where('objectives.organization_id','=',$org)
                ->select('objectives.name','objectives.description')
                ->get();
    }
}
