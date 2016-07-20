<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use dateTime;
use DB;

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
            $objetivos = \Ermtool\Objective::where('organization_id',(int)$_GET['organizacion'])
                                                ->where('status',0)->get();
            $nombre_organizacion = \Ermtool\Organization::name($_GET['organizacion']);
            $i=0; //para saber si hay objetivos
            $objectives = array(); //almacenará los objetivos con el formato correcto de sus atributos
            foreach ($objetivos as $objetivo)
            {
                $i = $i+1;
                 //damos formato a fecha expiración
                if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
                {
                    $fecha_exp = NULL;
                }
                else 
                {
                    $expiration_date = new DateTime($objetivo['expiration_date']);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                }

                //damos formato a fecha creación
                if ($objetivo['created_at'] != NULL)
                {
                    $fecha_creacion = date_format($objetivo['created_at'],"d-m-Y");
                }
                else
                    $fecha_creacion = NULL;

                //damos formato a fecha de actualización 
                if ($objetivo['updated_at'] != NULL)
                {
                    $fecha_act = date_format($objetivo['updated_at'],"d-m-Y");
                }
                else
                    $fecha_act = NULL;

                //damos formato a categoría de objetivo
                if ($objetivo['objective_category_id'] == NULL)
                {
                    $categoria = NULL;
                }
                else
                    $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('name');

                if ($objetivo['perspective'] == NULL)
                {
                    $perspective = NULL;
                }
                else
                {
                    $perspective = $objetivo['perspective'];   
                }

                $objectives[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['name'],
                                'descripcion'=>$objetivo['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['status'],
                                'perspective' => $perspective);
                $i += 1;

            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.objetivos.index',['organizations'=>$organizations,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i]);
            }
            else
            {
                return view('datos_maestros.objetivos.index',['organizations'=>$organizations,'objetivos'=>$objectives,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i]);
            }
        }
        else
        {
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.objetivos.index',['organizations'=>$organizations]);
            }
            else
            {
                return view('datos_maestros.objetivos.index',['organizations'=>$organizations]);
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
        $categorias = \Ermtool\Objective_category::where('status',0)->lists('name','id');

        $org_id = \Ermtool\Organization::where('id',$_GET['organizacion'])->value('id');
        if (Session::get('languaje') == 'en')
        {
            return view('en.datos_maestros.objetivos.create',['categorias'=>$categorias,'org_id'=>$org_id]);
        }
        else
        {
            return view('datos_maestros.objetivos.create',['categorias'=>$categorias,'org_id'=>$org_id]);
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
        DB::transaction(function() 
        {
            //si es que se agrego categoría de objetivo
            if ($_POST['objective_category_id'])
            {
                $categoria = $_POST['objective_category_id'];
            }
            else
            {
                $categoria = NULL;
            }
            \Ermtool\Objective::create([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'expiration_date' => $_POST['expiration_date'],
                'objective_category_id' => $categoria,
                'organization_id' => $_POST['organization_id'],
                'status' => 0,
                'perspective' => $_POST['perspective']
                ]);

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Bussiness objective was successfully created');
            }
            else
            {
                Session::flash('message','Objetivo corporativo agregado correctamente');
            }
        });
        return Redirect::to('/objetivos?organizacion='.$_POST['organization_id']);
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
        $org_id = \Ermtool\Organization::where('id',$objetivo['organization_id'])->value('id');
        if (Session::get('languaje') == 'en')
        {
            return view('en.datos_maestros.objetivos.edit',['categorias'=>$categorias,'objetivo'=>$objetivo,'org_id'=>$org_id]);
        }
        else
        {
            return view('datos_maestros.objetivos.edit',['categorias'=>$categorias,'objetivo'=>$objetivo,'org_id'=>$org_id]);
        }
        
    }

    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function() 
        {
            $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);
            $objetivo->status = 1;
            $objetivo->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Objective successfully blocked');
            }
            else
            {
                Session::flash('message','Objetivo bloqueado correctamente');
            }
        });

        return Redirect::to('/objetivos');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function() 
        {
            $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);
            $objetivo->status = 0;
            $objetivo->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Objective successfully unblocked');
            }
            else
            {
                Session::flash('message','Objetivo desbloqueado correctamente');
            }
        });

        //obtenemos org
            $id_org = \Ermtool\Objective::where('id',$id)->value('organization_id');
        return Redirect::to('/objetivos?organizacion='.$id_org);
    }

    public function verbloqueados($id_organizacion)
    {
        $combobox = \Ermtool\Organization::where('status',0)
                                        ->lists('name','id'); //guardamos array con lista de nombre de organizaciones + id

        $nombre_organizacion = \Ermtool\Organization::name($id_organizacion);

        $objective = array();
        $objetivos = \Ermtool\Objective::where('status',1)
                                            ->where('organization_id',(int)$id_organizacion)->get(); //select objetivos bloqueadas

        $i = 0;
        // ---recorremos todas las organizaciones para asignar formato de datos correspondientes--- //
        foreach ($objetivos as $objetivo)
        {

            //damos formato a fecha expiración
            if ($objetivo['expiration_date'] == NULL OR $objetivo['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = NULL;
            }
            else 
                $fecha_exp = $objetivo['fecha_exp'];

            //damos formato a categoría de objetivo
            if ($objetivo['objective_category_id'] == NULL)
            {
                 $categoria = NULL;
            }
            else
                $categoria = \Ermtool\Objective_category::where('id',$objetivo['objective_category_id'])->value('name');

            if ($objetivo['perspective'] == NULL)
            {
                $perspective = NULL;
            }
            else
            {
                $perspective = $objetivo['perspective'];   
            }

            $objective[$i] = array('id'=>$objetivo['id'],
                                'nombre'=>$objetivo['name'],
                                'descripcion'=>$objetivo['description'],
                                'fecha_creacion'=>$objetivo['created_at'],
                                'fecha_act'=>$objetivo['updated_at'],
                                'fecha_exp'=>$fecha_exp,
                                'categoria'=>$categoria,
                                'estado'=>$objetivo['status'],
                                'perspective' => $perspective);
            $i += 1;
        }

        if (Session::get('languaje') == 'en')
        {
            return view('en.datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objective,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i,'organizacion'=>$id_organizacion]);
        }
        else
        {
            return view('datos_maestros.objetivos.index',['organizations'=>$combobox,'objetivos'=>$objective,'nombre_organizacion'=>$nombre_organizacion,'probador' => $i,'organizacion'=>$id_organizacion]);
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
        global $id1;
        $id1 = $id;
        DB::transaction(function() 
        {
            $objetivo = \Ermtool\Objective::find($GLOBALS['id1']);
            $fecha_creacion = $objetivo->created_at; //Se debe obtener fecha de creación por si no fue modificada
            $fecha_exp = NULL;

            //vemos si tiene categoría
            if($_POST['objective_category_id'] != "")
            {
                $categoria = $_POST['objective_category_id'];
            }
            else
            {
                $categoria = NULL;
            }
            if ($_POST['perspective'] != "")
            {
                $perspective = $_POST['perspective'];
            }
            else
            {
                $perspective = NULL;
            }

            $objetivo->name = $_POST['name'];
            $objetivo->description = $_POST['description'];
            $objetivo->expiration_date = $_POST['expiration_date'];
            $objetivo->objective_category_id = $categoria;
            $objetivo->perspective = $perspective;

            $objetivo->save();

            if (Session::get('languaje') == 'en')
            {
                Session::flash('message','Objective was successfully updated');
            }
            else
            {
                Session::flash('message','Objetivo actualizado correctamente');
            }
            
        });

        $id_org = \Ermtool\Objective::where('id',$id)->value('organization_id');
        return Redirect::to('/objetivos?organizacion='.$id_org);
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

    //obtiene objetivos de una organización
    public function getObjectives($org)
    {
        $results = array();
        //obtenemos objetivos
        $objectives = \Ermtool\Objective::where('status',0)
                                        ->where('organization_id',(int)$org)->get();

        foreach ($objectives as $objective)
        {
            $results = [
                'id' => $objective->id,
                'name' => $objective->name,
            ];
        }
        
        return json_encode($results);
    }
}
