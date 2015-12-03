<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = ['nombre','fecha_creacion'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function questions()
    {
    	$this->hasMany('Ermtool\Question');
    }
}
