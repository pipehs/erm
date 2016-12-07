<?php
namespace Ermtool;
use Illuminate\Database\Eloquent\Model;
use DB;
class Audit_plan extends Model
{
    protected $fillable = ['name','description','objectives','scopes','status','resources','methodology','initial_date','final_date','rules','hh'];
    public function stakeholders()
    {
        return $this->belongsToMany('\Ermtool\Stakeholder');
    }
    public function organizations()
    {
        return $this->belongsToMany('\Ermtool\Organization');
    }
    public static function name($plan_id)
    {
        $res = DB::table('audit_plans')->where('id', $plan_id)->value('name');
        return $res;
    }
    public static function getNameByAuditAuditPlan($id)
    {
        $res = DB::table('audit_plans')
                    ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                    ->where('audit_audit_plan.id','=',$id)
                    ->select('audit_plans.name')
                    ->first();
        return $res->name;
    }

    public static function getHH($id)
    {
        $hh = DB::table('audit_plans')
                ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_audit_plan_id','=','audit_audit_plan.id')
                ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                ->where('audit_plans.id','=',$id)
                ->select('audit_tests.hh_plan','audit_tests.hh_real')
                ->get();

        return $hh;
    }
}