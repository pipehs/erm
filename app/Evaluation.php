<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['name','description','expiration_date','max_levels'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function risks()
    {
    	return $this->belongsToMany('\Ermtool\Risk');
    }
/*
    public function stakeholders()
    {
    	return $this->hasManyThrough('\Ermtool\Stakeholder','evaluation_risk');
    }
*/
}
