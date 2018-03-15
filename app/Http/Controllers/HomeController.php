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

                //ACT 27-12-17: Eliminamos saltos de línea de riesgos
                foreach ($risks_temp as $r)
                {
                    $r->name = eliminarSaltos($r->name);
                    $r->description = eliminarSaltos($r->description);
                }
                
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
            //enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function reporteConsolidado()
    {   
        if (Auth::guest())
        {
            return view('login');
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

                            $ppal_category = \Ermtool\Risk_category::name($ppal_category->id);
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

                            //obtenemos última evaluación
                            $last_evaluation = \Ermtool\Evaluation::getLastEvaluation($risk->id);
                            if ($last_evaluation == NULL )
                            {
                                $proba = 'No evaluado';
                                $impact = 'No evaluado';
                            }
                            else
                            {
                                $proba = $last_evaluation->avg_probability;
                                $impact = $last_evaluation->avg_impact;
                            }

                            //obtenemos controles asociados al riesgo
                            $controls = \Ermtool\Control::getControlsFromRisk($org->id,$risk->risk_id);

                            if (!empty($controls))
                            {
                                foreach ($controls as $ctrl)
                                {
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
                                    }

                                    //seteamos riesgo residual
                                    if ($last_evaluation != NULL)
                                    {
                                        //guardamos proba e impact, para poder enviar los datos en caso que no haya evaluación
                                        $proba = $last_evaluation->avg_probability;
                                        $impact = $last_evaluation->avg_impact;
                                        $sev = $proba * $impact;
                                        $residual_risk = $sev * (1-($ctrl->porcentaje_cont/100));
                                    }
                                    else
                                    {
                                        $sev = 'No se ha evaluado';
                                        $residual_risk = 'No se ha evaluado';
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
                                                            'Probabilidad' => $proba,
                                                            'Impacto' => $impact,
                                                            'Severidad' => $sev,
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
                                                            '% de Contribución' => $ctrl->porcentaje_cont.'%',            
                                                            'Riesgo Residual' => $residual_risk,
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
                                                            'probability' => $proba,
                                                            'impact' => $impact,
                                                            'score' => $sev,
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
                                                            'percentage_comments' => $percentage_comments
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
                                                        'Probabilidad' => $proba,
                                                        'Impacto' => $impact,
                                                        'Severidad' => $sev,
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
                                                        '% de Contribución' => $ctrl->porcentaje_cont.'%',
                                                        'Riesgo Residual' => $residual_risk,
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
                                                        'probability' => $proba,
                                                        'impact' => $impact,
                                                        'score' => $sev,
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
                                                        'percentage_comments' => $percentage_comments
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
                                                'Probabilidad' => $proba,
                                                'Impacto' => $impact,
                                                'Severidad' => $sev,
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
                                                '% de Contribución' => $ctrl->porcentaje_cont.'%',
                                                'Riesgo Residual' => $residual_risk,
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
                                                'probability' => $proba,
                                                'impact' => $impact,
                                                'score' => $sev,
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
                                                'percentage_comments' => $percentage_comments
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
                                $ctrl->porcentaje_cont = 'No hay control';
                                $ctrl->expected_cost = 'No hay control';
                                $control_resp = 'No hay control';
                                $control_resp_mail = 'No hay control';
                                $control_resp_position = 'No hay control';
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
                                        'Probabilidad' => $proba,
                                        'Impacto' => $impact,
                                        'Severidad' => $sev,
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
                                        '% de Contribución' => $ctrl->porcentaje_cont.'%',
                                        'Riesgo Residual' => $residual_risk,
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
                                        'probability' => $proba,
                                        'impact' => $impact,
                                        'score' => $sev,
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
                                        'percentage_comments' => $percentage_comments
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
}
