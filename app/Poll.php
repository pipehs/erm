<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['name','expiration_date'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function questions()
    {
    	return $this->hasMany('Ermtool\Question');
    }

    public function answers()
    {
    	return $this->hasManyThrough('Ermtool\Answer','Ermtool\Question');
    }

    public function stakeholders()
    {
        return $this->belongsToMany('Ermtool\Stakeholder');
    }
}
