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
                $fecha_creacion = date_format($efecto['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($efecto['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización
            if ($efecto['updated_at'] != NULL)
            {
                $fecha_act = date_format($efecto['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($efecto['updated_at'],"H:i:s");
            }
            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $efectos[$i] = array('id'=>$efecto['id'],
                                'nombre'=>$efecto['name'],
                                'descripcion'=>$efecto['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'estado'=>$efecto['status']);
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
        \Ermtool\Effect::create([
            'name' => $request['name'],
            'description' => $request['description'],
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

        $efecto->name = $request['name'];
        $efecto->description = $request['description'];

        $efecto->save();

        Session::flash('message','Efecto actualizado correctamente');

        return Redirect::to('/efectos');
    }

    public function bloquear($id)
    {
        $efecto = \Ermtool\Effect::find($id);
        $efecto->status = 1;
        $efecto->save();

        Session::flash('message','Efecto bloqueado correctamente');

        return Redirect::to('/efectos');
    }

    public function desbloquear($id)
    {
        $efecto = \Ermtool\Effect::find($id);
        $efecto->status = 0;
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
