<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class ObjetivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $organizations = \Ermtool\Organization::all()->where('estado',0); //select organizaciones desbloqueadas
        $combobox = $organizations->lists('nombre','id'); //guardamos array con lista de nombre de organizaciones + id

        if (isset($_GET['organizacion'])) //se seleccionó la organización para ver objetivos
        {
            $objetivos = \Ermtool\Objective::all()->where('organization_id',(int)$_GET['organizacion'])
                                                ->where('estado',0);
            $nombre_organizacion = \Ermtool\Organization::nombre($_GET['organizacion']);
            $i=0; //para saber si hay objetivos
            $objectives = array(); //almacenará los objetivos con el formato correcto de sus atributos
            foreach ($objetivos as $objetivo)
            {
                $i = $i+1;
                 //damos formato a fecha expiración
                if ($objetivo['fecha_exp'] == NULL OR $objetivo['fecha_exp'] == "0000-00-00")
                {
                    $fecha_expiracion = "Ninguna";
                }
                else 
                    $fecha_expiracion = $objetivo['fecha_exp'];

                //damos formato a categoría de objetivo
                if ($objetivo['objective_category_id'] == NULL)
                {
                    $categoria = "Ninguna";
                }
                else
                    $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('nombre');

                $objectives[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['nombre'],
                                'descripcion'=>$objetivo['descripcion'],
                                'fecha_creacion'=>$objetivo['fecha_creacion'],
                                'fecha_exp'=>$fecha_expiracion,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['estado']);
                $i += 1;

            }
            return view('datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i]);
        }
        else
        {
            return view('datos_maestros.objetivos.index',['organizations'=>$combobox]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = \Ermtool\Objective_category::where('estado',0);
        $combobox = $categorias->lists('nombre','id');
        return view('datos_maestros.objetivos.create',['categorias'=>$combobox]);
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
        //si es que se agrego categoría de objetivo
        if ($request['objective_category_id'])
        {
            $categoria = $request['objective_category_id'];
        }
        else
        {
            $categoria = NULL;
        }
        \Ermtool\Objective::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            'objective_category_id' => $categoria,
            'organization_id' => $request['organizacion'],
            'estado' => 0,
            ]);

        Session::flash('message','Objetivo corporativo agregado correctamente');

        return Redirect::to('/objetivos');
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
        $categorias = \Ermtool\Objective_category::where('estado',0);
        $objetivo = \Ermtool\Objective::find($id);
        $combobox = $categorias->lists('nombre','id');
        return view('datos_maestros.objetivos.edit',['categorias'=>$combobox,'objetivo'=>$objetivo]);
    }

    public function bloquear($id)
    {
        $objetivo = \Ermtool\Objective::find($id);
        $objetivo->estado = 1;
        $objetivo->save();

        Session::flash('message','Objetivo bloqueado correctamente');

        return Redirect::to('/objetivos');
    }

    public function desbloquear($id)
    {
        $objetivo = \Ermtool\Objective::find($id);
        $objetivo->estado = 0;
        $objetivo->save();

        Session::flash('message','Objetivo desbloqueado correctamente');

        return Redirect::to('/objetivos');
    }

    public function verbloqueados($id_organizacion)
    {
        $combobox = \Ermtool\Organization::where('estado',0)
                                        ->lists('nombre','id'); //guardamos array con lista de nombre de organizaciones + id

        $nombre_organizacion = \Ermtool\Organization::nombre($id_organizacion);

        $objective = array();
        $objetivos = \Ermtool\Objective::all()->where('estado',1)
                                            ->where('organization_id',(int)$id_organizacion); //select objetivos bloqueadas

        $i = 0;
        // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
        foreach ($objetivos as $objetivo)
        {
            //damos formato a categoria de objetivo
            if ($objetivo['objective_category_id'] != NULL)
            {
                $categoria = \Ermtool\Objective_category::find($objetivo['objective_category_id'])->value('nombre');
            }
            else 
                $categoria = "ninguna";

            //damos formato a fecha expiración
            if ($objetivo['fecha_exp'] == NULL OR $objetivo['fecha_exp'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $objetivo['fecha_exp'];

            $objective[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['nombre'],
                                'descripcion'=>$objetivo['descripcion'],
                                'fecha_creacion'=>$objetivo['fecha_creacion'],
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['estado']);
            $i += 1;
        }

        return view('datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objective,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i,'organizacion'=>$id_organizacion]);
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
        $objetivo = \Ermtool\Objective::find($id);
        $fecha_creacion = $objetivo->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
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

        //vemos si tiene categoría
        if($request['objective_category_id'] != "")
        {
            $categoria = $request['objective_category_id'];
        }
        else
        {
            $categoria = NULL;
        }

        $objetivo->nombre = $request['nombre'];
        $objetivo->descripcion = $request['descripcion'];
        $objetivo->fecha_creacion = $fecha_creacion;
        $objetivo->fecha_exp = $fecha_exp;
        $objetivo->objective_category_id = $categoria;

        $objetivo->save();

        Session::flash('message','Objetivo actualizado correctamente');

        return Redirect::to('/objetivos');
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
