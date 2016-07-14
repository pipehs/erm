<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class KRI extends Model
{
    protected $table = 'kri';
    protected $fillable = ['name','description','type','green_min','green_max','description_green','yellow_min',
    					   'yellow_max','description_yellow','red_min','red_max','description_red','uni_med',
    					   'kri_last_evaluation','date_evaluation'];
}
