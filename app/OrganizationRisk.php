<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use Carbon;


class OrganizationRisk extends Model
{
    use SoftDeletes;

    protected $table = 'organization_risk';
    protected $fillable = ['risk_id','organization_id','stakeholder_id','comments','risk_response_id'];
    protected $dates = ['deleted_at'];

    public static function getByOrgRisk($risk_id,$org_id)
    {
    	return DB::table('organization_risk')
            ->whereNull('organization_risk.deleted_at')
    		->where('risk_id','=',$risk_id)
    		->where('organization_id','=',$org_id)
    		->first(['id']);
    }

    public static function getByRisk($risk_id)
    {
        return DB::table('organization_risk')
            ->where('risk_id','=',$risk_id)
            ->select('id')
            ->get();
    }
}
