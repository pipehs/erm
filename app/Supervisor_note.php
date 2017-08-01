<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Supervisor_note extends Model
{
    
    protected $fillable = ['note','status'];
}
