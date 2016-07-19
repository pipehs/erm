@extends('en.master')

@section('title', 'Risk Events')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identification of risk events</a></li>
			<li><a href="crear_encuesta">Create poll</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Create poll</span>
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
<p>Now you have to enter the kind of answer for each question, and if it's necessary enter the alternatives.</p>

{!!Form::open(['url'=>'encuesta.store','method'=>'POST','class'=>'form-horizontal'])!!}

@for($i=1;$i<=$cont;$i++)
	@if ($_POST['pregunta'.$i] != "")
		<p><b>Pregunta {{ $i }}: {{ $_POST['pregunta'.$i] }}</b>
						<div class="form-group">
							{!!Form::label('Kind of answer *',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								<select id="select{{$i}}" required="true" name="tipo_respuesta{{ $i }}">
									<option selected="selected" value="">- Select kind of answer -</option>
									<option value="0">Text</option>
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
						{!!Form::submit("Create poll", ["class"=>"btn btn-primary","name"=>"agregar"])!!}	
					</div>
					{!!Form::close()!!}

					{!!Form::open(['url'=>'crear_encuesta','method'=>'POST','class'=>'form-horizontal'])!!}
					<div class="form-group">
						{!!Form::submit("Return", ["class"=>"btn btn-danger","name"=>"volver"])!!}	
					</div>
					{!!Form::close()!!}
				</center>



<label>* Remember that:</label>
	<p>Radio: It allows you to choose an answer. 
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
	<p>Checkbox: It allows you to choose various answers. 
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
				$("#respuestas{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="pregunta{{$i}}_alternativa1" required="true" placeholder="Option 1">');
				$("#respuestas{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="pregunta{{$i}}_alternativa2" required="true" placeholder="Option 2">');
				$("#respuestas{{$i}}").append('<center><a href="#" id="mascampos{{$i}}">Add Option</a></center></div></div>');
			}

			$("#mascampos{{$i}}").generaNuevosCampos("Option", "pregunta{{$i}}_alternativa", 3);
	    });

@endfor

</script>

@stop
