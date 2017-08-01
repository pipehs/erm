<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Answer extends Model
{

    protected $fillable = ['answer','question_id','stakeholder_id'];

    public function questions()
    {
    	return $this->belongsTo('Ermtool\Question');
    }
}
