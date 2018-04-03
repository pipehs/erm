<?php

namespace Ermtool;
use DB;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon;

class Control_evaluation extends Model
{
    
    protected $table = 'control_evaluation';
    //ACT 28-03-18: Pruebas dinámicas (obtenidas de tabla evaluation_tests)
    protected $fillable = ['control_id','evaluation_test_id','results','comments','status','issue_id','description','organization_id'];


    public static function getLastEvaluation($control_id,$kind)
    {   
        //ACT 28-03-18: Pruebas dinámicas (obtenidas de tabla evaluation_tests)
    	$last_updated = DB::table('control_evaluation')
    					->where('control_id','=',$control_id)
    					//->where('kind','=',$kind)
                        ->where('evaluation_test_id','=',$kind)
    					->max('updated_at');

        //ACT 28-03-18: Obtenemos datos generales de prueba
        $name = \Ermtool\Evaluation_test::name($kind);

    	if (!empty($last_updated))
    	{
            //ACTUALIZACIÓN 03-04-17: Para SQL Server: Damos formato de Ymd a last_updated
            //$last_updated = str_replace('-','',$last_updated);
    		//ahora obtenemos los datos de esta evaluación
    		$last_eval1 = DB::table('control_evaluation')
    					->where('control_id','=',$control_id)
    					//->where('kind','=',$kind)
                        ->where('evaluation_test_id','=',$kind)
    					->where('updated_at','=',$last_updated)
    					->select('id','control_id','evaluation_test_id','results','updated_at','comments','status','description')
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
                        'name' => $name->name,
    					//'kind' => $last_eval1->kind,
                        'evaluation_test_id' => $last_eval1->evaluation_test_id,
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
                        'name' => $name->name,
    					//'kind' => $last_eval1->kind,
                        'eid' => $last_eval1->evaluation_test_id,
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
            //ACT 28-03-18: Igual guardamos el nombre de la prueba
    		//$last_eval = NULL;
            $last_eval = [
                'id' => NULL,
                'eid' => $kind,
                'name' => $name->name,
                'name_eng' => $name->name_eng
            ];
    	}

    	return $last_eval;

    }

    public static function changeStatus($control_id,$org_id)
    {
        DB::table('control_eval_temp2')
            ->where('control_id','=',$control_id)
            ->where('organization_id','=',$org_id)
            ->update([
                'status' => 0,
            ]);
    }

    public static function saveControlValue($control_id,$org_id,$result_p,$result_i,$user_id)
    {
        return DB::table('control_eval_temp2')
            ->insertGetId([
                'control_id' => $control_id,
                'organization_id' => $org_id,
                'user_id' => $user_id,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1,
                'probability' => $result_p,
                'impact' => $result_i
            ]);
    }

}
