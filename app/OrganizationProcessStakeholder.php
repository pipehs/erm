<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationProcessStakeholder extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];
	
    protected $table = 'organization_process_stakeholder';

    protected $fillable = ['organization_id','process_id','stakeholder_id'];
}
