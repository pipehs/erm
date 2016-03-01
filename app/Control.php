<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
     protected $fillable = ['name','description','expiration_date','type','type2','evidence','periodicity','purpose','stakeholder_id','expected_cost'];

    public function stakeholders()
    {
    	return $this->belongsTo('Ermtool\Stakeholder');
    }
}
