<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class CcMessage extends Model
{
    protected $fillable = ['cc_case_id','cc_user_id','cc_message_id','description'];

    public function cases()
    {
    	return $this->belongsTo('Ermtool\CcCase');
    }
}
