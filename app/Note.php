<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['name','description','status','results','hh'];
}
