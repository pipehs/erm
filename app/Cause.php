<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Cause extends Model
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
    
    protected $fillable = ['name','description','status'];

    public function risks()
    {
    	return $this->belongsToMany('Ermtool\Risk');
    }

    public static function getCausesFromRisk($risk_id)
    {
        return DB::table('causes')
                ->join('cause_risk','cause_risk.cause_id','=','causes.id')
                ->where('cause_risk.risk_id','=',$risk_id)
                ->select('causes.name','causes.description')
                ->get();
    }
}
