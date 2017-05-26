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
                <div class="title">Ocurrio un problema en el servidor. Por favor vuelva a intentarlo y si el problema persiste comuniquese con soporte t&eacute;cnico de B-GRC.</div>
                <hr>
                <div id="boton">
                    <a href="#" class="btn btn-info btn-warning" id="boton" onclick="verError()">Ver código de error</a>
                </div>
                <div id="error_message" style="display: none;">
                    <div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
                                {{ $e }}
                    </div>
                </div>
                <hr>
                    <a href="home" class="btn btn-info btn-lg">Ir a Inicio</a>
            </div>
        </div>
        </div>
    </div>
</div>
@stop

@section('scripts2')
<script>

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
</script>
@stop
