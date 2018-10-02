<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class CcCase extends Model
{
    protected $fillable = ['id','cc_complainant_id','cc_kind_id','cc_status_id','password','cc_classification_id'];
}
