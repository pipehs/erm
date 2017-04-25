<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

class Evaluation extends Model
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
    
    protected $fillable = ['name','type','consolidation','description','expiration_date'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public function risks()
    {
    	return $this->belongsToMany('\Ermtool\Risk');
    }
/*
    public function stakeholders()
    {
    	return $this->hasManyThrough('\Ermtool\Stakeholder','evaluation_risk');
    }
*/
    public static function getEvaluationRiskSubprocess($org,$cat,$subs,$ano,$mes,$dia)
    {
        //evals2 = new stdClass();
        $evals2 = array();

        if ($cat != NULL)
        {

            $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval($org,$cat,$ano,$mes,$dia);

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getSubprocessRiskFromEval($org2,$cat,$ano,$mes,$dia);

                    foreach ($eval_temp as $e)
                    {
                        array_push($evals2,$e);
                    }
                }

                $evals2 = array_unique($evals2,SORT_REGULAR);

                $evals = array_merge($eval1,$evals2);
                $evals = array_unique($evals,SORT_REGULAR);
                return $evals;
            }
            else
            {
                return $eval1;
            }
        }
        else
        {
            $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval($org,NULL,$ano,$mes,$dia);

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getSubprocessRiskFromEval($org2,NULL,$ano,$mes,$dia);

                    foreach ($eval_temp as $e)
                    {
                        array_push($evals2,$e);
                    }
                    
                    //$evals2->append($eval_temp);
                    //$evals2->{$i} = $eval_temp;
                }

                //print_r($eval1);
                //echo '<br>-------------------------<br>';
                $evals2 = array_unique($evals2,SORT_REGULAR);
                //print_r($evals2);

                $evals = array_merge($eval1,$evals2);
                $evals = array_unique($evals,SORT_REGULAR);

                //echo '<br>----------------------------------------------<br>';
                //
                return $evals;
            }
            else
            {
                return $eval1;
            }
        }
    }

    public static function getEvaluationObjectiveRisk($org,$cat,$subs,$ano,$mes,$dia)
    {
        $evals2 = array();

        if ($cat != NULL)
        {
            //---- consulta multiples join para obtener los objective_risk evaluados relacionados a la organización ----// 
            $eval1= \Ermtool\Evaluation::getObjectiveRiskFromEval($org,$cat,$ano,$mes,$dia); 

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getObjectiveRiskFromEval($org2,$cat,$ano,$mes,$dia);

                    foreach ($eval_temp as $e)
                    {
                        array_push($evals2,$e);
                    }
                }

                $evals2 = array_unique($evals2,SORT_REGULAR);

                $evals = array_merge($eval1,$evals2);
                $evals = array_unique($evals,SORT_REGULAR);

                return $evals;
            }
            else
            {
                return $eval1;
            }
        }
        else //no se seleccionó una categoría de riesgos (por lo tanto no es un filtro en la consulta)
        {
            //---- consulta multiples join para obtener los objective_risk evaluados relacionados a la organización ----// 
            $eval1 = \Ermtool\Evaluation::getObjectiveRiskFromEval($org,NULL,$ano,$mes,$dia);

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getObjectiveRiskFromEval($org,NULL,$ano,$mes,$dia);

                    foreach ($eval_temp as $e)
                    {
                        array_push($evals2,$e);
                    }
                }

                $evals2 = array_unique($evals2,SORT_REGULAR);
                
                $evals = array_merge($eval1,$evals2);
                $evals = array_unique($evals,SORT_REGULAR);

                return $evals;
            }
            else
            {
                return $eval1;
            }
        }

    }

    public static function getSubprocessRiskFromEval($org,$cat,$ano,$mes,$dia)
    {
        if ($cat == NULL)
        {
           return $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->whereNotNull('evaluation_risk.organization_risk_id')
                ->where('organization_risk.organization_id','=',$org)
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                ->where('evaluations.consolidation','=',1)
                ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                ->get(); 
        }
        else
        {
            return $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->where('risks.risk_category_id','=',$cat)
                ->whereNotNull('evaluation_risk.organization_risk_id')
                ->where('organization_risk.organization_id','=',$org)
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                ->where('evaluations.consolidation','=',1)
                ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                ->get();
        }
    }

    public static function getObjectiveRiskFromEval($org,$cat,$ano,$mes,$dia)
    {
        //ahora validamos mes
            if ($cat == NULL)
            {
                return DB::table('evaluation_risk')
                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                        ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                        ->join('risks','risks.id','=','organization_risk.risk_id')
                        ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org)
                        ->where('evaluations.consolidation','=',1)
                        ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                        ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                        ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                        ->get();
            }
            else
            {
                return DB::table('evaluation_risk')
                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                        ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                        ->join('risks','risks.id','=','organization_risk.risk_id')
                        ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                        ->where('risks.risk_category_id','=',$cat)
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org)
                        ->where('evaluations.consolidation','=',1)
                        ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                        ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                        ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                        ->get();
            }
    }
}
