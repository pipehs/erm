<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Subprocess extends Model
{
    
	protected $fillable = ['name','description','expiration_date','process_id','subprocess_id','status'];

	//eliminamos created_at y updated_at
    //public $timestamps = false;
    
    public function organizations()
    {
    	return $this->belongsToMany('Ermtool\Organization');
    }

    public function processes()
    {
    	return $this->belongsTo('Ermtool\Process');
    }

    public function risks()
    {
        return $this->belongsToMany('Ermtool\Risk');
    }

}
