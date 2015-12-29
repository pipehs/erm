<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['name','description','expiration_date','process_id','status'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function subprocesses()
    {
    	return $this->hasMany('Ermtool\Subprocess');
    }

}
