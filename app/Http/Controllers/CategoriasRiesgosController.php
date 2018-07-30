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

//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class CategoriasRiesgosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $logger;
    //Hacemos función de construcción de logger (generico será igual para todas las clases, cambiando el nombre del elemento)
    public function __construct()
    {
        $dir = str_replace('public','',$_SERVER['DOCUMENT_ROOT']);
        $this->logger = new Logger('categorias_riesgos');
        $this->logger->pushHandler(new StreamHandler($dir.'/storage/logs/categorias_riesgos.log', Logger::INFO));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    public function index()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
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
                $j = 0;
                // ---recorremos todas las categorias para asignar formato de datos correspondientes--- //
                foreach ($risk_categories as $category)
                {

                    //buscamos categorias que dependen de ésta
                    $cat_dependientes = \Ermtool\Risk_category::where('risk_category_id',$category['id'])->get();
                    
                    
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
                        $fecha_creacion = NULL;
                    }
                    else
                    {
                        $lala = new DateTime($category['created_at']);
                        $fecha_creacion = date_format($lala,"d-m-Y");
                        //$fecha_creacion = date_format($category['created_at'],"d-m-Y");
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

                    //ACT 25-04: HACEMOS DESCRIPCIÓN CORTA (100 caracteres)
                    $short_des = substr($category['description'],0,100);

                    $risk_category[$i] = array('id'=>$category['id'],
                                        'nombre'=>$category['name'],
                                        'descripcion'=>$category['description'],
                                        'fecha_creacion'=>$fecha_creacion,
                                        'fecha_exp'=>$fecha_exp,
                                        'estado'=>$category['status'],
                                        'short_des'=>$short_des);
                    $i += 1;
                }

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.categorias_riesgos.index',['risk_categories'=>$risk_category,'categorias_dependientes'=>$categorias_dependientes]);
                }
                else
                {
                    return view('datos_maestros.categorias_riesgos.index',['risk_categories'=>$risk_category,'categorias_dependientes'=>$categorias_dependientes]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                //Seleccionamos categorías que pueden ser padres
                //ACTUALIZACIÓN 31-10-17: Existirán distintos niveles de categorías, por lo que no sólo se mostrarán las de primer nivel, sino que todas
                //$categorias = \Ermtool\Risk_category::where('risk_category_id',NULL)->where('status',0)->lists('name','id');
                $categorias = \Ermtool\Risk_category::where('status',0)->lists('name','id');

                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.categorias_riesgos.create',['categorias'=>$categorias]);
                }
                else
                {
                    return view('datos_maestros.categorias_riesgos.create',['categorias'=>$categorias]);
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    if (!isset($_POST['risk_category_id']) || $_POST['risk_category_id'] == NULL || $_POST['risk_category_id'] == '')
                    {
                        $risk_category_id = NULL;
                    }
                    else
                    {
                        $risk_category_id = $_POST['risk_category_id'];
                    }

                    $category = \Ermtool\Risk_category::create([
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'expiration_date' => $_POST['expiration_date'],
                        'risk_category_id' => $risk_category_id,
                        ]);

                        if (Session::get('languaje') == 'en')
                        {
                            Session::flash('message','Risk category successfully created');
                        }
                        else
                        {
                            Session::flash('message','Categor&iacute;a agregada correctamente');
                        }

                        $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha creado la categoría de riesgo con Id: '.$category->id.' llamada: '.$category->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('/categorias_risks');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                $risk_category = \Ermtool\Risk_category::find($id);

                //ACTUALIZACIÓN 31-10-17: Existirán distintos niveles de categorías, por lo que no sólo se mostrarán las de primer nivel, sino que todas
                //Seleccionamos categorias que pueden ser padres
                //$categorias = \Ermtool\Risk_category::where('risk_category_id',NULL)
                //                                    ->where('status',0)
                //                                    ->where('id','<>',$id)
                //                                    ->lists('name','id');
                global $id2;
                $id2 = $id;
                $categorias = \Ermtool\Risk_category::where('id','<>',$id)
                                                ->where('status',0)
                                                ->where((function ($query) {
                                                $query->where('risk_category_id','<>',$GLOBALS['id2'])
                                                    ->orWhere('risk_category_id','=',NULL);
                                                }))
                                                ->lists('name','id');
                                                
                if (Session::get('languaje') == 'en')
                {
                    return view('en.datos_maestros.categorias_riesgos.edit',['risk_category'=>$risk_category,
                    'categorias'=>$categorias]); 
                }
                else
                {
                    return view('datos_maestros.categorias_riesgos.edit',['risk_category'=>$risk_category,
                    'categorias'=>$categorias]); 
                }
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
                    $fecha_exp = NULL;

                    //vemos si tiene categoría padre
                    if (!isset($_POST['risk_category_id']) || $_POST['risk_category_id'] == NULL || $_POST['risk_category_id'] == '')
                    {
                        $risk_category_id = NULL;
                    }
                    else
                    {
                        $risk_category_id = $risk_category_id = $_POST['risk_category_id'];;
                    }

                    $risk_category->name = $_POST['name'];
                    $risk_category->description = $_POST['description'];
                    $risk_category->expiration_date = $_POST['expiration_date'];
                    $risk_category->risk_category_id = $risk_category_id;

                    $risk_category->save();
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Risk category succcessfully updated');
                    }
                    else
                    {
                        Session::flash('message','Categor&iacute;a de riesgo actualizada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha actualizado la categoría de riesgo con Id: '.$GLOBALS['id1'].' llamada: '.$risk_category->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });

                return Redirect::to('/categorias_risks');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function bloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
                    $risk_category->status = 1;
                    $risk_category->save();

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Risk category successfully blocked');
                    }
                    else
                    {
                        Session::flash('message','Categor&iacute;a de riesgo bloqueada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha bloqueado la categoría de riesgo con Id: '.$GLOBALS['id1'].' llamada: '.$risk_category->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/categorias_risks');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function desbloquear($id)
    {
        try
        {
            if (Auth::guest())
            {
                return Redirect::route('/');
            }
            else
            {
                global $id1;
                $id1 = $id;
                DB::transaction(function()
                {
                    $logger = $this->logger;
                    $risk_category = \Ermtool\Risk_category::find($GLOBALS['id1']);
                    $risk_category->status = 0;
                    $risk_category->save();
                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','Risk category successfully unblocked');
                    }
                    else
                    {
                        Session::flash('message','Categor&iacute;a de riesgo desbloqueada correctamente');
                    }

                    $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha desbloqueado la categoría de riesgo con Id: '.$GLOBALS['id1'].' llamada: '.$risk_category->name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
                });
                return Redirect::to('/categorias_risks');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
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
        try
        {
            global $id1;
            $id1 = $id;
            global $res;
            $res = 1;
            DB::transaction(function() {

                $logger = $this->logger;

                $name = \Ermtool\Risk_category::name($GLOBALS['id1']);
                //borramos de riesgo (si es que existe)
                DB::table('risks')
                ->where('risk_category_id','=',$GLOBALS['id1'])
                ->update(['risk_category_id' => NULL]);

                //actualizamos otras posibles categorías de riesgo donde se encuentre esta categoría
                DB::table('risk_categories')
                    ->where('risk_category_id','=',$GLOBALS['id1'])
                    ->update(['risk_category_id' => NULL]);

                //eliminamos categoría
                DB::table('risk_categories')
                    ->where('id','=',$GLOBALS['id1'])
                    ->delete();

                //Si todo pasa, res se asigna como 0
                $GLOBALS['res'] = 0;

                $logger->info('El usuario '.Auth::user()->name.' '.Auth::user()->surnames. ', Rut: '.Auth::user()->id.', ha eliminado la categoría de riesgo con Id: '.$GLOBALS['id1'].' llamada: '.$name.', con fecha '.date('d-m-Y').' a las '.date('H:i:s'));
            });
            
            return $res;
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function getSubCategories($risk_category_id)
    {
        try
        {
            $cat = DB::table('risk_categories')
                        ->where('risk_category_id','=',$risk_category_id)
                        ->select('id','name')
                        ->get();

            return json_encode($cat);
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }
}
