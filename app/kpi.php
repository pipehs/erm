<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class kpi extends Model
{
    protected $table = 'kpi';
    protected $fillable = ['name','description','calculation_method','periodicity','stakeholder_id','initial_date','initial_value','final_date','goal'];
}
