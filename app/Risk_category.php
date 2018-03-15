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

    public static function getSubcategories()
    {
        return DB::table('risk_categories')
                ->whereNotNull('risk_category_id')
                ->select('id','name')
                ->get();

        //Esto servía para cuando eran sólo 2 niveles (en parque arauco son 3 o 4)
        //return DB::table('risk_categories')
        //        ->whereNotNull('risk_category_id')
        //        ->select('id','name')
        //        ->get();

        //ACT 22-01-18: Hacemos ciclo para determinar (y retornar) último nivel de subcategorías
        //Primer nivel
        /*
        $categories = DB::table('risk_categories')
                ->whereNull('risk_category_id')
                ->select('id')
                ->get();

        foreach ($categories as $cat)
        {
            $subs = DB::table('risk_categories')
                ->where('risk_category_id','=',$cat->id)
                ->select('id','name')
                ->get();
        }*/
    }

    public static function getAllCategories()
    {
        return DB::table('risk_categories')
                ->select('id','name')
                ->get();
    }

    public static function getPrimaryCategories()
    {
        return DB::table('risk_categories')
                ->whereNull('risk_category_id')
                ->select('id','name')
                ->get();
    }

    public static function getRiskCategoryByName($name)
    {
        return DB::table('risk_categories')
                ->where('name','=',$name)
                ->select('id')
                ->first();
    }

    public static function getPrimaryCategory($cat_id)
    {
        return DB::table('risk_categories')
                ->where('id','=',$cat_id)
                ->select('risk_category_id as id')
                ->first();
    }
}
