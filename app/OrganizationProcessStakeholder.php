<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use stdClass;
use Auth;
use Carbon;

class OrganizationProcessStakeholder extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];
	
    protected $table = 'organization_process_stakeholder';

    protected $fillable = ['organization_id','process_id','stakeholder_id','criticality'];

    public static function getByOrgProcess($org,$prc)
    {
    	return DB::table('organization_process_stakeholder')
    			->where('organization_id','=',$org)
    			->where('process_id','=',$prc)
    			->first(['id']);
    }
}
