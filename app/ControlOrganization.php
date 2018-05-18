<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class ControlOrganization extends Model
{
    protected $table = 'control_organization';
    //ACT 28-03-18: Pruebas dinámicas (obtenidas de tabla evaluation_tests)
    protected $fillable = ['control_id','organization_id','stakeholder_id','evidence','comments','cont_percentage'];

    public static function getByCO($control_id,$org_id)
    {
    	return \Ermtool\ControlOrganization::where('control_id',$control_id)->where('organization_id',$org_id)->first();
    }
}
