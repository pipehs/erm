<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;

use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Auth;

class ConfigurationController extends Controller
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
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        else if (Auth::user()->superadmin == 1)
        {
            return view('configuration.create');
        }
        else
        {
            return locked();
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
            return Redirect::route('/');
        }
        else if (Auth::user()->superadmin == 1)
        {
            //Primero, nose aseguramos de que exista sólo un registro
            DB::transaction(function() {
                foreach ($_POST as $op_name => $op_val)
                {
                    if ($op_name != '_token')
                    {
                        \Ermtool\Configuration::create([
                            'option_name' => $op_name,
                            'option_value' => $op_val
                        ]);
                    }
                    
                }
                
                Session::flash('message','Configuración seteada exitosamente');
            });

            return Redirect::to('home');
        }
        else
        {
            return locked();
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
    public function edit()
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        else if (Auth::user()->superadmin == 1)
        {
            $config = \Ermtool\Configuration::all();
            $data = array();
            foreach ($config as $c)
            {
                switch ($c->option_name) 
                {
                    case 'system_url':
                        $system_url = $c->option_value;
                        break;
                    case 'version':
                        $version = $c->option_value;
                        break;
                    case 'organization':
                        $organization = $c->option_value;
                        break;
                    case 'alert_ap_message_expired':
                        $alert_ap_message_expired = $c->option_value;
                        break;
                    case 'alert_ap_message_to_expire':
                        $alert_ap_message_to_expire = $c->option_value;
                        break;             
                    default:
                        # code...
                        break;
                }
            }

            $data = [
                'system_url' => isset($system_url) ? $system_url : NULL,
                'version' => isset($version) ? $version : NULL,
                'organization' => isset($organization) ? $organization : NULL,
                'alert_ap_message_expired' => isset($alert_ap_message_expired) ? $alert_ap_message_expired : NULL,
                'alert_ap_message_to_expire' => isset($alert_ap_message_to_expire) ? $alert_ap_message_to_expire : NULL,
            ];

            return view('configuration.edit',['data' => $data]);
        }
        else
        {
            return locked();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        if (Auth::guest())
        {
            return Redirect::route('/');
        }
        else if (Auth::user()->superadmin == 1)
        {
            //Primero, nose aseguramos de que exista sólo un registro
            DB::transaction(function() {
                foreach ($_POST as $op_name => $op_val)
                {
                    if ($op_name != '_token')
                    {
                        //Vemos si es que existe o se debe crear
                        $q = \Ermtool\Configuration::where('option_name','=',$op_name)->first();

                        if (empty($q))
                        {
                            \Ermtool\Configuration::create([
                                'option_name' => $op_name,
                                'option_value' => $op_val
                            ]);
                        }
                        else
                        {
                            \Ermtool\Configuration::where('option_name','=',$op_name)
                                ->update([
                                    'option_value' => $op_val
                                ]);
                        }                    
                    }
                    
                }
                
                Session::flash('message','Configuración actualizada exitosamente');
            });

            return Redirect::to('home');
        }
        else
        {
            return locked();
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
        //
    }
}
