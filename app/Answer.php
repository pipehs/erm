<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['respuesta','question_id','stakeholder_id'];

    public function questions()
    {
    	return $this->belongsTo('Ermtool\Question');
    }
}
