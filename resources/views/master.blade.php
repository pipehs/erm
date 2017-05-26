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
	#container {
	    min-width: 300px;
	    max-width: 800px;
	    height: 400px;
	    margin: 1em auto;
	}
	label {
		display: inline-block;
	}  
	</style>
 	{!!Html::style('assets/css/matrix.css')!!}
 	{!!Html::style('assets/plugins/bootstrap/bootstrap.css')!!}
 	{!!Html::style('assets/css/bootstrap-min.css')!!}
 	{!!Html::style('assets/plugins/jquery-ui/jquery-ui.min.css')!!}
 	{!!Html::style('assets/fonts/fontawesome.css')!!}
 	{!!Html::style('assets/fonts/righteous.css')!!}
 	{!!Html::style('assets/plugins/fancybox-3.0/dist/jquery.fancybox.min.css')!!}
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
 	{!!Html::style('assets/css/fileinput.css')!!}

 	{!!Html::style('assets/bootstrap-toggle-master/css/bootstrap2-toggle.min.css')!!}

 	<!--<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>-->
<!--
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
	<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		 HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
				<script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
				<script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
		[endif]-->

	

	<!-- Fonts -->
	<!--<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>-->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
<body>

@include('header')

<!--Start Container-->
<div id="main" class="container-fluid">
<div class="row">
@include('sidebar')

<div id="content" class="col-xs-12 col-sm-10">
    @yield('content')
</div>

	{!!Html::script('http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js')!!}
	<!-- polyfiller file to detect and load polyfills -->
	{!!Html::script('http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js')!!}
	<script>
	  webshims.setOptions('forms-ext', {types: 'date'});
		webshims.polyfill('forms forms-ext');
		webshims.formcfg = {
			en: {
			    dFormat: '-',
			    dateSigns: '-',
			    patterns: {
			        d: "yy-mm-dd"
			    }
			}
		};

	</script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
	{!!Html::script('assets/js/fileinput.min.js')!!}
	{!!Html::script('assets/plugins/bootstrap/bootstrap.min.js')!!}
	{!!Html::script('assets/plugins/justified-gallery/jquery.justifiedgallery.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/tinymce.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/jquery.tinymce.min.js')!!}
	{!!Html::script('assets/plugins/fancybox-3.0/dist/jquery.fancybox.min.js')!!}
	{!!Html::script('assets/js/devoops.js')!!}
	{!!Html::script('assets/js/scripts.js')!!}
	{!!Html::script('assets/js/upload.js')!!}

	{!!Html::script('assets/js/highcharts.js')!!}
	{!!Html::script('assets/js/heatmap.js')!!}
	{!!Html::script('assets/js/exporting.js')!!}

	{!!Html::script('assets/plugins/sweetalert-master/dist/sweetalert.min.js')!!}


	{!!Html::script('assets/js/ajax_jquery-ui.min.js')!!}
	{!!Html::script('assets/js/descargar.js')!!}

	{!!Html::script('assets/bootstrap-toggle-master/js/bootstrap2-toggle.min.js')!!}
	

	@yield('scripts2')

</div></div></body>
</html>