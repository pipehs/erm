<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Risk_category extends Model
{
    
    protected $fillable = ['name','description','expiration_date','risk_category_id','status'];
    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public static function name($id)
    {
        $res = DB::table('risk_categories')->where('id', $id)->value('name');
        return $res;
    }
    public function risks()
    {
    	return $this->hasMany('Ermtool\Risk');
    }
}
