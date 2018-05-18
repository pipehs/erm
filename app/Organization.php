<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Organization extends Model
{
    
    protected $fillable = ['name','description','expiration_date','shared_services','organization_id','status','mision','vision','target_client','ebt','kind_ebt'];

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
                $res[$i] = ['id' => $o->id,
                            'name' => $o->name];
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

    public static function getOrganizationsFromRisk($risk_id)
    {
        return DB::table('organization_risk')
                ->join('organizations','organizations.id','=','organization_risk.organization_id')
                ->where('organization_risk.risk_id','=',$risk_id)
                ->select('organization_risk.organization_id','organization_risk.stakeholder_id','organizations.name')
                ->get();
    }

    public static function getOrgByName($org_name)
    {
        return DB::table('organizations')
                ->where('name','=',$org_name)
                ->select('id')
                ->first();
    }

    public static function getEBT($id,$kind)
    {
        //si no se envía ID, se busca EBT de org. principal
        if ($kind == 2) 
        {
            $org = DB::table('organizations')
                ->where('id','=',$id)
                ->select('organization_id','ebt','kind_ebt')
                ->first();

            //vemos si es organización principal
            if ($org->organization_id == NULL)
            {
                return $org;
            }
            //si no lo es, buscamos recursivamente cuál es la organización principal
            else
            {
                $org_father = DB::table('organizations')
                            ->where('id','=',$org->organization_id)
                            ->select('organization_id','ebt','kind_ebt')
                            ->first();

                while ($org_father->organization_id != NULL)
                {
                    $org_father = DB::table('organizations')
                            ->where('id','=',$org_father->organization_id)
                            ->select('organization_id','ebt','kind_ebt')
                            ->first();
                }

                return $org_father;
            }
        }
        else
        {
            return DB::table('organizations')
                ->where('id','=',$id)
                ->select('ebt','kind_ebt')
                ->first();
        }
    }

    public static function getOrgByControlEvaluation($id)
    {
        $org = DB::table('organizations')
            ->join('control_organization','control_organization.organization_id','=','organizations.id')
            ->join('control_evaluation','control_evaluation.control_organization_id','=','control_organization.id')
            ->where('control_evaluation.id','=',$id)
            ->select('organizations.id')
            ->first();

        //OBS 20-01-18: El agregado de atributo organization_id en control_evaluation es nuevo, por lo que si no existiera para evitar errores se buscará organización a través del control
        if (empty($org) || (isset($org->id) && $org->id == NULL))
        {
            $org = DB::table('organizations')
                ->join('organization_risk','organization_risk.organization_id','=','organizations.id')
                ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                ->join('controls','controls.id','=','control_organization_risk.control_id')
                ->join('control_evaluation','control_evaluation.control_id','=','controls.id')
                ->where('control_evaluation.id','=',$id)
                ->select('organizations.id')
                ->first(); 
        }
        
        return $org;
    }

    public static function getOrgByActionPlan($action_plan_id)
    {
        return DB::table('action_plans')
            ->join('issues','issues.id','=','action_plans.issue_id')
            ->join('organizations','organizations.id','=','issues.organization_id')
            ->where('action_plans.id','=',$action_plan_id)
            ->select('organizations.id','organizations.name','organizations.description')
            ->first();
    }

    public static function getOrganizationByCO($ctrl_org_id)
    {
        return DB::table('organizations')
            ->join('control_organization','control_organization.organization_id','=','organizations.id')
            ->where('control_organization.id','=',$ctrl_org_id)
            ->select('organizations.id','organizations.name','organizations.description')
            ->first();
    }
}
