<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Organization extends Model
{
    protected $fillable = ['name','description','expiration_date','shared_services','organization_id','status','mision','vision','target_client'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public static function name($organization_id)
    {
    	$res = DB::table('organizations')->where('id', $organization_id)->value('name');
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
        return $this->belongsToMany('Ermtool\Stakeholder');
    }
}
