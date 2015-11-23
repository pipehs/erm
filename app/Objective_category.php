<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Objective_category extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','estado'];
    //eliminamos created_at y updated_at
    public $timestamps = false;
}
