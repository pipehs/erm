<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Note extends Model
{
    
    protected $fillable = ['name','description','status','results','hh','user_id','stakeholder_id'];

    public static function getNotes($org,$audit_plan)
    {
    	$notes = DB::table('notes')
    		->join('audit_tests','audit_tests.id','=','notes.audit_test_id')
    		->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
    		->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
    		->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
    		->where('audit_plans.organization_id','=',$org)
    		->where('audit_plans.id','=',$audit_plan)
    		->select('notes.id','notes.name','notes.description')
    		->get();

    	return $notes;
    }

    public function notes_answers()
    {
    	return $this->hasMany('Ermtool\Notes_answer');
    }
}
