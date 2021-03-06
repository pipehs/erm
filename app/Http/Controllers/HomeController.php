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
use stdClass;
use Genert\BBCode\BBCode;

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
            //ACT 26-04-18: Obtenemos versión (si es que hay)
            //ACT 27-07-18: Seteamos toda la data de configuración, ya que también obtenedremos de ahí: logo, colores, etc.
            $data = [];
            $data['version'] = \Ermtool\Configuration::where('option_name','version')->first(['option_value as v']);
            $data['logo'] = \Ermtool\Configuration::where('option_name','logo')->first(['option_value as l']);
            $data['logo_width'] = \Ermtool\Configuration::where('option_name','logo_width')->first(['option_value as w']);
            $data['logo_height'] = \Ermtool\Configuration::where('option_name','logo_height')->first(['option_value as h']);
            $data['organization'] = \Ermtool\Configuration::where('option_name','short_name')->first(['option_value as o']);
            
            return view('login',['data' => $data]);
        }
        else
        {
            return Redirect::route('home');
        }
    }
    public function index()
    {
        //try
        //{
            if (Auth::guest())
            {
                return Redirect::route('/');
            }

            //vemos si hay configuración
            $version = \Ermtool\Configuration::where('option_name','version')->first();

            if (empty($version))
            {
                if (Auth::user()->superadmin == 1)
                {
                    //Vemos si es que hay alguna configuración seteada, si es así redirigimos a edit
                    $config = \Ermtool\Configuration::all();

                    if (empty($config))
                    {
                        return Redirect::to('configuration.create');
                    }
                    else
                    {
                        return Redirect::to('configuration.edit');
                    }
                    
                }
                else
                {
                    return locked();
                }
            }
            else
            {
                if (count(Session::get('roles')) > 1 || !in_array('9',Session::get('roles')))
                {
                    //Obtenemos mensaje bienvenida (si es que hay)
                    $m = \Ermtool\Configuration::where('option_name','welcome_message')->value('option_value');
                    //Pie de mensaje
                    $d = \Ermtool\Configuration::where('option_name','welcome_description')->value('option_value');
                    //Configuramos correctamente
                    if ($m)
                    {      
                        $BBCode = new BBCode();
                        $m = $BBCode->stripBBCodeTags($m);
                    }
                    if ($d)
                    {
                        $BBCode = new BBCode();
                        $d = $BBCode->stripBBCodeTags($d);
                    }
                    
                    /* ----------  DESACTIVADO EN IMPLEMENTACIÓN ---------- */
                    //--- SISTEMA DE ALERTA ---//
                    //$planes = new PlanesAccion;
                    //verificamos que hayan planes de acción próximos a cerrar
                    //$plans = $planes->verificarFechaPlanes();
                    $plans = NULL;
                    //--- GENERAMOS HEATMAP PARA ÚLTIMA ENCUESTA DE EVALUACIÓN AGREGADA ---//

                    $evalclass = new Evaluations;
                    //ACT 26-03-18: Obtenemos heatmap por categorías de Riesgo
                    $cats = $evalclass->heatmapForCategories();

                    if (Session::get('languaje') == 'es')
                    {
                        return view('home',['cats' => $cats,'m' => $m, 'd' => $d]);
                    }
                    else if (Session::get('languaje') == 'en')
                    {
                        return json_en('en.home',['cats' => $cats,'m' => $m,'d' => $d]);
                    }
                }    
                else
                {
                    $intro = \Ermtool\Configuration::where('option_name','cc_intro_message')->first(['option_value as o']);

                    if (!empty($intro))
                    {
                        $BBCode = new BBCode();
                        $intro = $BBCode->stripBBCodeTags($intro->o);
                    }
                    else
                    {
                        $intro = NULL;
                    }
                    if (Session::get('languaje') == 'es')
                    {
                        return view('denuncias.home',['intro' => $intro]);
                    }
                    else if (Session::get('languaje') == 'en')
                    {
                        return json_en('en.denuncias.home',['intro' => $intro]);
                    }
                }
            
                
            }
            

            
        //}
        //catch (\Exception $e)
        //{
            //enviarMailSoporte($e);
            //return view('errors.query',['e' => $e]);
        //}
    }

    public function help()
    {
        if (Auth::guest())
        {  
            return Redirect::route('/');
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
            return Redirect::route('/');
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
                return Redirect::route('/');
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
            //enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function reporteConsolidado()
    {   
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        else
        {
        //try
        //{
            //seteamos variables
            $i = 0;
            $results = [];
            $risk = new stdClass();
            //primero obtenemos organizaciones
            $orgs = DB::table('organizations')
                    ->where('status','=',0)
                    ->get(['id','name','description']);
            foreach ($orgs as $org)
            {
                //obtenemos subprocesos
                $subs = DB::table('subprocesses')
                        ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                        ->where('organization_subprocess.organization_id','=',$org->id)
                        ->where('subprocesses.status','=',0)
                        ->get(['subprocesses.id','subprocesses.name','subprocesses.description']);
                foreach ($subs as $sub)
                {
                    //obtenemos proceso
                    $process = DB::table('processes')
                            ->join('subprocesses','subprocesses.process_id','=','processes.id')
                            ->where('subprocesses.id','=',$sub->id)
                            ->where('processes.status','=',0)
                            ->select('processes.id','processes.name','processes.description')
                            ->first();
                    //obtenemos riesgos asociados al subproceso y la organización
                    $risks = \Ermtool\Risk::getRisksFromSubprocess($org->id,$sub->id);
                    //por algun motivo, no se están obteniendo todos los riesgos (en bgrc de parauco)
                    //print_r($risks);
                    if (!empty($risks))
                    {
                        $risk_resp_mail = 'No definido';
                        foreach ($risks as $risk)
                        {
                            //seteamos variables que dependen de cada riesgo
                            $causes = new stdClass();
                            $effects = new stdClass();
                            $last_evaluation = new stdClass();
                            $sev = 'No evaluado';
                            $ctrl = new stdClass();
                            $issue = new stdClass();
                            $plan = new stdClass();
                            //obtenemos categoría del riesgo
                            $risk_category = \Ermtool\Risk_category::name($risk->risk_category_id);
                            //obtenemos categoría principal (asociada a subcategoría)
                            $ppal_category = \Ermtool\Risk_category::getPrimaryCategory($risk->risk_category_id);
                            if (!empty($ppal_category))
                            {
                                $ppal_category = \Ermtool\Risk_category::name($ppal_category->id);
                            }
                            else
                            {
                                $ppal_category = NULL;
                            }
                            //obtenemos responsable
                            $risk_resp = \Ermtool\Stakeholder::getRiskStakeholder($org->id,$risk->risk_id);
                            if ($risk_resp->id != NULL)
                            {
                                //cargo responsable
                                $risk_resp_position = \Ermtool\Stakeholder::getPosition($risk_resp->id);
                                $risk_resp_position = $risk_resp_position->position;
                                if ($risk_resp_position == NULL)
                                {
                                    $risk_resp_position = 'No se ha definido cargo';
                                }
                                //mail responsable
                                $risk_resp_mail = \Ermtool\Stakeholder::getMail($risk_resp->id);
                                $risk_resp_mail = $risk_resp_mail->mail;
                                if ($risk_resp_mail == NULL)
                                {
                                    $risk_resp_mail = 'No definido';
                                }
                                //nombre responsable
                                $risk_resp = \Ermtool\Stakeholder::getName($risk_resp->id);
                            }
                            else
                            {
                                $risk_resp = 'No definido';
                                $risk_resp_position = 'No definido';
                            }
                            //pérdida esperada
                            if ($risk->expected_loss == NULL)
                            {
                                $risk->expected_loss = 'No se ha definido pérdida';
                            }
                            //obtenemos última evaluación
                            //ACT 26-03-18: Agregamos kind (1 es para cualquier tipo de evaluación)
                            //ACT 08-05-18: Obtenemos todas las evaluaciones
                            $eval = \Ermtool\Evaluation::getEvaluations($risk->id,1);
                            $last_eval = \Ermtool\Evaluation::getLastEvaluation($risk->id,1);
                            if (!empty($last_eval) || $last_eval != NULL)
                            {
                                $last_proba = $last_eval->avg_probability;
                                $last_impact = $last_eval->avg_impact;
                            }
                            else
                            {
                                $last_proba = NULL;
                                $last_impact = NULL;
                            }
                            
                            for ($j=0;$j<5;$j++)
                            {
                                if (!isset($eval[$j]))
                                {
                                    $eval[$j] = new stdClass;
                                    $eval[$j]->avg_probability = NULL;
                                    $eval[$j]->avg_impact = NULL;
                                    $eval[$j]->updated_at = NULL;
                                }
                            }
                            //causas y efectos
                            $causes = \Ermtool\Cause::getCausesFromRisk($risk->risk_id);
                            $effects = \Ermtool\Effect::getEffectsFromRisk($risk->risk_id);
                            //seteamos causas en caso de excel
                            if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
                            {
                                $c = '';
                                $e = '';
                                if (empty($causes))
                                {
                                    $causes = 'No se han agregado causas';
                                }
                                else
                                {
                                    $last = end($causes);
                                    foreach ($causes as $cause)
                                    {
                                        if ($cause == $last)
                                        {
                                            $c = $cause->name.' - '.$cause->description;
                                        }
                                        else
                                        {
                                            $c = $cause->name.' - '.$cause->description.', ';
                                        }
                                    }
                                    $causes = $c;
                                }
                                if (empty($effects))
                                {
                                    $effects = 'No se han agregado efectos';
                                }
                                else
                                {
                                    $last = end($effects);
                                    foreach ($effects as $effect)
                                    {
                                        if ($effect == $last)
                                        {
                                            $e = $effect->name.' - '.$effect->description;
                                        }
                                        else
                                        {
                                            $e = $effect->name.' - '.$effect->description.', ';
                                        }
                                    }
                                    $effects = $e;
                                }
                            }
                            //obtenemos controles asociados al riesgo
                            $controls = \Ermtool\Control::getControlsFromRisk($org->id,$risk->risk_id);
                            if (!empty($controls))
                            {
                                foreach ($controls as $ctrl)
                                {
                                    $co = \Ermtool\ControlOrganization::getByCO($ctrl->id,$org->id);
                                    //seteamos datos
                                    if (Session::get('languaje') == 'es')
                                    {
                                        //tipo de control
                                        if ($ctrl->type === 0)
                                        {
                                            $ctrl->type = 'Manual';
                                        }
                                        else if ($ctrl->type == 1)
                                        {
                                            $ctrl->type = 'Semi-automático';
                                        }
                                        else if ($ctrl->type == 2)
                                        {
                                            $ctrl->type = 'Automático';
                                        }
                                        else
                                        {
                                            $ctrl->type = 'No definido';
                                        }
                                        //periodicidad
                                        if ($ctrl->periodicity === 0)
                                        {
                                            $ctrl->periodicity = 'Diario';
                                        }
                                        else if ($ctrl->periodicity == 1)
                                        {
                                            $ctrl->periodicity = 'Semanal';
                                        }
                                        else if ($ctrl->periodicity == 2)
                                        {
                                            $ctrl->periodicity = 'Mensual';
                                        }
                                        else if ($ctrl->periodicity == 3)
                                        {
                                            $ctrl->periodicity = 'Semestral';
                                        }
                                        else if ($ctrl->periodicity == 4)
                                        {
                                            $ctrl->periodicity = 'Anual';
                                        }
                                        else if ($ctrl->periodicity == 5)
                                        {
                                            $ctrl->periodicity = 'Cada vez que ocurra';
                                        }
                                        else if ($ctrl->periodicity == 6)
                                        {
                                            $ctrl->periodicity = 'Trimestral';
                                        }
                                        else
                                        {
                                            $ctrl->periodicity = 'No definida';
                                        }
                                        //propósito
                                        if ($ctrl->purpose === 0)
                                        {
                                            $ctrl->purpose = 'Preventivo';
                                        }
                                        else if ($ctrl->purpose == 1)
                                        {
                                            $ctrl->purpose = 'Detectivo';
                                        }
                                        else if ($ctrl->purpose == 2)
                                        {
                                            $ctrl->purpose = 'Correctivo';
                                        }
                                        else
                                        {
                                            $ctrl->purpose = 'No se ha definido';
                                        }
                                        //comentarios
                                        if ($ctrl->comments == NULL)
                                        {
                                            $ctrl->comments = 'No se han agregado comentarios';
                                        }
                                        //evidencia
                                        if ($ctrl->evidence == NULL)
                                        {
                                            $ctrl->evidence = 'No se ha agregado evidencia';
                                        }
                                        //costo esperado
                                        if ($ctrl->expected_cost == NULL)
                                        {
                                            $ctrl->expected_cost = 'No se ha agregado costo esperado';
                                        }
                                    }
                                    else //se setean variables en inglés
                                    {
                                    }
                                    //obtenemos responsable de control
                                    $control_resp = \Ermtool\Control::getResponsable($ctrl->id,$risk->id);
                                    if ($control_resp->id != NULL)
                                    {
                                        //obtenemos correo
                                        $control_resp_mail = \Ermtool\Stakeholder::getMail($control_resp->id);
                                        $control_resp_mail = $control_resp_mail->mail;
                                        if ($control_resp_mail == NULL)
                                        {
                                            $control_resp_mail = 'No se ha agregado responsable';
                                        }
                                        //cargo responsable
                                        $control_resp_position = \Ermtool\Stakeholder::getPosition($control_resp->id);
                                        $control_resp_position = $control_resp_position->position;
                                        if ($control_resp_position == NULL)
                                        {
                                            $control_resp_position = 'No se ha definido cargo';
                                        }
                                        $control_resp = \Ermtool\Stakeholder::getName($control_resp->id);
                                    }
                                    else
                                    {
                                        $control_resp = 'No se ha agregado responsable';
                                        $control_resp_mail = 'No se ha agregado responsable';
                                        $control_resp_position = 'No se ha agregado responsable';
                                    }
                                    //seteamos riesgo residual
                                    $sev = array();
                                    $residual_risk = array();
                                    $cont_per = array();
                                    if (!empty($eval))
                                    {
                                        for($j=0;$j<5;$j++)
                                        {
                                            if ($eval[$j]->avg_probability != NULL)
                                            {
                                                if ($eval[$j]->updated_at != NULL)
                                                {
                                                    $cont_per[$j] = DB::table('control_eval_risk_temp')
                                                        ->where('created_at','<=',$eval[$j]->updated_at)
                                                        ->where('control_organization_id','=',$co->id)
                                                        ->orderBy('created_at','desc')
                                                        ->select('result')
                                                        ->first();
                                                    if (!empty($cont_per[$j]))
                                                    {
                                                        $cont_per[$j] = $cont_per[$j]->result;
                                                    }
                                                    else
                                                    {
                                                        $cont_per[$j] = NULL;
                                                    }
                                                }
                                                else
                                                {
                                                    $cont_per[$j] = NULL;
                                                }
                                                $proba = $eval[$j]->avg_probability;
                                                $impact = $eval[$j]->avg_impact;
                                                $sev = $proba * $impact;
                                                if ($cont_per[$j] !== NULL)
                                                {
                                                    $residual_risk[$j] = $sev * (1-($cont_per[$j]/100));
                                                }
                                                else
                                                {
                                                    $residual_risk[$j] = NULL;
                                                }
                                            }
                                            else
                                            {
                                                $sev = 'No hay evaluación';
                                                $residual_risk[$j] = 'No hay evaluación';
                                                $cont_per[$j] = NULL;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $sev = 'No se ha evaluado';
                                        $residual_risk[0] = 'No se ha evaluado';
                                        $residual_risk[1] = 'No se ha evaluado';
                                        $residual_risk[2] = 'No se ha evaluado';
                                        $residual_risk[3] = 'No se ha evaluado';
                                        $residual_risk[4] = 'No se ha evaluado';
                                        $cont_per[$j] = NULL;
                                    }
                                    //Agregamos último riesgo residual
                                    if ($last_proba != NULL && $last_impact != NULL)
                                    {
                                        $last_residual_risk = ($last_proba*$last_impact) * (1-($ctrl->cont_percentage/100));
                                    }
                                    //obtenemos hallazgos de control
                                    $issues = \Ermtool\Issue::getIssuesFromControl($org->id,$ctrl->id);
                                    if (!empty($issues))
                                    {
                                        foreach ($issues as $issue)
                                        {
                                            if (Session::get('languaje') == 'es')
                                            {
                                                //clasificación de hallazgo
                                                if ($issue->classification === 0)
                                                {
                                                    $issue->classification = 'Oportunidad de mejora';
                                                }
                                                else if ($issue->classification == 1)
                                                {
                                                    $issue->classification = 'Deficiencia'; 
                                                }
                                                else if ($issue->classification == 2)
                                                {
                                                    $issue->classification = 'Debilidad significativa';
                                                }
                                                else
                                                {
                                                    $issue->classification = 'No se ha definido';
                                                }
                                            }
                                            else //variables en inglés
                                            {
                                            }
                                            //obtenemos plan(es) de acción asociado(s) al hallazgo
                                            $action_plans = \Ermtool\Action_plan::getActionPlanFromIssue2($issue->id);
                                            if (!empty($action_plans))
                                            {
                                                foreach ($action_plans as $plan)
                                                {
                                                    if (Session::get('languaje') == 'es')
                                                    {
                                                        //estado de plan de acción
                                                        if ($plan->status === 0)
                                                        {
                                                            $plan->status = 'En progreso';
                                                        }
                                                        else if ($plan->status == 1)
                                                        {
                                                            $plan->status = 'Cerrado';
                                                        }
                                                        else
                                                        {
                                                            $plan->status = 'No se ha definido';
                                                        }
                                                    }
                                                    else //variables en inglés
                                                    {
                                                    }
                                                    //responsable plan de acción
                                                    if ($plan->stakeholder_id != NULL)
                                                    {
                                                        $plan_resp = \Ermtool\Stakeholder::getName($plan->stakeholder_id);
                                                        $plan_resp_mail = \Ermtool\Stakeholder::getMail($plan->stakeholder_id);
                                                        $plan_resp_mail = $plan_resp_mail->mail;
                                                        //cargo
                                                        $plan_resp_position = \Ermtool\Stakeholder::getPosition($plan->stakeholder_id);
                                                        $plan_resp_position = $plan_resp_position->position;
                                                        if ($plan_resp_position == NULL)
                                                        {
                                                            $plan_resp_position = 'No se ha definido cargo';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $plan_resp = 'No se ha definido responsable';
                                                        $plan_resp_mail = 'No se ha definido responsable';
                                                        $plan_resp_position = 'No se ha definido responsable';
                                                    }
                                                    //obtenemos porcentaje de avance del plan
                                                    //primero, obtenemos la máxima fecha de porcentaje de avance
                                                    $max_date = DB::table('progress_percentage')
                                                                    ->where('action_plan_id','=',$plan->id)
                                                                    ->max('updated_at');
                                                    //obtenemos porcentaje y comentarios
                                                    $per = DB::table('progress_percentage')
                                                            ->where('action_plan_id','=',$plan->id)
                                                            ->where('updated_at','=',$max_date)
                                                            ->select('percentage','comments','updated_at')
                                                            ->first();
                                                    if (!empty($per))
                                                    {
                                                        $percentage = $per->percentage.'%';
                                                        $percentage_comments = $per->comments;
                                                        $percentage_date = $per->updated_at;
                                                    }
                                                    else
                                                    {
                                                        $percentage = 'No hay porcentaje de avance';
                                                        $percentage_comments = 'No hay porcentaje de avance';
                                                        $percentage_date = 'No hay porcentaje de avance';
                                                    }
                                                    if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
                                                    {
                                                        $results[$i] = [
                                                            'Organización' => $org->name,
                                                            'Proceso' => $process->name,
                                                            'Subproceso' => $sub->name,
                                                            'Riesgo' => $risk->name,
                                                            'Descripción Riesgo' => $risk->description,
                                                            'Categoría de Riesgo' => $ppal_category,
                                                            'Subcategoría de Riesgo' => $risk_category,
                                                            'Responsable Riesgo' => $risk_resp,
                                                            'Cargo Responsable' => $risk_resp_position,
                                                            'Correo Responsable' => $risk_resp_mail,
                                                            'Causas' => $causes,
                                                            'Efectos' => $effects,
                                                            'Pérdida Esperada' => $risk->expected_loss,
                                                            'Probabilidad actual' => $last_proba,
                                                            'Impacto actual' => $last_impact,
                                                            'Probabilidad 1' => $eval[0]->avg_probability,
                                                            'Impacto 1' => $eval[0]->avg_impact,
                                                            'Fecha 1' => $eval[0]->updated_at,
                                                            'Probabilidad 2' => $eval[1]->avg_probability,
                                                            'Impacto 2' => $eval[1]->avg_impact,
                                                            'Fecha 2' => $eval[1]->updated_at,
                                                            'Probabilidad 3' => $eval[2]->avg_probability,
                                                            'Impacto 3' => $eval[2]->avg_impact,
                                                            'Fecha 3' => $eval[2]->updated_at,
                                                            'Probabilidad 4' => $eval[3]->avg_probability,
                                                            'Impacto 4' => $eval[3]->avg_impact,
                                                            'Fecha 4' => $eval[3]->updated_at,
                                                            'Probabilidad 5' => $eval[4]->avg_probability,
                                                            'Impacto 5' => $eval[4]->avg_impact,
                                                            'Fecha 5' => $eval[4]->updated_at,
                                                            //'Severidad' => $sev,
                                                            'Control' => $ctrl->name,
                                                            'Descripción Control' => $ctrl->description,
                                                            'Responsable Control' => $control_resp,
                                                            'Correo Responsable Control' => $control_resp_mail,
                                                            'Cargo Responsable Control' => $control_resp_position,
                                                            'Tipo Control' => $ctrl->type,
                                                            'Periodicidad' => $ctrl->periodicity,
                                                            'Propósito' => $ctrl->purpose,
                                                            'Costo Control' => $ctrl->expected_cost,
                                                            'Evidencia Control' => $ctrl->evidence,
                                                            'Comentarios Control' => $ctrl->comments,
                                                            '% de Contribución actual' => $ctrl->cont_percentage.'%',
                                                            'Riesgo Residual actual' => $last_residual_risk,
                                                            '% de Contribución 1' => $cont_per[0].'%',            
                                                            'Riesgo Residual 1' => $residual_risk[0],
                                                            '% de Contribución 2' => $cont_per[1].'%',
                                                            'Riesgo Residual 2' => $residual_risk[1],
                                                            '% de Contribución 3' => $cont_per[2].'%',
                                                            'Riesgo Residual 3' => $residual_risk[2],
                                                            '% de Contribución 4' => $cont_per[3].'%',
                                                            'Riesgo Residual 4' => $residual_risk[3],
                                                            '% de Contribución 5' => $cont_per[4].'%',
                                                            'Riesgo Residual 5' => $residual_risk[4],
                                                            'Hallazgo' => $issue->name,
                                                            'Descripción Hallazgo' => $issue->description,
                                                            'Clasificación Hallazgo' => $issue->classification,
                                                            'Recomendaciones' => $issue->recommendations,
                                                            'Plan de Acción' => $plan->description,
                                                            'Estado Plan' => $plan->status,
                                                            'Responsable Plan Acción' => $plan_resp,
                                                            'Correo Responsable Plan' => $plan_resp_mail,
                                                            'Cargo Responsable Plan' => $plan_resp_position,
                                                            '% de Avance' => $percentage,
                                                            'Fecha de avance' => $percentage_date,
                                                            'Comentarios de avance' => $percentage_comments,
                                                            'Fecha Final Plan' => $plan->final_date
                                                        ];
                                                    }
                                                    else
                                                    {
                                                        //echo $risk->name.'<br>';
                                                        $results[$i] = [
                                                            'org' => $org->name,
                                                            'process' => $process,
                                                            'subprocess' => $sub,
                                                            'risk' => $risk,
                                                            'ppal_category' => $ppal_category,
                                                            'risk_category' => $risk_category,
                                                            'risk_resp' => $risk_resp,
                                                            'risk_resp_position' => $risk_resp_position,
                                                            'risk_resp_mail' => $risk_resp_mail,
                                                            'causes' => $causes,
                                                            'effects' => $effects,
                                                            'eval' => $eval,
                                                            //'score' => $sev,
                                                            'residual_risk' => $residual_risk,
                                                            'control' => $ctrl,
                                                            'control_resp' => $control_resp,
                                                            'control_resp_mail' => $control_resp_mail,
                                                            'control_resp_position' => $control_resp_position,
                                                            'issue' => $issue,
                                                            'action_plan' => $plan,
                                                            'action_plan_resp' => $plan_resp,
                                                            'action_plan_resp_mail' => $plan_resp_mail,
                                                            'action_plan_resp_position' => $plan_resp_position,
                                                            'percentage' => $percentage,
                                                            'percentage_date' => $percentage_date,
                                                            'percentage_comments' => $percentage_comments,
                                                            'last_residual_risk' => $last_residual_risk,
                                                            'last_proba' => $last_proba,
                                                            'last_impact' => $last_impact,
                                                            'cont_per' => $cont_per
                                                        ];
                                                    }
                                                    $i += 1;
                                                }
                                            }
                                            else
                                            {
                                                //echo "NO HAY PLAN DE ACCIÓN<br>";
                                                $plan->description = 'No hay plan de acción';
                                                $plan->status = 'No hay plan de acción';
                                                $plan->final_date = 'No hay plan de acción';
                                                $percentage = 'No hay plan de acción';
                                                $percentage_date = 'No hay plan de acción';
                                                $percentage_comments = 'No hay plan de acción';
                                                $plan_resp = 'No hay plan de acción';
                                                $plan_resp_mail = 'No hay plan de acción';
                                                $plan_resp_position = 'No hay plan de acción';
                                                if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
                                                {
                                                    $results[$i] = [
                                                        'Organización' => $org->name,
                                                        'Proceso' => $process->name,
                                                        'Subproceso' => $sub->name,
                                                        'Riesgo' => $risk->name,
                                                        'Descripción Riesgo' => $risk->description,
                                                        'Categoría de Riesgo' => $ppal_category,
                                                        'Subcategoría de Riesgo' => $risk_category,
                                                        'Responsable Riesgo' => $risk_resp,
                                                        'Cargo Responsable' => $risk_resp_position,
                                                        'Correo Responsable' => $risk_resp_mail,
                                                        'Causas' => $causes,
                                                        'Efectos' => $effects,
                                                        'Pérdida Esperada' => $risk->expected_loss,
                                                        'Probabilidad actual' => $last_proba,
                                                        'Impacto actual' => $last_impact,
                                                        'Probabilidad 1' => $eval[0]->avg_probability,
                                                        'Impacto 1' => $eval[0]->avg_impact,
                                                        'Fecha 1' => $eval[0]->updated_at,
                                                        'Probabilidad 2' => $eval[1]->avg_probability,
                                                        'Impacto 2' => $eval[1]->avg_impact,
                                                        'Fecha 2' => $eval[1]->updated_at,
                                                        'Probabilidad 3' => $eval[2]->avg_probability,
                                                        'Impacto 3' => $eval[2]->avg_impact,
                                                        'Fecha 3' => $eval[2]->updated_at,
                                                        'Probabilidad 4' => $eval[3]->avg_probability,
                                                        'Impacto 4' => $eval[3]->avg_impact,
                                                        'Fecha 4' => $eval[3]->updated_at,
                                                        'Probabilidad 5' => $eval[4]->avg_probability,
                                                        'Impacto 5' => $eval[4]->avg_impact,
                                                        'Fecha 5' => $eval[4]->updated_at,
                                                        //'Severidad' => $sev,
                                                        'Control' => $ctrl->name,
                                                        'Descripción Control' => $ctrl->description,
                                                        'Responsable Control' => $control_resp,
                                                        'Correo Responsable Control' => $control_resp_mail,
                                                        'Cargo Responsable Control' => $control_resp_position,
                                                        'Tipo Control' => $ctrl->type,
                                                        'Periodicidad' => $ctrl->periodicity,
                                                        'Propósito' => $ctrl->purpose,
                                                        'Costo Control' => $ctrl->expected_cost,
                                                        'Evidencia Control' => $ctrl->evidence,
                                                        'Comentarios Control' => $ctrl->comments,
                                                        '% de Contribución actual' => $ctrl->cont_percentage.'%',
                                                        'Riesgo Residual actual' => $last_residual_risk,
                                                        '% de Contribución 1' => $cont_per[0].'%',            
                                                        'Riesgo Residual 1' => $residual_risk[0],
                                                        '% de Contribución 2' => $cont_per[1].'%',
                                                        'Riesgo Residual 2' => $residual_risk[1],
                                                        '% de Contribución 3' => $cont_per[2].'%',
                                                        'Riesgo Residual 3' => $residual_risk[2],
                                                        '% de Contribución 4' => $cont_per[3].'%',
                                                        'Riesgo Residual 4' => $residual_risk[3],
                                                        '% de Contribución 5' => $cont_per[4].'%',
                                                        'Riesgo Residual 5' => $residual_risk[4],
                                                        'Hallazgo' => $issue->name,
                                                        'Descripción Hallazgo' => $issue->description,
                                                        'Clasificación Hallazgo' => $issue->classification,
                                                        'Recomendaciones' => $issue->recommendations,
                                                        'Plan de Acción' => $plan->description,
                                                        'Estado Plan' => $plan->status,
                                                        'Responsable Plan Acción' => $plan_resp,
                                                        'Correo Responsable Plan' => $plan_resp_mail,
                                                        'Cargo Responsable Plan' => $plan_resp_position,
                                                        '% de Avance' => $percentage,
                                                        'Fecha de avance' => $percentage_date,
                                                        'Comentarios de avance' => $percentage_comments,
                                                        'Fecha Final Plan' => $plan->final_date
                                                    ];
                                                }
                                                else
                                                {
                                                    //echo $risk->name.'<br>';
                                                    $results[$i] = [
                                                        'org' => $org->name,
                                                        'process' => $process,
                                                        'subprocess' => $sub,
                                                        'risk' => $risk,
                                                        'ppal_category' => $ppal_category,
                                                        'risk_category' => $risk_category,
                                                        'risk_resp' => $risk_resp,
                                                        'risk_resp_position' => $risk_resp_position,
                                                        'risk_resp_mail' => $risk_resp_mail,
                                                        'causes' => $causes,
                                                        'eval' => $eval,
                                                        'impact' => $impact,
                                                        //'score' => $sev,
                                                        'residual_risk' => $residual_risk,
                                                        'control' => $ctrl,
                                                        'control_resp' => $control_resp,
                                                        'control_resp_mail' => $control_resp_mail,
                                                        'control_resp_position' => $control_resp_position,
                                                        'issue' => $issue,
                                                        'action_plan' => $plan,
                                                        'action_plan_resp' => $plan_resp,
                                                        'action_plan_resp_mail' => $plan_resp_mail,
                                                        'action_plan_resp_position' => $plan_resp_position,
                                                        'percentage' => $percentage,
                                                        'percentage_date' => $percentage_date,
                                                        'percentage_comments' => $percentage_comments,
                                                        'last_residual_risk' => $last_residual_risk,
                                                        'last_proba' => $last_proba,
                                                        'last_impact' => $last_impact,
                                                           'cont_per' => $cont_per
                                                    ];
                                                }
                                                $i += 1;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        //echo "NO HAY HALLAZGOS<br>";
                                        $issue->name = 'No hay hallazgo';
                                        $issue->description = 'No hay hallazgo';
                                        $issue->classification = 'No hay hallazgo';
                                        $issue->recommendations = 'No hay hallazgo';
                                        $plan->description = 'No hay plan de acción';
                                        $plan->status = 'No hay plan de acción';
                                        $plan->final_date = 'No hay plan de acción';
                                        $percentage = 'No hay plan de acción';
                                        $percentage_date = 'No hay plan de acción';
                                        $percentage_comments = 'No hay plan de acción';
                                        $plan_resp = 'No hay plan de acción';
                                        $plan_resp_mail = 'No hay plan de acción';
                                        $plan_resp_position = 'No hay plan de acción';
                                        if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
                                        {
                                            $results[$i] = [
                                                'Organización' => $org->name,
                                                'Proceso' => $process->name,
                                                'Subproceso' => $sub->name,
                                                'Riesgo' => $risk->name,
                                                'Descripción Riesgo' => $risk->description,
                                                'Categoría de Riesgo' => $ppal_category,
                                                'Subcategoría de Riesgo' => $risk_category,
                                                'Responsable Riesgo' => $risk_resp,
                                                'Cargo Responsable' => $risk_resp_position,
                                                'Correo Responsable' => $risk_resp_mail,
                                                'Causas' => $causes,
                                                'Efectos' => $effects,
                                                'Pérdida Esperada' => $risk->expected_loss,
                                                'Probabilidad actual' => $last_proba,
                                                'Impacto actual' => $last_impact,
                                                'Probabilidad 1' => $eval[0]->avg_probability,
                                                'Impacto 1' => $eval[0]->avg_impact,
                                                'Fecha 1' => $eval[0]->updated_at,
                                                'Probabilidad 2' => $eval[1]->avg_probability,
                                                'Impacto 2' => $eval[1]->avg_impact,
                                                'Fecha 2' => $eval[1]->updated_at,
                                                'Probabilidad 3' => $eval[2]->avg_probability,
                                                'Impacto 3' => $eval[2]->avg_impact,
                                                'Fecha 3' => $eval[2]->updated_at,
                                                'Probabilidad 4' => $eval[3]->avg_probability,
                                                'Impacto 4' => $eval[3]->avg_impact,
                                                'Fecha 4' => $eval[3]->updated_at,
                                                'Probabilidad 5' => $eval[4]->avg_probability,
                                                'Impacto 5' => $eval[4]->avg_impact,
                                                'Fecha 5' => $eval[4]->updated_at,
                                                //'Severidad' => $sev,
                                                'Control' => $ctrl->name,
                                                'Descripción Control' => $ctrl->description,
                                                'Responsable Control' => $control_resp,
                                                'Correo Responsable Control' => $control_resp_mail,
                                                'Cargo Responsable Control' => $control_resp_position,
                                                'Tipo Control' => $ctrl->type,
                                                'Periodicidad' => $ctrl->periodicity,
                                                'Propósito' => $ctrl->purpose,
                                                'Costo Control' => $ctrl->expected_cost,
                                                'Descripción Evidencia Control' => $ctrl->evidence,
                                                'Comentarios Control' => $ctrl->comments,
                                                '% de Contribución actual' => $ctrl->cont_percentage.'%',
                                                'Riesgo Residual actual' => $last_residual_risk,
                                                '% de Contribución 1' => $cont_per[0].'%',            
                                                'Riesgo Residual 1' => $residual_risk[0],
                                                '% de Contribución 2' => $cont_per[1].'%',
                                                'Riesgo Residual 2' => $residual_risk[1],
                                                '% de Contribución 3' => $cont_per[2].'%',
                                                'Riesgo Residual 3' => $residual_risk[2],
                                                '% de Contribución 4' => $cont_per[3].'%',
                                                'Riesgo Residual 4' => $residual_risk[3],
                                                '% de Contribución 5' => $cont_per[4].'%',
                                                'Riesgo Residual 5' => $residual_risk[4],
                                                'Hallazgo' => $issue->name,
                                                'Descripción Hallazgo' => $issue->description,
                                                'Clasificación Hallazgo' => $issue->classification,
                                                'Recomendaciones' => $issue->recommendations,
                                                'Plan de Acción' => $plan->description,
                                                'Estado Plan' => $plan->status,
                                                'Responsable Plan Acción' => $plan_resp,
                                                'Correo Responsable Plan' => $plan_resp_mail,
                                                'Cargo Responsable Plan' => $plan_resp_position,
                                                '% de Avance' => $percentage,
                                                'Fecha de avance' => $percentage_date,
                                                'Comentarios de avance' => $percentage_comments,
                                                'Fecha Final Plan' => $plan->final_date
                                            ];
                                        }
                                        else
                                        {
                                            //echo $risk->name.'<br>';
                                            $results[$i] = [
                                                'org' => $org->name,
                                                'process' => $process,
                                                'subprocess' => $sub,
                                                'risk' => $risk,
                                                'ppal_category' => $ppal_category,
                                                'risk_category' => $risk_category,
                                                'risk_resp' => $risk_resp,
                                                'risk_resp_position' => $risk_resp_position,
                                                'risk_resp_mail' => $risk_resp_mail,
                                                'causes' => $causes,
                                                'effects' => $effects,
                                                'eval' => $eval,
                                                //'score' => $sev,
                                                'residual_risk' => $residual_risk,
                                                'control' => $ctrl,
                                                'control_resp' => $control_resp,
                                                'control_resp_mail' => $control_resp_mail,
                                                'control_resp_position' => $control_resp_position,
                                                'issue' => $issue,
                                                'action_plan' => $plan,
                                                'action_plan_resp' => $plan_resp,
                                                'action_plan_resp_mail' => $plan_resp_mail,
                                                'action_plan_resp_position' => $plan_resp_position,
                                                'percentage' => $percentage,
                                                'percentage_date' => $percentage_date,
                                                'percentage_comments' => $percentage_comments,
                                                'last_residual_risk' => $last_residual_risk,
                                                'last_proba' => $last_proba,
                                                'last_impact' => $last_impact,
                                                'cont_per' => $cont_per
                                            ];
                                        }
                                        $i += 1;
                                    }
                                }
                            }
                            else
                            {
                                //echo "NO HAY CONTROLES<br>";
                                $ctrl->name = 'No hay control';
                                $ctrl->description = 'No hay control';
                                $ctrl->type = 'No hay control';
                                $ctrl->purpose = 'No hay control';
                                $ctrl->periodicity = 'No hay control';
                                $ctrl->evidence = 'No hay control';
                                $ctrl->comments = 'No hay control';
                                $ctrl->cont_percentage = 'No hay control';
                                $ctrl->expected_cost = 'No hay control';
                                $control_resp = 'No hay control';
                                $control_resp_mail = 'No hay control';
                                $control_resp_position = 'No hay control';
                                $residual_risk = array();
                                $residual_risk[0] = 'No hay control';
                                $residual_risk[1] = 'No hay control';
                                $residual_risk[2] = 'No hay control';
                                $residual_risk[3] = 'No hay control';
                                $residual_risk[4] = 'No hay control';
                                $last_residual_risk = 'No hay control';
                                $cont_pero = array();
                                $cont_per[0] = 'No hay control';
                                $cont_per[1] = 'No hay control';
                                $cont_per[2] = 'No hay control';
                                $cont_per[3] = 'No hay control';
                                $cont_per[4] = 'No hay control';
                                $issue->name = 'No hay hallazgo';
                                $issue->description = 'No hay hallazgo';
                                $issue->classification = 'No hay hallazgo';
                                $issue->recommendations = 'No hay hallazgo';
                                $plan->description = 'No hay plan de acción';
                                $plan->status = 'No hay plan de acción';
                                $plan->final_date = 'No hay plan de acción';
                                $percentage = 'No hay plan de acción';
                                $percentage_date = 'No hay plan de acción';
                                $percentage_comments = 'No hay plan de acción';
                                $plan_resp = 'No hay plan de acción';
                                $plan_resp_mail = 'No hay plan de acción';
                                $plan_resp_position = 'No hay plan de acción';
                                if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
                                {
                                    $results[$i] = [
                                        'Organización' => $org->name,
                                        'Proceso' => $process->name,
                                        'Subproceso' => $sub->name,
                                        'Riesgo' => $risk->name,
                                        'Descripción Riesgo' => $risk->description,
                                        'Categoría de Riesgo' => $ppal_category,
                                        'Subcategoría de Riesgo' => $risk_category,
                                        'Responsable Riesgo' => $risk_resp,
                                        'Cargo Responsable' => $risk_resp_position,
                                        'Correo Responsable' => $risk_resp_mail,
                                        'Causas' => $causes,
                                        'Efectos' => $effects,
                                        'Pérdida Esperada' => $risk->expected_loss,
                                        'Probabilidad actual' => $last_proba,
                                        'Impacto actual' => $last_impact,
                                        'Probabilidad 1' => $eval[0]->avg_probability,
                                        'Impacto 1' => $eval[0]->avg_impact,
                                        'Fecha 1' => $eval[0]->updated_at,
                                        'Probabilidad 2' => $eval[1]->avg_probability,
                                        'Impacto 2' => $eval[1]->avg_impact,
                                        'Fecha 2' => $eval[1]->updated_at,
                                        'Probabilidad 3' => $eval[2]->avg_probability,
                                        'Impacto 3' => $eval[2]->avg_impact,
                                        'Fecha 3' => $eval[2]->updated_at,
                                        'Probabilidad 4' => $eval[3]->avg_probability,
                                        'Impacto 4' => $eval[3]->avg_impact,
                                        'Fecha 4' => $eval[3]->updated_at,
                                        'Probabilidad 5' => $eval[4]->avg_probability,
                                        'Impacto 5' => $eval[4]->avg_impact,
                                        'Fecha 5' => $eval[4]->updated_at,
                                        //'Severidad' => $sev,
                                        'Control' => $ctrl->name,
                                        'Descripción Control' => $ctrl->description,
                                        'Responsable Control' => $control_resp,
                                        'Correo Responsable Control' => $control_resp_mail,
                                        'Cargo Responsable Control' => $control_resp_position,
                                        'Tipo Control' => $ctrl->type,
                                        'Periodicidad' => $ctrl->periodicity,
                                        'Propósito' => $ctrl->purpose,
                                        'Costo Control' => $ctrl->expected_cost,
                                        'Evidencia Control' => $ctrl->evidence,
                                        'Comentarios Control' => $ctrl->comments,
                                        '% de Contribución' => $ctrl->cont_percentage.'%',
                                        'Riesgo Residual 1' => $residual_risk[0],
                                        'Riesgo Residual 2' => $residual_risk[1],
                                        'Riesgo Residual 3' => $residual_risk[2],
                                        'Riesgo Residual 4' => $residual_risk[3],
                                        'Riesgo Residual 5' => $residual_risk[4],
                                        'Hallazgo' => $issue->name,
                                        'Descripción Hallazgo' => $issue->description,
                                        'Clasificación Hallazgo' => $issue->classification,
                                        'Recomendaciones' => $issue->recommendations,
                                        'Plan de Acción' => $plan->description,
                                        'Estado Plan' => $plan->status,
                                        'Responsable Plan Acción' => $plan_resp,
                                        'Correo Responsable Plan' => $plan_resp_mail,
                                        'Cargo Responsable Plan' => $plan_resp_position,
                                        '% de Avance' => $percentage,
                                        'Fecha de avance' => $percentage_date,
                                        'Comentarios de avance' => $percentage_comments,
                                        'Fecha Final Plan' => $plan->final_date
                                    ];
                                }
                                else
                                {
                                    //echo $risk->name.'<br>';
                                    $results[$i] = [
                                        'org' => $org->name,
                                        'process' => $process,
                                        'subprocess' => $sub,
                                        'risk' => $risk,
                                        'ppal_category' => $ppal_category,
                                        'risk_category' => $risk_category,
                                        'risk_resp' => $risk_resp,
                                        'risk_resp_position' => $risk_resp_position,
                                        'risk_resp_mail' => $risk_resp_mail,
                                        'causes' => $causes,
                                        'effects' => $effects,
                                        'eval' => $eval,
                                        //'score' => $sev,
                                        'residual_risk' => $residual_risk,
                                        'control' => $ctrl,
                                        'control_resp' => $control_resp,
                                        'control_resp_mail' => $control_resp_mail,
                                        'control_resp_position' => $control_resp_position,
                                        'issue' => $issue,
                                        'action_plan' => $plan,
                                        'action_plan_resp' => $plan_resp,
                                        'action_plan_resp_mail' => $plan_resp_mail,
                                        'action_plan_resp_position' => $plan_resp_position,
                                        'percentage' => $percentage,
                                        'percentage_date' => $percentage_date,
                                        'percentage_comments' => $percentage_comments,
                                        'last_residual_risk' => $last_residual_risk,
                                        'last_proba' => $last_proba,
                                        'last_impact' => $last_impact,
                                        'cont_per' => $cont_per
                                    ];
                                }
                                $i += 1;
                            }
                        }
                    }
                    /*else
                    {
                        //echo "NO HAY RIESGOS<br>";
                        $risk->name = 'No hay riesgo';
                        $risk->description = 'No hay riesgo';
                        $proba = 'No hay riesgo';
                        $impact = 'No hay riesgo';
                        $risk_category = 'No hay riesgo';
                        $risk_resp = 'No hay riesgo';
                        $ctrl->name = 'No hay control';
                        $ctrl->description = 'No hay control';
                        $ctrl->type = 'No hay control';
                        $ctrl->purpose = 'No hay control';
                        $ctrl->periodicity = 'No hay control';
                        $ctrl->porcentaje_cont = 'No hay control';
                        $ctrl->evidence = 'No hay control';
                        $ctrl->comments = 'No hay control';
                        $control_resp = 'No hay control';
                        $residual_risk = 'No hay control';
                        $issue->name = 'No hay hallazgo';
                        $issue->description = 'No hay hallazgo';
                        $issue->classification = 'No hay hallazgo';
                        $issue->recommendations = 'No hay hallazgo';
                        $plan->description = 'No hay plan de acción';
                        $plan->status = 'No hay plan de acción';
                        $plan->final_date = 'No hay plan de acción';
                        $percentage = 'No hay plan de acción';
                        $percentage_date = 'No hay plan de acción';
                        $percentage_comments = 'No hay plan de acción';
                        $plan_resp = 'No hay plan de acción';
                        $plan_resp_mail = 'No hay plan de acción';
                    }*/
                }
            }
            //print_r($results);
            if (strstr($_SERVER["REQUEST_URI"],'genexcelconsolidado'))
            {
                return $results;
            }
            else
            {
                return view('reportes.consolidado',['results' => $results]);
            }
        
        //}
        //catch (\Exception $e)
        //{
            //enviarMailSoporte($e);
        //    return view('errors.query',['e' => $e]);
        //}
        }
    }


    public function updateRiskSubprocess()
    {
        DB::transaction(function() {
            $rss = DB::table('risk_subprocess')
                ->get(['id','risk_id','subprocess_id']);

            foreach ($rss as $rs)
            {
                //Obtenemos organization_risk
                $or = \Ermtool\OrganizationRisk::getByRisk($rs->risk_id);

                //Primero actualizamos generalidad de risk_subprocess
                DB::table('risk_subprocess')
                    ->where('id','=',$rs->id)
                    ->update([
                        'organization_risk_id' => $or[0]->id
                    ]); 
            }

            //Ahora actualizamos (por si existe más de un organization_risk por riesgo)
            foreach ($rss as $rs)
            {
                //Volvemos a obtener organization_risk
                $or = \Ermtool\OrganizationRisk::getByRisk($rs->risk_id);

                //ACT 17-10-18: Hacemos esto para cada tupla organization_risk
                foreach ($or as $r)
                {
                    //Vemos si existe en risk_subprocess para organization_risk
                    $rs2 = DB::table('risk_subprocess')
                        ->where('organization_risk_id','=',$r->id)
                        ->where('subprocess_id','=',$rs->subprocess_id)
                        ->first(['id','organization_risk_id']);

                    if (empty($rs2))
                    {
                        DB::table('risk_subprocess')
                            ->insert([
                                'subprocess_id' => $rs->subprocess_id,
                                'organization_risk_id' => $r->id
                            ]);
                    }
                    else
                    {
                        if ($rs2->organization_risk_id == NULL)
                        {
                           DB::table('risk_subprocess')
                                ->where('id','=',$rs2->id)
                                ->update([
                                    'organization_risk_id' => $r->id
                                ]); 
                        }                 
                    }
                }
            }
        });
    }

    //Para carga masiva de koandina
    public function deleteAll()
    {
        DB::transaction(function(){
            //Obtenemos riesgos que no son contables ni de TI
            $risks = DB::table('risks')
                    ->whereNotIn('risk_category_id',[33,35])
                    ->select('id')
                    ->get();

            foreach ($risks as $r)
            {
                //obtenemos orgrisk
                $org_risk = \Ermtool\OrganizationRisk::where('risk_id','=',$r->id)->get();
                $evals = array();

                $controls = array();
                $subs = array();
                $prcs = array();


                foreach ($org_risk as $or)
                {
                    
                    //Eliminamos controlled_risk
                    DB::table('controlled_risk')
                        ->where('organization_risk_id','=',$or->id)
                        ->delete();

                    //Eliminamos issue_organization_risk
                    DB::table('issue_organization_risk')
                        ->where('organization_risk_id','=',$or->id)
                        ->delete();

                    //obtenemos cor, para obtener control_organization
                    $cor = DB::table('control_organization_risk')
                            ->where('organization_risk_id','=',$or->id)
                            ->select('*')
                            ->get();

                    foreach ($cor as $cor2)
                    {

                        array_push($controls,$cor2->control_id);

                        //Seleccionamos control_organizatión, donde control_id sea igual a cor2
                        $co = DB::table('control_organization')
                                ->where('control_id','=',$cor2->control_id)
                                ->select('*')
                                ->get();


                        DB::table('control_eval_risk_temp')
                                ->where('control_id','=',$cor2->control_id)
                                ->delete();


                        foreach ($co as $co2)
                        {
                            //Eliminamos control_eval_risk_temp
                            DB::table('control_eval_risk_temp')
                                ->where('control_organization_id','=',$co2->id)
                                ->delete();

                            //Seleccionamos issues para eliminar action_plan y progress_percentage
                            $issues = DB::table('issues')
                                    ->where('control_id','=',$co2->control_id)
                                    ->where('organization_id','=',$co2->organization_id)
                                    ->get(['id']);


                            //Seleccionamos issues mal cargados
                            $issues2 = DB::table('issues')
                                    ->whereNull('control_id')
                                    ->where('organization_id','=',$co2->organization_id)
                                    ->whereNull('kind')
                                    ->get(['id']);

                            $issues = array_merge($issues,$issues2);
                            $issues = array_unique($issues,SORT_REGULAR);



                            foreach ($issues as $i)
                            {
                                $aps = DB::table('action_plans')
                                    ->where('action_plans.issue_id','=',$i->id)
                                    ->get(['id']);

                                foreach ($aps as $ap)
                                {
                                    DB::table('progress_percentage')
                                        ->where('progress_percentage.action_plan_id','=',$ap->id)
                                        ->delete();
                                }

                                DB::table('action_plans')
                                    ->where('action_plans.issue_id','=',$i->id)
                                    ->delete();
                            }

                            //Eliminamos issues
                            DB::table('issues')
                                ->where('control_id','=',$co2->control_id)
                                ->where('organization_id','=',$co2->organization_id)
                                ->delete(); 


                            //Eliminamos issues 2
                            DB::table('issues')
                                ->whereNull('control_id')
                                ->where('organization_id','=',$co2->organization_id)
                                ->whereNull('kind')
                                ->delete();


                        }

                        //eliminamos co
                        $co = DB::table('control_organization')
                                ->where('control_id','=',$cor2->control_id)
                                ->delete();


                        //Eliminamos issues, action_plans y progress_percentage que sólo esté asociado a control
                        $action_plans = DB::table('action_plans')
                                    ->join('issues','issues.id','=','action_plans.id')
                                    ->where('issues.control_id','=',$cor2->control_id)
                                    ->get(['action_plans.id']);

                        foreach ($action_plans as $ap)
                        {
                            //Eliminamos porcentaje
                            DB::table('progress_percentage')
                                ->where('progress_percentage.action_plan_id','=',$ap->id)
                                ->delete();
                            
                            //Eliminamos plan
                            DB::table('action_plans')
                                ->where('id','=',$ap->id)
                                ->delete();
                        }

                        //Eliminamos issues
                        DB::table('issues')
                            ->where('control_id',$cor2->control_id)
                            ->delete();

                    }

                    //eliminamos co
                    $cor = DB::table('control_organization_risk')
                            ->where('organization_risk_id','=',$or->id)
                            ->delete();

                    //Eliminamos evaluation y sus atributos
                    $eval_risks = DB::table('evaluation_risk')
                            ->where('organization_risk_id','=',$or->id)
                            ->select('id','evaluation_id')
                            ->get();

                    
                    foreach ($eval_risks as $er)
                    {
                        array_push($evals,$er->evaluation_id);
                    }


                    //Obtenemos subprocesses_id a través de risk_subprocess para eliminar
                    $risk_sub = DB::table('risk_subprocess')->where('organization_risk_id','=',$or->id)->get(['subprocess_id']);

                    foreach ($risk_sub as $rs)
                    {
                        $sub = \Ermtool\Subprocess::find($rs->subprocess_id);

                        array_push($subs,$rs->subprocess_id);
                        array_push($prcs,$sub->process_id);
                    }
                    //Eliminamos risk_subprocess
                    $risk_sub = DB::table('risk_subprocess')->where('organization_risk_id','=',$or->id)->delete();
                }

                //Volvemos a eliminar control_eval_risk_temp
                DB::table('control_eval_risk_temp')
                    ->whereIn('control_id',$controls)
                    ->delete();

                //Eliminamos controles
                try{
                    DB::table('controls')
                        ->whereIn('id',$controls)
                        ->delete();
                }
                catch (\Exception $e)
                {
                    echo "No se puede eliminar de uno de los siguientes controles: <br>";
                    //print_r($e);
                    print_r($controls);
                    echo "<br>";
                }

                $evalrisk = DB::table('evaluation_risk')
                    ->whereIn('evaluation_id',$evals)
                    ->get();

                foreach ($evalrisk as $er)
                {
                    //Eliminamos evaluation_risk_stakeholder
                        DB::table('evaluation_risk_stakeholder')
                            ->where('evaluation_risk_id','=',$er->id)
                            ->delete();
                }

                DB::table('evaluation_risk')
                    ->whereIn('evaluation_id',$evals)
                    ->delete();

                //Eliminamos de evaluation_stakeholder
                DB::table('evaluation_stakeholder')
                    ->whereIn('evaluation_id',$evals)
                    ->delete();
                //Eliminamos evaluations
                DB::table('evaluations')
                    ->whereIn('id',$evals)
                    ->delete();


                //Eliminamos org_risk
                $org_risk = DB::table('organization_risk')->where('risk_id','=',$r->id)->delete();

                //Obtenemos subprocesses_id a través de risk_subprocess para eliminar
                $risk_sub = DB::table('risk_subprocess')->where('organization_risk_id','=',$r->id)->get(['subprocess_id']);
                $subs = array();
                $prcs = array();
                foreach ($risk_sub as $rs)
                {
                    $sub = \Ermtool\Subprocess::find($rs->subprocess_id);

                    array_push($subs,$rs->subprocess_id);
                    array_push($prcs,$sub->process_id);
                }
                //Eliminamos risk_subprocess
                $risk_sub = DB::table('risk_subprocess')->where('risk_id','=',$r->id)->delete();

                //Eliminamos cause_risk
                DB::table('cause_risk')->where('risk_id','=',$r->id)->delete();
            }
                
            //eliminamos risk 
            DB::table('risks')
                ->whereNotIn('risk_category_id',[33,35])
                ->delete();

            //Eliminamos organization_subprocess
            DB::table('organization_subprocess')
                ->whereIn('subprocess_id',$subs)
                ->delete();

            //Eliminamos organization_process_stakeholder
            DB::table('organization_process_stakeholder')
                ->whereIn('process_id',$prcs)
                ->delete();


            //Eliminamos hallazgos de riesgos
            //Primero seleccionamos planes de acción
            $risk_issues = DB::table('issues')
                        ->where('issues.kind','=',3)
                        ->get(['issues.id']);

            foreach ($risk_issues as $is)
            {
                //Seleccionamos planes
                $aps = \Ermtool\Action_plan::where('issue_id',$is->id)->get();

                foreach ($aps as $ap)
                {
                    //Eliminamos porcentaje
                    DB::table('progress_percentage')
                        ->where('progress_percentage.action_plan_id','=',$ap->id)
                        ->delete();
                }

                //Eliminamos plan
                DB::table('action_plans')
                    ->where('id','=',$ap->id)
                    ->delete();
            }

            //Eliminamos issues
            DB::table('issues')
                ->where('issues.kind','=',3)
                ->delete();
            
            //Eliminamos subprocesses y processes
            try{
                DB::table('subprocesses')
                    ->whereIn('id',$subs)
                    ->delete();
            }
            catch (\Exception $e)
            {
                echo "No se puede eliminar de uno de los siguientes subprocesos: <br>";
                print_r($subs);

                echo "<br>";

            }

            try{
                DB::table('processes')
                    ->whereIn('id',$prcs)
                    ->delete();
            }
            catch (\Exception $e)
            {
                echo "No se puede eliminar de uno de los siguientes procesos: <br>";
                print_r($prcs);

                echo "<br>";
         
            }
        });
        
    }
}
