<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Objective_category extends Model
{
    
    protected $fillable = ['name','description','expiration_date','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;
}
