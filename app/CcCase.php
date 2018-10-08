<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcCase extends Model
{
    protected $fillable = ['id','cc_complainant_id','cc_kind_id','cc_status_id','password','cc_classification_id'];

    public function ccAnswers()
    {
    	return $this->hasMany('Ermtool\CcAnswer');
    }

    public function ccComplainantMessages()
    {
    	return $this->hasMany('Ermtool\ccComplainantMessage');
    }

    public function ccMessage()
    {
    	return $this->hasMany('Ermtool\ccMessage');
    }
}
