<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class Risk_category extends Model
{
    protected $fillable = ['name','description','expiration_date','risk_category_id','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function risks()
    {
    	return $this->hasMany('Ermtool\Risk');
    }
}
