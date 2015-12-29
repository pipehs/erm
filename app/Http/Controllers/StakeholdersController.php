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
        $organizaciones = array(); //definimos por si no hay
        
        if (isset($_GET['verbloqueados']))
        {
            $stakeholders = \Ermtool\Stakeholder::all()->where('status',1); //select stakeholders bloqueadas  
        }
        else
        {
            $stakeholders = \Ermtool\Stakeholder::all()->where('status',0); //select stakeholders desbloqueadas
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
                                             'nombre'=>$organization['name']);

                 $j += 1;
            }

            if ($persona['position'] == NULL)
                $cargo = "No especificado";
            else
                $cargo = $persona['position'];

             //damos formato a fecha creación
            if ($persona['created_at'] != NULL)
            {
                $fecha_creacion = date_format($persona['created_at'],"d-m-Y");
                $fecha_creacion .= " a las ".date_format($persona['created_at'],"H:i:s");
            }
            else
                $fecha_creacion = "Error al registrar fecha de creaci&oacute;n";

            //damos formato a fecha de actualización 
            if ($persona['updated_at'] != NULL)
            {
                $fecha_act = date_format($persona['updated_at'],"d-m-Y");
                $fecha_act .= " a las ".date_format($persona['updated_at'],"H:i:s");
            }

            else
                $fecha_act = "Error al registrar fecha de actualizaci&oacute;n";

            $org = \Ermtool\Organization::find($persona['organization_id']);

            $stakeholder[$i] = array('id'=>$persona['id'],
                                'dv'=>$persona['dv'],
                                'nombre'=>$persona['name'],
                                'apellidos'=>$persona['surnames'],
                                'tipo'=>$persona['role'],
                                'fecha_creacion'=>$fecha_creacion,
                                'fecha_act'=>$fecha_act,
                                'cargo'=>$cargo,
                                'correo'=>$persona['mail'],
                                'estado'=>$persona['status']);
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
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
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
        \Ermtool\Stakeholder::create([
            'id' => $request['id'],
            'dv' => $request['dv'],
            'name' => $request['name'],
            'surnames' => $request['surnames'],
            'role' => $request['role'],
            'position' => $request['position'],
            'mail' => $request['mail']
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
        $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');
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

            $stakeholder->name = $request['name'];
            $stakeholder->surnames = $request['surnames'];
            $stakeholder->role = $request['role'];
            $stakeholder->position = $request['position'];
            $stakeholder->mail = $request['mail'];
    
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
        $stakeholder->status = 1;
        $stakeholder->save();

        Session::flash('message','Stakeholder bloqueado correctamente');

        return Redirect::to('/stakeholders');
    }

    public function desbloquear($id)
    {
        $stakeholder = \Ermtool\Stakeholder::find($id);
        $stakeholder->status = 0;
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
