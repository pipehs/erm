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
			
			En esta sección podrá crear las preguntas y respuestas que los denunciantes deberán responder en el sistema de Denuncias. Primero que todo, ingrese las preguntas para en el siguiente paso, ingresar el tipo de respuesta para las mismas.<br/>

			{!!Form::open(['route'=>'store_cc_questions1','method'=>'GET','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}

					<div class="form-group">
						{!!Form::label('Pregunta 1',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
						@if (strstr($_SERVER["REQUEST_URI"],'edit')) {{-- Ver como editar preguntas --}}
							
						@else
							{!!Form::text('cc_question_1',null,['id' => 'cc_question_1','class'=>'form-control'])!!}
						@endif
						</div>
						<div style="cursor:hand" onclick="add_questions()">
							<button type="button" class="btn btn-primary btn-app-sm btn-circle">
								<i class="fa fa-plus"></i>
							</button>
						</div> 
						<br>
					</div>

					<div id="new_questions">
					</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Ingresar tipo de respuestas', ['class'=>'btn btn-primary'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<center>
   				{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
   			<center>
			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<p>Esta es la URL que identifica la base del sistema, y el que será tomado para el envío de las encuestas. Por ejemplo: www.b-grc.com</p>
</div>
@stop

@section('scripts2')

<script>
//Agrega nuevas preguntas para canal de denuncia
cont_q = 2;
function add_questions()
{
			var new_questions = '<div class="form-group">'
			new_questions += '<label for="cc_question_'+cont_q+'" class="col-sm-4 control-label">Pregunta '+cont_q+'</label>'
			new_questions += '<div class="col-sm-4">'
			new_questions += '<input type="text" name="cc_question_'+cont_q+'" class="form-control"></input>'
			new_questions += '</div></div>'

			$("#new_questions").append(new_questions)
			cont_q = cont_q + 1
}
</script>
@stop



					