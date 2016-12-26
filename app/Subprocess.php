<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Subprocess extends Model
{
    
	protected $fillable = ['name','description','expiration_date','process_id','subprocess_id','status'];

	//eliminamos created_at y updated_at
    //public $timestamps = false;
    
    public function organizations()
    {
    	return $this->belongsToMany('Ermtool\Organization');
    }

    public function processes()
    {
    	return $this->belongsTo('Ermtool\Process');
    }

    public function risks()
    {
        return $this->belongsToMany('Ermtool\Risk');
    }

    public static function getSubprocesses($org)
    {
        $subprocesses = DB::table('subprocesses')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('subprocesses.status','=',0)
                    ->select('subprocesses.id','subprocesses.name')
                    ->distinct('subprocesses.id')
                    ->get();

        return $subprocesses;
    }

    //obtiene subprocesos que tienen issues(de una organización)
    public static function getSubprocessFromIssues($org)
    {
        //primero hallazgos de subproceso creados directamente
        $subprocesses1 = DB::table('issues')
                        ->whereNotNull('issues.subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                        ->groupBy('subprocesses.id')
                        ->get();

        //hallazgos obtenidos a través de la evaluación de controles
        $subprocesses2 = DB::table('control_evaluation')
                        ->join('controls','controls.id','=','control_evaluation.control_id')
                        ->join('control_risk_subprocess','control_risk_subprocess.control_id','=','controls.id')
                        ->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                        ->groupBy('subprocesses.id')
                        ->get();

        //hallazgos generados a través de auditoría orientada a procesos
        $subprocesses3 = DB::table('issues')
                        ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                        ->join('processes','processes.id','=','audit_tests.process_id')
                        ->join('subprocesses','subprocesses.process_id','=','processes.id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                        ->groupBy('subprocesses.id')
                        ->get();

        $subprocesses = array_merge($subprocesses1,$subprocesses2,$subprocesses3);
        //eliminamos duplicados (si es que hay)
        $subprocessesX = array_unique($subprocesses,SORT_REGULAR);
        return $subprocessesX;
    }

    public static function getSubprocessesFromProcess($org,$process)
    {
        $subprocesses = DB::table('subprocesses')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('subprocesses.process_id','=',$process)
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->where('subprocesses.status','=',0)
                        ->select('subprocesses.id','subprocesses.name')
                        ->distinct('subprocesses.id')
                        ->get();

        return $subprocesses;
    }

    public static function getSubprocessesFromControl($org,$control)
    {
        return DB::table('subprocesses')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                ->where('control_risk_subprocess.control_id','=',$control)
                ->where('organization_subprocess.organization_id','=',$org)
                ->select('subprocesses.id','subprocesses.name','subprocesses.description')
                ->groupBy('subprocesses.id')
                ->get();

    }

    public static function getProcess($id)
    {
        return DB::table('processes')
                ->join('subprocesses','subprocesses.process_id','=','processes.id')
                ->select('processes.id','processes.name','processes.description')
                ->first();
    }

}
