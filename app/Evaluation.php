<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;

class Evaluation extends Model
{
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
    public static function getEvaluationRiskSubprocess($org,$cat,$subs,$ano,$mes)
    {
        //evals2 = new stdClass();
        $evals2 = array();

        if ($cat != NULL)
        {
            $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->where('risks.risk_category_id','=',$cat)
                ->whereNotNull('evaluation_risk.risk_subprocess_id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                ->where('evaluations.consolidation','=',1)
                ->select('evaluation_risk.risk_subprocess_id as risk_id','risks.id as risk')
                ->groupBy('risks.id')
                ->get();

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = DB::table('evaluation_risk')
                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                        ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                        ->where('risks.risk_category_id','=',$cat)
                        ->whereNotNull('evaluation_risk.risk_subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org2->id)
                        ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                        ->where('evaluations.consolidation','=',1)
                        ->select('evaluation_risk.risk_subprocess_id as risk_id','risks.id as risk')
                        ->groupBy('risks.id')
                        ->get();

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
            $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->whereNotNull('evaluation_risk.risk_subprocess_id')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                ->where('evaluations.consolidation','=',1)
                ->select('evaluation_risk.risk_subprocess_id as risk_id','risks.id as risk')
                ->groupBy('risks.id')
                ->get();

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = DB::table('evaluation_risk')
                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                        ->join('risk_subprocess','risk_subprocess.id','=','evaluation_risk.risk_subprocess_id')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                        ->join('risks','risks.id','=','risk_subprocess.risk_id')
                        ->whereNotNull('evaluation_risk.risk_subprocess_id')
                        ->where('organization_subprocess.organization_id','=',$org2->id)
                        ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                        ->where('evaluations.consolidation','=',1)
                        ->select('evaluation_risk.risk_subprocess_id as risk_id','risks.id as risk')
                        ->groupBy('risks.id')
                        ->get();

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

    public static function getEvaluationObjectiveRisk($org,$cat,$subs,$ano,$mes)
    {
        $evals2 = array();

        if ($cat != NULL)
        {
            //---- consulta multiples join para obtener los objective_risk evaluados relacionados a la organización ----// 
            $eval1= DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                ->join('risks','risks.id','=','objective_risk.risk_id')
                ->where('risks.risk_category_id','=',$cat)
                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->where('evaluations.consolidation','=',1)
                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                ->select('evaluation_risk.objective_risk_id as risk_id','risks.id as risk')
                ->groupBy('risks.id')->get();

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                            ->join('risks','risks.id','=','objective_risk.risk_id')
                            ->where('risks.risk_category_id','=',$cat)
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->where('objectives.organization_id','=',$org2->id)
                            ->where('evaluations.consolidation','=',1)
                            ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                            ->select('evaluation_risk.objective_risk_id as risk_id','risks.id as risk')
                            ->groupBy('risks.id')->get();

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
            $eval1= DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                ->join('risks','risks.id','=','objective_risk.risk_id')
                ->join('objectives','objectives.id','=','objective_risk.objective_id')
                ->where('objectives.organization_id','=',$org)
                ->where('evaluations.consolidation','=',1)
                ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                ->select('evaluation_risk.objective_risk_id as risk_id','risks.id as risk')
                ->groupBy('risks.id')->get();

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->join('objective_risk','objective_risk.id','=','evaluation_risk.objective_risk_id')
                            ->join('risks','risks.id','=','objective_risk.risk_id')
                            ->join('objectives','objectives.id','=','objective_risk.objective_id')
                            ->where('objectives.organization_id','=',$org2->id)
                            ->where('evaluations.consolidation','=',1)
                            ->where('evaluations.updated_at','<=',date($ano.'-'.$mes).'-31 23:59:59')
                            ->select('evaluation_risk.objective_risk_id as risk_id','risks.id as risk')
                            ->groupBy('risks.id')->get();

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
}
