<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Posible_answer extends Model
{
    protected $fillable = ['respuesta','question_id'];
    
    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function questions()
    {
    	return $this->belongsTo('Ermtool\Question');
    }
}
