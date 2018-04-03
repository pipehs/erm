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
        //Esto servía para cuando eran sólo 2 niveles (en parque arauco son 3 o 4) 
        //return DB::table('risk_categories')
        //        ->whereNotNull('risk_category_id')
        //        ->select('id','name')
        //        ->get();

        $categories = array();
        $i = 0;
        //ACT 22-01-18: Hacemos ciclo para determinar (y retornar) último nivel de subcategorías
        //Primer nivel
        $cats = DB::table('risk_categories')
                ->whereNull('risk_category_id')
                ->select('id','name')
                ->get();

        foreach ($cats as $cat)
        {
            //2do nivel
            $subs = DB::table('risk_categories')
                ->where('risk_category_id','=',$cat->id)
                ->select('id','name')
                ->get();

            //vemos si esta categoría tiene subcategorías
            if (!empty($subs))
            {
                foreach ($subs as $sub)
                {
                    //3er nivel
                    $subs2 = DB::table('risk_categories')
                    ->where('risk_category_id','=',$sub->id)
                    ->select('id','name')
                    ->get();

                    if (!empty($subs2))
                    {
                        foreach ($subs2 as $sub2)
                        {
                            //4er nivel
                            $subs3 = DB::table('risk_categories')
                            ->where('risk_category_id','=',$sub2->id)
                            ->select('id','name')
                            ->get();

                            if (!empty($subs3))
                            {
                                foreach ($subs3 as $sub3)
                                {
                                    //5er nivel
                                    $subs4 = DB::table('risk_categories')
                                    ->where('risk_category_id','=',$sub3->id)
                                    ->select('id','name')
                                    ->get();
                                    
                                    if (!empty($subs4))
                                    {
                                        foreach ($subs4 as $sub4)
                                        {
                                            $categories[$i] = ['id' => $sub4->id, 'name' => $sub4->name,'level' => 5];
                                            $i += 1;
                                        }
                                    }
                                    else
                                    {
                                        $categories[$i] = ['id' => $sub3->id, 'name' => $sub3->name,'level' => 4];
                                        $i += 1;
                                    }
                                }
                            }
                            else
                            {
                                $categories[$i] = ['id' => $sub2->id, 'name' => $sub2->name,'level' => 3];
                                $i += 1;
                            }
                        }
                    }
                    else
                    {
                        $categories[$i] = ['id' => $sub->id, 'name' => $sub->name,'level' => 2];
                        $i += 1;
                    }
                }
            }
            else //este es el último nivel en esta rama de categoría
            {
                $categories[$i] = ['id' => $cat->id, 'name' => $cat->name,'level' => 1];
                $i += 1;
            }   
        }

        return $categories;
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
