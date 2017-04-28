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


class EfectosController extends Controller
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
                return view('en.datos_maestros.efectos.create');
            }
            else
            {
                return view('datos_maestros.efectos.create');
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
            \Ermtool\Effect::create([
                'name' => $request['name'],
                'description' => $request['description'],
                ]);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Effect successfully created');
            }
            else
            {
                Session::flash('message','Efecto agregado correctamente');
            }

            return Redirect::to('/efectos');
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

            return Redirect::to('/efectos');
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

            return Redirect::to('/efectos');
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

            return Redirect::to('/efectos');
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

            //eliminamos primero de effect_risk
            DB::table('effect_risk')
                ->where('effect_id','=',$GLOBALS['id1'])
                ->delete();

            //ahora eliminamos efecto
            DB::table('effects')
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

            $GLOBALS['res'] = 0;
        });

        return $res;
    }
}
