@extends('master')

@section('title', 'Seguimiento de denuncia')

@section('content')
<style>
.popper {
    border-radius: 100%;
    padding: 2px 6px;
    background: #4132bc;
    color: white !important;
    margin-left: 10px;
}

.popbox {
    display: none;
    position: absolute;
    z-index: 99999;
    width: 400px;
    padding: 10px;
    background: #4132bc;
    color: white;
    border: 1px solid #4D4F53;
    border-radius:3px;
    margin: 0px;
    -webkit-box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
    box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
}

.popbox p{
	margin:0;
}

.popbox h2
{
    background-color: #070664;
    font-weight: bold;
    color:  #E3E5DD;
    font-size: 14px;
    display: block;
    width: 100%;
    margin: -10px 0px 8px -10px;
    padding: 5px 10px;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="clasificacion">Clasificación de denuncia</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Clasificación de denuncia</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
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

			Información del caso:
			
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px; width: 50%;">

			<tr>
				<td><b>Id denuncia</b></td>
				<td>1703181</td>
			</tr>

			<tr>
				<td><b>Autor</b></td>
				<td>Juán Perez</td>
			</tr>
			<tr>
				<td><b>Descripción del caso</b></td>
				<td>Se presentó un caso de cohecho en las oficinas administrativas</td>
			</tr>
			<tr>
				<td><b>Fecha de agregado</b></td>
				<td>17-03-2018</td>
			</tr>
		</table>

				<div class="row form-group">
					<div class="col-sm-12">
						<div class="radio">
							<label>
								<input type="radio" name="radio" checked>Cohecho a Funcionario Público Nacional o Extranjero 
								<i class="fa fa-circle-o"></i><p>Comete esta infracción quien ofrece o da a un empleado público un beneficio económico, en provecho de éste o de un tercero, para que realice acciones.</p>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="radio">Lavado de activos<i class="fa fa-circle-o"></i>
								<p>Comete esta infracción quien oculta o disimula el origen ilícito de determinados bienes, a sabiendas de que provienen directa o indirectamente de la persona</p>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="radio">Financiamiento al terrorismo<i class="fa fa-circle-o"></i>
								<p>Comete esta infracción toda persona que por cualquier medio solicite, recaude o provea fondos con la finalidad de financiar terrorismo</p>
							</label>
						</div>
					
						<div class="radio">
							<label>
								<input type="radio" name="radio">Discriminación o acoso<i class="fa fa-circle-o"></i>
								<p>Comete esta infracción quienes con una conducta verbal o física no apropiada dirigida a un empleado debido a su sexo, clase social, religión</p>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="radio">Conflicto de intereses<i class="fa fa-circle-o"></i>
								<p>Comete esta infracción quien utiliza de manera inadecuada la propiedad intelectual o información confidencial de la compañia en beneficio propio o de terceros</p>
							</label>
						</div>
					</div>
				</div>

				<div class="form-group">
					<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
					</center>
				</div>

				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<p>Comete esta infracción quien ofrece o da a un empleado público un beneficio económico, en provecho de éste o de un tercero, para que realice acciones.</p>
</div>

@stop

@section('scripts2')
<script>
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
</script>
@stop