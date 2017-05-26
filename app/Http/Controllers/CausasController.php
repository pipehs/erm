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

class CausasController extends Controller
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
        $this->logger = new Logger('causas');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/causas.log', Logger::INFO));
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
            $causas = array();
            if (isset($_GET['verbloqueados']))
            {
                $causas2 = \Ermtool\Cause::where('status',1)->get(); //select causas bloqueadas  
            }
            else
            {
                $causas2 = \Ermtool\Cause::where('status',0)->get(); //select causas desbloqueadas
            }

            $i = 0;

            // ---recorremos todas las causas para asignar formato de datos correspondientes--- //
            foreach ($causas2 as $causa)
            {
                //damos formato a fecha de creación
                if ($causa['created_at'] != NULL)
                {
                    $lala = new DateTime($causa['created_at']);
                    $fecha_creacion = date_format($lala,"d-m-Y");
                    //$fecha_creacion = date_format($causa['created_at'],"d-m-Y");
                }
                else
                    $fecha_creacion = NULL;

                //damos formato a fecha de actualización
                if ($causa['updated_at'] != NULL)
                {
                    $lala = new DateTime($causa['updated_at']);
                    $fecha_act = date_format($lala,"d-m-Y");
                    //$fecha_act = date_format($causa['updated_at'],"d-m-Y");
                }
                else
                    $fecha_act = NULL;

                //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                $short_des = substr($causa['description'],0,100);

                $causas[$i] = array('id'=>$causa['id'],
                                    'nombre'=>$causa['name'],
                                    'descripcion'=>$causa['description'],
                                    'fecha_creacion'=>$fecha_creacion,
                                    'fecha_act'=>$fecha_act,
                                    'estado'=>$causa['status'],
                                    'short_des'=>$short_des);
                $i += 1;
            }

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.causas.index',['causas'=>$causas]);
            }
            else
            {
               return view('datos_maestros.causas.index',['causas'=>$causas]); 
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
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.causas.create');
            }
            else
            {
                return view('datos_maestros.causas.create'); 
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
            $logger = $this->logger;
            //Validación: Si la validación es pasada, el código continua
            $this->validate($request, [
                'name' => 'unique:causes',
                'description' => 'required',
            ]);

            $cause = \Ermtool\Cause::create([
                'name' => $request['name'],
                'description' => $request['description'],
                ]);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Cause successfully created');
            }
            else
            {
                Session::flash('message','Causa agregada correctamente');
            }

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la causa con Id: '.$cause->id.' llamado: '.$cause->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            return Redirect::to('/causas');
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
            $causa = \Ermtool\Cause::find($id);

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.causas.edit',['causa'=>$causa]);
            }
            else
            {
                return view('datos_maestros.causas.edit',['causa'=>$causa]);
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
            //Validación: Si la validación es pasada, el código continua
            $this->validate($request, [
                'name' => 'unique:causes',
                'description' => 'required',
            ]);

            $logger = $this->logger;
            
            $causa = \Ermtool\Cause::find($id);

            $causa->name = $request['name'];
            $causa->description = $request['description'];

            $causa->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Cause successfully updated');
            }
            else
            {
                Session::flash('message','Causa actualizada correctamente');
            }

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado la causa con Id: '.$causa->id.' llamado: '.$causa->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

            return Redirect::to('/causas');
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
            $logger = $this->logger;
            
            $causa = \Ermtool\Cause::find($id);
            $causa->status = 1;
            $causa->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Cause successfully blocked');
            }
            else
            {
                Session::flash('message','Causa bloqueada correctamente');
            }

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado la causa con Id: '.$causa->id.' llamada: '.$causa->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

            return Redirect::to('/causas');
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
            $logger = $this->logger;

            $causa = \Ermtool\Cause::find($id);
            $causa->status = 0;
            $causa->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Cause successfully unblocked');
            }
            else
            {
                Session::flash('message','Causa desbloqueada correctamente');
            }

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado la causa con Id: '.$causa->id.' llamada: '.$causa->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));

            return Redirect::to('/causas');
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
            $name = \Ermtool\Cause::name($GLOBALS['id1']);
            //eliminamos primero de cause_risk
            DB::table('cause_risk')
                ->where('cause_id','=',$GLOBALS['id1'])
                ->delete();

            //ahora eliminamos causa
            DB::table('causes')
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

            $GLOBALS['res'] = 0;

            $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la causa con Id: '.$GLOBALS['id1'].' llamada: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
        });

        return $res;
    }
}
