<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcQuestion extends Model
{
    //protected $table = 'cc_questions';

    protected $fillable = ['cc_kind_answer_id','description'];
}
