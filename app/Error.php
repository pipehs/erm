<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    //
    protected $fillable = ['user_id','description','status','status2'];
}
