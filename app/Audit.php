<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class Audit extends Model
{
    protected $fillable = ['name','description'];

    public static function name($audit_id)
    {
    	$res = DB::table('audits')
    			->join('audit_audit_plan','audit_audit_plan.audit_id','=','audits.id')
    			->where('audit_audit_plan.id','=',$audit_id)
    			->select('audits.name')
    			->first();

    	return $res->name;
    }
}
