<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon;

class Risk extends Model
{
    
   	protected $fillable = ['name','type','type2','description','expiration_date','status','risk_category_id','cause_id','effect_id','expected_loss','comments'];

    //eliminamos created_at y updated_at
    //public $timestamps = false;

    public static function name($id)
    {
        return DB::table('risks')->where('id', $id)->value('name');
    }

    public static function description($id)
    {
        return DB::table('risks')->where('id', $id)->value('description');
    }
    
    public function causes()
    {
    	return $this->belongsToMany('Ermtool\Cause');
    }

    public function effects()
    {
    	return $this->belongsToMany('Ermtool\Effect');
    }

    public function risk_categories()
    {
    	return $this->belongsTo('Ermtool\Risk_category');
    }
    public function subprocesses()
    {
        return $this->belongsToMany('Ermtool\Subprocess');
    }
    public function objectives()
    {
        return $this->belongsToMany('Ermtool\Objective');
    }
    public function organizations()
    {
        return $this->belongsToMany('Ermtool\Organization');
    }

    public function evaluations()
    {
        return $this->belongsToMany('Ermtool\Evaluation');
    }

    //obtenemos riesgos de control
    public static function getRisksFromControl($org,$control)
    {
        $risks = DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->join('control_organization_risk','control_organization_risk.organization_risk_id','=','organization_risk.id')
                    ->whereNull('organization_risk.deleted_at')
                    ->where('control_organization_risk.control_id','=',$control)
                    ->where('organization_risk.organization_id','=',(int)$org)
                    ->where('risks.status','=',0)
                    ->select('risks.id','risks.name','risks.description')
                    ->groupBy('risks.id','risks.name','risks.description')
                    ->get();

        return $risks;
    }

    public static function getObjectiveRisks($org)
    {
        //ACTUALIZACIÓN 29-03-17: Se obtendrán riesgos de la organización (organization_risk) que además sean riesgos de negocio
        //ACT 10-04-17: Agregamos opción NULL por KRI
        if ($org == NULL)
        {
            return DB::table('objective_risk')
                ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->whereNull('organization_risk.deleted_at')
                ->where('risks.type2','=',1)
                ->where('risks.status','=',0)
                ->groupBy('risks.id','risks.name','risks.description','risks.risk_category_id')
                ->select('risks.id as risk_id','risks.name as risk_name','risks.description','risks.risk_category_id')
                ->get();
        }
        else
        {
            return DB::table('objective_risk')
                ->join('organization_risk','organization_risk.risk_id','=','objective_risk.risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_risk.organization_id','=',$org)
                ->where('risks.type2','=',1)
                ->where('risks.status','=',0)
                ->groupBy('organization_risk.id','risks.id','risks.name','risks.description','risks.risk_category_id')
                ->select('organization_risk.id as org_risk_id','risks.id as risk_id','risks.name as risk_name','risks.description','risks.risk_category_id')
                ->get();
        }
        
    }

    //Función para identificación y matrices de riesgos (igual a la de arriba sólo que para no causar posibles fallos se hizo una nueva agregando la categoría)
    public static function getRisks($org,$category)
    {
        //ACTUALIZACIÓN 13-10-17: Se muestran primero todos los riesgos independiente de la organización
        if ($org != NULL)
        {
            if ($category == NULL)
            {
                //primero obtenemos sólo el id de los riesgos y luego su información
                return DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->where('organization_risk.organization_id','=',$org)
                        ->whereNull('organization_risk.deleted_at')
                        ->where('risks.type2','=',1)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id as org_risk_id')
                        ->get();
            }
            else
            {
                return DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->where('organization_risk.organization_id','=',$org)
                        ->whereNull('organization_risk.deleted_at')
                        ->where('risks.risk_category_id','=',$category)
                        ->where('risks.type2','=',1)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id as org_risk_id')
                        ->get();
            }
        }
        else
        {
            //ACT 06-06-18: Obtenemos los que no estén bloqueados en organization_risk
            return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->whereNull('organization_risk.deleted_at')
                ->where('risks.type2','=',1)
                ->where('risks.status','=',0)
                ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id')
                ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id','organization_risk.id as org_risk_id')
                ->get();
        }
        
    }

