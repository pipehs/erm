<?php

use DB;

Validator::extend('validateUser', function($attribute, $value, $parameters)
{
	$user = DB::table('evaluation_stakeholder')
        			->where('stakeholder_id','=',$value)
        			->select('id');

    return $user;
}); 
?>