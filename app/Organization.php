<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Organization extends Model
{
    protected $fillable = ['nombre','descripcion','fecha_creacion','fecha_exp','serv_compartidos','organization_id'];

    //eliminamos created_at y updated_at
    public $timestamps = false;

    public static function nombre($organization_id)
    {
    	$res = DB::table('organizations')->where('id', $organization_id)->value('nombre');
    	return $res;
    }

    public function processes()
    {
    	return $this->hasManyThrough('Ermtool\Process', 'Ermtool\Subprocess');
    }

    public function subprocesses()
    {
    	return $this->belongsToMany('Ermtool\Subprocess');
    }

    public function stakeholders()
    {
        return $this->hasMany('Ermtool\Stakeholder');
    }
}
