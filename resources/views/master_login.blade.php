<!DOCTYPE html>
<html lang="en">
<head>
	<title>B-GRC - @yield('title')</title>
	<meta charset="utf-8">
	<meta name="description" content="Sistema de gestión de riesgos">
	<meta name="author" content="ERM">
	<meta name="viewport" content="width=device-width, initial-scale=1">
 
 	{!!Html::style('assets/plugins/bootstrap/bootstrap.css')!!}
 	{!!Html::style('assets/plugins/jquery-ui/jquery-ui.min.css')!!}
 	{!!Html::style('https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css')!!}
 	{!!Html::style('https://fonts.googleapis.com/css?family=Righteous')!!}
 	{!!Html::style('assets/plugins/fancybox/jquery.fancybox.css')!!}
 	{!!Html::style('assets/plugins/xcharts/xcharts.min.css')!!}

 	@if (isset($data["organization"]))
	 	@if (file_exists(public_path().'/assets/css/style_'.strtolower($data["organization"]->o).'.css'))
	 		{!!Html::style('assets/css/style_'.strtolower($data["organization"]->o).'.css')!!}
	 	@else
	 		{!!Html::style('assets/css/style.css')!!}
	 	@endif
	@else
		{!!Html::style('assets/css/style.css')!!}
	@endif

 	{!!Html::style('assets/plugins/select2/select2.css')!!}
 	{!!Html::style('assets/plugins/sweetalert-master/dist/sweetalert.css')!!}
 	{!!Html::style('assets/plugins/sweetalert-master/themes/twitter.css')!!}

	{!!Html::script('assets/plugins/jquery/jquery-2.1.0.min.js')!!}
	{!!Html::script('assets/plugins/jquery-ui/jquery-ui.min.js')!!}
	{!!Html::script('assets/plugins/bootstrap/bootstrap.min.js')!!}
	{!!Html::script('assets/plugins/justified-gallery/jquery.justifiedgallery.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/tinymce.min.js')!!}
	{!!Html::script('assets/plugins/tinymce/jquery.tinymce.min.js')!!}
	{!!Html::script('assets/js/devoops.js')!!}

	{!!Html::script('https://code.highcharts.com/highcharts.js')!!}
	{!!Html::script('https://code.highcharts.com/modules/heatmap.js')!!}
	{!!Html::script('https://code.highcharts.com/modules/exporting.js')!!}

	{!!Html::script('assets/plugins/sweetalert-master/dist/sweetalert.min.js')!!}

</head>
<body class="body-login">

<br><br>
<div class="container">
@if (Session::has('errors'))
	<div class="alert alert-warning" role="alert">
		<ul>
            <strong>Ocurrio un error!</strong>
		    @foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
	            @endforeach
	    </ul>
	</div>
@endif

@if (Session::has('message-error'))
	<div class="alert alert-danger" role="alert">
		<ul>
            <strong>{{ Session::get('message-error') }}</strong>
	    </ul>
	</div>
@endif

@if(Session::has('message'))
      <div class="alert alert-success alert-dismissible" role="alert">
      {{ Session::get('message') }}
      </div>
@endif

</div>


    @yield('content')


	@yield('scripts2')
</body>
</html>