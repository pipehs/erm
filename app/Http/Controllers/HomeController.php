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
use Ermtool\Http\Controllers\EvaluacionRiesgosController as Evaluations;
use Ermtool\Http\Controllers\RiesgosController as Risks;
use DateTime;
use Mail;
use Storage;
use PDF;

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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }

            /* ----------  DESACTIVADO EN IMPLEMENTACIÓN ---------- */
            //--- SISTEMA DE ALERTA ---//
            //$planes = new PlanesAccion;
            //verificamos que hayan planes de acción próximos a cerrar
            //$plans = $planes->verificarFechaPlanes();
            $plans = NULL;
            //--- GENERAMOS HEATMAP PARA ÚLTIMA ENCUESTA DE EVALUACIÓN AGREGADA ---//

            $evalclass = new Evaluations;
            $evals = $evalclass->heatmapLastEvaluation();

            //--- Gráfico de Riesgos clasificados por categoría ---//
            //$riskclass = new Risks;
            //$risks = $risks->getRisks(NULL);

            //seteamos contador para cada categoría
            $p_categories = \Ermtool\Risk_category::getPrimaryCategories();
            $cont_categories = array();

            //seteamos variables en caso de que no hayan datos
            $categories = array();
            $categories2 = array();
            $riesgos_objective = array();
            $riesgos_subprocess = array();
            $i = 0;
            foreach ($p_categories as $category)
            {
        
                $cont_categories[$i] = 0;
                

                //obtenemos riesgos de cada categoria
                $risks_temp = \Ermtool\Risk::getRisksFromCategory($category->id);

                $cont_categories[$i] = count($risks_temp);

                //$randcolor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                //en vez e color random, probaremos un array con colores
                $html_colors = ['#a9cce3','#aed6f1','#d4e6f1','#d6eaf8','#566573','#626567','#3498db','#2980b9','#5dade2','#5499c7','#85c1e9','#7fb3d5','#21618c','#1a5276','#2874a6','#1f618d','#2e86c1','#2471a3','#bdc3c7','#616a6b','#717d7e','#7f8c8d','#1b4f72','#154360'];

                $c1 = count($html_colors)-1;
                $randcolor = $html_colors[mt_rand(0,$c1)];
                //seteamos correctamente los acentos

                $name = eliminaAcentos($category->name);
                $categories[$i] = ['id' => $category->id,'name' => $name, 'cont' => $cont_categories[$i], 'color' => $randcolor,'risks' => $risks_temp];
                $i += 1;
            }

            //--- Gráfico de Riesgos Críticos ---//
            //obtenemos subcategories
            $risk_categories = \Ermtool\Risk_category::getAllCategories();

            //$risks = array();
            $cont_categories2 = array();
            $i = 0;
            foreach ($risk_categories as $subcategory)
            {
                $categories2[$i] = ['id' => $subcategory->id,'name' => $subcategory->name];
                $cont_categories2[$i] = 0;
                $i += 1;
            }

            $ano = date('Y');
            $mes = date('m');
            $dia = date('d');

            $c_subprocess = \Ermtool\Evaluation::getEvaluationRiskSubprocess(NULL,NULL,NULL,FALSE,$ano,$mes,$dia); 

            $c_objective = \Ermtool\Evaluation::getEvaluationObjectiveRisk(NULL,NULL,NULL,FALSE,$ano,$mes,$dia);

            $evalclass = new Evaluations;

            if (isset($c_objective) && $c_objective != null && !empty($c_objective))
            {
                //inherente
                $prom_proba_in = array();
                $prom_criticidad_in = array();
                
                $riesgos_objective = $evalclass->getEvaluatedRisks(NULL,$c_objective,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,$categories2,$cont_categories2,$risk_categories);
            }
            else
            {
                $riesgos_objective = array();
            }        


            if (isset($c_subprocess) && $c_subprocess != null && !empty($c_subprocess))
            {
                    //inherente
                    $prom_proba_in = array();
                    $prom_criticidad_in = array();
                    $riesgos_subprocess = $evalclass->getEvaluatedRisks(NULL,$c_subprocess,$ano,$mes,$dia,$prom_proba_in,$prom_criticidad_in,$categories2,$cont_categories2,$risk_categories);
            }
            else
            {
                $riesgos_subprocess = array();
            }
            //retornamos la vista HOME con datos
            //OBS: desde 15-07-2016 verificaremos idioma seleccionado
            if (Session::get('languaje') == 'es')
            {
                return view('home',['nombre'=>$evals['nombre'],'descripcion'=>$evals['descripcion'],
                                            'riesgos'=>$evals['riesgos'],'prom_proba'=>$evals['prom_proba'],'prom_criticidad'=>$evals['prom_criticidad'],'plans' => $plans,'org' => $evals['org'],'categories'=>$categories,'riesgos_subprocess' => $riesgos_subprocess,'riesgos_objective' => $riesgos_objective]);
            }
            else if (Session::get('languaje') == 'en')
            {
                return json_en('en.home',['nombre'=>$evals['nombre'],'descripcion'=>$evals['descripcion'],
                                            'riesgos'=>$evals['riesgos'],'prom_proba'=>$evals['prom_proba'],'prom_criticidad'=>$evals['prom_criticidad'],'plans' => $plans,'org' => $evals['org'],'categories'=>$categories,'riesgos_subprocess' => $riesgos_subprocess,'riesgos_objective' => $riesgos_objective]);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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

    public function pdfHelp()
    {
        //$dompdf = new DOMPDF();
        //$dompdf->load_html( file_get_contents( 'http://erm.local/public' ) );
        //$dompdf->render();
        //$dompdf->stream("mi_archivo.pdf");

        
        $pdf = PDF::loadView('help');
        return $pdf->download('invoice.pdf');
        //pdf = PDF::loadView('help');
        //return $pdf->download('pruebapdf.pdf');
    }

    public function support()
    {
        if (Auth::guest())
        {  
            return view('login');
        }
        else
        {
            return view('support');
        }
    }

    public function supportStore(Request $request)
    {
        try
        {
            if (Auth::guest())
            {
                return view('login');
            }
            else
            {
                $evidence = $request->file('evidence_problem');

                $mail = 'fherrera@ixus.cl';

                $name = Auth::user()->name.' '.Auth::user()->surnames;
                $user_mail = Auth::user()->email;
                //verificamos que sea una imagen
                if ($evidence)
                {
                    $test = explode('.',$evidence->getClientOriginalName());
                }
                else
                {
                    $test = null;
                }

                if (isset($test[1])) //existe una extensión
                {
                    //verificamos que tenga extensión de imagen
                    if ($test[1] == 'png' || $test[1] == 'jpg' || $test[1] == 'jpeg' || $test[1] == 'gif' || $test[1] == 'PNG' || $test[1] == 'JPG' || $test[1] == 'JPEG' || $test[1] == 'GIF' || $test[1] == 'jpg')
                    {
                        //PROBAMOS GUARDAR IMAGEN TEMPORALMENTE
                        $guardado = Storage::put('temporal_mail/'.$evidence->getClientOriginalName(), file_get_contents($evidence->getRealPath())
                            );
                        //si es imagen, proseguimos con el envío de mail
                        Mail::send('mail_support',['user' => $name,'user_mail' => $user_mail,'problem' => $_POST['description'], 'imagen' => $evidence->getClientOriginalName()], function ($message) use ($mail,$name)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $message->to($mail, $name)->subject('Support ticket from B-GRC');
                            }
                            else
                            {
                                $message->to($mail, $name)->subject('Ticket de consulta B-GRC');
                            }
                        });

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Support ticket successfully sent');
                        }
                        else
                        {
                            Session::flash('message','Ticket de soporte enviado correctamente');
                        }

                        return Redirect::to('support');
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('error','The file uploaded is not an image');
                        }
                        else
                        {
                            Session::flash('error','El archivo cargado no es una imagen');
                        }

                        return Redirect::to('support')->withInput();
                    }
                }
                
                else if (isset($test[0])) //significa que es un archivo sin extensión
                {
                    if (Session::get('languaje') == 'en')
                        {
                            Session::flash('error','The file uploaded is not an image');
                        }
                        else
                        {
                            Session::flash('error','El archivo cargado no es una imagen');
                        }

                        return Redirect::to('support')->withInput();
                }

                else //no se agregó imagen
                {
                    Mail::send('mail_support',['user' => $name,'user_mail' => $user_mail,'problem' => $_POST['description']], function ($message) use ($mail,$name)
                        {
                            if (Session::get('languaje') == 'en')
                            {
                                $message->to($mail, $name)->subject('Support ticket from B-GRC');
                            }
                            else
                            {
                                $message->to($mail, $name)->subject('Ticket de consulta B-GRC');
                            }
                        });

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Support ticket successfully sent');
                        }
                        else
                        {
                            Session::flash('message','Ticket de soporte enviado correctamente');
                        }

                        return Redirect::to('support');
                }
                //print_r($_POST);
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
