<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Support\Facades\Request;
//use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;

class EncuestasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_POST['agregar'])) //se agregaron las preguntas, ahora se deben agregar las respuestas
        {
            //cantidad de preguntas
            $cont = count($_POST)-4;
            return view('identificacion_eventos_riesgos.crearencuesta2',['cont'=>$cont]);
        }
        else
        {
            return view('identificacion_eventos_riesgos.crearencuesta');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function enviar()
    {
        if (isset($_GET['aplicar']))
        {
            $tipo = Request::input('destinatarios');
            $encuesta = Request::input('encuesta');
            return view('identificacion_eventos_riesgos.enviarencuesta2',['tipo'=>$tipo,'encuesta'=>$encuesta]);
        }
        else if (isset($_GET['volver']))
        {
            return view('identificacion_eventos_riesgos.enviarencuesta');
        }
        else
        {
            return view('identificacion_eventos_riesgos.enviarencuesta');
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
        return view('identificacion_eventos_riesgos.encuestacreada',['post'=>$_POST]);
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
}
