<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

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
    			->select('audit_plans.name AS audit_plan_name',
                    'audits.name AS audit_name','audit_programs.name as audit_program_name','audit_tests.id','audit_tests.description',
                    'audit_tests.name','audit_tests.type','audit_tests.status','audit_tests.results',
                    'audit_tests.hh_plan','audit_tests.hh_real','audit_tests.process_id',
                    'audit_tests.stakeholder_id')
    			->groupBy('audit_plans.name','audits.name','audit_programs.name','audit_tests.id','audit_tests.description','audit_tests.name','audit_tests.type','audit_tests.status','audit_tests.results','audit_tests.hh_plan','audit_tests.hh_real','audit_tests.process_id','audit_tests.stakeholder_id')
    			->get();

    	return $tests;
    }

    public static function getMaxDate($control)
    {
        $max = DB::table('audit_tests')
                ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                ->where('audit_test_control.control_id','=',$control)
                ->where('status','=',2)
                ->max('updated_at');

        return $max;
    }

    public static function getTestFromDate($date,$control)
    {
        $test = DB::table('audit_tests')
                 ->join('audit_test_control','audit_test_control.audit_test_id','=','audit_tests.id')
                ->where('audit_tests.updated_at','=',$date)
                ->where('audit_test_control.control_id','=',$control)
                ->where('status','=',2)
                ->select('results')
                ->first();

        return $test;
    }

    public static function getAuditTestsFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            $audits = DB::table('issues')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->where('audit_tests.id','=',$kind)
                    ->select('audit_tests.id','audit_tests.name','audit_tests.description','audit_plans.name as audit_plan','audits.name as audit','audit_programs.name as program')
                    ->groupBy('audit_tests.id','audit_tests.name','audit_tests.description','audit_plans.name','audits.name','audit_programs.name')
                    ->get();
        }
        else
        {
            $audits = DB::table('issues')
                    ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->select('audit_tests.id','audit_tests.name','audit_tests.description','audit_plans.name as audit_plan','audits.name as audit','audit_programs.name as program')
                    ->groupBy('audit_tests.id','audit_tests.name','audit_tests.description','audit_plans.name','audits.name','audit_programs.name')
                    ->get();
        }

        return $audits;
    }

    public static function getAuditTests($org)
    {
        return DB::table('audit_tests')
            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
            ->where('audit_plans.organization_id','=',$org)
            ->where('audit_plans.status','=',0)
            ->select('audit_tests.id','audit_tests.name')
            ->get();
    }

    public static function getAuditTestByName($name)
    {
        return DB::table('audit_tests')
                ->where('name','=',$name)
                ->select('id')
                ->first();
    }
}
