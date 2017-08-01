<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Posible_answer extends Model
{
    
    protected $fillable = ['answer','question_id'];
    
    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function questions()
    {
    	return $this->belongsTo('Ermtool\Question');
    }
}
