<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;

class PlanesAccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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

    //obtiene plan de acción para un issue dado
    public function getActionPlan($id)
    {
        $results = array();
        //obtenemos action plan
        $action_plan = DB::table('action_plans')
                    ->where('action_plans.issue_id','=',$id)
                    ->select('action_plans.id','action_plans.description','action_plans.final_date',
                        'action_plans.stakeholder_id')
                    ->get();

        if ($action_plan == NULL)
        {
            $results = NULL;
        }

        else
        {
            foreach ($action_plan as $ap) //aunque solo existirá uno
            {
                //obtenemos stakeholder
                if ($ap->stakeholder_id == NULL)
                {
                    $stakeholder = NULL;
                    $rut = NULL;
                }
                else
                {
                    $stakeholder_temp = \Ermtool\stakeholder::find($ap->stakeholder_id);
                    $stakeholder = $stakeholder_temp->name.' '.$stakeholder_temp->surnames;
                    $rut = $stakeholder_temp->id;
                }
                $results = [
                    'id' => $ap->id,
                    'description' => $ap->description,
                    'final_date' => $ap->final_date,
                    'stakeholder' => $stakeholder,
                    'rut' => $rut,
                ];
            }
        }
        
        return json_encode($results);
    }


    public function generarReportePlanes($org)
    {
        $results = array();
        $i = 0;
        //obtenemos datos de plan de auditoría, auditoría, issue y plan de acción
        $action_plans = DB::table('action_plans')
            ->join('issues','issues.id','=','action_plans.issue_id')
            ->join('audit_tests','audit_tests.id','=','issues.audit_test_id')
            ->join('audit_audit_plan_audit_program','audit_audit_plan_audit_program.id','=','audit_tests.audit_audit_plan_audit_program_id')
            ->join('audit_audit_plan','audit_audit_plan.id','=','audit_audit_plan_audit_program.audit_audit_plan_id')
            ->join('audit_plans','audit_plans.id','=','audit_audit_plan.audit_plan_id')
            ->join('audits','audits.id','=','audit_audit_plan.audit_id')
            ->where('audit_plans.organization_id','=',(int)$org)
            ->select('audit_plans.name as audit_plan_name',
                     'audits.name as audit_name',
                     'issues.name as issue_name',
                     'action_plans.description',
                     'action_plans.final_date',
                     'action_plans.created_at',
                     'action_plans.status')
            ->get();

        foreach ($action_plans as $action_plan)
        {
            $fecha_creacion = date('d-m-Y',strtotime($action_plan->created_at));
            $fecha_creacion .= ' a las '.date('H:i:s',strtotime($action_plan->created_at));

            //¡¡¡¡¡¡¡¡¡corregir problema del año 2038!!!!!!!!!!!! //
            $fecha_final = date('d-m-Y',strtotime($action_plan->final_date));
            $fecha_final .= ' a las 00:00:00';

            if ($action_plan->status == 0)
            {
                $estado = 'Abierto';
            }
            else if ($action_plan->status == 1)
            {
                $estado = 'Cerrado';
            }
            else
            {
                $estado = 'Error al obtener estado';
            }

            $results[$i] = [
                        'Plan_de_auditoría' => $action_plan->audit_plan_name,
                        'Auditoría' => $action_plan->audit_name,
                        'Debilidad' => $action_plan->issue_name,
                        'Plan_de_acción' => $action_plan->description,
                        'Estado' => $estado,
                        'Fecha_creación' => $fecha_creacion,
                        'Fecha_final' => $fecha_final,
            ];

            $i += 1;
        }

        if (strstr($_SERVER["REQUEST_URI"],'genexcelplan')) //se esta generado el archivo excel, por lo que los datos no son codificados en JSON
        {
            return $results;
        }
        else
            return json_encode($results);
    }
}