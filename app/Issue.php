<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = ['name','description','observations','recommendations','evidence','classification',
    						'audit_test_id','audit_audit_plan_id','control_evaluation_id'];
}
