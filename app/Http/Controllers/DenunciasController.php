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
                        'description' => $_POST['question_'.$i]
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
                return view('en.denuncias.home');
            }
            else
            {
                Session::flash('message','Preguntas creadas satisfactoriamente');
                return view('denuncias.home');
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
                        'description' => $_POST['answer_'.$q->id],
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

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Your case was successfully created');
            }
            else
            {
                Session::flash('message','Su caso ha sido registrado exitosamente');
            }

            global $id2;
            $id2 = $id;
        });
        
        return view('denuncias.registro2',['id' => $GLOBALS['id2']]);
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
    public function show($id)
    {
        //
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
}
