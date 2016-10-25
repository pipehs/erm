<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta name="description" content="Sistema de gestión de riesgos">
    <meta name="author" content="IXUS IT Solutions">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {!!Html::style('assets/css/matrix.css')!!}
    {!!Html::style('assets/plugins/bootstrap/bootstrap.css')!!}
    {!!Html::style('assets/css/bootstrap-min.css')!!}
    {!!Html::style('assets/plugins/jquery-ui/jquery-ui.min.css')!!}
    {!!Html::style('assets/fonts/fontawesome.css')!!}
    {!!Html::style('assets/fonts/righteous.css')!!}
    {!!Html::style('assets/plugins/fancybox/jquery.fancybox.css')!!}
    {!!Html::style('assets/plugins/fullcalendar/fullcalendar.css')!!}
    {!!Html::style('assets/plugins/xcharts/xcharts.min.css')!!}
    {!!Html::style('assets/css/style.css')!!}
    {!!Html::style('assets/plugins/select2/select2.css')!!}
    {!!Html::style('assets/plugins/sweetalert-master/dist/sweetalert.css')!!}
    {!!Html::style('assets/plugins/sweetalert-master/themes/twitter/twitter.css')!!}
    {!!Html::style('assets/css/fileinput.css')!!}
    {!!Html::style('assets/css/fileinput.min.css')!!}
    {!!Html::style('assets/css/semaforo.css')!!}
    {!!Html::style('assets/css/upload.css')!!}

    {!!Html::style('assets/css/imprimir.css',['media'=>'print'])!!} 

    {!!Html::style('assets/bootstrap-toggle-master/css/bootstrap2-toggle.min.css')!!}
        <title>Error interno</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #849090;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

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
            }

            #container {
                min-width: 300px;
                max-width: 800px;
                height: 400px;
                margin: 1em auto;
            }
            label {
                display: inline-block;
                width: 5em;
              }
        </style>
    </head>
    <body>
        @include('header')

        <!--Start Container-->
        <div id="main" class="container-fluid">
        <div class="row">
        @include('sidebar')
        <div id="content" class="col-xs-12 col-sm-10">
            <div class="content">
            </br></br>
                <div class="title">Ocurrio un problema en el servidor. Por favor vuelva a intentarlo y si el problema persiste comuniquese con soporte t&eacute;cnico de B-GRC.</div>

                    <a href="home" class="btn btn-info btn-lg">Ir a Inicio</a>
            </div>
        </div>
        </div>
        
    </body>
</html>
