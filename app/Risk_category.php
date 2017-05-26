<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;
use DB;

class Risk_category extends Model
{
	public function getCreatedAtAttribute($date)
    {
        if(Auth::check())
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d H:i:s');
        else
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s.000', $date)->format('Y-m-d H:i:s');
    }
    
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
