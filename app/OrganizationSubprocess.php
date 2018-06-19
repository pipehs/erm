<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationSubprocess extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

    protected $table = 'organization_subprocess';

    protected $fillable = ['organization_id','subprocess_id','key_subprocess','stakeholder_id','criticality'];
}
