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

<h4>{{ $name }}</h4>
<p>Ahora debe ingresar el tipo de respuesta para cada pregunta, y en caso de ser necesario (radio o checkbox) ingresar las alternativas.</p>

{!!Form::open(['url'=>'encuesta.store','method'=>'POST','class'=>'form-horizontal'])!!}

@for($i=1;$i<=$cont;$i++)
	@if ($_POST['pregunta'.$i] != "")
		<p><b>Pregunta {{ $i }}: {{ $_POST['pregunta'.$i] }}</b>
						<div class="form-group">
							{!!Form::label('tipo_respuesta *',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								<select id="select<?php echo $i ?>" required="true" name="tipo_respuesta{{ $i }}">
									<option selected="selected" value="">- Seleccione tipo de respuesta -</option>
									<option value="0">Texto</option>
									<option value="1">Radio</option>
									<option value="2">Checkbox</option>
								</select>
							</div>
						</div>

						<div id="respuestas{{$i}}">
						</div>
		</p>
					<input type="hidden" name="pregunta<?php echo $i; ?>" value="{{ $_POST['pregunta'.$i] }}">
	@endif
@endfor

				<!-- Enviamos cantidad de preguntas -->
				{!!Form::hidden('contpreguntas',$cont)!!}

				<!-- Enviamos nombre de encuesta -->
				{!!Form::hidden('nombre_encuesta',$name)!!}
				<center>
					<div class="form-group">
						{!!Form::submit("Crear encuesta", ["class"=>"btn btn-primary","name"=>"agregar"])!!}	
					</div>
					{!!Form::close()!!}

					{!!Form::open(['url'=>'crear_encuesta','method'=>'POST','class'=>'form-horizontal'])!!}
					<div class="form-group">
						{!!Form::submit("Volver", ["class"=>"btn btn-danger","name"=>"volver"])!!}	
					</div>
					{!!Form::close()!!}
				</center>



<label>* Recuerde que:</label>
	<p>Radio: Permite elegir una respuesta. 
		<div class="radio-inline">
			<label>
				<input type="radio" name="radio-inline" checked>
				<i class="fa fa-circle-o"></i>
			</label>
		</div>
		<div class="radio-inline">
			<label>
				<input type="radio" name="radio-inline" checked>
				<i class="fa fa-circle-o"></i>
			</label>
		</div>
		<div class="radio-inline">
			<label>
				<input type="radio" name="radio-inline" checked>
				<i class="fa fa-circle-o"></i>
			</label>
		</div>
	</p>
	<p>Checkbox: Permite elegir varias respuestas. 
		<div class="checkbox-inline">
			<label>
				<input type="checkbox" checked>
				<i class="fa fa-square-o"></i>
			</label>
		</div>
		<div class="checkbox-inline">
			<label>
				<input type="checkbox">
				<i class="fa fa-square-o"></i>
			</label>
		</div>
		<div class="checkbox-inline">
			<label>
				<input type="checkbox">
				<i class="fa fa-square-o"></i>
			</label>
		</div>
	</p>
	</div>
</div>
@stop
@section('scripts2')
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

//generar nuevos campos de alternativa
 jQuery.fn.generaNuevosCampos = function(etiqueta, nombreCampo, indice){
         $(this).each(function(){
            elem = $(this);
            elem.data("etiqueta",etiqueta);
            elem.data("nombreCampo",nombreCampo);
            elem.data("indice",indice);

            elem.click(function(e){
               e.preventDefault();
               elem = $(this);
               etiqueta = elem.data("etiqueta");
               nombreCampo = elem.data("nombreCampo");
               indice = elem.data("indice");
               texto_insertar = '<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" placeholder="' + etiqueta +" "+ indice +'"name="' + nombreCampo + indice + '" /></div></div>';
               indice ++;
               elem.data("indice",indice);
               nuevo_campo = $(texto_insertar);
               elem.before(nuevo_campo);
            });
         });
         return this;
  }

	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Add Drag-n-Drop feature
	WinMove();

@for($i=1;$i<=$cont;$i++)
	$("#select{{$i}}").change(function() {
		//primero vaciamos las respuestas
			$("#respuestas{{$i}}").empty();

			if ($("#select{{$i}}").val() != 0)
			{
				$("#respuestas{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="pregunta{{$i}}_alternativa1" required="true" placeholder="Alternativa 1">');
				$("#respuestas{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="pregunta{{$i}}_alternativa2" required="true" placeholder="Alternativa 2">');
				$("#respuestas{{$i}}").append('<center><a href="#" id="mascampos{{$i}}">Agregar alternativa</a></center></div></div>');
			}

			$("#mascampos{{$i}}").generaNuevosCampos("Alternativa", "pregunta{{$i}}_alternativa", 3);
	    });

@endfor

</script>

@stop
