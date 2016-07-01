<?php
namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DateTime;
use DB;

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
            $risk_categories = \Ermtool\Risk_category::where('status',1)->get(); //select categorias bloqueadas  
        }
        else
        {
            $risk_categories = \Ermtool\Risk_category::where('status',0)->get(); //select categorias desbloqueadas
        }

        $i = 0;
        $categorias_dependientes = array();
        // ---recorremos todas las categorias para asignar formato de datos correspondientes--- //
        foreach ($risk_categories as $category)
        {

            //buscamos categorias que dependen de ésta
            $cat_dependientes = \Ermtool\Risk_category::where('risk_category_id',$category['id'])->get();
            
            $j = 0;
            foreach ($cat_dependientes as $hijos)
            {
                $categorias_dependientes[$j] = array('risk_category_id'=>$category['id'],
                                             'id'=>$hijos['id'],
                                             'nombre'=>$hijos['name']);
                $j += 1;
            }

            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($category['created_at'] == NULL OR $category['created_at'] == "0000-00-00" OR $category['created_at'] == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }
            else
            {
                $fecha_creacion = date_format($category['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($category['created_at'],"H:i:s");
            }

             //damos formato a fecha expiración
            if ($category['expiration_date'] == NULL OR $category['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
            {
                $expiration_date = new DateTime($category['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            $risk_category[$i] = array('id'=>$category['id'],
                                'nombre'=>$category['name'],
                                'descripcion'=>$category['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_exp'=>$fecha_exp,
                                'estado'=>$category['status']);
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
        $categorias = \Ermtool\Risk_category::where('risk_category_id',NULL)->where('status',0)->lists('name','id');
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
        DB::transaction(function()
        {

            if ($_POST['risk_category_id'] == NULL)
            {
                $risk_category_id = NULL;
            }
            else
            {
                $risk_category_id = $_POST['risk_category_id'];
            }

            \Ermtool\Risk_category::create([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'expiration_date' => $_POST['expiration_date'],
                'risk_category_id' => $risk_category_id,
                ]);

                Session::flash('message','Categor&iacute;a agregada correctamente');
        });

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
                                            ->where('status',0)
                                            ->where('id','<>',$id)
                                            ->lists('name','id');

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
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
            $fecha_exp = NULL;

            //vemos si tiene categoría padre
            if($_POST['risk_category_id'] != "")
            {
                $risk_category_id = $_POST['risk_category_id'];
            }
            else
            {
                $risk_category_id = NULL;
            }

            $risk_category->name = $_POST['name'];
            $risk_category->description = $_POST['description'];
            $risk_category->expiration_date = $_POST['expiration_date'];
            $risk_category->risk_category_id = $risk_category_id;

            $risk_category->save();

            Session::flash('message','Categor&iacute;a de riesgo actualizada correctamente');
        });

        return Redirect::to('/categorias_riesgos');
    }

    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
            $risk_category->status = 1;
            $risk_category->save();

            Session::flash('message','Categor&iacute;a de riesgo bloqueada correctamente');
        });
        return Redirect::to('/categorias_riesgos');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
            $risk_category->status = 0;
            $risk_category->save();

            Session::flash('message','Categor&iacute;a de riesgo desbloqueada correctamente');
        });
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
