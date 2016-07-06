<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Auth;
use Redirect;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            return Redirect::route('home');
        }
    }
    public function index()
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        //--- GENERAMOS HEATMAP PARA ÚLTIMA ENCUESTA DE EVALUACIÓN AGREGADA ---//

        //obtenemos id de última evaluación
        $id_eval = DB::table('evaluations')->max('id');
        //seteamos datos en NULL por si no existe evaluación
        $nombre = NULL;
        $descripcion = NULL;
        $riesgos = NULL;
        $prom_proba = NULL;
        $prom_criticidad = NULL;

        //---- consulta multiples join para obtener las respuestas relacionada a la encuesta ----// 
        $evaluations = DB::table('evaluation_risk')
                            ->where('evaluation_risk.evaluation_id',$id_eval)
                            ->select('evaluation_risk.id','evaluation_risk.risk_id',
                                'evaluation_risk.objective_risk_id','evaluation_risk.risk_subprocess_id',
                                'evaluation_risk.avg_probability','evaluation_risk.avg_impact')
                            ->get();

        //obtenemos nombre y descripcion de la última encuesta
        $datos = DB::table('evaluations')->where('id',$id_eval)->select('name','description')->get();

        foreach ($datos as $datos)
        {
             $nombre = $datos->name;
             $descripcion = $datos->description;
        }

        $prom_proba = array();
        $prom_criticidad = array();
        $riesgos = array();
        $i = 0;

        foreach ($evaluations as $evaluation)
        {

            //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
            $prom_proba[$i] = $evaluation->avg_probability;

            $prom_criticidad[$i] = $evaluation->avg_impact;

                //primero verificamos de que tipo de riesgo se trata
                if($evaluation->risk_subprocess_id != NULL) //si es riesgo de subproceso
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de subproceso
                    $riesgo_temp = DB::table('risk_subprocess')
                                    ->where('risk_subprocess.id','=',$evaluation->risk_subprocess_id)
                                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                                    ->select('risks.name as name','subprocesses.name as subobj')->get();
                }

                else if ($evaluation->objective_risk_id != NULL) //es riesgo de negocio
                {
                    //obtenemos nombre del riesgo y lo guardamos en array de riesgo junto al nombre de organización
                    $riesgo_temp = DB::table('objective_risk')
                                    ->where('objective_risk.id','=',$evaluation->objective_risk_id)
                                    ->join('risks','risks.id','=','objective_risk.risk_id')
                                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                                    ->join('organizations','organizations.id','=','objectives.organization_id')
                                    ->select('risks.name as name','organizations.name as subobj')->get();
                }

                else
                {
                    //aun no se soluciona para riesgos generales
                    $riesgo_temp = array();
                }
            
            foreach ($riesgo_temp as $temp) //el riesgo recién obtenido (de subproceso o negocio) es almacenado en riesgos
            {
                $riesgos[$i] = array('name' => $temp->name,
                                    'subobj' => $temp->subobj);
            }
            //obtenemos nombre del riesgo y lo guardamos en array de riesgo con foreach
            //$riesgos[$i] = \Ermtool\Risk::where('id',$evaluation->risk_id)->value('name');

       /*
            echo 
                 "Riesgo: ".$riesgos[$i]."<br>".
                 "Proba: ".$prom_proba[$i]."<br>".
                 "Criti: ".$prom_criticidad[$i]."<hr>"; */
            
            $i += 1;
        }

        //retornamos la vista HOME con datos

        return view('home',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                        'riesgos'=>$riesgos,'prom_proba'=>$prom_proba,
                                        'prom_criticidad'=>$prom_criticidad]);
    }

    
}
