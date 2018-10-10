<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CcQuestion extends Model
{
    //protected $table = 'cc_questions';
    use SoftDeletes;

    protected $fillable = ['cc_kind_answer_id','question','required'];
    protected $dates = ['deleted_at'];

    public function ccKindAnswers()
    {
    	return $this->belongsTo('Ermtool\CcKindAnswer');
    }

    public function ccPossibleAnswers()
    {
    	return $this->hasMany('Ermtool\CcPossibleAnswer');
    }
}
