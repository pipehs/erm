<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use DB;
use Auth;
use Redirect;
use Ermtool\Http\Controllers\PlanesAccionController as PlanesAccion;
use DateTime;

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

        //--- SISTEMA DE ALERTA ---//
        $planes = new PlanesAccion;
        //verificamos que hayan planes de acción próximos a cerrar
        $plans = $planes->verificarFechaPlanes();

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
                                'evaluation_risk.organization_risk_id',
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
        $j = 0; //para obtener sólo una vez la organización (solución rápida)

        $org2 = NULL; //inicializamos org por si no hay evaluaciones
        foreach ($evaluations as $evaluation)
        {
            //obtenemos organización sólo una vez
            if ($j == 0)
            {
                $org = DB::table('organizations')
                        ->join('organization_risk','organization_risk.organization_id','=','organizations.id')
                        ->where('organization_risk.id','=',$evaluation->organization_risk_id)
                        ->select('organizations.name')
                        ->first();
                $j += 1;   
            }

            $org2 = $org->name;
             
            //para cada riesgo evaluado, identificaremos promedio de probabilidad y de criticidad
            $prom_proba[$i] = $evaluation->avg_probability;

            $prom_criticidad[$i] = $evaluation->avg_impact;

                //ACTUALIZACIÓN 29-03-17: Se mostrará sólo riesgo ya que ahora se evaluará sólo el riesgo (quizás después se pueden obtener los elementos asociados)
            

            $riesgo_temp = DB::table('organization_risk')
                            ->where('organization_risk.id','=',$evaluation->organization_risk_id)
                            ->join('risks','risks.id','=','organization_risk.risk_id')
                            ->select('risks.name as name','risks.description')
                            ->get();
            
            foreach ($riesgo_temp as $temp) //el riesgo recién obtenido (de subproceso o negocio) es almacenado en riesgos
            {
                $riesgos[$i] = array('name' => $temp->name,
                                    'description' => $temp->description,);
            }
            
            $i += 1;
        }

        //retornamos la vista HOME con datos
        //OBS: desde 15-07-2016 verificaremos idioma seleccionado
        if (Session::get('languaje') == 'es')
        {
            return view('home',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                        'riesgos'=>$riesgos,'prom_proba'=>$prom_proba,
                                        'prom_criticidad'=>$prom_criticidad,'plans' => $plans,'org' => $org2]);
        }
        else if (Session::get('languaje') == 'en')
        {
            return view('en.home',['nombre'=>$nombre,'descripcion'=>$descripcion,
                                        'riesgos'=>$riesgos,'prom_proba'=>$prom_proba,
                                        'prom_criticidad'=>$prom_criticidad,'plans' => $plans,'org' => $org2]);
        }
    }

    public function help()
    {
        if (Auth::guest())
        {  
            return view('login');
        }
        else
        {
            return view('help');
        }
    }
}
