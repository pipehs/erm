<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Eval_description extends Model
{
    
    protected $table = 'eval_description';

    protected $fillable = ['value','kind','name_es','name_eng','description_es','description_eng','color'];


    public static function getImpactValues($lang)
    {
    	if ($lang == 1)
    	{
    		return DB::table('eval_description')
    			->where('kind','=',2)
    			->select('name_es as name','description_es as description','value','color')
    			->orderBy('value','desc')
    			->get();
    	}
    	else if ($lang == 2)
    	{
    		return DB::table('eval_description')
    			->where('kind','=',2)
    			->select('name_eng as name','description_eng as description','value','color')
    			->orderBy('value','desc')
    			->get();
    	}
    	
    }

    public static function getProbabilityValues($lang)
    {
    	if ($lang == 1)
    	{
    		return DB::table('eval_description')
    			->where('kind','=',1)
    			->select('name_es as name','description_es as description','value','color')
    			->orderBy('value','desc')
    			->get();
    	}
    	else if ($lang == 2)
    	{
    		return DB::table('eval_description')
    			->where('kind','=',1)
    			->select('name_eng as name','description_eng as description','value','color')
    			->orderBy('value','desc')
    			->get();
    	}
    }
}
