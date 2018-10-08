@extends('master')

@section('title', 'Crear formulario canal denuncia')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('cc_questions','Crear formulario Denuncias')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Crear formulario Denuncias</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
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

			Ahora deberá ingresar el tipo de respuesta para las preguntas ingresadas anteriormente.<br/>

			{!!Form::open(['route'=>'store_cc_questions2','method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}
				<?php $i = 1; ?>
				@foreach ($questions as $q)
					<p><b>Pregunta {{$i}}: {{ $q }}</b>
					<div class="form-group">
						<label for="required" class="col-sm-4 control-label">Indique si la respuesta es obligatoria</label>
						<div class="col-sm-5">
							<div class="radio-inline">
								<label>
									<input type="radio" required="true" name="required_{{ $i }}" value="0">No
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>
									<input type="radio" required="true" name="required_{{ $i }}" value="1">Si
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('tipo_respuesta *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							<select id="select{{$i}}" required="true" name="kind_answer_{{ $i }}">
								<option selected="selected" value="">- Seleccione tipo de respuesta -</option>
								<option value="1">Texto</option>
								<option value="2">Alternativa única</option>
								<option value="3">Alternativas múltiples</option>
								<option value="4">Fecha</option>
							</select>
						</div>
					</div>

					<div id="answers_{{$i}}"></div>

					<input type="hidden" name="question_{{$i}}" value="{{ $q }}">
					<?php $i += 1; ?>
					</p>
				@endforeach

			<div class="form-group">
				<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<center>
   				{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
   			</center>

   		<label>* Recuerde que:</label>
		<p>Alternativa única: Permite elegir una respuesta. 
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
		<p>Alternativas múltiples: Permite elegir varias respuestas a la vez. 
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
	</div>
</div>
@stop

@section('scripts2')

<script>
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

@for($i=1;$i<=count($questions);$i++)
	$("#select{{$i}}").change(function() {
		//primero vaciamos las respuestas
			$("#answers_{{$i}}").empty();

			if ($("#select{{$i}}").val() == 2 || $("#select{{$i}}").val() == 3)
			{
				$("#answers_{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="question_{{$i}}_choice1" required="true" placeholder="Alternativa 1">');
				$("#answers_{{$i}}").append('<div class="form-group"><label class="col-sm-4 control-label"></label><div class="col-sm-3"><input type="text" class="form-control" name="question_{{$i}}_choice2" required="true" placeholder="Alternativa 2">');
				$("#answers_{{$i}}").append('<center><a href="#" id="mascampos{{$i}}">Agregar alternativa</a></center></div></div>');
			}

			$("#mascampos{{$i}}").generaNuevosCampos("Alternativa", "question_{{$i}}_choice", 3);
	    });

@endfor
</script>
@stop



					