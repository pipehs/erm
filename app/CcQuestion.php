<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcQuestion extends Model
{
    //protected $table = 'cc_questions';

    protected $fillable = ['cc_kind_answer_id','question','required'];

    public function ccKindAnswers()
    {
    	return $this->belongsTo('Ermtool\CcKindAnswer');
    }

    public function ccPossibleAnswers()
    {
    	return $this->hasMany('Ermtool\CcPossibleAnswer');
    }
}
