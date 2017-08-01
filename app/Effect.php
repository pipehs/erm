<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Effect extends Model
{
    
    protected $fillable = ['name','description','status'];

    public function risks()
    {
    	return $this->belongsToMany('Ermtool\Risk');
    }

    public static function getEffectsFromRisk($risk_id)
    {
        return DB::table('effect_risk')
                ->join('effects','effects.id','=','effect_risk.effect_id')
                ->where('effect_risk.risk_id','=',$risk_id)
                ->select('effects.name','effects.description')
                ->get();
    }

    public static function name($id)
    {
        return DB::table('effects')->where('id',$id)->value('name');
    }
}
