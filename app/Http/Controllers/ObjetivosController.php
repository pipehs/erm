<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use dateTime;

class ObjetivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id'); //select organizaciones desbloqueadas en lista para select


        if (isset($_GET['organizacion'])) //se seleccionó la organización para ver objetivos
        {
            $objetivos = \Ermtool\Objective::all()->where('organization_id',(int)$_GET['organizacion'])
                                                ->where('status',0);
            $nombre_organizacion = \Ermtool\Organization::name($_GET['organizacion']);
            $i=0; //para saber si hay objetivos
            $objectives = array(); //almacenará los objetivos con el formato correcto de sus atributos
            foreach ($objetivos as $objetivo)
            {
                $i = $i+1;
                 //damos formato a fecha expiración
                if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
                {
                    $fecha_exp = "Ninguna";
                }
                else 
                {
                    $expiration_date = new DateTime($objetivo['expiration_date']);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                    $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
                }

                //damos formato a fecha creación
                if ($objetivo['created_at'] != NULL)
                {
                    $fecha_creacion = date_format($objetivo['created_at'],"d-m-Y");
                    $fecha_creacion .= " a las ".date_format($objetivo['created_at'],"H:i:s");
                }
                else
                    $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

                //damos formato a fecha de actualización 
                if ($objetivo['updated_at'] != NULL)
                {
                    $fecha_act = date_format($objetivo['updated_at'],"d-m-Y");
                    $fecha_act .= " a las ".date_format($objetivo['updated_at'],"H:i:s");
                }
                else
                    $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

                //damos formato a categoría de objetivo
                if ($objetivo['objective_category_id'] == NULL)
                {
                    $categoria = "Ninguna";
                }
                else
                    $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('name');

                $objectives[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['name'],
                                'descripcion'=>$objetivo['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['status']);
                $i += 1;

            }
            return view('datos_maestros.objetivos.index',['organizations'=>$organizations,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i]);
        }
        else
        {
            return view('datos_maestros.objetivos.index',['organizations'=>$organizations]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = \Ermtool\Objective_category::where('status',0)->lists('name','id');
        return view('datos_maestros.objetivos.create',['categorias'=>$categorias]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //obtenemos orden correcto de fecha expiración
        if ($request['expiration_date'] != "")
        {
            $fecha = explode("/",$request['expiration_date']);
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
            'name' => $request['name'],
            'description' => $request['description'],
            'fecha_exp' => $fecha_exp,
            'objective_category_id' => $categoria,
            'organization_id' => $request['organization_id'],
            'status' => 0,
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
        $categorias = \Ermtool\Objective_category::where('status',0)->lists('name','id');
        $objetivo = \Ermtool\Objective::find($id);
        return view('datos_maestros.objetivos.edit',['categorias'=>$categorias,'objetivo'=>$objetivo]);
    }

    public function bloquear($id)
    {
        $objetivo = \Ermtool\Objective::find($id);
        $objetivo->status = 1;
        $objetivo->save();

        Session::flash('message','Objetivo bloqueado correctamente');

        return Redirect::to('/objetivos');
    }

    public function desbloquear($id)
    {
        $objetivo = \Ermtool\Objective::find($id);
        $objetivo->status = 0;
        $objetivo->save();

        Session::flash('message','Objetivo desbloqueado correctamente');

        return Redirect::to('/objetivos');
    }

    public function verbloqueados($id_organizacion)
    {
        $combobox = \Ermtool\Organization::where('estado',0)
                                        ->lists('nombre','id'); //guardamos array con lista de nombre de organizaciones + id

        $nombre_organizacion = \Ermtool\Organization::name($id_organizacion);

        $objective = array();
        $objetivos = \Ermtool\Objective::all()->where('status',1)
                                            ->where('organization_id',(int)$id_organizacion); //select objetivos bloqueadas

        $i = 0;
        // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
        foreach ($objetivos as $objetivo)
        {
            //damos formato a categoria de objetivo
            if ($objetivo['objective_category_id'] != NULL)
            {
                $categoria = \Ermtool\Objective_category::find($objetivo['objective_category_id'])->value('name');
            }
            else 
                $categoria = "ninguna";

            //damos formato a fecha expiración
            if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $objetivo['fecha_exp'];

            $objective[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['name'],
                                'descripcion'=>$objetivo['description'],
                                'fecha_creacion'=>$objetivo['created_at'],
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['status']);
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
        $fecha_creacion = $objetivo->created_at; //Se debe obtener fecha de creación por si no fue modificada
        $fecha_exp = NULL;

        if (strpos($request['expiration_date'],'/')) //verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha expiración
            if ($request['expiration_date'] != "" OR $request['expiration_date'] != "0000-00-00")
            {
                $fecha = explode("/",$request['expiration_date']);
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

        $objetivo->name = $request['name'];
        $objetivo->description = $request['description'];
        $objetivo->expiration_date = $fecha_exp;
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
