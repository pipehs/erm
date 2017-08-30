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

            //--- SISTEMA DE ALERTA ---//
            $planes = new PlanesAccion;
            //verificamos que hayan planes de acción próximos a cerrar
            $plans = $planes->verificarFechaPlanes();

            //--- GENERAMOS HEATMAP PARA ÚLTIMA ENCUESTA DE EVALUACIÓN AGREGADA ---//

            $evalclass = new Evaluations;
            $evals = $evalclass->heatmapLastEvaluation();

            //retornamos la vista HOME con datos
            //OBS: desde 15-07-2016 verificaremos idioma seleccionado
            if (Session::get('languaje') == 'es')
            {
                return view('home',['nombre'=>$evals['nombre'],'descripcion'=>$evals['descripcion'],
                                            'riesgos'=>$evals['riesgos'],'prom_proba'=>$evals['prom_proba'],'prom_criticidad'=>$evals['prom_criticidad'],'plans' => $plans,'org' => $evals['org']]);
            }
            else if (Session::get('languaje') == 'en')
            {
                return view('home',['nombre'=>$evals['nombre'],'descripcion'=>$evals['descripcion'],
                                            'riesgos'=>$evals['riesgos'],'prom_proba'=>$evals['prom_proba'],'prom_criticidad'=>$evals['prom_criticidad'],'plans' => $plans,'org' => $evals['org']]);
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
