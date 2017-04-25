<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Redirect;
use Session;
use dateTime;
use Storage;
use Auth;

class KriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $kri = NULL;

            //seleccionamos todos los kri
            $kri_query = DB::table('kri')
                    ->join('risks','risks.id','=','kri.risk_id')
                    ->select('kri.*','risks.name as risk_name','risks.stakeholder_id as risk_stake')
                    ->get();

            $i=0;
            foreach ($kri_query as $k)
            {

                $tipo = $k->type;
                $uni_med = $k->uni_med;
                $periodicity = $k->periodicity;

                if ($k->kri_last_evaluation === NULL)
                {
                    if (Session::get('languaje') == 'en')
                    {
                        $last_eval = "Still have not evaluated";
                        $date_last = "Still have not evaluated";
                        $eval = 3; //probamos con el valor 3 ya que escribiendo "Ninguna" lo toma como = a 0
                        $description_eval = "None";
                        $date_min = null;
                        $date_max = null;
                    }
                    else
                    {
                        $last_eval = "Aun no ha sido evaluado";
                        $date_last = "Aun no ha sido evaluado";
                        $eval = 3; //probamos con el valor 3 ya que escribiendo "Ninguna" lo toma como = a 0
                        $description_eval = "Ninguna";
                        $date_min = null;
                        $date_max = null;
                    }
                }
                else
                {
                    $last_eval = $k->kri_last_evaluation;
                    $date_last0 = new DateTime($k->date_evaluation);
                    $date_last = date_format($date_last0, 'd-m-Y');
                    
                    //obtenemos periodo de evaluación

                    $periodo = DB::table('measurements')
                                ->where('kri_id','=',$k->id)
                                ->where('created_at','=',$k->date_evaluation)
                                ->select('date_min','date_max')
                                ->first();

                    $date_min_temp = new DateTime($periodo->date_min);
                    $date_min = date_format($date_min_temp,'d-m-Y');

                    $date_max_temp = new DateTime($periodo->date_max);
                    $date_max = date_format($date_max_temp,'d-m-Y');

                    //calculamos evaluacion (color)

                    $eval = $this->calc_sem($last_eval,$k->green_min,$k->interval_min,$k->interval_max,$k->red_max);
                    
                    if ($eval == 0) //0: verde
                    {
                        $description_eval = $k->description_green;
                    }
                    else if ($eval == 1) //1: amarillo
                    {
                        $description_eval = $k->description_yellow;
                    }
                    else if ($eval == 2) //2: rojo
                    {
                        $description_eval = $k->description_red;
                    }
                }

                //$created_at = date('d-m-Y',strtotime($k->created_at));
                $lala = new DateTime($k->created_at);
                $created_at = date_format($lala,"d-m-Y");

                //obtenemos stakeholder
                if ($k->risk_stake == 0 || $k->risk_stake == NULL)
                {
                    $stakeholder = $k->risk_stake;
                }
                 else
                {
                    //obtenemos stakeholder
                    $stake = DB::table('stakeholders')
                                ->where('id',$k->risk_stake)
                                ->select(DB::raw("CONCAT(name, ' ', surnames) AS full_name"))
                                ->first();
                    $stakeholder = $stake->full_name;
                }
                $kri[$i] = [
                    'id' => $k->id,
                    'name' => $k->name,
                    'description' => $k->description,
                    'last_eval' => $last_eval,
                    'date_last' => $date_last,
                    'uni_med' => $uni_med,
                    'created_at' => $created_at, 
                    'type' => $tipo,
                    'periodicity' => $periodicity,
                    'risk' => $k->risk_name,
                    'risk_stakeholder' => $stakeholder,
                    'eval' => $eval,
                    'description_eval' => $description_eval,
                    'last_evaluation' => $last_eval,
                    'date_min' => $date_min,
                    'date_max' => $date_max,
                ];

                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.index',['kri'=>$kri]);
            }
            else
            {
                return view('kri.index',['kri'=>$kri]);   
            }
        }
    }

    public function index2()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $kri_risk_subprocess = array();
            $kri_objective_risk = array();
            //seleccionamos todos los riesgos de proceso que estén enlazados a algún riesgo de negocio
            $risks = DB::table('objective_subprocess_risk')
                        ->join('risks as risk_subprocess','risk_subprocess.id','=','objective_subprocess_risk.risk_subprocess_id')
                        ->select('risk_subprocess.id as id','risk_subprocess.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($risks as $risk)
            {
                $kri_risk_subprocess[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            //ahora seleccionamos todos los riesgos de negocio
            $obj_risks = DB::table('objective_risk')
                        ->join('risks','risks.id','=','objective_risk.risk_id')
                        ->select('risks.id as id','risks.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($obj_risks as $risk)
            {
                $kri_objective_risk[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.index2',['risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);
            }
            else
            {
                return view('kri.index2',['risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
        /*
        if (strpos($id,"obj")) //se está creando un KRI para un riesgo de negocio directamente
        {
            //separamos id 
            $id2 = explode('_',$id);

            //obtenemos nombre del riesgo
            $name = DB::table('risks')
                        ->where('risks.id','=',$id2[0])
                        ->value('risks.name');

            return view('kri.create',['obj_risk_id' => $id2[0], 'name' => $name]);
        }

        else if (strpos($id,"sub")) //si es que se está creando KRI para un riesgo de proceso ASOCIADO a un riesgo de negocio
        {
            //separamos id 
            $id2 = explode('_',$id);

            //obtenemos nombre del riesgo //FALTARIA AGREGAR EL NOMBRE DEL RIESGO DE NEGOCIO
            $name = DB::table('risks')
                        ->where('risks.id','=',$id2[0])
                        ->value('risks.name');

            return view('kri.create',['sub_risk_id' => $id2[0], 'name' => $name]);
        }
        */

            //seleccionamos riesgos de negocio y riesgos de proceso asociados a un riesgo de negocio
            $kri_risk_subprocess = array();
            $kri_objective_risk = array();
            //seleccionamos todos los riesgos de proceso que estén enlazados a algún riesgo de negocio
            $risks = DB::table('objective_subprocess_risk')
                        ->join('risks','risks.id','=','objective_subprocess_risk.risk_subprocess_id')
                        ->select('risks.id as id','risks.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($risks as $risk)
            {
                $kri_risk_subprocess[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            //ahora seleccionamos todos los riesgos de negocio
            $obj_risks = DB::table('objective_risk')
                        ->join('risks','risks.id','=','objective_risk.risk_id')
                        ->select('risks.id as id','risks.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($obj_risks as $risk)
            {
                $kri_objective_risk[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.create',['risk_subprocess' => $kri_risk_subprocess, 'objective_risk' => $kri_objective_risk]);
            }
            else
            {
                return view('kri.create',['risk_subprocess' => $kri_risk_subprocess, 'objective_risk' => $kri_objective_risk]);
            }
        }
    }

    public function create2($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.create',['risk_id' => $id]);
            }
            else
            {
                return view('kri.create',['risk_id' => $id]);   
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);

            if ($_POST['uni_med'] == 0) //porcentaje, se deben volver a validar las medidas
            {
                //Validación: Si la validación es pasada, el código continua
                $this->validate($request, [
                    'green_min' => 'required|max:100|min:0',
                    'interval_min' => 'required|max:100|min:0',
                    'interval_max' => 'required|max:100|min:0',
                    'red_max' => 'required|max:100|min:0',
                ]);
            }
            

            //creamos NUEVO KRI
            DB::transaction(function() {

                $in = DB::table('kri')
                        ->insertGetId([
                            'risk_id' => $_POST['risk_id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description'],
                            'type' => $_POST['type'],
                            'periodicity' => $_POST['periodicity'],
                            'uni_med' => $_POST['uni_med'],
                            'min_max' => $_POST['min_max'],
                            'green_min' => $_POST['green_min'],
                            'description_green' => $_POST['description_green'],
                            'interval_min' => $_POST['interval_min'],
                            'interval_max' => $_POST['interval_max'],
                            'description_yellow' => $_POST['description_yellow'],
                            'red_max' => $_POST['red_max'],
                            'description_red' => $_POST['description_red'],
                            'created_at' => date('Ymd H:i:s'),
                            'updated_at' => date('Ymd H:i:s')
                        ]);
                

                if (isset($in))
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','KRI successfully created');
                    }
                    else
                    {
                        Session::flash('message','KRI generado correctamente');   
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('errors','Error storing KRI');
                    }
                    else
                    {
                        Session::flash('errors','Error al grabar KRI');   
                    }
                }
            });

            return Redirect::to('kri');
        }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //seleccionamos riesgos de negocio y riesgos de proceso asociados a un riesgo de negocio
            $kri_risk_subprocess = array();
            $kri_objective_risk = array();
            //seleccionamos todos los riesgos de proceso que estén enlazados a algún riesgo de negocio
            $risks = DB::table('objective_subprocess_risk')
                        ->join('risks as risk_subprocess','risk_subprocess.id','=','objective_subprocess_risk.risk_subprocess_id')
                        ->select('risk_subprocess.id as id','risk_subprocess.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($risks as $risk)
            {
                $kri_risk_subprocess[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            //ahora seleccionamos todos los riesgos de negocio
            $obj_risks = DB::table('objective_risk')
                        ->join('risks','risks.id','=','objective_risk.risk_id')
                        ->select('risks.id as id','risks.name as name')
                        ->distinct()
                        ->get();

            $i = 0;
            foreach ($obj_risks as $risk)
            {
                $kri_objective_risk[$i] = [
                        'id' => $risk->id,
                        'name' => $risk->name,
                ];

                $i += 1;
            }

            $kri = \Ermtool\KRI::find($id);

            //redondeamos valores de kri (ya que no sirvio redondearlos al guardar)
            $kri->green_min = round($kri->green_min,1);
            $kri->interval_min = round($kri->interval_min,1);
            $kri->interval_max = round($kri->interval_max,1);
            $kri->red_max  = round($kri->red_max,1);

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.edit',['kri'=>$kri,'risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);
            }
            else
            {
                return view('kri.edit',['kri'=>$kri,'risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);   
            }
        }
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            if ($_POST['uni_med'] == 0) //porcentaje, se deben volver a validar las medidas
            {
                //Validación: Si la validación es pasada, el código continua
                $this->validate($request, [
                    'green_min' => 'required|numeric|between:0,100',
                    'interval_min' => 'required|numeric|between:0,100',
                    'interval_max' => 'required|numeric|between:0,100',
                    'red_max' => 'required|numeric|between:0,100',
                ]);
            }

            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $kri = \Ermtool\KRI::find($GLOBALS['id1']);

                $kri->risk_id = $_POST['risk_id'];
                $kri->name = $_POST['name'];
                $kri->description = $_POST['description'];
                $kri->type = $_POST['type'];
                $kri->min_max = $_POST['min_max'];
                $kri->uni_med = $_POST['uni_med'];
                $kri->green_min = $_POST['green_min'];
                $kri->description_green = $_POST['description_green'];
                $kri->interval_min = $_POST['interval_min'];
                $kri->interval_max = $_POST['interval_max'];
                $kri->description_yellow = $_POST['description_yellow'];
                $kri->red_max = $_POST['red_max'];
                $kri->description_red = $_POST['description_red'];

                $kri->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','KRI successfully updated');
                }
                else
                {
                    Session::flash('message','KRI actualizado correctamente');   
                }
            });

            return Redirect::to('kri');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        global $id1;
        $id1 = $id;
        global $res;
        $res = 1;

        DB::transaction(function() {

            //vemos si es que tiene alguna medición
            $rev = DB::table('measurements')
                    ->where('kri_id','=',$GLOBALS['id1'])
                    ->select('id')
                    ->get();

            if (empty($rev))
            {
                //si es que se llega a esta instancia, se puede eliminar
                DB::table('kri')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                $GLOBALS['res'] = 0;
                
            }
        });

        return $res;
    }

    //vista de enlazador de riesgos de proceso a riesgos de negocio
    public function enlazar()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $risk_subprocess = array();
            $objective_risk = array();
            $enlaces = array();

            //primero obtenemos riesgos de subprocesso
            //ACT 10-04-17: HAY QUE HACER GROUP_BY RISK (procesos u objetivos son aparte)
            $risk = \Ermtool\Risk::getRiskSubprocess(NULL);
            $i = 0;
            foreach ($risk as $r)
            {
                $risk_subprocess[$i] = ['id' => $r->risk_id,
                                        'name' => $r->risk_name,
                                        'description' => $r->description];

                $i += 1;
            }

            //obtenemos riesgos de negocio
            //primero obtenemos riesgos de subprocesso
            $risk = \Ermtool\Risk::getObjectiveRisks(NULL);
            $i = 0;
            foreach ($risk as $r)
            {
                $objective_risk[$i] = ['id' => $r->risk_id,
                                        'name' => $r->risk_name,
                                        'description' => $r->description];

                $i += 1;
            }

            //obtenemos lista de enlaces
            $e = \Ermtool\Risk::getEnlacedRisks();

            $i = 0;
            foreach ($e as $en)
            {
                $enlaces[$i] = ['id' => $en->id,
                            'sub_name' => $en->sub_name,
                            'obj_name' => $en->obj_name,];
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.enlazar',['risk_subprocess' => $risk_subprocess, 'objective_risk' => $objective_risk,
                                        'enlaces' => $enlaces]);
            }
            else
            {
                return view('kri.enlazar',['risk_subprocess' => $risk_subprocess, 'objective_risk' => $objective_risk,
                                        'enlaces' => $enlaces]);
            }
        }
    }

    //función que almacena el enlace señalado entre un riesgo de proceso y de negocio
    public function guardarEnlace()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            //print_r($_POST);
            DB::transaction(function () {
                //primero verificamos que el enlace no existe previamente
                $enlace_previo = DB::table('objective_subprocess_risk')
                                ->where('risk_subprocess_id','=',$_POST['risk_subprocess_id'])
                                ->where('objective_risk_id','=',$_POST['objective_risk_id'])
                                ->select('id')
                                ->first();

                if ($enlace_previo)
                {
                    Session::forget('message');
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('warning','Selected risks are already attached');
                    }
                    else
                    {
                        Session::flash('warning','Los riesgos seleccionados ya se encuentran enlazados');
                    }
                }
                else
                {
                    Session::forget('warning'); 
                    //agregamos enlace
                    $in = DB::table('objective_subprocess_risk')
                    ->insertGetId([
                        'risk_subprocess_id' => $_POST['risk_subprocess_id'],
                        'objective_risk_id' => $_POST['objective_risk_id']
                        ]);

                    if (isset($in))
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Link successfully created');
                        }
                        else
                        {
                            Session::flash('message','Enlace generado correctamente');
                        }
                    }
                    else
                    {
                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('errors','Error storing KRI');
                        }
                        else
                        {
                            Session::flash('errors','Error al grabar KRI');
                        }
                    }
                }

            });

            return $this->enlazar();
        }
    }

    public function evaluar($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $kri = \Ermtool\KRI::find($id);
            //obtenemos unidad de medida
            $uni_med = \Ermtool\KRI::where('id',$id)->value('uni_med');

            //obtenemos nombre
            $name = \Ermtool\KRI::where('id',$id)->value('name');

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.evaluar',['id' => $id,'uni_med'=>$kri->uni_med,'name'=>$kri->name,
                                        'green_min' => $kri->green_min, 'red_max' => $kri->red_max]);
            }
            else
            {
                return view('kri.evaluar',['id' => $id,'uni_med'=>$kri->uni_med,'name'=>$kri->name,
                                        'green_min' => $kri->green_min, 'red_max' => $kri->red_max]);
            }
        }
    }

    public function storeEval()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            DB::transaction(function() {

                //verificamos que la evaluacion se encuentre dentro de los margenes establecidos, para esto obtenemos primero que todo los valores de green_min y red_max en la tabla KRI

                $kri = \Ermtool\KRI::find($_POST['id']);

                //verificamos en caso de que vaya de menos a más ó || de más a menos
                if ($_POST['evaluation'] >= $kri->green_min && $_POST['evaluation'] <= $kri->red_max || $_POST['evaluation'] <= $kri->green_min && $_POST['evaluation'] >= $kri->red_max)
                {
                    $date = date('Ymd H:i:s');
                    $id = DB::table('measurements')
                        ->insertGetId([
                            'value'=>$_POST['evaluation'],
                            'kri_id' => $_POST['id'],
                            'created_at' => $date,
                            'date_min' => $_POST['date_min'],
                            'date_max' => $_POST['date_max'],
                            ]);
                

                    if (isset($id))
                    {
                        //insertamos también en last_evaluation y date_evaluation de KRI
                        DB::table('kri')
                        ->where('id','=',$_POST['id'])
                        ->update([
                            'kri_last_evaluation' => $_POST['evaluation'],
                            'date_evaluation' => $date,
                            'updated_at' => $date,
                            ]);

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Measurement successfully created');
                        }
                        else
                        {
                            Session::flash('message','Evaluación realizada correctamente');
                        }
                    }
                }
                else
                {
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('error','Error storing measurement. Check if you entered a correct measurement value');
                    }
                    else
                    {
                        Session::flash('error','Error al grabar evaluación. Compruebe que ingreso un valor de evaluación correcto');
                    }

                }
            });
            if(Session::has('message'))
                return Redirect::to('kri');

            else if(Session::has('error'))
                return Redirect::to('kri.evaluar.'.$_POST['id']);
        }
    }
    //obtiene KRIs para un riesgo en específico
    public function getKri($id)
    {
        $kri = NULL;

        //hay más de un kri, buscamos todos
        $kri_query = DB::table('kri')
                ->where('risk_id','=',$id)
                ->select('kri.*')
                ->get();

        //obtenemos id de stakeholder
        $s = DB::table('risks')
                    ->where('id','=',$id)
                    ->select('stakeholder_id')
                    ->first();

        if ($s->stakeholder_id == 0 || $s->stakeholder_id == NULL)
        {
            $stakeholder = NULL;
        }
         else
        {
            //obtenemos stakeholder
            $stake = DB::table('stakeholders')
                        ->where('id',$s->stakeholder_id)
                        ->select(DB::raw("CONCAT(name,' ', surnames) AS full_name"))
                        ->first();
            $stakeholder = $stake->full_name;
        }

        $i=0;
        foreach ($kri_query as $k)
        {

            $tipo = $k->type;
            $uni_med = $k->uni_med;

            if ($k->kri_last_evaluation == NULL)
            {
                if (Session::get('languaje') == 'en')
                {
                    $last_eval = "Still have not evaluated";
                    $date_last = "Still have not evaluated";
                    $eval = "None";
                    $description_eval = "None";
                }
                else
                {
                    $last_eval = "Aun no ha sido evaluado";
                    $date_last = "Aun no ha sido evaluado";
                    $eval = "Ninguna";
                    $description_eval = "Ninguna";   
                }
            }
            else
            {
                $last_eval = $k->kri_last_evaluation;
                $date_last = date('d-m-Y',strtotime($k->date_evaluation));
                
                //calculamos evaluacion (color)

                $eval = $this->calc_sem($last_eval,$k->green_min,$k->interval_min,$k->interval_max,$k->red_max);

                
                if ($eval == 0) //0: verde
                {
                    $description_eval = $k->description_green;
                }
                else if ($eval == 1) //1: amarillo
                {
                    $description_eval = $k->description_yellow;
                }
                else if ($eval == 2) //2: rojo
                {
                    $description_eval = $k->description_red;
                }

            }

            $kri[$i] = [
                'id' => $k->id,
                'name' => $k->name,
                'description' => $k->description,
                'last_eval' => $last_eval,
                'date_last' => $date_last,
                'uni_med' => $uni_med,
                'created_at' => $k->created_at, 
                'type' => $tipo,
                'eval' => $eval,
                'description_eval' => $description_eval,
                'stakeholder' => $stakeholder
            ];

            $i += 1;
        }

        return json_encode($kri);
    }

    //obtiene evaluaciones anteriores de un riesgo (a través de evaluar)
    public function getEvaluations($id)
    {
        $evaluations = NULL;

        //primero obtenemos cotas de semaforo
        $cotas = DB::table('kri')
                ->where('id','=',$id)
                ->select('green_min','interval_min','interval_max','red_max')
                ->first();

        $evals = DB::table('measurements')
                    ->where('kri_id','=',$id)
                    ->select('value','created_at','date_min','date_max')
                    ->orderBy('id','desc')
                    ->get();

        $i = 0;
        foreach ($evals as $eval)
        {
            //calculamos evaluacion (color)
            $res = $this->calc_sem($eval->value,$cotas->green_min,$cotas->interval_min,$cotas->interval_max,$cotas->red_max);    
            
            $lala = new DateTime($eval->created_at);
            $date = date_format($lala,"d-m-Y");
            //$date = date('d-m-Y',strtotime($eval->created_at));
            //$date_min = date('d-m-Y',strtotime($eval->date_min));
            $date_min = date_format(new DateTime($eval->date_min),'d-m-Y');

            //$date_max = date('d-m-Y',strtotime($eval->date_max));
            $date_max = date_format(new DateTime($eval->date_max),'d-m-Y'); 
            $evaluations[$i] = [
                    'value' => $eval->value,
                    'eval' => $res,
                    'date' => $date,
                    'date_min' => $date_min,
                    'date_max' => $date_max,
            ];
            $i += 1;
        }

        return json_encode($evaluations);
    }

    //función para calcular semaforo
    function calc_sem($value,$green_min,$interval_min,$interval_max,$red_max)
    {
        if ($value >= $green_min && $value <= $red_max)
        {
            //OBS: También verificamos en caso de que las cotas sean de mayor a menor Ó de menor a mayor
            if ($value < $interval_min)
            {
                $eval = 0; //0: verde
            }
            else if ($value >= $interval_min && $value < $interval_max)
            {
                $eval = 1; //1: amarillo
            }
            //en este caso se deja >= $red_max en el último tramo ya que no hay más cotas
            else if ($value >= $interval_max && $value <= $red_max)
            {
                $eval = 2; //2: rojo
            }
        }
        else if ($value < $green_min && $value > $red_max) //ACT 10-04-17: Faltaba en caso de que la cota mayor sea verde y la mínima roja
        {
            if ($value <= $green_min && $value >= $interval_min)
            {
                $eval = 0; //0: verde
            }
            else if ($value < $interval_min && $value >= $interval_max)
            {
                $eval = 1; //1: amarillo
            }
            else if ($value < $interval_max && $value >= $red_max)
            {
                $eval = 2; //2: rojo
            }
        }
        else
        {
            $eval = NULL;
        }

        $r = $eval;

        return $r;
    }

    //lo mismo que getEvaluations solo que retorna vista en vez de JSON; por urgencia hice 2 funciones iguales en vez de buscar la forma de usar la misma en ambos casos
    public function showEvals($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $evaluations = NULL;

            //primero obtenemos cotas de semaforo
            $cotas = DB::table('kri')
                    ->where('id','=',$id)
                    ->select('green_min','interval_min','interval_max','red_max','name','description')
                    ->first();

            $evals = DB::table('measurements')
                        ->where('kri_id','=',$id)
                        ->select('value','created_at','date_min','date_max')
                        ->orderBy('id','asc')
                        ->get();

            $i = 0;
            foreach ($evals as $eval)
            {
                //calculamos evaluacion (color)
                $res = $this->calc_sem($eval->value,$cotas->green_min,$cotas->interval_min,$cotas->interval_max,$cotas->red_max);    
                //$date = date('d-m-Y',strtotime($eval->created_at));
                $lala = new DateTime($eval->created_at);
                $date = date_format($lala,"d-m-Y");

                $date_min = date_format(new DateTime($eval->date_min),'d-m-Y');
                $date_max = date_format(new DateTime($eval->date_max),'d-m-Y');
                //$date_min = date('d-m-Y',strtotime($eval->date_min));
                //$date_max = date('d-m-Y',strtotime($eval->date_max));

                //date text
                $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                $fechamod = explode('-',$date);

                $fecha_array = $fechamod[0].' de '.$meses[$fechamod[1]-1].' del '.$fechamod[2];
                $evaluations[$i] = [
                        'value' => $eval->value,
                        'eval' => $res,
                        'date' => $date,
                        'date_min' => $date_min,
                        'date_max' => $date_max,
                        'fecha_array' => $fecha_array,
                ];
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.kri.anteriores',['evaluations' => $evaluations, 'name' => $cotas->name, 'description' => $cotas->description,'id' => $id]);
            }
            else
            {
                return view('kri.anteriores',['evaluations' => $evaluations, 'name' => $cotas->name, 'description' => $cotas->description, 'id' => $id]);
            }
        }
    }
}
