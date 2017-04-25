<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Question extends Model
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
    
    protected $fillable = ['question','answers_type','poll_id'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function polls()
    {
    	return $this->belongsTo('Ermtool\Poll');
    }

    public function posible_answers()
    {
    	return $this->hasMany('Ermtool\Posible_answer');
    }

    public function answers()
    {
        return $this->hasMany('Ermtool\Answer');
    }
}
