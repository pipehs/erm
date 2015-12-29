<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
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
