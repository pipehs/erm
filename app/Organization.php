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
    public static function description($organization_id)
    {
        $res = DB::table('organizations')->where('id', $organization_id)->value('description');
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

    public static function getOrgByAuditPlan($id)
    {
        return DB::table('audit_plans')
                ->where('audit_plans.id','=',$id)
                ->select('organization_id as id')
                ->first();
    }

    public function issues()
    {
        return $this->hasMany('Ermtool\Issue');
    }

    //OJO (23-11-16): En un comienzo se está seleccionando una organización por control, pero puede darse el caso que un control esté apuntando a más de una organización
    public static function getOrganizationIdFromControl($id)
    {
        //vemos si es de proceso o de entidad
        $control = DB::table('control_risk_subprocess')
                    ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('control_risk_subprocess.control_id','=',$id)
                    ->select('organization_subprocess.organization_id as id')
                    ->first();

        if (empty($control)) //es de entidad
        {
            $control = DB::table('control_objective_risk')
                    ->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->where('control_objective_risk.control_id','=',$id)
                    ->select('objectives.organization_id as id')
                    ->first();
        }

        return $control;
    }
}
