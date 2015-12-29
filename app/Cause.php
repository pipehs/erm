<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    protected $fillable = ['name','description','status'];

    public function risks()
    {
    	return $this->hasMany('Ermtool\Risk');
    }
}
