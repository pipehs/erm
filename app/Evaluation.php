<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

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
    public static function getEvaluationRiskSubprocess($org,$cat,$subcat,$subs,$ano,$mes,$dia)
    {
        //evals2 = new stdClass();
        $evals2 = array();

        if ($cat != NULL)
        {

            if ($subcat != NULL)
            {
                if ($org == NULL)
                {
                    $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval(NULL,$cat,$subcat,$ano,$mes,$dia);
                }
                else
                {
                    $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval($org,$cat,$subcat,$ano,$mes,$dia);
                }
            }
            else
            {
                if ($org == NULL)
                {
                    $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval(NULL,$cat,NULL,$ano,$mes,$dia);
                }
                else
                {
                    $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval($org,$cat,NULL,$ano,$mes,$dia);
                }
            }

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    if ($subcat != NULL)
                    {
                        $eval_temp = \Ermtool\Evaluation::getSubprocessRiskFromEval($org2->id,$cat,$subcat,$ano,$mes,$dia);
                    }
                    else
                    {
                        $eval_temp = \Ermtool\Evaluation::getSubprocessRiskFromEval($org2->id,$cat,NULL,$ano,$mes,$dia);
                    }
                    

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
            //ACTUALIZACIÓN 29-08-17: Para Mostrar Consolidado en semáforo de Riesgos, enviaremos org como null
            if ($org == NULL)
            {
                $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval(NULL,NULL,NULL,$ano,$mes,$dia);
            }
            else
            {
                $eval1 = \Ermtool\Evaluation::getSubprocessRiskFromEval($org,NULL,NULL,$ano,$mes,$dia);
            }

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getSubprocessRiskFromEval($org2->id,NULL,NULL,$ano,$mes,$dia);

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

    public static function getEvaluationObjectiveRisk($org,$cat,$subcat,$subs,$ano,$mes,$dia)
    {
        $evals2 = array();

        if ($cat != NULL)
        {
            //---- consulta multiples join para obtener los objective_risk evaluados relacionados a la organización ----//
            if ($subcat != NULL)
            {
                $eval1= \Ermtool\Evaluation::getObjectiveRiskFromEval($org,$cat,$subcat,$ano,$mes,$dia); 
            }
            else
            {
                $eval1= \Ermtool\Evaluation::getObjectiveRiskFromEval($org,$cat,NULL,$ano,$mes,$dia); 
            }

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    if ($subcat != NULL)
                    {
                        $eval_temp = \Ermtool\Evaluation::getObjectiveRiskFromEval($org2->id,$cat,$subcat,$ano,$mes,$dia);
                    }
                    else
                    {
                        $eval_temp = \Ermtool\Evaluation::getObjectiveRiskFromEval($org2->id,$cat,NULL,$ano,$mes,$dia);
                    }
                    

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
            $eval1 = \Ermtool\Evaluation::getObjectiveRiskFromEval($org,NULL,NULL,$ano,$mes,$dia);

            if ($subs) //se deben seleccionar tambien los riesgos de las organizaciones dependientes
            {
                //buscamos estas organizaciones
                $orgs = DB::table('organizations')
                        ->where('organization_id','=',$org)
                        ->select('id')
                        ->get();

                foreach ($orgs as $org2) //para cada una de ellas guardamos sus evaluaciones en array temporal
                {
                    $eval_temp = \Ermtool\Evaluation::getObjectiveRiskFromEval($org->id,NULL,NULL,$ano,$mes,$dia);

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

    public static function getSubprocessRiskFromEval($org,$cat,$subcat,$ano,$mes,$dia)
    {
        if ($cat == NULL)
        {
            //ACTUALIZACIÓN 29-08-17: Para Mostrar Consolidado en semáforo de Riesgos, enviaremos org como null
            if ($org == NULL)
            {
                return $eval1 = DB::table('evaluation_risk')
                    ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                    ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                    ->join('risks','risks.id','=','organization_risk.risk_id')
                    ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                    //->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->whereNotNull('evaluation_risk.organization_risk_id')
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
                    //->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                    ->whereNotNull('evaluation_risk.organization_risk_id')
                    ->where('organization_risk.organization_id','=',$org)
                    //->where('organization_subprocess.organization_id','=',$org)
                    ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                    ->where('evaluations.consolidation','=',1)
                    ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                    ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                    ->get();
            }
            
        }
        else
        {
            if ($subcat != NULL)
            {
                return $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->where('risks.risk_category_id','=',$subcat)
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
                //ACTUALIZACIÓN 20-07-17: Debemos obtener también los riesgos de categorías dependientes
                //para eso, obtenemos subcategorias
                $subcategories_temp = DB::table('risk_categories')
                            ->where('risk_category_id','=',$cat)
                            ->select('id')
                            ->get();

                $subcategories = array();
                $i = 0;
                foreach ($subcategories_temp as $sub)
                {
                    $subcategories[$i] = $sub->id;
                    $i += 1;
                }

                //agregamos al último la categoría principal
                $subcategories[$i] = $cat;
                

                return $eval1 = DB::table('evaluation_risk')
                ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->whereIn('risks.risk_category_id',$subcategories)
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
    }

    public static function getObjectiveRiskFromEval($org,$cat,$subcat,$ano,$mes,$dia)
    {
        //ahora validamos mes
            if ($cat == NULL)
            {
                if ($org == NULL)
                {
                    return DB::table('evaluation_risk')
                        ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                        ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                        ->join('risks','risks.id','=','organization_risk.risk_id')
                        ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
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
                        ->join('objectives','objectives.id','=','objective_risk.objective_id')
                        ->where('objectives.organization_id','=',$org)
                        ->where('evaluations.consolidation','=',1)
                        ->where('evaluations.updated_at','<=',date($ano.$mes).$dia.' 23:59:59')
                        ->select('evaluation_risk.organization_risk_id as risk_id','risks.id as risk')
                        ->groupBy('evaluation_risk.organization_risk_id','risks.id')
                        ->get();
                }
            }
            else
            {
                if ($subcat != NULL)
                {
                    return DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                            ->join('risks','risks.id','=','organization_risk.risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->where('risks.risk_category_id','=',$subcat)
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
                    //ACTUALIZACIÓN 20-07-17: Debemos obtener también los riesgos de categorías dependientes
                    //para eso, obtenemos subcategorias
                    $subcategories_temp = DB::table('risk_categories')
                                ->where('risk_category_id','=',$cat)
                                ->select('id')
                                ->get();

                    $subcategories = array();
                    $i = 0;
                    foreach ($subcategories_temp as $sub)
                    {
                        $subcategories[$i] = $sub->id;
                        $i += 1;
                    }

                    //agregamos al último la categoría principal
                    $subcategories[$i] = $cat;
                    return DB::table('evaluation_risk')
                            ->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
                            ->join('organization_risk','organization_risk.id','=','evaluation_risk.organization_risk_id')
                            ->join('risks','risks.id','=','organization_risk.risk_id')
                            ->join('objective_risk','objective_risk.risk_id','=','organization_risk.risk_id')
                            ->whereIn('risks.risk_category_id',$subcategories)
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
}
