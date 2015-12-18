<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Session;
use Redirect;
use DB;

class StakeholdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stakeholder = array();
        if (isset($_GET['verbloqueados']))
        {
            $stakeholders = \Ermtool\Stakeholder::all()->where('estado',1); //select stakeholders bloqueadas  
        }
        else
        {
            $stakeholders = \Ermtool\Stakeholder::all()->where('estado',0); //select stakeholders desbloqueadas
        }

        $i = 0;
        $j = 0; //contador de organizaciones relacionadas 
        // ---recorremos todas los stakeholders para asignar formato de datos correspondientes--- //
        foreach ($stakeholders as $persona)
        {
            //ahora obtenemos todas las organizaciones a las que pertenece cada persona
            $orgs = \Ermtool\Stakeholder::find($persona['id'])->organizations;

            foreach ($orgs as $organization)
            {
                 $organizaciones[$j] = array('stakeholder_id'=>$persona['id'],
                                             'id'=>$organization['id'],
                                             'nombre'=>$organization['nombre']);

                 $j += 1;
            }

            if ($persona['cargo'] == NULL)
                $cargo = "No especificado";
            else
                $cargo = $persona['cargo'];

            $org = \Ermtool\Organization::find($persona['organization_id']);

            $stakeholder[$i] = array('id'=>$persona['id'],
                                'dv'=>$persona['dv'],
                                'nombre'=>$persona['nombre'],
                                'apellidos'=>$persona['apellidos'],
                                'tipo'=>$persona['tipo'],
                                'fecha_creacion'=>$persona['fecha_creacion'],
                                'cargo'=>$cargo,
                                'correo'=>$persona['correo'],
                                'estado'=>$persona['estado']);
            $i += 1;
        }
        return view('datos_maestros.stakeholders.index',['stakeholders'=>$stakeholder,'organizaciones'=>$organizaciones]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $organizations = \Ermtool\Organization::where('estado',0)->lists('nombre','id');
        //si es create, campo rut estara desbloqueado
        $required = 'required';
        $disabled = "";
        return view('datos_maestros.stakeholders.create',['organizations'=>$organizations,'disabled'=>$disabled,'required'=>$required]);
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
       
        \Ermtool\Stakeholder::create([
            'id' => $request['id'],
            'dv' => $request['dv'],
            'nombre' => $request['nombre'],
            'apellidos' => $request['apellidos'],
            'fecha_creacion' => $fecha_creacion,
            'tipo' => $request['tipo'],
            'cargo' => $request['cargo'],
            'correo' => $request['correo']
            ]);

        //otra forma para agregar relaciones -> en comparación a attach utilizado en por ej. SubprocesosController
        foreach($request['organization_id'] as $organization_id)
        {
            DB::table('organization_stakeholder')->insert([
                'organization_id'=>$organization_id,
                'stakeholder_id'=>$request['id']
                ]);
        }

            Session::flash('message','Stakeholder agregado correctamente');

            return Redirect::to('/stakeholders');
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
        $stakeholder = \Ermtool\Stakeholder::find($id);
        $organizations = \Ermtool\Organization::where('estado',0)->lists('nombre','id');
        //si es edit, campo rut estara bloqueado y no habrá required
        $disabled = 'disabled';
        return view('datos_maestros.stakeholders.edit',
            ['stakeholder'=>$stakeholder,'organizations'=>$organizations,'disabled'=>$disabled,'required'=>'']);
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
        $stakeholder = \Ermtool\Stakeholder::find($id);
        $fecha_creacion = $stakeholder->fecha_creacion; //Se debe obtener fecha de creación por si no fue modificada
        $fecha_exp = NULL;

        if (strpos($request['fecha_creacion'],'/')) //primero verificamos que la fecha no se encuentre ya en el orden correcto
        {
            //obtenemos orden correcto de fecha creación
            $fecha = explode("/",$request['fecha_creacion']);
            $fecha_creacion = $fecha[2]."-".$fecha[0]."-".$fecha[1];
        }
       

            $stakeholder->nombre = $request['nombre'];
            $stakeholder->apellidos = $request['apellidos'];
            $stakeholder->fecha_creacion = $fecha_creacion;
            $stakeholder->tipo = $request['tipo'];
            $stakeholder->cargo = $request['cargo'];
            $stakeholder->correo = $request['correo'];
    
            $stakeholder->save();

            //primero que todo, eliminaremos las organizaciones anteriores del stakeholder para evitar repeticiones
            DB::table('organization_stakeholder')->where('stakeholder_id',$id)->delete();

            //ahora, agregamos posibles nuevas relaciones
            foreach($request['organization_id'] as $organization_id)
            {
                DB::table('organization_stakeholder')->insert([
                    'organization_id'=>$organization_id,
                    'stakeholder_id'=>$id
                    ]);
            }

            Session::flash('message','Stakeholder actualizado correctamente');

            return Redirect::to('/stakeholders');
    }

    public function bloquear($id)
    {
        $stakeholder = \Ermtool\Stakeholder::find($id);
        $stakeholder->estado = 1;
        $stakeholder->save();

        Session::flash('message','Stakeholder bloqueado correctamente');

        return Redirect::to('/stakeholders');
    }

    public function desbloquear($id)
    {
        $stakeholder = \Ermtool\Stakeholder::find($id);
        $stakeholder->estado = 0;
        $stakeholder->save();

        Session::flash('message','Stakeholder desbloqueado correctamente');

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
