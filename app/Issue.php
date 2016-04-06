<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = ['name','observations','recommendations','evidence','classification'];
}
