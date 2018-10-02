<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class CcStatus extends Model
{
	protected $table = 'cc_status';
	
    protected $fillable = ['description','cc_kind_id'];

    public function ccKinds()
    {
    	return $this->belongsTo('\Ermtool\CcKind');
    }
}
