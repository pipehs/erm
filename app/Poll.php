<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Poll extends Model
{
    
    protected $fillable = ['name','expiration_date'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function questions()
    {
    	return $this->hasMany('Ermtool\Question');
    }

    public function answers()
    {
    	return $this->hasManyThrough('Ermtool\Answer','Ermtool\Question');
    }

    public function stakeholders()
    {
        return $this->belongsToMany('Ermtool\Stakeholder');
    }

    public static function getPollByQuestion($question_id)
    {
        return DB::table('polls')
                ->join('questions','questions.poll_id','=','polls.id')
                ->where('questions.id','=',$question_id)
                ->select('polls.id','polls.name')
                ->first();
    }
}
