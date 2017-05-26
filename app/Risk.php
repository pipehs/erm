<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Risk extends Model
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
    
   	protected $fillable = ['name','type','type2','description','expiration_date',
                            'status','stakeholder_id','risk_category_id','cause_id','effect_id','expected_loss'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;
    public static function name($id)
    {
        return DB::table('risks')->where('id', $id)->value('name');
    }
    
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
    public function organizations()
    {
        return $this->belongsToMany('Ermtool\Organization');
    }

    public function evaluations()
    {
        return $this->belongsToMany('Ermtool\Evaluation');
    }

    //obtenemos riesgos de control
    public static function getRisksFromControl($org,$control)
    {
        $risks = DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                    ->where('control_organization_risk.control_id','=',$control)
                    ->where('organization_risk.organization_id','=',(int)$org)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id','risks.name','risks.description')
                    ->get();

        return $risks;
    }

    public static function getObjectiveRisks($org)
    {
        //ACTUALIZACIÓN 29-03-17: Se obtendrán riesgos de la organización (organization_risk) que además sean riesgos de negocio
        //ACT 10-04-17: Agregamos opción NULL por KRI
        if ($org == NULL)
        {
            return DB::table('objective_risk')
                ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->where('risks.type2','=',1)
                ->groupBy('risks.id','risks.name','risks.description')
                ->select('risks.id as risk_id','risks.name as risk_name','risks.description')
                ->get();
        }
        else
        {
            return DB::table('objective_risk')
                ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->where('organization_risk.organization_id','=',$org)
                ->where('risks.type2','=',1)
                ->groupBy('organization_risk.id','risks.id','risks.name','risks.description')
                ->select('organization_risk.id as id','risks.id as risk_id','risks.name as risk_name','risks.description')
                ->get();
        }
        
    }

    //Función para identificación y matrices de riesgos (igual a la de arriba sólo que para no causar posibles fallos se hizo una nueva agregando la categoría)
    public static function getRisks($org,$category)
    {
        if ($category == NULL)
        {
            //primero obtenemos sólo el id de los riesgos y luego su información
            //ACTUALIZACIÓN 29-03-17: Probablemente group by da lo mismo
            return DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('risks.type2','=',1)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->get();
        }
        else
        {
            return DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('risks.risk_category_id','=',$category)
                    ->where('risks.type2','=',1)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->get();
        }
    }

    //ACT 04-04-17: Igual a las de arriba solo que con where de type para proceso o negocio
    public static function getRisksWithType($org,$category,$type)
    {
        if ($category == NULL)
        {
            //primero obtenemos sólo el id de los riesgos y luego su información
            //ACTUALIZACIÓN 29-03-17: Probablemente group by da lo mismo
            return DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('risks.type2','=',1)
                    ->where('risks.type','=',$type)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->get();
        }
        else
        {
            return DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->where('organization_risk.organization_id','=',$org)
                    ->where('risks.risk_category_id','=',$category)
                    ->where('risks.type2','=',1)
                    ->where('risks.type','=',$type)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risks.stakeholder_id')
                    ->get();
        }
    }

    public static function getRiskSubprocess($org)
    {
        //ACT 10-04-17: Agregamos opción de NULL por KRI  
        if ($org == NULL)
        {
            return DB::table('risk_subprocess')
                ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->where('risks.type2','=',1)
                ->groupBy('risks.id','risks.name','risks.description')
                ->select('risks.id as risk_id','risks.name as risk_name','risks.description')
                ->get();
        }
        else
        {
            return DB::table('risk_subprocess')
                ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->where('organization_risk.organization_id','=',$org)
                ->where('risks.type2','=',1)
                ->groupBy('organization_risk.id','risks.id','risks.name','risks.description')
                ->select('organization_risk.id as id','risks.id as risk_id','risks.name as risk_name','risks.description')
                ->get();
        }
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
                ->groupBy('risks.id','risks.name','risks.description')
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
                ->groupBy('risks.id','risks.name','risks.description')
                ->get();
    }

    public static function getRisksFromOrgRisk($org_risk)
    {
        return DB::table('organization_risk')
                        ->where('organization_risk.id','=',$org_risk)
                        ->join('risks','organization_risk.risk_id','=','risks.id')
                        ->join('organizations','organizations.id','=','organization_risk.organization_id')
                        ->select('risks.id','risks.name as risk_name','risks.description','organizations.name as org','organizations.id as org_id')
                        ->get();
    }

    public static function getOrganizationRisk($org,$risk_id)
    {
        return DB::table('organization_risk')
                ->where('organization_id','=',$org)
                ->where('risk_id','=',$risk_id)
                ->select('id')
                ->first();
    }

    public static function getEnlacedRisks()
    {
        return DB::table('objective_subprocess_risk')
            ->join('risks as risk_subprocess','risk_subprocess.id','=','objective_subprocess_risk.risk_subprocess_id')
            ->join('risks as objective_risk','objective_risk.id','=','objective_subprocess_risk.objective_risk_id')
            ->select('objective_subprocess_risk.id','objective_risk.name as obj_name','risk_subprocess.name as sub_name')
            ->get();
    }
}
