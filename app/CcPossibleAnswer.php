<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcPossibleAnswer extends Model
{
    //
    protected $fillable = ['cc_question_id','description'];

    public function ccQuestions()
    {
    	return $this->belongsTo('Ermtool\CcQuestion');
    }
}
