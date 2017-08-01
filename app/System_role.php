<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class System_role extends Model
{
    
    protected $table = 'system_roles';

 	protected $fillable = ['role'];


 	public function users()
    {
        return $this->belongsToMany('Ermtool\User');
    }
}
