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
}
