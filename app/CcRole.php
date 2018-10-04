<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcRole extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany('Ermtool\User');
    }
}
