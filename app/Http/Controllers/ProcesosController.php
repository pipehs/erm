<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;
use DateTime;

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
            $procesos = \Ermtool\Process::all()->where('status',1); //select procesos bloqueados 
        }
        else
        {
            $procesos = \Ermtool\Process::all()->where('status',0); //select procesos desbloqueados
        }
        $i = 0;
        $j = 0; //contador de subprocesos
        $k = 0; //contador de organizaciones

        // ---recorremos todas los procesos para asignar formato de datos correspondientes--- //
        $organizaciones = array(); //en este array almacenaremos todas las organizaciones que están relacionadas con un proceso
        $subprocesos = array(); //en este array almacenraemos todos los subprocesos relacionados a un proceso

        foreach ($procesos as $process)
        {

            //obtenemos todas las organizaciones a las que pertenece cada subproceso relacionado
            $orgs = DB::select('SELECT organizations.id, organizations.name
                                FROM organizations
                                WHERE organizations.id IN (SELECT DISTINCT organization_subprocess.organization_id
                                    FROM organization_subprocess
                                WHERE organization_subprocess.subprocess_id IN (SELECT subprocesses.id
                                    FROM subprocesses WHERE process_id = '.$process["id"].'))');

            foreach ($orgs as $organization)
            {
                $organizaciones[$k] = array('proceso_id'=>$process['id'],
                                            'id'=>$organization->id,
                                            'nombre'=>$organization->name);

                $k += 1;
            }


           //obtenemos subprocesos relacionados
            $subprocesses = \Ermtool\Process::find($process['id'])->subprocesses;

            foreach ($subprocesses as $subprocess)
            {
               
                $subprocesos[$j] = array('proceso_id'=>$process['id'],
                                        'id'=>$subprocess['id'],
                                        'nombre'=>$subprocess['name']);

                $j += 1;

            }

            //damos formato a fecha de creación (se verifica si no es NULL en caso de algún error en la creación)
            if ($process['created_at'] == NULL OR $process['created_at'] == "0000-00-00" OR $process['created_at'] == "")
            {
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";
            }

            else
            {
                $fecha_creacion = date_format($process['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($process['created_at'],"H:i:s");
            }

            //damos formato a fecha expiración
            if ($process['expiration_date'] == NULL OR $process['expiration_date'] == "0000-00-00")
            {
                $fecha_exp = "Ninguna";
            }
            else
            { 
                $expiration_date = new DateTime($process['expiration_date']);
                $fecha_exp = date_format($expiration_date, 'd-m-Y');
                $fecha_exp .= " a las ".date_format($expiration_date,"H:i:s");
            }

            //damos formato a fecha de actualización 
            if ($process['updated_at'] != NULL)
            {
                $fecha_act = date_format($process['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($process['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            //damos formato si depende de otro proceso
            if ($process['process_id'] == NULL)
            {
                $proceso_dependiente['name'] = "No";
                $proceso_dependiente['id'] = NULL;
            }
            else
                $proceso_dependiente = \Ermtool\Process::find($process['process_id']);

            $proceso[$i] = array('id'=>$process['id'],
                                'nombre'=>$process['name'],
                                'descripcion'=>$process['description'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'fecha_exp'=>$fecha_exp,
                                'proceso_dependiente'=>$proceso_dependiente['name'],
                                'proceso_dependiente_id'=>$proceso_dependiente['id'],
                                'estado'=>$process['status']);
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
        $procesos = \Ermtool\Process::where('process_id',NULL)->lists('name','id');

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

        DB::transaction(function()
        {
            //vemos si tiene proceso dependiente
            if ($_POST['process_id'] != "")
            {
                $process_id = $_POST['process_id'];
            }
            else
                $process_id = NULL;

            \Ermtool\Process::create([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'expiration_date' => $_POST['expiration_date'],
                'process_id' => $process_id,
                ]);

            Session::flash('message','Proceso agregado correctamente');
        });

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
        $combobox = \Ermtool\Process::where('id','<>',$id)->lists('name','id');
        return view('datos_maestros.procesos.edit',['proceso'=>$proceso,'procesos'=>$combobox]);
    }

    public function bloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $proceso = \Ermtool\Process::find($GLOBALS['id1']);
            $proceso->status = 1;
            $proceso->save();

            Session::flash('message','Proceso bloqueado correctamente');
        });

        return Redirect::to('/procesos');
    }

    public function desbloquear($id)
    {
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $proceso = \Ermtool\Process::find($GLOBALS['id1']);
            $proceso->status = 0;
            $proceso->save();

            Session::flash('message','Proceso desbloqueado correctamente');
        });
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
        global $id1;
        $id1 = $id;
        DB::transaction(function()
        {
            $proceso = \Ermtool\Process::find($GLOBALS['id1']);
            $fecha_exp = NULL;

            //vemos si tiene proceso padre
            if($_POST['process_id'] != "")
            {
                $process_id = $_POST['process_id'];
            }
            else
            {
                $process_id = NULL;
            }

            $proceso->name = $_POST['name'];
            $proceso->description = $_POST['description'];
            $proceso->expiration_date = $_POST['expiration_date'];
            $proceso->process_id = $process_id;

            $proceso->save();

            Session::flash('message','Proceso actualizado correctamente');
        });
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

/*
    //obtiene procesos de una organización
    public function getProcesses($org)
    {
        $results = array();

        $processes = DB::table('processes')
                    ->join('subprocesses','subprocesses.process_id','=','processes.id')
                    ->join('organization_subprocess','organization_subprocess.subprocess_id','=','subprocesses.id')
                    ->where('organization_subprocess.organization_id','=',$org)
                    ->where('processes.status','=',0)
                    ->select('processes.id','processes.name')
                    ->get();

        foreach ($processes as $process)
        {
            $results = [
                'id' => $process->id,
                'name' => $process->name,
            ];
        }
        
        return json_encode($results);
    }

*/
}
