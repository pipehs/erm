<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class CcKind extends Model
{
    protected $fillable = ['name','responsable_mail'];

    public function ccStatus()
    {
    	return $this->hasMany('Ermtool\CcStatus');
    }

    public function ccClassifications()
    {
    	return $this->hasMany('Ermtool\CcClassification');
    }
}
