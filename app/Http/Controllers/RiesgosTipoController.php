<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use DateTime;
use Auth;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class RiesgosTipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('riesgos_tipo');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/riesgos_tipo.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $riesgostipo = array();
            if (isset($_GET['verbloqueados']))
            {
                $riesgostipo2 = \Ermtool\Risk::where('type2',0)->where('status',1)->get(); //select riesgos tipo bloqueados  
            }
            else
            {
                $riesgostipo2 = \Ermtool\Risk::where('type2',0)->where('status',0)->get(); //select riesgos tipo desbloqueados
            }

            $i = 0;

            // ---recorremos todos los riesgos tipo para asignar formato de datos correspondientes--- //
            foreach ($riesgostipo2 as $riesgo)
            {
                //damos formato a fecha expiración
                if ($riesgo['expiration_date'] == NULL OR $riesgo['expiration_date'] == "0000-00-00")
                {
                    $fecha_exp = NULL;
                }
                else 
                {
                    $expiration_date = new DateTime($riesgo['expiration_date']);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
                }

                 //damos formato a fecha creación
                if ($riesgo['created_at'] != NULL)
                {
                    $lala = new DateTime($riesgo['created_at']);
                    $fecha_creacion = date_format($lala,"d-m-Y");
                }
                else
                    $fecha_creacion = NULL;

                //damos formato a fecha de actualización 
                if ($riesgo['updated_at'] != NULL)
                {
                    $lala = new DateTime($riesgo['updated_at']);
                    $fecha_act = date_format($lala,"d-m-Y");
                }
                else
                    $fecha_act = NULL;

                //obtenemos categoría de riesgo
                $categoria = \Ermtool\Risk_category::find($riesgo['risk_category_id']);

                //obtenemos causas si es que tiene
                $causes = DB::table('cause_risk')
                            ->join('causes','causes.id','=','cause_risk.cause_id')
                            ->where('cause_risk.risk_id','=',$riesgo['id'])
                            ->select('causes.name')
                            ->get();

                if ($causes)
                {
                    $causas = array();
                    $j = 0;
                    foreach ($causes as $cause)
                    {
                        $causas[$j] = $cause->name;
                        $j += 1;
                    }
                }
                else
                {
                    $causas = NULL;
                }

                //obtenemos efectos si es que existen
                $effects = DB::table('effect_risk')
                            ->join('effects','effects.id','=','effect_risk.effect_id')
                            ->where('effect_risk.risk_id','=',$riesgo['id'])
                            ->select('effects.name')
                            ->get();

                if ($effects)
                {
                    $efectos = array();
                    $j = 0;
                    foreach ($effects as $effect)
                    {
                        $efectos[$j] = $effect->name;
                        $j += 1;
                    }
                }
                else
                {
                    $efectos = NULL;
                }   

                //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                $short_des = substr($riesgo['description'],0,100);

                $riesgostipo[$i] = array('id'=>$riesgo['id'],
                                    'nombre'=>$riesgo['name'],
                                    'descripcion'=>$riesgo['description'],
                                    'fecha_creacion'=>$fecha_creacion,
                                    'fecha_act'=>$fecha_act,
                                    'fecha_exp'=>$fecha_exp,
                                    'causas'=>$causas,
                                    'efectos'=>$efectos,
                                    'categoria'=>$categoria['name'],
                                    'estado'=>$riesgo['status'],
                                    'short_des'=>$short_des);
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.riesgos_tipo.index',['riesgos'=>$riesgostipo]); 
            }
            else
            {
                return view('datos_maestros.riesgos_tipo.index',['riesgos'=>$riesgostipo]); 
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
            $categorias = \Ermtool\Risk_category::where('status',0)->lists('name','id');
            $causas = \Ermtool\Cause::where('status',0)->lists('name','id');
            $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.riesgos_tipo.create',
                        ['categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos]);
            }
            else
            {
               return view('datos_maestros.riesgos_tipo.create',
                        ['categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos]); 
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
            DB::transaction(function()
            {
                $logger = $this->logger;
                $causa = array();

                if ($_POST['expiration_date'] == NULL || $_POST['expiration_date'] == "")
                    $exp_date = NULL;
                else
                    $exp_date = $_POST['expiration_date'];

                $risk = \Ermtool\Risk::create([
                    'name'=>$_POST['name'],
                    'description'=>$_POST['description'],
                    'type2'=>0,
                    'expiration_date'=>$exp_date,
                    'risk_category_id'=>$_POST['risk_category_id'],
                    ]);

                //vemos si se agrego alguna causa nueva
                if (isset($_POST['causa_nueva']))
                {
                    $new_causa = \Ermtool\Cause::create([
                        'name'=>$_POST['causa_nueva']
                    ]);

                    //guardamos en cause_risk
                    DB::table('cause_risk')
                        ->insert([
                            'risk_id' => $risk->id,
                            'cause_id' => $new_causa->id,
                            ]);
                }
                else //se están agregando causas ya creadas
                {
                    if(isset($_POST['cause_id']))
                    {
                        foreach ($_POST['cause_id'] as $cause_id)
                        {
                            //insertamos cada causa en cause_risk
                            DB::table('cause_risk')
                                ->insert([
                                    'risk_id' => $risk->id,
                                    'cause_id' => $cause_id
                                    ]);
                        }
                    } 
                }

                //vemos si se agrego algún efecto nuevo
                if (isset($_POST['efecto_nuevo']))
                {
                    $new_effect = \Ermtool\Effect::create([
                        'name'=>$_POST['efecto_nuevo']
                        ]);

                     //guardamos en cause_risk
                    DB::table('effect_risk')
                        ->insert([
                            'risk_id' => $risk->id,
                            'effect_id' => $new_effect->id,
                            ]);
                }
                else
                {
                    if (isset($_POST['effect_id']))
                    {
                        if ($_POST['effect_id'] == NULL)
                            $efecto = NULL;
                        else
                        {
                            foreach ($_POST['effect_id'] as $effect_id)
                            {
                                //insertamos cada causa en cause_risk
                                DB::table('effect_risk')
                                    ->insert([
                                        'risk_id' => $risk->id,
                                        'effect_id' => $effect_id
                                        ]);
                            }
                        }
                    }
                }
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Template risk successfully created');
                }
                else
                {
                    Session::flash('message','Riesgo tipo agregado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el riesgo tipo con Id: '.$risk->id.' llamado: '.$risk->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });
            return Redirect::to('/riskstype');
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
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $riesgo = \Ermtool\Risk::find($id);
            $categorias = \Ermtool\Risk_category::where('status',0)->lists('name','id');
            $causas = \Ermtool\Cause::where('status',0)->lists('name','id');
            $efectos = \Ermtool\Effect::where('status',0)->lists('name','id');

            $causes_selected = array();
            $effects_selected = array();
            //obtenemos causas seleccionadas
            $causes = DB::table('cause_risk')
                                ->where('risk_id','=',$riesgo->id)
                                ->select('cause_risk.cause_id')
                                ->get();

            $i = 0;
            foreach ($causes as $cause)
            {
                $causes_selected[$i] = $cause->cause_id;
                $i += 1;
            }

            //obtenemos efectos seleccionados
            $effects = DB::table('effect_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->select('effect_risk.effect_id')
                            ->get();

            $i = 0;
            foreach ($effects as $effect)
            {
                $effects_selected[$i] = $effect->effect_id;
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.riesgos_tipo.edit',['riesgo'=>$riesgo,
                        'categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos,
                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected]);
            }
            else
            {
                return view('datos_maestros.riesgos_tipo.edit',['riesgo'=>$riesgo,
                        'categorias'=>$categorias,'causas'=>$causas,'efectos'=>$efectos,
                        'causes_selected'=>$causes_selected,'effects_selected'=>$effects_selected]);
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
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $logger = $this->logger;
                $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);

                if ($_POST['expiration_date'] == NULL || $_POST['expiration_date'] == "")
                    $exp_date = NULL;
                else
                    $exp_date = $_POST['expiration_date'];

                //vemos si se agrego alguna causa nueva
                if (isset($_POST['causa_nueva']))
                {
                    $new_causa = \Ermtool\Cause::create([
                        'name'=>$_POST['causa_nueva']
                    ]);

                    //guardamos en cause_risk
                    DB::table('cause_risk')
                        ->insert([
                            'risk_id' => $riesgo->id,
                            'cause_id' => $new_causa->id,
                            ]);
                }
                else //se están agregando causas ya creadas
                {
                    if (isset($_POST['cause_id']))
                    {
                        foreach ($_POST['cause_id'] as $cause_id)
                        {
                            //primero buscamos si es que existe previamente
                            $cause = DB::table('cause_risk')
                                ->where('cause_id','=',$cause_id)
                                ->where('risk_id','=',$riesgo->id)
                                ->first();

                            if (!$cause) //no existe, por lo que se agrega
                            {
                                DB::table('cause_risk')
                                ->insert([
                                    'risk_id' => $riesgo->id,
                                    'cause_id' => $cause_id
                                    ]);
                            }
                        }
                    } 
                }

                //vemos si se agrego algún efecto nuevo
                if (isset($_POST['efecto_nuevo']))
                {
                    $new_effect = \Ermtool\Effect::create([
                        'name'=>$_POST['efecto_nuevo']
                        ]);

                     //guardamos en cause_risk
                    DB::table('effect_risk')
                        ->insert([
                            'risk_id' => $riesgo->id,
                            'effect_id' => $new_effect->id,
                            ]);
                }
                else //efectos existentes
                {
                    if (isset($_POST['effect_id']))
                    {
                        foreach ($_POST['effect_id'] as $effect_id)
                        {
                            //primero buscamos si es que existe previamente
                            $effect = DB::table('effect_risk')
                                ->where('effect_id','=',$effect_id)
                                ->where('risk_id','=',$riesgo->id)
                                ->first();

                            if (!$effect) //no existe, por lo que se agrega
                            {
                                //insertamos cada causa en cause_risk
                                DB::table('effect_risk')
                                    ->insert([
                                        'risk_id' => $riesgo->id,
                                        'effect_id' => $effect_id
                                        ]);
                            }
                        }
                    } 
                }

                //ahora recorreremos todas las causas y efectos de este riesgo, para saber si es que no se borró alguna
                $causas = DB::table('cause_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->select('cause_id')
                            ->get();

                foreach($causas as $cause)
                {
                    $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                    //ahora recorremos todas las causas que se agregaron para comparar
                    foreach ($_POST['cause_id'] as $cause_add)
                    {
                        if ($cause_add == $cause->cause_id)
                        {
                            $cont += 1;
                        }
                    }

                    if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                    {
                        DB::table('cause_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->where('cause_id','=',$cause->cause_id)
                            ->delete();
                    }
                }

                //lo mismo ahora para efectos
                $efectos = DB::table('effect_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->select('effect_id')
                            ->get();

                foreach($efectos as $effect)
                {
                    $cont = 0; //si se mantiene en cero, nunca habrán sido iguales, por lo que significa que se habria borrado
                    //ahora recorremos todas las causas que se agregaron para comparar
                    foreach ($_POST['effect_id'] as $effect_add)
                    {
                        if ($effect_add == $effect->effect_id)
                        {
                            $cont += 1;
                        }
                    }

                    if ($cont == 0) //hay que eliminar la causa; por ahora solo la eliminaremos de cause_risk
                    {
                        DB::table('effect_risk')
                            ->where('risk_id','=',$riesgo->id)
                            ->where('effect_id','=',$effect->effect_id)
                            ->delete();
                    }
                }

                $riesgo->name = $_POST['name'];
                $riesgo->description = $_POST['description'];
                $riesgo->expiration_date = $exp_date;
                $riesgo->risk_category_id = $_POST['risk_category_id'];

                $riesgo->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Template risk successfully updated');
                }
                else
                {
                    Session::flash('message','Riesgo tipo actualizado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el riesgo tipo con Id: '.$riesgo->id.' llamado: '.$riesgo->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });

            return Redirect::to('/riskstype');
        }
    }

    public function bloquear($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $logger = $this->logger;
                $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
                $riesgo->status = 1;
                $riesgo->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Template risk successfully blocked');
                }
                else
                {
                    Session::flash('message','Riesgo tipo bloqueado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el riesgo tipo con Id: '.$GLOBALS['id1'].' llamado: '.$riesgo->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });
            return Redirect::to('/riskstype');
        }
    }

    public function desbloquear($id)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            global $id1;
            $id1 = $id;
            DB::transaction(function()
            {
                $logger = $this->logger;
                $riesgo = \Ermtool\Risk::find($GLOBALS['id1']);
                $riesgo->status = 0;
                $riesgo->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Template risk successfully updated');
                }
                else
                {
                    Session::flash('message','Riesgo tipo desbloqueado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el riesgo tipo con Id: '.$GLOBALS['id1'].' llamado: '.$riesgo->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

            });

            return Redirect::to('/riskstype');
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
            $logger = $this->logger;

            //nombre
            $name = \Ermtool\Risk::name($GLOBALS['id1']);
            //eliminamos primero causas y efectos (si es que tiene)
            DB::table('cause_risk')
                ->where('risk_id','=',$GLOBALS['id1'])
                ->delete();

            DB::table('effect_risk')
                ->where('risk_id','=',$GLOBALS['id1'])
                ->delete();

            //ahora eliminamos riesgo (nos aseguramos de que sea riesgo tipo (type2 = 0))
            DB::table('risks')
                ->where('type2','=',0)
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

            $GLOBALS['res'] = 0;

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el riesgo tipo con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

        });

        return $res;
    }
}
