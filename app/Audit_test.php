<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit_test extends Model
{
    protected $fillable = ['name','description','type','status','results','hh'];


    public static function getTestNameById($id)
    {
    	return DB::table('audit_tests')->where('id',$id)->value('name');
    }

    public static function getTests($org,$audit_plan)
    {
    	$tests = DB::table('audit_tests')
    			->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
    			->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
    			->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
    			->join('audits','audits.id','=','audit_audit_plan.audit_id')
    			->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
    			->where('audit_plans.id','=',$audit_plan)
    			->where('audit_plans.organization_id','=',$org)
    			->select('audit_tests.id','audit_tests.name','audit_tests.description','audit_programs.name as program','audits.name as audit')
    			->groupBy('audit_tests.id')
    			->get();

    	return $tests;
    }

    public static function getMaxDate($control)
    {
        $max = DB::table('audit_tests')
                ->where('control_id','=',$control)
                ->where('status','=',2)
                ->max('updated_at');

        return $max;
    }

    public static function getTestFromDate($date,$control)
    {
        $test = DB::table('audit_tests')
                ->where('updated_at','=',$date)
                ->where('control_id','=',$control)
                ->where('status','=',2)
                ->select('results')
                ->first();

        return $test;
    }
}
