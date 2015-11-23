@extends('master')

@section('title', 'Categor&iacute;as de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identificaci&oacute;n de Eventos de Riesgo</a></li>
			<li><a href="crear_encuesta">Crear Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Crear Encuesta</span>
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

En esta secci&oacute;n podr&aacute; crear encuestas para la identificaci&oacute;n de posibles eventos de riesgo.

{!!Form::open(['url'=>'crear_encuesta','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Ingrese cantidad de preguntas',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::number('cantidad_preguntas',null,
							['class'=>'form-control','id'=>'cantidad_preguntas','required'=>'true','min'=>'1'])!!}
						</div>
					</div>

					<div id="preguntas">
					</div>
			</div>
		</div>
	</div>
</div>
<script>
// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect2);
}
function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Add Drag-n-Drop feature
	WinMove();

	$("#cantidad_preguntas").change(function() {
		//primero vaciamos las preguntas
			$("#preguntas").empty();

			i = 0;
			if ($("#cantidad_preguntas").val() == 1)
			{
				$("#preguntas").append('<b>A continuaci&oacute;n ingrese la pregunta:</b>');
			}
			else
			{
				$("#preguntas").append('<b>A continuaci&oacute;n ingrese las '+$("#cantidad_preguntas").val()+' preguntas:</b>');	
			}
			while (i < $("#cantidad_preguntas").val())
			{
				$("#preguntas").append('<div class="form-group"><div class="col-sm-4 control-label"><label for="pregunta'+(i+1)+'">Pregunta '+(i+1)+'</label></div><div class="col-sm-3"><input type="text" class="form-control" required name="pregunta'+(i+1)+'"></div></div>');
	    		
	    		i++;
			}

			$("#preguntas").append('<center><div class="form-group">{!!Form::submit("Agregar Respuestas y Tipos de Respuestas", ["class"=>"btn btn-primary","name"=>"agregar"])!!}</div></center>');
			$("#preguntas").append('{!!Form::close()!!}')

			
	    });

});
</script>

@stop
