<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    protected $fillable = ['name','description','status'];

    public function risks()
    {
    	return $this->belongsToMany('Ermtool\Risk');
    }
}
