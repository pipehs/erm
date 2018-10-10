<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;

use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;
use DB;
use Auth;
use Crypt;
use Hash;
use Storage;

class DenunciasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {       
        $org_name = \Ermtool\Configuration::where('option_name','organization')->first(['option_value as o']);
        Session::put('org',$org_name->o);

        if (Session::get('languaje') == 'en')
        {
            return view('en.denuncias.home');
        }
        else
        {
            return view('denuncias.home');
        }
    }

    public function Questions()
    {
        if (Auth::user()->cc_user == 1)
        {
            $questions = \Ermtool\CcQuestion::all();

            if (Session::get('languaje') == 'en')
            {
                return view('en.denuncias_configuracion.create_questions',['questions'=>$questions]);
            }
            else
            {
                return view('denuncias_configuracion.create_questions',['questions'=>$questions]);
            }
        }
        else
        {
            return locked();
        }
    }

    //Función para crear preguntas asociadas al canal
    public function storeCcQuestions1()
    {
        //print_r($_POST);
        if (Auth::user()->cc_user == 1)
        {
            $questions = [];

            $i = 1;
            $j = 1;

            while (isset($_GET['cc_question_'.$i]))
            {
                if ($_GET['cc_question_'.$i] != '')
                {
                    $questions[$j] = $_GET['cc_question_'.$i];    
                    $j += 1;
                }

                $i += 1;
            }

            return view('denuncias_configuracion.create_questions2',['questions' => $questions]);
        }
        else
        {
            return locked();
        }   
    }

    public function storeCcQuestions2()
    {
        if (Auth::user()->cc_user == 1)
        {
            DB::transaction(function() {
                //print_r($_POST);
                $i = 1;

                while (isset($_POST['question_'.$i]))
                {
                    $question = \Ermtool\CcQuestion::create([
                        'cc_kind_answer_id' => $_POST['kind_answer_'.$i],
                        'question' => $_POST['question_'.$i],
                        'required' => $_POST['required_'.$i]
                    ]);

                    //vemos si hay alternativas
                    if ($_POST['kind_answer_'.$i] == 2 || $_POST['kind_answer_'.$i] == 3)
                    {
                        $j = 1;
                        while (isset($_POST['question_'.$i.'_choice'.$j]))
                        {
                            if ($_POST['question_'.$i.'_choice'.$j] != '')
                            {
                                \Ermtool\CcPossibleAnswer::create([
                                    'cc_question_id' => $question->id,
                                    'description' => $_POST['question_'.$i.'_choice'.$j]
                                ]);
                            }

                            $j += 1;
                        }
                    }

                    $i += 1;
                }
            });
            

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Question was successfully created');
                return Redirect::to('denuncias');
            }
            else
            {
                Session::flash('message','Preguntas creadas satisfactoriamente');
                return Redirect::to('denuncias');
            }

        }
        else
        {
            return locked();
        }   
    }

    public function registerComplaint()
    {
        $questions = \Ermtool\CcQuestion::all();
        $cc_kinds = \Ermtool\CcKind::lists('name','id');
        foreach ($questions as $q)
        {
            if ($q->cc_kind_answer_id != 1) //no es respuesta de texto
            {
                $p_answers = \Ermtool\CcPossibleAnswer::where('cc_question_id',$q->id)->get();

                if (!empty($p_answers))
                {
                    $q->p_answers = $p_answers;
                }
                else
                {
                    $q->p_answers = array();
                }
            }

            $q->required2 = $q->required == 1 ? 'required' : '';
        }

        return view('denuncias.registro',['questions' => $questions,'cc_kinds' => $cc_kinds]);

    }

    public function registerComplaint2(Request $request)
    {
        global $request2;
        $request2 = $request;
        //print_r($_POST);
        DB::transaction(function(){
            if ($_POST['anonymous'] == 2) //No es anónimo
            {
                if (isset($_POST['email']))
                {
                    //Vemos si existe denunciante o se debe crear
                    $complainant = \Ermtool\CcComplainant::where('email',$_POST['email'])->first();

                    if (empty($complainant))
                    {
                        //Creamos denunciante
                        $complainant = \Ermtool\CcComplainant::create([
                            'name' => $_POST['name'] != '' ? $_POST['name'] : NULL,
                            'surnames' => $_POST['surnames'] != '' ? $_POST['surnames'] : NULL,
                            'telephone' => $_POST['telephone'] != '' ? $_POST['telephone'] : NULL,
                            'email' => $_POST['email']
                        ]);
                    }

                    $complainant_id = $complainant->id;
                }
                else
                {
                    $complainant_id = NULL;
                }
            }
            else
            {
                $complainant_id = NULL;
            }

            $id = rand(); //Generamos id aleatoriamente
            //Vemos si ya existe este id
            while (!empty(\Ermtool\CcCase::find($id)))
            {
                $id = rand(10000,999999999); //generamos hasta que no exista
            }

            //Obtenemos status (siempre el primero será el predeterminado)
            $cc_status = \Ermtool\CcStatus::where('cc_kind_id',$_POST['cc_kind_id'])->first();
            //Creamos denuncia (o caso)
            $case = \Ermtool\CcCase::create([
                'id' => $id,
                'cc_complainant_id' => $complainant_id,
                'cc_kind_id' => $_POST['cc_kind_id'],
                'password' => bcrypt($_POST['password']),
                'cc_status_id' => $cc_status->id
            ]);

            //Guardamos cada respuesta asociada al caso
            $cc_questions = \Ermtool\CcQuestion::all();

            foreach ($cc_questions as $q)
            {
                if (isset($_POST['answer_'.$q->id]) && $_POST['answer_'.$q->id] != '')
                {    
                    \Ermtool\CcAnswer::create([
                        'cc_case_id' => $id,
                        'cc_question_id' => $q->id,
                        'description' => Crypt::encrypt($_POST['answer_'.$q->id]),
                    ]);
                }
            }

            if($GLOBALS['request2']->file('evidence_doc') != NULL)
            {
                foreach ($GLOBALS['request2']->file('evidence_doc') as $evidence)
                {
                    if ($evidence != NULL)
                    {
                        upload_file($evidence,'canal_denuncias',$id);
                    }
                }                    
            }

            global $id2;
            $id2 = $id;
        });
        if (Session::get('languaje') == 'en')
        {
            return json_encode(['id' => $GLOBALS['id2'], 'response' => 0, 'response_description' => 'Your case was successfully created']);
        }
        else
        {
            return json_encode(['id' => $GLOBALS['id2'], 'response' => 0, 'response_description' => 'Su caso ha sido registrado exitosamente']);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCase($id,$password,$kind)
    {
        //Obtenemos caso
        $case = \Ermtool\CcCase::find($id);
        if (!empty($case))
        {
            if ($kind == 1)
            {
                $checked = Hash::check($password, $case->password);
            }
            else
            {
                $checked = True;
            }
            if ($checked)
            {
                $case->questions = $this->getQuestionsAndAnswers($id);

                //Vemos denunciante (si es que no es anónimo)
                if ($case->cc_complainant_id != null)
                {
                    $complainant = \Ermtool\CcComplainant::find($case->cc_complainant_id);
                    $case->complainant = $complainant->name.' '.$complainant->surnames;
                }
                else
                {
                    $case->complainant = 'Anónimo';
                }

                //Estado
                $case->status = \Ermtool\CcStatus::where('id',$case->cc_status_id)->value('description');
                //Clasificación
                if ($case->cc_classification_id == NULL)
                {
                    $case->classification = 'No se ha clasificado';
                }
                else
                {
                    $case->classification = \Ermtool\CcClassification::where('id',$case->cc_status_id)->value('name');
                }

                //Fecha ordenada
                $case->created_at = new DateTime($case->created_at);
                $case->created_at = date_format($case->created_at,"d-m-Y").' a las '.date_format($case->created_at,"H:i:s");

                //Obtenemos mensajes asociados al caso
                $messages = \Ermtool\CcMessage::where('cc_case_id',$case->id)->orderBy('created_at','asc')->get();

                foreach ($messages as $m)
                {
                    if ($m->user_id != null)
                    {
                        $user = \Ermtool\User::find($m->user_id);
                        //$m->sender = 'Administrador';
                        $m->sender = $user->name.' '.$user->surnames;
                    }
                    else
                    {
                        $m->sender = $case->complainant;
                    }

                    $m->description = Crypt::decrypt($m->description);
                    $m->files = Storage::files('cc_mensajes/'.$m->id);

                    $m->created_at = new DateTime($m->created_at);
                    $m->created_at = date_format($m->created_at,"d-m-Y").' a las '.date_format($m->created_at,"H:i:s");
                }

                $case->messages = $messages;

                if (Session::get('languaje') == 'en')
                {
                    return json_encode(['case' => $case, 'response' => 0]);
                }
                else
                {
                    return json_encode(['case' => $case, 'response' => 0]);
                } 
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return json_encode(['response' => 99, 'response_description' => 'Incorrect password']);
                }
                else
                {
                    return json_encode(['response' => 99, 'response_description' => 'Contraseña incorrecta']);
                }
            }
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return json_encode(['response' => 99, 'response_description' => 'Case not found']);
            }
            else
            {
                return json_encode(['response' => 99, 'response_description' => 'Caso no encontrado']);
            }
        }
    }

    public function sendUserMessage(Request $request)
    {
        //print_r($request->file('evidence_doc'));
        global $request2;
        $request2 = $request;
        global $m;
        global $c;
        global $f;
        DB::transaction(function(){
            //vemos si lo está enviando usuario o denunciante
            if ($_POST['kind'] == 1) //denunciante
            {
                //Guardamos mensaje
                $message = \Ermtool\CcMessage::create([
                    'cc_case_id' => $_POST['case_id'],
                    'description' => Crypt::encrypt($_POST['new_message'])
                ]);

                //Retornamos autor si es que hay
                $case = \Ermtool\CcCase::find($_POST['case_id']);

                if ($case->cc_complainant_id != null)
                {
                    $complainant = \Ermtool\CcComplainant::find($case->cc_complainant_id);
                    $GLOBALS['c'] = $complainant->name.' '.$complainant->surnames;
                }
                else
                {
                    $GLOBALS['c'] = 'Anónimo';
                }
            }
            else if ($_POST['kind'] == 2) //admin
            {
                //Guardamos mensaje
                $message = \Ermtool\CcMessage::create([
                    'cc_case_id' => $_POST['case_id'],
                    'user_id' => Auth::user()->id,
                    'description' => Crypt::encrypt($_POST['new_message'])
                ]);

                $GLOBALS['c'] = Auth::user()->name.' '.Auth::user()->surnames;
            }

            $GLOBALS['m'] = Crypt::decrypt($message->description);

            if($GLOBALS['request2']->file('evidence_doc') != NULL)
            {
                foreach ($GLOBALS['request2']->file('evidence_doc') as $evidencedoc)
                {
                    if ($evidencedoc != NULL)
                    {
                        upload_file($evidencedoc,'cc_mensajes',$message->id);
                    }
                }                    
            }

            $files = Storage::files('cc_mensajes/'.$message->id);

            $GLOBALS['f'] = $files;
        });

        return json_encode(['message' => $m, 'response' => 0, 'sender' => $c, 'files' => $f, 'kind' => $_POST['kind']]);
    }

    public function indexTracking()
    {
        if (Auth::user()->cc_user == 1)
        {
            //obtenemos todos los casos
            $cases = \Ermtool\CcCase::all();
            $questions = \Ermtool\CcQuestion::all();

            foreach ($cases as $c)
            {
                //Vemos si hay autor
                if ($c->cc_complainant_id != null)
                {
                    $complainant = \Ermtool\CcComplainant::find($c->cc_complainant_id);
                    $c->complainant = $complainant->name.' '.$complainant->surnames;
                }
                else
                {
                    $c->complainant = 'Anónimo';
                }

                //obtenemos preguntas y respuestas asociadas
                $c->questions = $this->getQuestionsAndAnswers($c->id);

                //obtenemos tipo
                $c->kind = \Ermtool\CcKind::where('id',$c->cc_kind_id)->value('name');

                //Fecha ordenada
                $c->created_at = new DateTime($c->created_at);
                $c->created_at = date_format($c->created_at,"d-m-Y").' a las '.date_format($c->created_at,"H:i:s");
            }


            if (Session::get('languaje') == 'en')
            {
                return view('en.denuncias.seguimiento_admin',['cases' => $cases,'questions' => $questions]);
            }
            else
            {
                return view('denuncias.seguimiento_admin',['cases' => $cases,'questions' => $questions]);
            }
        }
        else
        {
            return locked();
        }
    }

    public function trackingCase($id)
    {
        $case = \Ermtool\CcCase::find($id);
        $password = \Ermtool\CcCase::where('id',$id)->value('password');

        if (Session::get('languaje') == 'en')
        {
            return view('en.denuncias.seguimiento_admin2',['id' => $id,'password' => $password]);
        }
        else
        {
            return view('denuncias.seguimiento_admin2',['id' => $id,'password' => $password]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function indexConfiguration()
    {
        if (!Auth::guest())
        {
            if (Auth::user()->cc_user == 1)
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.denuncias_configuracion.configuration');
                }
                else
                {
                    return view('denuncias_configuracion.configuration');
                }
            }
            else
            {
                return locked();
            }
        }
        else
        {
            return Redirect::route('/');
        }
    }
    public function indexConfigurationKinds()
    {
        if (!Auth::guest())
        {
            if (Auth::user()->cc_user == 1)
            {
                $cc_kinds = \Ermtool\CcKind::all();
                $cc_roles = \Ermtool\CcRole::lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.denuncias_configuracion.configuration_kinds',['cc_kinds' => $cc_kinds, 'cc_roles' => $cc_roles]);
                }
                else
                {
                    return view('denuncias_configuracion.configuration_kinds',['cc_kinds' => $cc_kinds, 'cc_roles' => $cc_roles]);
                }
            }
            else
            {
                return locked();
            }
        }
        else
        {
            return Redirect::route('/');
        }
    }

    public function storeConfigurationKinds()
    {
        //print_r($_POST);
        DB::transaction(function(){

            $cc_kinds = \Ermtool\CcKind::all();

            //Recorremos todos los tipos de casos
            foreach ($cc_kinds as $k)
            {
                $k->responsable_mail = $_POST['email_'.$k->id] != '' ? $_POST['email_'.$k->id] : NULL;

                //Actualizamos estados asociados al tipo de caso
                foreach ($k->ccStatus as $s)
                {
                    $s->description = $_POST['status_'.$k->id.'_'.$s->id] != '' ? $_POST['status_'.$k->id.'_'.$s->id] : NULL;

                    $s->save();
                }

                //Vemos si se agregaron nuevos estados
                $i = 1;
                while (isset($_POST['new_status_'.$k->id.'_'.$i]))
                {
                    //Si se agregó nombre y descripción, entonces se crea
                    if ($_POST['new_status_'.$k->id.'_'.$i] != '')
                    {
                        \Ermtool\CcStatus::create([
                            'description' => $_POST['new_status_'.$k->id.'_'.$i],
                            'cc_kind_id' => $k->id
                        ]);
                    }

                    $i += 1;
                }

                foreach ($k->ccClassifications as $c)
                {
                    $c->name = $_POST['name_class_'.$k->id.'_'.$c->id] != '' ? $_POST['name_class_'.$k->id.'_'.$c->id] : NULL;
                    $c->description = $_POST['description_class_'.$k->id.'_'.$c->id] != '' ? $_POST['description_class_'.$k->id.'_'.$c->id] : NULL;

                    //Vemos si existe el rol ingresado (si es que se ingresó)
                    if (isset($_POST['role_class_'.$k->id.'_'.$c->id]) && $_POST['role_class_'.$k->id.'_'.$c->id] != '')
                    {
                        $ccRole = \Ermtool\CcRole::find($_POST['role_class_'.$k->id.'_'.$c->id]);

                        if (empty($ccRole)) //verificamos que no se esté ingresando nombre
                        {   
                            $ccRole = \Ermtool\CcRole::where('name',$_POST['role_class_'.$k->id.'_'.$c->id])->first();

                            if (empty($ccRole)) //Creamos rol
                            {
                               $ccRole = \Ermtool\CcRole::create([
                                    'name' => $_POST['role_class_'.$k->id.'_'.$c->id]
                                ]); 
                            }
                        }
                       
                        $c->cc_role_id = $ccRole->id;
                    }
                     
                    $c->save();
                }

                //Vemos si se agregaron nuevas clasificaciones
                $i = 1;
                while (isset($_POST['new_name_class_'.$k->id.'_'.$i]))
                {
                    //Si se agregó nombre y descripción, entonces se crea
                    if ($_POST['new_name_class_'.$k->id.'_'.$i] != '' && $_POST['new_description_class_'.$k->id.'_'.$i] != '')
                    {
                        //Vemos si existe el rol ingresado (si es que se ingresó)
                        if (isset($_POST['new_role_class_'.$k->id.'_'.$i]) && $_POST['new_role_class_'.$k->id.'_'.$i] != '')
                        {
                            $ccRole = \Ermtool\CcRole::find($_POST['new_role_class_'.$k->id.'_'.$i]);

                            if (empty($ccRole)) //verificamos que no se esté ingresando nombre
                            {
                                $ccRole = \Ermtool\CcRole::where('name',$_POST['new_role_class_'.$k->id.'_'.$i])->first();

                                if (empty($ccRole)) //Creamos rol
                                {
                                    $ccRole = \Ermtool\CcRole::create([
                                        'name' => $_POST['new_role_class_'.$k->id.'_'.$i]
                                    ]);
                                }
                            }
                        }
                        \Ermtool\CcClassification::create([
                            'name' => $_POST['new_name_class_'.$k->id.'_'.$i],
                            'description' => $_POST['new_description_class_'.$k->id.'_'.$i],
                            'cc_kind_id' => $k->id,
                            'cc_role_id' => $ccRole->id
                        ]);
                    }

                    $i += 1;
                }

                $k->save();
            }

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Configuration was successfully updated');
            }
            else
            {
                Session::flash('message','La configuración fue actualizada correctamente');
            }

        });

        return Redirect::to('denuncias');  
    }

    public function getQuestionsAndAnswers($case_id)
    {
        $questions = \Ermtool\CcQuestion::all();

        foreach ($questions as $q)
        {
            //Vemos si es respuesta de texto plano o no
            $answer = \Ermtool\CcAnswer::where('cc_question_id',$q->id)->where('cc_case_id',$case_id)->value('description');

            if ($answer != null)
            {
                try
                {
                    $answer = Crypt::decrypt($answer);
                }
                catch (\Exception $e) //En caso de que no se encuentre encriptada la respuesta
                {
                    $answer = $answer;
                }

                if ($q->cc_kind_answer_id == 2 || $q->cc_kind_answer_id == 3) //radio o checkbox
                {
                    $answer = \Ermtool\CcPossibleAnswer::where('id',$answer)->value('description');
                }

                $q->answer = $answer;
            }
            else
            {
                $q->answer = NULL;
            }        
        }
        return $questions;
    }
}
