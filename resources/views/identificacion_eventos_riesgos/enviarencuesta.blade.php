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
	<div class="col-sm-12 col-m-6">
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

En esta secci&oacute;n podr&aacute; enviar encuestas previamente creadas.

{!!Form::open(['url'=>'enviar_encuesta','method'=>'GET','class'=>'form-horizontal'])!!}
<div class="row form-group">
	{!!Form::label('Seleccione una encuesta',null,['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-4">
		{!!Form::select('encuesta', 
							array('' => '- Seleccione una encuesta -',
								  '1' => 'Encuesta 1',
							 	  '2' => 'Encuesta 2',
							 	  '3' => 'Encuesta 3'),
							 	   null,
							 	   ['required' => 'true',
							 	   	'id' => 'el2'])
						!!}
	</div>
</div>	

<div class="row form-group">
	{!!Form::label('Seleccione modo de env&iacute;o de encuesta',null,['class'=>'col-sm-4 control-label'])!!}
	<div class="col-sm-4">
		{!!Form::select('destinatarios', 
							array('' => '- Seleccione una opci&oacute;n -',
								  '1' => 'Seleccionar stakeholders manualmente',
							 	  '2' => 'Enviar por organizaci&oacute;n',
							 	  '3' => 'Enviar por cargo'),
							 	   null,
							 	   ['required' => 'true',
							 	   	'id' => 'el3'])
						!!}
	</div>
</div>	
<center>
	<div class="row form-group">
	  {!!Form::submit('Aplicar', ['class'=>'btn btn-success','name'=>'aplicar'])!!}
	</div>
</center>
<!-- aquí se ingresará en html los datos ingresados a través de jquery !-->
<div id="seleccion">
</div>

			</div>
		</div>
	</div>
</div>

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