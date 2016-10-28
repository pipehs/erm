<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit_program extends Model
{
    protected $fillable = ['name','description','expiration_date'];


    public static function getProgramsByAudit($audit_audit_plan_id)
    {
    	$programs = DB::table('audit_audit_plan_audit_program')
            		->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
            		->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->where('audit_audit_plan.id','=',$audit_audit_plan_id)
            		->select('audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description',
                             'audit_audit_plan_audit_program.created_at','audit_audit_plan_audit_program.updated_at',
                             'audit_audit_plan_audit_program.expiration_date','audit_audit_plan_audit_program.audit_audit_plan_id')
            		->get();

        return $programs;
    }
}
