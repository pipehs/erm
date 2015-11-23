<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','process_id','estado'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function subprocesses()
    {
    	return $this->hasMany('Ermtool\Subprocess');
    }

}
