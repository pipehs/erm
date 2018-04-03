<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Evaluation_test extends Model
{
    protected $table = 'evaluation_tests';

    protected $fillable = ['name','name_eng'];

    public static function name($id)
    {
    	return $res = DB::table('evaluation_tests')->where('id', $id)
    			->first(['name','name_eng']);
    }
}
