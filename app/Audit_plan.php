<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Audit_plan extends Model
{
    protected $fillable = ['name','description','objectives','scopes','status','resources','methodology','initial_date','final_date','rules'];

    public function stakeholders()
    {
    	return $this->belongsToMany('\Ermtool\Stakeholder');
    }
}
