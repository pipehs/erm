<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Financial_statement extends Model
{
    protected $fillable = ['name','status'];

    public static function getFSByControl($control_id)
    {
    	return DB::table('control_financial_statement')
    			->join('financial_statements','financial_statements.id','=','control_financial_statement.financial_statement_id')
    			->where('control_financial_statement.control_id','=',$control_id)
    			->select('financial_statements.*','control_financial_statement.id as control_financial_statement_id')
    			->get();
    }

    public static function getFSControl($fs_id,$control_id)
    {
    	return DB::table('control_financial_statement')
    			->where('control_financial_statement.control_id','=',$control_id)
    			->where('control_financial_statement.financial_statement_id','=',$fs_id)
    			->select('id')
    			->first();
    }
}
