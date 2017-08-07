@extends('master')

@section('title', 'Error interno')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
<style>
.container {
    text-align: center;
    display: table-cell;
    vertical-align: middle;
}

.content {
    text-align: center;
    display: inline-block;
}

.title {
    font-size: 30px;
    margin-bottom: 40px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    width: 100%;
    color: #849090;
    display: table;
    font-family: 'Lato';
}
</style>
<div class="row">
    <div class="col-sm-12 col-m-6">
        <div class="box">
            <div id="content" class="col-xs-12 col-sm-10">
            <div class="content">
            </br></br><hr>
                <div class="title">
                <p>Ocurrio un problema en el servidor. Se ha enviado un correo informando de esta situación a nuestro equipo de Soporte. Esperamos solucionarlo a la brevedad. Disculpe las molestias.<p>

                <p>Equipo B-GRC</p>
                </div>
                
                <hr>
                    <center>
                        {!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
                    <center>
            </div>
        </div>
        </div>
    </div>
</div>
@stop

@section('scripts2')
<script>
/*
function verError()
{
    $("#boton").html('<a href="#" class="btn btn-success" id="boton" onclick="ocultarError()">Ocultar código de error</a>');
    $("#error_message").show(500);
}
function ocultarError()
{
    $("#boton").html('<a href="#" class="btn btn-warning" id="boton" onclick="verError()">Ver código de error</a>');
    $("#error_message").hide(500);
}
*/
</script>
@stop
