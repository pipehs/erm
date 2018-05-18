<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Risk_response extends Model
{
    protected $fillable = ['name','description'];

    public static function getByOrgRisk($org_risk_id)
    {
    	return DB::table('risk_responses')
    		->join('organization_risk','organization_risk.risk_response_id','=','risk_responses.id')
    		->where('organization_risk.id','=',$org_risk_id)
    		->select('risk_responses.id','risk_responses.name')
    		->first();
    }
}
