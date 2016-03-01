<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function stakeholders()
    {
    	return $this->belongsToMany('Ermtool\Stakeholder');
    }
}
