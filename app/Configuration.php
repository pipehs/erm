<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = ['option_name','option_value','comments'];
}
