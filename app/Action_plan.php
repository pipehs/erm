<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Action_plan extends Model
{
    //
    protected $fillable = ['description','final_date','status'];


    public static function getActionPlanFromIssue($issue)
    {
    	return DB::table('action_plans')
    			->where('issue_id','=',$issue)
    			->select('id','description','status')
    			->first();
    }

    public static function getOpenedActionPlans()
    {
    	return DB::table('action_plans')
    			->where('status','=',0)
    			->select('action_plans.id','action_plans.description','action_plans.final_date','action_plans.stakeholder_id')
    			->get();
    }
}
