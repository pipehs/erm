<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Organization extends Model
{
    protected $fillable = ['name','description','expiration_date','shared_services','organization_id','status','mision','vision','target_client'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public static function name($organization_id)
    {
    	$res = DB::table('organizations')->where('id', $organization_id)->value('name');
    	return $res;
    }

    public function processes()
    {
    	return $this->hasManyThrough('Ermtool\Process', 'Ermtool\Subprocess');
    }

    public function subprocesses()
    {
    	return $this->belongsToMany('Ermtool\Subprocess');
    }

    public function stakeholders()
    {
        return $this->belongsToMany('Ermtool\Stakeholder');
    }

    public function audit_plans()
    {
        return $this->hasMany('Ermtool\Audit_plan');
    }

    public static function getOrgIdByTestId($id)
    {
        $org = DB::table('organizations')
                    ->join('audit_plans','audit_plans.organization_id','=','organizations.id')
                    ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_audit_plan_id','=','audit_audit_plan.id')
                    ->join('audit_tests','audit_tests.audit_audit_plan_audit_program_id','=','audit_audit_plan_audit_program.id')
                    ->where('audit_tests.id','=',$id)
                    ->select('organizations.id')
                    ->first();

        return $org->id;
    }

    public static function getNameByAuditAuditPlan($id)
    {
        $org = DB::table('organizations')
                    ->join('audit_plans','audit_plans.organization_id','=','organizations.id')
                    ->join('audit_audit_plan','audit_audit_plan.audit_plan_id','=','audit_plans.id')
                    ->where('audit_audit_plan.id','=',$id)
                    ->select('organizations.name')
                    ->first();

        return $org->name;
    }
}
