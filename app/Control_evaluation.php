<?php

namespace Ermtool;
use DB;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Control_evaluation extends Model
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
    
    protected $table = 'control_evaluation';

    protected $fillable = ['control_id','kind','results','comments','status','issue_id','description'];


    public static function getLastEvaluation($control_id,$kind)
    {
    	$last_updated = DB::table('control_evaluation')
    					->where('control_id','=',$control_id)
    					->where('kind','=',$kind)
    					->max('updated_at');

    	if (!empty($last_updated))
    	{
            //ACTUALIZACIÓN 03-04-17: Para SQL Server: Damos formato de Ymd a last_updated
            $last_updated = str_replace('-','',$last_updated);
    		//ahora obtenemos los datos de esta evaluación
    		$last_eval1 = DB::table('control_evaluation')
    					->where('control_id','=',$control_id)
    					->where('kind','=',$kind)
    					->where('updated_at','=',$last_updated)
    					->select('id','control_id','kind','results','updated_at','comments','status','description')
    					->first();

    		$updated_at = new DateTime($last_eval1->updated_at);
            $updated_at = date_format($updated_at, 'd-m-Y');

    		//obtenemos (si es que el resultado es inefectivo) hallazgos
    		if ($last_eval1->results == 2)
    		{
    			$issues = DB::table('issues')
    						->where('control_evaluation_id','=',$last_eval1->id)
    						->select('name','description','classification','recommendations')
    						->get();

    			$last_eval = [
    					'id' => $last_eval1->id,
    					'control_id' => $last_eval1->control_id,
    					'kind' => $last_eval1->kind,
                        'description' => $last_eval1->description,
    					'results' => $last_eval1->results,
    					'updated_at' => $updated_at,
    					'comments' => $last_eval1->comments,
    					'status' => $last_eval1->status,
    					'issues' => $issues,
    				];
    		}
    		else
    		{
    			$last_eval = [
    					'id' => $last_eval1->id,
    					'control_id' => $last_eval1->control_id,
    					'kind' => $last_eval1->kind,
                        'description' => $last_eval1->description,
    					'results' => $last_eval1->results,
    					'updated_at' => $updated_at,
    					'comments' => $last_eval1->comments,
    					'status' => $last_eval1->status,
    					'issues' => NULL,
    				];
    		}

    	}
    	else
    	{
    		$last_eval = NULL;
    	}

    	return $last_eval;

    }

    public static function insertControlledRisk($risk,$result,$kind)
    {
        if ($kind == 1) //es riesgo de proceso
        {
            DB::table('controlled_risk')
                ->insert([
                    'organization_risk_id' => $risk,
                    'results' => $result,
                    'created_at' => date('Ymd H:i:s')
                    ]);
        }
        else //riesgo de entidad
        {
            DB::table('controlled_risk')
                ->insert([
                    'organization_risk_id' => $risk,
                    'results' => $result,
                    'created_at' => date('Ymd H:i:s')
                    ]);
        }
    }

}
