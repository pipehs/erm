@extends(Auth::user() ? 'master' : 'master2')

@section('title', 'Registro de denuncia')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="denuncias.registro">Registro de denuncia</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Registro de denuncia</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="no-move"></div>
			</div>
			<div class="box-content">
			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif


			<div class="alert alert-info alert-dismissible" role="alert">
				Puede dar seguimiento al caso con el siguiente id y con la contraseña ingresada anteriormente.
				<br><br>

				<b>Id: {{ $id }}</b>

				<br><br>
				<b>No olvide guarda este ID y su contraseña, ya que sólo con estos datos podrá consultar el estado del caso.</b>
			</div>
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts2')
<script>
	$(function() {
    var moveLeft = 0;
    var moveDown = 0;
    $('a.popper').hover(function(e) {
   
        var target = '#' + ($(this).attr('data-popbox'));
         
        $(target).show();
        moveLeft = $(this).outerWidth();
        moveDown = ($(target).outerHeight() / 2);
    }, function() {
        var target = '#' + ($(this).attr('data-popbox'));
        $(target).hide();
    });
 
    $('a.popper').mousemove(function(e) {
        var target = '#' + ($(this).attr('data-popbox'));
         
        leftD = e.pageX + parseInt(moveLeft);
        maxRight = leftD + $(target).outerWidth();
        windowLeft = $(window).width() - 40;
        windowRight = 0;
        maxLeft = e.pageX - (parseInt(moveLeft) + $(target).outerWidth() + 20);
         
        if(maxRight > windowLeft && maxLeft > windowRight)
        {
            leftD = maxLeft;
        }
     
        topD = e.pageY - parseInt(moveDown);
        maxBottom = parseInt(e.pageY + parseInt(moveDown) + 20);
        windowBottom = parseInt(parseInt($(document).scrollTop()) + parseInt($(window).height()));
        maxTop = topD;
        windowTop = parseInt($(document).scrollTop());
        if(maxBottom > windowBottom)
        {
            topD = windowBottom - $(target).outerHeight() - 20;
        } else if(maxTop < windowTop){
            topD = windowTop + 20;
        }
     
        $(target).css('top', topD).css('left', leftD);
     
     
    });
 
});
</script>
@stop