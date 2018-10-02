<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcAnswer extends Model
{
    protected $fillable = ['cc_case_id','cc_question_id','description'];
}
