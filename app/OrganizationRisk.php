<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationRisk extends Model
{
    use SoftDeletes;

    protected $table = 'organization_risk';
    protected $fillable = ['risk_id','organization_id','stakeholder_id','comments','risk_response_id'];
    protected $dates = ['deleted_at'];
}
