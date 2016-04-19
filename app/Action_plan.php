<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Action_plan extends Model
{
    //
    protected $fillable = ['description','final_date','status'];
}
