<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','organization_id','objectives_category_id'];

    //eliminamos created_at y updated_at
    public $timestamps = false;


    public function risks()
    {
    	return $this->belongsToMany('\Ermtool\Risk');
    }
}
