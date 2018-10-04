<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcKindAnswer extends Model
{
    protected $fillable = ['description'];

    public function ccQuestions()
    {
    	return $this->hasMany('Ermtool\CcQuestion');
    }
}
