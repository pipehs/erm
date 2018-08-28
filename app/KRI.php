<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class KRI extends Model
{
    
    protected $table = 'kri';
    protected $fillable = ['name','description','type','min_max','green_min','interval_min','interval_max','red_max','description_green','description_yellow','description_red','uni_med','kri_last_evaluation','date_evaluation','risk_id','stakeholder_id','periodicity'];
}
