<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Action_plan_alert extends Model
{
    //
    protected $fillable = ['action_plan_id','message','kind','stakeholder_id','cc','cco','email'];

    public function stakeholders()
    {
    	return $this->belongsTo('Ermtool\Stakeholder');
    }

    public function action_plans()
    {
    	return $this->belongsTo('Ermtool\Action_plan');
    }
}
