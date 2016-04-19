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

class KriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kri = NULL;

        //seleccionamos todos los kri
        $kri_query = DB::table('KRI')
                ->join('risks','risks.id','=','KRI.risk_id')
                ->select('KRI.*','risks.name as risk_name','risks.stakeholder_id as risk_stake')
                ->get();

        $i=0;
        foreach ($kri_query as $k)
        {

            if ($k->type == 0)
            {
                $tipo = "Manual";
            }
            else if ($k->type == 1)
            {
                $tipo = "Automático";
            }

            if ($k->uni_med == 0)
            {
                $uni_med = "Porcentaje";
            }
            else if ($k->uni_med == 1)
            {
                $uni_med = "Monto";
            }
            else if ($k->uni_med == 2)
            {
                $uni_med = "Cantidad";
            }


            //Seteamos periodicity. 0=Diario, 1=Semanal, 2=Mensual, 3=Semestral, 4=Anual
            switch ($k->periodicity)
            {
                case 0:
                    $periodicity = "Diario";
                    break;
                case 1:
                    $periodicity = "Semanal";
                    break;
                case 2:
                    $periodicity = "Mensual";
                    break;
                case 3:
                    $periodicity = "Semestral";
                    break;
                case 4:
                    $periodicity = "Anual";
                    break;
                case 5:
                    $periodicity = "Cada vez que ocurra";
                    break;
                case NULL:
                    $periodicity = "Falta asignación";
                    break;
            }

            if ($k->kri_last_evaluation === NULL)
            {
                $last_eval = "Aun no ha sido evaluado";
                $date_last = "Aun no ha sido evaluado";
                $eval = 3; //probamos con el valor 3 ya que escribiendo "Ninguna" lo toma como = a 0
                $description_eval = "Ninguna";
                $date_min = null;
                $date_max = null;
            }
            else
            {
                $last_eval = $k->kri_last_evaluation;
                $date_last = date('d-m-Y',strtotime($k->date_evaluation));
                
                //obtenemos periodo de evaluación

                $periodo = DB::table('measurements')
                            ->where('kri_id','=',$k->id)
                            ->where('created_at','=',$k->date_evaluation)
                            ->select('date_min','date_max')
                            ->first();
                $date_min = date('d-m-Y',strtotime($periodo->date_min));
                $date_max = date('d-m-Y',strtotime($periodo->date_max));

                //calculamos evaluacion (color)

                $eval = $this->calc_sem($last_eval,$k->green_min,$k->green_max,$k->yellow_min,$k->yellow_max,
                                        $k->red_min,$k->red_max);

                
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

            $created_at = date('d-m-Y',strtotime($k->created_at));

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
                'risk_stakeholder' => $k->risk_stake,
                'eval' => $eval,
                'description_eval' => $description_eval,
                'last_evaluation' => $last_eval,
                'date_min' => $date_min,
                'date_max' => $date_max,
            ];

            $i += 1;
        }

        return view('kri.index',['kri'=>$kri]);
    }

    public function index2()
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

        return view('kri.index2',['risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

        return view('kri.create',['risk_subprocess' => $kri_risk_subprocess, 'objective_risk' => $kri_objective_risk]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //print_r($_POST);

        //creamos NUEVO KRI
        DB::transaction(function() {

            $in = DB::table('KRI')
                    ->insertGetId([
                        'risk_id' => $_POST['risk_id'],
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'type' => $_POST['type'],
                        'periodicity' => $_POST['periodicity'],
                        'uni_med' => $_POST['uni_med'],
                        'green_min' => $_POST['green_min'],
                        'green_max' => $_POST['green_max'],
                        'description_green' => $_POST['description_green'],
                        'yellow_min' => $_POST['yellow_min'],
                        'yellow_max' => $_POST['yellow_max'],
                        'description_yellow' => $_POST['description_yellow'],
                        'red_min' => $_POST['red_min'],
                        'red_max' => $_POST['red_max'],
                        'description_red' => $_POST['description_red'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            

            if (isset($in))
            {
                Session::flash('message','KRI generado correctamente');
            }
            else
            {
                Session::flash('errors','Error al grabar KRI');
            }
        });

        return Redirect::to('kri');

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
        return view('kri.edit',['kri'=>$kri,'risk_subprocess'=>$kri_risk_subprocess,'objective_risk'=>$kri_objective_risk]);
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
        //print_r($_POST);
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $kri = \Ermtool\KRI::find($GLOBALS['id1']);

            $kri->risk_id = $_POST['risk_id'];
            $kri->name = $_POST['name'];
            $kri->description = $_POST['description'];
            $kri->type = $_POST['type'];
            $kri->uni_med = $_POST['uni_med'];
            $kri->green_min = $_POST['green_min'];
            $kri->green_max = $_POST['green_max'];
            $kri->description_green = $_POST['description_green'];
            $kri->yellow_min = $_POST['yellow_min'];
            $kri->yellow_max = $_POST['yellow_max'];
            $kri->description_yellow = $_POST['description_yellow'];
            $kri->red_min = $_POST['red_min'];
            $kri->red_max = $_POST['red_max'];
            $kri->description_red = $_POST['description_red'];

            $kri->save();

            Session::flash('message','KRI actualizado correctamente');
        });

        return Redirect::to('kri');
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

    //vista de enlazador de riesgos de proceso a riesgos de negocio
    public function enlazar()
    {
        $risk_subprocess = array();
        $objective_risk = array();
        $enlaces = array();

        //primero obtenemos riesgos de subprocesso
        $risk = DB::table('risk_subprocess')
                    ->join('risks','risks.id','=','risk_subprocess.risk_id')
                    ->join('subprocesses','subprocesses.id','=','risk_subprocess.subprocess_id')
                    ->join('processes','processes.id','=','subprocesses.process_id')
                    ->select('risks.id','risks.name','subprocesses.name as subprocess_name','processes.name as process_name')
                    ->get();
        $i = 0;
        foreach ($risk as $r)
        {
            $risk_subprocess[$i] = ['id' => $r->id,
                                    'name' => $r->name,
                                    'subprocess_name' => $r->subprocess_name,
                                    'process_name' => $r->process_name];

            $i += 1;
        }

        //obtenemos riesgos de negocio
        //primero obtenemos riesgos de subprocesso
        $risk = DB::table('objective_risk')
                    ->join('risks','risks.id','=','objective_risk.risk_id')
                    ->join('objectives','objectives.id','=','objective_risk.objective_id')
                    ->select('risks.id','risks.name','objectives.name as objective_name')
                    ->get();
        $i = 0;
        foreach ($risk as $r)
        {
            $objective_risk[$i] = ['id' => $r->id,
                                    'name' => $r->name,
                                    'objective_name' => $r->objective_name];

            $i += 1;
        }

        //obtenemos lista de enlaces
        $e = DB::table('objective_subprocess_risk')
                ->join('risks as risk_subprocess','risk_subprocess.id','=','objective_subprocess_risk.risk_subprocess_id')
                ->join('risks as objective_risk','objective_risk.id','=','objective_subprocess_risk.objective_risk_id')
                ->select('objective_subprocess_risk.id','objective_risk.name as obj_name','risk_subprocess.name as sub_name')
                ->get();
        $i = 0;
        foreach ($e as $en)
        {
            $enlaces[$i] = ['id' => $en->id,
                        'sub_name' => $en->sub_name,
                        'obj_name' => $en->obj_name,];
            $i += 1;
        }

        return view('kri.enlazar',['risk_subprocess' => $risk_subprocess, 'objective_risk' => $objective_risk,
                                    'enlaces' => $enlaces]);
    }

    //función que almacena el enlace señalado entre un riesgo de proceso y de negocio
    public function guardarEnlace()
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
                Session::flash('warning','Los riesgos seleccionados ya se encuentran enlazados');
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
                    Session::flash('message','Enlace generado correctamente');
                }
                else
                {
                    Session::flash('errors','Error al grabar KRI');
                }
            }

        });

        return $this->enlazar();
    }

    public function evaluar($id)
    {
        //obtenemos unidad de medida
        $uni_med = \Ermtool\KRI::where('id',$id)->value('uni_med');

        //obtenemos nombre
        $name = \Ermtool\KRI::where('id',$id)->value('name');

        return view('kri.evaluar',['id' => $id,'uni_med'=>$uni_med,'name'=>$name]);
    }

    public function storeEval()
    {
        DB::transaction(function() {

            //verificamos que la evaluacion se encuentre dentro de los margenes establecidos, para esto obtenemos primero que todo los valores de green_min y red_max en la tabla KRI

            $kri = \Ermtool\KRI::find($_POST['id']);

            //verificamos en caso de que vaya de menos a más ó || de más a menos
            if ($_POST['evaluation'] >= $kri->green_min && $_POST['evaluation'] <= $kri->red_max || $_POST['evaluation'] <= $kri->green_min && $_POST['evaluation'] >= $kri->red_max)
            {
                $date = date('Y-m-d H:i:s');
                $id = DB::table('measurements')
                    ->insertGetId([
                        'value'=>$_POST['evaluation'],
                        'KRI_id' => $_POST['id'],
                        'created_at' => $date,
                        'date_min' => $_POST['date_min'],
                        'date_max' => $_POST['date_max'],
                        ]);
            }

            if (isset($id))
            {
                //insertamos también en last_evaluation y date_evaluation de KRI
                DB::table('KRI')
                ->where('id','=',$_POST['id'])
                ->update([
                    'kri_last_evaluation' => $_POST['evaluation'],
                    'date_evaluation' => $date,
                    'updated_at' => $date,
                    ]);

                Session::flash('message','Evaluación realizada correctamente');
            }
            else
            {
                Session::flash('error','Error al grabar evaluación. Compruebe que ingreso un valor de evaluación correcto');

            }
        });
        if(Session::has('message'))
            return Redirect::to('kri');

        else if(Session::has('error'))
            return Redirect::to('kri.evaluar.'.$_POST['id']);
        
    }
    //obtiene KRIs para un riesgo en específico
    public function getKri($id)
    {
        $kri = NULL;

        //hay más de un kri, buscamos todos
        $kri_query = DB::table('KRI')
                ->where('risk_id','=',$id)
                ->select('KRI.*')
                ->get();

        $i=0;
        foreach ($kri_query as $k)
        {

            if ($k->type == 0)
            {
                $tipo = "Manual";
            }
            else if ($k->type == 1)
            {
                $tipo = "Automático";
            }

            if ($k->uni_med == 0)
            {
                $uni_med = "Porcentaje";
            }
            else if ($k->uni_med == 1)
            {
                $uni_med = "Monto";
            }
            else if ($k->uni_med == 2)
            {
                $uni_med = "Cantidad";
            }

            if ($k->kri_last_evaluation == NULL)
            {
                $last_eval = "Aun no ha sido evaluado";
                $date_last = "Aun no ha sido evaluado";
                $eval = "Ninguna";
                $description_eval = "Ninguna";
            }
            else
            {
                $last_eval = $k->kri_last_evaluation;
                $date_last = date('d-m-Y',strtotime($k->date_evaluation));
                
                //calculamos evaluacion (color)

                $eval = $this->calc_sem($last_eval,$k->green_min,$k->green_max,$k->yellow_min,$k->yellow_max,
                                        $k->red_min,$k->red_max);

                
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
                'last_evaluation' => $last_eval,
            ];

            $i += 1;
        }

        return json_encode($kri);
    }

    //obtiene evaluaciones anteriores de un riesgo
    public function getEvaluations($id)
    {
        $evaluations = NULL;

        //primero obtenemos cotas de semaforo
        $cotas = DB::table('KRI')
                ->where('id','=',$id)
                ->select('green_min','green_max','yellow_min','yellow_max','red_min','red_max')
                ->first();

        $evals = DB::table('measurements')
                    ->where('KRI_id','=',$id)
                    ->select('value','created_at','date_min','date_max')
                    ->orderBy('id','desc')
                    ->get();

        $i = 0;
        foreach ($evals as $eval)
        {
            //calculamos evaluacion (color)
            $res = $this->calc_sem($eval->value,$cotas->green_min,$cotas->green_max,$cotas->yellow_min,$cotas->yellow_max,
                $cotas->red_min,$cotas->red_max);    
            $date = date('d-m-Y',strtotime($eval->created_at));
            $date_min = date('d-m-Y',strtotime($eval->date_min));
            $date_max = date('d-m-Y',strtotime($eval->date_max));
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
    function calc_sem($value,$green_min,$green_max,$yellow_min,$yellow_max,$red_min,$red_max)
    {
        if ($green_max != $yellow_min && $yellow_max != $red_min) //son distintos, por lo tanto incluyentes los minimos
        {
                //OBS: Verificamos en caso de que las cotas sean de mayor a menor Ó de menor a mayor
                if ($value >= $green_min && $value <= $green_max || $value <= $green_min && $value >= $green_max)
                {
                    $eval = 0; //0: verde
                }
                else if ($value >= $yellow_min && $value <= $yellow_max || $value <= $yellow_min && $value >= $yellow_max)
                {
                    $eval = 1; //1: amarillo
                }
                else if ($value >= $red_min && $value <= $red_max || $value <= $red_min && $value >= $red_max)
                {
                    $eval = 2; //2: rojo
                }
        }

        else //son iguales, por lo que se debe elegir un criterio, en este caso sera incluyente el mínimo y excluira el máximo
        {
                //OBS: También verificamos en caso de que las cotas sean de mayor a menor Ó de menor a mayor
                if ($value >= $green_min && $value < $green_max || $value <= $green_min && $value > $green_max)
                {
                    $eval = 0; //0: verde
                }
                else if ($value >= $yellow_min && $value < $yellow_max || $value <= $yellow_min && $value > $yellow_max)
                {
                    $eval = 1; //1: amarillo
                }
                //en este caso se deja >= $red_max en el último tramo ya que no hay más cotas
                else if ($value >= $red_min && $value < $red_max || $value <= $red_min && $value >= $red_max)
                {
                    $eval = 2; //2: rojo
                }
        }
       

        $r = $eval;

        return $r;
    }
}