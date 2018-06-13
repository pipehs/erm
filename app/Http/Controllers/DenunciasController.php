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
        if (Auth::user()->cc_user == 1)
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.denuncias.home');
            }
            else
            {
                return view('denuncias.home');
            }
        }
        else
        {
            return locked();
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
}