    //ACT 04-04-17: Igual a las de arriba solo que con where de type para proceso o negocio
    public static function getRisksWithType($org,$category,$type)
    {
        if ($org != NULL)
        {
            if ($category == NULL)
            {
                //primero obtenemos sólo el id de los riesgos y luego su información
                //ACTUALIZACIÓN 29-03-17: Probablemente group by da lo mismo
                return DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->where('organization_risk.organization_id','=',$org)
                        ->where('risks.type2','=',1)
                        ->where('risks.type','=',$type)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();
            }
            else
            {
                //ACTUALIZACIÓN 21-08-17: Obtenemos también los riesgos de las subcategorías
                $subcategories_temp = DB::table('risk_categories')
                                ->where('risk_category_id','=',$category)
                                ->select('id')
                                ->get();

                //convertimos a array
                $subcategories = array();
                $i = 0;
                foreach ($subcategories_temp as $s)
                {
                    $subcategories[$i] = $s->id;
                    $i += 1;
                }
                //riesgos de categoría directamente
                $risks1 = DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->where('organization_risk.organization_id','=',$org)
                        ->where('risks.risk_category_id','=',$category)
                        ->where('risks.type2','=',1)
                        ->where('risks.type','=',$type)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();

                //riesgos de subcategoría
                $risks2 = DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->where('organization_risk.organization_id','=',$org)
                        ->whereIn('risks.risk_category_id',$subcategories)
                        ->where('risks.type2','=',1)
                        ->where('risks.type','=',$type)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();

                $risks = array_merge($risks1,$risks2);
                $risks = array_unique($risks,SORT_REGULAR);
                return $risks;
            }
        }
        else
        {
            if ($category == NULL)
            {
                //primero obtenemos sólo el id de los riesgos y luego su información
                //ACTUALIZACIÓN 29-03-17: Probablemente group by da lo mismo
                return DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->where('risks.type2','=',1)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();
            }
            else
            {
                //ACTUALIZACIÓN 21-08-17: Obtenemos también los riesgos de las subcategorías
                $subcategories_temp = DB::table('risk_categories')
                                ->where('risk_category_id','=',$category)
                                ->select('id')
                                ->get();

                //convertimos a array
                $subcategories = array();
                $i = 0;
                foreach ($subcategories_temp as $s)
                {
                    $subcategories[$i] = $s->id;
                    $i += 1;
                }
                //riesgos de categoría directamente
                $risks1 = DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->where('risks.risk_category_id','=',$category)
                        ->where('risks.type2','=',1)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();

                //riesgos de subcategoría
                $risks2 = DB::table('risks')
                        ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                        ->whereNull('organization_risk.deleted_at')
                        ->whereIn('risks.risk_category_id',$subcategories)
                        ->where('risks.type2','=',1)
                        ->where('risks.status','=',0)
                        ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','organization_risk.stakeholder_id')
                        ->get();

                $risks = array_merge($risks1,$risks2);
                $risks = array_unique($risks,SORT_REGULAR);
                return $risks;
            }
        }
        
    }

    //ACTUALIZACIÓN 05-10-17: Obtenemos riesgos por categoría (indepeniente de la organización)
    public static function getRisksFromCategory($category)
    {
            //Obtenemos también los riesgos de las subcategorías
            $subcategories_temp = DB::table('risk_categories')
                            ->where('risk_category_id','=',$category)
                            ->select('id')
                            ->get();

            //convertimos a array
            $subcategories = array();
            $i = 0;
            foreach ($subcategories_temp as $s)
            {
                $subcategories[$i] = $s->id;
                $i += 1;
            }
            //riesgos de categoría directamente
            //ACT 06-06-2018: Verificamos que no esté bloqueado en organization_risk
            $risks1 = DB::table('risks')
                    ->join('risk_categories','risk_categories.id','=','risks.risk_category_id')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->whereNull('organization_risk.deleted_at')
                    ->where('risks.risk_category_id','=',$category)
                    ->where('risks.type2','=',1)
                    ->where('risks.status','=',0)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risk_categories.name')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risk_categories.name as risk_category')
                    ->get();

            //riesgos de subcategoría
            $risks2 = DB::table('risks')
                    ->join('risk_categories','risk_categories.id','=','risks.risk_category_id')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->whereNull('organization_risk.deleted_at')
                    ->whereIn('risks.risk_category_id',$subcategories)
                    ->where('risks.type2','=',1)
                    ->where('risks.status','=',0)
                    ->groupBy('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risk_categories.name')
                    ->select('risks.id','risks.name','risks.created_at','risks.updated_at','risks.expiration_date','risks.description','risks.type','risks.type2','risks.status','risks.expected_loss','risks.risk_category_id','risk_categories.name as risk_category')
                    ->get();

            $risks = array_merge($risks1,$risks2);
            $risks = array_unique($risks,SORT_REGULAR);
            
