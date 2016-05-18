<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

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
}
 