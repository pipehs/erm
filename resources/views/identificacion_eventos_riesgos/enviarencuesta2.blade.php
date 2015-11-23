@extends('master')

@section('title', 'Categor&iacute;as de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identificaci&oacute;n de Eventos de Riesgo</a></li>
			<li><a href="enviar_encuesta">Enviar Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-6 col-m-3">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>

					<span>Enviar Encuesta</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
 
	@if ($tipo == 1)
		Seleccione los destinatarios manualmente a trav&eacute;s de la siguiente lista.
	@elseif ($tipo == 2)
		Seleccione la organizaci&oacute;n a la que desea enviar la encuesta.

		<div class="form-group">

							{!!Form::select('organizacion', 
							array('' => '- Seleccione -',
								  '1' => 'Organización 1',
					 	  		  '2' => 'Organización 2',
					 	  		  '3' => 'Organización 3'),
							 	   null, 
							 	   ['id' => 'el2'])!!}

		</div>

		<div class="form-group">
			Haga click para enviar correo con link a encuesta
			<div class="col-sm-3">
				{!!Form::submit('Enviar', ['class'=>'btn btn-primary'])!!}
			</div>
		</div>
	@elseif ($tipo == 3)
		Seleccione el cargo de los usuarios a los que desea enviar la encuesta.
	@endif

	

			</div>
		</div>
	</div>

	<div class="col-sm-6 col-m-3">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta seleccionada</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
			@if ($encuesta == 1)
				<b>Nombre:  Encuesta 1</b><br><br>
				<p>1. ¿Considera que ejemplo es un riesgo? </p>
				<p>
				<div class="radio-inline">
					<label>
						<input type="radio" name="radio-inline" checked> Si
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				<div class="radio-inline">
					<label>
						<input type="radio" name="radio-inline" checked> No
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				<div class="radio-inline">
					<label>
						<input type="radio" name="radio-inline" checked> Quizás
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				</p>
				<p>2. Seleccione sus colores favoritos</p>
				<p>
				<div class="checkbox-inline">
					<label>
						<input type="checkbox" checked>
						<i class="fa fa-square-o"></i> Rojo
					</label>
				</div>
				<div class="checkbox-inline">
					<label>
						<input type="checkbox">
						<i class="fa fa-square-o"></i> Verde
					</label>
				</div>
				<div class="checkbox-inline">
					<label>
						<input type="checkbox">
						<i class="fa fa-square-o"></i> Amarillo
					</label>
				</div>
				<div class="checkbox-inline">
					<label>
						<input type="checkbox">
						<i class="fa fa-square-o"></i> Azul
					</label>
				</div>
				</p>
				<p>3. Ingrese su opinión sobre esta encuesta</p>
				<p>
				<textarea class="form-control" name="" rows="4" cols="50"></textarea>
				</p>
			@elseif($encuesta == 2)
				<b>Nombre: Encuesta 2</b>
			@elseif($encuesta == 3)
				<b>Nombre: Encuesta 3</b>
			@endif



			</div>
		</div>
	</div>
</div> <!-- end div class="row"> -->

{!!Form::open(['url'=>'enviar_encuesta','method'=>'GET','class'=>'form-horizontal'])!!}
<center>
	<div class="row form-group">
	  {!!Form::submit('Volver', ['class'=>'btn btn-danger','name'=>'volver'])!!}
	</div>
</center>
{!!Form::close()!!}

@stop

@section('scripts')

<script>
// Run Select2 on element
function Select2Test(){
	$("#el2").select2();
	$("#el3").select2();
}

function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load script of Select2 and run this
	LoadSelect2Script(Select2Test);
	// Add Drag-n-Drop feature
	WinMove();
/*
	$("#el3").change(function(){
	//primero vaciamos las preguntas
		$("#seleccion").empty();

		if ($("#el3").val() == 1)
		{
			$("#seleccion").append('<b>Seleccione manualmente</b>');
			$("#seleccion").append('<div class="row form-group"><select </div>');
		}
		else if ($("#el3").val() == 2)
		{
			$("#seleccion").append('<b>Seleccione organizaci&oacute;n</b>');
		}
		else if ($("#el3").val() == 3)
		{
			$("#seleccion").append('<b>Seleccione cargo</b>');
		}
    });
 */
});
</script>

@stop