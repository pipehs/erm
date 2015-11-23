<?php
namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class CategoriasRiesgosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $risk_category = array();
        if (isset($_GET['verbloqueados']))
        {
            $risk_categories = \Ermtool\Risk_category::all()->where('estado',1); //select categorias bloqueadas  
        }
        else
        {
            $risk_categories = \Ermtool\Risk_category::all()->where('estado',0); //select categorias desbloqueadas
        }

        $i = 0;
        $categorias_dependientes = array();
        // ---recorremos todas las categorias para asignar formato de datos correspondientes--- //
        foreach ($risk_categories as $category)
        {

            //buscamos categorias que dependen de ésta
            $cat_dependientes = \Ermtool\Risk_category::all()->where('risk_category_id',$category['id']);
            
            $j = 0;
            foreach ($cat_dependientes as $hijos)
            {
                $categorias_dependientes[$j] = array('risk_category_id'=>$category['id'],
                                             'id'=>$hijos['id'],
                                             'nombre'=>$hijos['nombre']);
                $j += 1;
            }

             //damos formato a fecha expiración
            if ($category['fecha_exp'] == NULL OR $category['fecha_exp'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $category['fecha_exp'];

            $risk_category[$i] = array('id'=>$category['id'],
                                'nombre'=>$category['nombre'],
                                'descripcion'=>$category['descripcion'],
                                'fecha_creacion'=>$category['fecha_creacion'],
                                'fecha_exp'=>$fecha_exp,
                                'estado'=>$category['estado']);
            $i += 1;
        }

        return view('datos_maestros.categorias_riesgos.index',['risk_categories'=>$risk_category,'categorias_dependientes'=>$categorias_dependientes]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Seleccionamos categorías que pueden ser padres
        $categorias = \Ermtool\Risk_category::where('risk_category_id',NULL)->where('estado',0)->lists('nombre','id');
        return view('datos_maestros.categorias_riesgos.create',['categorias'=>$categorias]);
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

        if ($request['risk_category_id'] == NULL)
        {
            $risk_category_id = NULL;
        }
        else
        {
            $risk_category_id = $request['risk_category_id'];
        }

        \Ermtool\Risk_category::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            'risk_category_id' => $risk_category_id,
            ]);

            Session::flash('message','Categor&iacute;a agregada correctamente');

            return Redirect::to('/categorias_riesgos');
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
        $risk_category = \Ermtool\Risk_category::find($id);

        //Seleccionamos categorias que pueden ser padres
        $categorias = \Ermtool\Risk_category::where('risk_category_id',NULL)
                                            ->where('estado',0)
                                            ->where('id','<>',$id)
                                            ->lists('nombre','id');

        return view('datos_maestros.categorias_riesgos.edit',['risk_category'=>$risk_category,
            'categorias'=>$categorias]);
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
        $risk_category = \Ermtool\Risk_category::find($id);
        $fecha_creacion = $risk_category->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
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

        //vemos si tiene categoría padre
        if($request['risk_category_id'] != "")
        {
            $risk_category_id = $request['risk_category_id'];
        }
        else
        {
            $risk_category_id = NULL;
        }

        $risk_category->nombre = $request['nombre'];
        $risk_category->descripcion = $request['descripcion'];
        $risk_category->fecha_creacion = $fecha_creacion;
        $risk_category->fecha_exp = $fecha_exp;
        $risk_category->risk_category_id = $risk_category_id;

        $risk_category->save();

        Session::flash('message','Categor&iacute;a de riesgo actualizada correctamente');

        return Redirect::to('/categorias_riesgos');
    }

    public function bloquear($id)
    {
        $risk_category = \Ermtool\Risk_category::find($id);
        $risk_category->estado = 1;
        $risk_category->save();

        Session::flash('message','Categor&iacute;a de riesgo bloqueada correctamente');

        return Redirect::to('/categorias_riesgos');
    }

    public function desbloquear($id)
    {
        $risk_category = \Ermtool\Risk_category::find($id);
        $risk_category->estado = 0;
        $risk_category->save();

        Session::flash('message','Categor&iacute;a de riesgo desbloqueada correctamente');

        return Redirect::to('/categorias_riesgos');
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
