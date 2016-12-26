<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Process extends Model
{
    protected $fillable = ['name','description','expiration_date','process_id','status'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function subprocesses()
    {
    	return $this->hasMany('Ermtool\Subprocess');
    }

    //obtiene procesos que tienen issues(de una organización)
    public static function getProcessFromIssues($org)
    {
        $processes = DB::table('issues')
                    ->whereNotNull('issues.process_id')
                    ->join('processes','processes.id','=','issues.process_id')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->select('processes.id','processes.name','processes.description')
                    ->groupBy('processes.id')
                    ->get();

        return $processes;
    }

    public static function getProcesses($org)
    {
        $processes = DB::table('processes')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('processes.status','=',0)
                    ->groupBy('processes.id')
                    ->select('processes.id','processes.name')
                    ->get();
                    
        return $processes;
    }

    public static function getProcessesFromControl($org,$control)
    {
        return DB::table('processes')
                ->join('subprocesses','subprocesses.process_id','=','processes.id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                ->join('control_risk_subprocess','control_risk_subprocess.risk_subprocess_id','=','risk_subprocess.id')
                ->where('control_risk_subprocess.control_id','=',$control)
                ->where('organization_subprocess.organization_id','=',$org)
                ->select('processes.id','processes.name','processes.description')
                ->groupBy('processes.id')
                ->get();
    }

}
