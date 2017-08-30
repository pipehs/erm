<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Organization extends Model
{
    
    protected $fillable = ['name','description','expiration_date','shared_services','organization_id','status','mision','vision','target_client'];

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

    public function objectives()
    {
        return $this->hasMany('Ermtool\Objective');
    }

    public function risks()
    {
        return $this->belongsToMany('Ermtool\Risk');
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
        $control = DB::table('controls')
                    ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                    ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                    ->where('control_organization_risk.control_id','=',$id)
                    ->select('organization_risk.organization_id as id')
                    ->first();

        return $control;
    }
    public static function organizationsWithoutRisk($risk_id)
    {
        /*return DB::table('organizations')
            ->leftJoin('organization_risk','organization_risk.organization_id','=','organizations.id')
            ->where('organization_risk.risk_id','<>',$risk_id)
            ->where('organization_risk.id','=',NULL)
            ->lists('organizations.name','organizations.id');*/

        $orgs = DB::table('organizations')
                ->where('status','=',0)
                ->get(['name','id']);

        $res = array();
        $i = 0;
        foreach ($orgs as $o)
        {    
            $org_risk = DB::table('organization_risk')
                        ->where('organization_id','=',$o->id)
                        ->where('risk_id','=',$risk_id)
                        ->get();

            if (empty($org_risk)) //significa que esta organización no tiene 
            {
                $res[$i] = $o->id.',';
                $res[$i] .= $o->name;
                $i+=1;
            }
        }

        return $res;
    }

    public static function getFatherOrgName($org)
    {
        $father_org = DB::table('organizations')
                    ->where('id','=',$org)
                    ->select('organization_id as id')
                    ->first();

        if ($father_org != NULL && !empty($father_org))
        {
            $father_org_name = DB::table('organizations')->where('id', $father_org->id)->value('name');

            return $father_org_name;
        }
        else
        {
            return NULL;
        }
        
    }

    public static function getOrganizationByOrgRisk($org_risk_id)
    {
        return DB::table('organizations')
                ->join('organization_risk','organization_risk.organization_id','=','organizations.id')
                ->where('organization_risk.id','=',$org_risk_id)
                ->select('organizations.id','organizations.name','organizations.description')
                ->first();
    }
}
