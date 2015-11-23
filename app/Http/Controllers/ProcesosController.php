<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;

class ProcesosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proceso = array();

        if(isset($_GET['verbloqueados']))
        {
            $procesos = \Ermtool\Process::all()->where('estado',1); //select procesos bloqueados 
        }
        else
        {
            $procesos = \Ermtool\Process::all()->where('estado',0); //select procesos desbloqueados
        }
        $i = 0;
        // ---recorremos todas los procesos para asignar formato de datos correspondientes--- //
        $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
        $subprocesos = array(); //en este array almacenraemos todos los subprocesos relacionados a un proceso

        foreach ($procesos as $process)
        {
           //primero obtenemos subprocesos relacionados
            $subprocesses = \Ermtool\Process::find($process['id'])->subprocesses;
            $j = 0; //contador de subprocesos

            foreach ($subprocesses as $subprocess)
            {
                //ahora obtenemos todas las organizaciones a las que pertenece cada subproceso relacionado
                $orgs = \Ermtool\Subprocess::find($subprocess['id'])->organizations;
                $k = 0; //contador de organizaciones

                foreach ($orgs as $organization)
                {
                    $organizaciones[$k] = array('proceso_id'=>$process['id'],
                                                'id'=>$organization['id'],
                                                'nombre'=>$organization['nombre']);

                    $k += 1;
                }

                $subprocesos[$j] = array('proceso_id'=>$process['id'],
                                        'id'=>$subprocess['id'],
                                        'nombre'=>$subprocess['nombre']);

                $j += 1;

            }
            


            //damos formato a fecha expiración
            if ($process['fecha_exp'] == NULL OR $process['fecha_exp'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else 
                $fecha_exp = $process['fecha_exp'];

            //damos formato si depende de otro proceso
            if ($process['process_id'] == NULL)
            {
                $proceso_dependiente = "No";
            }
            else
                $proceso_dependiente = \Ermtool\Process::find($process['process_id'])->value('nombre');



            $proceso[$i] = array('id'=>$process['id'],
                                'nombre'=>$process['nombre'],
                                'descripcion'=>$process['descripcion'],
                                'fecha_creacion'=>$process['fecha_creacion'],
                                'fecha_exp'=>$fecha_exp,
                                'proceso_dependiente'=>$proceso_dependiente,
                                'estado'=>$process['estado']);
            $i += 1;
        }

        return view('datos_maestros.procesos.index',['procesos'=>$proceso,'subprocesos'=>$subprocesos,'organizaciones'=>$organizaciones]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Seleccionamos procesos que pueden ser padres
        $procesos = \Ermtool\Process::all()->where('process_id',NULL)->lists('nombre','id');

        return view('datos_maestros.procesos.create',['procesos'=>$procesos]);
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

        \Ermtool\Process::create([
            'nombre' => $request['nombre'],
            'descripcion' => $request['descripcion'],
            'fecha_creacion' => $fecha_creacion,
            'fecha_exp' => $fecha_exp,
            'process_id' => $request['process_id'],
            ]);

        Session::flash('message','Proceso agregado correctamente');

        return Redirect::to('/procesos');
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
        $proceso = \Ermtool\Process::find($id);
        $combobox = \Ermtool\Process::where('id','<>',$id)->lists('nombre','id');
        return view('datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox]);
    }

    public function bloquear($id)
    {
        $proceso = \Ermtool\Process::find($id);
        $proceso->estado = 1;
        $proceso->save();

        Session::flash('message','Proceso bloqueado correctamente');

        return Redirect::to('/procesos');
    }

    public function desbloquear($id)
    {
        $proceso = \Ermtool\Process::find($id);
        $proceso->estado = 0;
        $proceso->save();

        Session::flash('message','Proceso desbloqueado correctamente');

        return Redirect::to('/procesos');
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
        $proceso = \Ermtool\Process::find($id);
        $fecha_creacion = $proceso->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
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

        //vemos si tiene proceso padre
        if($request['process_id'] != "")
        {
            $process_id = $request['process_id'];
        }
        else
        {
            $process_id = NULL;
        }

        $proceso->nombre = $request['nombre'];
        $proceso->descripcion = $request['descripcion'];
        $proceso->fecha_creacion = $fecha_creacion;
        $proceso->fecha_exp = $fecha_exp;
        $proceso->process_id = $process_id;

        $proceso->save();

        Session::flash('message','Proceso actualizado correctamente');

        return Redirect::to('/procesos');
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
