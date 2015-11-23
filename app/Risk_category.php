<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Risk_category extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','risk_category_id','estado'];
    //eliminamos created_at y updated_at
    public $timestamps = false;
}
