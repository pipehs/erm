<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use Auth;
use DB;
use DateTime;

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class EfectosController extends Controller
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
        $this->logger = new Logger('efectos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/efectos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $efectos = array();
                if (isset($_GET['verbloqueados']))
                {
                    $efectos2 = \Ermtool\Effect::where('status',1)->get(); //select efectos bloqueados  
                }
                else
                {
                    $efectos2 = \Ermtool\Effect::where('status',0)->get(); //select efectos desbloqueados
                }

                $i = 0;

                // ---recorremos todas las efectos para asignar formato de datos correspondientes--- //
                foreach ($efectos2 as $efecto)
                {
                    //damos formato a fecha de creación
                    if ($efecto['created_at'] != NULL)
                    {
                        //$fecha_creacion = date_format($efecto['created_at'],"d-m-Y");
                        $lala = new DateTime($efecto['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_creacion = NULL;

                    //damos formato a fecha de actualización
                    if ($efecto['updated_at'] != NULL)
                    {
                        //$fecha_act = date_format($efecto['updated_at'],"d-m-Y");
                        $lala = new DateTime($efecto['updated_at']);
                        $fecha_act = date_format($lala,"d-m-Y");
                    }
                    else
                        $fecha_act = NULL;

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($efecto['description'],0,100);

                    $efectos[$i] = array('id'=>$efecto['id'],
                                        'nombre'=>$efecto['name'],
                                        'descripcion'=>$efecto['description'],
                                        'fecha_creacion'=>$fecha_creacion,
                                        'fecha_act'=>$fecha_act,
                                        'estado'=>$efecto['status'],
                                        'short_des'=>$short_des);
                    $i += 1;
                }
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.efectos.index',['efectos'=>$efectos]);
                }
                else
                {
                    return view('datos_maestros.efectos.index',['efectos'=>$efectos]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.efectos.create');
                }
                else
                {
                    return view('datos_maestros.efectos.create');
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //Validación: Si la validación es pasada, el código continua
                $this->validate($request, [
                    'name' => 'unique:effects'
                ]);

                $logger = $this->logger;

                if (isset($_POST['description']) && $_POST['description'] != '')
                {
                    $description = $_POST['description'];
                }
                else
                {
                    $description = NULL;
                }

                $effect = \Ermtool\Effect::create([
                    'name' => $request['name'],
                    'description' => $description
                    ]);

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Effect successfully created');
                }
                else
                {
                    Session::flash('message','Efecto agregado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado el efecto con Id: '.$effect->id.' llamado: '.$effect->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/efectos');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $efecto = \Ermtool\Effect::find($id);

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.efectos.edit',['efecto'=>$efecto]);
                }
                else
                {
                    return view('datos_maestros.efectos.edit',['efecto'=>$efecto]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $logger = $this->logger;

                $efecto = \Ermtool\Effect::find($id);

                $efecto->name = $request['name'];
                $efecto->description = $request['description'];

                $efecto->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Effect successfully updated');
                }
                else
                {
                    Session::flash('message','Efecto actualizado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado el efecto con Id: '.$efecto->id.' llamado: '.$efecto->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/efectos');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function bloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $logger = $this->logger;
                $efecto = \Ermtool\Effect::find($id);
                $efecto->status = 1;
                $efecto->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Effect successfully blocked');
                }
                else
                {
                    Session::flash('message','Efecto bloqueado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado el efecto con Id: '.$efecto->id.' llamado: '.$efecto->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/efectos');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function desbloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $logger = $this->logger;

                $efecto = \Ermtool\Effect::find($id);
                $efecto->status = 0;
                $efecto->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Effect successfully unblocked');
                }
                else
                {
                    Session::flash('message','Efecto desbloqueado correctamente');
                }

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado el efecto con Id: '.$efecto->id.' llamado: '.$efecto->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                return Redirect::to('/efectos');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;

            DB::transaction(function() {

                $logger = $this->logger;
                $name = \Ermtool\Effect::name($GLOBALS['id1']);
                //eliminamos primero de effect_risk
                DB::table('effect_risk')
                    ->where('effect_id','=',$GLOBALS['id1'])
                    ->delete();

                //ahora eliminamos efecto
                DB::table('effects')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado el efecto con Id: '.$GLOBALS['id1'].' llamado: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

                $GLOBALS['res'] = 0;
            });

            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
