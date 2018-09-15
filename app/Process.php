<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

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
    public static function getProcessFromIssues($org,$kind)
    {
        if ($kind != NULL)
        {
            $processes = DB::table('issues')
                    ->whereNotNull('issues.process_id')
                    ->join('processes','processes.id','=','issues.process_id')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('processes.id','=',$kind)
                    ->select('processes.id','processes.name','processes.description')
                    ->groupBy('processes.id','processes.name','processes.description')
                    ->get();
        }
        
        else
        {
            $processes = DB::table('issues')
                    ->whereNotNull('issues.process_id')
                    ->join('processes','processes.id','=','issues.process_id')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->select('processes.id','processes.name','processes.description')
                    ->groupBy('processes.id','processes.name','processes.description')
                    ->get();
        }

        return $processes;
    }

    public static function getProcesses($org)
    {
        $processes = DB::table('processes')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('processes.status','=',0)
                    ->groupBy('processes.id','processes.name','processes.description')
                    ->select('processes.id','processes.name','processes.description')
                    ->get();
                    
        return $processes;
    }

    public static function getProcessesFromControl($org,$control)
    {
        return DB::table('processes')
                ->join('subprocesses','subprocesses.process_id','=','processes.id')
                ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
                ->join('organization_risk','organization_risk.id','=','risk_subprocess.organization_risk_id')
                ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                ->where('control_organization_risk.control_id','=',$control)
                ->where('organization_risk.organization_id','=',$org)
                ->select('processes.id','processes.name','processes.description')
                ->groupBy('processes.id','processes.name','processes.description')
                ->get();
    }

    public static function getProcessesFromRisk($org_risk_id)
    {
        return DB::table('processes')
            ->join('subprocesses','subprocesses.process_id','=','processes.id')
            ->join('risk_subprocess','risk_subprocess.subprocess_id','=','subprocesses.id')
            ->join('organization_risk','organization_risk.id','=','risk_subprocess.organization_risk_id')
            ->where('organization_risk.id','=',$org_risk_id)
            ->select('processes.id','processes.name','processes.description')
            ->groupBy('processes.id','processes.name','processes.description')
            ->get();
        
    }

    public static function getProcessByName($name)
    {
        return DB::table('processes')
                ->where('name','=',$name)
                ->select('id')
                ->first();
    }

    public static function getProcessFromSubprocess($org,$sub)
    {
        return DB::table('processes')
            ->join('subprocesses','subprocesses.process_id','=','processes.id')
            ->where('subprocesses.id','=',$sub)
            ->select('processes.id','processes.name','processes.description')
            ->first();      
    }

}