            return $risks;
    }

    public static function getRiskSubprocess($org)
    {
        //ACT 10-04-17: Agregamos opción de NULL por KRI  
        if ($org == NULL)
        {
            return DB::table('risk_subprocess')
                ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->whereNull('organization_risk.deleted_at')
                ->where('risks.type2','=',1)
                ->where('risks.status','=',0)
                ->groupBy('risks.id','risks.name','risks.description')
                ->select('risks.id as risk_id','risks.name as risk_name','risks.description','risks.risk_category_id')
                ->get();
        }
        else
        {
            //ACT 30-07-18: Se agrega verificar que el subproceso se encuentre en la organización
            return DB::table('risk_subprocess')
                ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.risk_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','risk_subprocess.subprocess_id')
                ->join('risks','risks.id','=','risk_subprocess.risk_id')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_risk.organization_id','=',$org)
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('risks.type2','=',1)
                ->where('risks.status','=',0)
                ->groupBy('organization_risk.id','risks.id','risks.name','risks.description')
                ->select('organization_risk.id as org_risk_id','risks.id as risk_id','risks.name as risk_name','risks.description','risks.risk_category_id')
                ->get();
        }
    }

    public static function getRisksFromProcess($org,$process_id)
    {
        return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_subprocess.organization_id','=',$org)
                ->where('subprocesses.process_id','=',$process_id)
                ->where('risks.status','=',0)
                ->select('risks.id','risks.name','risks.description')
                ->groupBy('risks.id','risks.name','risks.description')
                ->get();
    }

    public static function getRisksFromSubprocess($org,$subprocess_id)
    {
        return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->join('risk_subprocess','risk_subprocess.risk_id','=','risks.id')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_risk.organization_id','=',$org)
                ->where('risk_subprocess.subprocess_id','=',$subprocess_id)
                ->where('risks.status','=',0)
                ->get(['organization_risk.id','risks.name','risks.description','risks.id as risk_id','risks.risk_category_id','risks.comments','risks.expected_loss']);
    }

    public static function getRisksFromOrgRisk($org_risk)
    {
        return DB::table('organization_risk')
                        ->where('organization_risk.id','=',$org_risk)
                        ->join('risks','organization_risk.risk_id','=','risks.id')
                        ->join('organizations','organizations.id','=','organization_risk.organization_id')
                        ->where('risks.status','=',0)
                        ->whereNull('organization_risk.deleted_at')
                        ->select('risks.id','risks.name as risk_name','risks.description','organizations.name as org','organizations.id as org_id')
                        ->get();
    }

    public static function getOrganizationRisk($org,$risk_id)
    {
        return DB::table('organization_risk')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_id','=',$org)
                ->where('risk_id','=',$risk_id)
                ->select('id')
                ->first();
    }

    public static function getEnlacedRisks()
    {
        return DB::table('objective_subprocess_risk')
            ->join('risks as risk_subprocess','risk_subprocess.id','=','objective_subprocess_risk.risk_subprocess_id')
            ->join('risks as objective_risk','objective_risk.id','=','objective_subprocess_risk.objective_risk_id')
            ->join('organization_risk','organization_risk.risk_id','=','risk_subprocess.id')
            ->whereNull('organization_risk.deleted_at')
            ->where('risk_subprocess.status','=',0)
            ->select('objective_subprocess_risk.id','objective_risk.name as obj_name','risk_subprocess.name as sub_name')
            ->get();
    }

    public static function insertOrganizationRisk($org_id,$risk_id,$stakeholder_id,$comments,$risk_response)
    {
        DB::table('organization_risk')
            ->insert([
                    'organization_id' => $org_id,
                    'risk_id' => $risk_id,
                    'stakeholder_id' => $stakeholder_id,
                    'comments' => $comments,
                    'risk_response_id' => $risk_response,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'deleted_at' => NULL, 
                ]);
    }

    public static function getRiskByName($risk,$org_id)
    {
        if ($org_id != NULL)
        {
            return DB::table('risks')
                ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                ->whereNull('organization_risk.deleted_at')
                ->where('risks.name','=',$risk)
                ->where('organization_risk.organization_id','=',$org_id)
                ->select('risks.id','organization_risk.id as org_risk_id')
                ->first();
        }
        else
        {
            return DB::table('risks')
                ->where('risks.name','LIKE','%'.$risk.'%')
                ->select('risks.id')
                ->first();
        }
    }

    public static function getRiskByNameAndDescription($name,$description,$org_id)
    {
        return DB::table('risks')
            ->join('organization_risk','organization_risk.risk_id','=','risks.id')
            ->whereNull('organization_risk.deleted_at')
            ->where('risks.name','=',$name)
            ->where('risks.description','=',$description)
            ->where('organization_risk.organization_id','=',$org_id)
            ->select('risks.id','organization_risk.id as org_risk_id')
            ->first();
    }

    public static function getRiskByName2($risk)
    {
        return DB::table('risks')
            ->where('risks.name','=',$risk)
            ->select('risks.id')
            ->first();
    }

    //obtenemos riesgos asociados a un issue
    public static function getRisksFromIssue($issue)
    {
        //ACT 07-03-18: Utilizamos nueva tabla (muchos a muchos) issue_organization_risk
        $risks = DB::table('risks')
                    ->join('organization_risk','organization_risk.risk_id','=','risks.id')
                    ->join('issue_organization_risk','issue_organization_risk.organization_risk_id','=','organization_risk.id')
                    ->whereNull('organization_risk.deleted_at')
                    ->where('issue_organization_risk.issue_id','=',$issue)
                    ->select('organization_risk.id','risks.name','risks.description')
                    ->groupBy('organization_risk.id','risks.name','risks.description')
                    ->get();

        return $risks;
    }

    //obtenemos org_risk_id
    public static function getOrgRisk($risk_id,$org_id)
    {
        return DB::table('organization_risk')
            ->whereNull('organization_risk.deleted_at')
            ->where('risk_id','=',$risk_id)
            ->where('organization_id','=',$org_id)
            ->select('id')
            ->first();
    }

    public static function getLastMateriality($org_id,$risk_id)
    {
        $org_risk = DB::table('organization_risk')
                    ->whereNull('organization_risk.deleted_at')
                    ->where('risk_id','=',$risk_id)
                    ->where('organization_id','=',$org_id)
                    ->select('id')
                    ->first();

        //obtenemos fecha de última materialidad
        $created_at = DB::table('materiality')
            ->where('organization_risk_id','=',$org_risk->id)
            ->max('created_at');

        return DB::table('materiality')
            ->where('organization_risk_id','=',$org_risk->id)
            ->where('created_at','=',$created_at)
            ->select('id','impact','probability','kind','calification')
            ->first();
    }

    //ACTUALIZACIÓN 06-03-18: Obtenemos riesgos a través de issues (según organización)
    public static function getRisksFromIssues($org_id)
    {
        return DB::table('issues')
                ->join('issue_organization_risk','issue_organization_risk.issue_id','=','issues.id')
                ->join('organization_risk','organization_risk.id','=','issue_organization_risk.organization_risk_id')
                ->join('risks','risks.id','=','organization_risk.risk_id')
                ->whereNull('organization_risk.deleted_at')
                ->where('organization_risk.organization_id','=',(int)$org_id)
                ->select('organization_risk.id as id','risks.name','risks.description')
                ->groupBy('organization_risk.id','risks.name','risks.description')
                ->get();
    }

    //ACT 26-03-18: Obtenemos org_risks asociadas a un riesgo genérico
    public static function getOrgRisks($risk_id)
    {
        return DB::table('organization_risk')
                ->whereNull('organization_risk.deleted_at')
                ->where('risk_id','=',$risk_id)
                ->select('id','organization_id','risk_id')
                ->get();
    }

    public static function insertControlledRisk($risk,$result,$kind,$ano,$mes,$dia)
    {
        //ACT 05-07-17: Kind ya no se usa
        DB::table('controlled_risk')
            ->insert([
                'organization_risk_id' => $risk,
                'results' => $result,
                'created_at' => date($ano.'-'.$mes.'-'.$dia.' H:i:s')
            ]);
    }

    public static function insertResidualRisk($risk,$p,$i,$ano,$mes,$dia)
    {
        DB::table('residual_risk')
            ->insert([
                'organization_risk_id' => $risk,
                'probability' => $p,
                'impact' => $i,
                'created_at' => date($ano.'-'.$mes.'-'.$dia.' H:i:s'),
                'updated_at' => date($ano.'-'.$mes.'-'.$dia.' H:i:s')
            ]);
    }

    public static function getRiskByOrgRisk($org_risk_id)
    {
        return DB::table('risks')
            ->join('organization_risk','organization_risk.risk_id','=','risks.id')
            ->whereNull('organization_risk.deleted_at')
            ->where('organization_risk.id','=',$org_risk_id)
            ->first();
    }
}
