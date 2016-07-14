<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    protected $fillable = ['name','description','expiration_date','organization_id','objective_category_id','status','perspective'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;


    public function risks()
    {
    	return $this->belongsToMany('\Ermtool\Risk');
    }
}
