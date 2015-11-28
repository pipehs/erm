<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
   	protected $fillable = ['nombre','tipo','tipo2','descripcion','fecha_creacion','fecha_exp','criticidad','probabilidad',
                            'estado','risk_category_id','cause_id','effect_id'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public function causes()
    {
    	return $this->belongsTo('Ermtool\Cause');
    }

    public function effects()
    {
    	return $this->belongsTo('Ermtool\Effect');
    }

    public function risk_categories()
    {
    	return $this->belongsTo('Ermtool\Risk_category');
    }
    public function subprocesses()
    {
        return $this->belongsToMany('Ermtool\Subprocess');
    }
    public function objectives()
    {
        return $this->belongsToMany('Ermtool\Objective');
    }

    public function evaluations()
    {
        return $this->belongsToMany('Ermtool\Evaluation');
    }
}
