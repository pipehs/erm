<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class AuditAuditor extends Model
{
    protected $fillable = ['audit_audit_plan_id','stakeholder_id','kind'];

    public static function getAuditors($id,$kind)
    {
    	return DB::table('audit_auditors')
    		->join('stakeholders','stakeholders.id','=','audit_auditors.stakeholder_id')
    		->where('audit_audit_plan_id','=',$id)
			->where('kind','=',$kind)
			->select('stakeholders.id','stakeholders.name','stakeholders.surnames')
			->get();
    }
}
