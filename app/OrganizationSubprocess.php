<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use stdClass;
use Auth;
use Carbon;

class OrganizationSubprocess extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $table = 'organization_subprocess';

    protected $fillable = ['organization_id','subprocess_id','key_subprocess','stakeholder_id','criticality'];

    public static function getByOrgSub($org,$sub)
    {
    	return DB::table('organization_subprocess')
    			->where('organization_id','=',$org)
    			->where('subprocess_id','=',$sub)
    			->first(['id']);
    }
}
