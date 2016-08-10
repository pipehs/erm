<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;
use DB;
use Auth;

class CategoriasObjetivosController extends Controller
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
            $objective_category = array();
            if (isset($_GET['verbloqueados']))
            {
                $objective_categories = \Ermtool\Objective_category::where('status',1)->get(); //select categorias bloqueadas  
            }
            else
            {
                $objective_categories = \Ermtool\Objective_category::where('status',0)->get(); //select categorias desbloqueadas
            }

            $i = 0;

            // ---recorremos todas las categorias para asignar formato de datos correspondientes--- //
            foreach ($objective_categories as $category)
            {
                //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
                if ($category['created_at'] == NULL OR $category['created_at'] == "0000-00-00" OR $category['created_at'] == "")
                {
                    $fecha_creacion = NULL;
                }
                else
                {
                    $fecha_creacion = date_format($category['created_at'],"d-m-Y");
                }

                 //damos formato a fecha expiración
                if ($category['expiration_date'] == NULL OR $category['expiration_date'] == "0000-00-00")
                {
                    $fecha_exp = NULL;
                }
                else
                {
                    $expiration_date = new DateTime($category['expiration_date']);
                    $fecha_exp = date_format($expiration_date, 'd-m-Y');
                }

                //damos formato a fecha de actualización 
                if ($category['updated_at'] != NULL)
                {
                    $fecha_act = date_format($category['updated_at'],"d-m-Y");
                }

                else
                    $fecha_act = NULL;

                $objective_category[$i] = array('id'=>$category['id'],
                                    'nombre'=>$category['name'],
                                    'descripcion'=>$category['description'],
                                    'fecha_creacion'=>$fecha_creacion,
                                    'fecha_act'=>$fecha_act,
                                    'fecha_exp'=>$fecha_exp,
                                    'estado'=>$category['status']);
                $i += 1;
            }
            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.categorias_objetivos.index',['objective_categories'=>$objective_category]); 
            }
            else
            {
                return view('datos_maestros.categorias_objetivos.index',['objective_categories'=>$objective_category]); 
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
                return view('en.datos_maestros.categorias_objetivos.create');
            }
            else
            {
                return view('datos_maestros.categorias_objetivos.create');
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
            DB::transaction(function() {

                \Ermtool\Objective_category::create([
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'expiration_date' => $_POST['expiration_date'],
                    ]);

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Category stored successfully');
                    }
                    else
                    {
                        Session::flash('message','Categor&iacute;a agregada correctamente');
                    }

            });

            return Redirect::to('/categorias_objetivos');
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
            $objective_category = \Ermtool\Objective_category::find($id);

            if (Session::get('languaje') == 'en')
            {
                return view('en.datos_maestros.categorias_objetivos.edit',['objective_category'=>$objective_category]);
            }
            else
            {
                return view('datos_maestros.categorias_objetivos.edit',['objective_category'=>$objective_category]);
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
            global $id1;
            $id1 = $id;
            DB::transaction(function() {
                $objective_category = \Ermtool\Objective_category::find($GLOBALS['id1']);
                $fecha_creacion = $objective_category->created_at; //Se debe obtener fecha de creación por si no fue modificada

                $objective_category->name = $_POST['name'];
                $objective_category->description = $_POST['description'];
                $objective_category->expiration_date = $_POST['expiration_date'];

                $objective_category->save();
                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Category updated successfully');
                }
                else
                {
                    Session::flash('message','Categor&iacute;a de objetivo actualizada correctamente');
                }
            });

            return Redirect::to('/categorias_objetivos');
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
            global $id1;
            $id1 = $id;
            DB::transaction(function() {
                $objective_category = \Ermtool\Objective_category::find($GLOBALS['id1']);
                $objective_category->status = 1;
                $objective_category->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Category blocked successfully');
                }
                else
                {
                    Session::flash('message','Categor&iacute;a de objetivo bloqueada correctamente');
                }
            });
            return Redirect::to('/categorias_objetivos');
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
            global $id1;
            $id1 = $id;
            DB::transaction(function() {
                $objective_category = \Ermtool\Objective_category::find($GLOBALS['id1']);
                $objective_category->status = 0;
                $objective_category->save();

                if (Session::get('languaje') == 'en')
                {
                    Session::flash('message','Category unblocked successfully');
                }
                else
                {
                    Session::flash('message','Categor&iacute;a de objetivo desbloqueada correctamente');
                }
            });
            return Redirect::to('/categorias_objetivos');
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
            //borramos de objetivo (si es que existe en objetivo)
            DB::table('objectives')
            ->where('objective_category_id','=',$GLOBALS['id1'])
            ->update(['objective_category_id' => NULL]);

            DB::table('objective_categories')
                ->where('id','=',$GLOBALS['id1'])
                ->delete();

            $GLOBALS['res'] = 0;
        });
        
        return $res;
    }
}
