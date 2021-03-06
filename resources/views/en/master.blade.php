<!DOCTYPE html>
<html lang="en">
<head>

	<title>B-GRC - @yield('title')</title>
	<meta charset="utf-8">
	<meta name="description" content="Sistema de gestión de riesgos">
	<meta name="author" content="IXUS IT Solutions">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<style type="text/css">
	ul{  
		list-style-type:none;  
	}
	#cero_espacios {
		margin: 0;
		padding: 0;
	}  
	</style>
 
 	{!!Html::style('assets/css/matrix.css')!!}
 	{!!Html::style('assets/plugins/bootstrap/bootstrap.css')!!}
 	{!!Html::style('assets/css/bootstrap-min.css')!!}
 	{!!Html::style('assets/plugins/jquery-ui/jquery-ui.min.css')!!}
 	{!!Html::style('assets/css/font-awesome.css')!!}
 	{!!Html::style('assets/css/Righteous.css')!!}
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

 	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
<!--
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
				<script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
				<script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
		<![endif]-->

	

	<!-- Fonts -->
	<!--<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>-->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	
	


<style>
#container {
    min-width: 300px;
    max-width: 800px;
    height: 400px;
    margin: 1em auto;
}
</style>
</head>
<body>

@include('en.header')

<!--Start Container-->
<div id="main" class="container-fluid">
<div class="row">
@include('en.sidebar')

<div id="content" class="col-xs-12 col-sm-10">
    @yield('content')
</div>
</body>
</html>

	{!!Html::script('assets/plugins/jquery/jquery-2.1.0.min.js')!!}
	{!!Html::script('assets/plugins/jquery-ui/jquery-ui.min.js')!!}
	{!!Html::script('assets/plugins/bootstrap/bootstrap.min.js')!!}
	{!!Html::script('assets/plugins/justified-gallery/jquery.justifiedgallery.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/tinymce.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/jquery.tinymce.min.js')!!}
	{!!Html::script('assets/js/devoops.js')!!}
	{!!Html::script('assets/js/en/scripts.js')!!}
	{!!Html::script('assets/js/upload.js')!!}

	{!!Html::script('assets/js/highcharts.js')!!}
	{!!Html::script('assets/js/heatmap.js')!!}
	{!!Html::script('assets/js/exporting.js')!!}

	{!!Html::script('assets/js/jquery-1.10.2.js')!!}
	{!!Html::script('assets/js/jquery-ui.js')!!}

	{!!Html::script('assets/plugins/sweetalert-master/dist/sweetalert.min.js')!!}

	{!!Html::script('assets/js/jquery.min.js')!!}
	{!!Html::script('assets/js/ajax_jquery-ui.min.js')!!}
	{!!Html::script('assets/js/descargar.js')!!}

	{!!Html::script('assets/bootstrap-toggle-master/js/bootstrap2-toggle.min.js')!!}
	
	@yield('scripts2')
</body>
</html>