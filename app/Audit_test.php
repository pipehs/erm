<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit_test extends Model
{
    protected $fillable = ['name','description','type','status','results','hh'];


    public static function getTestNameById($id)
    {
    	return DB::table('audit_tests')->where('id',$id)->value('name');
    }
}
