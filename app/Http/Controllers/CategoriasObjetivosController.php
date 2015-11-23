<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class CategoriasObjetivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objective_category = array();
        if (isset($_GET['verbloqueados']))
        {
            $objective_categories = \Ermtool\Objective_category::all()->where('estado',1); //select categorias bloqueadas  
        }
        else
        {
            $objective_categories = \Ermtool\Objective_category::all()->where('estado',0); //select categorias desbloqueadas
        }

        $i = 0;

        // ---recorremos todas las categorias para asignar formato de datos correspondientes--- //
        foreach ($objective_categories as $category)
        {
             //damos formato a fecha expiración
            if ($category['fecha_exp'] == NULL OR $category['fecha_exp'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $category['fecha_exp'];

            $objective_category[$i] = array('id'=>$category['id'],
                                'nombre'=>$category['nombre'],
                                'descripcion'=>$category['descripcion'],
                                'fecha_creacion'=>$category['fecha_creacion'],
                                'fecha_exp'=>$fecha_exp,
                                'estado'=>$category['estado']);
            $i += 1;
        }
        return view('datos_maestros.categorias_objetivos.index',['objective_categories'=>$objective_categories]);   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('datos_maestros.categorias_objetivos.create');
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

        //obtenemos orden correcto de fecha expiración
        if ($request['fecha_exp'] != "")
        {
            $fecha = explode("/",$request['fecha_exp']);
            $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }
        else
        {
            $fecha_exp = NULL;
        }

        \Ermtool\Objective_category::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            ]);

            Session::flash('message','Categor&iacute;a agregada correctamente');

            return Redirect::to('/categorias_objetivos');
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
        $objective_category = \Ermtool\Objective_category::find($id);

        return view('datos_maestros.categorias_objetivos.edit',['objective_category'=>$objective_category]);
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
        $objective_category = \Ermtool\Objective_category::find($id);
        $fecha_creacion = $objective_category->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
        $fecha_exp = NULL;

        if (strpos($request['fecha_creacion'],'/')) //primero verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha creación
            $fecha = explode("/",$request['fecha_creacion']);
            $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }

        if (strpos($request['fecha_exp'],'/')) //lo mismo para fecha de expiración
        {
            //obtenemos orden correcto de fecha expiración
            if ($request['fecha_exp'] != "" OR $request['fecha_exp'] != "0000-00-00")
            {
                $fecha = explode("/",$request['fecha_exp']);
                $fecha_exp = $fecha[2]."-".$fecha[0]."-".$fecha[1];
            }
            else
            {
                $fecha_exp = NULL;
            }
        }

        $objective_category->nombre = $request['nombre'];
        $objective_category->descripcion = $request['descripcion'];
        $objective_category->fecha_creacion = $fecha_creacion;
        $objective_category->fecha_exp = $fecha_exp;

        $objective_category->save();

        Session::flash('message','Categor&iacute;a de objetivo actualizada correctamente');

        return Redirect::to('/categorias_objetivos');
    }

    public function bloquear($id)
    {
        $objective_category = \Ermtool\Objective_category::find($id);
        $objective_category->estado = 1;
        $objective_category->save();

        Session::flash('message','Categor&iacute;a de objetivo bloqueada correctamente');

        return Redirect::to('/categorias_objetivos');
    }

    public function desbloquear($id)
    {
        $objective_category = \Ermtool\Objective_category::find($id);
        $objective_category->estado = 0;
        $objective_category->save();

        Session::flash('message','Categor&iacute;a de objetivo desbloqueada correctamente');

        return Redirect::to('/categorias_objetivos');
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
