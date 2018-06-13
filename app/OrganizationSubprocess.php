<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class OrganizationSubprocess extends Model
{
    protected $table = 'organization_subprocess';

    protected $fillable = ['organization_id','subprocess_id'];
}
