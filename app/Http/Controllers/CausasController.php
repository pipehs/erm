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
                $fecha_creacion = date_format($causa['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($causa['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización
            if ($causa['updated_at'] != NULL)
            {
                $fecha_act = date_format($causa['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($causa['updated_at'],"H:i:s");
            }
            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $causas[$i] = array('id'=>$causa['id'],
                                'nombre'=>$causa['name'],
                                'descripcion'=>$causa['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'estado'=>$causa['status']);
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
        
        \Ermtool\Cause::create([
            'name' => $request['name'],
            'description' => $request['description'],
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

        $causa->name = $request['name'];
        $causa->description = $request['description'];

        $causa->save();

        Session::flash('message','Causa actualizada correctamente');

        return Redirect::to('/causas');
    }

    public function bloquear($id)
    {
        $causa = \Ermtool\Cause::find($id);
        $causa->status = 1;
        $causa->save();

        Session::flash('message','Causa bloqueada correctamente');

        return Redirect::to('/causas');
    }

    public function desbloquear($id)
    {
        $causa = \Ermtool\Cause::find($id);
        $causa->status = 0;
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
