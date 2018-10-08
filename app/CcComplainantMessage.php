<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class CcComplainantMessage extends Model
{
    protected $fillable = ['cc_case_id','description'];

    public function cases()
    {
    	return $this->belongsTo('Ermtool\CcCase');
    }
}
