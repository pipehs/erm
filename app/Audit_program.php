<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit_program extends Model
{
    protected $fillable = ['name','description','expiration_date'];

    public static function name($program_id)
    {
        return DB::table('audit_programs')
            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
            ->where('audit_audit_plan_audit_program.id','=',$program_id)
            ->value('name');

    }

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

    public static function getAuditProgramFromAudit($org,$audit_id)
    {
        return DB::table('audit_programs')
            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
            ->where('audit_plans.organization_id','=',$org)
            ->where('audit_audit_plan.audit_id','=',$audit_id)
            ->select('audit_programs.id','audit_programs.name','audit_programs.description')
            ->get();
    }

    public static function getProgramsFromIssues($org)
    {
        //obtenemos programas que tienen issues
        $programs = DB::table('issues')
                    ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','issues.audit_audit_plan_audit_program_id')
                    ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                    ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                    ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                    ->where('audit_plans.organization_id','=',$org)
                    ->select('audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description')
                    ->groupBy('audit_audit_plan_audit_program.id')
                    ->get();

        return $programs;
    }

    public static function getPrograms($org,$audit_plan)
    {
        //obs: Vemos plan y organización sólo para asegurarnos, ya que un plan siempre debería pertenecer sólo a una organización (al menos ahora)
        $programs = DB::table('audit_audit_plan_audit_program')
                        ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                        ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                        ->join('audit_programs','audit_programs.id','=','audit_audit_plan_audit_program.audit_program_id')
                        ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                        ->where('audit_plans.id','=',$audit_plan)
                        ->where('audit_plans.organization_id','=',$org)
                        ->select('audits.name as audit','audit_audit_plan_audit_program.id','audit_programs.name','audit_programs.description')
                        ->groupBy('audit_audit_plan_audit_program.id')
                        ->get();

        return $programs;
    }

    public static function getAudits($org,$audit_program_id)
    {

        //QUEDE AQUI!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! CORREGIR ESTA FUNCIÓN
        return DB::table('audit_programs')
                ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.audit_program_id','=','audit_programs.id')
                ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
                ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
                ->join('audits','audits.id','=','audit_audit_plan.audit_id')
                ->where('audit_audit_plan_audit_program.id','=',$audit_program_id)
                ->where('audit_plans.organization_id','=',$org)
                ->select('audit_plans.name as audit_plan','audits.name as audit')
                ->get();
    }
}
