<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function risks()
    {
    	return $this->hasMany('Ermtool\Risk');
    }
}
