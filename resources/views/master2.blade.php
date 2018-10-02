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
 	{!!Html::style('assets/plugins/fancybox/jquery.fancybox.css')!!}
 	{!!Html::style('assets/plugins/fullcalendar/fullcalendar.css')!!}
 	{!!Html::style('assets/plugins/xcharts/xcharts.min.css')!!}
 	
 	@if (Session::get('org') != NULL && file_exists(public_path().'/assets/css/style_'.strtolower(Session::get('org')).'.css'))
 		{!!Html::style('assets/css/style_'.strtolower(Session::get('org')).'.css')!!}
 	@else
 		{!!Html::style('assets/css/style.css')!!}
 	@endif

 	{!!Html::style('assets/plugins/select2/select2.css')!!}
 	{!!Html::style('assets/plugins/sweetalert-master/dist/sweetalert.css')!!}
 	{!!Html::style('assets/plugins/sweetalert-master/themes/twitter/twitter.css')!!}
 	{!!Html::style('assets/css/fileinput.css')!!}
 	{!!Html::style('assets/css/fileinput.min.css')!!}
 	{!!Html::style('assets/css/semaforo.css')!!}
 	{!!Html::style('assets/css/upload.css')!!}
 	{!!Html::style('assets/css/fileinput.css')!!}
 	{!!Html::style('assets/css/popper.css')!!}

 	{!!Html::style('assets/bootstrap-toggle-master/css/bootstrap2-toggle.min.css')!!}

</head>
<body>

@include('header2')

<!--Start Container-->
<div id="main" class="container-fluid" style="padding-left: 2%;">

<div id="content" class="col-xs-12 col-sm-12">
    @yield('content')
</div>

	{!!Html::script('http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js')!!}
	<!-- polyfiller file to detect and load polyfills -->
	{!!Html::script('http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js')!!}
	{!!Html::script('assets/plugins/jquery/jquery-2.1.0.min.js')!!}
	{!!Html::script('assets/plugins/jquery-ui/jquery-ui.min.js')!!}
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
	{!!Html::script('assets/js/fileinput.min.js')!!}
	{!!Html::script('assets/plugins/bootstrap/bootstrap.min.js')!!}
	{!!Html::script('assets/plugins/justified-gallery/jquery.justifiedgallery.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/tinymce.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/jquery.tinymce.min.js')!!}
	{!!Html::script('assets/js/devoops.js')!!}
	{!!Html::script('assets/js/scripts.js')!!}
	{!!Html::script('assets/js/upload.js')!!}

	{!!Html::script('assets/js/highcharts.js')!!}
	{!!Html::script('assets/js/heatmap.js')!!}
	{!!Html::script('assets/js/exporting.js')!!}

<!--
	{!!Html::script('assets/js/jquery-1.10.2.js')!!}
	{!!Html::script('assets/js/jquery-ui.js')!!} -->

	{!!Html::script('assets/plugins/sweetalert-master/dist/sweetalert.min.js')!!}
	{!!Html::script('assets/js/descargar.js')!!}

	{!!Html::script('assets/bootstrap-toggle-master/js/bootstrap2-toggle.min.js')!!}
	

	@yield('scripts2')
	
</body>
</html>