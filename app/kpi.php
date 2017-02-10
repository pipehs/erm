<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;

class kpi extends Model
{
    protected $table = 'kpi';
    protected $fillable = ['name','description','calculation_method','periodicity','stakeholder_id','initial_date','initial_value','final_date','goal','measurement_unit'];

    public static function getLastEvaluationPeriod($id)
    {
    	$max_updated = DB::table('kpi_measurements')
                    ->where('kpi_id','=',$id)
                    ->where('status','=',1)
                    ->max('updated_at');

        if ($max_updated)
        {
        	//obtenemos periodicidad
	        $p = DB::table('kpi')
	        			->where('id','=',$id)
	        			->select('periodicity')
	        			->first();

	        if ($p->periodicity == 1) //mensual
	        {
	        	//obtenemos mes y año de ultima evaluación
	        	$date = DB::table('kpi_measurements')
	        				->where('kpi_id','=',$id)
	        				->where('updated_at','=',$max_updated)
	        				->select('month','year')
	        				->first();

	        	$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

	        	$res = $meses[$date->month-1].' del '.$date->year;
	        }
	        else if ($p->periodicity == 2) //semestral
	        {
	        	$date = DB::table('kpi_measurements')
	        				->where('kpi_id','=',$id)
	        				->where('updated_at','=',$max_updated)
	        				->select('semester','year')
	        				->first();

	        	$sem = ['1er Semestre','2do Semestre'];
	        	echo $date;
	        	$res = $sem[($date->semester)-1].' del '.$date->year;
	        }
	        else if ($p->periodicity == 3) //trimestral
	        {
	        	$date = DB::table('kpi_measurements')
	        				->where('kpi_id','=',$id)
	        				->where('updated_at','=',$max_updated)
	        				->select('trimester','year')
	        				->first();

	        	$trim = ['1er Trimestre','2do Trimestre','3er Trimestre','4to Trimestre'];
	        	$res = $trim[$date->trimester-1].' del '.$date->year;
	        }
	        else if ($p->periodicity == 4) //anual
	        {
	        	$date = DB::table('kpi_measurements')
	        				->where('kpi_id','=',$id)
	        				->where('updated_at','=',$max_updated)
	        				->select('year')
	        				->first();

	        	$res = $date->year;
	        }

	        return $res;
        }
        else
        {
        	return FALSE;
        }
    }

    public static function getFinalDate($id)
    {
    	return DB::table('kpi')
    			->where('id','=',$id)
    			->select('final_date')
    			->first();
    }

    
}
