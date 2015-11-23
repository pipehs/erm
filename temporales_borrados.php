{!! Html::style('assets/plugins/bootstrap/bootstrap.css') !!}
	{!! Html::style('assets/plugins/jquery-ui/jquery-ui.min.css') !!}
	{!! Html::style('assets/plugins/fancybox/jquery.fancybox.css') !!}
	{!! Html::style('assets/plugins/fullcalendar/fullcalendar.css') !!}
	{!! Html::style('assets/plugins/xcharts/xcharts.min.css') !!}
	{!! Html::style('assets/plugins/select2/select2.css') !!}
	{!! Html::style('assets/css/style.css') !!}





<link href="../public/assets/plugins/bootstrap/bootstrap.css" rel="stylesheet">
		<link href="../public/assets/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
		<link href="../public/assets/plugins/fancybox/jquery.fancybox.css" rel="stylesheet">
		<link href="../public/assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
		<link href="../public/assets/plugins/xcharts/xcharts.min.css" rel="stylesheet">
		<link href="../public/assets/plugins/select2/select2.css" rel="stylesheet">
		<link href="../public/assets/css/style.css" rel="stylesheet">



<script src="../public/assets/plugins/jquery/jquery-2.1.0.min.js"></script>
	<script src="../public/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../public/assets/plugins/bootstrap/bootstrap.min.js"></script>
	<script src="../public/assets/plugins/justified-gallery/jquery.justifiedgallery.min.js"></script>
	<script src="../public/assets/plugins/tinymce/tinymce.min.js"></script>
	<script src="../public/assets/plugins/tinymce/jquery.tinymce.min.js"></script>
	<!-- All functions for this theme + document.ready processing -->
	<script src="../public/assets/js/devoops.js"></script>
	

<!-- Scripts -->
	{!! Html::script('assets/js/bootstrap.min.js') !!}

	<!-- All functions for this theme + document.ready processing -->
	{!! Html::script('assets/js/devoops.js') !!}

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<!--<script src="http://code.jquery.com/jquery.js"></script>-->
	{!! Html::script('assets/plugins/jquery/jquery-2.1.0.min.js') !!}
	{!! Html::script('assets/plugins/jquery-ui/jquery-ui.min.js') !!}
	{!! Html::script('assets/plugins/bootstrap/bootstrap.min.js') !!}
	{!! Html::script('assets/plugins/justified-gallery/jquery.justifiedgallery.min.js') !!}
	{!! Html::script('assets/plugins/tinymce/tinymce.min.js') !!}
	{!! Html::script('assets/plugins/tinymce/jquery.tinymce.min.js') !!}







	//parte eliminada de devoops.js en (document).ready funcion $('.main-menu').on('click', 'a', function (e) {



	if ($(this).hasClass('ajax-link')) {
			e.preventDefault();
			if ($(this).hasClass('add-full')) {
				$('#content').addClass('full-content');
			}
			else {
				$('#content').removeClass('full-content');
			}
			var url = $(this).attr('href');
			window.location.hash = url;
			LoadAjaxContent(url);
		}