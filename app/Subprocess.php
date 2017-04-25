<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Subprocess extends Model
{
    public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }

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
    public static function getSubprocessFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            //primero hallazgos de subproceso creados directamente
            $subprocesses1 = DB::table('issues')
                            ->whereNotNull('issues.subprocess_id')
                            ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                            ->join('processes','processes.id','=','subprocesses.process_id')
                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('organization_subprocess.organization_id','=',$org)
                            ->where('subprocesses.id','=',$kind)
                            ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                            ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                            ->get();

            //hallazgos obtenidos a través de la evaluación de controles
            $subprocesses2 = DB::table('control_evaluation')
                            ->join('controls','controls.id','=','control_evaluation.control_id')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('processes','processes.id','=','subprocesses.process_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->where('subprocesses.id','=',$kind)
                            ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                            ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                            ->get();

            //hallazgos generados a través de auditoría orientada a procesos
            $subprocesses3 = DB::table('issues')
                            ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                            ->join('processes','processes.id','=','audit_tests.process_id')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('organization_subprocess.organization_id','=',$org)
                            ->where('subprocesses.id','=',$kind)
                            ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                            ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                            ->get();
        }
        else
        {
            $subprocesses1 = DB::table('issues')
                        ->whereNotNull('issues.subprocess_id')
                        ->join('subprocesses','subprocesses.id','=','issues.subprocess_id')
                        ->join('processes','processes.id','=','subprocesses.process_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org)
                        ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                        ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                        ->get();

            //hallazgos obtenidos a través de la evaluación de controles
            $subprocesses2 = DB::table('control_evaluation')
                            ->join('controls','controls.id','=','control_evaluation.control_id')
                            ->join('control_organization_risk','control_organization_risk.control_id','=','controls.id')
                            ->join('organization_risk','organization_risk.id','=','control_organization_risk.organization_risk_id')
                            ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                            ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                            ->join('processes','processes.id','=','subprocesses.process_id')
                            ->join('issues','issues.control_evaluation_id','=','control_evaluation.id')
                            ->where('organization_risk.organization_id','=',$org)
                            ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                            ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                            ->get();

            //hallazgos generados a través de auditoría orientada a procesos
            $subprocesses3 = DB::table('issues')
                            ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
                            ->join('processes','processes.id','=','audit_tests.process_id')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                            ->where('organization_subprocess.organization_id','=',$org)
                            ->select('subprocesses.id','subprocesses.name','subprocesses.description','processes.name as process_name')
                            ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description','processes.name')
                            ->get();
        }

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

    public static function getProcess($id)
    {
        return DB::table('processes')
                ->join('subprocesses','subprocesses.process_id','=','processes.id')
                ->select('processes.id','processes.name','processes.description')
                ->first();
    }

    public static function getSubprocessesFromOrgRisk($risk,$org)
    {
        return DB::table('subprocesses')
            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
            ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
            ->where('organization_subprocess.organization_id','=',$org)
            ->where('risk_subprocess.risk_id','=',$risk)
            ->select('subprocesses.name','subprocesses.description')
            ->get();
    }

    public static function getSubprocessesFromControl($org,$control)
    {
        return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->join('organization_subprocess','organization_subprocess.organization_id','=','organization_risk.organization_id')
                ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','organization_risk.risk_id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->where('control_organization_risk.control_id','=',$control)
                ->where('organization_subprocess.organization_id','=',$org)
                ->select('subprocesses.id','subprocesses.name','subprocesses.description')
                ->groupBy('subprocesses.id','subprocesses.name','subprocesses.description')
                ->get();
    }
}
