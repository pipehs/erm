<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Notes_answer extends Model
{
    
	protected $table = 'notes_answers';
    protected $fillable = ['answer','note_id'];

    public function notes_answers()
    {
    	return $this->belongsTo('Ermtool\Note');
    }
}
