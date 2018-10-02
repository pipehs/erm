<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcClassification extends Model
{
    protected $fillable = ['name','description','cc_kind_id'];
}
