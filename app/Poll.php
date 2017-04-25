<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Poll extends Model
{
    public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }
    
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
