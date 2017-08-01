<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Stakeholder extends Model
{
    
    protected $fillable = ['id','dv','name','surnames','role','position','mail','organization_id','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function organizations()
    {
    	return $this->belongsToMany('Ermtool\Organization');
    }

    public function evaluations()
    {
    	return $this->belongsTo('Ermtool\Evaluation','evaluation_risk_stakeholder','evaluation_id','stakeholder_id');
    }

    public function roles()
    {
        return $this->belongsToMany('Ermtool\Role');
    }

    public function audit_plans()
    {
        return $this->belongsToMany('Ermtool\Audit_plan');
    }
    public function polls()
    {
        return $this->belongsToMany('Ermtool\Poll');
    }
    public function user()
    {
        return $this->hasOne('App\User');
    }

    public static function getName($rut)
    {
        $stakeholder = \Ermtool\Stakeholder::where('id',$rut)->first(['name','surnames']);
        
        return $stakeholder->name.' '.$stakeholder->surnames;
    }

    public static function listStakeholders($org)
    {
        //para los casos en que sea muy difícil especificar la org, dejaremos momentáneamente la opción de NULL

        if ($org == NULL)
        {
            $stakeholders = \Ermtool\Stakeholder::where('status',0)->select('id', DB::raw("CONCAT(name, ' ', surnames) AS full_name"))
            ->orderBy('name')
            ->lists('full_name', 'id');
        }
        else
        {
            $stakeholders = \Ermtool\Stakeholder::where('status',0)
                ->join('organization_stakeholder','organization_stakeholder.stakeholder_id','=','stakeholders.id')
                ->where('organization_stakeholder.organization_id','=',$org)
                ->select('stakeholders.id', DB::raw("CONCAT(name,' ', surnames) AS full_name"))
                ->orderBy('name')
                ->lists('full_name', 'id');
        }

        return $stakeholders;
    }
}
 