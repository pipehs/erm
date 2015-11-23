<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;


class CausasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $causas = array();
        if (isset($_GET['verbloqueados']))
        {
            $causas2 = \Ermtool\Cause::all()->where('estado',1); //select causas bloqueadas  
        }
        else
        {
            $causas2 = \Ermtool\Cause::all()->where('estado',0); //select causas desbloqueadas
        }

        $i = 0;

        // ---recorremos todas las causas para asignar formato de datos correspondientes--- //
        foreach ($causas2 as $causa)
        {

            $causas[$i] = array('id'=>$causa['id'],
                                'nombre'=>$causa['nombre'],
                                'descripcion'=>$causa['descripcion'],
                                'fecha_creacion'=>$causa['fecha_creacion'],
                                'estado'=>$causa['estado']);
            $i += 1;
        }
        return view('datos_maestros.causas.index',['causas'=>$causas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('datos_maestros.causas.create');
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

        \Ermtool\Cause::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            ]);

            Session::flash('message','Causa agregada correctamente');

            return Redirect::to('/causas');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $causa = \Ermtool\Cause::find($id);

        return view('datos_maestros.causas.edit',['causa'=>$causa]);
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
        $causa = \Ermtool\Cause::find($id);
        $fecha_creacion = $causa->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada

        if (strpos($request['fecha_creacion'],'/')) //primero verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha creación
            $fecha = explode("/",$request['fecha_creacion']);
            $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }

        $causa->nombre = $request['nombre'];
        $causa->descripcion = $request['descripcion'];
        $causa->fecha_creacion = $fecha_creacion;

        $causa->save();

        Session::flash('message','Causa actualizada correctamente');

        return Redirect::to('/causas');
    }

    public function bloquear($id)
    {
        $causa = \Ermtool\Cause::find($id);
        $causa->estado = 1;
        $causa->save();

        Session::flash('message','Causa bloqueada correctamente');

        return Redirect::to('/causas');
    }

    public function desbloquear($id)
    {
        $causa = \Ermtool\Cause::find($id);
        $causa->estado = 0;
        $causa->save();

        Session::flash('message','Causa desbloqueada correctamente');

        return Redirect::to('/causas');
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
