<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Subprocess extends Model
{
    
	protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','process_id','subprocess_id','estado'];

	//eliminamos created_at y updated_at
    public $timestamps = false;
    
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
