<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;


class EfectosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $efectos = array();
        if (isset($_GET['verbloqueados']))
        {
            $efectos2 = \Ermtool\Effect::all()->where('estado',1); //select efectos bloqueados  
        }
        else
        {
            $efectos2 = \Ermtool\Effect::all()->where('estado',0); //select efectos desbloqueados
        }

        $i = 0;

        // ---recorremos todas las efectos para asignar formato de datos correspondientes--- //
        foreach ($efectos2 as $efecto)
        {

            $efectos[$i] = array('id'=>$efecto['id'],
                                'nombre'=>$efecto['nombre'],
                                'descripcion'=>$efecto['descripcion'],
                                'fecha_creacion'=>$efecto['fecha_creacion'],
                                'estado'=>$efecto['estado']);
            $i += 1;
        }
        return view('datos_maestros.efectos.index',['efectos'=>$efectos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('datos_maestros.efectos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //obtenemos orden correcto de fecha creación
        $fecha = explode("/",$request['fecha_creacion']);
        $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];

        \Ermtool\Effect::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            ]);

            Session::flash('message','Efecto agregado correctamente');

            return Redirect::to('/efectos');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $efecto = \Ermtool\Effect::find($id);

        return view('datos_maestros.efectos.edit',['efecto'=>$efecto]);
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
        $efecto = \Ermtool\Effect::find($id);
        $fecha_creacion = $efecto->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada

        if (strpos($request['fecha_creacion'],'/')) //primero verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha creación
            $fecha = explode("/",$request['fecha_creacion']);
            $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }

        $efecto->nombre = $request['nombre'];
        $efecto->descripcion = $request['descripcion'];
        $efecto->fecha_creacion = $fecha_creacion;

        $efecto->save();

        Session::flash('message','Efecto actualizado correctamente');

        return Redirect::to('/efectos');
    }

    public function bloquear($id)
    {
        $efecto = \Ermtool\Effect::find($id);
        $efecto->estado = 1;
        $efecto->save();

        Session::flash('message','Efecto bloqueado correctamente');

        return Redirect::to('/efectos');
    }

    public function desbloquear($id)
    {
        $efecto = \Ermtool\Effect::find($id);
        $efecto->estado = 0;
        $efecto->save();

        Session::flash('message','Efecto desbloqueado correctamente');

        return Redirect::to('/efectos');
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
